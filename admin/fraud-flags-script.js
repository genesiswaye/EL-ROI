// ==========================================
// FRAUD FLAGS DASHBOARD - JAVASCRIPT
// StudentLancer Admin Panel
// ==========================================

// Sample Fraud Flag Data
const fraudFlags = [
  {
    id: 'FF-2024-001',
    user: {
      name: 'Marcus Johnson',
      email: 'marcus.j@university.edu',
      id: 'USR-78492',
      avatar: 'MJ'
    },
    flagType: '🔴 Account Takeover',
    description: 'Multiple failed login attempts from different geolocations within 10 minutes',
    severity: 'critical',
    detectionMethod: 'AI Behavioral Analysis',
    date: '2026-05-29 14:32',
    status: 'investigating',
    fraudScore: 94,
    details: {
      accountAge: '245 days',
      totalTransactions: 156,
      suspiciousActivities: 8,
      lastLogin: '2026-05-29 14:30 (Nigeria)',
      normalLocation: 'United States',
      recentIPs: ['197.210.76.45 (NG)', '41.203.124.89 (NG)', '102.88.34.12 (NG)'],
      devices: ['Unknown Android Device', 'Windows 11 Desktop'],
      walletBalance: '$2,847.50'
    },
    timeline: [
      { time: '14:32', event: 'Fraud flag triggered by AI system', meta: 'Automated Detection' },
      { time: '14:30', event: 'Login attempt from Lagos, Nigeria', meta: 'IP: 197.210.76.45' },
      { time: '14:28', event: 'Failed login attempt from Abuja, Nigeria', meta: 'IP: 41.203.124.89' },
      { time: '14:25', event: 'Failed login attempt from Port Harcourt, Nigeria', meta: 'IP: 102.88.34.12' },
      { time: '14:20', event: 'Normal activity from Los Angeles, USA', meta: 'IP: 45.23.67.89' }
    ]
  },
  {
    id: 'FF-2024-002',
    user: {
      name: 'Sarah Chen',
      email: 'sarah.chen@tech.edu',
      id: 'USR-45821',
      avatar: 'SC'
    },
    flagType: '💳 Payment Fraud',
    description: 'Chargeback dispute initiated by card issuer for $1,200 graphic design project',
    severity: 'high',
    detectionMethod: 'Bank Alert Integration',
    date: '2026-05-29 11:15',
    status: 'suspended',
    fraudScore: 87,
    details: {
      accountAge: '89 days',
      totalTransactions: 23,
      suspiciousActivities: 4,
      lastLogin: '2026-05-28 09:45 (United States)',
      normalLocation: 'United States',
      recentIPs: ['192.168.1.45 (US)', '192.168.1.45 (US)'],
      devices: ['MacBook Pro (Safari)', 'iPhone 15 Pro'],
      walletBalance: '$0.00'
    },
    timeline: [
      { time: '11:15', event: 'Account suspended pending investigation', meta: 'Automated Action' },
      { time: '11:10', event: 'Chargeback notification received from Stripe', meta: 'Amount: $1,200' },
      { time: '11:05', event: 'User attempted to withdraw remaining balance', meta: 'Amount: $850' },
      { time: 'May 20', event: 'Payment received for project completion', meta: 'Project #PRJ-9823' }
    ]
  },
  {
    id: 'FF-2024-003',
    user: {
      name: 'David Martinez',
      email: 'david.m@college.edu',
      id: 'USR-92341',
      avatar: 'DM'
    },
    flagType: '🤖 Bot Activity',
    description: 'Unusual bid pattern detected - 47 bids placed in 2 minutes using automated script',
    severity: 'high',
    detectionMethod: 'Rate Limiting Trigger',
    date: '2026-05-29 08:42',
    status: 'monitoring',
    fraudScore: 76,
    details: {
      accountAge: '12 days',
      totalTransactions: 3,
      suspiciousActivities: 12,
      lastLogin: '2026-05-29 08:40 (United States)',
      normalLocation: 'United States',
      recentIPs: ['45.67.123.89 (US)', '45.67.123.89 (US)'],
      devices: ['Chrome (Windows 11)'],
      walletBalance: '$125.00'
    },
    timeline: [
      { time: '08:42', event: 'Rate limiting applied to account', meta: 'Max 5 bids per 10 minutes' },
      { time: '08:40', event: '47 rapid bids detected across multiple projects', meta: 'Avg 2.5 seconds per bid' },
      { time: '08:38', event: 'API requests spiked to 320 requests per minute', meta: 'Normal: 15 req/min' },
      { time: 'May 27', event: 'Account created', meta: 'Email verified' }
    ]
  },
  {
    id: 'FF-2024-004',
    user: {
      name: 'Emily Rodriguez',
      email: 'emily.r@university.edu',
      id: 'USR-23847',
      avatar: 'ER'
    },
    flagType: '🔗 Linked Accounts',
    description: 'Multiple accounts detected sharing same bank account and IP address',
    severity: 'medium',
    detectionMethod: 'Pattern Recognition AI',
    date: '2026-05-28 19:20',
    status: 'investigating',
    fraudScore: 68,
    details: {
      accountAge: '156 days',
      totalTransactions: 89,
      suspiciousActivities: 3,
      lastLogin: '2026-05-28 18:45 (United States)',
      normalLocation: 'United States',
      recentIPs: ['192.168.45.23 (US)', '192.168.45.23 (US)'],
      devices: ['MacBook Air (Chrome)', 'iPad Pro'],
      walletBalance: '$1,234.75'
    },
    timeline: [
      { time: '19:20', event: 'Investigation opened for linked accounts', meta: '3 accounts detected' },
      { time: '19:15', event: 'Same bank account used across 3 user profiles', meta: 'Bank: Chase ****4521' },
      { time: '19:10', event: 'Concurrent logins from same IP detected', meta: 'IP: 192.168.45.23' },
      { time: 'May 15', event: 'First transaction completed', meta: 'Web development project' }
    ]
  },
  {
    id: 'FF-2024-005',
    user: {
      name: 'Alex Thompson',
      email: 'alex.t@tech.edu',
      id: 'USR-67234',
      avatar: 'AT'
    },
    flagType: '⚠️ Suspicious Withdrawal',
    description: 'Withdrawal request of $8,500 immediately after receiving first payment',
    severity: 'high',
    detectionMethod: 'Transaction Pattern Analysis',
    date: '2026-05-28 15:30',
    status: 'investigating',
    fraudScore: 82,
    details: {
      accountAge: '7 days',
      totalTransactions: 1,
      suspiciousActivities: 2,
      lastLogin: '2026-05-28 15:25 (India)',
      normalLocation: 'India',
      recentIPs: ['103.234.56.78 (IN)', '103.234.56.78 (IN)'],
      devices: ['Chrome (Windows 10)'],
      walletBalance: '$8,500.00'
    },
    timeline: [
      { time: '15:30', event: 'Withdrawal request flagged for review', meta: 'Amount: $8,500' },
      { time: '15:28', event: 'Funds received from client escrow release', meta: 'Project #PRJ-4512' },
      { time: '15:20', event: 'Project marked as complete', meta: 'Mobile app development' },
      { time: 'May 21', event: 'Account created', meta: 'No profile picture or verification' }
    ]
  },
  {
    id: 'FF-2024-006',
    user: {
      name: 'Jessica Lee',
      email: 'jessica.lee@college.edu',
      id: 'USR-81923',
      avatar: 'JL'
    },
    flagType: '📧 Email Spoofing',
    description: 'Phishing emails sent to clients claiming to be StudentLancer support',
    severity: 'critical',
    detectionMethod: 'Email Security Gateway',
    date: '2026-05-27 22:10',
    status: 'suspended',
    fraudScore: 91,
    details: {
      accountAge: '34 days',
      totalTransactions: 12,
      suspiciousActivities: 6,
      lastLogin: '2026-05-27 21:50 (Vietnam)',
      normalLocation: 'United States',
      recentIPs: ['14.234.12.89 (VN)', '14.234.56.12 (VN)'],
      devices: ['Chrome (Windows 7)', 'Unknown Mobile Device'],
      walletBalance: '$450.00'
    },
    timeline: [
      { time: '22:10', event: 'Account permanently suspended', meta: 'Terms of Service violation' },
      { time: '22:05', event: '23 phishing emails detected and blocked', meta: 'Fake payment requests' },
      { time: '21:50', event: 'User logged in from Vietnam', meta: 'IP: 14.234.12.89' },
      { time: 'May 24', event: 'First complaint received from client', meta: 'Suspicious payment link' }
    ]
  },
  {
    id: 'FF-2024-007',
    user: {
      name: 'Mohammed Hassan',
      email: 'mohammed.h@uni.edu',
      id: 'USR-45672',
      avatar: 'MH'
    },
    flagType: '🎭 Identity Theft',
    description: 'Profile uses stolen identity documents and portfolio from another freelancer',
    severity: 'critical',
    detectionMethod: 'Image Recognition AI',
    date: '2026-05-27 14:55',
    status: 'resolved',
    fraudScore: 96,
    details: {
      accountAge: '19 days',
      totalTransactions: 5,
      suspiciousActivities: 7,
      lastLogin: '2026-05-26 10:30 (Pakistan)',
      normalLocation: 'Pakistan',
      recentIPs: ['39.45.67.123 (PK)', '39.45.67.123 (PK)'],
      devices: ['Chrome (Android 12)'],
      walletBalance: '$0.00 (Seized)'
    },
    timeline: [
      { time: '14:55', event: 'Case resolved - Account terminated', meta: 'Funds returned to victims' },
      { time: '14:30', event: 'Identity theft confirmed by original owner', meta: 'Police report filed' },
      { time: '14:00', event: 'AI detected duplicate portfolio images', meta: 'Match with USR-12345' },
      { time: 'May 25', event: 'Multiple clients reported suspicious behavior', meta: '4 complaints filed' }
    ]
  },
  {
    id: 'FF-2024-008',
    user: {
      name: 'Lisa Wang',
      email: 'lisa.wang@university.edu',
      id: 'USR-91823',
      avatar: 'LW'
    },
    flagType: '💰 Money Laundering',
    description: 'Circular transaction pattern detected - funds moving between related accounts',
    severity: 'medium',
    detectionMethod: 'Graph Analysis AI',
    date: '2026-05-27 09:15',
    status: 'monitoring',
    fraudScore: 72,
    details: {
      accountAge: '178 days',
      totalTransactions: 234,
      suspiciousActivities: 5,
      lastLogin: '2026-05-27 08:45 (United States)',
      normalLocation: 'United States',
      recentIPs: ['192.168.12.45 (US)', '192.168.12.45 (US)'],
      devices: ['Safari (macOS Sonoma)', 'iPhone 14'],
      walletBalance: '$3,456.00'
    },
    timeline: [
      { time: '09:15', event: 'Enhanced monitoring activated', meta: 'Transaction limits applied' },
      { time: '09:00', event: 'Circular transfer pattern identified', meta: '6 accounts in network' },
      { time: '08:45', event: '$12,000 transferred across network in 24 hours', meta: 'Avg transaction: $500' },
      { time: 'May 20', event: 'First suspicious pattern detected', meta: 'Same-day circular transfers' }
    ]
  },
  {
    id: 'FF-2024-009',
    user: {
      name: 'Kevin Brown',
      email: 'kevin.b@college.edu',
      id: 'USR-34567',
      avatar: 'KB'
    },
    flagType: '🔍 Fake Reviews',
    description: 'Artificial review manipulation - 15 5-star reviews from new accounts in 2 days',
    severity: 'low',
    detectionMethod: 'Review Pattern Analysis',
    date: '2026-05-26 16:40',
    status: 'resolved',
    fraudScore: 45,
    details: {
      accountAge: '234 days',
      totalTransactions: 67,
      suspiciousActivities: 2,
      lastLogin: '2026-05-26 15:30 (United States)',
      normalLocation: 'United States',
      recentIPs: ['45.67.89.123 (US)', '45.67.89.123 (US)'],
      devices: ['Chrome (macOS Ventura)'],
      walletBalance: '$2,345.00'
    },
    timeline: [
      { time: '16:40', event: 'Case closed - Warning issued to user', meta: 'Fake reviews removed' },
      { time: '16:30', event: 'User contacted and admitted to violation', meta: 'First-time offense' },
      { time: '16:00', event: '15 suspicious reviews identified and removed', meta: 'All from accounts <7 days old' },
      { time: 'May 24', event: 'Review pattern flagged by algorithm', meta: 'All posted within 48 hours' }
    ]
  }
];

// Live Activity Feed Data
let activityFeed = [
  { id: 1, title: 'New Critical Fraud Alert', description: 'Account takeover detected for USR-78492', time: 'Just now', user: 'Marcus Johnson', critical: true },
  { id: 2, title: 'Account Suspended', description: 'Payment fraud investigation initiated', time: '3 mins ago', user: 'Sarah Chen', critical: false },
  { id: 3, title: 'Bot Activity Detected', description: 'Rate limiting applied to USR-92341', time: '8 mins ago', user: 'David Martinez', critical: false },
  { id: 4, title: 'Chargeback Alert', description: '$1,200 dispute filed by card issuer', time: '15 mins ago', user: 'Sarah Chen', critical: true },
  { id: 5, title: 'Suspicious Login', description: 'Multiple failed attempts from Nigeria', time: '22 mins ago', user: 'Marcus Johnson', critical: true }
];

// State Management
let currentFilter = {
  search: '',
  severity: 'all',
  status: 'all'
};

let selectedFlag = null;

// ==========================================
// INITIALIZATION
// ==========================================
document.addEventListener('DOMContentLoaded', () => {
  renderFraudTable();
  renderActivityFeed();
  updateStatistics();
  initializeEventListeners();
  startLiveActivitySimulation();
});

// ==========================================
// RENDER FRAUD TABLE
// ==========================================
function renderFraudTable() {
  const tableBody = document.querySelector('.fraud-table tbody');

  if (!tableBody) return;

  const filteredFlags = filterFraudFlags();

  if (filteredFlags.length === 0) {
    tableBody.innerHTML = `
      <tr>
        <td colspan="8" style="text-align: center; padding: 40px; color: var(--color-text-secondary);">
          No fraud flags match your current filters
        </td>
      </tr>
    `;
    return;
  }

  tableBody.innerHTML = filteredFlags.map(flag => `
    <tr class="${flag.severity === 'critical' ? 'critical-row' : ''}" data-flag-id="${flag.id}">
      <td>
        <div class="fraud-user-cell">
          <div class="fraud-user-avatar">${flag.user.avatar}</div>
          <div class="fraud-user-info">
            <h4>${flag.user.name}</h4>
            <p>${flag.user.email}</p>
          </div>
        </div>
      </td>
      <td>
        <span class="fraud-flag-type">${flag.flagType}</span>
      </td>
      <td>
        <span class="fraud-description" title="${flag.description}">${flag.description}</span>
      </td>
      <td>
        <span class="fraud-severity-badge ${flag.severity}">
          ${flag.severity === 'critical' ? '🔴' : flag.severity === 'high' ? '🟠' : flag.severity === 'medium' ? '🟡' : '🟢'}
          ${flag.severity}
        </span>
      </td>
      <td>
        <span class="fraud-detection-method">${flag.detectionMethod}</span>
      </td>
      <td>
        <span class="fraud-date">${flag.date}</span>
      </td>
      <td>
        <span class="fraud-status-badge ${flag.status}">${capitalizeFirst(flag.status)}</span>
      </td>
      <td>
        <div class="fraud-actions">
          <button class="fraud-action-btn" onclick="viewInvestigation('${flag.id}')">Investigate</button>
        </div>
      </td>
    </tr>
  `).join('');
}

// ==========================================
// FILTER FRAUD FLAGS
// ==========================================
function filterFraudFlags() {
  return fraudFlags.filter(flag => {
    const matchesSearch = !currentFilter.search ||
      flag.user.name.toLowerCase().includes(currentFilter.search.toLowerCase()) ||
      flag.user.email.toLowerCase().includes(currentFilter.search.toLowerCase()) ||
      flag.flagType.toLowerCase().includes(currentFilter.search.toLowerCase()) ||
      flag.description.toLowerCase().includes(currentFilter.search.toLowerCase());

    const matchesSeverity = currentFilter.severity === 'all' || flag.severity === currentFilter.severity;
    const matchesStatus = currentFilter.status === 'all' || flag.status === currentFilter.status;

    return matchesSearch && matchesSeverity && matchesStatus;
  });
}

// ==========================================
// UPDATE STATISTICS
// ==========================================
function updateStatistics() {
  const activeCases = fraudFlags.filter(f => f.status === 'investigating' || f.status === 'monitoring').length;
  const criticalAlerts = fraudFlags.filter(f => f.severity === 'critical' && f.status !== 'resolved').length;
  const suspendedAccounts = fraudFlags.filter(f => f.status === 'suspended').length;
  const resolvedToday = fraudFlags.filter(f => f.status === 'resolved' && f.date.includes('2026-05-29')).length;

  document.querySelector('.fraud-stat-card:nth-child(1) .fraud-stat-value').textContent = activeCases;
  document.querySelector('.fraud-stat-card:nth-child(2) .fraud-stat-value').textContent = criticalAlerts;
  document.querySelector('.fraud-stat-card:nth-child(3) .fraud-stat-value').textContent = suspendedAccounts;
  document.querySelector('.fraud-stat-card:nth-child(4) .fraud-stat-value').textContent = resolvedToday;
}

// ==========================================
// RENDER ACTIVITY FEED
// ==========================================
function renderActivityFeed() {
  const feedList = document.querySelector('.activity-feed-list');

  if (!feedList) return;

  feedList.innerHTML = activityFeed.map(activity => `
    <div class="activity-item ${activity.critical ? 'critical' : ''}">
      <div class="activity-item-header">
        <span class="activity-item-title">${activity.title}</span>
        <span class="activity-item-time">${activity.time}</span>
      </div>
      <p class="activity-item-description">${activity.description}</p>
      <p class="activity-item-user">👤 ${activity.user}</p>
    </div>
  `).join('');
}

// ==========================================
// LIVE ACTIVITY SIMULATION
// ==========================================
function startLiveActivitySimulation() {
  setInterval(() => {
    const randomActivities = [
      { title: 'Login Attempt Blocked', description: 'Suspicious login from unknown location', critical: false },
      { title: 'Withdrawal Flagged', description: 'Large withdrawal request pending review', critical: true },
      { title: 'Pattern Detected', description: 'Unusual transaction velocity observed', critical: false },
      { title: 'Account Verified', description: 'Identity verification completed successfully', critical: false },
      { title: 'IP Blacklisted', description: 'Known fraud IP address blocked', critical: true }
    ];

    const randomActivity = randomActivities[Math.floor(Math.random() * randomActivities.length)];
    const randomUser = fraudFlags[Math.floor(Math.random() * fraudFlags.length)].user.name;

    activityFeed.unshift({
      id: Date.now(),
      title: randomActivity.title,
      description: randomActivity.description,
      time: 'Just now',
      user: randomUser,
      critical: randomActivity.critical
    });

    if (activityFeed.length > 10) {
      activityFeed.pop();
    }

    renderActivityFeed();
  }, 15000); // New activity every 15 seconds
}

// ==========================================
// VIEW INVESTIGATION PANEL
// ==========================================
function viewInvestigation(flagId) {
  selectedFlag = fraudFlags.find(f => f.id === flagId);

  if (!selectedFlag) return;

  // Populate investigation panel
  document.querySelector('.investigation-user-avatar').textContent = selectedFlag.user.avatar;
  document.querySelector('.investigation-user-details h3').textContent = selectedFlag.user.name;
  document.querySelector('.investigation-user-details p').textContent = selectedFlag.user.email;

  document.querySelector('.fraud-score-value').textContent = selectedFlag.fraudScore;

  // Populate account details
  const infoGrid = document.querySelector('.investigation-info-grid');
  infoGrid.innerHTML = `
    <div class="investigation-info-item">
      <div class="investigation-info-label">User ID</div>
      <div class="investigation-info-value">${selectedFlag.user.id}</div>
    </div>
    <div class="investigation-info-item">
      <div class="investigation-info-label">Account Age</div>
      <div class="investigation-info-value">${selectedFlag.details.accountAge}</div>
    </div>
    <div class="investigation-info-item">
      <div class="investigation-info-label">Total Transactions</div>
      <div class="investigation-info-value">${selectedFlag.details.totalTransactions}</div>
    </div>
    <div class="investigation-info-item">
      <div class="investigation-info-label">Suspicious Activities</div>
      <div class="investigation-info-value">${selectedFlag.details.suspiciousActivities}</div>
    </div>
    <div class="investigation-info-item">
      <div class="investigation-info-label">Last Login</div>
      <div class="investigation-info-value">${selectedFlag.details.lastLogin}</div>
    </div>
    <div class="investigation-info-item">
      <div class="investigation-info-label">Wallet Balance</div>
      <div class="investigation-info-value">${selectedFlag.details.walletBalance}</div>
    </div>
  `;

  // Populate timeline
  const timeline = document.querySelector('.investigation-timeline');
  timeline.innerHTML = selectedFlag.timeline.map(item => `
    <div class="investigation-timeline-item">
      <div class="investigation-timeline-time">${item.time}</div>
      <div class="investigation-timeline-content">${item.event}</div>
      <div class="investigation-timeline-meta">${item.meta}</div>
    </div>
  `).join('');

  // Show panel
  document.querySelector('.investigation-overlay').classList.add('active');
  document.querySelector('.investigation-panel').classList.add('active');
  document.body.style.overflow = 'hidden';
}

// ==========================================
// CLOSE INVESTIGATION PANEL
// ==========================================
function closeInvestigation() {
  document.querySelector('.investigation-overlay').classList.remove('active');
  document.querySelector('.investigation-panel').classList.remove('active');
  document.body.style.overflow = 'auto';
  selectedFlag = null;
}

// ==========================================
// SHOW ACTION MODAL
// ==========================================
function showSuspendModal() {
  if (!selectedFlag) return;

  const modal = document.querySelector('.action-modal');
  const modalHeader = modal.querySelector('.action-modal-header h3');
  const modalContent = modal.querySelector('.action-modal-content p');

  modalHeader.textContent = 'Suspend Account';
  modalContent.textContent = `Are you sure you want to suspend ${selectedFlag.user.name}'s account? This action will immediately freeze all account activities and pending transactions.`;

  document.querySelector('.action-modal-overlay').classList.add('active');
  modal.classList.add('active');
  modal.dataset.action = 'suspend';
}

function showResolveModal() {
  if (!selectedFlag) return;

  const modal = document.querySelector('.action-modal');
  const modalHeader = modal.querySelector('.action-modal-header h3');
  const modalContent = modal.querySelector('.action-modal-content p');

  modalHeader.textContent = 'Resolve Fraud Flag';
  modalContent.textContent = `Mark this fraud flag as resolved for ${selectedFlag.user.name}? Please provide resolution notes below.`;

  document.querySelector('.action-modal-overlay').classList.add('active');
  modal.classList.add('active');
  modal.dataset.action = 'resolve';
}

// ==========================================
// CONFIRM ACTION
// ==========================================
function confirmAction() {
  const modal = document.querySelector('.action-modal');
  const action = modal.dataset.action;
  const notes = document.querySelector('.action-modal-input').value;

  if (!selectedFlag) return;

  if (action === 'suspend') {
    selectedFlag.status = 'suspended';
    console.log(`Account suspended: ${selectedFlag.user.id} - Notes: ${notes}`);
  } else if (action === 'resolve') {
    selectedFlag.status = 'resolved';
    console.log(`Flag resolved: ${selectedFlag.id} - Notes: ${notes}`);
  }

  closeActionModal();
  closeInvestigation();
  renderFraudTable();
  updateStatistics();
}

// ==========================================
// CLOSE ACTION MODAL
// ==========================================
function closeActionModal() {
  document.querySelector('.action-modal-overlay').classList.remove('active');
  document.querySelector('.action-modal').classList.remove('active');
  document.querySelector('.action-modal-input').value = '';
}

// ==========================================
// EVENT LISTENERS
// ==========================================
function initializeEventListeners() {
  // Search input
  const searchInput = document.querySelector('.fraud-search');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      currentFilter.search = e.target.value;
      renderFraudTable();
    });
  }

  // Severity filter
  const severityFilter = document.querySelector('#severity-filter');
  if (severityFilter) {
    severityFilter.addEventListener('change', (e) => {
      currentFilter.severity = e.target.value;
      renderFraudTable();
    });
  }

  // Status filter
  const statusFilter = document.querySelector('#status-filter');
  if (statusFilter) {
    statusFilter.addEventListener('change', (e) => {
      currentFilter.status = e.target.value;
      renderFraudTable();
    });
  }

  // Close buttons
  const closeBtn = document.querySelector('.investigation-close-btn');
  if (closeBtn) {
    closeBtn.addEventListener('click', closeInvestigation);
  }

  const overlay = document.querySelector('.investigation-overlay');
  if (overlay) {
    overlay.addEventListener('click', closeInvestigation);
  }

  // Action modal buttons
  const cancelBtn = document.querySelector('.action-modal-btn.cancel');
  if (cancelBtn) {
    cancelBtn.addEventListener('click', closeActionModal);
  }

  const confirmBtn = document.querySelector('.action-modal-btn.confirm');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', confirmAction);
  }

  const modalOverlay = document.querySelector('.action-modal-overlay');
  if (modalOverlay) {
    modalOverlay.addEventListener('click', closeActionModal);
  }
}

// ==========================================
// UTILITY FUNCTIONS
// ==========================================
function capitalizeFirst(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}
