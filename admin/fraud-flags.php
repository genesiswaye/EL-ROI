<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fraud Flags - StudentLancer Admin</title>
    <link rel="stylesheet" href="fraud-flags-styles.css">
</head>
<body>
    
    <!-- Critical Alert Banner -->
    <div id="critical-banner" class="critical-banner" style="display: none;">
        <div class="critical-banner-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>
        <div class="critical-banner-content">
            <div class="critical-banner-title">Critical Fraud Alert</div>
            <div class="critical-banner-message" id="critical-message"></div>
        </div>
        <button class="critical-banner-close" onclick="closeCriticalBanner()">&times;</button>
    </div>

    <!-- Investigation Side Panel -->
    <div id="investigation-panel" class="investigation-panel">
        <div class="panel-content">
            <div class="panel-header">
                <h3 class="panel-title">Fraud Investigation</h3>
                <button class="panel-close" onclick="closeInvestigationPanel()">&times;</button>
            </div>
            <div id="investigation-body" class="panel-body">
                <!-- Investigation details will be populated here -->
            </div>
        </div>
    </div>

    <!-- Action Modal -->
    <div id="action-modal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-title">Confirm Action</h3>
                <button class="modal-close" onclick="closeActionModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p id="modal-message">Are you sure you want to proceed?</p>
                <div id="modal-details"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeActionModal()">Cancel</button>
                <button class="btn btn-primary" id="modal-confirm-btn" onclick="confirmModalAction()">Confirm</button>
            </div>
        </div>
    </div>

    <div class="page-container">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Fraud Monitoring</h1>
                <p class="page-subtitle">AI-Powered Fraud Detection & Security Operations Center</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-red">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                    </div>
                    <span class="stat-trend stat-trend-down">Active</span>
                </div>
                <div class="stat-value" id="active-cases">18</div>
                <div class="stat-label">Active Fraud Cases</div>
            </div>

            <div class="stat-card stat-card-critical">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-critical">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <span class="stat-trend stat-trend-critical">Urgent</span>
                </div>
                <div class="stat-value" id="critical-alerts">5</div>
                <div class="stat-label">Critical Alerts</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon stat-icon-orange">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                        </svg>
                    </div>
                </div>
                <div class="stat-value" id="suspended-accounts">12</div>
                <div class="stat-label">Suspended Accounts</div>
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
                <div class="stat-value" id="resolved-today">11</div>
                <div class="stat-label">Resolved Today</div>
            </div>
        </div>

        <div class="content-grid">
            <!-- Main Content -->
            <div class="main-column">
                <!-- Fraud Flags Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Fraud Detection Cases</h3>
                        <div class="filters-container">
                            <div class="search-input">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.35-4.35"></path>
                                </svg>
                                <input type="text" id="fraud-search" placeholder="Search cases..." onkeyup="filterFraudCases()">
                            </div>
                            
                            <select class="filter-select" id="severity-filter" onchange="filterFraudCases()">
                                <option value="">All Severity</option>
                                <option value="critical">Critical</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>

                            <select class="filter-select" id="status-filter" onchange="filterFraudCases()">
                                <option value="">All Status</option>
                                <option value="open">Open</option>
                                <option value="investigating">Investigating</option>
                                <option value="escalated">Escalated</option>
                                <option value="resolved">Resolved</option>
                                <option value="false-positive">False Positive</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Flag Type</th>
                                    <th>Description</th>
                                    <th>Severity</th>
                                    <th>Detection</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="fraud-table-body">
                                <!-- Fraud cases will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Live Activity Feed -->
            <div class="side-column">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Live Activity Feed</h3>
                        <span class="live-indicator">
                            <span class="live-dot"></span>
                            Live
                        </span>
                    </div>
                    <div class="activity-feed" id="activity-feed">
                        <!-- Activity items will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="fraud-flags-script.js"></script>
</body>
</html>
