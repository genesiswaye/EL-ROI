<?php
// require "middleware.php";
// require "helper.php";
require_once "../config/database.php";


$totalUsers = $pdo->query("
    SELECT COUNT(*) FROM users
")->fetchColumn();

$activeStudents = $pdo->query("
    SELECT COUNT(*)
    FROM users
    WHERE role='student'
")->fetchColumn();

$activeEmployers = $pdo->query("
    SELECT COUNT(*)
    FROM users
    WHERE role='company'
")->fetchColumn();

$openJobs = $pdo->query("
    SELECT COUNT(*)
    FROM jobs
    WHERE status='open'
")->fetchColumn();

$jobsInProgress = $pdo->query("
    SELECT COUNT(*)
    FROM jobs
    WHERE status='in_progress'
")->fetchColumn();

$completedJobs = $pdo->query("
    SELECT COUNT(*)
    FROM jobs
    WHERE status='completed'
")->fetchColumn();

$totalEscrow = $pdo->query("
    SELECT COALESCE(SUM(amount),0)
    FROM escrows
")->fetchColumn();

$pendingWithdrawals = $pdo->query("
    SELECT COUNT(*)
    FROM withdrawals
    WHERE status='pending'
")->fetchColumn();

$activeDisputes = $pdo->query("
    SELECT COUNT(*)
    FROM disputes
    WHERE status='open'
")->fetchColumn();
$recentWithdrawals = $pdo->query("
    SELECT
        w.amount,
        w.status,
        w.created_at,
        u.full_name
    FROM withdrawals w
    JOIN users u
        ON w.user_id = u.id
    ORDER BY w.created_at DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$recentDisputes = $pdo->query("
    SELECT
        d.status,
        j.title,
        u.full_name,
        e.amount
    FROM disputes d
    JOIN jobs j
        ON d.job_id = j.id
    JOIN users u
        ON d.opened_by = u.id
    LEFT JOIN escrows e
        ON d.job_id = e.job_id
    ORDER BY d.created_at DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
$recentActivity = $pdo->query("
    SELECT
        a.action,
        a.created_at,
        u.full_name
    FROM audit_logs a
    LEFT JOIN users u
        ON a.user_id = u.id
    ORDER BY a.created_at DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
$platformRevenue = $pdo->query("
    SELECT COALESCE(SUM(amount * 0.05),0)
    FROM escrows
    WHERE status='released'
")->fetchColumn();
$pendingWithdrawals = $pdo->query("
SELECT COUNT(*)
FROM withdrawals
WHERE status='pending'
")->fetchColumn();
$openDisputes = $pdo->query("
SELECT COUNT(*)
FROM disputes
WHERE status='open'
")->fetchColumn();
$totalEscrow = $pdo->query("
    SELECT COALESCE(SUM(amount),0)
    FROM escrows
    WHERE status = 'released'
")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - StudentLancer</title>
    <link rel="stylesheet" href="admin-dashboard.css">
</head>

<body>
    <!-- Top Navigation Bar -->
    <?php
    $activePage = "admin-dashboard";
    include "admins2.php";
    ?>

    <!-- Notification Center -->
    <div class="notification-center" id="notificationCenter">
        <div class="notification-header">
            <h3>Notifications</h3>
            <button class="notification-close" onclick="toggleNotifications()">×</button>
        </div>
        <div class="notification-list">
            <div class="notification-item unread">
                <div class="notification-icon critical">⚠️</div>
                <div class="notification-content">
                    <div class="notification-title">Critical Fraud Alert</div>
                    <div class="notification-description">Account takeover detected - Marcus Johnson</div>
                    <div class="notification-time">2 minutes ago</div>
                </div>
            </div>
            <div class="notification-item unread">
                <div class="notification-icon warning">💰</div>
                <div class="notification-content">
                    <div class="notification-title">Withdrawal Pending</div>
                    <div class="notification-description">$8,500 withdrawal awaiting approval</div>
                    <div class="notification-time">15 minutes ago</div>
                </div>
            </div>
            <div class="notification-item unread">
                <div class="notification-icon info">🔧</div>
                <div class="notification-content">
                    <div class="notification-title">Dispute Escalated</div>
                    <div class="notification-description">Project dispute requires immediate attention</div>
                    <div class="notification-time">1 hour ago</div>
                </div>
            </div>
            <div class="notification-item">
                <div class="notification-icon success">✅</div>
                <div class="notification-content">
                    <div class="notification-title">System Update Complete</div>
                    <div class="notification-description">Platform upgraded to v2.4.1</div>
                    <div class="notification-time">3 hours ago</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Container -->
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="dashboard-welcome">
            <div class="welcome-content">
                <h1 class="welcome-title">Welcome back, Super Admin</h1>
                <p class="welcome-subtitle">Here's what's happening with your platform today</p>
            </div>
            <div class="welcome-meta">
                <div class="current-date">
                    <span class="date-icon">📅</span>
                    <span id="currentDate">Friday, May 29, 2026</span>
                </div>
            </div>
        </div>

        <!-- KPI Statistics Grid -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-icon">👥</span>
                    <span class="kpi-trend trend-up">
                        <span class="trend-arrow">↑</span>
                        <span class="trend-value">12%</span>
                    </span>
                </div>
                <div class="kpi-value" id="totalUsers"><?= number_format($totalUsers) ?></div>
                <div class="kpi-label">Total Users</div>
                <!-- <div class="kpi-comparison">+15 this week</div> -->
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-icon">🎓</span>
                    <span class="kpi-trend trend-up">
                        <span class="trend-arrow">↑</span>
                        <span class="trend-value">8%</span>
                    </span>
                </div>
                <div class="kpi-value" id="activeStudents"><?= number_format($activeStudents) ?></div>
                <div class="kpi-label">Active Students</div>
                <!-- <div class="kpi-comparison">+20 this week</div> -->
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-icon">🏢</span>
                    <span class="kpi-trend trend-up">
                        <span class="trend-arrow">↑</span>
                        <span class="trend-value">15%</span>
                    </span>
                </div>
                <div class="kpi-value" id="activeEmployers"><?= number_format($activeEmployers) ?></div>
                <div class="kpi-label">Active Employers</div>
                <!-- <div class="kpi-comparison">+1,743 this week</div> -->
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-icon">💼</span>
                    <span class="kpi-trend trend-up">
                        <span class="trend-arrow">↑</span>
                        <span class="trend-value">6%</span>
                    </span>
                </div>
                <div class="kpi-value" id="openJobs"><?= number_format($openJobs) ?></div>
                <div class="kpi-label">Open Jobs</div>
                <!-- <div class="kpi-comparison">+156 this week</div> -->
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-icon">⚙️</span>
                    <span class="kpi-trend trend-up">
                        <span class="trend-arrow">↑</span>
                        <span class="trend-value">4%</span>
                    </span>
                </div>
                <div class="kpi-value" id="jobsInProgress"> <?= number_format($jobsInProgress) ?></div>
                <div class="kpi-label">Jobs In Progress</div>
                <!-- <div class="kpi-comparison">+71 this week</div> -->
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-icon">✅</span>
                    <span class="kpi-trend trend-up">
                        <span class="trend-arrow">↑</span>
                        <span class="trend-value">18%</span>
                    </span>
                </div>
                <div class="kpi-value" id="completedJobs"><?= number_format($completedJobs) ?></div>
                <div class="kpi-label">Completed Jobs</div>
                <!-- <div class="kpi-comparison">+2,834 this week</div> -->
            </div>

            <div class="kpi-card highlight">
                <div class="kpi-header">
                    <span class="kpi-icon">💰</span>
                    <span class="kpi-trend trend-up">
                        <span class="trend-arrow">↑</span>
                        <span class="trend-value">22%</span>
                    </span>
                </div>
                <div class="kpi-val" id="totalEscrow">₦<?= number_format($totalEscrow, 2) ?></div>
                <div class="kpi-label">Total Escrow Balance</div>
                <!-- <div class="kpi-comparison">+$512,340 this week</div> -->
            </div>

            <div class="kpi-card highlight">
                <div class="kpi-header">
                    <span class="kpi-icon">📈</span>
                    <span class="kpi-trend trend-up">
                        <span class="trend-arrow">↑</span>
                        <span class="trend-value">25%</span>
                    </span>
                </div>
                <div class="kpi-val" id="platformRevenue"> ₦<?= number_format($platformRevenue,2) ?></div>
                <div class="kpi-label">Platform Revenue</div>
                <!-- <div class="kpi-comparison">+$85,234 this week</div> -->
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions-section">
            <h2 class="section-title">Quick Actions</h2>
            <div class="quick-actions-grid">
                <button class="quick-action-btn" onclick="window.location.href='withdrawal.php'">
                    <span class="action-icon">💸</span>
                    <span class="action-label">Review Withdrawals</span>
                    <span class="action-badge"> <?= $pendingWithdrawals ?></span>
                </button>
                <button class="quick-action-btn" onclick="window.location.href='dispute.php'">
                    <span class="action-icon">⚖️</span>
                    <span class="action-label">Open Disputes</span>
                    <span class="action-badge"><?= $openDisputes ?></span>
                </button>
                <button class="quick-action-btn" onclick="window.location.href='fraud_flags.php'">
                    <span class="action-icon">🛡️</span>
                    <span class="action-label">View Fraud Flags</span>
                    <!-- <span class="action-badge">3</span> -->
                </button>
                <button class="quick-action-btn" onclick="window.location.href='create-admin.php'">
                    <span class="action-icon">👤</span>
                    <span class="action-label">Create Admin</span>
                </button>
                <button class="quick-action-btn" onclick="window.location.href='audit-logs.php'">
                    <span class="action-icon">📋</span>
                    <span class="action-label">Audit Logs</span>
                </button>
            </div>
        </div>

        <!-- Dashboard Widgets Grid -->
        <div class="widgets-grid">
            <!-- Recent Withdrawals Widget -->
            <div class="widget-card">
                <div class="widget-header">
                    <h3 class="widget-title">Recent Withdrawals</h3>
                    <a href="withdrawals.html" class="widget-link">View All →</a>
                </div>
                <div class="widget-content">
                    <table class="widget-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentWithdrawals as $withdrawal): ?>
                                <tr>
                                    <td><?= htmlspecialchars($withdrawal['full_name']) ?></td>
                                    <td>₦<?= number_format($withdrawal['amount'], 2) ?></td>
                                    <td><?= ucfirst($withdrawal['status']) ?></td>
                                    <td><?= date('d M Y', strtotime($withdrawal['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Disputes Widget -->
            <div class="widget-card">
                <div class="widget-header">
                    <h3 class="widget-title">Recent Disputes</h3>
                    <a href="disputes.html" class="widget-link">View All →</a>
                </div>
                <div class="widget-content">
                    <table class="widget-table">
                        <thead>
                            <tr>
                                <th>Job</th>
                                <th>Opened By</th>
                                <th>Status</th>
                                <th>Escrow</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentDisputes as $dispute): ?>
                                <tr>
                                    <td><?= htmlspecialchars($dispute['title']) ?></td>
                                    <td><?= htmlspecialchars($dispute['full_name']) ?></td>
                                    <td><?= ucfirst($dispute['status']) ?></td>
                                    <td>₦<?= number_format($dispute['amount'] ?? 0, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            

            <!-- Recent Admin Activity Widget -->
            <div class="widget-card">
                <div class="widget-header">
                    <h3 class="widget-title">Recent Admin Activity</h3>
                    <a href="audit-logs.html" class="widget-link">View All →</a>
                </div>
                <div class="widget-content">
                    <table class="widget-table">
                        <thead>
                            <tr>
                                <th>Admin</th>
                                <th>Action</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentActivity as $activity): ?>
                                <tr>
                                    <td><?= htmlspecialchars($activity['full_name'] ?? 'System') ?></td>
                                    <td><?= htmlspecialchars($activity['action']) ?></td>
                                    <td><?= date('d M Y H:i', strtotime($activity['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Platform Health Section -->
        <div class="platform-health-section">
            <h2 class="section-title">Platform Health</h2>
            <div class="health-metrics-grid">
                <div class="health-metric">
                    <div class="health-label">Escrow Health</div>
                    <div class="health-progress">
                        <div class="health-bar" style="width: 98%;" data-color="success"></div>
                    </div>
                    <div class="health-value">98% Healthy</div>
                </div>
                <div class="health-metric">
                    <div class="health-label">Payment Success Rate</div>
                    <div class="health-progress">
                        <div class="health-bar" style="width: 96%;" data-color="success"></div>
                    </div>
                    <div class="health-value">96% Success</div>
                </div>
                <div class="health-metric">
                    <div class="health-label">Dispute Resolution Rate</div>
                    <div class="health-progress">
                        <div class="health-bar" style="width: 89%;" data-color="warning"></div>
                    </div>
                    <div class="health-value">89% Resolved</div>
                </div>
                <div class="health-metric">
                    <div class="health-label">Fraud Detection Accuracy</div>
                    <div class="health-progress">
                        <div class="health-bar" style="width: 94%;" data-color="success"></div>
                    </div>
                    <div class="health-value">94% Accurate</div>
                </div>
                <div class="health-metric">
                    <div class="health-label">User Satisfaction Score</div>
                    <div class="health-progress">
                        <div class="health-bar" style="width: 92%;" data-color="success"></div>
                    </div>
                    <div class="health-value">4.6/5.0 Rating</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <h2 class="section-title">Platform Analytics</h2>
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">User Growth</h3>
                        <select class="chart-filter">
                            <option>Last 7 Days</option>
                            <option>Last 30 Days</option>
                            <option>Last 90 Days</option>
                        </select>
                    </div>
                    <div class="chart-container" id="userGrowthChart"></div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Revenue Trend</h3>
                        <select class="chart-filter">
                            <option>Last 7 Days</option>
                            <option>Last 30 Days</option>
                            <option>Last 90 Days</option>
                        </select>
                    </div>
                    <div class="chart-container" id="revenueTrendChart"></div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Jobs Posted</h3>
                        <select class="chart-filter">
                            <option>Last 7 Days</option>
                            <option>Last 30 Days</option>
                            <option>Last 90 Days</option>
                        </select>
                    </div>
                    <div class="chart-container" id="jobsPostedChart"></div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Escrow Volume</h3>
                        <select class="chart-filter">
                            <option>Last 7 Days</option>
                            <option>Last 30 Days</option>
                            <option>Last 90 Days</option>
                        </select>
                    </div>
                    <div class="chart-container" id="escrowVolumeChart"></div>
                </div>
            </div>
        </div>

        <!-- Live Activity Feed -->
        <div class="live-activity-section">
            <h2 class="section-title">
                Live Activity Feed
                <span class="live-indicator">
                    <span class="live-dot"></span>
                    LIVE
                </span>
            </h2>
            <div class="activity-feed" id="activityFeed"></div>
        </div>
    </div>

    <script src="admin-dashboard.js"></script>
</body>

</html>