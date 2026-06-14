<?php
session_start();
require_once "../config/database.php";

if (!isset($_GET['application_id'])) {
    exit;
}

$application_id = $_GET['application_id'];

$stmt = $pdo->prepare("
    SELECT *
    FROM messages
    WHERE application_id = ?
    ORDER BY created_at ASC
");

$stmt->execute([$application_id]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($messages as $msg):
?>

<div class="message <?= $msg['sender_id'] == $_SESSION['user_id'] ? 'message-sent' : 'message-received' ?>">

    <div class="message-content">

        <div class="message-bubble <?= $msg['sender_id'] == $_SESSION['user_id'] ? 'message-bubble-sent' : '' ?>">

            <p><?= htmlspecialchars($msg['message']) ?></p>

        </div>

        <p class="message-time">
            <?= date("H:i", strtotime($msg['created_at'])) ?>
        </p>

    </div>

</div>

<?php endforeach; ?>