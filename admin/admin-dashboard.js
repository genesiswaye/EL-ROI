// ==========================================
// ADMIN DASHBOARD - JAVASCRIPT
// StudentLancer Platform
// ==========================================

// Sample Data


let liveActivityData = [
  { icon: '💰', title: 'New Withdrawal Request', description: 'Sarah Chen requested $2,450 withdrawal', time: '2 mins ago' },
  { icon: '👤', title: 'New User Registration', description: 'Michael Brown joined as a student', time: '5 mins ago' },
  { icon: '💼', title: 'Job Posted', description: 'Company posted "Senior React Developer" job', time: '8 mins ago' },
  { icon: '✅', title: 'Job Completed', description: 'Website Redesign project marked complete', time: '12 mins ago' },
  { icon: '⚖️', title: 'Dispute Opened', description: 'Client opened dispute for Mobile App project', time: '18 mins ago' },
  { icon: '💳', title: 'Payment Processed', description: 'Escrow payment of $1,200 released', time: '25 mins ago' },
  { icon: '🛡️', title: 'Fraud Alert', description: 'Suspicious activity detected for Marcus Johnson', time: '32 mins ago' },
  { icon: '📊', title: 'Revenue Milestone', description: 'Platform revenue reached $428,547 this week', time: '45 mins ago' }
];

// ==========================================
// INITIALIZATION
// ==========================================
document.addEventListener('DOMContentLoaded', () => {
  initializeDashboard();
  
  renderLiveActivityFeed();
  startLiveActivityUpdates();
  animateKPINumbers();
});

// ==========================================
// INITIALIZE DASHBOARD
// ==========================================
function initializeDashboard() {
  // Set current date
  const dateElement = document.getElementById('currentDate');
  if (dateElement) {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    dateElement.textContent = now.toLocaleDateString('en-US', options);
  }

  // Format KPI numbers with commas
  formatKPINumbers();
}

// ==========================================
// FORMAT KPI NUMBERS
// ==========================================
function formatKPINumbers() {
  const kpiValues = document.querySelectorAll('.kpi-value');
  kpiValues.forEach(el => {
    const value = el.textContent;
    if (value.includes('$')) {
      // Already formatted
      return;
    }
    const formatted = parseInt(value.replace(/,/g, '')).toLocaleString();
    el.textContent = formatted;
  });
}

// ==========================================
// ANIMATE KPI NUMBERS
// ==========================================
function animateKPINumbers() {
  const kpiCards = document.querySelectorAll('.kpi-value');
  
  kpiCards.forEach(card => {
    const originalValue = card.textContent;
    let targetValue;
    let isCurrency = originalValue.includes('₦');
    
    if (isCurrency) {
      targetValue = parseInt(originalValue.replace(/[₦$,]/g, ''));
    } else {
      targetValue = parseInt(originalValue.replace(/,/g, ''));
    }
    
    let currentValue = 0;
    const increment = targetValue / 50;
    const duration = 1500;
    const stepTime = duration / 50;
    
    const timer = setInterval(() => {
      currentValue += increment;
      
      if (currentValue >= targetValue) {
        currentValue = targetValue;
        clearInterval(timer);
      }
      
      if (isCurrency) {
        card.textContent = '₦' + Math.floor(currentValue).toLocaleString();
      } else {
        card.textContent = Math.floor(currentValue).toLocaleString();
      }
    }, stepTime);
  });
}

// ==========================================
// RENDER RECENT WITHDRAWALS
// ==========================================
function renderRecentWithdrawals() {
  const tbody = document.getElementById('recentWithdrawals');
  if (!tbody) return;
  
  tbody.innerHTML = recentWithdrawalsData.map(item => `
    <tr>
      <td style="font-weight: 600;">${item.user}</td>
      <td style="font-family: 'Monaco', monospace;">${item.amount}</td>
      <td><span class="widget-status-badge ${item.status}">${capitalizeFirst(item.status)}</span></td>
      <td style="color: var(--color-text-secondary);">${item.date}</td>
    </tr>
  `).join('');
}

// ==========================================
// RENDER RECENT DISPUTES
// ==========================================
function renderRecentDisputes() {
  const tbody = document.getElementById('recentDisputes');
  if (!tbody) return;
  
  tbody.innerHTML = recentDisputesData.map(item => `
    <tr>
      <td style="font-weight: 600;">${item.job}</td>
      <td>${item.openedBy}</td>
      <td><span class="widget-status-badge ${item.status}">${capitalizeFirst(item.status)}</span></td>
      <td style="font-family: 'Monaco', monospace;">${item.escrow}</td>
    </tr>
  `).join('');
}

// ==========================================
// RENDER RECENT FRAUD ALERTS
// ==========================================
function renderRecentFraud() {
  const tbody = document.getElementById('recentFraud');
  if (!tbody) return;
  
  tbody.innerHTML = recentFraudData.map(item => `
    <tr>
      <td style="font-weight: 600;">${item.user}</td>
      <td><span class="widget-status-badge ${item.riskLevel}">${capitalizeFirst(item.riskLevel)}</span></td>
      <td><span class="widget-status-badge ${item.status}">${capitalizeFirst(item.status)}</span></td>
    </tr>
  `).join('');
}

// ==========================================
// RENDER RECENT ADMIN ACTIVITY
// ==========================================
function renderRecentActivity() {
  const tbody = document.getElementById('recentActivity');
  if (!tbody) return;
  
  tbody.innerHTML = recentActivityData.map(item => `
    <tr>
      <td style="font-weight: 600;">${item.admin}</td>
      <td>${item.action}</td>
      <td style="color: var(--color-text-secondary);">${item.timestamp}</td>
    </tr>
  `).join('');
}

// ==========================================
// RENDER LIVE ACTIVITY FEED
// ==========================================
function renderLiveActivityFeed() {
  const feedContainer = document.getElementById('activityFeed');
  if (!feedContainer) return;
  
  feedContainer.innerHTML = liveActivityData.map(item => `
    <div class="activity-item">
      <div class="activity-icon">${item.icon}</div>
      <div class="activity-content">
        <div class="activity-title">${item.title}</div>
        <div class="activity-description">${item.description}</div>
      </div>
      <div class="activity-time">${item.time}</div>
    </div>
  `).join('');
}

// ==========================================
// START LIVE ACTIVITY UPDATES
// ==========================================
function startLiveActivityUpdates() {
  setInterval(() => {
    const newActivities = [
      { icon: '👤', title: 'New User Registration', description: 'Student joined the platform' },
      { icon: '💼', title: 'Job Posted', description: 'New freelance job available' },
      { icon: '✅', title: 'Job Completed', description: 'Project successfully delivered' },
      { icon: '💰', title: 'Payment Processed', description: 'Escrow payment released' },
      { icon: '⚖️', title: 'Dispute Resolved', description: 'Dispute closed successfully' },
      { icon: '🛡️', title: 'Security Check', description: 'Fraud detection scan completed' },
      { icon: '📊', title: 'Analytics Update', description: 'Daily metrics refreshed' },
      { icon: '💳', title: 'Transaction Completed', description: 'Payment successfully processed' }
    ];
    
    const randomActivity = newActivities[Math.floor(Math.random() * newActivities.length)];
    
    liveActivityData.unshift({
      icon: randomActivity.icon,
      title: randomActivity.title,
      description: randomActivity.description,
      time: 'Just now'
    });
    
    // Keep only last 15 activities
    if (liveActivityData.length > 15) {
      liveActivityData.pop();
    }
    
    renderLiveActivityFeed();
  }, 10000); // Update every 10 seconds
}

// ==========================================
// TOGGLE NOTIFICATION CENTER
// ==========================================
function toggleNotifications() {
  const notificationCenter = document.getElementById('notificationCenter');
  if (!notificationCenter) return;
  
  notificationCenter.classList.toggle('active');
}

// ==========================================
// UTILITY FUNCTIONS
// ==========================================
function capitalizeFirst(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatCurrency(amount) {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount);
}

function formatNumber(num) {
  return num.toLocaleString('en-US');
}

// ==========================================
// KPI UPDATE SIMULATION
// ==========================================
setInterval(() => {
  // Simulate real-time KPI updates
  const kpiCards = document.querySelectorAll('.kpi-card');
  kpiCards.forEach(card => {
    // Add subtle pulse animation to random cards
    if (Math.random() > 0.7) {
      card.style.animation = 'none';
      setTimeout(() => {
        card.style.animation = '';
      }, 10);
    }
  });
}, 5000);

// ==========================================
// NOTIFICATION BADGE UPDATE
// ==========================================
setInterval(() => {
  const badge = document.querySelector('.notification-badge');
  if (badge && Math.random() > 0.8) {
    const currentCount = parseInt(badge.textContent);
    badge.textContent = currentCount + 1;
  }
}, 30000); // Update every 30 seconds

// ==========================================
// HEALTH METRICS ANIMATION
// ==========================================
window.addEventListener('load', () => {
  const healthBars = document.querySelectorAll('.health-bar');
  healthBars.forEach(bar => {
    const width = bar.style.width;
    bar.style.width = '0';
    setTimeout(() => {
      bar.style.width = width;
    }, 100);
  });
});
