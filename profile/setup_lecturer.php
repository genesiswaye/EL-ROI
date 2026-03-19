<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $department = $_POST['department'];
    $rank = $_POST['rank'];

    $research_interests = $_POST['expertise'] ?? '';
    $research_interests = json_decode($research_interests, true);

    $interests = [];

    if (!empty($research_interests)) {

        foreach ($research_interests as $item) {

            $interest = trim($item['value']);

            if ($interest !== "") {
                $interests[] = $interest;
            }
        }
    }

    /* convert to text */

    $researchText = implode(", ", $interests);

    /* insert lecturer profile */

    $stmt = $pdo->prepare("
        INSERT INTO lecturer_profiles
        (user_id, department, rank, research_interests)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $user_id,
        $department,
        $rank,
        $researchText
    ]);

    header("Location: ../dashboard/lecturer.php");
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
                <label class="text-sm font-medium text-gray-700 mb-1">Department</label>
                <input type="text" name="dept"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Office location</label>
                <input type="text" name="dept"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div> -->

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Academic Rank</label>
                <select name="level"
                    class="border border-gray-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select position</option>
                    <option value="Professor">Professor</option>
                    <option value="Associate Professor">Associate Professor</option>
                    <option value="Senior Lecturer">Senior Lecturer</option>
                    <option value="Lecturer">Lecturer</option>
                    <option value="Assistant Lecturer">Assistant Lecturer</option>
                </select>
            </div>

            

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Research Interest</label>
                <input id="expertise" name="expertise"
                    placeholder="Your interests and areas of expertise (type and press Enter)"
                    class="border border-gray-300 rounded-lg px-3 py-2 ">
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

        var input = document.querySelector('#expertise');

        new Tagify(input, {
            duplicates: false,
            maxTags: 8
        });
    </script>

</body>

</html>