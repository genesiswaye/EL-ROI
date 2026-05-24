<?php
session_start();

require_once "../config/database.php";
require_once "../wallet/transaction.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

echo "STEP 0: FILE HIT<br>";

$employer_id = $_SESSION['user_id'];
$submission_id = $_POST['submission_id'] ?? null;
$application_id = $_POST['application_id'] ?? null;

var_dump($submission_id, $application_id);

if (!$submission_id || !$application_id) {
    die("Missing POST data");
}

try {

    $pdo->beginTransaction();
    echo "STEP 1: Transaction started<br>";

    /* 1. Get job + escrow info */

    $stmt = $pdo->prepare("
        SELECT 
            applications.job_id,
            escrows.id AS escrow_id,
            escrows.amount,
            escrows.freelancer_id
        FROM applications
        JOIN escrows ON escrows.job_id = applications.job_id
        WHERE applications.id = ?
        AND escrows.status = 'held'
    ");

    $stmt->execute([$application_id]);
    echo "STEP 2: Query executed<br>";

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    var_dump($data);

    if (!$data) {
        throw new Exception("No escrow found or already released");
    }

    echo "STEP 3: Escrow data found<br>";

    $job_id = $data['job_id'];
    $amount = (float)$data['amount'];
    $freelancer_id = $data['freelancer_id'];
    $escrow_id = $data['escrow_id'];

    /* 2. RELEASE FUNDS */

    echo "STEP 4: Releasing funds<br>";

    releaseFunds($pdo, $employer_id, $freelancer_id, $amount, $job_id);

    echo "STEP 5: Funds released<br>";

    /* 3. Update escrow */

    $stmt = $pdo->prepare("
        UPDATE escrows
        SET status = 'released'
        WHERE id = ?
    ");
    $stmt->execute([$escrow_id]);

    echo "STEP 6: Escrow updated<br>";

    /* 4. UPDATE SUBMISSION */

    $stmt = $pdo->prepare("
        UPDATE work_submissions
        SET status = 'accepted'
        WHERE id = ?
    ");
    $stmt->execute([$submission_id]);

    echo "STEP 7: Submission updated<br>";

    /* 5. UPDATE APPLICATION */

    $stmt = $pdo->prepare("
        UPDATE applications
        SET status = 'completed'
        WHERE id = ?
    ");
    $stmt->execute([$application_id]);

    echo "STEP 8: Application updated<br>";

    /* 6. UPDATE JOB */

    $stmt = $pdo->prepare("
        UPDATE jobs
        SET status = 'completed'
        WHERE id = ?
    ");
    $stmt->execute([$job_id]);

    echo "STEP 9: Job updated<br>";

    $pdo->commit();

    echo "STEP 10: COMMIT DONE<br>";

    exit(); // 🔥 STOP REDIRECT so you can see output

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "ERROR: " . $e->getMessage();
}