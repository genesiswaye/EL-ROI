<?php

require "middleware.php";
require "helper.php";

requireRole([
    'support_admin',
    'super_admin'
]);

require_once "../config/database.php";

try {

    $stmt = $pdo->query(

        "
        SELECT

            d.id,
            d.reason,
            d.status,
            d.created_at,

            j.title AS job_title,

            u.full_name AS opened_by,

            e.amount AS escrow_amount

        FROM disputes d

        JOIN jobs j
            ON d.job_id = j.id

        JOIN users u
            ON d.opened_by = u.id

        LEFT JOIN escrows e
            ON d.job_id = e.job_id

        ORDER BY d.created_at DESC
        "

    );

    $disputes = $stmt->fetchAll(
        PDO::FETCH_ASSOC
    );

} catch (Exception $e) {

    die(
        $e->getMessage()
    );

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

Disputes Panel

</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100">

<div class="flex">

    <!-- Sidebar -->

    <aside
    class="
    w-64
    min-h-screen
    bg-blue-900
    text-white
    p-6
    "
    >

        <h1
        class="
        text-2xl
        font-bold
        mb-8
        "
        >

            StudentLancer Admin

        </h1>

        <nav class="space-y-4">

            <a href="dashboard.php">

                Dashboard

            </a>

            <a href="withdrawals.php">

                Withdrawals

            </a>

            <a
            href="disputes.php"
            class="font-bold"
            >

                Disputes

            </a>

            <a href="analytics.php">

                Analytics

            </a>

            <a href="audit_logs.php">

                Audit Logs

            </a>

        </nav>

    </aside>


    <!-- Main -->

    <main
    class="
    flex-1
    p-8
    "
    >

        <h2
        class="
        text-3xl
        font-bold
        mb-6
        "
        >

            Dispute Panel

        </h2>

        <div
        class="
        bg-white
        rounded-xl
        shadow
        overflow-x-auto
        "
        >

            <table class="w-full">

                <thead
                class="
                bg-gray-50
                "
                >

                    <tr>

                        <th class="p-4">

                            Job

                        </th>

                        <th class="p-4">

                            Opened By

                        </th>

                        <th class="p-4">

                            Escrow

                        </th>

                        <th class="p-4">

                            Reason

                        </th>

                        <th class="p-4">

                            Status

                        </th>

                        <th class="p-4">

                            Date

                        </th>

                        <th class="p-4">

                            Action

                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach (
                    $disputes
                    as $dispute
                ): ?>

                    <tr
                    class="
                    border-t
                    hover:bg-gray-50
                    "
                    >

                        <td class="p-4">

                            <?= htmlspecialchars(
                                $dispute['job_title']
                            ) ?>

                        </td>

                        <td class="p-4">

                            <?= htmlspecialchars(
                                $dispute['opened_by']
                            ) ?>

                        </td>

                        <td class="p-4">

                            ₦<?= number_format(

                                $dispute['escrow_amount']
                                ?? 0,

                                2

                            ) ?>

                        </td>

                        <td class="p-4">

                            <?= htmlspecialchars(
                                $dispute['reason']
                            ) ?>

                        </td>

                        <td class="p-4">

                            <span
                            class="
                            px-3
                            py-1
                            rounded-full
                            text-sm

                            <?=

                            $dispute['status']
                            === 'open'

                            ?

                            'bg-red-100 text-red-700'

                            :

                            'bg-green-100 text-green-700'

                            ?>

                            "
                            >

                                <?= ucfirst(
                                    $dispute['status']
                                ) ?>

                            </span>

                        </td>

                        <td class="p-4">

                            <?= date(

                                "d M Y",

                                strtotime(
                                    $dispute['created_at']
                                )

                            ) ?>

                        </td>

                        <td class="p-4">

                            <a

                            href="
                            view_dispute.php?id=
                            <?= $dispute['id'] ?>
                            "

                            class="
                            bg-blue-600
                            text-white
                            px-3
                            py-2
                            rounded
                            hover:bg-blue-700
                            "

                            >

                                View

                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

                <?php if (
                    empty($disputes)
                ): ?>

                    <tr>

                        <td
                        colspan="7"
                        class="
                        p-8
                        text-center
                        text-gray-500
                        "
                        >

                            No disputes found.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </main>

</div>

</body>

</html>