<?php
session_start();

require_once "../config/database.php";
require_once "../wallet/transaction.php";
require_once "../includes/audit.php";
require_once "../includes/notifications.php";

/* =========================
   AUTH CHECK
========================= */

if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    die("Unauthorized");
}

if (empty($_SESSION['is_admin'])) {
    http_response_code(403);
    die("Forbidden");
}

/* =========================
   METHOD CHECK
========================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Method not allowed");
}

/* =========================
   CSRF VALIDATION
========================= */

if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    die("Invalid CSRF token");
}

/* =========================
   INPUT VALIDATION
========================= */

$withdrawal_id = isset($_POST['withdrawal_id']) ? (int) $_POST['withdrawal_id'] : 0;
$action = $_POST['action'] ?? '';

if ($withdrawal_id <= 0) {
    die("Invalid withdrawal ID");
}

if (!in_array($action, ['approve', 'reject'], true)) {
    die("Invalid action");
}

/* =========================
   PROCESS WITHDRAWAL
========================= */

try {

    /* FETCH WITHDRAWAL */

    $stmt = $pdo->prepare("
    SELECT user_id
    FROM withdrawals
    WHERE id = ?
");

    $stmt->execute([$withdrawal_id]);

    $withdrawal = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$withdrawal) {
        throw new Exception("Withdrawal not found");
    }

    processWithdrawal($pdo, $withdrawal_id, $action);

    
    if ($action === 'approve') {

    createNotification(
        $pdo,
        $withdrawal['user_id'],
        'withdrawal_approved',
        'Withdrawal Approved',
        'Your withdrawal request has been approved.',
        '../wallet/wallet_history.php'
    );


        logAudit(
            $pdo,
            $_SESSION['user_id'],
            'withdrawal_approved',
            'withdrawal',
            $withdrawal_id,
            'Admin approved withdrawal request'
        );
    } else {

        createNotification(
            $pdo,
            $withdrawal['user_id'],
            'withdrawal_rejected',
            'Withdrawal Rejected',
            'Your withdrawal request was rejected.',
            '../wallet/wallet_history.php'
        );

        logAudit(
            $pdo,
            $_SESSION['user_id'],
            'withdrawal_rejected',
            'withdrawal',
            $withdrawal_id,
            'Admin rejected withdrawal request'
        );
    }

    header("Location: withdrawal.php?success=1");
    exit();
} catch (Exception $e) {

    logAudit(
        $pdo,
        $_SESSION['user_id'],
        'withdrawal_processing_failed',
        'withdrawal',
        $withdrawal_id,
        $e->getMessage()
    );

    die($e->getMessage());
}
