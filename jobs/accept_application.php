<?php

session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit();
}

$proposal_id = $_POST['proposal_id'];
$job_id = $_POST['job_id'];
$employer_id = $_SESSION['user_id'];

try {

    $pdo->beginTransaction();

    /* Verify job belongs to employer */

    $stmt = $pdo->prepare("
        SELECT id
        FROM jobs
        WHERE id = ? AND created_by = ?
    ");

    $stmt->execute([$job_id, $employer_id]);

    if (!$stmt->fetch()) {
        throw new Exception("Unauthorized action");
    }


    /* Verify proposal belongs to this job */

    $stmt = $pdo->prepare("
        SELECT id
        FROM applications
        WHERE id = ? AND job_id = ?
    ");

    $stmt->execute([$proposal_id, $job_id]);

    if (!$stmt->fetch()) {
        throw new Exception("Invalid Application");
    }


    /* Accept selected proposal */

    $stmt = $pdo->prepare("
        UPDATE applications
        SET status = 'accepted'
        WHERE id = ?
    ");

    $stmt->execute([$proposal_id]);


    /* Reject all other proposals */

    $stmt = $pdo->prepare("
        UPDATE applications
        SET status = 'rejected'
        WHERE job_id = ?
        AND id != ?
    ");

    $stmt->execute([$job_id, $proposal_id]);


    /* Update job status */

    $stmt = $pdo->prepare("
        UPDATE jobs
        SET status = 'in_progress'
        WHERE id = ?
    ");

    $stmt->execute([$job_id]);


    $pdo->commit();

    header("Location: browse_jobs.php?job_id=" . $job_id);
    exit();

} catch (Exception $e) {

    $pdo->rollBack();
    die($e->getMessage());

}