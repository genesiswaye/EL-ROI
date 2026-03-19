<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ---------- FETCH SKILLS FOR DROPDOWN ---------- */

$stmt = $pdo->query("SELECT id, name FROM skills ORDER BY name");
$skills = $stmt->fetchAll();

/* ---------- FORM SUBMISSION ---------- */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $matnum = $_POST['matnum'];
    $dept   = $_POST['dept'];
    $level  = $_POST['level'];
    $bio    = $_POST['bio'];

    $selectedSkills = $_POST['skills'] ?? [];
    $customSkills = $_POST['custom_skills'] ?? '';
    $customSkills = json_decode($customSkills, true);

    /* ---------- INSERT STUDENT PROFILE ---------- */

    $stmt = $pdo->prepare("
        INSERT INTO student_profiles
        (user_id, matric_num, department, level, bio)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $user_id,
        $matnum,
        $dept,
        $level,
        $bio
    ]);

    /* ---------- INSERT SELECTED SKILLS ---------- */

    if (!empty($selectedSkills)) {

        foreach ($selectedSkills as $skill_id) {

            $stmt = $pdo->prepare("
                INSERT INTO student_skills (user_id, skill_id)
                VALUES (?, ?)
            ");

            $stmt->execute([$user_id, $skill_id]);
        }
    }

    /* ---------- HANDLE CUSTOM SKILLS ---------- */

    if (!empty($customSkills)) {

        foreach ($customSkills as $skillData) {

            $skill = trim($skillData['value']);

            if ($skill === "") continue;

            /* Insert skill if not exists */

            $stmt = $pdo->prepare("
                INSERT INTO skills (name)
                VALUES (?)
                ON DUPLICATE KEY UPDATE id=id
            ");

            $stmt->execute([$skill]);

            /* Get skill ID */

            $stmt = $pdo->prepare("
                SELECT id FROM skills WHERE name = ?
            ");

            $stmt->execute([$skill]);
            $skill_id = $stmt->fetchColumn();

            /* Link skill */

            $stmt = $pdo->prepare("
                INSERT INTO student_skills (user_id, skill_id)
                VALUES (?, ?)
            ");

            $stmt->execute([$user_id, $skill_id]);
        }
    }

    header("Location: ../dashboard/student.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dist/output.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <title>Document</title>
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">

    <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-lg">

        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Complete Your Profile</h2>
        <p class="text-gray-500 mb-6">Tell us a little about yourself</p>

        <form method="POST" class="space-y-5">

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Matriculation Number</label>
                <input type="text" name="matnum"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Department</label>
                <input type="text" name="dept"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Level</label>
                <select name="level"
                    class="border border-gray-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select level</option>
                    <option value="100">100 Level</option>
                    <option value="200">200 Level</option>
                    <option value="300">300 Level</option>
                    <option value="400">400 Level</option>
                    <option value="500">500 Level</option>
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Skills</label>
                <select id="skills" name="skills[]" multiple
                    class="border border-gray-300 rounded-lg">
                    <?php foreach ($skills as $skill): ?>

                        <option value="<?= $skill['id'] ?>">
                            <?= htmlspecialchars($skill['name']) ?>
                        </option>

                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Other Skills</label>
                <input id="custom_skills" name="custom_skills"
                    placeholder="Type a skill and press Enter"
                    class="border border-gray-300 rounded-lg px-3 py-2 ">
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Bio</label>
                <textarea name="bio" rows="4"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <button
                class="w-full bg-[#052a4d] text-white font-medium py-2.5 rounded-lg hover:bg-[#184776] transition duration-200">
                Save Profile
            </button>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script>
        new TomSelect("#skills", {
            plugins: ['remove_button'],
            maxItems: 5,
            placeholder: "Select up to 5 skills"
        });

        var input = document.querySelector('#custom_skills');

        new Tagify(input, {
            duplicates: false,
            maxTags: 5
        });
    </script>

</body>

</html>