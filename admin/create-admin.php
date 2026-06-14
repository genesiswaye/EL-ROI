<?php

include "../config/database.php";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if (
        empty($name) ||
        empty($email) ||
        empty($role)
    ) {

        $error =
            "All fields required.";
    } else {

        $check =
            $pdo->prepare(

                "

        SELECT admin_id

        FROM admins

        WHERE email=?

        "

            );

        $check->execute([
            $email
        ]);

        if (
            $check->fetch()
        ) {

            $error =
                "Admin already exists.";
        } else {

            $tempPassword =
                bin2hex(
                    random_bytes(6)
                );

            $hash =
                password_hash(
                    $tempPassword,
                    PASSWORD_DEFAULT
                );

            $stmt =
                $pdo->prepare(

                    "

            INSERT INTO admins(

            full_name,
            email,
            password_hash,
            role,
            created_by

            )

            VALUES(

            ?,
            ?,
            ?,
            ?,
            ?

            )

            "

                );

            $stmt->execute([

                $name,

                $email,

                $hash,

                $role,

                $_SESSION['admin_id']

            ]);

            $success =
                "Admin created.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Administrator - StudentLancer Admin</title>
    <link rel="stylesheet" href="create-admin-style.css">
</head>
<body>
    <?php
    $activePage = "create-admin";
    include "admins2.php";
    ?>
    <div class="page-container">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Create Administrator</h1>
                <p class="page-subtitle">Secure Admin Onboarding & Access Management</p>
            </div>
        </div>

        <!-- Success Notification (Hidden by default) -->
        <div id="success-notification" class="success-notification">
            <div class="success-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div class="success-content">
                <h3 class="success-title">Administrator Created Successfully!</h3>
                <p class="success-message">The new admin account has been created. Credentials have been generated securely.</p>
            </div>
            <button class="success-close" onclick="closeSuccessNotification()">&times;</button>
        </div>

        <!-- Main Content Grid -->
        <div class="content-grid">
            <!-- Left Column - Form -->
            <div class="form-section">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Account Information</h3>
                        <p class="card-subtitle">Enter the administrator's details below</p>
                    </div>

                    <form id="create-admin-form" onsubmit="handleSubmit(event)">
                        <!-- Full Name -->
                        <div class="form-group">
                            <label for="admin-name" class="form-label">
                                Full Name
                                <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="admin-name" 
                                class="form-input"
                                placeholder="Enter full name"
                                required
                            >
                            <span class="error-message" id="name-error"></span>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="admin-email" class="form-label">
                                Email Address
                                <span class="required">*</span>
                            </label>
                            <input 
                                type="email" 
                                id="admin-email" 
                                class="form-input"
                                placeholder="admin@studentlancer.com"
                                required
                            >
                            <span class="error-message" id="email-error"></span>
                        </div>

                        <!-- Role Selection -->
                        <div class="form-group">
                            <label class="form-label">
                                Administrator Role
                                <span class="required">*</span>
                            </label>
                            <p class="form-hint">Select the access level for this administrator</p>

                            <div class="role-cards">
                                <label class="role-card" for="role-finance">
                                    <input 
                                        type="radio" 
                                        id="role-finance" 
                                        name="admin-role" 
                                        value="finance"
                                        onchange="updatePermissionPreview('finance')"
                                    >
                                    <div class="role-card-content">
                                        <div class="role-icon role-icon-finance">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                            </svg>
                                        </div>
                                        <div class="role-info">
                                            <div class="role-name">Finance Admin</div>
                                            <div class="role-desc">Manage withdrawals & payments</div>
                                        </div>
                                        <div class="role-check">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </div>
                                    </div>
                                </label>

                                <label class="role-card" for="role-support">
                                    <input 
                                        type="radio" 
                                        id="role-support" 
                                        name="admin-role" 
                                        value="support"
                                        onchange="updatePermissionPreview('support')"
                                    >
                                    <div class="role-card-content">
                                        <div class="role-icon role-icon-support">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="role-info">
                                            <div class="role-name">Support Admin</div>
                                            <div class="role-desc">Handle disputes & support tickets</div>
                                        </div>
                                        <div class="role-check">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </div>
                                    </div>
                                </label>

                                <label class="role-card" for="role-super">
                                    <input 
                                        type="radio" 
                                        id="role-super" 
                                        name="admin-role" 
                                        value="super"
                                        onchange="updatePermissionPreview('super')"
                                    >
                                    <div class="role-card-content">
                                        <div class="role-icon role-icon-super">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                            </svg>
                                        </div>
                                        <div class="role-info">
                                            <div class="role-name">Super Admin</div>
                                            <div class="role-desc">Full system access & control</div>
                                        </div>
                                        <div class="role-check">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <span class="error-message" id="role-error"></span>
                        </div>

                        <!-- Password Generation -->
                        <div class="form-group">
                            <label class="form-label">
                                Generated Password
                                <span class="required">*</span>
                            </label>
                            <p class="form-hint">A secure password will be generated automatically</p>
                            
                            <div class="password-section">
                                <div class="password-display" id="password-display">
                                    <span class="password-placeholder">Click "Generate Password" to create a secure password</span>
                                </div>
                                <button type="button" class="btn btn-outline" onclick="generatePassword()">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="23 4 23 10 17 10"></polyline>
                                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                                    </svg>
                                    Generate Password
                                </button>
                            </div>

                            <div id="password-actions" class="password-actions" style="display: none;">
                                <button type="button" class="btn btn-text" onclick="copyPassword()">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                    Copy Password
                                </button>
                                <span class="copy-feedback" id="copy-feedback">Copied!</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <span class="btn-text">Create Administrator</span>
                                <span class="btn-loader"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column - Permission Preview -->
            <div class="preview-section">
                <div class="card sticky-card">
                    <div class="card-header">
                        <h3 class="card-title">Permission Preview</h3>
                        <p class="card-subtitle">Access rights for selected role</p>
                    </div>

                    <div id="permission-preview" class="permission-preview">
                        <div class="empty-state">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                            <p>Select a role to view permissions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="create-admin-script.js"></script>
</body>
</html>
