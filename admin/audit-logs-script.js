// Audit Logs Data
const auditLogsData = [
    {
        id: 'LOG-001',
        action: 'login',
        actionName: 'User Login',
        description: 'Successful login from new device',
        user: { name: 'Sarah Johnson', initials: 'SJ', type: 'admin' },
        targetType: 'Session',
        targetId: 'sess_8h2k9j3',
        ipAddress: '192.168.1.105',
        timestamp: new Date(Date.now() - 15 * 60 * 1000),
        severity: 'low',
        details: {
            userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            location: 'New York, USA',
            deviceType: 'Desktop',
            browser: 'Chrome 124'
        }
    },
    {
        id: 'LOG-002',
        action: 'fraud',
        actionName: 'Fraud Flag',
        description: 'Multiple failed login attempts detected',
        user: { name: 'System', initials: 'SYS', type: 'system' },
        targetType: 'User',
        targetId: 'user_9k3j2h',
        ipAddress: '45.142.87.23',
        timestamp: new Date(Date.now() - 25 * 60 * 1000),
        severity: 'critical',
        details: {
            attempts: '7',
            timeWindow: '5 minutes',
            accountStatus: 'Locked',
            riskScore: '95/100'
        }
    },
    {
        id: 'LOG-003',
        action: 'withdrawal',
        actionName: 'Withdrawal Processed',
        description: 'Student withdrawal approved and processed',
        user: { name: 'Emma Davis', initials: 'ED', type: 'admin' },
        targetType: 'Transaction',
        targetId: 'txn_7h3k2j9',
        ipAddress: '192.168.1.142',
        timestamp: new Date(Date.now() - 45 * 60 * 1000),
        severity: 'medium',
        details: {
            amount: '$2,450.00',
            studentName: 'Michael Chen',
            paymentMethod: 'Bank Transfer',
            processingTime: '2.4 seconds'
        }
    },
    {
        id: 'LOG-004',
        action: 'escrow',
        actionName: 'Escrow Released',
        description: 'Funds released from escrow to student',
        user: { name: 'Sarah Johnson', initials: 'SJ', type: 'admin' },
        targetType: 'Escrow',
        targetId: 'esc_5j2h8k',
        ipAddress: '192.168.1.105',
        timestamp: new Date(Date.now() - 1 * 60 * 60 * 1000),
        severity: 'high',
        details: {
            amount: '$3,200.00',
            projectId: 'PRJ-8472',
            releaseReason: 'Project completed',
            studentId: 'user_3k2j9h'
        }
    },
    {
        id: 'LOG-005',
        action: 'dispute',
        actionName: 'Dispute Opened',
        description: 'New dispute filed by student',
        user: { name: 'Alex Thompson', initials: 'AT', type: 'student' },
        targetType: 'Dispute',
        targetId: 'dsp_2k9j3h',
        ipAddress: '203.45.67.89',
        timestamp: new Date(Date.now() - 2 * 60 * 60 * 1000),
        severity: 'high',
        details: {
            disputeType: 'Payment Issue',
            escrowAmount: '$1,800.00',
            projectId: 'PRJ-8471',
            status: 'Open'
        }
    },
    {
        id: 'LOG-006',
        action: 'admin',
        actionName: 'Admin Permission Changed',
        description: 'Admin role updated for user',
        user: { name: 'Sarah Johnson', initials: 'SJ', type: 'admin' },
        targetType: 'Admin',
        targetId: 'adm_8k2j9h',
        ipAddress: '192.168.1.105',
        timestamp: new Date(Date.now() - 3 * 60 * 60 * 1000),
        severity: 'critical',
        details: {
            targetAdmin: 'Michael Chen',
            previousRole: 'Support Admin',
            newRole: 'Finance Admin',
            approvedBy: 'Sarah Johnson'
        }
    },
    {
        id: 'LOG-007',
        action: 'login',
        actionName: 'Failed Login',
        description: 'Invalid credentials provided',
        user: { name: 'Unknown', initials: '??', type: 'student' },
        targetType: 'Session',
        targetId: 'N/A',
        ipAddress: '78.129.45.67',
        timestamp: new Date(Date.now() - 4 * 60 * 60 * 1000),
        severity: 'medium',
        details: {
            attemptNumber: '3',
            username: 'jdoe@example.com',
            failureReason: 'Invalid password',
            accountStatus: 'Active'
        }
    },
    {
        id: 'LOG-008',
        action: 'withdrawal',
        actionName: 'Withdrawal Rejected',
        description: 'Insufficient verification documents',
        user: { name: 'Emma Davis', initials: 'ED', type: 'admin' },
        targetType: 'Transaction',
        targetId: 'txn_9k3j2h',
        ipAddress: '192.168.1.142',
        timestamp: new Date(Date.now() - 5 * 60 * 60 * 1000),
        severity: 'low',
        details: {
            amount: '$850.00',
            studentName: 'Sophia Taylor',
            rejectionReason: 'Missing ID verification',
            requiredAction: 'Upload government ID'
        }
    },
    {
        id: 'LOG-009',
        action: 'escrow',
        actionName: 'Escrow Funded',
        description: 'Client funded project escrow',
        user: { name: 'Tech Corp Inc', initials: 'TC', type: 'company' },
        targetType: 'Escrow',
        targetId: 'esc_7h2k9j',
        ipAddress: '156.78.90.12',
        timestamp: new Date(Date.now() - 6 * 60 * 60 * 1000),
        severity: 'medium',
        details: {
            amount: '$4,500.00',
            projectId: 'PRJ-8475',
            paymentMethod: 'Credit Card',
            escrowDuration: '30 days'
        }
    },
    {
        id: 'LOG-010',
        action: 'fraud',
        actionName: 'Suspicious Activity',
        description: 'Unusual withdrawal pattern detected',
        user: { name: 'System', initials: 'SYS', type: 'system' },
        targetType: 'User',
        targetId: 'user_2k9j3h',
        ipAddress: '45.87.123.45',
        timestamp: new Date(Date.now() - 7 * 60 * 60 * 1000),
        severity: 'critical',
        details: {
            pattern: '5 withdrawals in 10 minutes',
            totalAmount: '$12,500.00',
            riskScore: '88/100',
            actionTaken: 'Account flagged for review'
        }
    },
    {
        id: 'LOG-011',
        action: 'admin',
        actionName: 'Security Settings Changed',
        description: 'Two-factor authentication enabled',
        user: { name: 'Michael Chen', initials: 'MC', type: 'admin' },
        targetType: 'Settings',
        targetId: 'set_3k2j9h',
        ipAddress: '192.168.1.118',
        timestamp: new Date(Date.now() - 8 * 60 * 60 * 1000),
        severity: 'low',
        details: {
            settingChanged: '2FA Authentication',
            previousValue: 'Disabled',
            newValue: 'Enabled',
            method: 'Authenticator App'
        }
    },
    {
        id: 'LOG-012',
        action: 'dispute',
        actionName: 'Dispute Resolved',
        description: 'Dispute closed in favor of student',
        user: { name: 'Sarah Johnson', initials: 'SJ', type: 'admin' },
        targetType: 'Dispute',
        targetId: 'dsp_8k2j9h',
        ipAddress: '192.168.1.105',
        timestamp: new Date(Date.now() - 10 * 60 * 60 * 1000),
        severity: 'medium',
        details: {
            resolution: 'Payment released to student',
            amount: '$2,100.00',
            resolutionTime: '4.2 hours',
            winner: 'Student'
        }
    }
];

// Get avatar color
function getAvatarColor(name) {
    const colors = [
        'linear-gradient(135deg, #3B82F6, #2563EB)',
        'linear-gradient(135deg, #10B981, #059669)',
        'linear-gradient(135deg, #F59E0B, #D97706)',
        'linear-gradient(135deg, #8B5CF6, #7C3AED)',
        'linear-gradient(135deg, #EC4899, #DB2777)',
        'linear-gradient(135deg, #EF4444, #DC2626)',
        'linear-gradient(135deg, #14B8A6, #0D9488)',
        'linear-gradient(135deg, #6366F1, #4F46E5)'
    ];
    const index = name.charCodeAt(0) % colors.length;
    return colors[index];
}

// Get action icon HTML
function getActionIcon(action) {
    const icons = {
        login: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
            <polyline points="10 17 15 12 10 7"></polyline>
            <line x1="15" y1="12" x2="3" y2="12"></line>
        </svg>`,
        withdrawal: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="1" x2="12" y2="23"></line>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
        </svg>`,
        escrow: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>`,
        dispute: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>`,
        admin: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
        </svg>`,
        fraud: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>`
    };
    return icons[action] || icons.admin;
}

// Format timestamp
function formatTimestamp(date) {
    const now = new Date();
    const diff = now - date;
    const hours = Math.floor(diff / (1000 * 60 * 60));

    if (hours < 1) {
        const mins = Math.floor(diff / (1000 * 60));
        return `${mins}m ago`;
    } else if (hours < 24) {
        return `${hours}h ago`;
    } else {
        const days = Math.floor(hours / 24);
        if (days === 1) return '1 day ago';
        return `${days} days ago`;
    }
}

// Format full timestamp
function formatFullTimestamp(date) {
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}

// Render logs table
function renderLogsTable() {
    const tableBody = document.getElementById('logs-table-body');
    const searchTerm = document.getElementById('log-search').value.toLowerCase();
    const actionFilter = document.getElementById('action-filter').value;
    const severityFilter = document.getElementById('severity-filter').value;
    const userFilter = document.getElementById('user-filter').value;
    const dateFilter = document.getElementById('date-filter').value;

    const filteredLogs = auditLogsData.filter(log => {
        const matchesSearch = log.actionName.toLowerCase().includes(searchTerm) ||
                            log.description.toLowerCase().includes(searchTerm) ||
                            log.user.name.toLowerCase().includes(searchTerm) ||
                            log.targetId.toLowerCase().includes(searchTerm);
        const matchesAction = !actionFilter || log.action === actionFilter;
        const matchesSeverity = !severityFilter || log.severity === severityFilter;
        const matchesUser = !userFilter || log.user.type === userFilter;
        
        let matchesDate = true;
        if (dateFilter) {
            const logDate = new Date(log.timestamp).toISOString().split('T')[0];
            matchesDate = logDate === dateFilter;
        }

        return matchesSearch && matchesAction && matchesSeverity && matchesUser && matchesDate;
    });

    let html = '';
    filteredLogs.forEach((log, index) => {
        html += `
            <tr data-log-id="${log.id}">
                <td>
                    <button class="expand-btn" onclick="toggleExpand('${log.id}', this)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>
                </td>
                <td>
                    <div class="action-cell">
                        <div class="action-icon action-icon-${log.action}">
                            ${getActionIcon(log.action)}
                        </div>
                        <div class="action-info">
                            <div class="action-name">${log.actionName}</div>
                            <span class="severity-badge severity-${log.severity}">${log.severity}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="log-description" title="${log.description}">${log.description}</div>
                </td>
                <td>
                    <div class="user-cell">
                        <div class="user-avatar" style="background: ${getAvatarColor(log.user.name)}">
                            ${log.user.initials}
                        </div>
                        <span class="user-name">${log.user.name}</span>
                    </div>
                </td>
                <td><span class="target-type">${log.targetType}</span></td>
                <td><span class="target-cell">${log.targetId}</span></td>
                <td><span class="ip-address">${log.ipAddress}</span></td>
                <td><span class="timestamp">${formatTimestamp(log.timestamp)}</span></td>
            </tr>
            <tr class="expandable-row" id="expand-${log.id}">
                <td colspan="8">
                    <div class="expandable-content">
                        <div class="details-grid">
                            <div class="detail-item">
                                <div class="detail-label">Log ID</div>
                                <div class="detail-value">${log.id}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Full Timestamp</div>
                                <div class="detail-value">${formatFullTimestamp(log.timestamp)}</div>
                            </div>
                            ${Object.entries(log.details).map(([key, value]) => `
                                <div class="detail-item">
                                    <div class="detail-label">${key.replace(/([A-Z])/g, ' $1').trim()}</div>
                                    <div class="detail-value">${value}</div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </td>
            </tr>
        `;
    });

    tableBody.innerHTML = html;
    updateStats();
}

// Toggle expandable row
function toggleExpand(logId, button) {
    const expandableRow = document.getElementById(`expand-${logId}`);
    const parentRow = button.closest('tr');
    
    if (expandableRow.classList.contains('show')) {
        expandableRow.classList.remove('show');
        button.classList.remove('expanded');
        parentRow.classList.remove('expanded');
    } else {
        expandableRow.classList.add('show');
        button.classList.add('expanded');
        parentRow.classList.add('expanded');
    }
}

// Update statistics
function updateStats() {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    const todayEvents = auditLogsData.filter(log => {
        const logDate = new Date(log.timestamp);
        logDate.setHours(0, 0, 0, 0);
        return logDate.getTime() === today.getTime();
    }).length;
    
    const criticalEvents = auditLogsData.filter(log => log.severity === 'critical').length;
    const adminActions = auditLogsData.filter(log => log.action === 'admin').length;
    const fraudLogs = auditLogsData.filter(log => log.action === 'fraud').length;

    document.getElementById('today-events').textContent = todayEvents;
    document.getElementById('critical-events').textContent = criticalEvents;
    document.getElementById('admin-actions').textContent = adminActions;
    document.getElementById('fraud-logs').textContent = fraudLogs;
}

// Filter logs
function filterLogs() {
    renderLogsTable();
}

// Export to CSV
function exportToCSV() {
    const headers = ['Log ID', 'Action', 'Description', 'User', 'User Type', 'Target Type', 'Target ID', 'IP Address', 'Timestamp', 'Severity'];
    
    const csvContent = [
        headers.join(','),
        ...auditLogsData.map(log => [
            log.id,
            `"${log.actionName}"`,
            `"${log.description}"`,
            `"${log.user.name}"`,
            log.user.type,
            log.targetType,
            log.targetId,
            log.ipAddress,
            formatFullTimestamp(log.timestamp),
            log.severity
        ].join(','))
    ].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', `audit-logs-${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    renderLogsTable();
    
    // Set today's date as default for date filter
    const dateFilter = document.getElementById('date-filter');
    const today = new Date().toISOString().split('T')[0];
    dateFilter.max = today;
});
