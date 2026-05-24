<?php
session_start();

require_once "../config/database.php";

require "helper.php";

requireRole([
    'support_admin',
    'super_admin'
]);

/* =========================
   FILTERS
========================= */

$severity = $_GET['severity'] ?? 'all';
$status = $_GET['status'] ?? 'active';

$where = [];
$params = [];

if ($severity !== 'all') {
    $where[] = "fraud_flags.severity = ?";
    $params[] = $severity;
}

if ($status !== 'all') {
    $where[] = "fraud_flags.status = ?";
    $params[] = $status;
}

$whereSQL = '';

if (!empty($where)) {
    $whereSQL = 'WHERE ' . implode(' AND ', $where);
}

/* =========================
   FETCH FLAGS
========================= */

$stmt = $pdo->prepare("
    SELECT
        fraud_flags.*,

        users.full_name,
        users.email,

        wallets.balance,
        wallets.withdrawal_hold,
        wallets.held_balance

    FROM fraud_flags

    JOIN users
        ON fraud_flags.user_id = users.id

    LEFT JOIN wallets
        ON fraud_flags.user_id = wallets.user_id

    $whereSQL

    ORDER BY
        FIELD(fraud_flags.severity, 'high', 'medium', 'low'),
        fraud_flags.created_at DESC
");

$stmt->execute($params);

$flags = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   STATS
========================= */

$totalFlags = count($flags);

$highCount = count(array_filter(
    $flags,
    fn($f) => $f['severity'] === 'high'
));

$activeCount = count(array_filter(
    $flags,
    fn($f) => $f['status'] === 'active'
));
?>

<!DOCTYPE html>
<html>

<head>
    <title>Fraud Monitoring</title>
    <link href="../dist/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen">

<div class="max-w-7xl mx-auto p-6">

    <!-- HEADER -->

    <div class="flex justify-between items-center mb-8">

        <div>
            <h1 class="text-3xl font-bold">
                Fraud Monitoring
            </h1>

            <p class="text-gray-500 mt-1">
                Suspicious account activity tracking
            </p>
        </div>

        <div class="flex gap-4">

            <div class="bg-white rounded-xl shadow px-5 py-3">
                <p class="text-xs text-gray-500">
                    Total Flags
                </p>

                <p class="text-2xl font-bold">
                    <?= $totalFlags ?>
                </p>
            </div>

            <div class="bg-red-100 rounded-xl shadow px-5 py-3">
                <p class="text-xs text-red-700">
                    High Severity
                </p>

                <p class="text-2xl font-bold text-red-700">
                    <?= $highCount ?>
                </p>
            </div>

            <div class="bg-yellow-100 rounded-xl shadow px-5 py-3">
                <p class="text-xs text-yellow-700">
                    Active Cases
                </p>

                <p class="text-2xl font-bold text-yellow-700">
                    <?= $activeCount ?>
                </p>
            </div>

        </div>

    </div>

    <!-- FILTERS -->

    <div class="bg-white rounded-xl shadow p-4 mb-6">

        <form class="flex gap-4">

            <select
                name="severity"
                class="border rounded px-4 py-2"
            >
                <option value="all">All Severity</option>
                <option value="high" <?= $severity === 'high' ? 'selected' : '' ?>>
                    High
                </option>

                <option value="medium" <?= $severity === 'medium' ? 'selected' : '' ?>>
                    Medium
                </option>

                <option value="low" <?= $severity === 'low' ? 'selected' : '' ?>>
                    Low
                </option>
            </select>

            <select
                name="status"
                class="border rounded px-4 py-2"
            >
                <option value="all">All Status</option>

                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>
                    Active
                </option>

                <option value="resolved" <?= $status === 'resolved' ? 'selected' : '' ?>>
                    Resolved
                </option>
            </select>

            <button class="bg-black text-white px-5 py-2 rounded-lg">
                Filter
            </button>

        </form>

    </div>

    <!-- FLAGS -->

    <div class="space-y-5">

        <?php if (empty($flags)): ?>

            <div class="bg-white rounded-xl shadow p-10 text-center text-gray-500">
                No suspicious activity detected.
            </div>

        <?php else: ?>

            <?php foreach ($flags as $flag): ?>

                <?php
                    $severityColors = [
                        'low' => 'bg-blue-100 text-blue-700',
                        'medium' => 'bg-yellow-100 text-yellow-700',
                        'high' => 'bg-red-100 text-red-700'
                    ];

                    $statusColors = [
                        'active' => 'bg-red-100 text-red-700',
                        'resolved' => 'bg-green-100 text-green-700'
                    ];
                ?>

                <div class="bg-white rounded-2xl shadow p-6">

                    <div class="flex justify-between items-start">

                        <!-- LEFT -->

                        <div class="flex-1">

                            <div class="flex items-center gap-3 mb-3">

                                <h2 class="text-xl font-semibold">
                                    <?= htmlspecialchars($flag['full_name']) ?>
                                </h2>

                                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $severityColors[$flag['severity']] ?>">
                                    <?= strtoupper($flag['severity']) ?>
                                </span>

                                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusColors[$flag['status']] ?>">
                                    <?= strtoupper($flag['status']) ?>
                                </span>

                            </div>

                            <p class="text-gray-600 mb-4">
                                <?= htmlspecialchars($flag['reason']) ?>
                            </p>

                            <div class="grid grid-cols-3 gap-4">

                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500">
                                        Wallet Balance
                                    </p>

                                    <p class="font-bold">
                                        ₦<?= number_format($flag['balance'] ?? 0, 2) ?>
                                    </p>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500">
                                        Withdrawal Hold
                                    </p>

                                    <p class="font-bold">
                                        ₦<?= number_format($flag['withdrawal_hold'] ?? 0, 2) ?>
                                    </p>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500">
                                        Escrow Hold
                                    </p>

                                    <p class="font-bold">
                                        ₦<?= number_format($flag['held_balance'] ?? 0, 2) ?>
                                    </p>
                                </div>

                            </div>

                            <div class="mt-4 text-sm text-gray-500">

                                <p>
                                    Email:
                                    <?= htmlspecialchars($flag['email']) ?>
                                </p>

                                <p class="mt-1">
                                    Detected:
                                    <?= date("M d, Y H:i", strtotime($flag['created_at'])) ?>
                                </p>

                            </div>

                        </div>

                        <!-- RIGHT -->

                        <div class="ml-6">

                            <?php if ($flag['status'] === 'active'): ?>

                                <form
                                    method="POST"
                                    action="resolve_flag.php"
                                >
                                    <input
                                        type="hidden"
                                        name="flag_id"
                                        value="<?= $flag['id'] ?>"
                                    >

                                    <button class="bg-black text-white px-4 py-2 rounded-lg">
                                        Mark Resolved
                                    </button>
                                </form>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>

</body>
</html>