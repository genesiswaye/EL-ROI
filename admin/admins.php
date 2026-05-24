<?php

require "middleware.php";
require "helper.php";
requireRole(['super_admin']);

include "../config/database.php";

try {

    $stmt = $pdo->query("
        SELECT
            a.admin_id,
            a.full_name,
            a.email,
            a.role,
            a.status,
            creator.full_name AS creator_name,
            a.created_at

        FROM admins a

        LEFT JOIN admins creator
        ON a.created_by = creator.admin_id

        ORDER BY a.created_at DESC
    ");

    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {

    die("Error loading admins");

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

<title>Admin Management</title>

<link rel="stylesheet" href="../dist/output.css">
</head>

<body class="bg-gray-100 min-h-screen">

<div class="flex">

    <!-- Sidebar -->

    <aside
    class="w-64 bg-blue-900 text-white min-h-screen p-6"
    >

        <h1 class="text-2xl font-bold mb-8">

            StudentLancer Admin

        </h1>

        <nav class="space-y-4">

            <a
            href="dashboard.php"
            class="block hover:text-blue-300"
            >
                Dashboard
            </a>

            <a
            href="withdrawals.php"
            class="block hover:text-blue-300"
            >
                Withdrawals
            </a>

            <a
            href="disputes.php"
            class="block hover:text-blue-300"
            >
                Disputes
            </a>

            <a
            href="analytics.php"
            class="block hover:text-blue-300"
            >
                Analytics
            </a>

            <a
            href="audit_logs.php"
            class="block hover:text-blue-300"
            >
                Audit Logs
            </a>
            <a
            href="fraud_flags.php"
            class="block hover:text-blue-300"
            >
                Flags
            </a>

            <a
            href="admins.php"
            class="block font-bold text-blue-300"
            >
                Admin Management
            </a>

        </nav>

    </aside>


    <!-- Main -->

    <main class="flex-1 p-8">

        <div
        class="
        flex
        justify-between
        items-center
        mb-6
        "
        >

            <div>

                <h2
                class="
                text-3xl
                font-bold
                "
                >

                    Admin Management

                </h2>

                <p
                class="text-gray-500"
                >

                    Manage platform administrators

                </p>

            </div>

            <a
            href="create_admin.php"

            class="

            bg-blue-600
            text-white
            px-5
            py-3
            rounded-lg

            hover:bg-blue-700

            "

            >

            + Add Admin

            </a>

        </div>


        <div
        class="
        bg-white
        rounded-xl
        shadow
        overflow-hidden
        "
        >

            <table
            class="
            w-full
            "
            >

                <thead
                class="
                bg-gray-50
                "
                >

                    <tr>

                        <th class="p-4 text-left">

                            Name

                        </th>

                        <th class="p-4 text-left">

                            Email

                        </th>

                        <th class="p-4 text-left">

                            Role

                        </th>

                        <th class="p-4 text-left">

                            Status

                        </th>

                        <th class="p-4 text-left">

                            Created By

                        </th>

                        <th class="p-4 text-left">

                            Created

                        </th>

                        <th class="p-4 text-center">

                            Actions

                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php if(empty($admins)): ?>

                    <tr>

                        <td
                        colspan="7"
                        class="
                        text-center
                        py-10
                        text-gray-500
                        "
                        >

                            No admins found

                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach($admins as $admin): ?>

                    <tr
                    class="
                    border-t
                    hover:bg-gray-50
                    "
                    >

                        <td class="p-4">

                            <?= htmlspecialchars($admin['full_name']) ?>

                        </td>

                        <td class="p-4">

                            <?= htmlspecialchars($admin['email']) ?>

                        </td>

                        <td class="p-4">

                            <?= ucfirst(
                                str_replace(
                                    "_",
                                    " ",
                                    $admin['role']
                                )
                            ) ?>

                        </td>

                        <td class="p-4">

                            <span

                            class="

                            px-3
                            py-1
                            rounded-full
                            text-sm

                            <?= $admin['status']
                            === 'active'

                            ?

                            'bg-green-100 text-green-700'

                            :

                            'bg-red-100 text-red-700'

                            ?>

                            "

                            >

                            <?= ucfirst(
                                $admin['status']
                            ) ?>

                            </span>

                        </td>

                        <td class="p-4">

                            <?=

                            htmlspecialchars(

                            $admin['creator_name']

                            ??

                            "System"

                            )

                            ?>

                        </td>

                        <td class="p-4">

                        <?=

                        date(

                        "d M Y",

                        strtotime(
                        $admin['created_at']
                        )

                        )

                        ?>

                        </td>

                        <td
                        class="
                        p-4
                        text-center
                        "
                        >

                            <div
                            class="
                            flex
                            justify-center
                            gap-2
                            "
                            >

                                <a

                                href="edit_admin.php?id=<?= $admin['admin_id'] ?>"

                                class="

                                bg-yellow-500
                                px-3
                                py-2
                                rounded
                                text-white

                                "

                                >

                                Edit

                                </a>


                                <?php

                                if(

                                $admin['status']

                                ===

                                'active'

                                ):

                                ?>

                                <a

                                href="disable_admin.php?id=<?= $admin['admin_id'] ?>"

                                class="

                                bg-red-600
                                px-3
                                py-2
                                rounded
                                text-white

                                "

                                onclick="return confirm('Disable admin?')"

                                >

                                Disable

                                </a>

                                <?php endif; ?>

                            </div>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </main>

</div>

</body>

</html>