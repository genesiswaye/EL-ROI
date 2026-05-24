<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_guard.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    die("Unauthorized access");
}

$student_id = $_SESSION['user_id'];

/* =========================
   VALIDATE INPUT
========================= */

if (!isset($_GET['application_id'])) {
    die("Application not specified");
}

$application_id = (int) $_GET['application_id'];

/* =========================
   VERIFY OWNERSHIP
========================= */

$stmt = $pdo->prepare("
    SELECT applications.*, jobs.title
    FROM applications
    JOIN jobs ON applications.job_id = jobs.id
    WHERE applications.id = ? 
      AND applications.student_id = ? 
      AND applications.status = 'accepted'
");

$stmt->execute([$application_id, $student_id]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    die("Invalid application");
}

/* =========================
   HANDLE SUBMISSION
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $message = trim($_POST['message'] ?? '');
    $fileType = $_FILES['submission_file']['type'];
    $fileSize = $_FILES['submission_file']['size'];
    $uploadDir = __DIR__ . "/../uploads/submissions/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (!isset($_FILES['submission_file']) || $_FILES['submission_file']['error'] !== UPLOAD_ERR_OK) {
        die("Please upload a file");
    }

    $originalName = $_FILES['submission_file']['name'];
    $tmpName = $_FILES['submission_file']['tmp_name'];

    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $newFileName = uniqid('submission_', true) . ($extension ? '.' . $extension : '');

    $destination = $uploadDir . $newFileName;

    if (!move_uploaded_file($tmpName, $destination)) {
        die("Failed to upload file");
    }

    $stmt = $pdo->prepare("
        SELECT MAX(version)
        FROM work_submissions
        WHERE application_id = ?
    ");
    $stmt->execute([$application_id]);
    $current_version = $stmt->fetchColumn();

    $new_version = $current_version ? $current_version + 1 : 1;

    $stmt = $pdo->prepare("
    INSERT INTO work_submissions
    (application_id, file_path, file_name, file_type, file_size, message, status, version)
    VALUES (?, ?, ?, ?, ?, ?, 'submitted', ?)
    ");

   $stmt->execute([
    $application_id,
    $newFileName,
    $originalName,
    $fileType,
    $fileSize,
    $message,
    $new_version
]);

    header("Location: ../messages/messages.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Work - CampusLink</title>
    <link rel="stylesheet" href="submit_work.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="page">
        <a href="../messages/messages.php" class="back-link">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to previous page
        </a>

        <div class="page-header">
            <h1 class="page-title">Submit Work</h1>
            <p class="page-subtitle">Upload your completed work for employer review. You can also include a short note explaining what you submitted.</p>
        </div>

        <div class="card">
            <div class="job-pill">
                <?= htmlspecialchars($app['title']) ?>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="submission_file" class="form-label">Upload File</label>

                    <div class="file-upload-wrapper">
                        <span id="file-name" class="file-name">Choose your work file</span>

                        <label for="submission_file" class="file-upload-btn">
                            Browse
                        </label>

                        <input
                            type="file"
                            name="submission_file"
                            id="submission_file"
                            class="file-input"
                            required>
                    </div>

                    <p class="helper-text">Upload the final file or revision you want the employer to review.</p>
                </div>

                <div class="form-group">
                    <label for="message" class="form-label">Message (optional)</label>
                    <textarea
                        name="message"
                        id="message"
                        class="form-textarea"
                        placeholder="Explain what you submitted, mention changes made, or add any helpful context..."></textarea>
                </div>

                <button type="submit" class="submit-btn">Submit Work</button>
            </form>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById("submission_file");
        const fileName = document.getElementById("file-name");

        fileInput.addEventListener("change", function () {
            if (this.files && this.files.length > 0) {
                fileName.textContent = this.files[0].name;
            } else {
                fileName.textContent = "Choose your work file";
            }
        });
    </script>
</body>
</html>