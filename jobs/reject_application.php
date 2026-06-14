<?php

session_start();

require_once "../config/database.php";
require_once "../includes/audit.php";
require_once "../includes/notifications.php";

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: ../dashboard/overview.php");
    exit();
}

$proposal_id = (int) $_POST['proposal_id'];
$job_id = (int) $_POST['job_id'];

$employer_id = $_SESSION['user_id'];

try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT
            applications.id,
            applications.student_id,
            applications.status,
            jobs.created_by,
            jobs.title
        FROM applications

        JOIN jobs
            ON applications.job_id = jobs.id

        WHERE applications.id = ?
        AND applications.job_id = ?
    ");

    $stmt->execute([
        $proposal_id,
        $job_id
    ]);

    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
        throw new Exception("Application not found");
    }

    if ($application['created_by'] != $employer_id) {
        throw new Exception("Unauthorized action");
    }

    if ($application['status'] !== 'pending') {
        throw new Exception("Application already processed");
    }

    $stmt = $pdo->prepare("
        UPDATE applications
        SET status = 'rejected'
        WHERE id = ?
    ");

    $stmt->execute([
        $proposal_id
    ]);

    $pdo->commit();

    createNotification(
        $pdo,
        $application['student_id'],
        'application_rejected',
        'Application Rejected',
        'Your application for "' .
        $application['title'] .
        '" was not selected.',
        '../jobs/my_applications.php'
    );

    logAudit(
        $pdo,
        $employer_id,
        'application_rejected',
        'application',
        $proposal_id,
        'Employer rejected application'
    );

    header(
        "Location:view_applications.php?job_id=" .
        $job_id .
        "&success=rejected"
    );

    exit();

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    logAudit(
        $pdo,
        $employer_id,
        'application_reject_failed',
        'application',
        $proposal_id,
        $e->getMessage()
    );

    header(
        "Location: ../errors/error.php?message=" .
        urlencode($e->getMessage())
    );

    exit();
}