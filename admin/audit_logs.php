<?php
session_start();

require_once "../config/database.php";

require "helper.php";

requireRole([
    'support_admin',
    'super_admin'
]);

$stmt = $pdo->query("
    SELECT
        audit_logs.*,
        users.full_name
    FROM audit_logs

    LEFT JOIN users
        ON audit_logs.user_id = users.id

    ORDER BY audit_logs.created_at DESC
");

$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Audit Logs</title>
    <link href="../dist/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

<div class="max-w-7xl mx-auto p-6">

    <h1 class="text-2xl font-bold mb-6">
        Audit Logs
    </h1>

    <div class="space-y-4">

        <?php foreach ($logs as $log): ?>

            <div class="bg-white p-4 rounded shadow">

                <div class="flex justify-between items-start">

                    <div>

                        <p class="font-semibold">
                            <?= htmlspecialchars($log['action']) ?>
                        </p>

                        <p class="text-sm text-gray-600">
                            <?= htmlspecialchars($log['description']) ?>
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            User:
                            <?= htmlspecialchars($log['full_name'] ?? 'System') ?>
                        </p>

                        <p class="text-xs text-gray-500">
                            Target:
                            <?= htmlspecialchars($log['target_type'] ?? '-') ?>
                            #<?= htmlspecialchars($log['target_id'] ?? '-') ?>
                        </p>

                        <p class="text-xs text-gray-400">
                            IP:
                            <?= htmlspecialchars($log['ip_address'] ?? 'Unknown') ?>
                        </p>

                    </div>

                    <div class="text-xs text-gray-400">
                        <?= date("M d, Y H:i", strtotime($log['created_at'])) ?>
                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

</body>
</html>