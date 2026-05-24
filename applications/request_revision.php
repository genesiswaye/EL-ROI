<?php
    session_start();
    require_once "../config/database.php";
    require_once "../includes/notifications.php";

    if (!isset($_SESSION['user_id'])) {
        die("Unauthorized");
    }

    $submission_id = $_POST['submission_id'];
    $application_id = $_POST['application_id'];

    /* UPDATE SUBMISSION */

    $stmt = $pdo->prepare("
    UPDATE work_submissions
    SET status = 'revision_requested'
    WHERE id = ?
    ");
    $stmt->execute([$submission_id]);

    /* FETCH STUDENT */

$stmt = $pdo->prepare("
    SELECT student_id
    FROM applications
    WHERE id = ?
");

$stmt->execute([$application_id]);

$application = $stmt->fetch(PDO::FETCH_ASSOC);

if ($application) {

    createNotification(
        $pdo,
        (int)$application['student_id'],
        'revision_requested',
        'Revision Requested',
        'The employer requested revisions to your submitted work.',
        '../messages/messages.php?application_id=' . $application_id
    );
}

    /* UPDATE APPLICATION */

    $stmt = $pdo->prepare("
    UPDATE applications
    SET status = 'in_progress'
    WHERE id = ?
    ");
    $stmt->execute([$application_id]);

    header("Location: ../jobs/my_jobs.php");
    exit();
?>