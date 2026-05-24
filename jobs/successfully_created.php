<?php

session_start();

include "../config/database.php";

/*
Expected redirect after creating a job:

header(
    "Location: success.php?job_id=".$pdo->lastInsertId()
);

*/

if (!isset($_GET['job_id'])) {

    header("Location: ../dashboard/overview.php");
    exit();
}

$job_id = $_GET['job_id'];

$stmt = $pdo->prepare("
    SELECT *
    FROM jobs
    WHERE id = ?
");

$stmt->execute([
    $job_id
]);

$job = $stmt->fetch(
    PDO::FETCH_ASSOC
);

if (!$job) {

    header("Location: dashboard.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0" />

    <title>
        StudentLancer — Job Created Successfully
    </title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com" />

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin />

    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet" />

    <style>
        body {

            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            color: #0f172a;

        }

        .card {

            background: white;

            max-width: 600px;
            width: 100%;

            padding: 45px;

            border-radius: 18px;

            border: 1px solid #e2e8f0;

            box-shadow:
                0 8px 30px rgba(0, 0, 0, .06);

        }

        .actions {

            display: flex;

            gap: 12px;

            flex-wrap: wrap;

        }

        .btn {

            padding: 14px 22px;

            border-radius: 12px;

            text-decoration: none;

            font-weight: 700;

        }

        .primary {

            background: #2563eb;
            color: white;

        }

        .secondary {

            border: 1px solid #e2e8f0;
            color: #334155;

        }
    </style>

</head>

<body>

    <main>

        <div class="card">

            <h1>

                Job Created Successfully!

            </h1>

            <p>

                Your job has been posted successfully.

            </p>

            <div>

                <strong>Job ID:</strong>

                #SL-

                <?=

                str_pad(
                    $job['id'],
                    4,
                    "0",
                    STR_PAD_LEFT
                )

                ?>

            </div>

            <div>

                <strong>Status:</strong>

                <?=

                ucfirst(
                    $job['status']
                )

                ?>

            </div>

            <div>

                <strong>Posted:</strong>

                <?=

                date(
                    "M j, Y",
                    strtotime(
                        $job['created_at']
                    )
                )

                ?>

            </div>

            <div class="actions">

                <a
                    href="../dashboard/overview.php"
                    class="btn primary">

                    Go To Dashboard

                </a>

                <a
                    href="post_job.php"
                    class="btn secondary">

                    Create Another Job

                </a>

            </div>

        </div>

    </main>

</body>

</html>