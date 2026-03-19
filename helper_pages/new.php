<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

if (!isset($_GET['job_id'])) {
    die("Job not specified");
}


$job_id = $_GET['job_id'];

/* FETCH JOB */
// Get the job poster ID from the job


$stmt = $pdo->prepare("
    SELECT 
    jobs.id,
    jobs.title,
    jobs.description,
    jobs.requirements,
    jobs.milestones,
    jobs.location_type,
    jobs.budget,
    jobs.deadline,
    jobs.category,
    jobs.status,
    jobs.created_at,
    jobs.created_by,

    users.full_name,
    users.role

    FROM jobs
    JOIN users ON jobs.created_by = users.id

    WHERE jobs.id = ?
    ");

$stmt->execute([$job_id]);

$job = $stmt->fetch();

if (!$job) {
    die("Job not found");
}
$duration_weeks = null;

if (!empty($job['deadline']) && !empty($job['created_at'])) {

    $start = new DateTime($job['created_at']);
    $end = new DateTime($job['deadline']);

    $days = $start->diff($end)->days;

    $duration_weeks = ceil($days / 7);
}
$stmt = $pdo->prepare("
    SELECT id, title, budget, status
    FROM jobs
    WHERE category = ?
    AND id != ?
    ORDER BY created_at DESC
    LIMIT 3
");

$stmt->execute([$job['category'], $job['id']]);

$related_jobs = $stmt->fetchAll();

$poster_id = $job['created_by'];


// Get the role of the user
$stmt = $pdo->prepare("
    SELECT role 
    FROM users 
    WHERE id = ?
");

$stmt->execute([$poster_id]);

$role = $stmt->fetchColumn();


// Decide which profile table to use
switch ($role) {

    case 'student':
        $table = 'student_profiles';
        break;

    case 'lecturer':
        $table = 'lecturer_profiles';
        break;

    case 'company':
        $table = 'company_profiles';
        break;

    default:
        $table = null;
}


// Fetch the bio if a table exists
$bio = '';

if ($table) {

    $stmt = $pdo->prepare("
        SELECT bio 
        FROM $table 
        WHERE user_id = ?
    ");

    $stmt->execute([$poster_id]);

    $bio = $stmt->fetchColumn();
}

$milestones = [];
$requirements = [];

if (!empty($job['requirements'])) {
    $requirements = explode("\n", $job['requirements']);
}
if (!empty($job['milestones'])) {
    $milestones = explode("\n", $job['milestones']);
}
$status = $job['status'];

switch ($status) {

    case 'open':
        $status_text = "Open for Applications";
        break;

    case 'in_progress':
        $status_text = "Work In Progress";
        break;

    case 'completed':
        $status_text = "Completed";
        break;

    default:
        $status_text = ucfirst($status);
}
/* FETCH JOB SKILLS */

$stmt = $pdo->prepare("
    SELECT skills.name
    FROM job_skills
    JOIN skills ON job_skills.skill_id = skills.id
    WHERE job_skills.job_id = ?
    ");

$stmt->execute([$job_id]);

$skills = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dist/output.css">
    <link rel="stylesheet" href="new.css">
    <title>Project Details - CampusLink</title>
    <link rel="stylesheet" href="project-details.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Top Navigation -->


    <!-- Main Content -->
    <main class="main-content">
        <!-- Back Button -->
        <a href="browse_jobs.php" class="back-button">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Browse Projects
        </a>

        <!-- Project Header -->
        <div class="project-header">
            <h1 class="project-title"><?= htmlspecialchars($job['title']) ?></h1>

            <!-- Posted By -->
            <div class="posted-by">
                <span class="posted-label">Posted by:</span>
                <div class="role-badge role-company">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                        <path d="M3 9h18"></path>
                        <path d="M9 21V9"></path>
                    </svg>
                    <?= htmlspecialchars($job['role']) ?>
                </div>
                <span class="poster-name"><?= htmlspecialchars($job['full_name']) ?></span>
            </div>

            <!-- Metadata -->
            <div class="metadata">
                <div class="meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span><?= date("M d, Y", strtotime($job['created_at'])) ?></span>
                </div>
                <div class="meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span><?= date("M d, Y", strtotime($job['deadline'])) ?></span>
                </div>
                <!-- <div class="project-type-badge">Commercial</div> -->
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="content-layout">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Project Description -->
                <section class="content-section">
                    <h2 class="section-title">Project Description</h2>
                    <div class="description-text">
                        <p> <?= nl2br(htmlspecialchars($job['description'])) ?></p>

                    </div>
                </section>

                <!-- Project Requirements -->
                <section class="content-section">
                    <h2 class="section-title">Project Requirements</h2>
                    <ul class="requirements-list">
                        <?php foreach ($requirements as $req): ?>
                            <?php $req = trim($req);
                            if ($req === '') continue; ?>
                            <li class="requirement-item">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                <span><?= htmlspecialchars($req) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <!-- Required Skills -->
                <section class="content-section">
                    <h2 class="section-title">Required Skills</h2>
                    <div class="skills-tags">
                        <?php foreach ($skills as $skill): ?>
                            <span class="skill-tag"><?= htmlspecialchars($skill['name']) ?></span>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Milestones -->
                <section class="content-section">
                    <h2 class="section-title">Milestones / Deliverables</h2>
                    <div class="milestones-list">
                        <?php
                        $index = 1;

                        foreach ($milestones as $milestone):

                            $milestone = trim($milestone);
                            if ($milestone === '') continue;

                            $parts = explode("|", $milestone, 2);

                            $title = trim($parts[0]);
                            $desc = isset($parts[1]) ? trim($parts[1]) : '';
                        ?>

                            <div class="milestone-item">
                                <div class="milestone-number"><?= $index ?></div>
                                <div class="milestone-content">

                                    <h3 class="milestone-title"> <?= htmlspecialchars($title) ?></h3>
                                    <p class="milestone-description"><?= htmlspecialchars($desc) ?></p>
                                </div>
                            </div>

                        <?php
                            $index++;
                        endforeach;
                        ?>


                    </div>
                </section>

                <!-- About the Poster -->
                <section class="content-section">
                    <h2 class="section-title">About the Poster</h2>
                    <div class="poster-info">
                        <div class="poster-avatar">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                <path d="M3 9h18"></path>
                                <path d="M9 21V9"></path>
                            </svg>
                        </div>
                        <div class="poster-details">
                            <div class="poster-header">
                                <span class="poster-company"><?= htmlspecialchars($job['full_name']) ?><span>
                                        <div class="role-badge role-company-small">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                                <path d="M3 9h18"></path>
                                                <path d="M9 21V9"></path>
                                            </svg>
                                            <?= htmlspecialchars($job['role']) ?>
                                        </div>
                            </div>
                            <p class="poster-bio">
                                <?= htmlspecialchars($bio ?? 'No bio available') ?>
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Related Projects -->
                <section class="content-section">
                    <h2 class="section-title">Related Projects</h2>

                    <div class="related-projects">

                        <?php foreach ($related_jobs as $related): ?>

                            <div class="related-project">

                                <a href="view_job.php?job_id=<?= $related['id'] ?>">
                                    <h3 class="related-title">
                                        <?= htmlspecialchars($related['title']) ?>
                                    </h3>
                                </a>

                                <div class="related-meta">

                                    <div class="related-budget">

                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">

                                            <line x1="12" y1="1" x2="12" y2="23"></line>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>

                                        </svg>

                                        <span>$<?= htmlspecialchars($related['budget']) ?></span>

                                    </div>

                                    <span class="status-badge status-<?= $related['status'] ?>">
                                        <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $related['status']))) ?>
                                    </span>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>
                    <?php if ($related_jobs): ?>
                    <!-- loop -->
                    <?php else: ?>
                    <p class="text-gray-500">No related projects found.</p>
                    <?php endif; ?>
                </section>
            </div>

            <!-- Right Column - Sticky Summary Card -->
            <div class="right-column">
                <div class="summary-card">
                    <h2 class="summary-title">Project Summary</h2>

                    <!-- Budget -->
                    <div class="summary-item">
                        <div class="summary-label">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="6" y1="3" x2="6" y2="21"></line>
                                <line x1="18" y1="3" x2="18" y2="21"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                <line x1="4" y1="10" x2="20" y2="10"></line>
                                <line x1="4" y1="14" x2="20" y2="14"></line>
                            </svg>
                            <span>Budget</span>
                        </div>
                        <div class="summary-value"><?= number_format($job['budget']) ?></div>
                    </div>

                    <!-- Duration -->
                    <div class="summary-item">
                        <div class="summary-label">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span>Estimated Duration</span>
                        </div>
                        <div class="summary-duration"><?= $duration_weeks ?> week<?= $duration_weeks > 1 ? 's' : '' ?></div>
                    </div>

                    <!-- Status -->
                    <div class="summary-item-last">
                        <div class="summary-label">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span>Project Status</span>
                        </div>
                        <span class="status-badge-large"><?= htmlspecialchars($status_text) ?></span>
                    </div>

                    <!-- Location -->
                    <div class="summary-item-last">
                        <div class="summary-label">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span>Location</span>
                        </div>
                        <span class="status-badge-large"><?= htmlspecialchars($job['location_type']) ?></span>
                    </div>

                    <!-- Apply Button -->
                    <div class="flex flex-col">
                        <?php if ($role === 'student'): ?>

                            <a href="submit_proposal.php?job_id=<?= $job['id'] ?>" class="btn-apply text-center">
                                Apply for Project
                            </a>

                            <p class="apply-note">Only students can apply to projects</p>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>