<?php

function createNotification(
    PDO $pdo,
    int $user_id,
    string $type,
    string $title,
    string $message,
    ?string $link = null
): void {

    $stmt = $pdo->prepare("
        INSERT INTO notifications (
            user_id,
            type,
            title,
            message,
            link
        )
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $user_id,
        $type,
        $title,
        $message,
        $link
    ]);
}