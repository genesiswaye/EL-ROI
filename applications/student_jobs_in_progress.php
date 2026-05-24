<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_guard.php";

if ($_SESSION['role'] !== 'student') {
    die("Unauthorized access");
}

$student_id = $_SESSION['user_id'];

/* FETCH JOBS IN PROGRESS */

$stmt = $pdo->prepare("
SELECT 
    applications.id,
    applications.status,
    applications.bid_amount,
    applications.created_at,

    jobs.id AS job_id,
    jobs.title,
    jobs.deadline,
    jobs.budget,

    users.id AS employer_id,
    users.full_name AS employer_name

    FROM applications

    JOIN jobs ON applications.job_id = jobs.id
    JOIN users ON jobs.created_by = users.id

    WHERE applications.student_id = ?
    AND applications.status IN ('accepted', 'in_progress')

    ORDER BY jobs.deadline ASC
");

$stmt->execute([$student_id]);

$jobs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Jobs In Progress</title>
    <link rel="stylesheet" href="../dist/output.css">
    <style>
        @media (max-width: 768px) {

            .job-card {
                display: grid;
                grid-template-rows: 1fr auto;
                height: 100%;
            }

            .job-card-actions {
                display: grid;
               grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
                gap: 0.5rem;
            }

            .job-card-actions a {
                display: block;
                text-align: center;
                padding: 0.5rem;
                border-radius: 0.5rem;
                font-size: 0.875rem;
            }

        }
    </style>
</head>

<body class="bg-[#F7F8FA] min-h-screen">

    <div class="max-w-5xl mx-auto py-10 px-6">

        <h1 class="text-2xl font-semibold mb-6">
            Jobs In Progress
        </h1>

        <?php if (empty($jobs)): ?>

            <div class="bg-white p-6 rounded-lg shadow text-gray-500">
                No jobs in progress yet.
            </div>

        <?php else: ?>

            <div class="space-y-4">

                <?php foreach ($jobs as $job): ?>

                    <div class="job-card-content bg-white p-6 rounded-lg shadow">

                        <!-- HEADER -->
                        <div class="flex justify-between items-start">

                            <div>
                                <h2 class="font-semibold text-lg">
                                    <?= htmlspecialchars($job['title']) ?>
                                </h2>

                                <p class="text-sm text-gray-500">
                                    Employer: <?= htmlspecialchars($job['employer_name']) ?>
                                </p>
                            </div>

                            <span class="text-sm font-medium
                            <?= $job['status'] === 'accepted' ? 'text-green-600' : 'text-blue-600' ?>">
                                <?= ucfirst(str_replace('_', ' ', $job['status'])) ?>
                            </span>

                        </div>

                        <!-- META -->
                        <div class="mt-4 flex justify-between items-center">

                            <div class="text-sm text-gray-500">
                                Deadline: <?= date("M d, Y", strtotime($job['deadline'])) ?>
                            </div>

                            <div class="text-[#4B2E83] font-medium">
                                ₦<?= number_format($job['bid_amount']) ?>
                            </div>

                        </div>

                        <!-- ACTIONS -->
                        <div class="job-card-actions mt-5 flex gap-3">

                            <!-- VIEW JOB -->
                            <a href="../jobs/view_job.php?job_id=<?= $job['job_id'] ?>"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">
                                View Job
                            </a>

                            <!-- MESSAGE -->
                            <a href="../messages/messages.php?user_id=<?= $job['employer_id'] ?>&job_id=<?= $job['job_id'] ?>"
                                class="px-4 py-2 border border-blue-400 text-blue-600 rounded-lg text-sm hover:bg-blue-50">
                                Message
                            </a>

                            <!-- ONLY SHOW SUBMIT IF IN PROGRESS -->
                            <?php if ($job['status'] === 'in_progress'): ?>
                                <a href="submit_work.php?job_id=<?= $job['job_id'] ?>"
                                    class="px-4 py-2 bg-[#4B2E83] text-white rounded-lg text-sm hover:bg-[#5d3a9e]">
                                    Submit Work
                                </a>
                            <?php endif; ?>

                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>
        <?php if ($job['status'] === 'accepted'): ?>
            <p class="text-xs text-gray-500 mt-2">
                Start by discussing details with the employer before submitting work.
            </p>
        <?php endif; ?>

    </div>

</body>

</html>