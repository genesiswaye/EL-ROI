<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $company_name = $_POST['company_name'];
    $industry = $_POST['industry'];
    $website = $_POST['website'];
    $cac_number = $_POST['cac_number'];
    $comp_description = $_POST['description'];

    $stmt = $pdo->prepare("
INSERT INTO company_profiles
(user_id, company_name, industry, website, cac_number, description)
VALUES (?, ?, ?, ?, ?, ?)
");

    $stmt->execute([
        $user_id,
        $company_name,
        $industry,
        $website,
        $cac_number,
        $comp_description
    ]);

    header("Location: ../dashboard/overview.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../dist/output.css">
    <title>Document</title>
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">

    <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-lg">

        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Complete Company Profile</h2>
        <p class="text-gray-500 mb-6">Tell students about your company</p>

        <form method="POST" class="space-y-5">

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Company Name</label>
                <input type="text" name="company_name"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Industry</label>

                <select name="industry"
                    class="border border-gray-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <option value="">Select Industry</option>
                    <option value="Technology">Technology</option>
                    <option value="Finance">Finance</option>
                    <option value="Healthcare">Healthcare</option>
                    <option value="Education">Education</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Media">Media</option>
                    <option value="Manufacturing">Manufacturing</option>
                    <option value="Consulting">Consulting</option>

                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Company Website</label>
                <input type="text" name="website"
                    placeholder="https://yourcompany.com"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">CAC Number</label>
                <input type="text" name="cac_number"
                    placeholder="RC123456"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
             <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Company description</label>
                <textarea name="description" rows="4"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <button
                class="w-full bg-[#052a4d] text-white font-medium py-2.5 rounded-lg hover:bg-[#184776] transition">
                Save Company Profile
            </button>

        </form>
    </div>

</body>

</html>