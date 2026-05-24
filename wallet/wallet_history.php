<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];

$filter = $_GET['filter'] ?? 'all';

$where = "WHERE transactions.user_id = ?";
$params = [$user_id];

if ($filter === 'deposit') {
    $where .= " AND transactions.type = 'deposit'";
}

if ($filter === 'withdrawal') {
    $where .= " AND transactions.type = 'withdrawal'";
}

if ($filter === 'payments') {

    $where .= "
        AND transactions.type IN (
            'escrow_hold',
            'escrow_release',
            'payment_received'
        )
    ";
}

/* FETCH TRANSACTIONS */

$stmt = $pdo->prepare("
    SELECT 
        transactions.type,
        transactions.amount,
        transactions.reference,
        transactions.status,
        transactions.description,
        transactions.created_at,
        transactions.job_id,
        jobs.title AS job_title

    FROM transactions

    LEFT JOIN jobs 
        ON transactions.job_id = jobs.id

    $where

    ORDER BY transactions.created_at DESC
");

$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Transaction History</title>
    <link href="../dist/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <div class="max-w-5xl mx-auto p-6">

        <h1 class="text-2xl font-bold mb-6">Transaction History</h1>
        <div class="flex gap-3 mb-6">

    <a
        href="?filter=all"
        class="px-4 py-2 rounded-lg border <?= $filter === 'all' ? 'bg-black text-white' : 'bg-white' ?>"
    >
        All
    </a>

    <a
        href="?filter=deposit"
        class="px-4 py-2 rounded-lg border <?= $filter === 'deposit' ? 'bg-green-600 text-white' : 'bg-white' ?>"
    >
        Deposits
    </a>

    <a
        href="?filter=withdrawal"
        class="px-4 py-2 rounded-lg border <?= $filter === 'withdrawal' ? 'bg-red-500 text-white' : 'bg-white' ?>"
    >
        Withdrawals
    </a>

    <a
    href="?filter=payments"
    class="px-4 py-2 rounded-lg border <?= $filter === 'payments' ? 'bg-yellow-500 text-white' : 'bg-white' ?>"
>
    Payments
</a>

</div>

        <?php if (empty($transactions)): ?>

            <div class="bg-white p-6 rounded shadow text-gray-500">
                No transactions yet.
            </div>

        <?php else: ?>

            <div class="space-y-4">

                <?php foreach ($transactions as $tx): ?>

                    <div class="bg-white p-4 rounded shadow flex justify-between items-center">

                        <div>
                            <?php
                            $typeLabels = [
                                'deposit' => 'Wallet Funding',
                                'escrow_hold' => 'Moved to Escrow',
                                'escrow_release' => 'Payment Released'
                            ];

                            $label = $typeLabels[$tx['type']] ?? ucfirst($tx['type']);
                            ?>

                            <?php if (!empty($tx['job_title'])): ?>
                                <p class="text-xs text-gray-500">
                                    Job: <?= htmlspecialchars($tx['job_title']) ?>
                                </p>
                            <?php endif; ?>
                            <p class="font-semibold">
                                <?= htmlspecialchars($label) ?>
                            </p>

                            <p class="text-sm text-gray-500">
                                <?= htmlspecialchars($tx['description']) ?>
                            </p>

                            <?php if (!empty($tx['reference'])): ?>
                                <p class="text-xs text-gray-400">
                                    Ref: <?= htmlspecialchars($tx['reference']) ?>
                                </p>
                            <?php endif; ?>

                            <p class="text-xs text-gray-400">
                                <?= date("M d, Y H:i", strtotime($tx['created_at'])) ?>
                            </p>
                        </div>

                        <div class="text-right">

                            <p class="font-bold
                            <?= in_array($tx['type'], ['deposit', 'escrow_release']) ? 'text-green-600' : 'text-red-500' ?>">

                                <?= in_array($tx['type'], ['deposit', 'escrow_release']) ? '+' : '-' ?>
                                ₦<?= number_format($tx['amount'], 2) ?>
                            </p>

                            <?php
                            $statusColors = [
                                'success' => 'text-green-600',
                                'pending' => 'text-yellow-500',
                                'failed' => 'text-red-500'
                            ];
                            ?>

                            <p class="text-xs <?= $statusColors[$tx['status']] ?? 'text-gray-500' ?>">
                                <?= ucfirst($tx['status']) ?>
                            </p>
                            <?php if ($tx['status'] === 'failed'): ?>
                                <p class="text-xs text-red-500 mt-1">
                                    <?= htmlspecialchars($tx['description']) ?>
                                </p>
                            <?php endif; ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</body>

</html>