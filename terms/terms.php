<?php
session_start();

require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("
        UPDATE users
        SET
            terms_accepted = 1,
            terms_accepted_at = NOW()
        WHERE id = ?
    ");

    $stmt->execute([
        $_SESSION['user_id']
    ]);

    $redirect =
    $_SESSION['after_terms']
    ?? "../auth/post_login_redirect.php";

    unset($_SESSION['after_terms']);

    header("Location: $redirect");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentLancer Terms</title>

    <link href="../dist/output.css" rel="stylesheet">
</head>

<body class="min-h-screen bg-linear-to-r from-blue-50 via-white to-purple-50 flex items-center justify-center p-6">

    <div class="w-full max-w-3xl bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">

        <!-- Header -->

        <div class="bg-linear-to-r from-blue-500 to-purple-600 p-8 text-black">

            <h1 class="text-3xl font-bold">
                StudentLancer Terms & Conditions
            </h1>

            <p class="mt-2 text-blue-100">
                Please review and accept before continuing.
            </p>

        </div>

        <!-- Summary -->

        <div class="p-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">

                <div class="flex items-center gap-3 p-4 rounded-xl bg-blue-50">
                    <span class="text-green-600 text-xl">✓</span>
                    <span>5% Platform Fee</span>
                </div>

                <div class="flex items-center gap-3 p-4 rounded-xl bg-purple-50">
                    <span class="text-green-600 text-xl">✓</span>
                    <span>Escrow Protected Payments</span>
                </div>

                <div class="flex items-center gap-3 p-4 rounded-xl bg-blue-50">
                    <span class="text-green-600 text-xl">✓</span>
                    <span>Dispute Resolution System</span>
                </div>

                <div class="flex items-center gap-3 p-4 rounded-xl bg-purple-50">
                    <span class="text-green-600 text-xl">✓</span>
                    <span>Verified University Accounts</span>
                </div>

                <div class="flex items-center gap-3 p-4 rounded-xl bg-blue-50">
                    <span class="text-green-600 text-xl">✓</span>
                    <span>Secure Withdrawals</span>
                </div>

                <div class="flex items-center gap-3 p-4 rounded-xl bg-purple-50">
                    <span class="text-green-600 text-xl">✓</span>
                    <span>Ratings & Reviews</span>
                </div>

            </div>

            <!-- Expandable Terms -->

            <button
                onclick="toggleTerms()"
                class="w-full flex justify-between items-center p-4 rounded-xl bg-gray-100 hover:bg-gray-200 transition">

                <span class="font-semibold">
                    View Full Terms & Conditions
                </span>

                <span id="arrow">
                    ▼
                </span>

            </button>

            <div
                id="termsContent"
                class="hidden mt-4 border rounded-xl p-6 bg-gray-50 max-h-96 overflow-y-auto">

                <h2 class="font-bold mb-4">
                    StudentLancer Terms & Conditions
                </h2>

                <p class="mb-4">
                    By using StudentLancer, you agree to comply with all platform rules and policies.
                </p>

                <ul class="space-y-3 text-gray-700">

                    <li>
                        • StudentLancer charges a 5% platform fee on every completed project.
                    </li>

                    <li>
                        • Payments are secured through the StudentLancer escrow system.
                    </li>

                    <li>
                        • Funds are only released after approval or dispute resolution.
                    </li>

                    <li>
                        • Disputes may result in full payment, refund, or partial compensation.
                    </li>

                    <li>
                        • Freelancers who demonstrate substantial effort during a dispute may receive up to 15% compensation.
                    </li>

                    <li>
                        • Fraudulent activity may lead to suspension or permanent account termination.
                    </li>

                    <li>
                        • Ratings and reviews must be honest and professional.
                    </li>

                    <li>
                        • AI recommendations are advisory and do not guarantee employment opportunities.
                    </li>

                    <li>
                        • Users must maintain accurate profile information.
                    </li>

                    <li>
                        • StudentLancer reserves the right to update these terms when necessary.
                    </li>

                </ul>

            </div>

            <!-- Form -->

            <form method="POST" class="mt-8">

                <label class="flex items-start gap-3 mb-6">

                    <input
                        type="checkbox"
                        id="agree"
                        required
                        onchange="toggleButton()"
                        class="mt-1">

                    <span class="text-gray-700">
                        I have read and agree to the StudentLancer Terms & Conditions.
                    </span>

                </label>

                <button
                    type="submit"
                    id="acceptBtn"
                    class="w-full py-4 rounded-xl bg-gray-300 text-black font-semibold cursor-not-allowed transition">

                    Accept & Continue

                </button>

            </form>
            

        </div>

    </div>

    <script>

        function toggleTerms() {

            const content =
                document.getElementById('termsContent');

            const arrow =
                document.getElementById('arrow');

            content.classList.toggle('hidden');

            arrow.textContent =
                content.classList.contains('hidden')
                ? '▼'
                : '▲';
        }

        function toggleButton() {

            const checkbox =
                document.getElementById('agree');

            const button =
                document.getElementById('acceptBtn');

            if (checkbox.checked) {

                button.disabled = false;

                button.classList.remove(
                    'bg-gray-300',
                    'cursor-not-allowed'
                );

                button.classList.add(
                    'bg-purple-600',
                    'hover:bg-purple-700'
                );

            } else {

                button.disabled = true;

                button.classList.remove(
                    'bg-purple-600',
                    'hover:bg-purple-700'
                );

                button.classList.add(
                    'bg-gray-300',
                    'cursor-not-allowed'
                );
            }
        }

    </script>

</body>

</html>