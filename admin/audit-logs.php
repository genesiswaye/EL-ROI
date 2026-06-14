<?php


require_once "../config/database.php";


$stmt = $pdo->query("
    SELECT
        audit_logs.*,
        users.full_name
    FROM audit_logs

    LEFT JOIN users
        ON audit_logs.user_id = users.id

    ORDER BY audit_logs.created_at DESC
");

$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
Statistics
*/

$todayEvents = 0;
$criticalEvents = 0;
$adminActions = 0;
$fraudLogs = 0;

foreach ($logs as $log) {

    if (
        date('Y-m-d', strtotime($log['created_at']))
        === date('Y-m-d')
    ) {
        $todayEvents++;
    }

    if (
        strpos(
            strtolower($log['action']),
            'fraud'
        ) !== false
    ) {
        $fraudLogs++;
        $criticalEvents++;
    }

    if (
        strpos(
            strtolower($log['action']),
            'admin'
        ) !== false
    ) {
        $adminActions++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - StudentLancer Admin</title>
    <link rel="stylesheet" href="audit-logs-style.css">
</head>

<body>
    <?php
    $activePage = "audit-logs";
    include "admins2.php";
    ?>
    <div class="page-container">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Audit Logs</h1>
                <p class="page-subtitle">Security Monitoring & Activity Timeline</p>
            </div>
            <button class="btn btn-primary" onclick="exportToCSV()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Export CSV
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                    <span class="stat-trend stat-trend-up">+24%</span>
                </div>
                <div class="stat-value" id="today-events"><?= $todayEvents ?></div>
                <div class="stat-label">Today's Events</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-red">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <span class="stat-trend stat-trend-down">Critical</span>
                </div>
                <div class="stat-value" id="critical-events"><?= $criticalEvents ?></div>
                <div class="stat-label">Critical Events</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-purple">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                </div>
                <div class="stat-value" id="admin-actions"><?= $adminActions ?></div>
                    <div class="stat-label">Admin Actions</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon stat-icon-orange">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value" id="fraud-logs"><?= $fraudLogs ?></div>
                    <div class="stat-label">Fraud Related</div>
                </div>
            </div>

            <!-- Audit Logs Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Activity Timeline</h3>
                    <div class="filters-container">
                        <div class="search-input">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            <input type="text" id="log-search" placeholder="Search logs..." onkeyup="filterLogs()">
                        </div>

                        <select class="filter-select" id="action-filter" onchange="filterLogs()">
                            <option value="">All Actions</option>
                            <option value="login">Login</option>
                            <option value="withdrawal">Withdrawal</option>
                            <option value="escrow">Escrow</option>
                            <option value="dispute">Dispute</option>
                            <option value="admin">Admin Action</option>
                            <option value="fraud">Fraud Flag</option>
                        </select>

                        <select class="filter-select" id="severity-filter" onchange="filterLogs()">
                            <option value="">All Severity</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>

                        <select class="filter-select" id="user-filter" onchange="filterLogs()">
                            <option value="">All Users</option>
                            <option value="admin">Admins</option>
                            <option value="student">Students</option>
                            <option value="company">Companies</option>
                            <option value="system">System</option>
                        </select>

                        <input type="date" class="filter-date" id="date-filter" onchange="filterLogs()">
                    </div>
                </div>

                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>User</th>
                                <th>Target Type</th>
                                <th>Target ID</th>
                                <th>IP Address</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody id="logs-table-body">

                            <?php foreach ($logs as $log): ?>

                                <?php

                                $severity = 'low';

                                if (
                                    str_contains(
                                        strtolower($log['action']),
                                        'fraud'
                                    )
                                ) {

                                    $severity = 'critical';
                                } elseif (

                                    str_contains(
                                        strtolower($log['action']),
                                        'dispute'
                                    )

                                ) {

                                    $severity = 'high';
                                } elseif (

                                    str_contains(
                                        strtolower($log['action']),
                                        'withdrawal'
                                    )

                                ) {

                                    $severity = 'medium';
                                }

                                ?>

                                <tr>

                                    <td>
                                        +
                                    </td>

                                    <td>

                                        <div class="action-cell">

                                            <div class="action-info">

                                                <div class="action-name">

                                                    <?= htmlspecialchars(
                                                        $log['action']
                                                    ) ?>

                                                </div>

                                                <span class="severity-badge severity-<?= $severity ?>">

                                                    <?= ucfirst($severity) ?>

                                                </span>

                                            </div>

                                        </div>

                                    </td>

                                    <td>

                                        <div class="log-description">

                                            <?= htmlspecialchars(
                                                $log['description']
                                            ) ?>

                                        </div>

                                    </td>

                                    <td>

                                        <div class="user-cell">

                                            <div class="user-avatar">

                                                <?= strtoupper(
                                                    substr(
                                                        $log['full_name']
                                                            ?? 'S',
                                                        0,
                                                        1
                                                    )
                                                ) ?>

                                            </div>

                                            <span class="user-name">

                                                <?= htmlspecialchars(
                                                    $log['full_name']
                                                        ?? 'System'
                                                ) ?>

                                            </span>

                                        </div>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $log['target_type']
                                                ?? '-'
                                        ) ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $log['target_id']
                                                ?? '-'
                                        ) ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $log['ip_address']
                                                ?? '-'
                                        ) ?>

                                    </td>

                                    <td>

                                        <?= date(
                                            "M d, Y H:i",
                                            strtotime(
                                                $log['created_at']
                                            )
                                        ) ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            <?php if (empty($logs)): ?>

                                <tr>

                                    <td colspan="8">

                                        No audit logs found

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            function filterLogs() {

                let search =
                    document
                    .getElementById("log-search")
                    .value
                    .toLowerCase();

                let rows =
                    document.querySelectorAll(
                        "#logs-table-body tr"
                    );

                rows.forEach(row => {

                    row.style.display =
                        row.innerText
                        .toLowerCase()
                        .includes(search)

                        ?

                        ""

                        :

                        "none";

                });

            }
        </script>
</body>

</html>