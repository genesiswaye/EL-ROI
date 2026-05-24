<?php
session_start();

require_once "../config/database.php";
require_once "../includes/auth_guard.php";

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");
    exit();
}

/* ROLE PROTECTION */

if ($_SESSION['role'] !== 'student') {

    header("Location: ../errors/error.php?message=" .
        urlencode("Unauthorized access"));

    exit();
}


$user_id = $_SESSION['user_id'];



/* GET FILTER VALUES */

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? 'all';
$role = $_GET['role'] ?? 'all';
$skill = $_GET['skill'] ?? 'all';

$params = [];
$params['user_id'] = $user_id;

/* BASE QUERY */
$limit = 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

$sql = "
SELECT DISTINCT jobs.id,
jobs.title,
jobs.description,
jobs.budget,
jobs.deadline,
jobs.category,
jobs.created_at,
users.full_name,
users.role,

COALESCE(
job_recommendations.ai_score,
0
)

AS ai_score


FROM jobs
JOIN users ON jobs.created_by = users.id

LEFT JOIN
job_recommendations

ON jobs.id = job_recommendations.job_id AND
job_recommendations.student_id= :user_id
LEFT JOIN job_skills ON jobs.id = job_skills.job_id
WHERE jobs.status = 'open'
";

/* SEARCH FILTER */

if (!empty($search)) {
    $sql .= " AND (jobs.title LIKE :search OR jobs.description LIKE :search)";
    $params['search'] = "%$search%";
}

/* CATEGORY FILTER */

if ($category !== 'all') {
    $sql .= " AND jobs.category = :category";
    $params['category'] = $category;
}

/* POSTED BY FILTER */

if ($role !== 'all') {
    $sql .= " AND users.role = :role";
    $params['role'] = $role;
}

/* Price FILTER */
$price = $_GET['price'] ?? 'all';

if ($price !== 'all') {

    if ($price === '100000+') {

        $sql .= " AND jobs.budget >= :min_price";
        $params['min_price'] = 100000;
    } else {

        list($min, $max) = explode('-', $price);

        $sql .= " AND jobs.budget BETWEEN :min_price AND :max_price";

        $params['min_price'] = (int)$min;
        $params['max_price'] = (int)$max;
    }
}
// if ($skill !== 'all') {
//     $sql .= " AND job_skills.skill_id = :skill";
//     $params['skill'] = $skill;
// }

$sql .= " ORDER BY ai_score DESC LIMIT :limit OFFSET :offset";

/* EXECUTE QUERY */

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue(":$key", $value);
}

$stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

$stmt->execute();
$jobs = $stmt->fetchAll();



/* FETCH SKILLS FOR FILTER DROPDOWN */

$stmt = $pdo->query("
SELECT id, name
FROM skills
ORDER BY name
");

$skills = $stmt->fetchAll();

$stmt = $pdo->query("
SELECT DISTINCT category
FROM jobs
ORDER BY category
");

$categories = $stmt->fetchAll();


?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dist/output.css">
    <link rel="stylesheet" href="neww.css">
    <title>Browse Jobs</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

</head>

<body>

    <?php
    $activePage = "browse_jobs";
    include "../includes/employer_nav.php";
    ?>
    <header class="page-header">
        <div class="header-container">
            <div class="header-content">
                <h1 class="page-title">Browse Available Projects</h1>
                <p class="page-subtitle">Explore real-world projects posted by lecturers, companies, and students.</p>
                <div class="campus-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </svg>
                    Campus-only projects
                </div>
            </div>
        </div>
    </header>
    <div>
        <form method="GET" action="browse_jobs.php">

            <main class="main-content">

                <div class="filter-section">

                    <div class="filter-grid">

                        <!-- Search Input -->

                        <div class="search-wrapper">

                            <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>

                            </svg>

                            <input
                                type="text"
                                name="search"
                                class="search-input"
                                placeholder="Search by skill, keyword, or project title"
                                value="<?= htmlspecialchars($search) ?>">

                        </div>


                        <!-- Project Type Filter -->

                        <div class="filter-select-wrapper">

                            <select name="category" class="filter-select">

                                <option value="all">All Types</option>

                                <?php foreach ($categories as $cat): ?>

                                    <option value="<?= htmlspecialchars($cat['category']) ?>"
                                        <?= $category == $cat['category'] ? 'selected' : '' ?>>

                                        <?= ucfirst(htmlspecialchars($cat['category'])) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <svg class="select-icon" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">

                                <polyline points="6 9 12 15 18 9"></polyline>

                            </svg>

                        </div>


                        <!-- Posted By Filter -->

                        <div class="filter-select-wrapper">

                            <select name="role" class="filter-select">

                                <option value="all">Posted By: All</option>

                                <option value="student" <?= $role == 'student' ? 'selected' : '' ?>>Student</option>
                                <option value="lecturer" <?= $role == 'lecturer' ? 'selected' : '' ?>>Lecturer</option>
                                <option value="company" <?= $role == 'company' ? 'selected' : '' ?>>Company</option>

                            </select>

                            <svg class="select-icon" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">

                                <polyline points="6 9 12 15 18 9"></polyline>

                            </svg>

                        </div>


                        <!-- Skill Category Filter -->

                        <div class="filter-select-wrapper">

                            <select name="price" class="filter-select" onchange="this.form.submit()">

                                <option value="all">All Budgets</option>

                                <option value="0-5000" <?= $price == '0-5000' ? 'selected' : '' ?>>₦0 - ₦5,000</option>
                                <option value="5000-20000" <?= $price == '5000-20000' ? 'selected' : '' ?>>₦5,000 - ₦20,000</option>
                                <option value="20000-50000" <?= $price == '20000-50000' ? 'selected' : '' ?>>₦20,000 - ₦50,000</option>
                                <option value="50000-100000" <?= $price == '50000-100000' ? 'selected' : '' ?>>₦50,000 - ₦100,000</option>
                                <option value="100000+" <?= $price == '100000+' ? 'selected' : '' ?>>₦100,000+</option>

                            </select>

                            <svg class="select-icon" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">

                                <polyline points="6 9 12 15 18 9"></polyline>

                            </svg>

                        </div>
                    </div>

                    <!-- Clear Filters -->

                    <a href="browse_jobs.php" class="clear-filters">

                        <svg width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">

                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>

                        </svg>

                        Clear Filters

                    </a>

                </div>

            </main>

        </form>
    </div>
    <!-- Projects Grid -->
    <div class="projects-grid">

        <?php foreach ($jobs as $job): ?>
            <!-- Project Card 1 -->
            <div class="project-card">

                <div class="card-header">
                    <h3 class="project-title">

                        <?= htmlspecialchars($job['title']) ?>

                    </h3>
                    <p class="text-sm
                        font-semibold
                        text-[#4B2E83]
                        mt-2">

                        AI Match:

                        <?= $job['ai_score'] ?>%

                    </p>
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
                $stmt = $pdo->prepare("
                    SELECT skills.name
                    FROM job_skills
                    JOIN skills ON job_skills.skill_id = skills.id
                    WHERE job_skills.job_id = ?
                    ");

                $stmt->execute([$job['id']]);

                $skills = $stmt->fetchAll();
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
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="6" y1="3" x2="6" y2="21"></line>
                            <line x1="18" y1="3" x2="18" y2="21"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                            <line x1="4" y1="10" x2="20" y2="10"></line>
                            <line x1="4" y1="14" x2="20" y2="14"></line>
                        </svg>

                        <span>

                            ₦<?= number_format($job['budget']) ?>

                        </span>

                    </div>

                    <div class="meta-item">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>

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

                <p class="apply-note text-center">Only students can apply</p>
            </div>

        <?php endforeach; ?>

    </div>
    <div class="pagination">
        <button id="loadMoreBtn" class="btn-load-more">
            Load More Projects
        </button>
    </div>
    </div>
    <div>
        something

    </div>
    <script>
        let page = 1;

        document.getElementById("loadMoreBtn").addEventListener("click", function() {

            page++;

            fetch("load_more_jobs.php?page=" + page)
                .then(res => res.text())
                .then(data => {

                    document.querySelector(".projects-grid")
                        .insertAdjacentHTML("beforeend", data);

                });

        });
    </script>
</body>

</html>