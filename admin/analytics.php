<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <?php
    $activePage = "analytics";
    include "admins2.php";
    ?>
    <div class="dashboard-container">
        <!-- Analytics Page -->
        <div class="page" id="page-analytics">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Analytics</h1>
                    <p class="page-subtitle">Platform performance and insights</p>
                </div>
                <select class="filter-select">
                    <option>Last 7 Days</option>
                    <option>Last 30 Days</option>
                    <option>Last 90 Days</option>
                    <option>Last Year</option>
                </select>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Revenue Growth</div>
                    <div class="stat-value">+24.5%</div>
                    <div class="mini-chart">
                        <svg width="100%" height="40" viewBox="0 0 100 40">
                            <polyline points="0,30 20,25 40,20 60,15 80,10 100,5" fill="none" stroke="#10B981" stroke-width="2" />
                        </svg>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Active Projects</div>
                    <div class="stat-value">1,284</div>
                    <div class="mini-chart">
                        <svg width="100%" height="40" viewBox="0 0 100 40">
                            <polyline points="0,20 20,18 40,22 60,15 80,12 100,8" fill="none" stroke="#2563EB" stroke-width="2" />
                        </svg>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Completion Rate</div>
                    <div class="stat-value">94.2%</div>
                    <div class="mini-chart">
                        <svg width="100%" height="40" viewBox="0 0 100 40">
                            <polyline points="0,15 20,14 40,13 60,14 80,12 100,11" fill="none" stroke="#F59E0B" stroke-width="2" />
                        </svg>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">User Satisfaction</div>
                    <div class="stat-value">4.8/5</div>
                    <div class="mini-chart">
                        <svg width="100%" height="40" viewBox="0 0 100 40">
                            <polyline points="0,25 20,23 40,20 60,18 80,15 100,12" fill="none" stroke="#8B5CF6" stroke-width="2" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="content-grid">
                <div class="card chart-card">
                    <div class="card-header">
                        <h2 class="card-title">Revenue Overview</h2>
                    </div>
                    <div class="chart-placeholder">
                        <svg width="100%" height="300" viewBox="0 0 600 300">
                            <text x="300" y="150" text-anchor="middle" fill="#94A3B8" font-size="14">Chart visualization would go here</text>
                            <rect x="50" y="50" width="500" height="200" fill="none" stroke="#E2E8F0" stroke-width="1" />
                        </svg>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Top Performers</h2>
                    </div>
                    <div class="leaderboard">
                        <div class="leaderboard-item">
                            <div class="rank rank-1">1</div>
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Top1" alt="Top 1" class="user-avatar">
                            <div class="leaderboard-info">
                                <div class="leaderboard-name">Jessica Wang</div>
                                <div class="text-muted text-sm">Computer Science</div>
                            </div>
                            <div class="leaderboard-value">$12,450</div>
                        </div>
                        <div class="leaderboard-item">
                            <div class="rank rank-2">2</div>
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Top2" alt="Top 2" class="user-avatar">
                            <div class="leaderboard-info">
                                <div class="leaderboard-name">Marcus Johnson</div>
                                <div class="text-muted text-sm">Business Analytics</div>
                            </div>
                            <div class="leaderboard-value">$10,890</div>
                        </div>
                        <div class="leaderboard-item">
                            <div class="rank rank-3">3</div>
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Top3" alt="Top 3" class="user-avatar">
                            <div class="leaderboard-info">
                                <div class="leaderboard-name">Sofia Rodriguez</div>
                                <div class="text-muted text-sm">Graphic Design</div>
                            </div>
                            <div class="leaderboard-value">$9,720</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="admin.js"></script>
</body>

</html>