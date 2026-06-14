<?php

session_start();
require_once "../config/database.php";
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT *
    FROM lecturer_profiles
    WHERE user_id = ?
");

$stmt->execute([$user_id]);

$profile = $stmt->fetch(PDO::FETCH_ASSOC);

$department = $profile['department'] ?? '';
$rank = $profile['rank'] ?? '';
$research_interests = $profile['research_interests'] ?? '';
$expertise = $profile['expertise'] ?? '';
$biography = $profile['biography'] ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $department = trim($_POST['department'] ?? '');

    if ($department === 'Other') {

        $department =
            trim($_POST['other_department'] ?? '');
    }
    $rank = trim($_POST['rank'] ?? '');

    $researchArray =
        json_decode(
            $_POST['research_interests'] ?? '[]',
            true
        );

    $expertiseArray =
        json_decode(
            $_POST['expertise'] ?? '[]',
            true
        );

    $biography =
        trim($_POST['biography'] ?? '');

    if (
        empty($department) ||
        empty($rank)
    ) {
        die("Department and rank are required.");
    }

    $researchText =
        implode(", ", $researchArray);

    $expertiseText =
        implode(", ", $expertiseArray);

    if ($profile) {

        $stmt = $pdo->prepare("
        UPDATE lecturer_profiles
        SET
            department = ?,
            rank = ?,
            research_interests = ?,
            expertise = ?,
            biography = ?
        WHERE user_id = ?
    ");

        $stmt->execute([
            $department,
            $rank,
            $researchText,
            $expertiseText,
            $biography,
            $user_id
        ]);
    } else {

        $stmt = $pdo->prepare("
        INSERT INTO lecturer_profiles
        (
            user_id,
            department,
            rank,
            research_interests,
            expertise,
            biography
        )
        VALUES
        (?, ?, ?, ?, ?, ?)
    ");

        $stmt->execute([
            $user_id,
            $department,
            $rank,
            $researchText,
            $expertiseText,
            $biography
        ]);
    }
    header(
        "Location: ../dashboard/overview.php"
    );

    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Profile Setup - StudentLancer</title>
    <link rel="stylesheet" href="lecturer-profile-setup-style.css">
</head>

<body>
    <!-- Header -->
    <!-- <header class="lecturer-header">
        <div class="header-container">
            <div class="logo">
                <span class="logo-icon">🎓</span>
                <span class="logo-text">StudentLancer</span>
                <span class="logo-subtitle">Faculty Portal</span>
            </div>
            <div class="header-actions">
                <button class="btn-preview" onclick="previewProfile()">
                    <span class="btn-icon">👁️</span>
                    Preview Profile
                </button>
                <button class="btn-logout" onclick="logout()">Logout</button>
            </div>
        </div>
    </header> -->

    <!-- Main Container -->
    <div class="lecturer-container">
        <div class="lecturer-grid">
            <!-- Left Panel -->
            <div class="lecturer-left">
                <!-- University Illustration -->
                <div class="university-illustration">
                    <div class="building-container">
                        <div class="building main-building">
                            <div class="building-roof"></div>
                            <div class="building-body">
                                <div class="building-window"></div>
                                <div class="building-window"></div>
                                <div class="building-window"></div>
                                <div class="building-door"></div>
                            </div>
                        </div>
                        <div class="building side-building left">
                            <div class="building-roof small"></div>
                            <div class="building-body small">
                                <div class="building-window small"></div>
                            </div>
                        </div>
                        <div class="building side-building right">
                            <div class="building-roof small"></div>
                            <div class="building-body small">
                                <div class="building-window small"></div>
                            </div>
                        </div>
                    </div>
                    <div class="research-icons">
                        <div class="research-icon icon-book">📚</div>
                        <div class="research-icon icon-microscope">🔬</div>
                        <div class="research-icon icon-chart">📊</div>
                        <div class="research-icon icon-globe">🌍</div>
                    </div>
                    <div class="cloud cloud-1"></div>
                    <div class="cloud cloud-2"></div>
                </div>

                <!-- Profile Completion Card -->
                <div class="completion-card">
                    <div class="completion-header">
                        <h3 class="completion-title">Profile Strength</h3>
                        <div class="completion-badge" id="strengthBadge">Beginner</div>
                    </div>

                    <div class="strength-meter">
                        <div class="strength-fill" id="strengthFill"></div>
                        <div class="strength-percentage" id="strengthPercentage">0%</div>
                    </div>

                    <div class="completion-checklist">
                        <div class="checklist-item" id="check-department">
                            <span class="check-icon">○</span>
                            <span class="check-text">Department Information</span>
                        </div>
                        <div class="checklist-item" id="check-rank">
                            <span class="check-icon">○</span>
                            <span class="check-text">Academic Rank</span>
                        </div>
                        <div class="checklist-item" id="check-research">
                            <span class="check-icon">○</span>
                            <span class="check-text">Research Interests</span>
                        </div>
                        <div class="checklist-item" id="check-expertise">
                            <span class="check-icon">○</span>
                            <span class="check-text">Areas of Expertise</span>
                        </div>
                        <div class="checklist-item" id="check-bio">
                            <span class="check-icon">○</span>
                            <span class="check-text">Academic Biography</span>
                        </div>
                    </div>

                    <div class="completion-benefits">
                        <h4 class="benefits-title">Complete Profile Benefits</h4>
                        <ul class="benefits-list">
                            <li>Connect with motivated students</li>
                            <li>Showcase your research areas</li>
                            <li>Build your academic network</li>
                            <li>Mentor talented freelancers</li>
                        </ul>
                    </div>
                </div>

                <!-- Academic Badge -->
                <div class="academic-badge-card">
                    <div class="badge-icon">🏆</div>
                    <div class="badge-content">
                        <h4 class="badge-title">Verified Lecturer</h4>
                        <p class="badge-description">Complete your profile to receive your verified badge</p>
                    </div>
                </div>
            </div>

            <!-- Right Panel -->
            <div class="lecturer-right">
                <div class="profile-form-card">
                    <!-- Form Header -->
                    <div class="form-header">
                        <h1 class="form-title">Lecturer Profile Setup</h1>
                        <p class="form-subtitle">Build your academic presence on StudentLancer</p>
                    </div>

                    <!-- Profile Form -->
                    <form class="lecturer-form" id="lecturerForm" method="POST">
                        <!-- Department -->
                        <div class="form-group">
                            <label class="form-label" for="department">Department *</label>
                            <div class="input-wrapper">
                                <span class="field-icon">🏫</span>
                                <select
                                    id="department"
                                    name="department"
                                    class="form-input form-select"
                                    required
                                    onchange="updateCompletion()">
                                    <option value="" disabled selected>Select your department</option>
                                    <option value="Electrical and Information Engineering"
                                        <?= $department === 'Electrical and Information Engineering' ? 'selected' : '' ?>>
                                        Electrical and Information Engineering
                                    </option>
                                    <option value="Civil Engineering"
                                        <?= $department === 'Civil Engineering' ? 'selected' : '' ?>>
                                        Civil Engineering
                                    </option>
                                    <option value="Mechanical Engineering"
                                        <?= $department === 'Mechanical Engineering' ? 'selected' : '' ?>>
                                        Mechanical Engineering
                                    </option>
                                    <option value="Petroleum Engineering"
                                        <?= $department === 'Petroleum Engineering' ? 'selected' : '' ?>>
                                        Petroleum Engineering
                                    </option>
                                    <option value="Chemical Engineering"
                                        <?= $department === 'Chemical Engineering' ? 'selected' : '' ?>>
                                        Chemical Engineering
                                    </option>

                                    <option value="Computer Science"
                                        <?= $department === 'Computer Science' ? 'selected' : '' ?>>
                                        Computer Science
                                    </option>
                                    <option value="Mathematics"
                                        <?= $department === 'Mathematics' ? 'selected' : '' ?>>
                                        Mathematics
                                    </option>

                                    <option value="Physics"
                                        <?= $department === 'Physics' ? 'selected' : '' ?>>
                                        Physics
                                    </option>

                                    <option value="Chemistry"
                                        <?= $department === 'Chemistry' ? 'selected' : '' ?>>
                                        Chemistry
                                    </option>

                                    <option value="Economics"
                                        <?= $department === 'Economics' ? 'selected' : '' ?>>
                                        Economics
                                    </option>

                                    <option value="Law"
                                        <?= $department === 'Law' ? 'selected' : '' ?>>
                                        Law
                                    </option>

                                    <option value="Medicine"
                                        <?= $department === 'Medicine' ? 'selected' : '' ?>>
                                        Medicine
                                    </option>

                                    <option value="Mass Communication"
                                        <?= $department === 'Mass Communication' ? 'selected' : '' ?>>
                                        Mass Communication
                                    </option>

                                    <option value="Arts & Design"
                                        <?= $department === 'Arts & Design' ? 'selected' : '' ?>>
                                        Arts & Design
                                    </option>

                                    <option value="Other"
                                        <?= $department === 'Other' ? 'selected' : '' ?>>
                                        Other
                                    </option>
                                </select>
                                <span class="select-arrow">▼</span>
                            </div>
                        </div>
                        <div
                            id="otherDepartmentContainer"
                            style="display:none;"
                            class="form-group">
                            <label class="form-label">
                                Specify Department
                            </label>

                            <input
                                type="text"
                                name="other_department"
                                id="otherDepartment"
                                class="form-input"
                                placeholder="Enter department name"
                                value="<?= htmlspecialchars(
                                            !in_array($department, [
                                                'Computer Science',
                                                'Engineering',
                                                'Business Administration',
                                                'Mathematics',
                                                'Physics',
                                                'Chemistry',
                                                'Economics',
                                                'Law',
                                                'Medicine',
                                                'Mass Communication',
                                                'Arts & Design'
                                            ]) ? $department : ''
                                        ) ?>">
                        </div>

                        <!-- Academic Rank -->
                        <div class="form-group">
                            <label class="form-label" for="academicRank">Academic Rank *</label>
                            <div class="input-wrapper">
                                <span class="field-icon">🎖️</span>
                                <select
                                    id="academicRank"
                                    name="rank"
                                    class="form-input form-select"
                                    required
                                    onchange="updateCompletion()">
                                    <option value="" disabled>Select your rank</option>
                                    <option value="Lab technician"
                                        <?= $rank === 'Graduate Assistant' ? 'selected' : '' ?>>
                                        Graduate Assistant
                                    </option>

                                    <option value="Graduate Assistant"
                                        <?= $rank === 'Graduate Assistant' ? 'selected' : '' ?>>
                                        Graduate Assistant
                                    </option>

                                    <option value="Assistant Lecturer"
                                        <?= $rank === 'Assistant Lecturer' ? 'selected' : '' ?>>
                                        Assistant Lecturer
                                    </option>

                                    <option value="Lecturer II"
                                        <?= $rank === 'Lecturer I' ? 'selected' : '' ?>>
                                        Lecturer II
                                    </option>

                                    <option value="Lecturer I"
                                        <?= $rank === 'Lecturer II' ? 'selected' : '' ?>>
                                        Lecturer I
                                    </option>

                                    <option value="Senior Lecturer"
                                        <?= $rank === 'Senior Lecturer' ? 'selected' : '' ?>>
                                        Senior Lecturer
                                    </option>

                                    <option value="Reader"
                                        <?= $rank === 'Reader' ? 'selected' : '' ?>>
                                        Reader
                                    </option>

                                    <option value="Professor"
                                        <?= $rank === 'Professor' ? 'selected' : '' ?>>
                                        Professor
                                    </option>
                                </select>
                                <span class="select-arrow">▼</span>
                            </div>
                            <p class="field-helper">Your current position in the university</p>
                        </div>

                        <!-- Research Interests -->
                        <div class="form-group">
                            <label class="form-label">Research Interests *</label>
                            <p class="field-helper-top">Select or add your primary research areas</p>

                            <div class="research-interests">
                                <div class="interest-suggestions">
                                    <button type="button" class="interest-chip" data-interest="Machine Learning" onclick="toggleInterest(this)">Machine Learning</button>
                                    <button type="button" class="interest-chip" data-interest="Data Science" onclick="toggleInterest(this)">Data Science</button>
                                    <button type="button" class="interest-chip" data-interest="Artificial Intelligence" onclick="toggleInterest(this)">Artificial Intelligence</button>
                                    <button type="button" class="interest-chip" data-interest="Web Development" onclick="toggleInterest(this)">Web Development</button>
                                    <button type="button" class="interest-chip" data-interest="Mobile Computing" onclick="toggleInterest(this)">Mobile Computing</button>
                                    <button type="button" class="interest-chip" data-interest="Cybersecurity" onclick="toggleInterest(this)">Cybersecurity</button>
                                    <button type="button" class="interest-chip" data-interest="Cloud Computing" onclick="toggleInterest(this)">Cloud Computing</button>
                                    <button type="button" class="interest-chip" data-interest="IoT" onclick="toggleInterest(this)">IoT</button>
                                    <button type="button" class="interest-chip" data-interest="Blockchain" onclick="toggleInterest(this)">Blockchain</button>
                                    <button type="button" class="interest-chip" data-interest="Software Engineering" onclick="toggleInterest(this)">Software Engineering</button>
                                </div>

                                <div class="custom-interest-input">
                                    <input
                                        type="text"
                                        id="customInterest"
                                        name="research_interests"
                                        class="form-input"
                                        placeholder="Add custom research interest and press Enter"
                                        onkeydown="handleInterestInput(event)">
                                </div>

                                <div class="selected-interests" id="selectedInterests"></div>
                            </div>
                        </div>

                        <!-- Areas of Expertise -->
                        <div class="form-group">
                            <label class="form-label">Areas of Expertise *</label>
                            <p class="field-helper-top">Add your teaching and professional expertise</p>

                            <div class="expertise-container">
                                <div class="expertise-input-wrapper">
                                    <span class="field-icon">🎯</span>
                                    <input
                                        type="text"
                                        id="expertiseInput"
                                        name="expertise"
                                        class="form-input"
                                        placeholder="Type an area of expertise and press Enter"
                                        onkeydown="handleExpertiseInput(event)">
                                </div>
                                <div class="expertise-tags" id="expertiseTags"></div>
                            </div>
                            <p class="field-helper">e.g., Software Architecture, Database Design, Algorithm Analysis</p>
                        </div>

                        <!-- Academic Biography -->
                        <div class="form-group">
                            <label class="form-label" for="biography">Academic Biography *</label>
                            <p class="field-helper-top">Share your academic journey, achievements, and professional experience</p>

                            <div class="textarea-wrapper">
                                <span class="field-icon">📝</span>
                                <textarea
                                    id="biography"
                                    name="biography"
                                    class="form-textarea"
                                    rows="8"
                                    maxlength="1000"
                                    placeholder="Write about your education, research, publications, awards, teaching experience..."
                                    oninput="updateCharCount(); updateCompletion()"
                                    required><?= htmlspecialchars($biography) ?></textarea>
                            </div>

                            <div class="textarea-footer">
                                <p class="field-helper">Minimum 100 characters recommended</p>
                                <span class="char-count" id="charCount">0 / 1000</span>
                            </div>
                        </div>

                        <!-- Profile Strength Summary -->
                        <div class="profile-strength-summary">
                            <div class="summary-row">
                                <span class="summary-label">Profile Completeness</span>
                                <span class="summary-value" id="summaryPercentage">0%</span>
                            </div>
                            <div class="summary-bar">
                                <div class="summary-fill" id="summaryFill"></div>
                            </div>
                            <p class="summary-message" id="summaryMessage">Complete all sections to unlock your verified badge</p>
                        </div>

                        <input type="hidden" name="research_interests" id="researchHidden">
                        <input type="hidden" name="expertise" id="expertiseHidden">

                        <!-- Submit Button -->
                        <button type="submit" class="btn-submit">
                            <span class="btn-text">
                                <?= $profile
                                    ? 'Update Profile'
                                    : 'Save Profile & Continue'
                                ?>

                            </span>
                            <span class="btn-arrow">→</span>
                        </button>

                        <!-- Footer Note -->
                        <p class="form-footer-note">
                            Your profile will be reviewed by our team before being made public
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="preview-modal" id="previewModal">
        <div class="preview-content">
            <div class="preview-header">
                <h3>Profile Preview</h3>
                <button class="preview-close" onclick="closePreview()">×</button>
            </div>
            <div class="preview-body" id="previewBody">
                <!-- Preview content will be injected here -->
            </div>
        </div>
    </div>
    <script>
        const existingResearch =
            <?= json_encode(
                array_filter(
                    array_map(
                        'trim',
                        explode(',', $research_interests)
                    )
                )
            ) ?>;

        const existingExpertise =
            <?= json_encode(
                array_filter(
                    array_map(
                        'trim',
                        explode(',', $expertise)
                    )
                )
            ) ?>;
    </script>
    <script src="lecturer-profile-setup-script.js"></script>
</body>

</html>