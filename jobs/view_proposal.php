<?php
session_start();

require_once "../config/database.php";
require_once "../includes/auth_guard.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$user_id = $_SESSION['user_id'];

/* VALIDATE PROPOSAL ID */

if (!isset($_GET['proposal_id'])) {
    die("Proposal not specified");
}

$proposal_id = $_GET['proposal_id'];

/* FETCH PROPOSAL + JOB + STUDENT PROFILE */

$stmt = $pdo->prepare("
SELECT
applications.id,
applications.bid_amount,
applications.estimated_delivery_days,
applications.cover_letter_file,
applications.sample_file,
applications.status,
applications.job_id,

users.full_name,
users.email,

student_profiles.department,
student_profiles.level,
student_profiles.portfolio_link,

jobs.title,
jobs.created_by

FROM applications

JOIN users
ON applications.student_id = users.id

LEFT JOIN student_profiles
ON users.id = student_profiles.user_id

JOIN jobs
ON applications.job_id = jobs.id

WHERE applications.id = ?
");

$stmt->execute([$proposal_id]);

$proposal = $stmt->fetch();

if (!$proposal) {
    die("Proposal not found");
}

/* SECURITY CHECK
   Only the job creator can view proposals */

if ($proposal['created_by'] != $user_id) {
    die("Unauthorized proposal access");
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal Review - University Freelancing Platform</title>
    <link rel="stylesheet" href="../dist/output.css">
    <link rel="stylesheet" href="view_proposals.css">
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <div class="page-wrapper">
        <!-- Page Header -->
        <header class="page-header">
            <div class="container">
                <a href="view_applications.php?job_id=<?= $proposal['job_id'] ?>" class="back-button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back to previous page
                </a>
                <div class="header-content">
                    <h1>Proposal for: <?= htmlspecialchars($proposal['title']) ?></h1>
                    <!-- <p class="subtitle">Project: Mobile App Development</p> -->
                </div>
            </div>

        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <div class="layout-grid">
                    <!-- Left Column - Main Content -->
                    <div class="main-column">
                        <!-- Candidate Card -->
                        <div class="card candidate-card">
                            <!-- Candidate Header -->
                            <div class="candidate-header">
                                <div class="avatar-wrapper">
                                    <img
                                        src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=120&h=120&fit=crop"
                                        alt="Candidate Avatar"
                                        class="avatar" />
                                    <!-- <div class="verification-badge">
                                        <i data-lucide="check-circle"></i>
                                    </div> -->
                                </div>

                                <div class="candidate-info">
                                    <div class="info-header">
                                        <div>
                                            <h2><?= htmlspecialchars($proposal['full_name']) ?></h2>
                                            <div class="badges">
                                                <span class="badge badge-student">Student</span>
                                                <span class="badge badge-verified">
                                                    <i data-lucide="check-circle"></i>
                                                    University Verified
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="candidate-details">
                                        <p class="detail-primary">Computer Science, 300 Level</p>
                                        <p class="detail-secondary">University of Lagos</p>
                                        <p class="detail-secondary"> <?= htmlspecialchars($proposal['email']) ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Bid and Details Section -->
                            <div class="details-section">
                                <!-- Bid Amount -->
                                <div class="bid-card">
                                    <div class="bid-header">
                                        <i data-lucide="dollar-sign"></i>
                                        <span>Bid Amount</span>
                                    </div>
                                    <p class="bid-amount">₦<?= number_format($proposal['bid_amount']) ?></p>
                                </div>

                                <!-- Estimated Delivery -->
                                <div class="info-box">
                                    <div class="info-icon">
                                        <i data-lucide="clock"></i>
                                    </div>
                                    <div class="info-content">
                                        <p class="info-label">Estimated Delivery</p>
                                        <p class="info-value"> <?= $proposal['estimated_delivery_days']
                                                                    ? htmlspecialchars($proposal['estimated_delivery_days']) . " days"
                                                                    : "Not specified" ?></p>
                                    </div>
                                </div>

                                <!-- Portfolio Button -->
                                <?php if (!empty($proposal['portfolio_link'])): ?>

                                    <a href="<?= htmlspecialchars($proposal['portfolio_link']) ?>"
                                        target="_blank"
                                        class="action-button block">

                                        <div class="action-button-content">

                                            <div class="action-icon">
                                                <i data-lucide="briefcase"></i>
                                            </div>

                                            <div class="action-text">
                                                <p class="action-title">View Portfolio</p>
                                                <p class="action-subtitle">See previous work and projects</p>
                                            </div>

                                        </div>

                                        <i data-lucide="arrow-right" class="action-arrow"></i>

                                    </a>

                                <?php endif; ?>

                                <!-- Cover Letter Button -->
                                <?php if (!empty($proposal['cover_letter_file'])): ?>

                                    <a href="../uploads/cover_letters/<?= htmlspecialchars($proposal['cover_letter_file']) ?>"
                                        target="_blank"
                                        class="action-button block" style="text-decoration: none;">

                                        <div class="action-button-content">

                                            <div class="action-icon">
                                                <i data-lucide="file-text"></i>
                                            </div>

                                            <div class="action-text">
                                                <p class="action-title">Open Cover Letter (PDF)</p>
                                                <p class="action-subtitle">Read the applicant's cover letter</p>
                                            </div>

                                        </div>

                                        <i data-lucide="arrow-right" class="action-arrow"></i>

                                    </a>

                                <?php endif; ?>

                                <!-- Sample Work Button -->
                                <?php if (!empty($proposal['sample_file'])): ?>

                                    <a href="../uploads/samples/<?= htmlspecialchars($proposal['sample_file']) ?>"
                                        target="_blank"
                                        class="action-button block" style="text-decoration: none;">

                                        <div class="action-button-content">

                                            <div class="action-icon">
                                                <i data-lucide="image"></i>
                                            </div>

                                            <div class="action-text">
                                                <p class="action-title no-underline">View Sample Work</p>
                                                <p class="action-subtitle">Preview the student's work sample</p>
                                            </div>

                                        </div>

                                        <i data-lucide="arrow-right" class="action-arrow"></i>

                                    </a>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Sidebar -->
                    <div class="sidebar-column flex flex-col gap-4">
                        <!-- Applicant Stats Card -->
                        <div class="card stats-card">
                            <h3>Applicant Stats</h3>

                            <div class="stats-list">
                                <!-- Completed Jobs -->
                                <div class="stat-item">
                                    <div class="stat-left">
                                        <div class="stat-icon stat-icon-green">
                                            <i data-lucide="check-circle"></i>
                                        </div>
                                        <span>Completed Jobs</span>
                                    </div>
                                    <span class="stat-value">12</span>
                                </div>

                                <!-- Ongoing Projects -->
                                <div class="stat-item">
                                    <div class="stat-left">
                                        <div class="stat-icon stat-icon-orange">
                                            <i data-lucide="award"></i>
                                        </div>
                                        <span>Ongoing Projects</span>
                                    </div>
                                    <span class="stat-value">2</span>
                                </div>

                                <!-- Average Rating -->
                                <div class="stat-item">
                                    <div class="stat-left">
                                        <div class="stat-icon stat-icon-yellow">
                                            <i data-lucide="star"></i>
                                        </div>
                                        <span>Average Rating</span>
                                    </div>
                                    <div class="rating-value">
                                        <span class="stat-value">4.8</span>
                                        <i data-lucide="star" class="star-icon"></i>
                                    </div>
                                </div>

                                <!-- Reviews Count -->
                                <div class="stat-item">
                                    <div class="stat-left">
                                        <div class="stat-icon stat-icon-blue">
                                            <i data-lucide="briefcase"></i>
                                        </div>
                                        <span>Reviews</span>
                                    </div>
                                    <span class="stat-value">12</span>
                                </div>
                            </div>
                        </div>

                        <!-- Decision Panel -->
                        <?php if ($proposal['status'] === 'pending'): ?>
                            <div class="card decision-card flex flex-col gap-4">
                                <h3>Make a Decision</h3>

                                <form action="accept_application.php" method="POST">

                                    <div class="decision-buttons">
                                        <input type="hidden" name="proposal_id" value="<?= $proposal['id'] ?>">
                                        <input type="hidden" name="job_id" value="<?= $proposal['job_id'] ?>">
                                        <!-- Accept Button -->
                                        <button onclick="acceptJob(this)" class="decision-button decision-accept">
                                            <i data-lucide="check-circle"></i>
                                            Accept Proposal
                                        </button>
                                    </div>
                                </form>
                                <!-- Reject Button -->
                                <form action="reject_application.php" method="POST">
                                    <div class="decision-buttons">
                                        <input type="hidden" name="proposal_id" value="<?= $proposal['id'] ?>">
                                        <!-- Accept Button -->
                                        <button class="decision-button decision-reject">
                                            <i data-lucide="x"></i>
                                            Reject Proposal
                                        </button>
                                    </div>
                                </form>

                                <p class="decision-note">
                                    This action will notify the candidate via email
                                </p>
                            <?php else: ?>
                                <p class="text-sm font-medium text-gray-500">

                                    This proposal has already been <?= htmlspecialchars($proposal['status']) ?>.

                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
    </div>
    </main>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

      function acceptJob(button) {

    if (button.disabled) {
        return;
    }

    button.disabled = true;
    button.innerText = "Processing...";

    button.form.submit();
}
    </script>
</body>

</html>