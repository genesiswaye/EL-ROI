<?php
session_start();
require_once "../config/database.php";
require_once "transaction.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $amount = (float) $_POST['amount'];
        $bank_name = trim($_POST['bank_name']);
        $account_number = trim($_POST['account_number']);
        $account_name = trim($_POST['account_name']);

        if (!$bank_name || !$account_number || !$account_name) {
            throw new Exception("All fields are required");
        }

        // 🔥 CALL YOUR FUNCTION
        $withdrawal_id = requestWithdrawal($pdo, $user_id, $amount);

        // OPTIONAL: store bank details
        $stmt = $pdo->prepare("
            UPDATE withdrawals 
            SET bank_name = ?, account_number = ?, account_name = ?
            WHERE id = ? 
        ");
        $stmt->execute([$bank_name, $account_number, $account_name, $withdrawal_id]);

        $success = "Withdrawal request submitted";
        
        echo("Withdrawal request submitted successfully.");

        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
        echo("Withdrawal request failed: " . $error);
        exit();
    }

     
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Withdraw Funds</title>
    <link href="../dist/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <div class="max-w-md mx-auto p-6">

        <h1 class="text-2xl font-bold mb-6">Withdraw Funds</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-600 p-3 mb-4 rounded">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 p-4 rounded mb-4">
            Withdrawal requests temporarily reserve your funds until approved or rejected by admin.
        </div>

        <form method="POST" class="space-y-4">

            <input type="number" name="amount" placeholder="Amount (₦)" required
                class="w-full p-3 border rounded">

            <input type="text" name="bank_name" placeholder="Bank Name" required
                class="w-full p-3 border rounded">

            <input type="text" name="account_number" placeholder="Account Number" required
                class="w-full p-3 border rounded">

            <input type="text" name="account_name" placeholder="Account Name" required
                class="w-full p-3 border rounded">

            <button class="bg-green-600 text-white px-4 py-2 rounded w-full">
                Request Withdrawal
            </button>

        </form>

    </div>

</body>

</html>