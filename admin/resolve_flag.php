<?php


require_once "../config/database.php";

if (empty($_SESSION['is_admin'])) {
    die("Forbidden");
}

$flag_id = (int)($_POST['flag_id'] ?? 0);

$stmt = $pdo->prepare("
    UPDATE fraud_flags
    SET status = 'resolved'
    WHERE id = ?
");

$stmt->execute([$flag_id]);

header("Location: fraud_flags.php");
exit();