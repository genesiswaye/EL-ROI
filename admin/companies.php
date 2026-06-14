<?php

// session_start();

require_once "../config/database.php";
// require_once "helper.php";

// requireRole([
//     'support_admin',
//     'super_admin'
// ]);

$stmt = $pdo->query("
    SELECT
        company_profiles.id,
        company_profiles.company_name,
        company_profiles.industry,
        company_profiles.cac_number,
        company_profiles.verification_status,
        users.email,
        users.created_at

    FROM company_profiles

    JOIN users
        ON company_profiles.user_id = users.id

    WHERE company_profiles.verification_status = 'pending'

    ORDER BY users.created_at DESC
");

$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Company Verification</title>

    <link
        href="../dist/output.css"
        rel="stylesheet">

</head>

<body class="bg-gray-100">
    <?php
    $activePage = "companies";
    include "admins2.php";
    ?>

<div class="max-w-7xl mx-auto p-6">

    <div class="mb-8">

        <h1 class="text-3xl font-bold text-gray-900">
            Company Verification Queue
        </h1>

        <p class="text-gray-600 mt-2">
            Review pending company registrations.
        </p>

    </div>

    <?php if (empty($companies)): ?>

        <div class="bg-white rounded-xl p-10 text-center shadow">

            <h3 class="text-lg font-semibold text-gray-700">
                No Pending Applications
            </h3>

            <p class="text-gray-500 mt-2">
                All company applications have been reviewed.
            </p>

        </div>

    <?php else: ?>

        <div class="grid gap-6">

            <?php foreach ($companies as $company): ?>

                <div class="bg-white rounded-xl shadow p-6">

                    <div class="flex justify-between items-center">

                        <div>

                            <h2 class="text-xl font-semibold">

                                <?= htmlspecialchars(
                                    $company['company_name']
                                ) ?>

                            </h2>

                            <p class="text-gray-500">

                                <?= htmlspecialchars(
                                    $company['email']
                                ) ?>

                            </p>

                        </div>

                        <span
                            class="px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-700">

                            Pending Review

                        </span>

                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-6">

                        <div>

                            <p class="text-sm text-gray-500">
                                Industry
                            </p>

                            <p class="font-medium">
                                <?= htmlspecialchars(
                                    $company['industry']
                                ) ?>
                            </p>

                        </div>

                        <div>

                            <p class="text-sm text-gray-500">
                                CAC Number
                            </p>

                            <p class="font-medium">
                                <?= htmlspecialchars(
                                    $company['cac_number']
                                ) ?>
                            </p>

                        </div>

                        <div>

                            <p class="text-sm text-gray-500">
                                Submitted
                            </p>

                            <p class="font-medium">
                                <?= date(
                                    "M d, Y",
                                    strtotime(
                                        $company['created_at']
                                    )
                                ) ?>
                            </p>

                        </div>

                    </div>

                    <div class="mt-6">

                        <a
                            href="review_company.php?id=<?= $company['id'] ?>"
                            class="inline-flex items-center px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">

                            Review Application

                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

</body>
</html>