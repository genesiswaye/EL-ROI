<?php

session_start();

require_once "../config/database.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE email = ?
        AND role = 'company'
    ");

    $stmt->execute([
        $email
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {

        $error = "Invalid email or password.";
    } else {

        if ($user['account_status'] === 'pending') {

            $error =
                "Your company verification is still under review.";
        } elseif ($user['account_status'] === 'rejected') {

            $error =
                "Your company application was not approved.";
        } elseif (
            $user['account_status'] !== 'active'
        ) {

            $error =
                "Your account is not active.";
        } elseif (
            empty($user['password'])
        ) {

            $error =
                "Account setup has not been completed.";
        } elseif (
            !password_verify(
                $password,
                $user['password']
            )
        ) {

            $error =
                "Invalid email or password.";
        } else {

            $_SESSION['user_id'] =
                $user['id'];

            $_SESSION['role'] =
                $user['role'];

            $_SESSION['email'] =
                $user['email'];

            $_SESSION['name'] =
                $user['full_name'];

            header(
                "Location: ../dashboard/overview.php"
            );

            exit();
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <link
        rel="stylesheet"
        href="../dist/output.css">
    

    <title>
        Company Login
    </title>

</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">

    <div
        class="bg-white rounded-xl shadow-lg w-full max-w-md p-8">

        <div class="text-center mb-8">

            <img
                src="../assets/studentLancerlogo.jpg"
                alt="StudentLancer"
                class="w-20 mx-auto mb-4">

            <h1 class="text-2xl font-bold">
                Company Login
            </h1>

            <p class="text-gray-500 mt-2">
                Access your StudentLancer company account
            </p>

        </div>

        <?php if (!empty($error)): ?>

            <div
                class="bg-red-100 text-red-700 p-3 rounded mb-4">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="mb-4">

                <label
                    class="block mb-2 font-medium">

                    Email Address

                </label>

                <input
                    type="email"
                    name="email"
                    required
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-6">

                <label
                    class="block mb-2 font-medium">

                    Password

                </label>

                <input
                    type="password"
                    name="password"
                    required
                    class="w-full border rounded-lg p-3">

            </div>

            <button
                type="submit"
                class="w-full bg-purple-700 text-white p-3 rounded-lg hover:bg-purple-800">

                Login

            </button>

        </form>

        <div class="mt-6 text-center">

            <a
                href="company_register.php"
                class="text-purple-700">

                Register Your Company

            </a>

        </div>

    </div>

</body>

</html>