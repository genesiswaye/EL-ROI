<?php

require "middleware.php";
require "helper.php";

requireRole([
    'support_admin',
    'super_admin'
]);

require_once "../config/database.php";

if (

    $_SERVER['REQUEST_METHOD']
    !== 'POST'

) {

    header(
        "Location: dispute.php"
    );

    exit();
}

$dispute_id =
    (int)(
        $_POST['dispute_id']
        ?? 0
    );

$action =
    $_POST['action']
    ?? '';

$resolution =
    trim(
        $_POST['resolution']
            ?? ''
    );

$admin_id =
    $_SESSION['admin_id'];

if (

    $dispute_id <= 0

    ||

    $resolution === ''

) {

    die("Invalid request");
}

try {

    $pdo->beginTransaction();

    /*
Load dispute
*/

    $stmt =
        $pdo->prepare(

            "
SELECT

d.id,
d.job_id,
d.status AS dispute_status,

j.created_by,

a.student_id,

e.amount

FROM disputes d

JOIN jobs j
ON d.job_id = j.id

LEFT JOIN applications a
ON a.job_id = j.id
AND a.status='accepted'

LEFT JOIN escrows e
ON e.job_id = j.id

WHERE d.id=?

FOR UPDATE

"

        );

    $stmt->execute([
        $dispute_id
    ]);

    $data =
        $stmt->fetch(
            PDO::FETCH_ASSOC
        );

    if (
        !$data
    ) {

        throw new Exception(
            "Dispute not found"
        );
    }

    if (

        $data['dispute_status']
        !== 'open'

    ) {

        throw new Exception(
            "Dispute already resolved"
        );
    }

    $job_id =
        $data['job_id'];

    $employer =
        $data['created_by'];

    $freelancer =
        $data['student_id'];

    $amount =
        (float)(
            $data['amount']
            ?? 0
        );

    $freelancer_pay = 0;
    $employer_return = 0;

    /*
Decision logic
*/

    switch ($action) {

        case 'release_full':

            $freelancer_pay =
                $amount;

            $new_job =
                'completed';

            $escrow_status =
                'released';

            break;

        case 'refund_full':

            $employer_return =
                $amount;

            $new_job =
                'cancelled';

            $escrow_status =
                'refunded';

            break;

        case 'partial_refund':

            $freelancer_pay =
                round(
                    $amount * 0.10,
                    2
                );

            $employer_return =
                round(
                    $amount * 0.90,
                    2
                );

            $new_job =
                'cancelled';

            $escrow_status =
                'partial_refund';

            break;

        case 'reject':

            $new_job =
                'in_progress';

            $escrow_status =
                'held';

            break;

        default:

            throw new Exception(
                "Invalid action"
            );
    }

    /*
Employer wallet
*/

    if (

        $employer_return > 0

    ) {

        $stmt =
            $pdo->prepare(

                "
UPDATE wallets

SET balance =
balance + ?

WHERE user_id=?

"

            );

        $stmt->execute([

            $employer_return,

            $employer

        ]);
    }

    /*
Freelancer wallet
*/

    if (

        $freelancer_pay > 0

        &&

        $freelancer

    ) {

        $stmt =
            $pdo->prepare(

                "
UPDATE wallets

SET balance =
balance + ?

WHERE user_id=?

"

            );

        $stmt->execute([

            $freelancer_pay,

            $freelancer

        ]);
    }

    /*
Escrow update
*/

    $stmt =
        $pdo->prepare(

            "
UPDATE escrows

SET status=?

WHERE job_id=?

"

        );

    $stmt->execute([

        $escrow_status,

        $job_id

    ]);

    /*
Job update
*/

    $stmt =
        $pdo->prepare(

            "
UPDATE jobs

SET status=?

WHERE id=?

"

        );

    $stmt->execute([

        $new_job,

        $job_id

    ]);

    /*
Dispute resolved
*/

    $stmt =
        $pdo->prepare(

            "
UPDATE disputes

SET

status='resolved',

resolution=?

WHERE id=?

"

        );

    $stmt->execute([

        $resolution,

        $dispute_id

    ]);

    /*
Audit log
*/

    $stmt =
        $pdo->prepare(

            "
INSERT INTO audit_logs
(

user_id,
action,
target_type,
target_id,
description,
ip_address

)

VALUES
(
?,
?,
?,
?,
?,
?
)

"

        );

    $stmt->execute([

        $admin_id,

        'dispute_resolved',

        'dispute',

        $dispute_id,

        "Resolved dispute #{$dispute_id} using {$action}",

        $_SERVER['REMOTE_ADDR']

    ]);

    /*
COMMIT
THIS WAS THE MISSING PIECE
*/

    $pdo->commit();

    header(
        "Location: dispute.php"
    );

    exit();
} catch (Exception $e) {

    $pdo->rollBack();

    die($e->getMessage());
}
