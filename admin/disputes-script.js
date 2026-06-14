// Dispute Data

// Get avatar color based on name
function getAvatarColor(name) {
    const colors = [
        'linear-gradient(135deg, #3B82F6, #2563EB)',
        'linear-gradient(135deg, #10B981, #059669)',
        'linear-gradient(135deg, #F59E0B, #D97706)',
        'linear-gradient(135deg, #8B5CF6, #7C3AED)',
        'linear-gradient(135deg, #EC4899, #DB2777)',
        'linear-gradient(135deg, #EF4444, #DC2626)',
        'linear-gradient(135deg, #14B8A6, #0D9488)',
        'linear-gradient(135deg, #F43F5E, #E11D48)'
    ];
    const index = name.charCodeAt(0) % colors.length;
    return colors[index];
}

// Get status badge HTML
function getStatusBadge(status) {
    const statusMap = {
        'open': { class: 'badge-open', text: '● Open' },
        'review': { class: 'badge-review', text: '⏱ Under Review' },
        'resolved': { class: 'badge-resolved', text: '✓ Resolved' },
        'refunded': { class: 'badge-refunded', text: '↩ Refunded' },
        'released': { class: 'badge-released', text: '✓ Released' }
    };
    const statusInfo = statusMap[status] || { class: 'badge-open', text: status };
    return `<span class="badge ${statusInfo.class}">${statusInfo.text}</span>`;
}

// Get priority indicator
function getPriorityIndicator(priority) {
    return `<span class="priority-indicator priority-${priority}"></span>`;
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

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0
    }).format(amount);
}

// Render disputes table

// Filter disputes


// Close dispute modal
function closeDisputeModal() {
    document.getElementById('dispute-modal').classList.remove('active');
}

// Close modal on overlay click
document.getElementById('dispute-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDisputeModal();
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDisputeModal();
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    renderDisputesTable();
});
