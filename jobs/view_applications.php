<?php

session_start();

require_once "../config/database.php";
require_once "../includes/auth_guard.php";

$user_id = $_SESSION['user_id'];

if (!isset($_GET['job_id'])) {

    die("Job not specified");
}

$job_id = $_GET['job_id'];

/* VERIFY OWNER */

$stmt = $pdo->prepare("

SELECT
id,
title

FROM jobs

WHERE id=?
AND created_by=?

");

$stmt->execute([
    $job_id,
    $user_id
]);

$job = $stmt->fetch();

if (!$job) {

    die("Unauthorized job access");
}

/* JOB SKILLS */

$stmt = $pdo->prepare("

SELECT skill_id

FROM job_skills

WHERE job_id=?

");

$stmt->execute([$job_id]);

$job_skills =
    $stmt->fetchAll(PDO::FETCH_COLUMN);

$total_required_skills =
    count($job_skills);

/* APPLICATIONS */

$stmt = $pdo->prepare("

SELECT

applications.id,
applications.student_id,
applications.proposal_text,
applications.bid_amount,
applications.status,
applications.created_at,
applications.ai_score,

users.full_name,
users.email,
users.rating,
users.completed_jobs,

student_profiles.department,
student_profiles.level

FROM applications

JOIN users

ON applications.student_id=
users.id

LEFT JOIN student_profiles

ON users.id=
student_profiles.user_id

WHERE applications.job_id=?

ORDER BY applications.created_at DESC

");

$stmt->execute([
    $job_id
]);

$applications =
    $stmt->fetchAll();

/* PYTHON PATHS */

$python =
    "C:\\xampp\\htdocs\\EL-ROI\\AI\\winvenv\\Scripts\\python.exe";

$predict =
    "C:\\xampp\\htdocs\\EL-ROI\\AI\\predict.py";

?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width,initial-scale=1">

    <title>

        Applications

    </title>

    <link
        rel="stylesheet"
        href="../dist/output.css">

</head>

<body
    class="bg-[#F7F8FA] min-h-screen">

    <?php

    $activePage = "my_jobs";

    include
        "../includes/employer_nav.php";

    ?>

    <div
        class="max-w-6xl mx-auto py-10 px-6">

        <h1
            class="text-2xl font-semibold mb-6">

            Applications for:

            <?= htmlspecialchars(
                $job['title']
            ) ?>

        </h1>

        <?php if (empty($applications)): ?>

            <div
                class="bg-white p-6 rounded-lg shadow">

                <p
                    class="text-gray-500">

                    No students have applied yet.

                </p>

            </div>

        <?php else: ?>

            <div class="space-y-4">

                <?php

                foreach (
                    $applications
                    as $app
                ):

                    /* STUDENT SKILLS */

                    $stmt =
                        $pdo->prepare("

SELECT skill_id

FROM student_skills

WHERE user_id=?

");

                    $stmt->execute([
                        $app['student_id']
                    ]);

                    $student_skills =
                        $stmt->fetchAll(
                            PDO::FETCH_COLUMN
                        );

                    /* FEATURES */

                    $matched_skills =
                        count(

                            array_intersect(

                                $job_skills,

                                $student_skills

                            )

                        );

                    $skill_match =
                        $total_required_skills

                        ?

                        (

                            $matched_skills
                            /

                            $total_required_skills

                        )

                        :

                        0;

                    /* MODEL INPUT */

                    $rating =
                        $app['rating']
                        ?? 0;

                    $completed_jobs =
                        $app['completed_jobs']
                        ?? 0;

                    /* PREDICT */

                    $command =

                        "\"$python\" \"$predict\" "

                        . $skill_match . " "

                        . $matched_skills . " "

                        . $rating . " "

                        . $completed_jobs

                        . " 2>&1";

                    $ai_score =
                        trim(

                            shell_exec(

                                $command

                            )

                        );
                    $skill_component =
                        $skill_match * 70;

                    $rating_component =
                        ($rating / 5) * 20;

                    $completed_component =
                        min(
                            $completed_jobs,
                            10
                        )
                        *
                        1;

                    $ml_component =
                        (float)$ai_score
                        *
                        0.15;

                    $final_score =

                        $skill_component

                        +

                        $rating_component

                        +

                        $completed_component

                        +

                        $ml_component;

                    $final_score =
                        min(
                            100,
                            round(
                                $final_score,
                                2
                            )
                        );

                    $statusColor =
                        match ($app['status']) {

                            'accepted' =>

                            'text-green-600',

                            'rejected' =>

                            'text-red-500',

                            default =>

                            'text-yellow-600'
                        };

                ?>

                    <div
                        class="bg-white p-6 rounded-lg shadow">

                        <div
                            class="flex justify-between">

                            <div>

                                <h2
                                    class="font-semibold text-lg">

                                    <?= htmlspecialchars(

                                        $app['full_name']

                                    ) ?>

                                </h2>

                                <p
                                    class="text-sm text-gray-500">

                                    <?= htmlspecialchars(

                                        $app['department']

                                    ) ?>

                                    —

                                    Level

                                    <?= htmlspecialchars(

                                        $app['level']

                                    ) ?>

                                </p>

                                <p
                                    class="text-sm text-gray-400 mt-1">

                                    Applied on

                                    <?= date(

                                        "M d,Y",

                                        strtotime(

                                            $app['created_at']

                                        )

                                    ) ?>

                                </p>

                            </div>

                            <div
                                class="text-right">

                                <span
                                    class="<?= $statusColor ?>

font-medium text-sm">

                                    <?= ucfirst(

                                        $app['status']

                                    ) ?>

                                </span>

                                <p
                                    class="text-sm
font-semibold
text-[#4B2E83]
mt-2">

                                    AI Match:

                                    <?= number_format(
                                        $final_score,
                                        2
                                    ) ?>%


                                </p>

                                
                            </div>

                        </div>

                        <div
                            class="mt-5 flex gap-3">

                            <a

                                href="../profile/student_profile.php?user_id=<?= $app['student_id'] ?>&job_id=<?= $job_id ?>"

                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm">

                                View Profile

                            </a>

                            <a

                                href="view_proposal.php?proposal_id=<?= $app['id'] ?>"

                                class="px-4 py-2 bg-[#4B2E83] text-white rounded-lg text-sm">

                                View Proposal

                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</body>

</html>