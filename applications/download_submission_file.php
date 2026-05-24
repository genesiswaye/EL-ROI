<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_guard.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$user_id = (int) $_SESSION['user_id'];

if (!isset($_GET['submission_id'])) {
    die("Submission not specified");
}

$submission_id = (int) $_GET['submission_id'];

$stmt = $pdo->prepare("
SELECT 
    work_submissions.file_path,
    work_submissions.file_name,
    applications.student_id,
    jobs.created_by
FROM work_submissions
JOIN applications ON work_submissions.application_id = applications.id
JOIN jobs ON applications.job_id = jobs.id
WHERE work_submissions.id = ?
");

$stmt->execute([$submission_id]);
$submission = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$submission) {
    die("Submission not found");
}

if ($user_id !== (int)$submission['student_id'] && $user_id !== (int)$submission['created_by']) {
    die("Unauthorized access");
}

$file = basename($submission['file_path']);
$filePath = realpath(__DIR__ . "/../uploads/submissions/" . $file);

$uploadsDir = realpath(__DIR__ . "/../uploads/submissions");

if (!$filePath || !$uploadsDir || strpos($filePath, $uploadsDir) !== 0 || !file_exists($filePath)) {
    die("File not found");
}

header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . basename($submission['file_name'] ?: $file) . "\"");
header("Content-Length: " . filesize($filePath));
readfile($filePath);
exit();
?>