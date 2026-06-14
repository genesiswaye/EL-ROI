<?php
session_start();
require_once "../config/database.php";
require_once "../AI/update_old_application_ai.php";


if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $_SESSION['job_form'] = $_POST;

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $budget = $_POST['budget'];
    $deadline = $_POST['deadline'];
    $category = $_POST['category'];

    $requirements = $_POST['requirements'] ?? null;
    $milestones = $_POST['milestones'] ?? null;
    $location_type = $_POST['location_type'] ?? null;

    /* BUILD MILESTONES */

    $milestones = null;

    if (!empty($_POST['milestone_titles'])) {

        $titles = $_POST['milestone_titles'];
        $descs = $_POST['milestone_descriptions'];

        $data = [];

        for ($i = 0; $i < count($titles); $i++) {

            $m_title = trim($titles[$i]);
            $desc = trim($descs[$i]);

            if ($m_title === "") continue;

            $data[] = $m_title . "|" . $desc;
        }

        $milestones = implode("\n", $data);
    }


    $errors = [];

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $budget = trim($_POST['budget'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if ($title === '') {

        $errors[] = "Title is required.";
    }

    if ($description === '') {

        $errors[] = "Description is required.";
    }

    if ($budget === '') {

        $errors[] = "Budget is required.";
    }

    if ($deadline === '') {

        $errors[] = "Deadline is required.";
    }

    if ($category === '') {

        $errors[] = "Category is required.";
    }

    if (!empty($errors)) {

        $_SESSION['errors'] = $errors;

        $_SESSION['job_form'] = $_POST;

        header("Location: post_job.php");

        exit();
    }
    /* INSERT JOB */

    $stmt = $pdo->prepare("
    INSERT INTO jobs
    (created_by, title, description, requirements, milestones, location_type, budget, deadline, category, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'open')
");

    $stmt->execute([
        $user_id,
        $title,
        $description,
        $requirements,
        $milestones,
        $location_type,
        $budget,
        $deadline,
        $category
    ]);

    /* GET JOB ID */


    $job_id = $pdo->lastInsertId();

    /* HANDLE SKILLS */

    if (!empty($_POST['skills'])) {

        $skills = json_decode($_POST['skills'], true);

        foreach ($skills as $skillData) {

            $skill = trim($skillData['value']);

            if ($skill === "") {
                continue;
            }

            /* INSERT SKILL IF NOT EXISTS */

            $stmt = $pdo->prepare("
                INSERT INTO skills (name)
                VALUES (?)
                ON DUPLICATE KEY UPDATE id=id
            ");

            $stmt->execute([$skill]);
            // runRecommendations();

            /* GET SKILL ID */

            $stmt = $pdo->prepare("
                SELECT id FROM skills WHERE name = ?
            ");

            $stmt->execute([$skill]);
            $skill_id = $stmt->fetchColumn();

            /* LINK SKILL TO JOB */

            $stmt = $pdo->prepare("
                INSERT IGNORE INTO job_skills (job_id, skill_id)
                VALUES (?, ?)
            ");

            $stmt->execute([$job_id, $skill_id]);
        }
    }

    header(
        "Location: successfully_created.php?job_id="
            . $job_id
    );

    exit();
}
/* ---------- FETCH SKILLS FOR TOMSELECT ---------- */

$stmt = $pdo->query("
SELECT name
FROM skills
ORDER BY name
");

$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Post Job - CampusLink</title>

    <link rel="stylesheet" href="../dist/output.css">
    <link rel="stylesheet" href="post_edit.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        .error-box {

            background: #fee2e2;

            color: #991b1b;

            padding: 12px;

            border-radius: 8px;

            margin-bottom: 10px;

            border: 1px solid #fca5a5;

        }
    </style>
</head>

<body class="bg-[#F7F8FA] min-h-screen font-sans">

    <!-- NAVBAR -->
    <?php
    $activePage = "post_job";
    include "../includes/employer_nav.php";
    ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-container">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Post a Job</h1>
                <p class="page-subtitle">Create a new job posting to find talented students for your project</p>
            </div>

            <!-- Form Card -->
            <div class="form-card">
                <form id="postJobForm" method="POST">
                    <?php

                    if (!empty($_SESSION['errors'])) {

                        foreach ($_SESSION['errors'] as $error) {

                            echo "
        <div class='error-box'>
            $error
        </div>
        ";
                        }

                        unset($_SESSION['errors']);
                    }

                    ?>
                    <!-- Job Title -->
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
                                placeholder="e.g., Mobile App UI/UX Designer">
                        </div>
                    </div>

                    <!-- Job Description -->
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
                                rows="6"
                                placeholder="Describe the project requirements, deliverables, and expectations..."></textarea>
                        </div>
                        <p class="form-helper">Provide clear details about the scope, goals, and any specific requirements</p>
                    </div>
                    <!-- Project Requirements -->
                    <div class="form-group">

                        <label for="requirements" class="form-label">
                            Project Requirements
                        </label>

                        <div class="textarea-wrapper">
                            <svg class="textarea-icon" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 11l2 2 4-4"></path>
                                <path d="M21 12a9 9 0 11-6.219-8.56"></path>
                            </svg>


                            <textarea
                                id="requirements"
                                name="requirements"
                                class="form-textarea"
                                rows="5"
                                placeholder="One requirement per line and press enter."></textarea>

                        </div>

                        <p class="form-helper">
                            Press Enter after each requirement to create a list item.
                        </p>

                    </div>
                    <!-- Milestones -->
                    <div class="form-group">

                        <label class="form-label">Milestones / Deliverables</label>

                        <div id="milestones-container">

                            <div class="milestone-item flex flex-col gap-4 space-y-4" style="margin-bottom: 20px;">
                                <h6 class="milestone-number text-sm font-medium text-gray-700 mb-2">
                                    Requirement 1
                                </h6>

                                <input
                                    type="text"
                                    name="milestone_titles[]"
                                    class="form-input mb-2"
                                    placeholder="Milestone title (e.g. Research & Wireframes)">

                                <textarea
                                    name="milestone_descriptions[]"
                                    class="form-textarea"
                                    rows="2"
                                    placeholder="Describe what will be delivered in this stage"></textarea>

                            </div>

                        </div>

                        <button
                            type="button"
                            id="add-milestone"
                            class="mt-3 text-sm text-purple-600 font-medium">

                            + Add another milestone

                        </button>

                    </div>
                    <!-- Required Skills -->
                    <div class="form-group">

                        <label for="skills" class="form-label">
                            Required Skills <span class="required">*</span>
                        </label>

                        <div class="input-wrapper">

                            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>

                            <select id="skills" multiple placeholder="Type or select skills">

                                <?php foreach ($skills as $skill): ?>

                                    <option value="<?= htmlspecialchars($skill['name']) ?>">
                                        <?= htmlspecialchars($skill['name']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <input type="hidden" name="skills" id="skillsData">
                        </div>



                    </div>
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-gray-700 mb-1">Category</label>

                        <select id="category" name="category"
                            class="border border-gray-300 rounded-lg w-full ">

                            <option value="">Select or type category</option>

                            <option value="Web Development">Web Development</option>
                            <option value="Research Assistant">Research Assistant</option>
                            <option value="Data Analysis">Data Analysis</option>
                            <option value="UI/UX Design">UI/UX Design</option>
                            <option value="Machine Learning">Machine Learning</option>

                        </select>

                    </div>
                    <div class="form-group" style="padding-top: 1.5rem;">

                        <label for="location_type" class="form-label">
                            Job Location
                        </label>

                        <div class="input-wrapper">

                            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 11-9 11s-9-4-9-11a9 9 0 1118 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>

                            <select
                                id="location_type"
                                name="location_type"
                                class="form-input">

                                <option value="">Select location</option>
                                <option value="Remote">Remote</option>
                                <option value="On Campus">On Campus</option>
                                <option value="Hybrid">Hybrid</option>

                            </select>

                        </div>

                    </div>

                    <!-- Budget and Deadline Row -->
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
                                    placeholder="Enter amount"
                                    min="0"
                                    step="10">
                            </div>
                        </div>

                        <!-- Deadline -->
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
                                    class="form-input">
                            </div>
                        </div>
                    </div>

                    <!-- Project Type -->
                    <!-- <div class="form-group">
                        <label class="form-label">
                            Project Type <span class="required">*</span>
                        </label>
                        <div class="project-type-grid">
                            <label class="project-type-card">
                                <input type="radio" name="projectType" value="academic" class="project-type-radio">
                                <div class="project-type-content">
                                    <div class="project-type-title">Academic</div>
                                    <div class="project-type-desc">Research, papers, thesis</div>
                                </div>
                            </label>

                            <label class="project-type-card project-type-card-active">
                                <input type="radio" name="projectType" value="commercial" class="project-type-radio" checked>
                                <div class="project-type-content">
                                    <div class="project-type-title">Commercial</div>
                                    <div class="project-type-desc">Business projects</div>
                                </div>
                            </label>

                            <label class="project-type-card">
                                <input type="radio" name="projectType" value="research" class="project-type-radio">
                                <div class="project-type-content">
                                    <div class="project-type-title">Research</div>
                                    <div class="project-type-desc">Studies, experiments</div>
                                </div>
                            </label>
                        </div>
                    </div> -->

                    <!-- Action Buttons -->
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Publish Job</button>
                        <!-- <button type="button" class="btn-secondary">Save Draft</button> -->
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
        document.addEventListener(
            "DOMContentLoaded",
            function() {

                const form =
                    document.querySelector("form");

                if (!form) return;

                form.addEventListener(
                    "submit",
                    function(event) {

                        if (
                            window.preventSubmit
                        ) {

                            event.preventDefault();

                        }

                    }
                );

                form.querySelectorAll(
                    "input, select"
                ).forEach(field => {

                    field.addEventListener(
                        "keydown",
                        function(event) {

                            if (
                                event.key === "Enter"
                            ) {

                                event.preventDefault();

                                return false;

                            }

                        }
                    );

                });

            }
        );
        // Project type selection
        const projectTypeCards = document.querySelectorAll('.project-type-card');
        const projectTypeRadios = document.querySelectorAll('.project-type-radio');

        projectTypeCards.forEach((card, index) => {
            card.addEventListener('click', () => {
                projectTypeCards.forEach(c => c.classList.remove('project-type-card-active'));
                card.classList.add('project-type-card-active');
            });
        });
        const skillSelect = new TomSelect("#skills", {

            plugins: ['remove_button'],

            create: true, // allow new skills

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

                document.getElementById("skillsData").value =
                    JSON.stringify(data);

            }

        });
        // Form submission
        new TomSelect("#category", {
            create: true,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
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
                class="remove-milestone text-sm text-red-500 mt-2 " style="padding-bottom:1rem;">
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
        document.getElementById("postJobForm").addEventListener("submit", function() {

            const values = skillSelect.getValue();

            const data = values.map(v => ({
                value: v
            }));

            document.getElementById("skillsData").value =
                JSON.stringify(data);

        });
    </script>
</body>

</html>