<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_guard.php";

if ($_SESSION['role'] !== 'student') {
    die("Unauthorized access");
}

$student_id = $_SESSION['user_id'];

/* FETCH APPLICATIONS */

$stmt = $pdo->prepare("
SELECT 
    applications.id,
    applications.bid_amount,
    applications.status,
    applications.created_at,

    jobs.id AS job_id,
    jobs.title,
    jobs.budget,

    users.full_name AS employer_name

FROM applications

JOIN jobs ON applications.job_id = jobs.id
JOIN users ON jobs.created_by = users.id

WHERE applications.student_id = ?

ORDER BY applications.created_at DESC
");

$stmt->execute([$student_id]);

$applications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Applications</title>
    <link rel="stylesheet" href="../dist/output.css">
</head>

<body class="bg-[#F7F8FA] min-h-screen">
    <?php
    $activePage = "my_applications";
    include "../includes/employer_nav.php";
    ?>

    <div class="max-w-6xl mx-auto py-10 px-6">

        <h1 class="text-2xl font-semibold mb-6">My Applications</h1>

        <?php if (empty($applications)): ?>

            <div class="bg-white p-6 rounded-lg shadow text-gray-500">
                You haven’t applied to any jobs yet.
            </div>

        <?php else: ?>

            <div class="space-y-4">

                <?php foreach ($applications as $app): ?>

                    <div class="bg-white p-6 rounded-lg shadow">

                        <div class="flex justify-between items-start">

                            <div>
                                <h2 class="font-semibold text-lg">
                                    <?= htmlspecialchars($app['title']) ?>
                                </h2>

                                <p class="text-sm text-gray-500">
                                    Employer: <?= htmlspecialchars($app['employer_name']) ?>
                                </p>

                                <p class="text-sm text-gray-400 mt-1">
                                    Applied on <?= date("M d, Y", strtotime($app['created_at'])) ?>
                                </p>
                            </div>

                            <span class="text-sm font-medium
                            <?=
                            $app['status'] === 'accepted' ? 'text-green-600' : ($app['status'] === 'rejected' ? 'text-red-500' : ($app['status'] === 'in_progress' ? 'text-blue-600' : ($app['status'] === 'completed' ? 'text-gray-600' :
                                'text-yellow-600')))
                            ?>">
                                <?= ucfirst(str_replace('_', ' ', $app['status'])) ?>
                            </span>

                        </div>

                        <div class="mt-4 flex justify-between items-center">

                            <p class="text-[#4B2E83] font-medium">
                                Your Bid: ₦<?= number_format($app['bid_amount']) ?>
                            </p>

                            <div class="flex gap-2">

                                <!-- VIEW JOB -->
                                <a href="../jobs/view_job.php?job_id=<?= $app['job_id'] ?>"
                                    class="px-4 py-2 bg-[#4B2E83] text-white rounded-lg text-sm hover:bg-[#5d3a9e]">
                                    View Job
                                </a>

                                <!-- WITHDRAW -->
                                <?php if ($app['status'] === 'pending'): ?>
                                    <button
                                        onclick="openModal(<?= $app['id'] ?>)"
                                        class="px-4 py-2 border border-red-400 text-red-600 rounded-lg text-sm hover:bg-red-50">
                                        Withdraw
                                    </button>
                                <?php endif; ?>

                                <!-- CONTINUE WORK change to message later -->
                                <?php if ($app['status'] === 'accepted'): ?>
                                    <a href="submit_work.php?job_id=<?= $app['job_id'] ?>"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                                        Continue Work
                                    </a>
                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>
    <!-- Confirm Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

        <div class="bg-white rounded-lg p-6 w-full max-w-sm">

            <h2 class="text-lg font-semibold mb-2">Withdraw Application</h2>

            <p class="text-sm text-gray-600 mb-6">
                Are you sure you want to withdraw this application?
            </p>

            <div class="flex justify-end gap-3">

                <button onclick="closeModal()"
                    class="px-4 py-2 border rounded-lg text-sm">
                    Cancel
                </button>

                <a id="confirmDeleteBtn"
                    href="#"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">
                    Yes, Withdraw
                </a>

            </div>

        </div>

    </div>
    <script>
            function openModal(appId) {

                const modal = document.getElementById("confirmModal");
                const btn = document.getElementById("confirmDeleteBtn");

                btn.href = "withdraw_application.php?id=" + appId;

                modal.classList.remove("hidden");
                modal.classList.add("flex");
            }

            function closeModal() {
                const modal = document.getElementById("confirmModal");
                modal.classList.remove("flex");
                modal.classList.add("hidden");
            }
    </script>

</body>

</html>