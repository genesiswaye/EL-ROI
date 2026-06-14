<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: ../dashboard/overview.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$job_id  = (int) $_POST['job_id'];

$stmt = $pdo->prepare("
    UPDATE jobs
    SET is_deleted = 1
    WHERE id = ?
    AND created_by = ?
");

$stmt->execute([
    $job_id,
    $user_id
]);

header("Location: my_jobs.php");
exit();