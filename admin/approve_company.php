<?php

session_start();

require_once "../config/database.php";
require_once "helper.php";
require_once "../includes/company_mailer.php";

requireRole([
    'support_admin',
    'super_admin'
]);

$company_id = (int) ($_GET['id'] ?? 0);

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

if (
    $company['verification_status'] !== 'pending'
) {
    die("This company has already been reviewed.");
}

if (!$company) {
    die("Company not found");
}

// $tempPassword =
//     strtoupper(
//         bin2hex(random_bytes(4))
//     );

// $hash =
//     password_hash(
//         $tempPassword,
//         PASSWORD_DEFAULT
//     );

$pdo->beginTransaction();

try {

    $stmt = $pdo->prepare("
        UPDATE users
        SET account_status = 'active'
        WHERE id = ?
    ");

    $stmt->execute([
        $company['user_id']
    ]);

    $stmt = $pdo->prepare("
        UPDATE company_profiles
        SET verification_status='verified'
        WHERE id=?
    ");

    $stmt->execute([
        $company_id
    ]);

    $pdo->commit();

}catch (PDOException $e) {

    $pdo->rollBack();

    die($e->getMessage());
}

$emailSent = sendCompanyApprovalEmail(
    $company['email'],
    $company['company_name']
);

if (!$emailSent) {
    error_log(
        "Failed to send approval email to " .
        $company['email']
    );
}

header(
    "Location: companies.php?success=approved"
);

exit();