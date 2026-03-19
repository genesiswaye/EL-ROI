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

            <div class="space-y-4" >

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

                            <p class="text-sm mt-2">

                                <?php if ($job['status'] === 'open'): ?>

                                    <span class="text-green-600 font-medium">Open</span>

                                <?php else: ?>

                                    <span class="text-gray-500 font-medium"><?= htmlspecialchars($job['status']) ?></span>

                                <?php endif; ?>

                            </p>

                        </div>

                        <div class="flex gap-3">

                            <a href="view_applications.php?job_id=<?= $job['id'] ?>"
                                class="px-4 py-2 bg-[#4B2E83] text-white rounded-lg text-sm hover:bg-[#5d3a9e]">
                                Applications
                            </a>

                            <a href="edit_job.php?job_id=<?= $job['id'] ?>"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                                Edit
                            </a>

                            <a href="delete_job.php?job_id=<?= $job['id'] ?>"
                                class="px-4 py-2 border border-red-400 text-red-600 rounded-lg text-sm hover:bg-red-50">
                                Delete
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</body>

</html>