<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];

/* =========================
   USER INFO
========================= */

$stmt = $pdo->prepare("
    SELECT full_name, role
    FROM users
    WHERE id = ?
");

$stmt->execute([$user_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

$full_name = $user['full_name'] ?? 'User';
$role = $user['role'] ?? '';

/* =========================
   ACTIVE GIGS
========================= */

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM applications
    WHERE student_id = ?
    AND status IN ('accepted', 'in_progress')
");

$stmt->execute([$user_id]);

$activeGigs = $stmt->fetchColumn();

/* =========================
   COMPLETED JOBS
========================= */

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM applications
    WHERE student_id = ?
    AND status = 'completed'
");

$stmt->execute([$user_id]);

$completedJobs = $stmt->fetchColumn();

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

$pendingApplications = $stmt->fetchColumn();

/* =========================
   TOTAL EARNINGS
========================= */

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(amount),0)
    FROM transactions
    WHERE user_id = ?
    AND type IN ('payment_received', 'escrow_release')
    AND status = 'success'
");

$stmt->execute([$user_id]);

$totalEarnings = $stmt->fetchColumn();

/* =========================
   WALLET BALANCE
========================= */

$stmt = $pdo->prepare("
    SELECT balance
    FROM wallets
    WHERE user_id = ?
");

$stmt->execute([$user_id]);

$wallet = $stmt->fetch(PDO::FETCH_ASSOC);

$walletBalance = $wallet['balance'] ?? 0;

/* =========================
   PENDING PAYMENTS
========================= */

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(amount),0)
    FROM escrows
    WHERE freelancer_id = ?
    AND status = 'held'
");

$stmt->execute([$user_id]);

$pendingPayments = $stmt->fetchColumn();

/* =========================
   RECOMMENDED JOBS
========================= */

$stmt = $pdo->prepare("
    SELECT id, title, budget, deadline
    FROM jobs
    WHERE status = 'open'
    ORDER BY created_at DESC
    LIMIT 5
");

$stmt->execute();

$recommendedJobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   RECENT ACTIVITY
========================= */

$stmt = $pdo->prepare("
    SELECT
        type,
        amount,
        description,
        created_at
    FROM transactions
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 5
");

$stmt->execute([$user_id]);

$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM jobs
    WHERE created_by = ?
");

$stmt->execute([$user_id]);

$totalJobsPosted = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM jobs
    WHERE created_by = ?
    AND status = 'completed'
");

$stmt->execute([$user_id]);

$completedEmployerJobs = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM jobs
    WHERE created_by = ?
    AND status = 'in_progress'
");

$stmt->execute([$user_id]);

$activeEmployerJobs = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM applications
    JOIN jobs ON applications.job_id = jobs.id
    WHERE jobs.created_by = ?
");

$stmt->execute([$user_id]);

$totalApplicants = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(amount),0)
    FROM transactions
    WHERE user_id = ?
    AND type = 'escrow_hold'
    AND status = 'success'
");

$stmt->execute([$user_id]);

$totalSpent = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CampusLink</title>
    <link rel="stylesheet" href="overview.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Top Navigation -->
    <?php
    $activePage = "overview";
    include "../includes/employer_nav.php";
    ?>

    <div class="dashboard-layout">
        <!-- Left Sidebar -->

        <!-- Main Content -->
        <main class="main-content">
            <!-- Welcome Section -->
            <!-- Welcome Section -->
            <section class="welcome-section">

                <div>

                    <h1 class="welcome-title">
                        Welcome back, <?= htmlspecialchars($full_name) ?>
                    </h1>

                    <p class="welcome-text">

                        You have

                        <?= $activeGigs ?>

                        active gigs and

                        <?= $pendingApplications ?>

                        pending applications

                    </p>

                </div>

            </section>

            <!-- Stat Cards -->
            <section class="stats-grid">
                <?php if ($role === 'student'): ?>
                    <!-- ACTIVE GIGS -->
                    <div class="stat-card">

                        <div class="stat-header">

                            <span class="stat-label">
                                Active Gigs
                            </span>

                            <div class="stat-icon stat-icon-purple">

                                <svg width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2">

                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>

                                    <circle cx="9" cy="7" r="4"></circle>

                                </svg>

                            </div>

                        </div>

                        <div class="stat-value">

                            <?= $activeGigs ?>

                        </div>

                        <div class="stat-footer">

                            <span class="stat-change positive">
                                Currently active
                            </span>

                        </div>

                    </div>
                <?php endif; ?>
                <!-- COMPLETED JOBS -->
                <div class="stat-card">

                    <div class="stat-header">

                        <span class="stat-label">
                            Jobs Posted
                        </span>

                    </div>

                    <div class="stat-value">

                        <?= $totalJobsPosted ?>

                    </div>

                </div>
                <div class="stat-card">

                    <div class="stat-header">

                        <span class="stat-label">
                            Total Amount Spent
                        </span>

                    </div>

                    <div class="stat-value">

                        ₦<?= number_format($totalSpent, 2) ?>

                    </div>

                </div>
                <?php if ($role === 'student'): ?>
                    <!-- COMPLETED JOBS -->
                    <div class="stat-card">

                        <div class="stat-header">

                            <span class="stat-label">
                                Completed Jobs
                            </span>

                            <div class="stat-icon stat-icon-green">

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

                        </div>

                        <div class="stat-value">

                            <?= $completedJobs ?>

                        </div>

                    </div>
                <?php endif; ?>

                <!-- PENDING APPLICATIONS -->
                <div class="stat-card">

                    <div class="stat-header">

                        <span class="stat-label">
                            Pending Applications
                        </span>

                        <div class="stat-icon stat-icon-blue">

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

                    </div>

                    <div class="stat-value">

                        <?= $pendingApplications ?>

                    </div>

                    <div class="stat-footer">

                        <span class="stat-change">
                            Awaiting review
                        </span>

                    </div>

                </div>
                <?php if ($role === 'student'): ?>
                    <!-- TOTAL EARNINGS -->
                    <div class="stat-card">

                        <div class="stat-header">

                            <span class="stat-label">
                                Total Earnings
                            </span>

                            <div class="stat-icon stat-icon-green">

                                <svg width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2">

                                    <line x1="12" y1="1" x2="12" y2="23"></line>

                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>

                                </svg>

                            </div>

                        </div>

                        <div class="stat-value">

                            ₦<?= number_format($totalEarnings, 2) ?>

                        </div>

                    </div>
                <?php endif; ?>

                <!-- WALLET BALANCE -->
                <div class="stat-card">

                    <div class="stat-header">

                        <span class="stat-label">
                            Wallet Balance
                        </span>

                        <div class="stat-icon stat-icon-orange">

                            <svg width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2">

                                <rect width="20"
                                    height="14"
                                    x="2"
                                    y="5"
                                    rx="2"></rect>

                                <line x1="2"
                                    y1="10"
                                    x2="22"
                                    y2="10"></line>

                            </svg>

                        </div>

                    </div>

                    <div class="stat-value">

                        ₦<?= number_format($walletBalance, 2) ?>

                    </div>

                    <div class="stat-footer">

                        <span class="stat-change">
                            Available to withdraw
                        </span>

                    </div>

                </div>
                <?php if ($role === 'student'): ?>
                    <!-- PENDING PAYMENTS -->
                    <div class="stat-card">

                        <div class="stat-header">

                            <span class="stat-label">
                                Pending Payments
                            </span>

                            <div class="stat-icon stat-icon-amber">

                                <svg width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2">

                                    <circle cx="12" cy="12" r="10"></circle>

                                    <polyline points="12 6 12 12 16 14"></polyline>

                                </svg>

                            </div>

                        </div>

                        <div class="stat-value">

                            ₦<?= number_format($pendingPayments, 2) ?>

                        </div>

                    </div>
                <?php endif; ?>
            </section>


            <!-- Two Column Layout -->
            <div class="content-columns">
                <?php if ($role === 'student'): ?>
                    <!-- RECOMMENDED GIGS -->
                    <section class="gigs-section">

                        <div class="section-header">

                            <h2 class="section-title">
                                Recommended Gigs
                            </h2>

                            <a href="../jobs/browse_jobs.php"
                                class="view-all-link">

                                View all

                            </a>

                        </div>

                        <div class="gigs-list">

                            <?php foreach ($recommendedJobs as $job): ?>

                                <div class="gig-card">

                                    <h3 class="gig-title">

                                        <?= htmlspecialchars($job['title']) ?>

                                    </h3>

                                    <!-- <p class="gig-company">

                                        CampusLink Project

                                    </p> -->

                                    <div class="gig-details">

                                        <div class="gig-detail-item">

                                            <svg width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2">

                                                <line x1="12" y1="1" x2="12" y2="23"></line>

                                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>

                                            </svg>

                                            <span>

                                                ₦<?= number_format($job['budget']) ?>

                                            </span>

                                        </div>

                                        <div class="gig-detail-item">

                                            <svg width="16"
                                                height="16"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2">

                                                <rect width="18"
                                                    height="18"
                                                    x="3"
                                                    y="4"
                                                    rx="2"></rect>

                                                <line x1="16"
                                                    y1="2"
                                                    x2="16"
                                                    y2="6"></line>

                                            </svg>

                                            <span>

                                                Due:
                                                <?= date("M d, Y", strtotime($job['deadline'])) ?>

                                            </span>

                                        </div>

                                    </div>

                                    <a href="../jobs/view_job.php?job_id=<?= $job['id'] ?>" style="text-decoration: none;"
                                        class="btn-apply">

                                        Apply Now

                                    </a>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    </section>
                <?php endif; ?>

                <!-- RECENT ACTIVITY -->
                <section class="activity-section">

                    <div class="section-header">

                        <h2 class="section-title">
                            Recent Activity
                        </h2>

                    </div>

                    <div class="activity-list">

                        <?php foreach ($activities as $activity): ?>

                            <div class="activity-item">

                                <div class="activity-icon activity-icon-payment">

                                    <svg width="16"
                                        height="16"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2">

                                        <line x1="12" y1="1" x2="12" y2="23"></line>

                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>

                                    </svg>

                                </div>

                                <div class="activity-content">

                                    <p class="activity-text">

                                        <?= ucfirst(str_replace('_', ' ', $activity['type'])) ?>

                                    </p>

                                    <p class="activity-description">

                                        <?= htmlspecialchars($activity['description']) ?>

                                    </p>

                                    <p class="activity-time">

                                        <?= date(
                                            "M d, Y h:i A",
                                            strtotime($activity['created_at'])
                                        ) ?>

                                    </p>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </section>

            </div>
        </main>
    </div>
</body>

</html>