<?php

session_start();
require_once "../config/database.php";
require_once "../wallet/transaction.php";
require_once "../includes/audit.php";
require_once "../includes/notifications.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit();
}

$proposal_id = (int)$_POST['proposal_id'];
$job_id = (int)$_POST['job_id'];
$employer_id = $_SESSION['user_id'];

try {

    $pdo->beginTransaction();

    /* 1. Verify job belongs to employer */

    // $stmt = $pdo->prepare("
    //     SELECT id, budget
    //     FROM jobs
    //     WHERE id = ? AND created_by = ?
    // ");
    // $stmt->execute([$job_id, $employer_id]);

    // $job = $stmt->fetch(PDO::FETCH_ASSOC);

    // if (!$job) {
    //     throw new Exception("Unauthorized action");
    // }

    // $amount = (float)$job['budget'];

    /* 1. Validate + Fetch */

    $stmt = $pdo->prepare("
    SELECT bid_amount, student_id, status
    FROM applications
    WHERE id = ? AND job_id = ? FOR UPDATE
");
    $stmt->execute([$proposal_id, $job_id]);

    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
    throw new Exception("Application not found");
}

    if ($application['status'] !== 'pending') {
        throw new Exception("Application already processed");
    }

    $amount = (float)$application['bid_amount'];
    $freelancer_id = $application['student_id'];



    /* 2. CHECK ESCROW BEFORE INSERT */

    $stmt = $pdo->prepare("
    SELECT id FROM escrows WHERE job_id = ? AND status = 'held'
");
    $stmt->execute([$job_id]);

    if ($stmt->fetch()) {
        throw new Exception("Escrow already exists");
    }



    /* 3. LOCK MONEY (ESCROW) */

    holdFunds($pdo, $employer_id, $amount, $job_id);


    /* 4. Accept selected proposal */

    $stmt = $pdo->prepare("
        UPDATE applications
        SET status = 'accepted'
        WHERE id = ?
    ");
    $stmt->execute([$proposal_id]);


    /* 5. Reject others */

    $stmt = $pdo->prepare("
        UPDATE applications
        SET status = 'rejected'
        WHERE job_id = ?
        AND id != ?
    ");
    $stmt->execute([$job_id, $proposal_id]);


    /* 6. CREATE ESCROW RECORD */

    $stmt = $pdo->prepare("
        INSERT INTO escrows (job_id, employer_id, freelancer_id, amount, status)
        VALUES (?, ?, ?, ?, 'held')
    ");

    $stmt->execute([
        $job_id,
        $employer_id,
        $freelancer_id,
        $amount
    ]);


    /* 7. Update job */

    $stmt = $pdo->prepare("
        UPDATE jobs
        SET status = 'in_progress'
        WHERE id = ?
    ");
    $stmt->execute([$job_id]);


    $pdo->commit();

    createNotification(
    $pdo,
    $freelancer_id,
    'proposal_accepted',
    'Proposal Accepted',
    'Your proposal has been accepted and work can now begin.',
    '../messages/messages.php?application_id=' . $proposal_id
);

    /* 🔥 AUDIT LOG */

    logAudit(
        $pdo,
        $employer_id,
        'proposal_accepted',
        'job',
        $job_id,
        'Employer accepted freelancer proposal'
    );

    header("Location: browse_jobs.php?job_id=" . $job_id);
    exit();
} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // 🔥 LOG FAILED ESCROW HOLD
    logTransaction(
        $pdo,
        $employer_id,
        'escrow_hold',
        $amount ?? 0,
        null,
        'failed',
        $e->getMessage(),
        $job_id
    );
    
    logAudit(
    $pdo,
    $employer_id,
    'proposal_accept_failed',
    'job',
    $job_id,
    $e->getMessage()
);

    header(
        "Location: ../errors/error.php?message=" .
            urlencode($e->getMessage()) .
            "&return=" .
            urlencode("browse_jobs.php?job_id=" . $job_id)
    );
    exit();
}
