<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

if (!isset($_GET['user_id'])) {
    die("Student not specified");
}

$user_id = $_GET['user_id'];

$stmt = $pdo->prepare("
    SELECT 
    student_profiles.user_id,
    student_profiles.department,
    student_profiles.matric_num,
    student_profiles.level,
    student_profiles.bio,
    users.full_name,
    users.role
    FROM student_profiles
    JOIN users ON student_profiles.user_id = users.id
    WHERE student_profiles.user_id = ?
");

$stmt->execute([$user_id]);

$student = $stmt->fetch();

if (!$student) {
    die("Student not found");
}

$job_id = $_GET['job_id'] ?? null;
if (!$job_id) {
    die("Invalid navigation");
}

$stmt = $pdo->prepare("
SELECT skills.name
FROM student_skills
JOIN skills 
ON student_skills.skill_id = skills.id
WHERE student_skills.user_id = ?
");

$stmt->execute([$user_id]);

$skills = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - CampusLink</title>
    <link rel="stylesheet" href="student_profile.css">
    <link rel="stylesheet" href="../dist/output.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <!-- Navigation -->
    <header class="page-header" style="padding: 1rem;">
        <a href="../jobs/view_applications.php?job_id=<?= $job_id ?>" class="back-button">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to previous page
        </a>
    </header>
    
    <!-- Main Content -->
    <div class="main-content">
        
        <div class="container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="header-content">
                    <!-- Avatar -->
                    <div class="avatar-wrapper">
                        <div class="avatar-large">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop" alt="Sarah Chen">
                        </div>
                        <div class="verified-badge">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                    </div>

                    <!-- Profile Info -->
                    <div class="profile-info">
                        <div class="info-header">
                            <div>
                                <h1 class="profile-name"><?= htmlspecialchars($student['full_name']) ?></h1>
                                <div class="badges-row">
                                    <span class="role-badge role-student"><?= htmlspecialchars($student['role']) ?></span>
                                    <span class="verified-text">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                        </svg>
                                        University Verified
                                    </span>
                                </div>
                                <p class="affiliation">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                    </svg>
                                    University of Washington
                                </p>
                            </div>

                            <!-- Action Button -->
                           <?php if ($_SESSION['role'] === 'student'): ?>

                                <button class="btn-primary">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Edit Profile
                                </button>

                            <?php endif; ?>
                        </div>

                        <p class="bio">Computer Science Student | Full-Stack Developer</p>

                        <!-- Quick Stats -->
                        <div class="quick-stats">
                            <div class="rating-display">
                                <div class="stars">
                                    <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                    <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                    <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                    <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                    <svg class="star-empty" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                </div>
                                <span class="rating-value">4.8</span>
                                <span class="rating-count">(12 reviews)</span>
                            </div>
                            <div class="divider"></div>
                            <div class="stat-text">12 jobs completed</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="two-column-layout">
                <!-- Left Column -->
                <div class="left-column">
                    <!-- About Section -->
                    <div class="card">
                        <h2 class="card-title">About</h2>
                        <p class="about-text">
                            <?= htmlspecialchars($student['bio']) ?>
                        </p>
                    </div>

                    <!-- Skills Section -->
                    <div class="card">
                        <h2 class="card-title">Skills & Expertise</h2>
                        <?php if ($skills): ?>
                        <div class="skills-grid">
                           <?php foreach ($skills as $skill): ?>

                            <span class="skill-tag">
                            <?= htmlspecialchars($skill['name']) ?>
                            </span>

                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>

                        <p class="text-gray-500 text-sm">No skills added yet.</p>

                        <?php endif; ?>
                                            </div>

                    <!-- Education Section -->
                    <div class="card">
                        <h2 class="card-title">Education</h2>
                        <div class="education-list">
                            <div class="education-item">
                                <svg class="education-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                </svg>
                                <div>
                                    <p class="education-title">University of Washington</p>
                                    <p class="education-subtitle">Computer Science & Engineering</p>
                                </div>
                            </div>
                            <div class="education-item">
                                <svg class="education-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <div>
                                    <p class="education-subtitle">Junior (3rd Year)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews Section -->
                    <div class="card">
                        <h2 class="card-title">Reviews & Feedback</h2>
                        <div class="reviews-list">
                            <!-- Review 1 -->
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">P</div>
                                        <div>
                                            <p class="reviewer-name">Prof. Michael Torres</p>
                                            <div class="reviewer-meta">
                                                <span class="role-badge role-lecturer">Lecturer</span>
                                                <span class="review-date">2 weeks ago</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stars">
                                        <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                        <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                        <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                        <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                        <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                    </div>
                                </div>
                                <p class="review-project">Research Data Dashboard</p>
                                <p class="review-text">
                                    Excellent work on the research data visualization project. Sarah delivered ahead of 
                                    schedule and incorporated feedback professionally.
                                </p>
                            </div>

                            <!-- Review 2 -->
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">T</div>
                                        <div>
                                            <p class="reviewer-name">TechStart Solutions</p>
                                            <div class="reviewer-meta">
                                                <span class="role-badge role-company">Company</span>
                                                <span class="review-date">1 month ago</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stars">
                                        <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                        <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                        <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                        <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                        <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                    </div>
                                </div>
                                <p class="review-project">Landing Page Redesign</p>
                                <p class="review-text">
                                    Very impressed with the quality of work. Professional communication and clean code delivery.
                                </p>
                            </div>

                            <!-- Review 3 -->
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">D</div>
                                        <div>
                                            <p class="reviewer-name">Dr. Emily Richardson</p>
                                            <div class="reviewer-meta">
                                                <span class="role-badge role-lecturer">Lecturer</span>
                                                <span class="review-date">1 month ago</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stars">
                                        <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                        <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                        <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                        <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                        <svg class="star-empty" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                    </div>
                                </div>
                                <p class="review-project">Course Management System</p>
                                <p class="review-text">
                                    Good work overall. Met all requirements and was responsive to questions.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="right-column">
                    <!-- Activity Stats -->
                    <div class="card">
                        <h2 class="card-title">Activity & Stats</h2>
                        <div class="stats-list">
                            <!-- Jobs Posted -->
                            <div class="stat-item">
                                <div class="stat-left">
                                    <div class="stat-icon stat-icon-blue">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                        </svg>
                                    </div>
                                    <span class="stat-label">Jobs Posted</span>
                                </div>
                                <span class="stat-value">3</span>
                            </div>

                            <!-- Jobs Completed -->
                            <div class="stat-item">
                                <div class="stat-left">
                                    <div class="stat-icon stat-icon-green">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                        </svg>
                                    </div>
                                    <span class="stat-label">Jobs Completed</span>
                                </div>
                                <span class="stat-value">12</span>
                            </div>

                            <!-- Ongoing Projects -->
                            <div class="stat-item">
                                <div class="stat-left">
                                    <div class="stat-icon stat-icon-amber">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="8" r="7"></circle>
                                            <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                                        </svg>
                                    </div>
                                    <span class="stat-label">Ongoing Projects</span>
                                </div>
                                <span class="stat-value">2</span>
                            </div>

                            <!-- Average Rating -->
                            <div class="stat-item">
                                <div class="stat-left">
                                    <div class="stat-icon stat-icon-purple">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                    </div>
                                    <span class="stat-label">Average Rating</span>
                                </div>
                                <div class="rating-stat">
                                    <span class="stat-value">4.8</span>
                                    <svg class="star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                </div>
                            </div>

                            <!-- Total Earnings -->
                            <div class="stat-item stat-item-last">
                                <div class="stat-left">
                                    <div class="stat-icon stat-icon-green">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="12" y1="1" x2="12" y2="23"></line>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                        </svg>
                                    </div>
                                    <span class="stat-label">Total Earnings</span>
                                </div>
                                <span class="stat-value">$3,450</span>
                            </div>
                        </div>
                    </div>

                    <!-- Verification -->
                    <div class="card">
                        <h2 class="card-title">Verification</h2>
                        <div class="verification-content">
                            <div class="verification-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <div>
                                <p class="verification-title">University Verified</p>
                                <p class="verification-text">
                                    This user's identity has been verified through their university email and enrollment status.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>
