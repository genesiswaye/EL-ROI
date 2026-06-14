<?php

// session_start();

require_once "../config/database.php";

$stmt = $pdo->prepare("
SELECT
    w.id,
    w.user_id,
    w.amount,
    w.reference,
    w.status,
    w.created_at,
    w.processed_at,
    w.bank_name,
    w.account_number,
    w.account_name,
    u.full_name,
    u.email
FROM withdrawals w
JOIN users u
ON w.user_id = u.id
WHERE w.status = 'pending'
ORDER BY w.created_at DESC
");

$stmt->execute();

$withdrawals = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Pending */

$stmt = $pdo->query("
SELECT COUNT(*) 
FROM withdrawals
WHERE status='pending'
");

$pendingCount = $stmt->fetchColumn();

/* Approved Today */

$stmt = $pdo->query("
SELECT COUNT(*)
FROM withdrawals
WHERE status='paid'
AND DATE(processed_at)=CURDATE()
");

$approvedToday = $stmt->fetchColumn();

/* Total Volume */

$stmt = $pdo->query("
SELECT COALESCE(SUM(amount),0)
FROM withdrawals
WHERE status='paid'
");

$totalVolume = $stmt->fetchColumn();

/* Failed */

$stmt = $pdo->query("
SELECT COUNT(*)
FROM withdrawals
WHERE status='failed'
");

$failedCount = $stmt->fetchColumn();

if (empty($_SESSION['csrf_token'])) {

    $_SESSION['csrf_token'] =
        bin2hex(
            random_bytes(32)
        );

}

$csrf_token =
    $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawals Management - StudentLancer Admin</title>
    <link rel="stylesheet" href="withdrawals-style.css">
</head>

<body>
    <?php
    $activePage = "withdrawal";
    include "admins2.php";
    ?>
    <!-- Detail Side Panel -->
    <div id="detail-panel" class="detail-panel">
        <div class="detail-panel-content">
            <div class="detail-panel-header">
                <h3 class="detail-panel-title">Withdrawal Details</h3>
                <button class="panel-close" onclick="closeDetailPanel()">&times;</button>
            </div>
            <div id="detail-panel-body" class="detail-panel-body">
                <!-- Details will be populated here -->
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-title">Confirm Action</h3>
                <button class="modal-close" onclick="closeConfirmationModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p id="modal-message">Are you sure you want to proceed?</p>
                <div id="modal-details"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeConfirmationModal()">Cancel</button>
                <button class="btn btn-primary" id="modal-confirm-btn" onclick="confirmAction()">Confirm</button>
            </div>
        </div>
    </div>

    <div class="page-container">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Withdrawals Management</h1>
                <p class="page-subtitle">Secure Payout Operations & Transaction Processing</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-orange">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <span class="stat-trend stat-trend-down">Pending</span>
                </div>
                <div class="stat-value"><?= number_format($pendingCount) ?></div>
                <div class="stat-label">Pending Withdrawals</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-green">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <span class="stat-trend stat-trend-up">+8</span>
                </div>
                <div class="stat-value"><?= number_format($approvedToday) ?></div>
                <div class="stat-label">Approved Today</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                </div>
                <div class="stat-value">₦<?= number_format($totalVolume, 2) ?></div>
                <div class="stat-label">Total Volume</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-red">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                    </div>
                </div>
                <div class="stat-value"><?= number_format($failedCount) ?></div>
                <div class="stat-label">Flagged Withdrawals</div>
            </div>
        </div>

        <!-- Withdrawals Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Withdrawal Requests</h3>
                <div class="filters-container">
                    <div class="search-input">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <input type="text" id="withdrawal-search" placeholder="Search withdrawals..." onkeyup="filterWithdrawals()">
                    </div>

                    <select class="filter-select" id="status-filter" onchange="filterWithdrawals()">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="processing">Processing</option>
                        <option value="failed">Failed</option>
                    </select>

                    <select class="filter-select" id="risk-filter" onchange="filterWithdrawals()">
                        <option value="">All Risk Levels</option>
                        <option value="low">Low Risk</option>
                        <option value="medium">Medium Risk</option>
                        <option value="high">High Risk</option>
                    </select>
                </div>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Bank Name</th>
                            <th>Account Number</th>
                            <th>Request Date</th>
                            <th>Status</th>
                            <th>Risk Level</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="withdrawals-table-body">
                        <?php foreach ($withdrawals as $w): ?>

                            <tr>

                                <td>

                                    <div class="user-cell">

                                        <div class="user-avatar">

                                            <?= strtoupper(substr($w['full_name'], 0, 1)) ?>

                                        </div>

                                        <div class="user-info">

                                            <div class="user-name">
                                                <?= htmlspecialchars($w['full_name']) ?>
                                            </div>

                                            <div class="user-email">
                                                <?= htmlspecialchars($w['email']) ?>
                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <td>

                                    <div class="amount-cell">

                                        ₦<?= number_format($w['amount'], 2) ?>

                                    </div>

                                </td>

                                <td>

                                    <div class="bank-name">

                                        <?= htmlspecialchars($w['bank_name']) ?>

                                    </div>

                                </td>

                                <td>

                                    <div class="account-number">

                                        <?= htmlspecialchars($w['account_number']) ?>

                                    </div>

                                </td>

                                <td>

                                    <div class="date-cell">

                                        <?= date(
                                            'M d, Y H:i',
                                            strtotime($w['created_at'])
                                        ) ?>

                                    </div>

                                </td>

                                <td>

                                    <span class="status-badge status-<?= $w['status'] ?>">

                                        <?= ucfirst($w['status']) ?>

                                    </span>

                                </td>

                                <td>

                                    <div class="risk-indicator">

                                        <div class="risk-dot risk-low"></div>

                                        <span class="risk-text">

                                            Low

                                        </span>

                                    </div>

                                </td>

                                <td>

                                    <div class="action-buttons">

                                        <button
                                            class="btn btn-sm btn-primary"
                                            onclick="openDetailPanel(<?= htmlspecialchars(json_encode($w), ENT_QUOTES) ?>)">

                                            View

                                        </button>

                                        <?php if ($w['status'] === 'pending'): ?>

                                            <form
                                                method="POST"
                                                action="process_withdrawal.php"
                                                style="display:inline;">

                                                <input
                                                    type="hidden"
                                                    name="withdrawal_id"
                                                    value="<?= $w['id'] ?>">

                                                <input
                                                    type="hidden"
                                                    name="action"
                                                    value="approve">

                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= $csrf_token ?>">

                                                <button
                                                    class="btn btn-sm btn-success">

                                                    Approve

                                                </button>

                                            </form>

                                            <form
                                                method="POST"
                                                action="process_withdrawal.php"
                                                style="display:inline;">

                                                <input
                                                    type="hidden"
                                                    name="withdrawal_id"
                                                    value="<?= $w['id'] ?>">

                                                <input
                                                    type="hidden"
                                                    name="action"
                                                    value="reject">

                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= $csrf_token ?>">

                                                <button
                                                    class="btn btn-sm btn-error">

                                                    Reject

                                                </button>

                                            </form>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="withdrawals-script.js"></script>
</body>

</html>