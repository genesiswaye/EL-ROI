<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT *
    FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$stmt->execute([$user_id]);

$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    UPDATE notifications
    SET is_read = 1
    WHERE user_id = ?
    AND is_read = 0
");

$stmt->execute([$_SESSION['user_id']]);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Notifications</title>
    <link href="../dist/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
     <?php
    $activePage = "notifications";
    include "../includes/employer_nav.php";
    ?>

<div class="max-w-4xl mx-auto p-6">

    <h1 class="text-2xl font-bold mb-6">
        Notifications
    </h1>

    <div class="space-y-4">

        <?php if (empty($notifications)): ?>

            <div class="bg-white rounded-xl shadow p-6 text-gray-500">
                No notifications yet.
            </div>

        <?php else: ?>

            <?php foreach ($notifications as $n): ?>

                <a
                    href="<?= htmlspecialchars($n['link'] ?? '#') ?>"
                    class="block bg-white rounded-xl shadow p-5 hover:bg-gray-50 transition"
                >

                    <div class="flex justify-between items-start">

                        <div class="p-4">

                            <div class="flex items-center gap-2 mb-1">

                                <?php if (!$n['is_read']): ?>
                                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                <?php endif; ?>

                                <h2 class="font-semibold">
                                    <?= htmlspecialchars($n['title']) ?>
                                </h2>

                            </div>

                            <p class="text-sm text-gray-600 ">
                                <?= htmlspecialchars($n['message']) ?>
                            </p>

                        </div>

                        <div class="text-xs text-gray-400 px-2 py-2">
                            <?= date("M d, Y H:i", strtotime($n['created_at'])) ?>
                        </div>

                    </div>

                </a>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>

</body>
</html>