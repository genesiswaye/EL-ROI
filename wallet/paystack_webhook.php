<?php

require_once "../config/database.php";
require_once "transaction.php";
require_once "../includes/audit.php";

/* =========================
   GET RAW PAYLOAD
========================= */

$payload = file_get_contents("php://input");

if (!$payload) {
    http_response_code(400);
    exit();
}

/* =========================
   VERIFY PAYSTACK SIGNATURE
========================= */

$secretKey = "sk_test_YOUR_SECRET_KEY";

$signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';

$computedSignature = hash_hmac(
    'sha512',
    $payload,
    $secretKey
);

if (!hash_equals($computedSignature, $signature)) {

    http_response_code(403);
    exit();
}

/* =========================
   DECODE JSON
========================= */

$event = json_decode($payload, true);

if (!$event) {
    http_response_code(400);
    exit();
}

/* =========================
   ONLY HANDLE SUCCESS
========================= */

if (
    $event['event'] !== 'charge.success'
) {
    http_response_code(200);
    exit();
}

$data = $event['data'];

/* =========================
   EXTRACT DATA
========================= */

$reference = $data['reference'];

$user_id =
    $data['metadata']['user_id']
    ?? null;

$amount =
    ((float)$data['amount']) / 100;

if (
    !$reference ||
    !$user_id ||
    $amount <= 0
) {
    http_response_code(400);
    exit();
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

    http_response_code(200);
    exit();
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

    logAudit(
        $pdo,
        (int)$user_id,
        'wallet_funded_webhook',
        'transaction',
        null,
        'Wallet funded via Paystack webhook'
    );

    http_response_code(200);
    exit();

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    exit();
}