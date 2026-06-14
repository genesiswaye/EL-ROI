// Navigation State
let currentPage = 'dashboard';

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initializeNavigation();
    initializeMobileMenu();
    initializeModals();
    initializeActions();
    initializeSearch();
});

// Navigation
function initializeNavigation() {
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            navigateToPage(page);
        });
    });
}

function navigateToPage(page) {
    // Update active nav item
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelector(`[data-page="${page}"]`).classList.add('active');
    
    // Hide all pages
    document.querySelectorAll('.page').forEach(pageEl => {
        pageEl.style.display = 'none';
    });
    
    // Show selected page
    const targetPage = document.getElementById(`page-${page}`);
    if (targetPage) {
        targetPage.style.display = 'block';
        currentPage = page;
        
        // Close mobile menu if open
        document.getElementById('sidebar').classList.remove('mobile-open');
    }
}

// Mobile Menu
function initializeMobileMenu() {
    const mobileToggle = document.getElementById('mobile-menu-toggle');
    const sidebar = document.getElementById('sidebar');
    
    mobileToggle.addEventListener('click', function() {
        sidebar.classList.toggle('mobile-open');
    });
    
    // Close on backdrop click
    document.addEventListener('click', function(e) {
        if (sidebar.classList.contains('mobile-open') && 
            !sidebar.contains(e.target) && 
            !mobileToggle.contains(e.target)) {
            sidebar.classList.remove('mobile-open');
        }
    });
}

// Modals
let currentModalCallback = null;

function initializeModals() {
    const modalOverlay = document.getElementById('modal-overlay');
    const modalClose = document.getElementById('modal-close');
    const modalCancel = document.getElementById('modal-cancel');
    const modalConfirm = document.getElementById('modal-confirm');
    
    modalClose.addEventListener('click', closeModal);
    modalCancel.addEventListener('click', closeModal);
    
    modalConfirm.addEventListener('click', function() {
        if (currentModalCallback) {
            currentModalCallback();
        }
        closeModal();
    });
    
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            closeModal();
        }
    });
}

function showModal(title, message, callback) {
    const modalOverlay = document.getElementById('modal-overlay');
    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
    
    modalTitle.textContent = title;
    modalMessage.textContent = message;
    currentModalCallback = callback;
    
    modalOverlay.classList.add('active');
}

function closeModal() {
    const modalOverlay = document.getElementById('modal-overlay');
    modalOverlay.classList.remove('active');
    currentModalCallback = null;
}

// Toast Notifications
function showToast(title, message, type = 'info') {
    const toastContainer = document.getElementById('toast-container');
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    const icon = getToastIcon(type);
    
    toast.innerHTML = `
        ${icon}
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close">&times;</button>
    `;
    
    toastContainer.appendChild(toast);
    
    // Close button
    toast.querySelector('.toast-close').addEventListener('click', function() {
        removeToast(toast);
    });
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        removeToast(toast);
    }, 5000);
}

function removeToast(toast) {
    toast.style.animation = 'slideInRight 0.3s ease reverse';
    setTimeout(() => {
        toast.remove();
    }, 300);
}

function getToastIcon(type) {
    const icons = {
        success: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
        error: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
        warning: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
        info: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'
    };
    return icons[type] || icons.info;
}

// Actions
function initializeActions() {
    const logoutBtn = document.getElementById('logout-btn');
    const notificationBtn = document.getElementById('notification-btn');
    
    logoutBtn.addEventListener('click', function() {
        showModal(
            'Confirm Logout',
            'Are you sure you want to logout?',
            function() {
                showToast('Logged Out', 'You have been successfully logged out.', 'success');
                // In a real app, redirect to login
            }
        );
    });
    
    notificationBtn.addEventListener('click', function() {
        showToast('Notifications', 'You have 5 new notifications', 'info');
    });
}

// Withdrawal Actions
function approveWithdrawal(id) {
    showModal(
        'Approve Withdrawal',
        `Are you sure you want to approve withdrawal ${id}?`,
        function() {
            // Simulate API call
            showLoadingState();
            setTimeout(() => {
                hideLoadingState();
                showToast('Success', `Withdrawal ${id} has been approved`, 'success');
                updateWithdrawalStatus(id, 'approved');
            }, 1500);
        }
    );
}

function rejectWithdrawal(id) {
    showModal(
        'Reject Withdrawal',
        `Are you sure you want to reject withdrawal ${id}?`,
        function() {
            showLoadingState();
            setTimeout(() => {
                hideLoadingState();
                showToast('Rejected', `Withdrawal ${id} has been rejected`, 'warning');
                updateWithdrawalStatus(id, 'rejected');
            }, 1500);
        }
    );
}

function updateWithdrawalStatus(id, status) {
    // In a real app, this would update the backend
    const row = document.querySelector(`td.font-mono:contains("${id}")`);
    if (row) {
        const statusBadge = row.parentElement.querySelector('.badge');
        if (statusBadge) {
            if (status === 'approved') {
                statusBadge.className = 'badge badge-success';
                statusBadge.textContent = 'Approved';
            } else if (status === 'rejected') {
                statusBadge.className = 'badge badge-error';
                statusBadge.textContent = 'Rejected';
            }
        }
    }
}

// Loading State
function showLoadingState() {
    const pageContent = document.getElementById('page-content');
    const loadingSkeleton = document.getElementById('loading-skeleton');
    
    // Hide all pages
    document.querySelectorAll('.page').forEach(page => {
        if (page.id !== 'loading-skeleton') {
            page.style.display = 'none';
        }
    });
    
    loadingSkeleton.style.display = 'block';
}

function hideLoadingState() {
    const loadingSkeleton = document.getElementById('loading-skeleton');
    loadingSkeleton.style.display = 'none';
    
    // Show current page
    navigateToPage(currentPage);
}

// Search
function initializeSearch() {
    const globalSearch = document.getElementById('global-search');
    
    globalSearch.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        
        if (query.length > 0) {
            // Simulate search
            console.log('Searching for:', query);
        }
    });
    
    globalSearch.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const query = e.target.value;
            if (query.length > 0) {
                showToast('Search', `Searching for: ${query}`, 'info');
            }
        }
    });
}

// Add Admin Modal
function showAddAdminModal() {
    showToast('Coming Soon', 'Add admin functionality will be available soon', 'info');
}

// Helper: Contains selector
(function() {
    document.querySelectorAll = (function(original) {
        return function(selector) {
            if (selector.includes(':contains')) {
                const match = selector.match(/:contains\("([^"]+)"\)/);
                if (match) {
                    const text = match[1];
                    const baseSelector = selector.replace(/:contains\("[^"]+"\)/, '');
                    const elements = original.call(document, baseSelector || '*');
                    return Array.from(elements).filter(el => el.textContent.includes(text));
                }
            }
            return original.call(document, selector);
        };
    })(document.querySelectorAll);
})();

// Table Sorting (example)
function sortTable(tableId, column) {
    // Placeholder for table sorting functionality
    showToast('Sort', `Sorting by column ${column}`, 'info');
}

// Export Functions
function exportData(format) {
    showToast('Export', `Exporting data as ${format}...`, 'info');
    
    setTimeout(() => {
        showToast('Success', `Data exported successfully as ${format}`, 'success');
    }, 2000);
}

// Pagination
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('pagination-page')) {
        document.querySelectorAll('.pagination-page').forEach(page => {
            page.classList.remove('active');
        });
        e.target.classList.add('active');
        
        showToast('Navigation', `Loading page ${e.target.textContent}`, 'info');
    }
});

// Select All Checkboxes
document.addEventListener('change', function(e) {
    if (e.target.type === 'checkbox' && e.target.closest('thead')) {
        const table = e.target.closest('table');
        const checkboxes = table.querySelectorAll('tbody input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
    }
});

// Simulate Real-time Updates
function simulateRealTimeUpdates() {
    setInterval(() => {
        // Randomly update notification badge
        const notificationBadge = document.querySelector('.notification-badge');
        if (notificationBadge && Math.random() > 0.7) {
            const currentCount = parseInt(notificationBadge.textContent);
            notificationBadge.textContent = currentCount + 1;
            
            // Show toast for new notification
            showToast('New Activity', 'A new transaction requires your attention', 'info');
        }
    }, 30000); // Every 30 seconds
}

// Start real-time updates
simulateRealTimeUpdates();

// Keyboard Shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K for search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('global-search').focus();
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Performance: Lazy load images
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
});

// Form Validation Example
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const inputs = form.querySelectorAll('input[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = 'var(--color-error)';
            isValid = false;
        } else {
            input.style.borderColor = '';
        }
    });
    
    if (!isValid) {
        showToast('Validation Error', 'Please fill in all required fields', 'error');
    }
    
    return isValid;
}

// Analytics tracking (placeholder)
function trackEvent(category, action, label) {
    console.log('Analytics Event:', { category, action, label });
    // In a real app, send to analytics service
}

// Console welcome message
console.log('%cStudentLancer Admin Dashboard', 'font-size: 24px; font-weight: bold; color: #2563EB;');
console.log('%cVersion 1.0.0', 'font-size: 14px; color: #64748B;');
console.log('%cBuild: Production', 'font-size: 14px; color: #10B981;');
