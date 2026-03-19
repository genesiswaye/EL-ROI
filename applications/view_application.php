<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$student_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$app_id = $_GET['id'];

/* Ensure student owns the application */

$stmt = $pdo->prepare("
SELECT id 
FROM applications 
WHERE id = ? AND student_id = ? AND status = 'pending'
");

$stmt->execute([$app_id, $student_id]);

$app = $stmt->fetch();

if (!$app) {
    die("Unauthorized or cannot withdraw");
}

/* DELETE APPLICATION */

$stmt = $pdo->prepare("DELETE FROM applications WHERE id = ?");
$stmt->execute([$app_id]);

header("Location: my_applications.php");
exit();
