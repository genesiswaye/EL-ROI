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

    $stmt = $pdo->prepare("
        SELECT 
            jobs.id,
            jobs.title,
            jobs.budget,
            jobs.deadline,
            jobs.category,
            users.full_name AS student_name,
            applications.student_id,
            work_submissions.id AS submission_id
        FROM jobs
        JOIN applications
            ON jobs.id = applications.job_id
        JOIN users
            ON applications.student_id = users.id
        LEFT JOIN work_submissions
        ON applications.id = work_submissions.application_id
        WHERE jobs.created_by = ?
        AND jobs.status = 'completed'
        AND applications.status = 'completed'
        ORDER BY jobs.deadline DESC
    ");

    $stmt->execute([$user_id]);

    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {

    die("Error loading completed jobs.");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../dist/output.css">
    <link rel="stylesheet" href="my_jobs.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body class="bg-[#F7F8FA] min-h-screen">
    <?php
    $activePage = "my_jobs";
    include "../includes/employer_nav.php";
    ?>

    <div class="max-w-6xl mx-auto px-6 py-10">

        <h1 class="text-2xl font-semibold mb-6">Completed Jobs</h1>

        <?php if (empty($jobs)): ?>

            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-500">No jobs have been completed yet.</p>
            </div>

        <?php else: ?>

            <div class="space-y-4">

                <?php foreach ($jobs as $job): ?>

                    <div class="bg-white p-6 rounded-lg shadow flex flex-col md:flex-row md:justify-between md:items-center">

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

                        </div>

                        <div class="flex flex-wrap gap-3 mt-4">

                            <a href="../jobs/view_job.php?job_id=<?= (int)$job['id'] ?>"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                View Job
                            </a>

                            <?php if ($job['submission_id']): ?>

                                <a
                                    href="../applications/view_submissions.php?submission_id=<?= (int)$job['submission_id'] ?>&return=<?= urlencode('/EL-ROI/messages/messages.php') ?>"
                                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                                    View Submission
                                </a>

                            <?php endif; ?>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>
</body>

</html>