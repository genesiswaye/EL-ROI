<?php
session_start();

require_once "../config/database.php";
require_once "wallet_logic.php";
require_once "transaction.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];

/* =========================
   WALLET DATA
========================= */

$pending = getPendingEarnings($pdo, $user_id);

$wallet = getWallet($pdo, $user_id);

$balance = (float)($wallet['balance'] ?? 0);
$held = (float)($wallet['held_balance'] ?? 0);
$withdrawal_hold = (float)($wallet['withdrawal_hold'] ?? 0);

/* =========================
   TOTAL DEPOSITED
========================= */

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(amount), 0)
    FROM transactions
    WHERE user_id = ?
    AND type = 'deposit'
    AND status = 'success'
");

$stmt->execute([$user_id]);

$totalDeposited = (float)$stmt->fetchColumn();

/* =========================
   TOTAL EARNED
========================= */

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(net_amount), 0)
    FROM transactions
    WHERE user_id = ?
    AND type = 'payment_received'
    AND status = 'success'
");

$stmt->execute([$user_id]);

$totalEarned = (float)$stmt->fetchColumn();

/* =========================
   TOTAL WITHDRAWN
========================= */

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(amount), 0)
    FROM transactions
    WHERE user_id = ?
    AND type = 'withdrawal'
    AND status = 'success'
");

$stmt->execute([$user_id]);

$totalWithdrawn = (float)$stmt->fetchColumn();

$filter = $_GET['filter'] ?? 'all';

$where = "WHERE transactions.user_id = ?";
$params = [$user_id];

if ($filter === 'deposit') {
    $where .= " AND transactions.type = 'deposit'";
}

if ($filter === 'withdrawal') {
    $where .= " AND transactions.type = 'withdrawal'";
}

if ($filter === 'payments') {

    $where .= "
        AND transactions.type IN (
            'escrow_hold',
            'escrow_release',
            'payment_received'
        )
    ";
}

$search = trim($_GET['search'] ?? '');

/* =========================
   BASE QUERY
========================= */

$sql = "
    SELECT 
        transactions.type,
        transactions.amount,
        transactions.net_amount,
        transactions.platform_fee,
        transactions.reference,
        transactions.status,
        transactions.description,
        transactions.created_at,
        transactions.job_id,

        jobs.title AS job_title

    FROM transactions

    LEFT JOIN jobs
        ON transactions.job_id = jobs.id

    WHERE transactions.user_id = ?
";

$params = [$user_id];

/* =========================
   FILTERS
========================= */

if (!empty($filter)) {

    if ($filter === 'deposit') {

        $sql .= "
            AND transactions.type = 'deposit'
        ";
    } elseif ($filter === 'withdrawal') {

        $sql .= "
            AND transactions.type = 'withdrawal'
        ";
    } elseif ($filter === 'payments') {

        $sql .= "
            AND transactions.type IN (
                'escrow_hold',
                'escrow_release',
                'payment_received'
            )
        ";
    } elseif ($filter === 'failed') {

        $sql .= "
            AND transactions.status = 'failed'
        ";
    } elseif ($filter === 'pending') {

        $sql .= "
            AND transactions.status = 'pending'
        ";
    }
}

/* =========================
   SEARCH
========================= */

if (!empty($search)) {

    $sql .= "
        AND (
            transactions.reference LIKE ?
            OR transactions.description LIKE ?
            OR transactions.type LIKE ?
        )
    ";

    $searchTerm = "%$search%";

    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

/* =========================
   ORDER
========================= */

$sql .= "
    ORDER BY transactions.created_at DESC
";

/* =========================
   LIMIT
========================= */

$sql .= "
    LIMIT 10
";

/* =========================
   EXECUTE
========================= */

$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   QUICK STATS
========================= */

/* THIS MONTH */

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(amount), 0)
    FROM transactions
    WHERE user_id = ?
    AND status = 'success'
    AND MONTH(created_at) = MONTH(CURRENT_DATE())
    AND YEAR(created_at) = YEAR(CURRENT_DATE())
    AND type IN ('deposit', 'payment_received', 'escrow_release')
");

$stmt->execute([$user_id]);

$thisMonth = (float)$stmt->fetchColumn();

/* ACTIVE PROJECTS */

if ($_SESSION['role'] === 'student') {

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM applications
        WHERE student_id = ?
        AND status IN ('accepted', 'in_progress', 'submitted')
    ");
} else {

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM jobs
        WHERE created_by = ?
        AND status = 'in_progress'
    ");
}

$stmt->execute([$user_id]);

$activeProjects = (int)$stmt->fetchColumn();

/* AVG PER PROJECT */

$stmt = $pdo->prepare("
    SELECT 
        COALESCE(AVG(amount), 0)
    FROM transactions
    WHERE user_id = ?
    AND type IN ('payment_received', 'escrow_release')
    AND status = 'success'
");

$stmt->execute([$user_id]);

$avgPerProject = (float)$stmt->fetchColumn();

/* =========================
   ACTIVE GIGS
========================= */

if ($_SESSION['role'] === 'student') {

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM applications
        WHERE student_id = ?
        AND status IN ('accepted', 'in_progress', 'submitted')
    ");
} else {

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM jobs
        WHERE created_by = ?
        AND status = 'in_progress'
    ");
}

$stmt->execute([$user_id]);

$activeGigs = (int)$stmt->fetchColumn();

/* =========================
   PENDING APPLICATIONS
========================= */

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM applications
    WHERE student_id = ?
    AND status = 'pending'
");

$stmt->execute([$user_id]);

$pendingApplications = (int)$stmt->fetchColumn();

/* =========================
   ACTIVE ESCROW
========================= */

$stmt = $pdo->prepare("
    SELECT 
        escrows.id,
        escrows.amount,
        escrows.status,

        jobs.title,

        applications.status AS application_status,

        work_submissions.status AS submission_status,
        work_submissions.created_at AS submitted_at

    FROM escrows

    JOIN jobs
        ON escrows.job_id = jobs.id

    JOIN applications
        ON applications.job_id = jobs.id

    LEFT JOIN work_submissions
        ON work_submissions.application_id = applications.id

    WHERE (
        applications.student_id = ?
        OR jobs.created_by = ?
    )

    ORDER BY escrows.created_at DESC

    LIMIT 1
");

$stmt->execute([$user_id, $user_id]);

$escrow = $stmt->fetch(PDO::FETCH_ASSOC);

/* =========================
   QUICK AMOUNTS
========================= */

$quickAmounts = [5000, 10000, 25000, 50000, 100000];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="dash.css" rel="stylesheet">
    <link href="../dist/output.css" rel="stylesheet">
    <title>CampusLink - Wallet Dashboard</title>
    <style>

    </style>
</head>

<body>
    <?php
    $activePage = "wallet";
    include "../includes/employer_nav.php";
    ?>

    <!-- Header -->
    <header class="header">
        <div class="header-right">
            <div class="header-actions">
                <div class="notification-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notification-badge">3</span>
                </div>


            </div>
        </div>

    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <h2>
                Welcome back, <?= htmlspecialchars($_SESSION['name']) ?>
            </h2>

            <p>
                Manage your deposits, earnings, withdrawals and escrow payments securely.
            </p>
            <p>

                You have
                <?= $activeGigs ?> active gigs
                and
                <?= $pendingApplications ?> pending applications

            </p>
        </div>

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>My Wallet</h1>
                <p class="subtitle">Manage deposits, earnings, escrow payments, and withdrawals securely.</p>
            </div>
            <!-- <div class="header-buttons">
                <a href="export_transactions.php" class="btn btn-outline">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Export CSV
                </a>
                <a href="withdraw.php" class="btn btn-blue">Withdraw Funds</a>
            </div> -->
        </div>

        <!-- Wallet Summary Cards -->
        <div class="stats-grid">
            <div class="stat-card stat-green">
                <div class="stat-icon green-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                </div>
                <p class="stat-label">Available Balance</p>
                <p class="stat-amount">₦<?= number_format($balance, 2) ?></p>
                <p class="stat-helper">Ready to withdraw</p>
            </div>

            <div class="stat-card stat-green">
                <div class="stat-icon green-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"></path>
                        <path d="M4 6v12c0 1.1.9 2 2 2h14v-4"></path>
                        <path d="M18 12a2 2 0 0 0-2 2c0 1.1.9 2 2 2h4v-4h-4z"></path>
                    </svg>
                </div>
                <p class="stat-label">Total Deposited</p>
                <p class="stat-amount">₦<?= number_format($totalDeposited, 2) ?></p>
                <p class="stat-helper">Personal funding</p>
            </div>
            <?php if ($_SESSION['role'] === 'student'): ?>
                <div class="stat-card stat-blue">
                    <div class="stat-icon blue-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                            <polyline points="17 6 23 6 23 12"></polyline>
                        </svg>
                    </div>
                    <p class="stat-label">Total Earned</p>
                    <p class="stat-amount">₦<?= number_format($totalEarned, 2) ?></p>
                    <p class="stat-helper">From completed jobs</p>
                </div>
            <?php endif; ?>
            <div class="stat-card stat-red">
                <div class="stat-icon red-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline>
                        <polyline points="17 18 23 18 23 12"></polyline>
                    </svg>
                </div>
                <p class="stat-label">Total Withdrawn</p>
                <p class="stat-amount">₦<?= number_format($totalWithdrawn, 2) ?></p>
                <p class="stat-helper">Total withdrawals</p>
            </div>

            <div class="stat-card stat-yellow">
                <div class="stat-icon yellow-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <p class="stat-label">Held in Escrow</p>
                <p class="stat-amount">₦<?= number_format($held, 2) ?></p>
                <p class="stat-helper">Locked in active projects</p>
            </div>

            <div class="stat-card stat-orange">
                <div class="stat-icon orange-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <p class="stat-label">Pending Withdrawals</p>
                <p class="stat-amount">₦<?= number_format($withdrawal_hold, 2) ?></p>
                <p class="stat-helper">Awaiting admin approval</p>
            </div>
            <?php if ($_SESSION['role'] === 'student'): ?>
                <div class="stat-card stat-yellow">
                    <div class="stat-icon yellow-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <p class="stat-label">Pending Earnings</p>
                    <p class="stat-amount">₦<?= number_format($pending, 2) ?></p>
                    <p class="stat-helper">Awaiting employer approval</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Fund Wallet & Quick Stats -->
        <div class="two-column-grid">
            <div class="fund-wallet-card">
                <div class="card-header-flex">
                    <div>
                        <div class="card-icon green-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                        </div>
                        <div>
                            <h3>Fund Your Wallet</h3>
                            <p class="card-subtitle">Add money via Paystack</p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Enter Amount (₦)</label>
                    <input type="number" id="amount" class="amount-input" placeholder="0.00">
                </div>

                <div class="quick-amounts">
                    <p class="quick-label">Quick amounts:</p>
                    <div class="quick-buttons">
                        <button class="quick-btn">₦5,000</button>
                        <button class="quick-btn">₦10,000</button>
                        <button class="quick-btn">₦25,000</button>
                        <button class="quick-btn">₦50,000</button>
                        <button class="quick-btn">₦100,000</button>
                    </div>
                </div>

                <button onclick="payWithPaystack()" class="btn btn-primary btn-full">Fund Wallet with Paystack</button>

                <div class="security-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    <span>Secured by Paystack • PCI DSS Compliant</span>
                </div>
            </div>

            <div class="quick-stats-card">

                <div class="card-icon blue-icon">

                    <svg width="24" height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2">

                        <rect x="1" y="4"
                            width="22"
                            height="16"
                            rx="2"
                            ry="2"></rect>

                        <line x1="1" y1="10"
                            x2="23" y2="10"></line>

                    </svg>

                </div>

                <h3>Quick Stats</h3>

                <div class="stats-list">

                    <!-- THIS MONTH -->
                    <div class="stat-row">

                        <span>This Month</span>

                        <span class="stat-value green">

                            +₦<?= number_format($thisMonth, 2) ?>

                        </span>

                    </div>

                    <!-- ACTIVE PROJECTS -->
                    <div class="stat-row">

                        <span>Active Projects</span>

                        <span class="stat-value blue">

                            <?= $activeProjects ?>

                        </span>

                    </div>

                    <!-- AVG -->
                    <div class="stat-row">

                        <span>Avg. per Project</span>

                        <span class="stat-value">

                            ₦<?= number_format($avgPerProject, 2) ?>

                        </span>

                    </div>

                </div>

            </div>
        </div>


        <!-- Escrow & Withdrawal -->
        <div class="two-column-grid">

            <!-- Escrow Timeline -->
            <div class="card">

                <div class="card-header">

                    <h3>
                        Escrow Protection
                    </h3>

                    <p class="card-subtitle">
                        Track your secured project payments
                    </p>

                </div>

                <?php if ($escrow): ?>

                    <!-- ALERT -->

                    <div class="escrow-alert">

                        <div class="alert-icon yellow-icon">

                            <svg width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2">

                                <rect x="3"
                                    y="11"
                                    width="18"
                                    height="11"
                                    rx="2"
                                    ry="2"></rect>

                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>

                            </svg>

                        </div>

                        <div>

                            <h4>
                                Funds Held Securely
                            </h4>

                            <p>

                                ₦<?= number_format($escrow['amount'], 2) ?>

                                locked for

                                "<?= htmlspecialchars($escrow['title']) ?>"

                            </p>

                            <?php if ($escrow['status'] === 'held'): ?>

                                <p class="alert-helper">

                                    Escrow protection is active

                                </p>

                            <?php else: ?>

                                <p class="alert-helper">

                                    Funds have been released

                                </p>

                            <?php endif; ?>

                        </div>

                    </div>

                    <!-- TIMELINE -->

                    <div class="timeline">

                        <!-- PROPOSAL ACCEPTED -->

                        <div class="timeline-item completed">

                            <div class="timeline-icon">

                                <svg width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2">

                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>

                                    <polyline points="14 2 14 8 20 8"></polyline>

                                </svg>

                            </div>

                            <div class="timeline-content">

                                <h4>
                                    Proposal Accepted
                                </h4>

                                <p>
                                    Completed
                                </p>

                            </div>

                            <svg class="check-icon"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2">

                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>

                                <polyline points="22 4 12 14.01 9 11.01"></polyline>

                            </svg>

                        </div>

                        <!-- ESCROW FUNDED -->

                        <div class="timeline-item completed">

                            <div class="timeline-icon">

                                <svg width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2">

                                    <rect x="3"
                                        y="11"
                                        width="18"
                                        height="11"
                                        rx="2"
                                        ry="2"></rect>

                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>

                                </svg>

                            </div>

                            <div class="timeline-content">

                                <h4>
                                    Escrow Funded
                                </h4>

                                <p>
                                    Completed
                                </p>

                            </div>

                            <svg class="check-icon"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2">

                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>

                                <polyline points="22 4 12 14.01 9 11.01"></polyline>

                            </svg>

                        </div>

                        <!-- WORK SUBMITTED -->

                        <?php if ($escrow['submission_status']): ?>

                            <div class="timeline-item completed">

                                <div class="timeline-icon">

                                    <svg width="20"
                                        height="20"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2">

                                        <line x1="22"
                                            y1="2"
                                            x2="11"
                                            y2="13"></line>

                                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>

                                    </svg>

                                </div>

                                <div class="timeline-content">

                                    <h4>
                                        Work Submitted
                                    </h4>

                                    <p>
                                        Completed
                                    </p>

                                </div>

                                <svg class="check-icon"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2">

                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>

                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>

                                </svg>

                            </div>

                        <?php else: ?>

                            <div class="timeline-item pending">

                                <div class="timeline-icon">

                                    📤

                                </div>

                                <div class="timeline-content">

                                    <h4>
                                        Work Submission
                                    </h4>

                                    <p>
                                        Waiting
                                    </p>

                                </div>

                            </div>

                        <?php endif; ?>

                        <!-- EMPLOYER REVIEW -->

                        <?php if (
                            $escrow['submission_status'] === 'submitted'
                        ): ?>

                            <div class="timeline-item active">

                                <div class="timeline-icon">

                                    <svg width="20"
                                        height="20"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2">

                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>

                                        <circle cx="12"
                                            cy="12"
                                            r="3"></circle>

                                    </svg>

                                </div>

                                <div class="timeline-content">

                                    <h4>
                                        Employer Review
                                    </h4>

                                    <p>
                                        In progress
                                    </p>

                                </div>

                            </div>

                        <?php endif; ?>

                        <!-- RELEASE -->

                        <?php if ($escrow['status'] === 'released'): ?>

                            <div class="timeline-item completed">

                                <div class="timeline-icon">

                                    <svg width="20"
                                        height="20"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2">

                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>

                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>

                                    </svg>

                                </div>

                                <div class="timeline-content">

                                    <h4>
                                        Payment Released
                                    </h4>

                                    <p>
                                        Completed
                                    </p>

                                </div>

                                <svg class="check-icon"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2">

                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>

                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>

                                </svg>

                            </div>

                        <?php else: ?>

                            <div class="timeline-item pending">

                                <div class="timeline-icon">

                                    <svg width="20"
                                        height="20"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2">

                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>

                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>

                                    </svg>

                                </div>

                                <div class="timeline-content">

                                    <h4>
                                        Payment Released
                                    </h4>

                                    <p>
                                        Waiting
                                    </p>

                                </div>

                            </div>

                        <?php endif; ?>

                    </div>

                    <!-- INFO -->

                    <div class="info-box">

                        <p>

                            <strong>Protection:</strong>

                            Funds are securely held until work approval.
                            Automatic release after 7-day inactivity period.

                        </p>

                    </div>

                <?php else: ?>

                    <!-- EMPTY -->

                    <div class="info-box">

                        <p>

                            No active escrow projects found.

                        </p>

                    </div>

                <?php endif; ?>

            </div>

            <!-- Withdrawal Form -->
            <div class="card">
                <h2 class="card-title">Request Withdrawal</h2>

                <div class="balance-display">
                    <div>
                        <p class="balance-label">Available Balance</p>
                        <p class="balance-amount"> ₦<?= number_format($balance, 2) ?></p>
                    </div>

                </div>
                <form class="withdrawal-form" id="withdrawForm">

                    <div class="form-group">
                        <label>Bank Name</label>

                        <input
                            type="text"
                            class="form-input"
                            list="bank-list"
                            name="bank_name"
                            placeholder="Type or select your bank"
                            required>

                        <datalist id="bank-list">
                            <option value="GTBank">
                            <option value="Access Bank">
                            <option value="Zenith Bank">
                            <option value="First Bank">
                            <option value="UBA">
                            <option value="Opay">
                            <option value="Kuda Bank">
                            <option value="PalmPay">
                            <option value="Moniepoint">
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label>Account Number</label>
                        <input
                            type="text"
                            class="form-input"
                            name="account_number"
                            placeholder="0123456789"
                            maxlength="10"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Account Name</label>
                        <input
                            type="text"
                            class="form-input"
                            name="account_name"
                            placeholder="John Doe"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Withdrawal Amount (₦)</label>
                        <input
                            type="number"
                            class="form-input amount-field"
                            name="amount"
                            placeholder="0.00"
                            required>
                    </div>

                    <div class="warning-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>

                        <div>
                            <p class="warning-title">Pending Approval</p>
                            <p class="warning-text">
                                Withdrawals require admin verification.
                                Expect processing within 24-48 hours.
                            </p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">
                        Submit Withdrawal Request
                    </button>

                </form>

            </div>
        </div>

        <!-- Transaction History -->
        <div class="card" id="transactions">
            <div class="card-header-flex">
                <h2 class="card-title">Transaction History</h2>
                <button class="btn btn-outline-small">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Export CSV
                </button>
            </div>

            <div class="filters-row">
                <div class="filter-buttons">

                    <a href="?filter=all#transactions"
                        class="filter-btn <?= $filter === 'all' ? 'active' : '' ?>">
                        All
                    </a>

                    <a href="?filter=deposit#transactions"
                        class="filter-btn <?= $filter === 'deposit' ? 'active' : '' ?>">
                        Deposits
                    </a>

                    <a href="?filter=payments#transactions"
                        class="filter-btn <?= $filter === 'payments' ? 'active' : '' ?>">
                        Payments
                    </a>

                    <a href="?filter=withdrawal#transactions"
                        class="filter-btn <?= $filter === 'withdrawal' ? 'active' : '' ?>">
                        Withdrawals
                    </a>
                    <a href="?filter=pending#transactions"
                        class="filter-btn <?= $filter === 'pending' ? 'active' : '' ?>">
                        Pending
                    </a>
                    <a href="?filter=failed#transactions"
                        class="filter-btn <?= $filter === 'failed' ? 'active' : '' ?>">
                        Failed
                    </a>
                </div>

                <form method="GET" class="search-form">

                    <div class="search-input-wrapper">

                        <svg class="input-icon"
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2">

                            <circle cx="11" cy="11" r="8"></circle>

                            <path d="m21 21-4.35-4.35"></path>

                        </svg>

                        <input
                            type="text"
                            name="search"
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                            placeholder="Search by reference..."
                            class="search-input-small">

                    </div>

                </form>
            </div>

            <div class="transactions-list">

                <?php foreach ($transactions as $tx): ?>

                    <?php

                    $positive = in_array(
                        $tx['type'],
                        ['deposit', 'payment_received', 'refund', 'escrow_release']
                    );

                    $amountClass = $positive
                        ? 'positive'
                        : 'negative';

                    $iconClass = match ($tx['type']) {

                        'deposit' => 'green-tx',
                        'payment_received' => 'green-tx',
                        'withdrawal' => 'red-tx',
                        'escrow_hold' => 'yellow-tx',
                        'escrow_release' => 'green-tx',
                        'refund' => 'blue-tx',

                        default => 'green-tx'
                    };

                    $label = match ($tx['type']) {

                        'deposit' => 'Wallet Funding',
                        'payment_received' => 'Payment Received',
                        'withdrawal' => 'Withdrawal Request',
                        'escrow_hold' => 'Escrow Hold',
                        'escrow_release' => 'Escrow Released',
                        'refund' => 'Refund',

                        default => ucfirst(str_replace('_', ' ', $tx['type']))
                    };

                    ?>

                    <div class="transaction-item">

                        <div class="transaction-icon <?= $iconClass ?>">

                            <?php if ($tx['type'] === 'deposit'): ?>
                                +
                            <?php elseif ($tx['type'] === 'withdrawal'): ?>
                                ↗
                            <?php elseif ($tx['type'] === 'escrow_hold'): ?>
                                🔒
                            <?php elseif ($tx['type'] === 'refund'): ?>
                                ↺
                            <?php else: ?>
                                ✓
                            <?php endif; ?>

                        </div>

                        <div class="transaction-details">

                            <h4>
                                <?= htmlspecialchars($label) ?>
                            </h4>

                            <p>
                                <?= htmlspecialchars($tx['description']) ?>
                            </p>

                            <?php if (
                                $tx['type'] === 'payment_received'
                                && $tx['platform_fee'] > 0
                            ): ?>

                                <p class="text-sm text-gray-500 mt-1">

                                    Platform fee:
                                    ₦<?= number_format(
                                            $tx['platform_fee'],
                                            2
                                        ) ?>

                                </p>

                            <?php endif; ?>

                            <?php if (!empty($tx['job_title'])): ?>

                                <p class="job-link">
                                    → <?= htmlspecialchars($tx['job_title']) ?>
                                </p>

                            <?php endif; ?>

                            <div class="transaction-meta">

                                <span class="status-badge <?= $tx['status'] ?>">

                                    <?= ucfirst($tx['status']) ?>

                                </span>

                                <span class="meta-text">

                                    <?= date(
                                        "Y-m-d H:i",
                                        strtotime($tx['created_at'])
                                    ) ?>

                                </span>

                                <?php if (!empty($tx['reference'])): ?>

                                    <span class="meta-text">

                                        Ref:
                                        <?= htmlspecialchars($tx['reference']) ?>

                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>
                        <?php

                        $displayAmount = $tx['amount'];

                        /* Show net earnings after fee */

                        if (
                            $tx['type'] === 'payment_received'
                            && !empty($tx['net_amount'])
                        ) {

                            $displayAmount = $tx['net_amount'];
                        }

                        ?>

                        <div class="transaction-amount <?= $amountClass ?>">

                            <?= $positive ? '+' : '-' ?>

                            ₦<?= number_format($displayAmount, 2) ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>
        </div>
    </main>
    <script src="https://js.paystack.co/v1/inline.js"></script>

    <script>
        function payWithPaystack() {

            let amountInput = document.getElementById("amount").value;

            if (!amountInput || amountInput <= 0) {
                alert("Enter a valid amount");
                return;
            }

            let amount = parseFloat(amountInput);

            let handler = PaystackPop.setup({

                key: 'pk_test_4988996324d62da606215d93acc2c4295f7e3bc6',

                email: "<?php echo $_SESSION['email']; ?>",

                amount: amount * 100,

                metadata: {
                    user_id: "<?php echo $_SESSION['user_id']; ?>"
                },

                currency: "NGN",

                callback: function(response) {

                    window.location.href =
                        "../wallet/verify_payment.php?reference=" +
                        response.reference;
                },

                onClose: function() {
                    alert("Payment cancelled");
                }
            });

            handler.openIframe();
        }

        const quickButtons = document.querySelectorAll(".quick-btn");

        quickButtons.forEach(button => {

            button.addEventListener("click", function() {

                let value = this.innerText
                    .replace("₦", "")
                    .replace(/,/g, "");

                document.getElementById("amount").value = value;
            });

        });


        document.getElementById("withdrawForm").addEventListener("submit", async function(e) {

            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            try {

                const response = await fetch("withdraw.php", {
                    method: "POST",
                    body: formData
                });

                const result = await response.text();

                alert(result);

                form.reset();

            } catch (error) {

                alert("Something went wrong.");

                console.error(error);
            }

        });

        document.querySelector(".search-input-small")
            .addEventListener("input", function() {

                if (e.key === "Enter") {
                    this.form.submit();
                }


            });
    </script>
</body>

</html>