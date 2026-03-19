<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_guard.php";

/* =========================
   AUTH CHECK
========================= */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    die("Unauthorized access");
}

$student_id = $_SESSION['user_id'];



/* =========================
   FETCH APPLICATIONS
========================= */

$stmt = $pdo->prepare("
SELECT 
    applications.id AS application_id,
    applications.job_id,
    applications.status,

    jobs.title,
    jobs.created_by,

    users.full_name AS employer_name

FROM applications

JOIN jobs 
ON applications.job_id = jobs.id

JOIN users 
ON jobs.created_by = users.id

WHERE applications.student_id = ?

ORDER BY applications.created_at DESC
");

$stmt->execute([$student_id]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   ATTACH LATEST SUBMISSIONS
========================= */

foreach ($applications as &$app) {

    $stmt = $pdo->prepare("
        SELECT id, status, version
        FROM work_submissions
        WHERE application_id = ?
        ORDER BY version DESC
        LIMIT 1
    ");

    $stmt->execute([$app['application_id']]);
    $submission = $stmt->fetch(PDO::FETCH_ASSOC);

    // Attach submission data to each application
    $app['submission_id'] = $submission['id'] ?? null;
    $app['submission_status'] = $submission['status'] ?? null;
    $app['submission_version'] = $submission['version'] ?? null;
}

unset($app); // avoid reference issues
$active_application_id = $_GET['application_id'] ?? null;

if ($active_application_id) {

    $valid = false;

    foreach ($applications as $app) {
        if ($app['application_id'] == $active_application_id) {
            $valid = true;
            break;
        }
    }

    if (!$valid) {
        die("Invalid application");
    }
}

$activeApp = null;

foreach ($applications as $app) {
    if ($app['application_id'] == $active_application_id) {
        $activeApp = $app;
        break;
    }
}

if (!$activeApp && !empty($applications)) {
    $activeApp = $applications[0];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - CampusLink</title>
    <link rel="stylesheet" href="messages.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Top Navigation -->
    <?php
    $activePage = "messages";
    include "../includes/employer_nav.php";
    ?>

    <!-- Messaging Layout -->
    <div class="messaging-layout">
        <!-- Left Panel - Conversation List -->
        <div class="conversation-list-panel">
            <!-- Header -->
            <div class="conversations-header">
                <h2 class="conversations-title">Messages</h2>
                <p class="conversations-subtitle">Project conversations</p>
            </div>

            <!-- Conversations -->
            <div class="conversations-scroll">
               

                <!-- Conversation 2 -->
                <?php foreach ($applications as $app): ?>

                    <a href="?application_id=<?= $app['application_id'] ?>" style="text-decoration: none;"
                        class="conversation-item <?= ($activeApp['application_id'] == $app['application_id']) ? 'active' : '' ?>">

                        <div class="conversation-avatar lecturer-avatar">
                            <!-- SVG -->
                        </div>

                        <div class="conversation-content">

                            <div class="conversation-header">
                                <h3 class="conversation-name">
                                    <?= htmlspecialchars($app['employer_name']) ?>
                                </h3>
                            </div>

                            <p class="conversation-project-inactive">
                                <?= htmlspecialchars($app['title']) ?>
                            </p>

                            <p class="conversation-preview">
                                Thank you for the excellent work!
                            </p>

                            <!-- STATUS -->
                            <p class="text-sm mt-1">
                                <?php if ($app['submission_status'] === 'revision_requested'): ?>
                                    <span class="text-orange-500">Revision Requested</span>
                                <?php elseif ($app['submission_status'] === 'accepted'): ?>
                                    <span class="text-green-600">Accepted</span>
                                <?php elseif ($app['submission_status'] === 'submitted'): ?>
                                    <span class="text-blue-500">Awaiting Review</span>
                                <?php else: ?>
                                    <span class="text-gray-400">No submission yet</span>
                                <?php endif; ?>
                            </p>

                        </div>

                    </a>

                <?php endforeach; ?>



                <!-- Conversation 4 -->



            </div>
        </div>

        <!-- Right Panel - Chat Window -->
        <div class="chat-window">
            <!-- Conversation Header -->
            <div class="chat-header">
                <div class="chat-participant">
                    <div class="chat-participant-avatar">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                            <path d="M3 9h18"></path>
                            <path d="M9 21V9"></path>
                        </svg>
                    </div>
                    <div class="chat-participant-info">
                        <h2 class="chat-participant-name">Tech Solutions Inc.</h2>
                        <div class="chat-project-info">
                            <p class="chat-project-title">Website Redesign for Student Portal</p>
                            <span class="job-status-badge">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="messages-area">
                <!-- Message from other user -->
                <div class="message message-received">
                    <div class="message-avatar">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                            <path d="M3 9h18"></path>
                            <path d="M9 21V9"></path>
                        </svg>
                    </div>
                    <div class="message-content">
                        <div class="message-bubble">
                            <p>Hi! Thanks for applying to our project. We're excited to work with you on this student portal redesign.</p>
                        </div>
                        <p class="message-time">10:32 AM</p>
                    </div>
                </div>

                <!-- Message from current user -->
                <div class="message message-sent">
                    <div class="message-avatar message-avatar-sent">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="message-content">
                        <div class="message-bubble message-bubble-sent">
                            <p>Thank you! I'm looking forward to this project. I've reviewed the requirements and have some initial questions about the design system.</p>
                        </div>
                        <p class="message-time">10:35 AM</p>
                    </div>
                </div>

                <!-- Message from other user -->
                <div class="message message-received">
                    <div class="message-avatar">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                            <path d="M3 9h18"></path>
                            <path d="M9 21V9"></path>
                        </svg>
                    </div>
                    <div class="message-content">
                        <div class="message-bubble">
                            <p>Of course! Feel free to ask. We have brand guidelines that I can share with you. The main colors are based on our university branding.</p>
                        </div>
                        <p class="message-time">10:38 AM</p>
                    </div>
                </div>

                <!-- Message from current user -->
                <div class="message message-sent">
                    <div class="message-avatar message-avatar-sent">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="message-content">
                        <div class="message-bubble message-bubble-sent">
                            <p>That would be perfect. Also, do you have any specific accessibility requirements beyond WCAG 2.1 AA?</p>
                        </div>
                        <p class="message-time">10:42 AM</p>
                    </div>
                </div>

                <!-- Message from other user -->
                <div class="message message-received">
                    <div class="message-avatar">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                            <path d="M3 9h18"></path>
                            <path d="M9 21V9"></path>
                        </svg>
                    </div>
                    <div class="message-content">
                        <div class="message-bubble">
                            <p>WCAG 2.1 AA is our baseline. We also need to ensure keyboard navigation works perfectly and screen reader compatibility is excellent. I'll send you the complete guidelines document.</p>
                            <p style="margin-top: 0.75rem;">When do you think you can have the initial wireframes ready?</p>
                        </div>
                        <p class="message-time">10:45 AM</p>
                    </div>
                </div>

                <!-- Message from current user -->
                <div class="message message-sent">
                    <div class="message-avatar message-avatar-sent">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="message-content">
                        <div class="message-bubble message-bubble-sent">
                            <p>Great! I'll send the final wireframes by end of this week. That should give you enough time to review before we move to high-fidelity designs.</p>
                        </div>
                        <p class="message-time">Just now</p>
                    </div>
                </div>
            </div>

            <!-- Message Input Area -->
            <div class="message-input-area">
                <button class="attachment-button">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                    </svg>
                </button>
                <div class="input-wrapper">
                    <input type="text" placeholder="Type your message..." class="message-input">
                </div>
                <button class="send-button">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    Send
                </button>
            </div>
        </div>

        <!-- Context Sidebar - Project Details -->
        <div class="context-sidebar">
            <h3 class="sidebar-title">Project Details</h3>

            <!-- Project Title -->
            <div class="detail-section">
                <label class="detail-label">PROJECT</label>
                <p class="detail-value">Website Redesign for Student Portal</p>
            </div>

            <!-- Budget -->
            <div class="detail-section">
                <label class="detail-label">BUDGET</label>
                <div class="detail-with-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                    <p class="detail-value">$550.00</p>
                </div>
            </div>

            <!-- Deadline -->
            <div class="detail-section">
                <label class="detail-label">DEADLINE</label>
                <div class="detail-with-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <p class="detail-value">Jan 20, 2026</p>
                </div>
            </div>

            <!-- Escrow Status -->
            <div class="detail-section">
                <label class="detail-label">ESCROW STATUS</label>
                <div class="detail-with-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="escrow-icon">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <p class="detail-value">Funds Held</p>
                </div>
                <p class="detail-description">Released upon completion</p>
            </div>

            <!-- Job Status -->
            <div class="detail-section">
                <label class="detail-label">JOB STATUS</label>
                <div class="detail-with-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="active-icon">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span class="status-badge-active">Active</span>
                </div>
            </div>

            <!-- Action Button -->

            <a href="../applications/review_submission.php?submission_id=<?= $app['submission_id'] ?>&return=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                class="px-3 py-2 border border-green-500 text-green-600 rounded-lg text-sm hover:bg-green-50">

                Review Submission

            </a>

        </div>
    </div>
</body>

</html>