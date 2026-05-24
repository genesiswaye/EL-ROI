<?php

declare(strict_types=1);

function logTransaction(
    PDO $pdo,
    int $user_id,
    string $type,
    float $amount,
    ?string $reference = null,
    string $status = 'success',
    ?string $description = null,
    ?int $job_id = null,
    float $platform_fee = 0,
    float $net_amount = 0
): int {

    $stmt = $pdo->prepare("
        INSERT INTO transactions (user_id, type, amount, reference, status, description, job_id, platform_fee, net_amount)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $user_id,
        $type,
        $amount,
        $reference,
        $status,
        $description,
        $job_id,
        $platform_fee,
       $net_amount
    ]);

    return (int)$pdo->lastInsertId();
}

function creditWallet(PDO $pdo, int $user_id, float $amount, ?string $reference = null): bool
{
    // ensure wallet exists
    $stmt = $pdo->prepare("SELECT id FROM wallets WHERE user_id = ?");
    $stmt->execute([$user_id]);

    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("
            INSERT INTO wallets (user_id, balance, held_balance, currency)
            VALUES (?, 0.00, 0.00, 'NGN')
        ");
        $stmt->execute([$user_id]);
    }

    // update balance
    $stmt = $pdo->prepare("
        UPDATE wallets 
        SET balance = balance + ? 
        WHERE user_id = ?
    ");
    $stmt->execute([$amount, $user_id]);

    logTransaction($pdo, $user_id, 'deposit', $amount, $reference, 'success', 'Wallet funded');

    return true;
}

function holdFunds(PDO $pdo, int $user_id, float $amount, ?int $job_id = null): bool
{
    try {

        // 🔥 LOCK WALLET
        $stmt = $pdo->prepare("
            SELECT balance
            FROM wallets
            WHERE user_id = ?
            FOR UPDATE
        ");

        $stmt->execute([$user_id]);

        $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$wallet) {
            throw new Exception("Wallet not found");
        }

        if (round((float)$wallet['balance'], 2) < round($amount, 2)) {
            throw new Exception("Insufficient funds");
        }

        // 🔥 MOVE MONEY TO ESCROW
        $stmt = $pdo->prepare("
            UPDATE wallets
            SET
                balance = balance - ?,
                held_balance = held_balance + ?
            WHERE user_id = ?
        ");

        $stmt->execute([
            $amount,
            $amount,
            $user_id
        ]);

        // 🔥 LOG TRANSACTION
        logTransaction(
            $pdo,
            $user_id,
            'escrow_hold',
            $amount,
            null,
            'success',
            'Funds moved to escrow',
            $job_id
        );

        return true;
    } catch (Exception $e) {

        throw $e;
    }
}
function releaseFunds(PDO $pdo, int $payer_id, int $receiver_id, float $amount, ?int $job_id = null): bool
{
    // $pdo->beginTransaction();

    try {

        // 🔥 LOCK PAYER WALLET
        $stmt = $pdo->prepare("
            SELECT held_balance FROM wallets WHERE user_id = ? FOR UPDATE
        ");
        $stmt->execute([$payer_id]);
        $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$wallet) {
            throw new Exception("Wallet not found");
        }

        if ((float)$wallet['held_balance'] < $amount) {
            throw new Exception("Invalid escrow release");
        }

        // deduct escrow
        $stmt = $pdo->prepare("
            UPDATE wallets 
            SET held_balance = held_balance - ?
            WHERE user_id = ?
        ");
        $stmt->execute([$amount, $payer_id]);

        // 🔥 LOCK RECEIVER WALLET (optional but clean)
        $stmt = $pdo->prepare("
            SELECT balance FROM wallets WHERE user_id = ? FOR UPDATE
        ");
        $stmt->execute([$receiver_id]);

        // ensure exists
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare("
                INSERT INTO wallets (user_id, balance, held_balance, currency)
                VALUES (?, 0.00, 0.00, 'NGN')
            ");
            $stmt->execute([$receiver_id]);
        }
        $feePercentage = 5;

        $platformFee = round(
            ($amount * $feePercentage) / 100,
            2
        );

        $netAmount = round(
            $amount - $platformFee,
            2
        );
        // credit
        $stmt = $pdo->prepare("
            UPDATE wallets 
            SET balance = balance + ?
            WHERE user_id = ?
        ");
        $stmt->execute([$netAmount, $receiver_id]);

        logTransaction(
            $pdo,
            $payer_id,
            'escrow_release',
            $amount,
            null,
            'success',
            'Escrow payment sent',
            $job_id
            
        );

        logTransaction(
            $pdo,
            $receiver_id,
            'payment_received',
            $amount,
            null,
            'success',
            'Payment received from completed job',
            $job_id,
            $platformFee,
            $netAmount

        );
        createNotification(
            $pdo,
            $receiver_id,
            'payment_received',
            'Payment Received',
            '₦' .
                number_format($netAmount, 2) .
                ' credited after ₦' .
                number_format($platformFee, 2) .
                ' platform fee.',
            '../wallet/dashboard.php'
        );
        return true;
    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}
function getPendingEarnings(PDO $pdo, int $user_id): float
{
    $stmt = $pdo->prepare("
        SELECT SUM(amount) as pending
        FROM escrows
        WHERE freelancer_id = ?
        AND status = 'held'
    ");

    $stmt->execute([$user_id]);

    return (float) ($stmt->fetchColumn() ?? 0);
}

function requestWithdrawal(PDO $pdo, int $user_id, float $amount): int
{
    if ($amount < 1000) {
        throw new Exception("Minimum withdrawal is ₦1000");
    }

    $pdo->beginTransaction();

    try {

        // 🔥 LOCK WALLET ROW
        $stmt = $pdo->prepare("
            SELECT balance 
            FROM wallets 
            WHERE user_id = ? 
            FOR UPDATE
        ");

        $stmt->execute([$user_id]);

        $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$wallet) {
            throw new Exception("Wallet not found");
        }

        if ((float)$wallet['balance'] < $amount) {
            throw new Exception("Insufficient funds");
        }

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM withdrawals
            WHERE user_id = ?
            AND status IN ('pending', 'paid')
            AND created_at >= NOW() - INTERVAL 1 DAY
        ");

        $stmt->execute([$user_id]);

        $withdrawalCount = (int)$stmt->fetchColumn();

        if ($withdrawalCount >= 3) {

            flagSuspiciousActivity(
                $pdo,
                $user_id,
                'Too many withdrawal requests within 24 hours',
                'medium'
            );

            throw new Exception(
                "Too many withdrawal requests. Please try again later."
            );
        }
        // 🔥 RESERVE FUNDS IMMEDIATELY
        $stmt = $pdo->prepare("
            UPDATE wallets
            SET 
                balance = balance - ?,
                withdrawal_hold = withdrawal_hold + ?
            WHERE user_id = ?
        ");

        $stmt->execute([
            $amount,
            $amount,
            $user_id
        ]);

        $reference = 'WTH_' . date('YmdHis') . '_' . bin2hex(random_bytes(4));

        // 🔥 CREATE WITHDRAWAL RECORD
        $stmt = $pdo->prepare("
            INSERT INTO withdrawals (user_id, amount, reference)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $user_id,
            $amount,
            $reference
        ]);

        $withdrawal_id = (int)$pdo->lastInsertId();

        $pdo->commit();

        // 🔥 LOG TRANSACTION
        logTransaction(
            $pdo,
            $user_id,
            'withdrawal',
            $amount,
            $reference,
            'pending',
            'Withdrawal request submitted'
        );

        return $withdrawal_id;
    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}

function processWithdrawal(PDO $pdo, int $withdrawal_id, string $action): bool
{
    try {

        $pdo->beginTransaction();

        if (!in_array($action, ['approve', 'reject'])) {
            throw new Exception("Invalid action");
        }

        /* =========================
           LOCK WITHDRAWAL
        ========================= */

        $stmt = $pdo->prepare("
            SELECT * 
            FROM withdrawals 
            WHERE id = ? 
            FOR UPDATE
        ");

        $stmt->execute([$withdrawal_id]);

        $withdrawal = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$withdrawal) {
            throw new Exception("Withdrawal not found");
        }

        if ($withdrawal['status'] !== 'pending') {
            throw new Exception("Already processed");
        }

        /* =========================
           LOCK WALLET
        ========================= */

        $stmt = $pdo->prepare("
            SELECT *
            FROM wallets
            WHERE user_id = ?
            FOR UPDATE
        ");

        $stmt->execute([$withdrawal['user_id']]);

        $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$wallet) {
            throw new Exception("Wallet not found");
        }

        /* =========================
           APPROVE
        ========================= */

        if ($action === 'approve') {

            // REMOVE HELD MONEY

            $stmt = $pdo->prepare("
                UPDATE wallets
                SET withdrawal_hold = withdrawal_hold - ?
                WHERE user_id = ?
            ");

            $stmt->execute([
                $withdrawal['amount'],
                $withdrawal['user_id']
            ]);

            // UPDATE WITHDRAWAL

            $stmt = $pdo->prepare("
                UPDATE withdrawals
                SET 
                    status = 'paid',
                    processed_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([$withdrawal_id]);

            // UPDATE EXISTING TRANSACTION

            $stmt = $pdo->prepare("
                UPDATE transactions
                SET 
                    status = 'success',
                    description = 'Withdrawal successful'
                WHERE reference = ?
                AND type = 'withdrawal'
            ");

            $stmt->execute([
                $withdrawal['reference']
            ]);
        }

        /* =========================
           REJECT
        ========================= */ else {

            // RETURN MONEY

            $stmt = $pdo->prepare("
                UPDATE wallets
                SET 
                    withdrawal_hold = withdrawal_hold - ?,
                    balance = balance + ?
                WHERE user_id = ?
            ");

            $stmt->execute([
                $withdrawal['amount'],
                $withdrawal['amount'],
                $withdrawal['user_id']
            ]);

            // UPDATE WITHDRAWAL

            $stmt = $pdo->prepare("
                UPDATE withdrawals
                SET 
                    status = 'failed',
                    processed_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([$withdrawal_id]);

            // UPDATE EXISTING TRANSACTION

            $stmt = $pdo->prepare("
                UPDATE transactions
                SET 
                    status = 'failed',
                    description = 'Withdrawal rejected'
                WHERE reference = ?
                AND type = 'withdrawal'
            ");

            $stmt->execute([
                $withdrawal['reference']
            ]);
        }

        $pdo->commit();

        return true;
    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}
