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

                <div class="bg-white p-6 rounded-lg shadow flex justify-between items-center" style="padding: 20px;">
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
                
                   <div class="flex gap-3">

                        <a href="view_job.php?job_id=<?= $job['id'] ?>"
                            class="px-4 py-2 bg-[#4B2E83] text-white rounded-lg text-sm hover:bg-[#5d3a9e]">
                            Job Details
                        </a>

                        <a href="edit_job.php?job_id=<?= $job['id'] ?>"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                            Message
                        </a>

                        <a href="delete_job.php?job_id=<?= $job['id'] ?>"
                            class="px-4 py-2 border border-red-400 text-red-600 rounded-lg text-sm hover:bg-red-50">
                            Dispute
                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <?php endif; ?>

    </div>
</body>
</html>