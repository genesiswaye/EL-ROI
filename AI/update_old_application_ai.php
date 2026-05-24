<?php

require "../config/database.php";

$python =
"C:\\xampp\\htdocs\\EL-ROI\\AI\\winvenv\\Scripts\\python.exe";

$predict =
"C:\\xampp\\htdocs\\EL-ROI\\AI\\predict.py";

/* GET OLD APPLICATIONS */

$stmt = $pdo->query("

SELECT

applications.id,

applications.job_id,

applications.student_id,

users.rating,

users.completed_jobs

FROM applications

JOIN users

ON applications.student_id=
users.id

WHERE applications.ai_score IS NULL

OR applications.ai_score=0

");

$applications =
$stmt->fetchAll();

foreach(

$applications

as $app

){

    /* JOB SKILLS */

    $stmt=
    $pdo->prepare("

    SELECT skill_id

    FROM job_skills

    WHERE job_id=?

    ");

    $stmt->execute([
    $app['job_id']
    ]);

    $job_skills=

    array_map(

    'intval',

    $stmt->fetchAll(
    PDO::FETCH_COLUMN
    )

    );

    /* STUDENT SKILLS */

    $stmt=
    $pdo->prepare("

    SELECT skill_id

    FROM student_skills

    WHERE user_id=?

    ");

    $stmt->execute([
    $app['student_id']
    ]);

    $student_skills=

    array_map(

    'intval',

    $stmt->fetchAll(
    PDO::FETCH_COLUMN
    )

    );

    $matched=

    count(

    array_intersect(

    $job_skills,

    $student_skills

    )

    );

    $total=

    max(

    count(
    $job_skills
    ),

    1

    );

    $skill_match=

    $matched

    /

    $total;

    $cmd=

    "\"$python\" \"$predict\" "

    .$skill_match." "

    .$matched." "

    .$app['rating']." "

    .$app['completed_jobs']

    ." 2>&1";

    $ml=

    (float)

    trim(

    shell_exec(

    $cmd

    )

    );

    $final=

    ($skill_match*70)

    +

    (($app['rating']/5)*20)

    +

    min(

    $app['completed_jobs'],

    10

    )

    +

    ($ml*0.15);

    $final=

    round(

    min(
    100,
    $final
    ),

    2

    );

    $stmt=

    $pdo->prepare("

    UPDATE applications

    SET ai_score=?

    WHERE id=?

    ");

    $stmt->execute([

    $final,

    $app['id']

    ]);

}

echo "Old applications updated";