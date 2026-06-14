<?php

session_start();

require_once "../config/database.php";
require_once "helper.php";
require_once "../includes/company_mailer.php";

requireRole([
    'support_admin',
    'super_admin'
]);

$company_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT
        company_profiles.*,
        users.id AS user_id,
        users.email

    FROM company_profiles

    JOIN users
        ON company_profiles.user_id = users.id

    WHERE company_profiles.id = ?
");

$stmt->execute([
    $company_id
]);

$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    die("Company not found");
}

$stmt = $pdo->prepare("
    UPDATE users
    SET account_status='rejected'
    WHERE id=?
");

$stmt->execute([
    $company['user_id']
]);

$stmt = $pdo->prepare("
    UPDATE company_profiles
    SET verification_status='rejected'
    WHERE id=?
");

$stmt->execute([
    $company_id
]);

sendCompanyRejectionEmail(
    $company['email'],
    $company['company_name']
);

header(
    "Location: companies.php?success=rejected"
);

exit();