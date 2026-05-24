<?php

require_once __DIR__ . "/notifications.php";

function logAudit(
    PDO $pdo,
    ?int $user_id,
    string $action,
    ?string $target_type = null,
    ?int $target_id = null,
    ?string $description = null
): void {

    $ip = $_SERVER['REMOTE_ADDR'] ?? null;

    /* ======================
       STORE AUDIT LOG
    ====================== */

    $stmt = $pdo->prepare("
        INSERT INTO audit_logs (

            user_id,
            action,
            target_type,
            target_id,
            description,
            ip_address

        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([

        $user_id,
        $action,
        $target_type,
        $target_id,
        $description,
        $ip

    ]);

    /* ======================
       STOP IF SYSTEM EVENT
    ====================== */

    if (!$user_id) {
        return;
    }

    /* ======================
       EVENTS THAT CREATE
       NOTIFICATIONS
    ====================== */

    $allowedNotifications = [

        'wallet_funded',
        'wallet_funding_failed',

        'withdrawal_requested',
        'withdrawal_approved',
        'withdrawal_rejected',

        'payment_received',
        'escrow_release',

        'proposal_accepted',
        'job_completed',

        'payment_verification_failed',

        'refund',
        'work_submitted',

        /* DISPUTE EVENTS */

        'dispute_opened',
        'dispute_under_review',
        'dispute_refund',
        'dispute_release',
        'dispute_rejected'

    ];

    /* ======================
       CREATE NOTIFICATION
    ====================== */

    if (

        in_array(
            $action,
            $allowedNotifications
        )

        && function_exists(
            'createNotification'
        )

    ) {

        $title = ucwords(

            str_replace(
                "_",
                " ",
                $action
            )

        );

        $message =

            $description
            ?: $title;

        createNotification(

            $pdo,

            $user_id,

            $action,

            $title,

            $message,

            "../notifications/index.php"

        );

    }

}

?>