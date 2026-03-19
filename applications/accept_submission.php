<?php
    session_start();
    require_once "../config/database.php";

    if (!isset($_SESSION['user_id'])) {
        die("Unauthorized");
    }

    $submission_id = $_POST['submission_id'];
    $application_id = $_POST['application_id'];

    /* UPDATE SUBMISSION */

    $stmt = $pdo->prepare("
    UPDATE work_submissions
    SET status = 'accepted'
    WHERE id = ?
    ");
    $stmt->execute([$submission_id]);

    /* UPDATE APPLICATION */

    $stmt = $pdo->prepare("
    UPDATE applications
    SET status = 'completed'
    WHERE id = ?
    ");
    $stmt->execute([$application_id]);

    header("Location: ../jobs/my_jobs.php");
    exit();
?>