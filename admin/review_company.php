<?php

session_start();

require_once "../config/database.php";
require_once "helper.php";

requireRole([
    'support_admin',
    'super_admin'
]);

$company_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT
        company_profiles.*,
        users.email,
        users.created_at
    FROM company_profiles

    JOIN users
        ON company_profiles.user_id = users.id

    WHERE company_profiles.id = ?

    ORDER BY users.created_at DESC
");

$stmt->execute([
    $company_id
]);

$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    die("Company not found");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="create-admin-style.css">
    <title>Document</title>
</head>

<body>

        <div class="card mb-6">

            <div class="card-header">

                <h3 class="card-title">
                    <?= htmlspecialchars($company['company_name']) ?>
                </h3>

                <span class="status-badge pending">
                    Pending Review
                </span>

            </div>

            <div class="content-grid">

                <!-- LEFT SIDE -->
                <div class="form-section">

                    <div class="form-group">
                        <label class="form-label">
                            Company Email
                        </label>

                        <div class="form-input readonly">
                            <?= htmlspecialchars($company['email']) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Industry
                        </label>

                        <div class="form-input readonly">
                            <?= htmlspecialchars($company['industry']) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Website
                        </label>

                        <div class="form-input readonly">
                            <?= htmlspecialchars($company['website'] ?: 'Not Provided') ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            CAC Number
                        </label>

                        <div class="form-input readonly">
                            <?= htmlspecialchars($company['cac_number']) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Phone Number
                        </label>

                        <div class="form-input readonly">
                            <?= htmlspecialchars($company['phone_number']) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Company Address
                        </label>

                        <div class="form-input readonly">
                            <?= htmlspecialchars($company['address']) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Company Description
                        </label>

                        <div class="description-box">
                            <?= nl2br(htmlspecialchars($company['description'])) ?>
                        </div>
                    </div>

                    <div class="form-actions">

                        <a
                            href="approve_company.php?id=<?= $company['id'] ?>"
                            class="btn btn-success">
                            Approve Company
                        </a>

                        <a
                            href="reject_company.php?id=<?= $company['id'] ?>"
                            class="btn btn-danger">
                            Reject Company
                        </a>

                    </div>

                </div>

                <!-- RIGHT SIDE -->
                <div class="preview-section">

                    <div class="card sticky-card">

                        <div class="card-header">

                            <h3 class="card-title">
                                Verification Summary
                            </h3>

                            <p class="card-subtitle">
                                Application Review Checklist
                            </p>

                        </div>

                        <ul class="verification-list">

                            <li>
                                <?= !empty($company['cac_number']) ? '✓' : '✗' ?>
                                CAC Number Submitted
                            </li>

                            <li>
                                <?= !empty($company['email']) ? '✓' : '✗' ?>
                                Company Email Provided
                            </li>

                            <li>
                                <?= !empty($company['website']) ? '✓' : '✗' ?>
                                Website Added
                            </li>

                            <li>
                                <?= !empty($company['contact_name']) ? '✓' : '✗' ?>
                                Contact Person Added
                            </li>

                        </ul>

                        <hr class="my-4">

                        <p>
                            <strong>Submitted:</strong>
                            <?= date(
                                "M d, Y",
                                strtotime($company['created_at'])
                            ) ?>
                        </p>

                        <p>
                            <strong>Verification Status:</strong>
                            Pending
                        </p>

                        <div class="risk-card low">
                            Risk Level: LOW
                        </div>

                    </div>

                </div>

            </div>

        </div>
</body>

</html>