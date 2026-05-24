<?php

require "middleware.php";
require "helper.php";

requireRole([
    'support_admin',
    'super_admin'
]);

require_once "../config/database.php";

$dispute_id =
    (int)(
        $_GET['id']
        ?? 0
    );

$stmt =
    $pdo->prepare(

        "
SELECT

d.id,
d.reason,
d.status,
d.created_at,

j.id AS job_id,
j.title,
j.budget,
j.status AS job_status,

u1.full_name AS opened_by,

u2.full_name AS employer,

u3.full_name AS freelancer,

e.amount AS escrow_amount

FROM disputes d

JOIN jobs j
ON d.job_id = j.id

JOIN users u1
ON d.opened_by = u1.id

LEFT JOIN users u2
ON j.created_by = u2.id

LEFT JOIN applications a
ON a.job_id = j.id
AND a.status='accepted'

LEFT JOIN users u3
ON a.student_id = u3.id

LEFT JOIN escrows e
ON e.job_id = j.id

WHERE d.id = ?

"

    );

$stmt->execute([
    $dispute_id
]);

$dispute =
    $stmt->fetch(
        PDO::FETCH_ASSOC
    );

if (
    !$dispute
) {

    die("Dispute not found");
}

?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <script src="https://cdn.tailwindcss.com"></script>

    <title>

        View Dispute

    </title>

</head>

<body
    class="
bg-gray-100
p-8
">

    <div
        class="
max-w-5xl
mx-auto
bg-white
rounded-xl
shadow
p-8
">

        <h1
            class="
text-3xl
font-bold
mb-8
">

            Dispute Review

        </h1>

        <div
            class="
grid
grid-cols-2
gap-6
mb-8
">

            <div>

                <p>

                    <strong>

                        Job

                    </strong>

                </p>

                <p>

                    <?= htmlspecialchars(
                        $dispute['title']
                    ) ?>

                </p>

            </div>

            <div>

                <p>

                    <strong>

                        Escrow

                    </strong>

                </p>

                <p>

                    ₦<?= number_format(

                            $dispute['escrow_amount']
                                ?? 0

                        )

                        ?>

                </p>

            </div>

            <div>

                <p>

                    <strong>

                        Employer

                    </strong>

                </p>

                <p>

                    <?= htmlspecialchars(
                        $dispute['employer']
                    )
                    ?>

                </p>

            </div>

            <div>

                <p>

                    <strong>

                        Freelancer

                    </strong>

                </p>

                <p>

                    <?= htmlspecialchars(
                        $dispute['freelancer']
                            ?? 'Unknown'
                    )
                    ?>

                </p>

            </div>

            <div>

                <p>

                    <strong>

                        Opened By

                    </strong>

                </p>

                <p>

                    <?= htmlspecialchars(
                        $dispute['opened_by']
                    )
                    ?>

                </p>

            </div>

            <div>

                <p>

                    <strong>

                        Status

                    </strong>

                </p>

                <p>

                    <?= htmlspecialchars(
                        $dispute['status']
                    )
                    ?>

                </p>

            </div>

        </div>

        <div
            class="
mb-8
">

            <h2
                class="
font-bold
mb-2
">

                Dispute Reason

            </h2>

            <div
                class="
bg-gray-100
p-4
rounded
">

                <?= nl2br(

                    htmlspecialchars(
                        $dispute['reason']
                    )

                )

                ?>

            </div>

        </div>

        <form
            method="POST"
            action="resolve_dispute.php">

            <input
                type="hidden"
                name="dispute_id"
                value="<?= $dispute['id'] ?>">

            <textarea

                name="resolution"

                required

                placeholder="
Admin resolution notes...
"

                class="
w-full
border
rounded
p-4
mb-6
"

                rows="5"></textarea>

            <div
                class="
flex
flex-wrap
gap-4
">

                <button

                    name="action"
                    value="release_full"

                    class="
bg-green-600
text-white
px-5
py-3
rounded
">

                    Release Escrow

                </button>

                <button

                    name="action"
                    value="refund_full"

                    class="
bg-blue-600
text-white
px-5
py-3
rounded
">

                    Refund Employer

                </button>

                <button

                    name="action"
                    value="partial_refund"

                    class="
bg-yellow-600
text-white
px-5
py-3
rounded
">

                    10% Freelancer

                </button>

                <button

                    name="action"
                    value="reject"

                    class="
bg-gray-600
text-white
px-5
py-3
rounded
">

                    Reject Dispute

                </button>

            </div>

        </form>

    </div>

</body>

</html>