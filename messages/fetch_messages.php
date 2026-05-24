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
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>

    <body>
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

    <script>
        const applicationId = <?= json_encode($activeApp['application_id'] ?? null) ?>;

        function fetchMessages() {
            if (!applicationId) return;

            fetch(`fetch_messages.php?application_id=${applicationId}`)
                .then(res => res.text())
                .then(data => {
                    const container = document.getElementById("messages-container");

                    // Only update if changed (prevents flicker)
                    if (container.innerHTML.trim() !== data.trim()) {
                        container.innerHTML = data;

                        // Auto-scroll to bottom
                        container.scrollTop = container.scrollHeight;
                    }
                })
                .catch(err => console.error("Fetch error:", err));
        }

        // Run every 2 seconds
        setInterval(fetchMessages, 2000);

        window.addEventListener("load", () => {
            const container = document.getElementById("messages-container");
            container.scrollTop = container.scrollHeight;
        });
    </script>
    </body>

    </html>