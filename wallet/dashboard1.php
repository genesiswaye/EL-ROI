<?php
session_start();

require_once "../config/database.php";
require_once "wallet_logic.php";
require_once "transaction.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];

/* =========================
   WALLET DATA
========================= */

$pending = getPendingEarnings($pdo, $user_id);

$wallet = getWallet($pdo, $user_id);

$balance = (float)($wallet['balance'] ?? 0);
$held = (float)($wallet['held_balance'] ?? 0);
$withdrawal_hold = (float)($wallet['withdrawal_hold'] ?? 0);

/* =========================
   TOTAL DEPOSITED
========================= */

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(amount), 0)
    FROM transactions
    WHERE user_id = ?
    AND type = 'deposit'
    AND status = 'success'
");

$stmt->execute([$user_id]);

$totalDeposited = (float)$stmt->fetchColumn();

/* =========================
   TOTAL EARNED
========================= */

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(amount), 0)
    FROM transactions
    WHERE user_id = ?
    AND type = 'payment_received'
    AND status = 'success'
");

$stmt->execute([$user_id]);

$totalEarned = (float)$stmt->fetchColumn();

/* =========================
   TOTAL WITHDRAWN
========================= */

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(amount), 0)
    FROM transactions
    WHERE user_id = ?
    AND type = 'withdrawal'
    AND status = 'success'
");

$stmt->execute([$user_id]);

$totalWithdrawn = (float)$stmt->fetchColumn();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Wallet</title>
    <link href="../dist/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen">

    <?php
    $activePage = "wallet";
    include "../includes/employer_nav.php";
    ?>

    <div class="max-w-6xl mx-auto p-6">

        <!-- HEADER -->

        <div class="flex justify-between items-center mb-8">

            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    My Wallet
                </h1>

                <p class="text-gray-500 mt-1">
                    Manage deposits, earnings, escrow and withdrawals
                </p>
            </div>

            <div class="flex gap-3">

                <a href="wallet_history.php"
                    class="bg-white border px-4 py-2 rounded-lg shadow-sm hover:bg-gray-50">

                    Transaction History
                </a>

                <a href="withdraw.php"
                    class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600">

                    Withdraw Funds
                </a>

            </div>

        </div>

        <!-- FUND WALLET -->

        <div class="bg-white rounded-2xl shadow p-6 mb-8">

            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                Fund Wallet
            </h2>

            <div class="flex gap-4">

                <input
                    type="number"
                    id="amount"
                    placeholder="Enter amount (₦)"
                    class="border p-3 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-green-500">

                <button onclick="payWithPaystack()"
                    class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">

                    Fund Wallet
                </button>

            </div>

        </div>

        <a href="export_transactions.php"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">

            Export CSV
        </a>

        <!-- STATS GRID -->

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <!-- AVAILABLE BALANCE -->

            <div class="bg-green-600 text-white p-6 rounded-2xl shadow">

                <p class="text-sm opacity-80">
                    Available Balance
                </p>

                <h2 class="text-3xl font-bold mt-2">
                    ₦<?= number_format($balance, 2) ?>
                </h2>

            </div>

            <!-- TOTAL DEPOSITED -->

            <div class="bg-white rounded-2xl shadow p-6">

                <p class="text-sm text-gray-500">
                    Total Deposited
                </p>

                <h3 class="text-2xl font-bold text-green-600 mt-2">
                    ₦<?= number_format($totalDeposited, 2) ?>
                </h3>

            </div>

            <!-- TOTAL EARNED -->

            <?php if ($_SESSION['role'] === 'student'): ?>

                <div class="bg-white rounded-2xl shadow p-6">

                    <p class="text-sm text-gray-500">
                        Total Earned
                    </p>

                    <h3 class="text-2xl font-bold text-blue-600 mt-2">
                        ₦<?= number_format($totalEarned, 2) ?>
                    </h3>

                </div>

            <?php endif; ?>

            <!-- TOTAL WITHDRAWN -->

            <div class="bg-white rounded-2xl shadow p-6">

                <p class="text-sm text-gray-500">
                    Total Withdrawn
                </p>

                <h3 class="text-2xl font-bold text-red-500 mt-2">
                    ₦<?= number_format($totalWithdrawn, 2) ?>
                </h3>

            </div>

        </div>

        <!-- SECONDARY STATS -->

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <!-- HELD ESCROW -->

            <div class="bg-yellow-500 text-white p-6 rounded-2xl shadow">

                <p class="text-sm opacity-80">
                    Held in Escrow
                </p>

                <h3 class="text-2xl font-semibold mt-2">
                    ₦<?= number_format($held, 2) ?>
                </h3>

            </div>

            <!-- WITHDRAWAL HOLD -->

            <div class="bg-red-500 text-white p-6 rounded-2xl shadow">

                <p class="text-sm opacity-80">
                    Pending Withdrawals
                </p>

                <h3 class="text-2xl font-semibold mt-2">
                    ₦<?= number_format($withdrawal_hold, 2) ?>
                </h3>

            </div>

        </div>

        <!-- PENDING EARNINGS -->

        <?php if ($_SESSION['role'] === 'student'): ?>

            <div class="bg-white rounded-2xl shadow p-6">

                <p class="text-sm text-gray-500">
                    Pending Earnings
                </p>

                <h3 class="text-2xl font-bold text-yellow-600 mt-2">
                    ₦<?= number_format($pending, 2) ?>
                </h3>

                <p class="text-sm text-gray-400 mt-2">
                    Funds awaiting employer approval
                </p>

            </div>

        <?php endif; ?>

    </div>

    <!-- PAYSTACK -->

    <script src="https://js.paystack.co/v1/inline.js"></script>

    <script>
        function payWithPaystack() {

            let amountInput = document.getElementById("amount").value;

            if (!amountInput || amountInput <= 0) {
                alert("Enter a valid amount");
                return;
            }

            let amount = parseFloat(amountInput);

            let handler = PaystackPop.setup({

                key: 'pk_test_4988996324d62da606215d93acc2c4295f7e3bc6',

                email: "<?php echo $_SESSION['email']; ?>",

                amount: amount * 100,

                metadata: {
                    user_id: "<?php echo $_SESSION['user_id']; ?>"
                },

                currency: "NGN",

                callback: function(response) {

                    window.location.href =
                        "../wallet/verify_payment.php?reference=" +
                        response.reference;
                },

                onClose: function() {
                    alert("Payment cancelled");
                }
            });

            handler.openIframe();
        }
    </script>

</body>

</html>