<?php

require "../config/database.php";

$python =
    "C:\\xampp\\htdocs\\EL-ROI\\AI\\winvenv\\Scripts\\python.exe";

$predict =
    "C:\\xampp\\htdocs\\EL-ROI\\AI\\predict.py";

/* GET STUDENTS */

$students =
    $pdo->query("

SELECT

id,
rating,
completed_jobs

FROM users

WHERE role='student'

")->fetchAll();

$jobs =
    $pdo->query("

SELECT id

FROM jobs

WHERE status='open'

")->fetchAll();

foreach (

    $students

    as $student

) {

    $stmt = $pdo->prepare("

SELECT skill_id

FROM student_skills

WHERE user_id=?

");

    $stmt->execute([
        $student['id']
    ]);

    $student_skills =

        array_map(

            'intval',

            $stmt->fetchAll(
                PDO::FETCH_COLUMN
            )

        );

    foreach (

        $jobs

        as $job

    ) {

        $stmt = $pdo->prepare("

SELECT skill_id

FROM job_skills

WHERE job_id=?

");

        $stmt->execute([
            $job['id']
        ]);

        $job_skills =

            array_map(

                'intval',

                $stmt->fetchAll(
                    PDO::FETCH_COLUMN
                )

            );

        $matched =

            count(

                array_intersect(

                    $student_skills,

                    $job_skills

                )

            );

        $total =

            max(
                count(
                    $job_skills
                ),
                1
            );

        $skill_match =

            $matched

            /

            $total;

        $cmd =

            "\"$python\" \"$predict\" "

            . $skill_match . " "

            . $matched . " "

            . $student['rating'] . " "

            . $student['completed_jobs']

            . " 2>&1";

        $ml =

            (float)

            trim(

                shell_exec(
                    $cmd
                )

            );

        $score =

            ($skill_match * 70)

            +

            (($student['rating'] / 5) * 20)

            +

            min(

                $student['completed_jobs'],

                10

            )

            +

            ($ml * 0.15);

        $score =

            round(

                min(
                    100,
                    $score
                ),

                2

            );

        $stmt =

            $pdo->prepare("

INSERT INTO
job_recommendations(

student_id,

job_id,

ai_score

)

VALUES(

?,

?,

?

)

ON DUPLICATE KEY UPDATE

ai_score=

VALUES(ai_score)

");

        $stmt->execute([

            $student['id'],

            $job['id'],

            $score

        ]);
    }
}

echo "Done";
