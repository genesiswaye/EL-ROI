<?php

session_start();

require_once "../config/database.php";
require_once "helper.php";

requireRole([
    'support_admin',
    'super_admin'
]);

$stmt = $pdo->query("
    SELECT
        company_profiles.*,
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


<div class="max-w-7xl mx-auto p-6">

    <h1 class="text-3xl font-bold mb-6">
        Pending Company Verifications
    </h1>

    <?php if(empty($companies)): ?>

        <div class="bg-white p-6 rounded-lg shadow">
            No pending companies.
        </div>

    <?php endif; ?>

    <div class="space-y-5">

        <?php foreach($companies as $company): ?>

            <div class="bg-white rounded-lg shadow p-6">

                <h2 class="text-xl font-semibold">
                    <?= htmlspecialchars($company['company_name']) ?>
                </h2>

                <div class="mt-3 space-y-2">

                    <p>
                        <strong>Email:</strong>
                        <?= htmlspecialchars($company['email']) ?>
                    </p>

                    <p>
                        <strong>Industry:</strong>
                        <?= htmlspecialchars($company['industry']) ?>
                    </p>

                    <p>
                        <strong>Website:</strong>
                        <?= htmlspecialchars($company['website']) ?>
                    </p>

                    <p>
                        <strong>CAC Number:</strong>
                        <?= htmlspecialchars($company['cac_number']) ?>
                    </p>

                    <p>
                        <strong>Phone:</strong>
                        <?= htmlspecialchars($company['phone_number']) ?>
                    </p>

                    <p>
                        <strong>Address:</strong>
                        <?= htmlspecialchars($company['address']) ?>
                    </p>

                    <p>
                        <strong>Contact Person:</strong>
                        <?= htmlspecialchars($company['contact_name']) ?>
                    </p>

                    <p>
                        <strong>Position:</strong>
                        <?= htmlspecialchars($company['contact_position']) ?>
                    </p>

                    <p>
                        <strong>Description:</strong>
                    </p>

                    <div class="bg-gray-100 p-3 rounded">
                        <?= nl2br(htmlspecialchars($company['description'])) ?>
                    </div>

                </div>

                <div class="flex gap-3 mt-5">

                    <a
                        href="approve_company.php?id=<?= $company['id'] ?>"
                        class="bg-green-600 text-white px-4 py-2 rounded"
                    >
                        Approve
                    </a>

                    <a
                        href="reject_company.php?id=<?= $company['id'] ?>"
                        class="bg-red-600 text-white px-4 py-2 rounded"
                    >
                        Reject
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>