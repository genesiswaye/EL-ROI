<?php

function flagSuspiciousActivity(
    PDO $pdo,
    int $user_id,
    string $reason,
    string $severity = 'low'
): void {

    $stmt = $pdo->prepare("
        INSERT INTO fraud_flags (
            user_id,
            reason,
            severity
        )
        VALUES (?, ?, ?)
    ");

    $stmt->execute([
        $user_id,
        $reason,
        $severity
    ]);
}


?>