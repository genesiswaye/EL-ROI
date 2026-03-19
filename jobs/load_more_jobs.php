<?php
require_once "../config/database.php";

$page = $_GET['page'] ?? 1;

$limit = 10;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("
SELECT jobs.id,
jobs.title,
jobs.description,
jobs.budget,
jobs.deadline,
jobs.category,
users.full_name,
users.role
FROM jobs
JOIN users ON jobs.created_by = users.id
WHERE jobs.status = 'open'
ORDER BY jobs.created_at DESC
LIMIT :limit OFFSET :offset
");

$stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

$stmt->execute();

$jobs = $stmt->fetchAll();

foreach ($jobs as $job):
?>

    <div class="project-card">

        <div class="card-header">
            <h3 class="project-title">
                <?= htmlspecialchars($job['title']) ?>
            </h3>

            <span class="status-badge status-open">
                Open
            </span>
        </div>

        <p class="project-description">
            <?= htmlspecialchars($job['description']) ?>
        </p>

        <div class="posted-by">

            <span class="posted-label">Posted by:</span>

            <div class="role-badge role-<?= $job['role'] ?>">
                <?= ucfirst($job['role']) ?>
            </div>

            <span class="poster-name">
                <?= htmlspecialchars($job['full_name']) ?>
            </span>

        </div>

        <?php
        $stmt2 = $pdo->prepare("
SELECT skills.name
FROM job_skills
JOIN skills ON job_skills.skill_id = skills.id
WHERE job_skills.job_id = ?
");

        $stmt2->execute([$job['id']]);
        $skills = $stmt2->fetchAll();
        ?>

        <div class="skills-tags">

            <?php foreach ($skills as $skill): ?>

                <span class="skill-tag">
                    <?= htmlspecialchars($skill['name']) ?>
                </span>

            <?php endforeach; ?>

        </div>

        <div class="project-meta">

            <div class="meta-item">
                <span>₦<?= number_format($job['budget']) ?></span>
            </div>

            <div class="meta-item">
                <span>
                    Due: <?= date("M d, Y", strtotime($job['deadline'])) ?>
                </span>
            </div>

        </div>

        <div class="flex">

            <a href="view_job.php?job_id=<?= $job['id'] ?>"
                class="btn-view-details text-center">

                View Details

            </a>

        </div>

        <p class="apply-note text-center">
            Only students can apply
        </p>

    </div>

<?php endforeach; ?>