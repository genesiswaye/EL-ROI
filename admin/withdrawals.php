<?php
session_start();

require_once "../config/database.php";
require "helper.php";

requireRole([
    'support_admin',
    'super_admin'
]);
/* =========================
   AUTH CHECK (CRITICAL)
========================= */



$allowedStatuses = ['pending', 'paid', 'failed'];
$status = $_GET['status'] ?? 'pending';

if (!in_array($status, $allowedStatuses, true)) {
    $status = 'pending';
}

/* =========================
   FETCH PENDING WITHDRAWALS
========================= */

$stmt = $pdo->prepare("
    SELECT 
        w.id,
        w.user_id,
        w.amount,
        w.reference,
        w.status,
        w.created_at,
        w.processed_at,
        w.bank_name,
        w.account_number,
        w.account_name,
        u.full_name,
        u.email
    FROM withdrawals w
    JOIN users u ON w.user_id = u.id
    WHERE w.status = 'pending'
    ORDER BY w.created_at DESC
");
$stmt->execute();
$withdrawals = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   CSRF TOKEN
========================= */

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Withdrawals</title>
    <link href="../dist/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <div class="max-w-6xl mx-auto p-6">

        <h1 class="text-2xl font-bold mb-6">Pending Withdrawals</h1>

        <a href="?status=pending"
                    class="<?= $status === 'pending' ? 'font-bold underline' : '' ?>">
                    Pending
                </a>

                <a href="?status=paid"
                    class="<?= $status === 'paid' ? 'font-bold underline' : '' ?>">
                    Paid
                </a>

                <a href="?status=failed"
                    class="<?= $status === 'failed' ? 'font-bold underline' : '' ?>">
                    Failed
                </a>


        <?php if (empty($withdrawals)): ?>
            <div class="bg-white p-6 rounded shadow text-gray-500">
                No pending withdrawals.
            </div>
        <?php else: ?>

            <div class="flex flex-col gap-4 mb-6">

                
                <?php foreach ($withdrawals as $w): ?>

                    <div class="bg-white p-4 rounded shadow flex justify-between items-center">

                        <div>
                            <div class="flex gap-4 mb-6">



                            </div>
                            <p class="font-semibold">
                                <?= htmlspecialchars($w['full_name']) ?>
                            </p>

                            <p class="text-sm text-gray-500">
                                <?= htmlspecialchars($w['email']) ?>
                            </p>

                            <p class="text-sm mt-1">
                                Amount: ₦<?= number_format($w['amount'], 2) ?>
                            </p>

                            <p class="text-sm">
                                Bank: <?= htmlspecialchars($w['bank_name']) ?>
                            </p>

                            <p class="text-sm">
                                Account Number: <?= htmlspecialchars($w['account_number']) ?>
                            </p>

                            <p class="text-sm">
                                Account Name: <?= htmlspecialchars($w['account_name']) ?>
                            </p>

                            <p class="text-xs text-gray-400">
                                Ref: <?= htmlspecialchars($w['reference']) ?>
                            </p>

                            <p class="text-xs text-gray-500">
                                Status: <?= ucfirst($w['status']) ?>
                            </p>

                            <?php if (!empty($w['processed_at'])): ?>
                                <p class="text-xs text-gray-400">
                                    Processed: <?= date("M d, Y H:i", strtotime($w['processed_at'])) ?>
                                </p>
                            <?php endif; ?>

                            <p class="text-xs text-gray-400">
                                <?= date("M d, Y H:i", strtotime($w['created_at'])) ?>
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <?php if ($w['status'] === 'pending'): ?>
                                <form method="POST" action="process_withdrawal.php">
                                    <input type="hidden" name="withdrawal_id" value="<?= $w['id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                                    <button class="bg-green-600 text-white px-4 py-2 rounded">
                                        Approve
                                    </button>
                                </form>

                                <!-- REJECT -->
                                <form method="POST" action="process_withdrawal.php">
                                    <input type="hidden" name="withdrawal_id" value="<?= $w['id'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                                    <button style="background-color: red;" class=" text-white px-4 py-2 rounded">
                                        Reject
                                    </button>
                                </form>
                            <?php endif; ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</body>

</html>