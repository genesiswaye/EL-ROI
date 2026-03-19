<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_guard.php";

$user_id = $_SESSION['user_id'];

/* GET JOB ID */

if (!isset($_GET['job_id'])) {
    die("Job not specified");
}

$job_id = $_GET['job_id'];

/* VERIFY USER OWNS THIS JOB */

$stmt = $pdo->prepare("
    SELECT id, title
    FROM jobs
    WHERE id = ? AND created_by = ?
    ");

$stmt->execute([$job_id, $user_id]);

$job = $stmt->fetch();

if (!$job) {
    die("Unauthorized job access");
}

/* FETCH PROPOSALS */

$stmt = $pdo->prepare("
SELECT 
applications.id,
applications.student_id,
applications.proposal_text,
applications.bid_amount,
applications.status,
applications.created_at,

users.full_name,
users.email,

student_profiles.department,
student_profiles.level

FROM applications

JOIN users 
ON applications.student_id = users.id

LEFT JOIN student_profiles 
ON users.id = student_profiles.user_id

WHERE applications.job_id = ?
ORDER BY applications.created_at DESC
");
$stmt->execute([$job_id]);

$applications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications</title>

    <link rel="stylesheet" href="../dist/output.css">

</head>

<body class="bg-[#F7F8FA] min-h-screen">

    <?php
    $activePage = "my_jobs";
    include "../includes/employer_nav.php";
    ?>

    <div class="max-w-6xl mx-auto py-10 px-6">

        <h1 class="text-2xl font-semibold mb-6">
            Applications for: <?= htmlspecialchars($job['title']) ?>
        </h1>

        <?php if (empty($applications)): ?>

            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-500 p-4">No students have applied yet.</p>
            </div>

        <?php else: ?>

            <div class="space-y-4">

                <?php foreach ($applications as $app): ?>

                    <div class="bg-white p-6 rounded-lg shadow">

                        <div class="flex justify-between items-start">

                            <div>

                                <h2 class="font-semibold text-lg">
                                    <?= htmlspecialchars($app['full_name']) ?>
                                </h2>

                                <p class="text-sm text-gray-500">
                                    <?= htmlspecialchars($app['department']) ?> — Level <?= htmlspecialchars($app['level']) ?>
                                </p>

                                <p class="text-sm text-gray-400 mt-1">
                                    Applied on <?= date("M d, Y", strtotime($app['created_at'])) ?>
                                </p>

                            </div>

                            <?php
                            $statusColor = match ($app['status']) {
                                'accepted' => 'text-green-600',
                                'rejected' => 'text-red-500',
                                default => 'text-yellow-600'
                            };
                            ?>

                            <span class="text-sm font-medium <?= $statusColor ?>">
                                <?= ucfirst($app['status']) ?>
                            </span>

                        </div>

                        <!-- BID -->

                        <!-- <p class="mt-3 font-medium text-[#4B2E83]"> -->
                            <!-- Bid: ₦<?= number_format($app['bid_amount']) ?> -->
                        <!-- </p> -->

                        <!-- ACTION BUTTONS -->

                        <div class="mt-5 flex gap-3">

                            <a href="../profile/student_profile.php?user_id=<?= $app['student_id'] ?>&job_id=<?= $job_id ?>"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">

                                View Profile

                            </a>

                            <a
                                href="view_proposal.php?proposal_id=<?= $app['id'] ?>"
                                class="px-4 py-2 bg-[#4B2E83] text-white rounded-lg text-sm hover:bg-[#5d3a9e]">

                                View Proposal

                            </a>

                        </div>

                    </div>
                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</body>

</html>