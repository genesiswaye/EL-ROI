// Role Permissions Data
const rolePermissions = {
    finance: {
        name: 'Finance Admin',
        description: 'Manage financial operations and transactions',
        icon: `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="1" x2="12" y2="23"></line>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
        </svg>`,
        iconClass: 'role-icon-finance',
        permissions: [
            'View all withdrawals',
            'Approve withdrawal requests',
            'Process payments',
            'View transaction history',
            'Generate financial reports',
            'Manage payment methods'
        ],
        securityNote: 'Finance admins have access to sensitive financial data. Regular audits are performed on all financial operations.'
    },
    support: {
        name: 'Support Admin',
        description: 'Handle user support and dispute resolution',
        icon: `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>`,
        iconClass: 'role-icon-support',
        permissions: [
            'View disputes',
            'Mediate disputes',
            'Access support tickets',
            'Communicate with users',
            'View user profiles',
            'Generate support reports'
        ],
        securityNote: 'Support admins can access user data for dispute resolution. All interactions are logged and monitored.'
    },
    super: {
        name: 'Super Admin',
        description: 'Complete system access and control',
        icon: `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
        </svg>`,
        iconClass: 'role-icon-super',
        permissions: [
            'Full system access',
            'Manage all admins',
            'Configure system settings',
            'Access all modules',
            'View audit logs',
            'Override all permissions',
            'Database access',
            'Security configuration'
        ],
        securityNote: 'Super Admins have unrestricted access. This role should only be assigned to trusted personnel. All actions are heavily monitored.'
    }
};

// Current state
let generatedPassword = '';
let selectedRole = null;

// Update permission preview
function updatePermissionPreview(role) {
    selectedRole = role;
    const preview = document.getElementById('permission-preview');
    const roleData = rolePermissions[role];

    if (!roleData) {
        preview.innerHTML = `
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                <p>Select a role to view permissions</p>
            </div>
        `;
        return;
    }

    preview.innerHTML = `
        <div class="role-preview">
            <div class="role-preview-header">
                <div class="role-preview-icon ${roleData.iconClass}">
                    ${roleData.icon}
                </div>
                <div>
                    <div class="role-preview-name">${roleData.name}</div>
                    <div class="role-preview-desc">${roleData.description}</div>
                </div>
            </div>

            <div class="permissions-section">
                <div class="permissions-title">Granted Permissions</div>
                <div class="permissions-list">
                    ${roleData.permissions.map(permission => `
                        <div class="permission-item">
                            <div class="permission-check">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <span class="permission-name">${permission}</span>
                        </div>
                    `).join('')}
                </div>
            </div>

            <div class="security-note">
                <div class="security-note-title">Security Note</div>
                <div class="security-note-text">${roleData.securityNote}</div>
            </div>
        </div>
    `;
}

// Generate secure password
function generatePassword() {
    const length = 16;
    const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    const numbers = '0123456789';
    const special = '!@#$%^&*';
    const upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const lower = 'abcdefghijklmnopqrstuvwxyz';

    let password = '';
    
    // Ensure at least one of each type
    password += upper[Math.floor(Math.random() * upper.length)];
    password += lower[Math.floor(Math.random() * lower.length)];
    password += numbers[Math.floor(Math.random() * numbers.length)];
    password += special[Math.floor(Math.random() * special.length)];

    // Fill the rest randomly
    for (let i = password.length; i < length; i++) {
        password += charset[Math.floor(Math.random() * charset.length)];
    }

    // Shuffle the password
    password = password.split('').sort(() => Math.random() - 0.5).join('');

    generatedPassword = password;
    
    // Display password
    const passwordDisplay = document.getElementById('password-display');
    passwordDisplay.innerHTML = `<span class="password-value">${password}</span>`;
    
    // Show copy actions
    document.getElementById('password-actions').style.display = 'flex';
}

// Copy password to clipboard
function copyPassword() {
    if (!generatedPassword) return;

    // Create temporary textarea
    const textarea = document.createElement('textarea');
    textarea.value = generatedPassword;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
        document.execCommand('copy');
        
        // Show feedback
        const feedback = document.getElementById('copy-feedback');
        feedback.classList.add('show');
        
        setTimeout(() => {
            feedback.classList.remove('show');
        }, 2000);
    } catch (err) {
        console.error('Failed to copy password:', err);
    }
    
    document.body.removeChild(textarea);
}

// Validate email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Clear error
function clearError(fieldId) {
    const field = document.getElementById(fieldId);
    const error = document.getElementById(`${fieldId.replace('admin-', '')}-error`);
    
    if (field) field.classList.remove('error');
    if (error) error.textContent = '';
}

// Show error
function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const error = document.getElementById(`${fieldId.replace('admin-', '')}-error`);
    
    if (field) field.classList.add('error');
    if (error) error.textContent = message;
}

// Validate form
function validateForm() {
    let isValid = true;

    // Clear all errors first
    clearError('admin-name');
    clearError('admin-email');
    document.getElementById('role-error').textContent = '';

    // Validate name
    const name = document.getElementById('admin-name').value.trim();
    if (!name) {
        showError('admin-name', 'Full name is required');
        isValid = false;
    } else if (name.length < 3) {
        showError('admin-name', 'Name must be at least 3 characters');
        isValid = false;
    }

    // Validate email
    const email = document.getElementById('admin-email').value.trim();
    if (!email) {
        showError('admin-email', 'Email address is required');
        isValid = false;
    } else if (!validateEmail(email)) {
        showError('admin-email', 'Please enter a valid email address');
        isValid = false;
    }

    // Validate role
    const roleSelected = document.querySelector('input[name="admin-role"]:checked');
    if (!roleSelected) {
        document.getElementById('role-error').textContent = 'Please select an administrator role';
        isValid = false;
    }

    // Validate password
    if (!generatedPassword) {
        document.getElementById('role-error').textContent = 'Please generate a password';
        isValid = false;
    }

    return isValid;
}

// Handle form submission
function handleSubmit(event) {
    event.preventDefault();

    // Validate form
    if (!validateForm()) {
        return;
    }

    // Get form data
    const name = document.getElementById('admin-name').value.trim();
    const email = document.getElementById('admin-email').value.trim();
    const role = document.querySelector('input[name="admin-role"]:checked').value;

    // Show loading state
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;

    // Simulate API call
    setTimeout(() => {
        // Hide loading state
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;

        // Show success notification
        showSuccessNotification();

        // Log the data (in production, this would be sent to the server)
        console.log('Admin Created:', {
            name,
            email,
            role,
            password: generatedPassword,
            createdAt: new Date().toISOString()
        });

        // Reset form
        resetForm();
    }, 2000);
}

// Show success notification
function showSuccessNotification() {
    const notification = document.getElementById('success-notification');
    notification.classList.add('show');

    // Auto-hide after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
    }, 5000);
}

// Close success notification
function closeSuccessNotification() {
    const notification = document.getElementById('success-notification');
    notification.classList.remove('show');
}

// Reset form
function resetForm() {
    document.getElementById('create-admin-form').reset();
    document.getElementById('password-display').innerHTML = '<span class="password-placeholder">Click "Generate Password" to create a secure password</span>';
    document.getElementById('password-actions').style.display = 'none';
    
    generatedPassword = '';
    selectedRole = null;
    
    // Reset permission preview
    document.getElementById('permission-preview').innerHTML = `
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
            <p>Select a role to view permissions</p>
        </div>
    `;
    
    // Clear errors
    clearError('admin-name');
    clearError('admin-email');
    document.getElementById('role-error').textContent = '';
}

// Add input listeners for real-time validation
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('admin-name');
    const emailInput = document.getElementById('admin-email');

    nameInput.addEventListener('input', function() {
        if (this.value.trim()) {
            clearError('admin-name');
        }
    });

    emailInput.addEventListener('input', function() {
        if (this.value.trim()) {
            clearError('admin-email');
        }
    });
});
