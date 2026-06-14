// Withdrawals Data

// Get avatar color
function getAvatarColor(name) {
    const colors = [
        'linear-gradient(135deg, #3B82F6, #2563EB)',
        'linear-gradient(135deg, #10B981, #059669)',
        'linear-gradient(135deg, #F59E0B, #D97706)',
        'linear-gradient(135deg, #8B5CF6, #7C3AED)',
        'linear-gradient(135deg, #EC4899, #DB2777)',
        'linear-gradient(135deg, #EF4444, #DC2626)'
    ];
    const index = name.charCodeAt(0) % colors.length;
    return colors[index];
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
    }).format(amount);
}

// Format date
function formatDate(date) {
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
        return `${days}d ago`;
    }
}


function openDetailPanel(data){

document
.getElementById("detail-panel")
.classList
.add("open");

document
.getElementById("detail-panel-body")
.innerHTML = `

<div class="detail-section">

<h4>${data.full_name}</h4>

<p>${data.email}</p>

<hr>

<p>
Amount:
₦${parseFloat(data.amount).toLocaleString()}
</p>

<p>
Bank:
${data.bank_name}
</p>

<p>
Account:
${data.account_number}
</p>

<p>
Reference:
${data.reference}
</p>

<p>
Status:
${data.status}
</p>

</div>

`;

}

function closeDetailPanel(){

document
.getElementById("detail-panel")
.classList
.remove("open");

}
// View withdrawal details
function viewDetails(withdrawalId) {
    const withdrawal = withdrawalsData.find(w => w.id === withdrawalId);
    if (!withdrawal) return;

    const detailBody = document.getElementById('detail-panel-body');
    detailBody.innerHTML = `
        <!-- User Profile -->
        <div class="detail-section">
            <div class="user-profile-card">
                <div class="profile-avatar" style="background: ${getAvatarColor(withdrawal.user.name)}">
                    ${withdrawal.user.initials}
                </div>
                <div class="profile-name">${withdrawal.user.name}</div>
                <div class="profile-email">${withdrawal.user.email}</div>
            </div>
        </div>

        <!-- Withdrawal Information -->
        <div class="detail-section">
            <div class="detail-section-title">Withdrawal Information</div>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Withdrawal ID</div>
                    <div class="detail-value">${withdrawal.id}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Amount</div>
                    <div class="detail-value-large">${formatCurrency(withdrawal.amount)}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <span class="status-badge status-${withdrawal.status}">${withdrawal.status}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Risk Level</div>
                    <div class="detail-value">
                        <div class="risk-indicator">
                            <div class="risk-dot risk-${withdrawal.riskLevel}"></div>
                            <span class="risk-text">${withdrawal.riskLevel}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Details -->
        <div class="detail-section">
            <div class="detail-section-title">Bank Details</div>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Bank Name</div>
                    <div class="detail-value">${withdrawal.bankName}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Account Number</div>
                    <div class="detail-value">${withdrawal.accountNumber}</div>
                </div>
            </div>
        </div>

        <!-- Account Summary -->
        <div class="detail-section">
            <div class="detail-section-title">Account Summary</div>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Wallet Balance</div>
                    <div class="detail-value">${formatCurrency(withdrawal.walletBalance)}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Total Withdrawals</div>
                    <div class="detail-value">${withdrawal.withdrawalHistory}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Fraud Score</div>
                    <div class="detail-value" style="color: ${withdrawal.fraudScore > 70 ? 'var(--color-error)' : withdrawal.fraudScore > 40 ? 'var(--color-warning)' : 'var(--color-success)'}">
                        ${withdrawal.fraudScore}/100
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Verification</div>
                    <div class="detail-value">
                        <span class="verification-badge verification-${withdrawal.verificationStatus}">
                            ${withdrawal.verificationStatus === 'verified' ? '✓ Verified' : '⏱ Pending'}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="detail-section">
            <div class="detail-section-title">Recent Transactions</div>
            <div class="transaction-list">
                ${withdrawal.recentTransactions.map(txn => `
                    <div class="transaction-item">
                        <div class="transaction-info">
                            <div class="transaction-type">${txn.type}</div>
                            <div class="transaction-date">${txn.date}</div>
                        </div>
                        <div class="transaction-amount" style="color: ${txn.amount.startsWith('+') ? 'var(--color-success)' : 'var(--color-error)'}">
                            ${txn.amount}
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>

        <!-- Payout Timeline -->
        <div class="detail-section">
            <div class="detail-section-title">Payout Timeline</div>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Requested</div>
                        <div class="timeline-time">${formatDate(withdrawal.requestDate)}</div>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-marker ${withdrawal.status === 'pending' ? 'current' : ''}"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Under Review</div>
                        <div class="timeline-time">${withdrawal.status === 'pending' ? 'In Progress' : 'Completed'}</div>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-marker ${withdrawal.status === 'approved' || withdrawal.status === 'processing' ? '' : 'pending'}"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Approved</div>
                        <div class="timeline-time">${withdrawal.status === 'approved' || withdrawal.status === 'processing' ? 'Completed' : 'Pending'}</div>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-marker ${withdrawal.status === 'processing' ? '' : 'pending'}"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Completed</div>
                        <div class="timeline-time">${withdrawal.status === 'processing' ? 'Processing' : 'Pending'}</div>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('detail-panel').classList.add('open');
}

// Close detail panel
function closeDetailPanel() {
    document.getElementById('detail-panel').classList.remove('open');
}

// Show approve modal
let currentWithdrawalId = null;
let currentAction = null;



// Show reject modal


// Confirm action


// Close confirmation modal
function closeConfirmationModal() {
    document.getElementById('confirmation-modal').classList.remove('active');
}

// Close modal on overlay click
document.getElementById('confirmation-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmationModal();
    }
});

// Close modals on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeConfirmationModal();
        closeDetailPanel();
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    renderWithdrawalsTable();
});
