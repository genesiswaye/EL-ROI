<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$user_id = $_SESSION['user_id'];

/* FETCH JOBS POSTED BY USER */

$stmt = $pdo->prepare("
SELECT id, title, category, budget, deadline, status, created_at
FROM jobs
WHERE created_by = ?
AND is_deleted = 0
AND status = 'open'
ORDER BY created_at DESC
");

$stmt->execute([$user_id]);

$jobs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Jobs - CampusLink</title>
    <link rel="stylesheet" href="../dist/output.css">
    <link rel="stylesheet" href="my_jobs.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        .modal {

            display: none;

            position: fixed;

            top: 0;
            left: 0;

            width: 100%;
            height: 100%;

            background:
                rgba(0, 0, 0, .45);

            justify-content: center;
            align-items: center;

            z-index: 9999;

        }

        .modal.show {

            display: flex;

        }

        .modal-content {

            background: white;

            padding: 28px;

            border-radius: 16px;

            width: 90%;
            max-width: 420px;

            text-align: center;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, .15);

        }

        .modal-content h3 {

            margin-bottom: 12px;

        }

        .modal-content p {

            color: #64748b;

            margin-bottom: 20px;

            line-height: 1.6;

        }

        .modal-buttons {

            display: flex;

            gap: 12px;

            justify-content: center;

        }

        .cancel-btn {

            padding: 12px 18px;

            border: none;

            border-radius: 10px;

            background: #e2e8f0;

            cursor: pointer;

        }

        .confirm-delete {

            padding: 12px 18px;

            border: none;

            border-radius: 10px;

            background: #ef4444;

            color: white;

            cursor: pointer;

        }

        .confirm-delete:hover {

            background: #dc2626;

        }
    </style>
</head>

<body class="bg-[#F7F8FA] min-h-screen">
    <?php
    $activePage = "my_jobs";
    include "../includes/employer_nav.php";
    ?>
    <div class="max-w-6xl mx-auto py-10 px-6">

        <h1 class="text-2xl font-semibold mb-6">Open Jobs</h1>

        <?php if (empty($jobs)): ?>

            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-500">You haven't posted any jobs yet.</p>
            </div>

        <?php else: ?>

            <div class="space-y-4">

                <?php foreach ($jobs as $job): ?>

                    <div class=" job-card bg-white p-6 rounded-lg shadow flex justify-between items-center" style="padding: 20px;">

                        <div class="job-card-content">

                            <h2 class="text-lg font-semibold">
                                <?= htmlspecialchars($job['title']) ?>
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                Category: <?= htmlspecialchars($job['category']) ?>
                            </p>

                            <p class="text-sm text-gray-500">
                                Budget: ₦<?= number_format($job['budget']) ?>
                            </p>

                            <p class="text-sm text-gray-500">
                                Deadline: <?= htmlspecialchars($job['deadline']) ?>
                            </p>

                            <p class="text-sm mt-2">

                                <?php if ($job['status'] === 'open'): ?>

                                    <span class="text-green-600 font-medium">Open</span>

                                <?php else: ?>

                                    <span class="text-gray-500 font-medium"><?= htmlspecialchars($job['status']) ?></span>

                                <?php endif; ?>

                            </p>

                        </div>

                        <div class="job-card-actions flex gap-3">

                            <a href="view_applications.php?job_id=<?= $job['id'] ?>"
                                class="px-4 py-2 bg-[#4B2E83] text-white rounded-lg text-sm hover:bg-[#5d3a9e]">
                                Applications
                            </a>

                            <a href="edit_job.php?job_id=<?= $job['id'] ?>"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                                Edit
                            </a>

                            <a href="delete_job.php" onclick="event.preventDefault(); openDeleteModal(<?= $job['id'] ?>);"
                                class="px-4 py-2 border border-red-400 text-red-600 rounded-lg text-sm hover:bg-red-50">
                                Delete
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>
    <div
        id="deleteModal"
        class="modal">

        <div class="modal-content">

            <h3>

                Delete Job

            </h3>

            <p>

                Are you sure you want to delete this job?

                This action cannot be undone.

            </p>

            <div class="modal-buttons">

                <button
                    class="cancel-btn"
                    onclick="closeDeleteModal()">

                    Cancel

                </button>

                <form
                    id="deleteForm"
                    method="POST"
                    action="delete_job.php">

                    <input
                        type="hidden"
                        name="job_id"
                        id="deleteJobId">

                    <button
                        type="submit"
                        class="confirm-delete">

                        Delete Job

                    </button>

                </form>

            </div>

        </div>

    </div>
    <script>
        function openDeleteModal(jobId) {

            document
                .getElementById(
                    "deleteModal"
                )
                .classList
                .add(
                    "show"
                );

            document
                .getElementById(
                    "deleteJobId"
                )
                .value =
                jobId;

        }

        function closeDeleteModal() {

            document
                .getElementById(
                    "deleteModal"
                )
                .classList
                .remove(
                    "show"
                );

        }

        window.onclick =
            function(event) {

                const modal =
                    document
                    .getElementById(
                        "deleteModal"
                    );

                if (
                    event.target === modal
                ) {

                    closeDeleteModal();

                }

            }
    </script>
    </script>

</body>

</html>