<?php

session_start();

require_once
    "../config/database.php";

if (
    !isset($_SESSION['user_id'])
) {

    header(
        "Location: ../auth/login.php"
    );

    exit();
}

if (
    $_SERVER['REQUEST_METHOD']
    !== 'POST'
) {

    header(
        "Location: ../dashboard/overview.php"
    );

    exit();
}

$user_id =
    $_SESSION['user_id'];

$job_id =
    (int)
    $_POST['job_id'];

$reason =
    trim(
        $_POST['reason']
    );

if (
    $reason === ''
) {

    header(
        "Location: ../dashboard/overview.php"
    );

    exit();
}

/*
Verify user belongs to job
*/

$stmt =
    $pdo->prepare(

        "
SELECT

j.created_by,

a.student_id

FROM jobs j

LEFT JOIN applications a

ON j.id = a.job_id

AND a.status='accepted'

WHERE j.id=?

"

    );

$stmt->execute([
    $job_id
]);

$data =
    $stmt->fetch(
        PDO::FETCH_ASSOC
    );

if (
    !$data
) {

    exit("Job not found");
}

$employer =
    $data['created_by'];

$freelancer =
    $data['student_id'];

if (

    $user_id != $employer

    &&

    $user_id != $freelancer

) {

    header(
        "Location: ../dashboard/overview.php"
    );

    exit();
}

/*
Prevent duplicate disputes
*/

$stmt =
    $pdo->prepare(

        "
SELECT id
FROM disputes
WHERE job_id = ?
AND status='open'
"

    );

$stmt->execute([
    $job_id
]);

if (
    $stmt->fetch()
) {

    header(
        "Location: ../dashboard/overview.php"
    );

    exit();
}

/*
Insert dispute
*/

$stmt =
    $pdo->prepare(

        "
INSERT INTO disputes
(
job_id,
opened_by,
reason
)

VALUES
(
?,
?,
?
)
"

    );

$stmt->execute([

    $job_id,

    $user_id,

    $reason

]);

/*
Put job on hold
*/

$stmt =
    $pdo->prepare(

        "
UPDATE jobs
SET status='on_hold'
WHERE id=?
"

    );

$stmt->execute([
    $job_id
]);

header(
    "Location: ../dashboard/overview.php"
);

exit();
