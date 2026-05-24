<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];

/* =========================
   FETCH TRANSACTIONS
========================= */

$stmt = $pdo->prepare("
    SELECT
        type,
        amount,
        reference,
        status,
        description,
        created_at
    FROM transactions
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$stmt->execute([$user_id]);

$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   CSV HEADERS
========================= */

$filename = "transactions_" . date("Y-m-d_H-i-s") . ".csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

/* =========================
   OPEN OUTPUT STREAM
========================= */

$output = fopen("php://output", "w");

/* =========================
   CSV COLUMN HEADERS
========================= */

fputcsv($output, [
    'Type',
    'Amount',
    'Reference',
    'Status',
    'Description',
    'Date'
]);

/* =========================
   WRITE ROWS
========================= */

foreach ($transactions as $tx) {

    fputcsv($output, [
        $tx['type'],
        $tx['amount'],
        $tx['reference'],
        $tx['status'],
        $tx['description'],
        $tx['created_at']
    ]);
}

fclose($output);
exit();