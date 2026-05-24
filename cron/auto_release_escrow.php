<?php

require_once "../config/database.php";
require_once "../wallet/transaction.php";
require_once "../includes/notifications.php";
require_once "../includes/audit.php";

/* =========================
   FIND EXPIRED SUBMISSIONS
========================= */

$stmt = $pdo->prepare("
    SELECT
        work_submissions.id AS submission_id,
        work_submissions.application_id,

        applications.job_id,
        applications.student_id,

        escrows.id AS escrow_id,
        escrows.amount,
        escrows.employer_id,
        escrows.freelancer_id

    FROM work_submissions

    JOIN applications
        ON work_submissions.application_id = applications.id

    JOIN escrows
        ON escrows.job_id = applications.job_id

    WHERE work_submissions.status = 'submitted'
    AND escrows.status = 'held'

    AND work_submissions.created_at <=
        NOW() - INTERVAL 7 DAY
");

$stmt->execute();

$expired = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   PROCESS EACH
========================= */

foreach ($expired as $item) {

    try {

        $pdo->beginTransaction();

        $submission_id = $item['submission_id'];
        $application_id = $item['application_id'];
        $job_id = $item['job_id'];

        $employer_id = $item['employer_id'];
        $freelancer_id = $item['freelancer_id'];

        $amount = (float)$item['amount'];
        $escrow_id = $item['escrow_id'];

        /* RELEASE FUNDS */

        releaseFunds(
            $pdo,
            $employer_id,
            $freelancer_id,
            $amount,
            $job_id
        );

        /* UPDATE ESCROW */

        $stmt = $pdo->prepare("
            UPDATE escrows
            SET status = 'released'
            WHERE id = ?
        ");

        $stmt->execute([$escrow_id]);

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

        /* UPDATE JOB */

        $stmt = $pdo->prepare("
            UPDATE jobs
            SET status = 'completed'
            WHERE id = ?
        ");

        $stmt->execute([$job_id]);

        /* NOTIFY FREELANCER */

        createNotification(
            $pdo,
            $freelancer_id,
            'escrow_auto_released',
            'Escrow Automatically Released',
            'Your payment was automatically released because the employer did not respond in time.',
            '../wallet/wallet_history.php'
        );

        /* NOTIFY EMPLOYER */

        createNotification(
            $pdo,
            $employer_id,
            'escrow_auto_released',
            'Escrow Automatically Released',
            'Escrow was automatically released after inactivity.',
            '../wallet/wallet_history.php'
        );

        /* AUDIT */

        logAudit(
            $pdo,
            $employer_id,
            'escrow_auto_released',
            'job',
            $job_id,
            'System automatically released escrow after timeout'
        );

        $pdo->commit();

    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        echo $e->getMessage() . "<br>";
    }
}

echo "Auto-release completed.";