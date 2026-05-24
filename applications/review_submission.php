<?php
session_start();

require_once "../config/database.php";
require_once "../includes/auth_guard.php";
require_once "../includes/audit.php";
require_once "../includes/notifications.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$employer_id = (int) $_SESSION['user_id'];

if (!isset($_GET['submission_id'])) {
    die("Submission not specified");
}

$submission_id = (int) $_GET['submission_id'];
$return_url = $_GET['return'] ?? '../messages/messages.php';

$stmt = $pdo->prepare("
SELECT 
    work_submissions.id,
    work_submissions.file_path,
    work_submissions.file_name,
    work_submissions.message,
    work_submissions.status,
    work_submissions.version,
    work_submissions.created_at,
    work_submissions.file_type,
    work_submissions.file_size,

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

if (!$submission) {
    header("Location: ../errors/error.php?message=" . urlencode("Submission not found") . "&return=" . urlencode($return_url));
    exit();
}

if ((int)$submission['employer_id'] !== (int)$employer_id) {
    header("Location: ../errors/error.php?message=" . urlencode("Unauthorized access") . "&return=" . urlencode($return_url));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($submission['status'] === 'accepted') {
        header("Location: ../errors/error.php?message=" . urlencode("This submission has already been accepted") . "&return=" . urlencode($return_url));
        exit();
    }

    if ($action === 'accept') {

        require_once "../wallet/transaction.php";

        try {

            $pdo->beginTransaction();

            /* 1. GET ESCROW */

            $stmt = $pdo->prepare("
        SELECT 
            escrows.id,
            escrows.amount,
            escrows.freelancer_id,
            escrows.job_id,
            escrows.status
        FROM escrows
        WHERE job_id = ?
        AND status = 'held'
    ");

            $stmt->execute([$submission['job_id']]);
            $escrow = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$escrow) {
                throw new Exception("Escrow not found");
            }

            if ($escrow['status'] !== 'held') {
                throw new Exception("Escrow is no longer available for release");
            }

            $amount = (float)$escrow['amount'];
            $freelancer_id = (int)$escrow['freelancer_id'];
            $escrow_id = (int)$escrow['id'];

            /* 2. RELEASE FUNDS */

            releaseFunds(
                $pdo,
                $employer_id,
                $freelancer_id,
                $amount,
                $submission['job_id']
            );

            /* 3. UPDATE ESCROW */

            $stmt = $pdo->prepare("
        UPDATE escrows
        SET status = 'released'
        WHERE id = ?
    ");

            $stmt->execute([$escrow_id]);

            /* 4. UPDATE SUBMISSION */

            $stmt = $pdo->prepare("
        UPDATE work_submissions
        SET status = 'accepted'
        WHERE id = ?
    ");

            $stmt->execute([$submission_id]);

            /* 5. UPDATE APPLICATION */

            $stmt = $pdo->prepare("
        UPDATE applications
        SET status = 'completed'
        WHERE id = ?
    ");

            $stmt->execute([$submission['application_id']]);

            /* 6. UPDATE JOB */

            $stmt = $pdo->prepare("
        UPDATE jobs
        SET status = 'completed'
        WHERE id = ?
    ");

            $stmt->execute([$submission['job_id']]);

            $pdo->commit();

            createNotification(
                $pdo,
                $freelancer_id,
                'payment_released',
                'Escrow Released',
                'Your payment has been released successfully.',
                '../wallet/wallet_history.php'
            );
            /*AUDIT LOG */

            logAudit(
                $pdo,
                $employer_id,
                'escrow_released',
                'job',
                $submission['job_id'],
                'Employer approved submitted work and released escrow'
            );

            header("Location: ../messages/messages.php?application_id=" . $submission['application_id']);
            exit();
        } catch (Exception $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            // 🔥 LOG FAILURE
            logTransaction(
                $pdo,
                $employer_id,
                'escrow_release',
                $amount ?? 0,
                null,
                'failed',
                $e->getMessage(),
                $submission['job_id']
            );

            header(
                "Location: ../errors/error.php?message=" .
                    urlencode($e->getMessage()) .
                    "&return=" .
                    urlencode($return_url)
            );

            exit();
        }
    }

    if ($action === 'revision') {
        $stmt = $pdo->prepare("
            UPDATE work_submissions
            SET status = 'revision_requested'
            WHERE id = ?
        ");
        $stmt->execute([$submission_id]);

        header("Location: ../messages/messages.php?application_id=" . $submission['application_id']);
        exit();
    }
}

function formatStatus($status)
{
    return match ($status) {
        'submitted' => 'Awaiting Review',
        'revision_requested' => 'Revision Requested',
        'accepted' => 'Accepted',
        default => ucfirst($status)
    };
}


function formatFileSize($bytes)
{
    if (!$bytes || $bytes <= 0) {
        return 'Unknown size';
    }

    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    }

    if ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    }

    return $bytes . ' bytes';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Submission - CampusLink</title>
    <link rel="stylesheet" href="review_submission.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="page">
        <a href="<?= htmlspecialchars($return_url) ?>" class="back-link">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to previous page
        </a>

        <div class="card">
            <div class="card-header">
                <h1 class="page-title">Submission Review</h1>
                <p class="page-subtitle">Review the freelancer’s submitted work, download the file if needed, and decide whether to approve it or request changes.</p>
            </div>

            <div class="card-body">
                <div class="top-grid">
                    <div class="info-panel">
                        <div class="section-title">Submission Details</div>

                        <div class="detail-row">
                            <div class="detail-label">Project</div>
                            <div class="detail-value"><?= htmlspecialchars($submission['title']) ?></div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Freelancer</div>
                            <div class="detail-value"><?= htmlspecialchars($submission['student_name']) ?></div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Version</div>
                            <div class="detail-value">Version <?= (int)$submission['version'] ?></div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Status</div>
                            <div class="detail-value muted">
                                <span class="status-badge 
                                    <?= $submission['status'] === 'accepted' ? 'status-accepted' : '' ?>
                                    <?= $submission['status'] === 'revision_requested' ? 'status-revision' : '' ?>
                                    <?= $submission['status'] === 'submitted' ? 'status-submitted' : '' ?>">
                                    <?= htmlspecialchars(formatStatus($submission['status'])) ?>
                                </span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Submitted On</div>
                            <div class="detail-value">
                                <?= date('F j, Y \a\t g:i A', strtotime($submission['created_at'])) ?>
                            </div>
                        </div>
                    </div>

                    <div class="file-panel">
                        <div class="section-title">Attached File</div>

                        <div class="file-name-box">
                            <?= htmlspecialchars($submission['file_name']) ?>
                        </div>

                        <div class="file-actions">
                            <a href="view_submission_file.php?submission_id=<?= $submission['id'] ?>"
                                target="_blank"
                                class="btn btn-outline">
                                View File
                            </a>

                            <a href="download_submission_file.php?submission_id=<?= $submission['id'] ?>"
                                class="btn btn-outline">
                                Download File
                            </a>
                        </div>

                        <div class="detail-row" style="margin: 0.75rem 0.1rem;">
                            <div class="detail-label">File Type</div>
                            <div class="detail-value muted">
                                <?= htmlspecialchars($submission['file_type'] ?: 'Unknown') ?>
                            </div>
                        </div>

                        <div class="detail-row" style="margin-bottom: 1rem;">
                            <div class="detail-label">File Size</div>
                            <div class="detail-value muted">
                                <?= htmlspecialchars(formatFileSize($submission['file_size'])) ?>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="message-panel">
                    <div class="section-title">Freelancer Note</div>

                    <?php if (!empty($submission['message'])): ?>
                        <div class="message-text"><?= htmlspecialchars($submission['message']) ?></div>
                    <?php else: ?>
                        <div class="message-text empty-message">No message was included with this submission.</div>
                    <?php endif; ?>
                </div>

                <div class="action-panel">
                    <div class="section-title">Review Action</div>

                    <?php if ($submission['status'] !== 'accepted'): ?>
                        <div class="action-buttons">
                            <button type="button" class="btn btn-accept" onclick="openAcceptModal()">
                                Accept Submission
                            </button>

                            <button type="button" class="btn btn-revision" onclick="openRevisionModal()">
                                Request Revision
                            </button>
                        </div>
                    <?php else: ?>
                        <p class="accepted-note">This submission has already been accepted.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="acceptModal">
        <div class="modal">
            <h3 class="modal-title">Accept Submission</h3>
            <p class="modal-text">
                Are you sure you want to accept this submission? This confirms that the delivered work is satisfactory.
            </p>

            <form method="POST">
                <input type="hidden" name="action" value="accept">

                <div class="modal-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('acceptModal')">Cancel</button>
                    <button type="submit" class="btn btn-accept">Yes, Accept</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="revisionModal">
        <div class="modal">
            <h3 class="modal-title">Request Revision</h3>
            <p class="modal-text">
                Are you sure you want to request a revision? The freelancer will need to submit an updated version.
            </p>

            <form method="POST">
                <input type="hidden" name="action" value="revision">

                <div class="modal-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('revisionModal')">Cancel</button>
                    <button type="submit" class="btn btn-revision">Yes, Request Revision</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAcceptModal() {
            document.getElementById("acceptModal").style.display = "flex";
        }

        function openRevisionModal() {
            document.getElementById("revisionModal").style.display = "flex";
        }

        function closeModal(id) {
            document.getElementById(id).style.display = "none";
        }

        window.addEventListener("click", function(e) {
            const acceptModal = document.getElementById("acceptModal");
            const revisionModal = document.getElementById("revisionModal");

            if (e.target === acceptModal) {
                acceptModal.style.display = "none";
            }

            if (e.target === revisionModal) {
                revisionModal.style.display = "none";
            }
        });
    </script>
</body>

</html>