<?php

// require "middleware.php";
// require "helper.php";

// requireRole([
//     'support_admin',
//     'super_admin'
// ]);

require_once "../config/database.php";

try {

    $stmt = $pdo->query(

        "
        SELECT

            d.id,
            d.reason,
            d.status,
            d.created_at,

            j.title AS job_title,

            u.full_name AS opened_by,

            e.amount AS escrow_amount

        FROM disputes d

        JOIN jobs j
            ON d.job_id = j.id

        JOIN users u
            ON d.opened_by = u.id

        LEFT JOIN escrows e
            ON d.job_id = e.job_id

        ORDER BY d.created_at DESC
        "

    );

    $disputes = $stmt->fetchAll(
        PDO::FETCH_ASSOC
    );

    $openDisputes = 0;
$resolvedToday = 0;
$escrowAtRisk = 0;

$today = date('Y-m-d');

foreach ($disputes as $d) {

    if (
        $d['status'] === 'open'
        ||
        $d['status'] === 'review'
    ) {

        $openDisputes++;

        $escrowAtRisk +=
            (float)($d['escrow_amount'] ?? 0);
    }

    if (
        $d['status'] === 'resolved'
        &&
        date(
            'Y-m-d',
            strtotime($d['created_at'])
        ) === $today
    ) {

        $resolvedToday++;
    }
}
    
} catch (Exception $e) {

    die(
        $e->getMessage()
    );

}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispute Management - StudentLancer Admin</title>
    <link rel="stylesheet" href="disputes-style.css">
</head>

<body>
    <?php
    $activePage = "dispute";
    include "admins2.php";
    ?>
    <div class="page-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Dispute Management</h1>
            <p class="page-subtitle">Escrow Operations & Dispute Resolution Center</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-red">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <span class="stat-trend stat-trend-down">Active</span>
                </div>
                <div class="stat-value"><?= $openDisputes ?></div>
                <div class="stat-label">Open Disputes</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-green">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <span class="stat-trend stat-trend-up">+12</span>
                </div>
                <div class="stat-value"><?= $resolvedToday ?></div>
                <div class="stat-label">Resolved Today</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-orange">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                </div>
                <div class="stat-value">₦<?= number_format($escrowAtRisk, 2) ?></div>
                <div class="stat-label">Escrow At Risk</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="stat-value" id="avg-resolution">2.4h</div>
                <div class="stat-label">Avg Resolution Time</div>
            </div>
        </div>

        <!-- Disputes Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Disputes</h3>
                <div class="filters-row">
                    <div class="search-input">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <input type="text" id="dispute-search" placeholder="Search disputes..." onkeyup="filterDisputes()">
                    </div>
                    <select class="filter-select" id="status-filter" onchange="filterDisputes()">
                        <option value="">All Status</option>
                        <option value="open">Open</option>
                        <option value="review">Under Review</option>
                        <option value="resolved">Resolved</option>
                        <option value="refunded">Refunded</option>
                        <option value="released">Released</option>
                    </select>
                    <select class="filter-select" id="priority-filter" onchange="filterDisputes()">
                        <option value="">All Priority</option>
                        <option value="high">High Risk</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Job</th>
                            <th>Opened By</th>
                            <th>Escrow Amount</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="disputes-table-body">

                        <?php foreach ($disputes as $dispute): ?>

                            <tr>

                                <td>

                                    <div class="job-cell">

                                        <div>

                                            <span class="priority-indicator priority-medium"></span>

                                            <span class="job-title">

                                                <?= htmlspecialchars($dispute['job_title']) ?>

                                            </span>

                                        </div>

                                        <span class="job-id">

                                            #<?= $dispute['id'] ?>

                                        </span>

                                    </div>

                                </td>

                                <td>

                                    <div class="user-cell">

                                        <div class="user-avatar">

                                            <?= strtoupper(substr($dispute['opened_by'], 0, 1)) ?>

                                        </div>

                                        <div class="user-info">

                                            <div class="user-name">

                                                <?= htmlspecialchars($dispute['opened_by']) ?>

                                            </div>

                                            <div class="user-role">

                                                User

                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <td>

                                    <div class="escrow-amount">

                                        ₦<?= number_format($dispute['escrow_amount'] ?? 0, 2) ?>

                                    </div>

                                    <div class="escrow-status">

                                        Escrow
                                    </div>

                                </td>

                                <td>

                                    <div class="dispute-reason">

                                        <?= htmlspecialchars($dispute['reason']) ?>

                                    </div>

                                </td>

                                <td>

                                    <?php

                                    $badgeClass = match ($dispute['status']) {

                                        'open' => 'badge-open',

                                        'review' => 'badge-review',

                                        'resolved' => 'badge-resolved',

                                        'released' => 'badge-released',

                                        'refunded' => 'badge-refunded',

                                        default => 'badge-open'
                                    };

                                    ?>

                                    <span class="badge <?= $badgeClass ?>">

                                        <?= ucfirst($dispute['status']) ?>

                                    </span>

                                </td>

                                <td>

                                    <div class="date-cell">

                                        <?= date(
                                            "d M Y",
                                            strtotime($dispute['created_at'])
                                        ) ?>

                                    </div>

                                </td>

                                <td>

                                    <a
                                        href="view_dispute.php?id=<?= $dispute['id'] ?>"
                                        class="btn btn-primary">

                                        View

                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        <?php if (empty($disputes)): ?>

                            <tr>

                                <td colspan="7" style="text-align:center;padding:40px;">

                                    No disputes found

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Dispute Details Modal -->
    <div id="dispute-modal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Dispute Details</h3>
                <button class="modal-close" onclick="closeDisputeModal()">&times;</button>
            </div>
            <div class="modal-body" id="dispute-modal-body">
                <!-- Details will be populated here -->
            </div>
        </div>
    </div>

    <script src="disputes-script.js"></script>
</body>

</html>