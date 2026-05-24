<?php

require "middleware.php";
require "helper.php";

requireRole([
    'super_admin'
]);

include "../config/database.php";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if (
        empty($name) ||
        empty($email) ||
        empty($role)
    ) {

        $error =
            "All fields required.";
    } else {

        $check =
            $pdo->prepare(

                "

        SELECT admin_id

        FROM admins

        WHERE email=?

        "

            );

        $check->execute([
            $email
        ]);

        if (
            $check->fetch()
        ) {

            $error =
                "Admin already exists.";
        } else {

            $tempPassword =
                bin2hex(
                    random_bytes(6)
                );

            $hash =
                password_hash(
                    $tempPassword,
                    PASSWORD_DEFAULT
                );

            $stmt =
                $pdo->prepare(

                    "

            INSERT INTO admins(

            full_name,
            email,
            password_hash,
            role,
            created_by

            )

            VALUES(

            ?,
            ?,
            ?,
            ?,
            ?

            )

            "

                );

            $stmt->execute([

                $name,

                $email,

                $hash,

                $role,

                $_SESSION['admin_id']

            ]);

            $success =
                "Admin created.";
        }
    }
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>

        Create Admin

    </title>

    <script src="https://cdn.tailwindcss.com">

    </script>

</head>

<body
    class="bg-gray-100">

    <div
        class="
max-w-xl
mx-auto
mt-10
bg-white
p-8
rounded-lg
shadow
">

        <h1
            class="
text-2xl
font-bold
mb-6
">

            Add Admin

        </h1>

        <?php if ($error): ?>

            <div
                class="
bg-red-100
text-red-700
p-3
rounded
mb-4
">

                <?= $error ?>

            </div>

        <?php endif; ?>

        <?php if ($success): ?>

            <div
                class="
bg-green-100
text-green-700
p-3
rounded
mb-4
">

                <?= $success ?>

                <br>

                Temporary Password:

                <b>

                    <?= $tempPassword ?>

                </b>

            </div>

        <?php endif; ?>

        <form method="POST">

            <input

                name="name"

                placeholder="Full Name"

                required

                class="
w-full
border
p-3
mb-4
rounded
">

            <input

                name="email"

                type="email"

                placeholder="Email"

                required

                class="
w-full
border
p-3
mb-4
rounded
">

            <select

                name="role"

                required

                class="
w-full
border
p-3
mb-4
rounded
">

                <option value="">

                    Select Role

                </option>

                <option
                    value="finance_admin">

                    Finance Admin

                </option>

                <option
                    value="support_admin">

                    Support Admin

                </option>

                <option
                    value="super_admin">

                    Super Admin

                </option>

            </select>

            <button

                class="

bg-blue-700
text-white
px-6
py-3
rounded

">

                Create Admin

            </button>

        </form>

    </div>

</body>

</html>