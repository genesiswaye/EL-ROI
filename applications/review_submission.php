<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_guard.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$employer_id = $_SESSION['user_id'];

/* VALIDATE INPUT */

if (!isset($_GET['submission_id'])) {
    die("Submission not specified");
}

$submission_id = $_GET['submission_id'];

$return_url = $_GET['return'] ?? '../messages/messages.php';

/* FETCH SUBMISSION + RELATED DATA */

$stmt = $pdo->prepare("
SELECT 
    work_submissions.id,
    work_submissions.file_path,
    work_submissions.file_name,
    work_submissions.message,
    work_submissions.status,
    work_submissions.created_at,

    applications.id AS application_id,
    applications.student_id,
    applications.job_id,

    users.full_name AS student_name,
    users.email AS student_email,

    jobs.title,
    jobs.created_by AS employer_id

FROM work_submissions

JOIN applications 
ON work_submissions.application_id = applications.id

JOIN users 
ON applications.student_id = users.id

JOIN jobs 
ON applications.job_id = jobs.id

WHERE work_submissions.id = ?
");

$stmt->execute([$submission_id]);

$submission = $stmt->fetch(PDO::FETCH_ASSOC);

/* CHECK IF EXISTS */

if (!$submission) {
    header("Location: ../errors/error.php?message=" . urlencode("Submission not found") . "&return=" . urlencode($return_url));
    exit();
}


if ($submission['status'] === 'accepted') {
    header("Location: ../errors/error.php?message=" . urlencode("Unauthorized access") . "&return=" . urlencode($return_url));
    exit();
}

/* SECURITY CHECK: ONLY JOB OWNER CAN VIEW */

if ($submission['employer_id'] != $employer_id) {
    header("Location: ../errors/error.php?message=" . urlencode("Already accepted") . "&return=" . urlencode($return_url));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div class="bg-white p-6 rounded-lg shadow">

        <h3 class="font-semibold text-lg mb-4">Submission</h3>
        <p class="text-sm text-gray-500 mb-2">
            Version <?= $submission['version'] ?>
        </p>

        <!-- VIEW -->
        <a href="../uploads/submissions/<?= htmlspecialchars($submission['file_path']) ?>"
            target="_blank"
            class="block mb-3 text-[#4B2E83] font-medium underline">
            View File
        </a>

        <!-- DOWNLOAD -->
        <a href="../uploads/submissions/<?= $submission['file_path'] ?>"
            download
            class="block mb-5 text-blue-600 font-medium underline">
            Download File
        </a>

        <!-- ACTIONS -->
        <div class="flex gap-3">

            <button onclick="openAcceptModal(<?= $submission['id'] ?>)"
                class="px-4 py-2 bg-green-600 text-white rounded-lg">
                Accept
            </button>

            <button onclick="openRevisionModal(<?= $submission['id'] ?>)"
                class="px-4 py-2 border border-orange-400 text-orange-600 rounded-lg">
                Request Revision
            </button>

        </div>

    </div>
</body>

</html>