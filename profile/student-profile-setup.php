<?php

session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

/* LOAD PROFILE */

$stmt = $pdo->prepare("
    SELECT *
    FROM student_profiles
    WHERE user_id = ?
");

$stmt->execute([$user_id]);

$profile = $stmt->fetch(PDO::FETCH_ASSOC);

$isEdit = !empty($profile);

/* LOAD USER SKILLS */

$stmt = $pdo->prepare("
    SELECT skill_id
    FROM student_skills
    WHERE user_id = ?
");

$stmt->execute([$user_id]);

$userSkills = $stmt->fetchAll(PDO::FETCH_COLUMN);

/* LOAD ALL SKILLS */

$stmt = $pdo->query("
    SELECT id, name
    FROM skills
    ORDER BY name
");

$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* FORM SUBMISSION */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $matnum = trim($_POST['matnum'] ?? '');
  $dept   = trim($_POST['dept'] ?? '');
  $level  = trim($_POST['level'] ?? '');
  $bio    = trim($_POST['bio'] ?? '');

  $selectedSkills =
    json_decode($_POST['skills'] ?? '[]', true);

  if ($isEdit) {

    $stmt = $pdo->prepare("
            UPDATE student_profiles
            SET
                matric_num = ?,
                department = ?,
                level = ?,
                bio = ?
            WHERE user_id = ?
        ");

    $stmt->execute([
      $matnum,
      $dept,
      $level,
      $bio,
      $user_id
    ]);
  } else {

    $stmt = $pdo->prepare("
            INSERT INTO student_profiles
            (
                user_id,
                matric_num,
                department,
                level,
                bio
            )
            VALUES (?, ?, ?, ?, ?)
        ");

    $stmt->execute([
      $user_id,
      $matnum,
      $dept,
      $level,
      $bio
    ]);
  }

  /* REFRESH SKILLS */

  $stmt = $pdo->prepare("
        DELETE FROM student_skills
        WHERE user_id = ?
    ");

  $stmt->execute([$user_id]);

  // require_once "../AI/run_recommendations.php";

  // runRecommendations();

  foreach ($selectedSkills as $skill_id) {

    $stmt = $pdo->prepare("
            INSERT INTO student_skills
            (
                user_id,
                skill_id
            )
            VALUES (?, ?)
        ");

    $stmt->execute([
      $user_id,
      $skill_id
    ]);
  }

  // require_once "../AI/run_recommendations.php";

  // runRecommendations();

  header("Location: ../dashboard/overview.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complete Your Profile - StudentLancer</title>
  <link rel="stylesheet" href="student-profile-setup.css">
</head>

<body>
  <!-- Header -->
  <!-- <header class="profile-header">
    <div class="header-container">
      <div class="logo">
        <span class="logo-icon">🎓</span>
        <span class="logo-text">StudentLancer</span>
      </div>
      <div class="header-actions">
        <button class="btn-skip" onclick="skipProfile()">Skip for now</button>
        <button class="btn-logout" onclick="logout()">Logout</button>
      </div>
    </div>
  </header> -->

  <!-- Main Container -->
  <div class="profile-container">
    <div class="profile-grid">
      <!-- Left Column -->
      <div class="profile-left">
        <div class="illustration-container">
          <div class="student-illustration">
            <div class="laptop">
              <div class="laptop-screen">
                <div class="code-line"></div>
                <div class="code-line"></div>
                <div class="code-line short"></div>
              </div>
              <div class="laptop-base"></div>
            </div>
            <div class="student-avatar">
              <div class="avatar-head"></div>
              <div class="avatar-body"></div>
            </div>
            <div class="desk"></div>
            <div class="floating-icon icon-1">💡</div>
            <div class="floating-icon icon-2">📚</div>
            <div class="floating-icon icon-3">⭐</div>
          </div>
        </div>

        <div class="info-card">
          <h2 class="info-title">Complete Your Profile</h2>
          <p class="info-description">
            A complete profile helps us match you with the best freelance opportunities
            and increases your visibility to potential clients.
          </p>

          <div class="completion-indicator">
            <div class="completion-header">
              <span class="completion-label">Profile Completion</span>
              <span class="completion-percentage" id="completionPercentage">0%</span>
            </div>
            <div class="completion-bar">
              <div class="completion-progress" id="completionProgress"></div>
            </div>
          </div>

          <div class="benefits-list">
            <div class="benefit-item">
              <span class="benefit-icon">✓</span>
              <span class="benefit-text">Get personalized job recommendations</span>
            </div>
            <div class="benefit-item">
              <span class="benefit-icon">✓</span>
              <span class="benefit-text">Appear in client searches</span>
            </div>
            <div class="benefit-item">
              <span class="benefit-icon">✓</span>
              <span class="benefit-text">Build trust with verified information</span>
            </div>
            <div class="benefit-item">
              <span class="benefit-icon">✓</span>
              <span class="benefit-text">Unlock premium features</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div class="profile-right">
        <div class="form-card">
          <div class="form-header">
            <h1 class="form-title"><?= $isEdit ? 'Edit Profile' : 'Create Profile' ?></h1>
            <p class="form-subtitle"><?= $isEdit
                                        ? 'Update your information'
                                        : 'Let\'s get to know you better'
                                      ?></p>
          </div>

          <form method="POST" class="profile-form" id="profileForm" onsubmit="handleSubmit(event)">
            <!-- Matric Number -->
            <div class="form-group">
              <div class="input-wrapper">
                <input
                  type="text"
                  id="matricNumber"
                  name="matnum"
                  class="form-input"
                  placeholder=" "
                  required
                  pattern="[A-Z0-9]+"
                  value="<?= htmlspecialchars($profile['matric_num'] ?? '') ?>"
                  oninput="validateMatricNumber(this); updateCompletion()">
                <label for="matricNumber" class="form-label">Matric Number</label>
                <span class="input-icon">🎓</span>
                <span class="validation-icon" id="matricValidation"></span>
              </div>
            </div>

            <!-- Department -->
            <div class="form-group">
              <div class="input-wrapper">
                <select
                  id="department"
                  name="dept"
                  class="form-input form-select"
                  required
                  onchange="updateCompletion(); this.classList.add('has-value')">
                  <option value="" disabled selected></option>
                  <option value="EIE"
                    <?= (($profile['department'] ?? '') == 'EIE') ? 'selected' : '' ?>>
                    EIE
                  </option>
                  <option value="Electrical and Information Engineering"
                        <?= (($profile['department'] ?? '') === 'Electrical and Information Engineering') ? 'selected' : '' ?>>
                        Electrical and Information Engineering
                    </option>
                    <option value="Civil Engineering"
                        <?= (($profile['department'] ?? '') === 'Civil Engineering') ? 'selected' : '' ?>>
                        Civil Engineering
                    </option>
                    <option value="Mechanical Engineering"
                        <?= (($profile['department'] ?? '') === 'Mechanical Engineering') ? 'selected' : '' ?>>
                        Mechanical Engineering
                    </option>
                    <option value="Petroleum Engineering"
                        <?= (($profile['department'] ?? '') === 'Petroleum Engineering') ? 'selected' : '' ?>>
                        Petroleum Engineering
                    </option>
                    <option value="Chemical Engineering"
                        <?= (($profile['department'] ?? '') === 'Chemical Engineering') ? 'selected' : '' ?>>
                        Chemical Engineering
                    </option>

                    <option value="Computer Science"
                        <?= (($profile['department'] ?? '') === 'Computer Science') ? 'selected' : '' ?>>
                        Computer Science
                    </option>
                    <option value="Mathematics"
                        <?= (($profile['department'] ?? '') === 'Mathematics') ? 'selected' : '' ?>>
                        Mathematics
                    </option>

                    <option value="Physics"
                        <?= (($profile['department'] ?? '') === 'Physics') ? 'selected' : '' ?>>
                        Physics
                    </option>

                    <option value="Chemistry"
                        <?= (($profile['department'] ?? '') === 'Chemistry') ? 'selected' : '' ?>>
                        Chemistry
                    </option>

                    <option value="Economics"
                        <?= (($profile['department'] ?? '') === 'Economics') ? 'selected' : '' ?>>
                        Economics
                    </option>

                    <option value="Law"
                        <?= (($profile['department'] ?? '') === 'Law') ? 'selected' : '' ?>>
                        Law
                    </option>

                    <option value="Medicine"
                        <?= (($profile['department'] ?? '') === 'Medicine') ? 'selected' : '' ?>>
                        Medicine
                    </option>

                    <option value="Mass Communication"
                        <?= (($profile['department'] ?? '') === 'Mass Communication') ? 'selected' : '' ?>>
                        Mass Communication
                    </option>

                    <option value="Arts & Design"
                        <?= (($profile['department'] ?? '') === 'Arts & Design') ? 'selected' : '' ?>>
                        Arts & Design
                    </option>
                </select>
                <label for="department" class="form-label">Department</label>
                <span class="input-icon">🏫</span>
                <span class="select-arrow">▼</span>
              </div>
            </div>

            <!-- Level -->
            <div class="form-group">
              <div class="input-wrapper">
                <select
                  name="level"
                  id="level"
                  class="form-input form-select"
                  required
                  onchange="updateCompletion(); this.classList.add('has-value')">
                  <option value="" disabled selected></option>

                  <option value="100"
                    <?= (($profile['level'] ?? '') == '100') ? 'selected' : '' ?>>
                    100 Level
                  </option>
                  <option value="200"
                    <?= (($profile['level'] ?? '') == '200') ? 'selected' : '' ?>>
                    200 Level
                  </option>
                  <option value="300"
                    <?= (($profile['level'] ?? '') == '300') ? 'selected' : '' ?>>
                    300 Level
                  </option>
                  <option value="400"
                    <?= (($profile['level'] ?? '') == '400') ? 'selected' : '' ?>>
                    400 Level
                  </option>
                  <option value="500"
                    <?= (($profile['level'] ?? '') == '500') ? 'selected' : '' ?>>
                    500 Level
                  </option>
                </select>
                <label for="level" class="form-label">Current Level</label>
                <span class="input-icon">📊</span>
                <span class="select-arrow">▼</span>
              </div>
            </div>

            <!-- Skills Multi-Select -->
            <div class="form-group">
              <label class="field-label">Your Skills</label>
              <div class="skills-container">
                <div class="skill-options">

                  <?php foreach ($skills as $skill): ?>

                    <button
                      type="button"
                      class="skill-chip <?= in_array($skill['id'], $userSkills) ? 'active' : '' ?>"
                      data-id="<?= $skill['id'] ?>"
                      data-skill="<?= htmlspecialchars($skill['name']) ?>"
                      onclick="toggleSkill(this)">
                      <?= htmlspecialchars($skill['name']) ?>
                    </button>

                  <?php endforeach; ?>

                </div>

                <div class="selected-skills" id="selectedSkills"></div>
                <input
                  type="hidden"
                  name="skills"
                  id="skillsInput">
              </div>
            </div>

            <!-- Additional Skills -->
            <div class="form-group">
              <label class="field-label">Additional Skills (Optional)</label>
              <div class="tag-input-container">
                <div class="tags-wrapper" id="tagsWrapper">
                  <input
                    type="text"
                    id="additionalSkills"
                    class="tag-input"
                    placeholder="Type a skill and press Enter"
                    onkeydown="handleTagInput(event)">
                </div>
              </div>
              <span class="form-helper">Press Enter to add custom skills</span>
            </div>

            <!-- Bio -->
            <div class="form-group">
              <div class="input-wrapper textarea-wrapper">
                <textarea
                  id="bio"
                  name="bio"
                  class="form-input form-textarea"
                  placeholder=" "
                  maxlength="500"
                  rows="5"
                  oninput="updateCharCount(); updateCompletion()"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                <label for="bio" class="form-label">Bio</label>
                <span class="input-icon">📝</span>
              </div>
              <div class="textarea-footer">
                <span class="form-helper">Tell clients about yourself and your experience</span>
                <span class="char-count" id="charCount">0 / 500</span>
              </div>
            </div>

            <!-- Profile Completion Summary -->
            <div class="completion-summary">
              <div class="completion-summary-header">
                <span class="completion-summary-label">Profile Strength</span>
                <span class="completion-summary-value" id="completionSummary">Getting Started</span>
              </div>
              <div class="completion-summary-bar">
                <div class="completion-summary-progress" id="completionSummaryProgress"></div>
              </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">
              <span class="btn-text">Save Profile</span>
              <span class="btn-icon">→</span>
            </button>

            <!-- Optional Skip Link -->
            <div class="form-footer">
              <p class="footer-text">You can always update your profile later</p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="student-profile-setup.js"></script>
</body>

</html>