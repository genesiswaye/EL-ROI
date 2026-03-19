<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

if (!isset($_GET['job_id'])) {
    die("Job not specified");
}


$job_id = $_GET['job_id'];

/* FETCH JOB */

$stmt = $pdo->prepare("
SELECT 
jobs.id,
jobs.title,
jobs.description,
jobs.requirements,
jobs.milestones,
jobs.location_type,
jobs.budget,
jobs.deadline,
jobs.category,
jobs.status,
jobs.created_at,

users.full_name,
users.role

FROM jobs
JOIN users ON jobs.created_by = users.id

WHERE jobs.id = ?
");

$stmt->execute([$job_id]);

$job = $stmt->fetch();

if (!$job) {
    die("Job not found");
}
$duration_weeks = null;

if (!empty($job['deadline']) && !empty($job['created_at'])) {

    $start = new DateTime($job['created_at']);
    $end = new DateTime($job['deadline']);

    $days = $start->diff($end)->days;

    $duration_weeks = ceil($days / 7);
}

$milestones = [];
$requirements = [];

if (!empty($job['requirements'])) {
    $requirements = explode("\n", $job['requirements']);
}
if (!empty($job['milestones'])) {
    $milestones = explode("\n", $job['milestones']);
}
/* FETCH JOB SKILLS */

$stmt = $pdo->prepare("
SELECT skills.name
FROM job_skills
JOIN skills ON job_skills.skill_id = skills.id
WHERE job_skills.job_id = ?
");

$stmt->execute([$job_id]);

$skills = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dist/output.css">
    <!-- <link rel="stylesheet" href="browse_jobs.css"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <title>Document</title>
</head>

<body class="bg-red-900 min-h-screen p-8">
    <div class="max-w-4xl mx-auto space-y-6">

        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Project Requirements</h2>

            <ul class="space-y-3">

                <?php foreach ($requirements as $req): ?>
                    <?php $req = trim($req);
                    if ($req === '') continue; ?>

                    <li class="flex items-start gap-3">

                        <!-- SVG check icon -->
                        <svg class="w-3.5 h-3.5 text-green-500 mt-1 shrink-0"
                            style="width: 1.5rem; height: 1.5rem;"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4"></path>
                            <circle cx="12" cy="12" r="10"></circle>
                        </svg>

                        <span class="text-gray-700">
                            <?= htmlspecialchars($req) ?>
                        </span>

                    </li>

                <?php endforeach; ?>

            </ul>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow mt-6">

        <h2 class="text-lg font-semibold mb-6">Milestones / Deliverables</h2>

        <div class="space-y-6">

            <?php
            $index = 1;

            foreach ($milestones as $milestone):

                $milestone = trim($milestone);
                if ($milestone === '') continue;

                $parts = explode("|", $milestone, 2);

                $title = trim($parts[0]);
                $desc = isset($parts[1]) ? trim($parts[1]) : '';
            ?>

                <div class="flex gap-4">

                    <div class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 text-gray-700 font-medium">
                        <?= $index ?>
                    </div>

                    <div>

                        <h3 class="font-semibold">
                            <?= htmlspecialchars($title) ?>
                        </h3>

                        <p class="text-gray-600 text-sm">
                            <?= htmlspecialchars($desc) ?>
                        </p>

                    </div>

                </div>

            <?php
                $index++;
            endforeach;
            ?>

        </div>

    </div>
    <p class="text-sm text-gray-500">
        Location: <?= htmlspecialchars($job['location_type']) ?>
    </p>
    <p class="text-sm text-gray-500">
        Estimated Duration:
        <strong><?= $duration_weeks ?> week<?= $duration_weeks > 1 ? 's' : '' ?></strong>
    </p>

</body>

</html>