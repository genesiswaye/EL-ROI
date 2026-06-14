<?php

session_start();

require "../config/database.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("
        SELECT *
        FROM admins
        WHERE email = ?
        AND status = 'active'
    ");

    $stmt->execute([
        $email
    ]);

    $admin = $stmt->fetch(
        PDO::FETCH_ASSOC
    );

    if ($admin) {

        if (
            password_verify(
                $password,
                $admin['password_hash']
            )
        ) {

            $_SESSION['admin_id'] =
                $admin['admin_id'];

            $_SESSION['admin_role'] =
                $admin['role'];

            $_SESSION['admin_name'] =
                $admin['full_name'];

            header(
                "Location: admin-dashboard.php"
            );

            exit();

        }

    }

    $error =
        "Invalid email or password.";

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>

        StudentLancer Admin Login

    </title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body
    class="
        bg-linear-to-br
        from-blue-900
        to-blue-700
        min-h-screen
        flex
        items-center
        justify-center
    "
>

    <div
        class="
            bg-white
            w-full
            max-w-md
            rounded-2xl
            shadow-2xl
            p-8
        "
    >

        <div class="text-center mb-8">

            <h1
                class="
                    text-3xl
                    font-bold
                    text-blue-900
                "
            >

                StudentLancer

            </h1>

            <p
                class="
                    text-gray-500
                    mt-2
                "
            >

                Admin Portal

            </p>

        </div>

        <?php if($error): ?>

            <div
                class="
                    bg-red-100
                    text-red-700
                    p-3
                    rounded-lg
                    mb-5
                "
            >

                <?= $error ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="mb-5">

                <label
                    class="
                        block
                        font-medium
                        mb-2
                    "
                >

                    Email Address

                </label>

                <input
                    type="email"
                    name="email"
                    required
                    placeholder="admin@studentlancer.com"
                    class="
                        w-full
                        border
                        border-gray-300
                        rounded-lg
                        p-3
                        focus:outline-none
                        focus:ring-2
                        focus:ring-blue-500
                    "
                >

            </div>

            <div class="mb-6">

                <label
                    class="
                        block
                        font-medium
                        mb-2
                    "
                >

                    Password

                </label>

                <input
                    type="password"
                    name="password"
                    required
                    placeholder="Enter password"
                    class="
                        w-full
                        border
                        border-gray-300
                        rounded-lg
                        p-3
                        focus:outline-none
                        focus:ring-2
                        focus:ring-blue-500
                    "
                >

            </div>

            <button
                class="
                    w-full
                    bg-blue-800
                    hover:bg-blue-900
                    text-white
                    font-semibold
                    py-3
                    rounded-lg
                    transition
                "
            >

                Login

            </button>

        </form>

        <div
            class="
                mt-6
                text-center
                text-sm
                text-gray-500
            "
        >

            StudentLancer Admin System

        </div>

    </div>

</body>

</html>