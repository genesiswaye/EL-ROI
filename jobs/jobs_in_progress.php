<?php

session_start();

require_once "../config/database.php";

/* Ensure user is logged in */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {

    /* Get jobs that are currently in progress */

    $stmt = $pdo->prepare("
    SELECT 
        jobs.id,
        jobs.title,
        jobs.budget,
        jobs.deadline,
        jobs.category,
        jobs.status,
        users.full_name AS student_name,
        applications.student_id
    FROM jobs
    JOIN applications 
        ON jobs.id = applications.job_id
    JOIN users 
        ON applications.student_id = users.id
    WHERE jobs.created_by = ?
    AND jobs.status = 'in_progress'
    AND applications.status = 'accepted'
    ORDER BY jobs.deadline ASC
");

    $stmt->execute([$user_id]);

    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {

    die("Error loading jobs.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dist/output.css">
    <link rel="stylesheet" href="my_jobs.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <title>Document</title>
    <style>
        .modal {

            display: none;

            position: fixed;

            inset: 0;

            background:
                rgba(15,
                    23,
                    42,
                    0.65);

            backdrop-filter:
                blur(4px);

            justify-content: center;

            align-items: center;

            padding: 20px;

            z-index: 9999;

            animation:
                fadeIn 0.25s ease;

        }

        .modal.show {

            display: flex;

        }

        .modal-content {

            background: white;

            width: 100%;

            max-width: 500px;

            padding: 28px;

            border-radius: 20px;

            box-shadow:
                0 25px 60px rgba(0,
                    0,
                    0,
                    0.18);

            animation:
                slideUp 0.25s ease;

        }

        .modal-content h3 {

            font-size: 1.35rem;

            font-weight: 700;

            color: #111827;

            margin-bottom: 10px;

        }

        .modal-content p {

            color: #6b7280;

            line-height: 1.6;

            margin-bottom: 20px;

            font-size: 0.95rem;

        }

        .modal textarea {

            width: 100%;

            min-height: 140px;

            padding: 14px;

            border:

                1px solid #d1d5db;

            border-radius: 12px;

            resize: vertical;

            font-size: 0.95rem;

            outline: none;

            transition:
                border-color .2s,
                box-shadow .2s;

        }

        .modal textarea:focus {

            border-color:
                #4B2E83;

            box-shadow:
                0 0 0 4px rgba(75,
                    46,
                    131,
                    0.15);

        }

        .modal-buttons {

            display: flex;

            justify-content: flex-end;

            gap: 12px;

            margin-top: 22px;

        }

        .modal-buttons button {

            padding:
                12px 20px;

            border-radius: 10px;

            font-weight: 600;

            cursor: pointer;

            transition:
                all .2s ease;

        }

        .modal-buttons button:first-child {

            background: white;

            border:
                1px solid #d1d5db;

            color: #374151;

        }

        .modal-buttons button:first-child:hover {

            background:
                #f3f4f6;

        }

        .modal-buttons button:last-child {

            background:
                #dc2626;

            border: none;

            color: white;

        }

        .modal-buttons button:last-child:hover {

            background:
                #b91c1c;

        }

        @keyframes fadeIn {

            from {

                opacity: 0;

            }

            to {

                opacity: 1;

            }

        }

        @keyframes slideUp {

            from {

                opacity: 0;

                transform:
                    translateY(18px);

            }

            to {

                opacity: 1;

                transform:
                    translateY(0);

            }

        }

        @media (max-width: 768px) {

            .job-card {
                display: grid;
                grid-template-rows: 1fr auto;
                height: 100%;
                gap: 1rem;
            }

            .job-card-actions {
                display: flex;
                gap: 0.5rem;
                justify-content: flex-start;
            }

            .job-card-actions a {
                display: inline-block;
            }

        }
    </style>
</head>

<body>
    <?php
    $activePage = "my_jobs";
    include "../includes/employer_nav.php";
    ?>
    <div class="max-w-6xl mx-auto px-6 py-10">

        <h1 class="text-2xl font-semibold mb-6">Jobs In Progress</h1>

        <?php if (empty($jobs)): ?>

            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-500">No jobs are currently in progress.</p>
            </div>

        <?php else: ?>

            <div class="space-y-4">

                <?php foreach ($jobs as $job): ?>

                    <div class="job-card bg-white p-6 rounded-lg shadow flex justify-between items-center" style="padding: 20px;">
                        <div>
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

                            <p class="mt-3 text-sm text-gray-700">
                                Freelancer: <strong><?= htmlspecialchars($job['student_name']) ?></strong>
                            </p>
                            <p class="text-sm mt-2">

                                <?php if ($job['status'] === 'in_progress'): ?>

                                    <span class="text-yellow-600 font-medium">In Progress</span>

                                <?php else: ?>

                                    <span class="text-gray-500 font-medium">Completed</span>

                                <?php endif; ?>

                            </p>


                        </div>

                        <div class="flex gap-3 job-card-actions">

                            <a href="view_job.php?job_id=<?= $job['id'] ?>"
                                class="px-4 py-2 bg-[#4B2E83] text-white rounded-lg text-sm hover:bg-[#5d3a9e]">
                                Job Details
                            </a>

                            <a href="../messages/messages.php?job_id=<?= $job['id'] ?>"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                                Message
                            </a>

                            <a href="#" onclick="event.preventDefault();openDisputeModal(<?= $job['id'] ?>);"
                                class="dispute-btn px-4 py-2 border border-red-400 text-red-600 rounded-lg text-sm hover:bg-red-50">
                                Dispute
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>
    <div
        id="disputeModal"
        class="modal">

        <div class="modal-content">

            <h3>

                Open Dispute

            </h3>

            <p>

                Explain the issue.

                StudentLancer admins may review evidence.

            </p>

            <form
                method="POST"
                action="create_dispute.php">

                <input
                    type="hidden"
                    name="job_id"
                    id="disputeJobId">

                <textarea

                    name="reason"

                    required

                    placeholder="
                    Explain the issue...
                    "

                    rows="5"></textarea>

                <div class="modal-buttons">

                    <button
                        type="button"
                        onclick="closeDisputeModal()">

                        Cancel

                    </button>

                    <button
                        type="submit">

                        Submit Dispute

                    </button>

                </div>

            </form>

        </div>

    </div>
    <script>
        function openDisputeModal(jobId) {

            document.getElementById("disputeModal").classList.add("show");

            document.getElementById("disputeJobId").value = jobId;

        }

        function closeDisputeModal() {

            document
                .getElementById(
                    "disputeModal"
                )
                .classList
                .remove(
                    "show"
                );

        }
    </script>
</body>

</html>