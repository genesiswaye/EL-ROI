<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <!-- Dashboard Page -->
    <div class="page" id="page-dashboard">
        <div class="page-header">
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Welcome back, Sarah. Here's what's happening today.</p>
        </div>

        <!-- Analytics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <span class="stat-trend stat-trend-up">+12.5%</span>
                </div>
                <div class="stat-value">12,345</div>
                <div class="stat-label">Total Users</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-green">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <span class="stat-trend stat-trend-up">+8.2%</span>
                </div>
                <div class="stat-value">$2.4M</div>
                <div class="stat-label">Total Revenue</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-orange">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                    </div>
                    <span class="stat-trend stat-trend-down">-3.1%</span>
                </div>
                <div class="stat-value">142</div>
                <div class="stat-label">Pending Withdrawals</div>
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
                    <span class="stat-trend stat-trend-up">+2</span>
                </div>
                <div class="stat-value">23</div>
                <div class="stat-label">Active Disputes</div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="content-grid">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Recent Transactions</h2>
                    <button class="btn btn-text">View All</button>
                </div>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>User</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-mono">#TXN-5847</td>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=John" alt="John" class="user-avatar">
                                        <span>John Doe</span>
                                    </div>
                                </td>
                                <td>Withdrawal</td>
                                <td class="font-semibold">$1,250.00</td>
                                <td><span class="badge badge-success">Completed</span></td>
                                <td class="text-muted">2 hours ago</td>
                            </tr>
                            <tr>
                                <td class="font-mono">#TXN-5846</td>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah" alt="Sarah" class="user-avatar">
                                        <span>Sarah Smith</span>
                                    </div>
                                </td>
                                <td>Payment</td>
                                <td class="font-semibold">$850.00</td>
                                <td><span class="badge badge-warning">Pending</span></td>
                                <td class="text-muted">3 hours ago</td>
                            </tr>
                            <tr>
                                <td class="font-mono">#TXN-5845</td>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Mike" alt="Mike" class="user-avatar">
                                        <span>Mike Johnson</span>
                                    </div>
                                </td>
                                <td>Refund</td>
                                <td class="font-semibold">$450.00</td>
                                <td><span class="badge badge-info">Processing</span></td>
                                <td class="text-muted">5 hours ago</td>
                            </tr>
                            <tr>
                                <td class="font-mono">#TXN-5844</td>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Emma" alt="Emma" class="user-avatar">
                                        <span>Emma Wilson</span>
                                    </div>
                                </td>
                                <td>Withdrawal</td>
                                <td class="font-semibold">$2,100.00</td>
                                <td><span class="badge badge-error">Failed</span></td>
                                <td class="text-muted">1 day ago</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">System Alerts</h2>
                </div>
                <div class="alerts-list">
                    <div class="alert alert-error">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div>
                            <div class="alert-title">Fraud Alert</div>
                            <div class="alert-desc">3 suspicious transactions detected in the last hour</div>
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        <div>
                            <div class="alert-title">High Volume</div>
                            <div class="alert-desc">Withdrawal requests are 45% above normal</div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <div>
                            <div class="alert-title">System Update</div>
                            <div class="alert-desc">Scheduled maintenance in 48 hours</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>