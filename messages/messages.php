<?php
session_start();
require_once "../config/database.php";
require_once "../includes/auth_guard.php";
require_once "../includes/notifications.php";

/* =========================
   AUTH CHECK
========================= */

if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    die("Unauthorized access");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if (!in_array($role, ['student', 'lecturer', 'company'])) {
    die("Unauthorized access");
}


/* =========================
   FETCH APPLICATIONS (ROLE + TABS LOGIC)
========================= */

if (isset($_GET['tab'])) {
    $_SESSION['tab'] = $_GET['tab'];
}

$tab = $_SESSION['tab'] ?? 'applied';

/*
|--------------------------------------------------------------------------
| LOCK TAB ACCESS
|--------------------------------------------------------------------------
| Only students (freelancers) can use tabs
| Employers/lecturers should ONLY see jobs they posted
*/
if ($role !== 'student') {
    $tab = 'posted';
}

/*
|--------------------------------------------------------------------------
| QUERY BASED ON TAB
|--------------------------------------------------------------------------
*/
if ($tab === 'posted') {

    // Jobs created by user (employer view OR student switching tab)
    $stmt = $pdo->prepare("
        SELECT 
            applications.id AS application_id,
            applications.student_id,
            applications.job_id,
            applications.status,

            jobs.title,
            jobs.created_by,
            jobs.budget,
            jobs.deadline,

            student_user.full_name AS student_name,
            employer_user.full_name AS employer_name

        FROM applications

        JOIN jobs 
        ON applications.job_id = jobs.id

        JOIN users AS student_user
        ON applications.student_id = student_user.id

        JOIN users AS employer_user
        ON jobs.created_by = employer_user.id

        WHERE jobs.created_by = ?
AND applications.status IN ('accepted', 'pending', 'in_progress', 'completed', 'submitted', 'rejected' )

        ORDER BY
COALESCE(
    (
        SELECT MAX(messages.created_at)
        FROM messages
        WHERE messages.application_id = applications.id
    ),
    applications.created_at
) DESC
    ");

    $stmt->execute([$user_id]);
} else {

    // Jobs user applied for (student only)
    $stmt = $pdo->prepare("
        SELECT 
            applications.id AS application_id,
            applications.student_id,
            applications.job_id,
            applications.status,

            jobs.title,
            jobs.created_by,
            jobs.budget,
            jobs.deadline,

            student_user.full_name AS student_name,
            employer_user.full_name AS employer_name

        FROM applications

        JOIN jobs 
        ON applications.job_id = jobs.id

        JOIN users AS student_user
        ON applications.student_id = student_user.id

        JOIN users AS employer_user
        ON jobs.created_by = employer_user.id

        WHERE applications.student_id = ?

     ORDER BY
COALESCE(
    (
        SELECT MAX(messages.created_at)
        FROM messages
        WHERE messages.application_id = applications.id
    ),
    applications.created_at
) DESC
    ");

    $stmt->execute([$user_id]);
}

$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| ATTACH VIEW CONTEXT (WHO IS THE OTHER PERSON)
|--------------------------------------------------------------------------
*/
foreach ($applications as &$app) {

    if ((int)$user_id === (int)$app['student_id']) {
        $app['other_party_name'] = $app['employer_name'];
        // $app['viewer_side'] = 'freelancer';
    } else {
        $app['other_party_name'] = $app['student_name'];
        // $app['viewer_side'] = 'employer';
    }

    // Get latest submission
    $stmt = $pdo->prepare("
        SELECT id, status, version
        FROM work_submissions
        WHERE application_id = ?
        ORDER BY version DESC
        LIMIT 1
    ");

    $stmt->execute([$app['application_id']]);
    $submission = $stmt->fetch(PDO::FETCH_ASSOC);

    $app['submission_id'] = $submission['id'] ?? null;
    $app['submission_status'] = $submission['status'] ?? null;
    $app['submission_version'] = $submission['version'] ?? null;
}
unset($app);
/* =========================
   ACTIVE APPLICATION
========================= */

$active_application_id = $_GET['application_id'] ?? null;
$activeApp = null;

if ($active_application_id) {

    // 🔥 Validate from DB (NOT from filtered list)
    $stmt = $pdo->prepare("
    SELECT 
        applications.id AS application_id,
        applications.student_id,
        applications.job_id,
        applications.status,

        jobs.title,
        jobs.created_by AS created_by,
        jobs.budget,
        jobs.deadline,

        student_user.full_name AS student_name,
        employer_user.full_name AS employer_name,

        escrows.status AS escrow_status

    FROM applications

    JOIN jobs ON applications.job_id = jobs.id

    JOIN users AS student_user
        ON applications.student_id = student_user.id

    JOIN users AS employer_user
        ON jobs.created_by = employer_user.id

    LEFT JOIN escrows
        ON escrows.job_id = jobs.id

    WHERE applications.id = ?
");

    $stmt->execute([$active_application_id]);
    $activeApp = $stmt->fetch(PDO::FETCH_ASSOC);



    $activeApp = $activeApp ?? [];

    if ($activeApp) {
        $stmt = $pdo->prepare("
        SELECT id, status
        FROM work_submissions
        WHERE application_id = ?
        ORDER BY version DESC
        LIMIT 1
    ");

        $stmt->execute([$activeApp['application_id']]);
        $submission = $stmt->fetch(PDO::FETCH_ASSOC);

        $activeApp['submission_id'] = $submission['id'] ?? null;
        $activeApp['submission_status'] = $submission['status'] ?? null;
    }

    if (!$activeApp) {
        die("No active conversation found");
    }

    // if ((int)$user_id === (int)$activeApp['student_id']) {
    //     $activeApp['other_party_name'] = $activeApp['employer_name'];
    // } else {
    //     $activeApp['other_party_name'] = $activeApp['student_name'];
    // }



    // 🔒 Ensure user belongs to this conversation
    if (
        $user_id != $activeApp['student_id'] &&
        $user_id != $activeApp['created_by']
    ) {
        die("Unauthorized access");
    }

    // 👤 DETERMINE CHAT PARTNER
    if ($user_id == $activeApp['student_id']) {
        $activeApp['other_party_name'] = $activeApp['employer_name'];
        $receiver_id = $activeApp['created_by'];
    } else {
        $activeApp['other_party_name'] = $activeApp['student_name'];
        $receiver_id = $activeApp['student_id'];
    }
} elseif (!$active_application_id && !empty($applications)) {

    // fallback
    $activeApp = $applications[0];

    if ($user_id == $activeApp['student_id']) {
        $receiver_id = $activeApp['created_by'];
    } else {
        $receiver_id = $activeApp['student_id'];
    }
}
if (!empty($activeApp) && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $message = trim($_POST['message']);
    if ($message === '') {
        die("Message cannot be empty");
    }
    if ($user_id == $activeApp['student_id']) {
        $receiver_id = $activeApp['created_by'];
    } else {
        $receiver_id = $activeApp['student_id'];
    }

    $stmt = $pdo->prepare("
    INSERT INTO messages (application_id, sender_id, receiver_id, message)
    VALUES (?, ?, ?, ?)
");

    $stmt->execute([
        $activeApp['application_id'],
        $user_id,
        $receiver_id,
        $message
    ]);

    /* =========================
   CREATE MESSAGE NOTIFICATION
========================= */

    $stmt = $pdo->prepare("
    SELECT full_name
    FROM users
    WHERE id = ?
");

    $stmt->execute([$user_id]);

    $sender = $stmt->fetch(PDO::FETCH_ASSOC);

    $sender_name = $sender['full_name'] ?? 'Someone';

    createNotification(
        $pdo,
        $receiver_id,
        'new_message',
        'New Message',
        'You received a new message from ' . $sender_name,
        '../messages/messages.php?application_id=' . $activeApp['application_id']
    );
    // prevent resubmission on refresh
    header("Location: ?application_id=" . $activeApp['application_id']);
    exit;
}
if (empty($activeApp)) {
    $messages = [];
} else {
    $stmt = $pdo->prepare("
        SELECT *
        FROM messages
        WHERE application_id = ?
        ORDER BY created_at ASC
    ");

    $stmt->execute([$activeApp['application_id']]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

<body class="<?= $active_application_id ? 'chat-open' : '' ?>">
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
                <?php if ($role === 'student'): ?>
                    <div class="tabs">

                        <!-- APPLIED TAB -->
                        <a href="?tab=applied<?= $active_application_id ? '&application_id=' . $active_application_id : '' ?>"
                            class="tab <?= ($tab === 'applied') ? 'active-tab' : '' ?>">
                            Applied
                        </a>

                        <!-- POSTED TAB -->
                        <a href="?tab=posted<?= $active_application_id ? '&application_id=' . $active_application_id : '' ?>"
                            class="tab <?= ($tab === 'posted') ? 'active-tab' : '' ?>">
                            Posted
                        </a>

                    </div>
                <?php endif; ?>
            </div>

            <!-- Conversations -->
            <div class="conversations-scroll">


                <!-- Conversation 2 -->
                <?php foreach ($applications as $app): ?>

                    <a href="?application_id=<?= $app['application_id'] ?>&tab=<?= $tab ?>" style="text-decoration: none;"
                        class="conversation-item <?= ($activeApp['application_id'] == $app['application_id']) ? 'active' : '' ?>">

                        <div class="conversation-avatar lecturer-avatar">
                            <!-- SVG -->
                        </div>

                        <div class="conversation-content">

                            <div class="conversation-header">
                                <h3 class="conversation-name">
                                    <?= htmlspecialchars($app['other_party_name']) ?>
                                </h3>
                            </div>

                            <p class="conversation-project-inactive">
                                <?= htmlspecialchars($app['title']) ?>
                            </p>

                            <!-- <p class="conversation-preview">
                                Thank you for the excellent work!
                            </p> -->

                            <!-- STATUS -->
                            <p class="text-sm mt-1">
                                <?php if ($app['submission_status'] === 'revision_requested'): ?>
                                    <span class="badge badge-orange">Revision Requested</span>

                                <?php elseif ($app['submission_status'] === 'accepted'): ?>
                                    <span class="badge badge-green">Accepted</span>

                                <?php elseif ($app['submission_status'] === 'submitted'): ?>
                                    <span class="badge badge-blue">Awaiting Review</span>

                                <?php else: ?>
                                    <span class="badge badge-gray">No submission yet</span>
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
            <div class="overlay"></div>
            <!-- Conversation Header -->
            <div class="chat-header">
                <div class="chat-participant">
                    <a href="messages.php" class="back-button">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4B2E83" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5" />
                            <path d="M12 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="chat-participant-avatar">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                            <path d="M3 9h18"></path>
                            <path d="M9 21V9"></path>
                        </svg>
                    </div>
                    <div class="chat-participant-info">
                        <h2 class="chat-participant-name"><?= htmlspecialchars($activeApp['other_party_name'] ?? 'Unknown User') ?></h2>
                        <div class="chat-project-info">
                            <p class="chat-project-title"><?= htmlspecialchars($activeApp['title'] ?? 'No Title') ?></p>
                        </div>
                    </div>
                    <button class="details-toggle">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1F2933" stroke-width="2" stroke-linecap="round">
                            <circle cx="12" cy="5" r="1" />
                            <circle cx="12" cy="12" r="1" />
                            <circle cx="12" cy="19" r="1" />
                        </svg>
                    </button>
                </div>

            </div>

            <!-- Messages Area -->
            <div class="messages-area" style="padding-top: 1rem;" id="messages-container">
                <!-- Message from other user -->
                <?php if (!$activeApp): ?>

                    <!-- NO APPLICATION / NO CONVERSATION -->
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center" style="padding: 1rem;">
                            <h2 class="text-xl font-semibold text-gray-700">No conversations yet</h2>
                            <p class="text-gray-500 mt-2">
                                When someone responds to your job, your messages will appear here.
                            </p>
                        </div>
                    </div>

                <?php elseif (empty($messages)): ?>

                    <!-- APPLICATION EXISTS BUT NO MESSAGES -->
                    <div class="flex items-center justify-center h-full">
                        <p class="text-gray-500">No messages in this conversation yet.</p>
                    </div>

                <?php else: ?>



                    <?php foreach ($messages as $msg): ?>

                        <div class="message <?= $msg['sender_id'] == $user_id ? 'message-sent' : 'message-received' ?>" style="padding: 0.2rem 1rem;">
                            <!-- <div class="message-avatar message-avatar-sent">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div> -->
                            <div class="message-content">
                                <div class="message-bubble <?= $msg['sender_id'] == $user_id ? 'message-bubble-sent' : '' ?>">
                                    <p><?= htmlspecialchars($msg['message']) ?></p>
                                </div>
                                <p class="message-time">
                                    <?= date("H:i", strtotime($msg['created_at'])) ?>
                                </p>
                            </div>

                        </div>

                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

            <!-- Message Input Area -->
            <form method="POST" class="message-input-area">

                <!-- <button type="button" class="attachment-button">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                    </svg>
                </button> -->

                <div class="input-wrapper">
                    <input
                        type="text"
                        name="message"
                        placeholder="Type your message..."
                        class="message-input"
                        required>
                </div>

                <button type="submit" class="send-button">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    Send
                </button>

            </form>
            <!-- Mobile details -->
            <div class="mobile-details mobile-details-header">
                <div class="closeflex">
                    <h3 class="sidebar-title">Project Details</h3>
                    <button class="close-details">✕</button>

                </div>
                <?php if (!empty($activeApp)): ?>
                    <!-- Project Title -->
                    <div class="detail-section">
                        <label class="detail-label">PROJECT</label>
                        <p class="detail-value"><?= htmlspecialchars($activeApp['title'] ?? 'No Title') ?></p>
                    </div>

                    <!-- Budget -->
                    <div class="detail-section">
                        <label class="detail-label">BUDGET</label>
                        <div class="detail-with-icon">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="6" y1="3" x2="6" y2="21"></line>
                                <line x1="18" y1="3" x2="18" y2="21"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                <line x1="4" y1="10" x2="20" y2="10"></line>
                                <line x1="4" y1="14" x2="20" y2="14"></line>
                            </svg>
                            <p class="detail-value"> <?= number_format($activeApp['budget']) ?></p>
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
                            <p class="detail-value"><?= date("M d, Y", strtotime($activeApp['deadline'])) ?></p>
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
                            <?php
                            $escrowStatus = $activeApp['escrow_status'] ?? 'none';

                            if ($escrowStatus === 'held') {
                                $label = "Funds Held";
                                $desc = "Held securely in escrow";
                                $color = "text-yellow-600";
                            } elseif ($escrowStatus === 'released') {
                                $label = "Released";
                                $desc = "Funds have been paid to freelancer";
                                $color = "text-green-600";
                            } else {
                                $label = "Not Available";
                                $desc = "No escrow record found";
                                $color = "text-gray-500";
                            }
                            ?>
                            <p class="detail-value <?= $color ?>">
                                <?= $label ?>
                            </p>
                        </div>
                        <p class="detail-description">
                            <?= $desc ?>
                        </p>
                    </div>

                    <!-- Job Status -->
                    <div class="detail-section">
                        <label class="detail-label">JOB STATUS</label>
                        <div class="detail-with-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="active-icon">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span class="status-badge-active"><?= htmlspecialchars($activeApp['status']) ?></span>
                        </div>
                    </div>
                <?php else: ?>

                    <div class="text-center text-gray-500">
                        <p>No project selected</p>
                    </div>

                <?php endif; ?>

                <!-- Action Button -->

                <?php if ($activeApp): ?>

                    <?php if (
                        $user_id == $activeApp['student_id'] &&
                        $activeApp['status'] !== 'completed' &&
                        (
                            empty($activeApp['submission_status']) ||
                            $activeApp['submission_status'] === 'revision_requested'
                        )
                    ): ?>
                        <div class="view-project-button" style="margin-top: 10px;">
                            <a href="../applications/submit_work.php?application_id=<?= $activeApp['application_id'] ?>&return=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                                style="text-decoration: none; text-align: center; display: block; padding: 0.75rem 1rem; background-color: #4B2E83; color: white; border-radius: 0.375rem;">
                                Submit Work
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (
                        $user_id == $activeApp['created_by'] &&
                        !empty($activeApp['submission_id']) &&
                        $activeApp['submission_status'] !== 'accepted'
                    ): ?>
                        <div class="view-project-button" style="margin-top: 10px;">
                            <a href="../applications/review_submission.php?submission_id=<?= $activeApp['submission_id'] ?>&return=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                                style="text-decoration: none; text-align: center; display: block; padding: 0.75rem 1rem; background-color: #4B2E83; color: white; border-radius: 0.375rem;">
                                Review Submission
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if (
                        $activeApp &&
                        $activeApp['status'] === 'completed' &&
                        $activeApp['submission_status'] === 'accepted'
                    ): ?>
                        <div class="view-project-button" style="margin-top: 10px;">
                            <a href="../applications/view_submissions.php?submission_id=<?= $activeApp['submission_id'] ?>&return=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                                style="text-decoration: none; text-align: center; display: block; padding: 0.75rem 1rem; background-color: #16aa4a; color: white; border-radius: 0.375rem;">
                                View Work
                            </a>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>


            </div>
        </div>

        <!-- Context Sidebar - Project Details -->
        <div class="context-sidebar">
            <h3 class="sidebar-title">Project Details</h3>

            <?php if (!empty($activeApp)): ?>
                <!-- Project Title -->
                <div class="detail-section">
                    <label class="detail-label">PROJECT</label>
                    <p class="detail-value"><?= htmlspecialchars($activeApp['title'] ?? 'No Title') ?></p>
                </div>

                <!-- Budget -->
                <div class="detail-section">
                    <label class="detail-label">BUDGET</label>
                    <div class="detail-with-icon">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="6" y1="3" x2="6" y2="21"></line>
                            <line x1="18" y1="3" x2="18" y2="21"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                            <line x1="4" y1="10" x2="20" y2="10"></line>
                            <line x1="4" y1="14" x2="20" y2="14"></line>
                        </svg>
                        <p class="detail-value"> <?= number_format($activeApp['budget']) ?></p>
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
                        <p class="detail-value"><?= date("M d, Y", strtotime($activeApp['deadline'])) ?></p>
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
                        <?php
                        $escrowStatus = $activeApp['escrow_status'] ?? 'none';

                        if ($escrowStatus === 'held') {
                            $label = "Funds Held";
                            $desc = "Held securely in escrow";
                            $color = "text-yellow-600";
                        } elseif ($escrowStatus === 'released') {
                            $label = "Released";
                            $desc = "Funds have been paid to freelancer";
                            $color = "text-green-600";
                        } else {
                            $label = "Not Available";
                            $desc = "No escrow record found";
                            $color = "text-gray-500";
                        }
                        ?>
                        <p class="detail-value <?= $color ?>">
                            <?= $label ?>
                        </p>

                    </div>
                    <p class="detail-description">
                        <?= $desc ?>
                    </p>
                </div>

                <!-- Job Status -->
                <div class="detail-section">
                    <label class="detail-label">JOB STATUS</label>
                    <div class="detail-with-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="active-icon">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <span class="status-badge-active"><?= htmlspecialchars($activeApp['status']) ?></span>
                    </div>
                </div>
            <?php else: ?>

                <div class="text-center text-gray-500">
                    <p>No project selected</p>
                </div>

            <?php endif; ?>
            <!-- Action Button -->
            <?php if ($activeApp): ?>
                <?php
                $returnUrl = isset($_SERVER['REQUEST_URI']) ? urlencode($_SERVER['REQUEST_URI']) : '';
                $submissionId = isset($activeApp['submission_id']) ? $activeApp['submission_id'] : '';
                $applicationId = $activeApp['application_id'] ?? '';
                ?>


                <?php if (
                    $user_id == $activeApp['student_id'] &&
                    $activeApp['status'] !== 'completed' &&
                    (
                        empty($activeApp['submission_status']) ||
                        $activeApp['submission_status'] === 'revision_requested'
                    )
                ): ?>
                    <div class="view-project-button" style="margin-top: 10px;">
                        <a href="../applications/submit_work.php?application_id=<?= $activeApp['application_id'] ?>&return=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                            style="text-decoration: none; text-align: center; display: block; padding: 0.75rem 1rem; background-color: #4B2E83; color: white; border-radius: 0.375rem;">
                            Submit Work
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (
                    $user_id == $activeApp['created_by'] &&
                    !empty($activeApp['submission_id']) &&
                    $activeApp['submission_status'] !== 'accepted'
                ): ?>
                    <div class="view-project-button" style="margin-top: 10px;">
                        <a href="../applications/review_submission.php?submission_id=<?= $activeApp['submission_id'] ?>&return=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                            style="text-decoration: none; text-align: center; display: block; padding: 0.75rem 1rem; background-color: #4B2E83; color: white; border-radius: 0.375rem;">
                            Review Submission
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (
                    $activeApp &&
                    $activeApp['status'] === 'completed'
                ): ?>
                    <div class="view-project-button" style="margin-top: 10px;">
                        <a href="../applications/view_submissions.php?submission_id=<?= $activeApp['submission_id'] ?>&return=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                            style="text-decoration: none; text-align: center; display: block; padding: 0.75rem 1rem; background-color: #4B2E83; color: white; border-radius: 0.375rem;">
                            View Work
                        </a>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.querySelector('.details-toggle');
            const details = document.querySelector('.mobile-details');
            const closeBtn = document.querySelector('.close-details');
            const overlay = document.querySelector('.overlay');

            toggleBtn?.addEventListener('click', function() {
                details.classList.toggle('active');
                overlay.classList.toggle('active');
            });

            closeBtn?.addEventListener('click', function() {
                details.classList.remove('active');
                overlay.classList.remove('active');
            });

            overlay?.addEventListener('click', function() {
                details.classList.remove('active');
                overlay.classList.remove('active');
            });
        });
    </script>
</body>

</html>