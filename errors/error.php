<?php
$message = $_GET['message'] ?? "Something went wrong";
$return = $_GET['return'] ?? null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Error</title>
    <link rel="stylesheet" href="../dist/output.css">
</head>
<body class="bg-[#F7F8FA] flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow text-center max-w-md">

        <h2 class="text-xl font-semibold text-red-600 mb-3">
            ⚠️ Error
        </h2>

        <p class="text-gray-600 mb-6">
            <?= htmlspecialchars($message) ?>
        </p>

        <?php if ($return): ?>
            <a href="<?= htmlspecialchars($return) ?>"
               class="px-4 py-2 bg-[#4B2E83] text-white rounded-lg">
               Go Back
            </a>
        <?php else: ?>
            <button onclick="history.back()"
                class="px-4 py-2 bg-[#4B2E83] text-white rounded-lg">
                Go Back
            </button>
        <?php endif; ?>

    </div>

</body>
</html>