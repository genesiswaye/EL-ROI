<?php

session_start();

require_once "../config/database.php";
require_once "transaction.php";
require_once "../includes/fraud.php";
require_once "../includes/audit.php";
require_once "../includes/notifications.php";

/* =========================
   VALIDATE INPUT
========================= */

if (!isset($_GET['reference'])) {
    die("No payment reference supplied");
}

$reference = trim($_GET['reference']);

if ($reference === '') {
    die("Invalid payment reference");
}

/* =========================
   VERIFY WITH PAYSTACK
========================= */

$secretKey = "sk_test_70e31c0738862007c9d1524582dec3bffceeac84";

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . urlencode($reference),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $secretKey,
        "Cache-Control: no-cache"
    ],
]);

$response = curl_exec($ch);

if ($response === false) {

    curl_close($ch);

    die("Could not connect to Paystack");
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

/* =========================
   VALIDATE PAYSTACK RESPONSE
========================= */

if ($httpCode !== 200) {
    die("Payment verification service unavailable");
}

$result = json_decode($response, true);

if (!is_array($result)) {
    die("Invalid payment response");
}

/* =========================
   PAYMENT FAILED
========================= */

if (
    empty($result['status']) ||
    empty($result['data']) ||
    $result['data']['status'] !== 'success'
) {

    $user_id = $_SESSION['user_id'] ?? null;

    if ($user_id) {

        logTransaction(
            $pdo,
            (int)$user_id,
            'deposit',
            0,
            $reference,
            'failed',
            'Paystack verification failed'
        );

        /* 🔥 OPTIONAL RATE CHECK */

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM fraud_flags
            WHERE user_id = ?
            AND reason = ?
            AND created_at >= NOW() - INTERVAL 1 HOUR
        ");

        $reason = 'Repeated failed Paystack verification';

        $stmt->execute([
            $user_id,
            $reason
        ]);

        $flagCount = (int)$stmt->fetchColumn();

        if ($flagCount < 5) {

            flagSuspiciousActivity(
                $pdo,
                (int)$user_id,
                $reason,
                'high'
            );
        }

        logAudit(
            $pdo,
            (int)$user_id,
            'payment_verification_failed',
            'transaction',
            null,
            'Paystack verification failed'
        );
    }

    die("Payment verification failed");
}

/* =========================
   EXTRACT DATA
========================= */

$data = $result['data'];

$user_id =
    $data['metadata']['user_id']
    ?? ($data['metadata']['custom_fields'][0]['value'] ?? null);

if (!$user_id) {
    die("Invalid payment metadata");
}

/* =========================
   PREVENT DOUBLE CREDIT
========================= */

$stmt = $pdo->prepare("
    SELECT id
    FROM transactions
    WHERE reference = ?
");

$stmt->execute([$reference]);

if ($stmt->fetch()) {
    die("Transaction already processed");
}

/* =========================
   VALIDATE AMOUNT
========================= */

$amount = ((float)$data['amount']) / 100;

if ($amount <= 0) {
    die("Invalid payment amount");
}

/* =========================
   CREDIT WALLET
========================= */

try {

    $pdo->beginTransaction();

    creditWallet(
        $pdo,
        (int)$user_id,
        (float)$amount,
        $reference
    );

    $pdo->commit();

    /* =========================
   CREATE NOTIFICATION
========================= */

createNotification(
    $pdo,
    (int)$user_id,
    'wallet_funded',
    'Wallet Funded',
    '₦' . number_format($amount, 2) .
    ' was added successfully to your wallet.',
    '../wallet/dashboard.php'
);


    /* =========================
       AUDIT LOG
    ========================= */

    logAudit(
        $pdo,
        (int)$user_id,
        'wallet_funded',
        'transaction',
        null,
        'User funded wallet via Paystack'
    );

    header("Location: dashboard.php?success=1");
    exit();

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    logTransaction(
        $pdo,
        (int)$user_id,
        'deposit',
        $amount ?? 0,
        $reference,
        'failed',
        'Wallet funding failed during processing'
    );

    logAudit(
        $pdo,
        (int)$user_id,
        'wallet_funding_failed',
        'transaction',
        null,
        $e->getMessage()
    );

    die("Payment processing failed");
}