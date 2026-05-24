<?php

declare(strict_types=1);

require_once "transaction.php";

/**
 * Create wallet for new user
 */
function createWallet(PDO $pdo, int $user_id): bool
{
    $stmt = $pdo->prepare("
        INSERT INTO wallets (user_id, balance, held_balance, currency, withdraw_hold) 
        VALUES (?, 0.00, 0.00, 'NGN', 0.00)
    ");
    return $stmt->execute([$user_id]);
}

/**
 * Get wallet details
 */
function getWallet(PDO $pdo, int $user_id): array|null
{
    // $stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id = ?");
    // $stmt->execute([$user_id]);

    // $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

    // exit();
    $stmt = $pdo->prepare("
        SELECT balance, held_balance, withdrawal_hold 
        FROM wallets 
        WHERE user_id = ?
    ");
    $stmt->execute([$user_id]);

    $wallet = $stmt->fetch(PDO::FETCH_ASSOC);
    return $wallet ?: null;
}

