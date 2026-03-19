<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* GET JOB ID */

if (!isset($_GET['job_id'])) {
    die("Job not specified");
}

$job_id = $_GET['job_id'];

/* VERIFY JOB BELONGS TO USER */

$stmt = $pdo->prepare("
SELECT * 
FROM jobs
WHERE id = ? AND created_by = ?
");

$stmt->execute([$job_id, $user_id]);

$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    die("Unauthorized access");
}

$requirements = explode("\n", $job['requirements']);

$milestones = json_decode($job['milestones'], true) ?? [];
/* FETCH EXISTING SKILLS FOR JOB */

$stmt = $pdo->prepare("
SELECT skills.id, skills.name
FROM job_skills
JOIN skills ON job_skills.skill_id = skills.id
WHERE job_skills.job_id = ?
");

$stmt->execute([$job_id]);
$jobSkills = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* FETCH ALL SKILLS */

$stmt = $pdo->query("SELECT id, name FROM skills ORDER BY name");
$skills = $stmt->fetchAll();

/* HANDLE UPDATE */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $_POST['title'];
    $description = $_POST['description'];
    $budget = $_POST['budget'];
    $deadline = $_POST['deadline'];
    $category = $_POST['category'];
    $requirements = $_POST['requirements'] ?? '';
    $location_type = $_POST['location_type'] ?? '';
    $milestones = [];

    $milestones_data = [];

    if (!empty($_POST['milestone_titles'])) {

        foreach ($_POST['milestone_titles'] as $index => $m_title) {

            $m_title = trim($m_title);
            $m_desc = trim($_POST['milestone_descriptions'][$index] ?? '');

            if ($m_title === '') continue;

            $milestones_data[] = $m_title . "|" . $m_desc;
        }
    }

    $milestones_string = implode("\n", $milestones_data);

    $stmt = $pdo->prepare("
    UPDATE jobs
    SET title=?, description=?, budget=?, deadline=?, category=?, requirements=?, milestones = ?, location_type=?
    WHERE id=? AND created_by=?
    ");

    $stmt->execute([
        $title,
        $description,
        $budget,
        $deadline,
        $category,
        $requirements,
        $milestones_string,
        $location_type,
        $job_id,
        $user_id
    ]);


    /* RESET JOB SKILLS */

    $stmt = $pdo->prepare("DELETE FROM job_skills WHERE job_id=?");
    $stmt->execute([$job_id]);

    /* ADD NEW SKILLS */

    if (!empty($_POST['skills'])) {

        $submittedSkills = json_decode($_POST['skills'], true);

        foreach ($submittedSkills as $skillData) {

            $skill = trim($skillData['value']);

            if ($skill === "") continue;

            /* INSERT SKILL IF NEEDED */

            $stmt = $pdo->prepare("
            INSERT INTO skills (name)
            VALUES (?)
            ON DUPLICATE KEY UPDATE id=id
            ");

            $stmt->execute([$skill]);

            /* GET SKILL ID */

            $stmt = $pdo->prepare("
            SELECT id FROM skills WHERE name = ?
            ");

            $stmt->execute([$skill]);

            $skill_id = $stmt->fetchColumn();

            /* LINK SKILL TO JOB */

            $stmt = $pdo->prepare("
            INSERT INTO job_skills (job_id, skill_id)
            VALUES (?, ?)
            ");

            $stmt->execute([$job_id, $skill_id]);
        }
    }


    header("Location: my_jobs.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dist/output.css">
    <link rel="stylesheet" href="post_edit.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <title>Document</title>
</head>

<body class="bg-[#F7F8FA] min-h-screen font-sans">
    <main class="main-content">
        <div class="content-container">
            <div style="margin-bottom: 1rem;">
                <a href="../jobs/view_applications.php?job_id=<?= $job_id ?>" 
                class="back-button flex items-center gap-2 text-sm text-gray-600 mb-4 hover:text-black">

                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">

                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>

                    </svg>

                    Back to previous page
                 </a>
            </div>
            <!-- Page Header -->
            <div class="page-header" style="margin-bottom: 24px;">

            <!-- Back button -->
           
                <h1 class="page-title">Edit Job</h1>
                <p class="page-subtitle">Edit job posting to correct possible mistakes</p>
            </div>
            <!-- Form Card -->
            <div class="form-card">
                <form id="postJobForm" method="POST">

                    <div class="form-group">
                        <label for="jobTitle" class="form-label">
                            Job Title <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                            <input
                                type="text"
                                name="title"
                                id="jobTitle"
                                class="form-input"
                                value="<?= htmlspecialchars($job['title']) ?>"
                                required>

                        </div>
                    </div>

                    <div class="form-group">
                        <label for="jobDescription" class="form-label">
                            Job Description <span class="required">*</span>
                        </label>
                        <div class="textarea-wrapper">
                            <svg class="textarea-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            <textarea
                                id="jobDescription"
                                name="description"
                                class="form-textarea"
                                rows="6"><?= htmlspecialchars($job['description']) ?></textarea>
                        </div>
                        <p class="form-helper">Provide clear details about the scope, goals, and any specific requirements</p>
                    </div>
                    <div class="form-group">

                        <label for="requirements" class="form-label">
                            Project Requirements
                        </label>

                        <div class="textarea-wrapper">

                            <textarea
                                id="requirements"
                                name="requirements"
                                class="form-textarea"
                                rows="5"><?= htmlspecialchars($job['requirements']) ?></textarea>

                        </div>

                        <p class="form-helper">
                            Press Enter after each requirement to create a list item.
                        </p>

                    </div>
                    <div class="form-group">

                        <label class="form-label">Milestones / Deliverables</label>

                        <div id="milestones-container">

                            <?php if (!empty($milestones)): ?>

                                <?php foreach ($milestones as $index => $milestone): ?>

                                    <div class="milestone-item flex flex-col gap-4 space-y-4" style="margin-bottom:20px;">

                                        <h6 class="milestone-number text-sm font-medium text-gray-700 mb-2">
                                            Requirement <?= $index + 1 ?>
                                        </h6>

                                        <input
                                            type="text"
                                            name="milestone_titles[]"
                                            class="form-input mb-2"
                                            value="<?= htmlspecialchars($milestone['title']) ?>"
                                            placeholder="Milestone title">

                                        <textarea
                                            name="milestone_descriptions[]"
                                            class="form-textarea"
                                            rows="2"
                                            placeholder="Describe milestone"><?= htmlspecialchars($milestone['description']) ?></textarea>



                                    </div>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <div class="milestone-item flex flex-col gap-4 space-y-4">

                                    <h6 class="milestone-number text-sm font-medium text-gray-700 mb-2">
                                        Requirement 1
                                    </h6>

                                    <input
                                        type="text"
                                        name="milestone_titles[]"
                                        class="form-input mb-2"
                                        placeholder="Milestone title">

                                    <textarea
                                        name="milestone_descriptions[]"
                                        class="form-textarea"
                                        rows="2"
                                        placeholder="Describe milestone"></textarea>



                                </div>

                            <?php endif; ?>

                        </div>

                        <button
                            type="button"
                            id="add-milestone"
                            class="mt-3 text-sm text-purple-600 font-medium">

                            + Add another milestone

                        </button>

                    </div>
                    <div class="form-group">

                        <label class="form-label">
                            Required Skills <span class="required">*</span>
                        </label>

                        <select id="skills" multiple>

                            <?php foreach ($skills as $skill): ?>

                                <option
                                    value="<?= $skill['name'] ?>"
                                    <?= in_array($skill['name'], $jobSkills) ? 'selected' : '' ?>>

                                    <?= htmlspecialchars($skill['name']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                        <input type="hidden" name="skills" id="skillsData">

                    </div>

                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">Category</label>



                        <select id="category" name="category"
                            class="border border-gray-300 rounded-lg w-full" style="padding:10px;">

                            <option value="">Select or type category</option>

                            <?php
                            $categories = [
                                "Web Development",
                                "Research Assistant",
                                "Data Analysis",
                                "UI/UX Design",
                                "Machine Learning"
                            ];

                            // If the saved category is not in the list, add it
                            if ($job['category'] && !in_array($job['category'], $categories)) {
                                echo "<option value='{$job['category']}' selected>{$job['category']}</option>";
                            }

                            foreach ($categories as $category) {
                                $selected = ($job['category'] == $category) ? "selected" : "";
                                echo "<option value='$category' $selected>$category</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" style="padding-top: 1.5rem;">

                        <label class="form-label">Job Location</label>

                        <div class="input-wrapper">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 11-9 11s-9-4-9-11a9 9 0 1118 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <select name="location_type" class="form-input">

                                <option value="">Select location</option>

                                <option value="Remote" <?= $job['location_type'] == 'Remote' ? 'selected' : '' ?>>Remote</option>
                                <option value="On Campus" <?= $job['location_type'] == 'On Campus' ? 'selected' : '' ?>>On Campus</option>
                                <option value="Hybrid" <?= $job['location_type'] == 'Hybrid' ? 'selected' : '' ?>>Hybrid</option>

                            </select>

                        </div>
                    </div>
                    <div class="form-row">
                        <!-- Budget -->
                        <div class="form-group">
                            <label for="budget" class="form-label">
                                Budget<span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="6" y1="3" x2="6" y2="21"></line>
                                    <line x1="18" y1="3" x2="18" y2="21"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                    <line x1="4" y1="10" x2="20" y2="10"></line>
                                    <line x1="4" y1="14" x2="20" y2="14"></line>
                                </svg>
                                <input
                                    type="number"
                                    name="budget"
                                    id="budget"
                                    class="form-input"
                                    value="<?= htmlspecialchars($job['budget']) ?>"
                                    min="0"
                                    step="10" required>
                            </div>

                        </div>


                        <div class="form-group">
                            <label for="deadline" class="form-label">
                                Deadline <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <input
                                    type="date"
                                    name="deadline"
                                    id="deadline"
                                    value="<?= htmlspecialchars($job['deadline']) ?>"
                                    class="form-input" required>
                            </div>


                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Update Job</button>

                    </div>
                </form>
            </div>
            <!-- Help Text -->
            <div class="help-box">
                <p><strong>Tip:</strong> Be specific about your requirements and expectations. Clear job descriptions attract more qualified applicants.</p>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script>
        const existingSkills = <?= json_encode(array_column($jobSkills, 'name')) ?>;

        const skillsSelect = new TomSelect("#skills", {
            plugins: ['remove_button'],
            create: true,
            persist: false,
            maxItems: 8,
            sortField: {
                field: "text",
                direction: "asc"
            },
            onChange: function(values) {
                const data = values.map(v => ({
                    value: v
                }));
                document.getElementById("skillsData").value = JSON.stringify(data);
            }
        });

        /* preload existing skills */
        skillsSelect.setValue(existingSkills);

        document.getElementById("add-milestone").addEventListener("click", function() {

            const container = document.getElementById("milestones-container");

            const block = document.createElement("div");

            block.className = "milestone-item flex flex-col gap-4 space-y-4";

            block.innerHTML = `
<h6 class="milestone-number text-sm font-medium text-gray-700 mb-2">
Requirement
</h6>

<input
type="text"
name="milestone_titles[]"
class="form-input mb-2"
placeholder="Milestone title">

<textarea
name="milestone_descriptions[]"
class="form-textarea"
rows="2"
placeholder="Describe what will be delivered"></textarea>

<button
type="button"
class="remove-milestone text-sm text-red-500 mt-2"
style="padding-bottom:1rem;">
Remove
</button>
`;

            container.appendChild(block);

            updateRequirementNumbers();

        });

        document.addEventListener("click", function(e) {

            if (e.target.classList.contains("remove-milestone")) {

                e.target.closest(".milestone-item").remove();

                updateRequirementNumbers();

            }

        });

        function updateRequirementNumbers() {

            const items = document.querySelectorAll(".milestone-item");

            items.forEach((item, index) => {

                const label = item.querySelector(".milestone-number");

                if (label) {
                    label.textContent = "Requirement " + (index + 1);
                }

            });

        }
        updateRequirementNumbers();
    </script>
</body>

</html>