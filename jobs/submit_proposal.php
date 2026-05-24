<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$student_id = $_SESSION['user_id'];

if (!isset($_GET['job_id'])) {
    die("Job not specified");
}

$job_id = $_GET['job_id'];

/* FETCH JOB */

$stmt = $pdo->prepare("
SELECT id, title, budget, created_by
FROM jobs
WHERE id = ? AND status = 'open'
");

$stmt->execute([$job_id]);
$job = $stmt->fetch();

if (!$job) {
    die("Job not found or closed");
}

/* PREVENT APPLYING TO OWN JOB */

if ($job['created_by'] == $student_id) {
    die("You cannot apply to your own job.");
}

/* CHECK IF STUDENT ALREADY APPLIED */

$stmt = $pdo->prepare("
SELECT id
FROM applications
WHERE job_id = ? AND student_id = ?
");

$stmt->execute([$job_id, $student_id]);
$already_applied = $stmt->fetch();

/* HANDLE FORM SUBMISSION */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if ($already_applied) {
        die("You already submitted a proposal for this job.");
    }

    $bid_amount = $_POST['bid_amount'];
    $delivery_days = $_POST['estimated_delivery_days'];

    $cover_letter_name = null;
    $sample_file = null;

    /* COVER LETTER */

    if (!empty($_FILES['cover_letter']['name'])) {

        $coverDir = "../uploads/cover_letters/";

        if (!is_dir($coverDir)) {
            mkdir($coverDir, 0777, true);
        }

        $cover_letter_name = time() . "_" . basename($_FILES['cover_letter']['name']);

        move_uploaded_file(
            $_FILES['cover_letter']['tmp_name'],
            $coverDir . $cover_letter_name
        );
    }

    /* SAMPLE WORK */

    if (!empty($_FILES['sample_file']['name'])) {

        $sampleDir = "../uploads/samples/";

        if (!is_dir($sampleDir)) {
            mkdir($sampleDir, 0777, true);
        }

        $sample_file = time() . "_" . basename($_FILES['sample_file']['name']);

        move_uploaded_file(
            $_FILES['sample_file']['tmp_name'],
            $sampleDir . $sample_file
        );
    }


    /* JOB SKILLS */

    $stmt = $pdo->prepare("

SELECT skill_id

FROM job_skills

WHERE job_id=?

");

    $stmt->execute([
        $job_id
    ]);

    $job_skills =
        $stmt->fetchAll(
            PDO::FETCH_COLUMN
        );

    $total_skills =
        max(
            count(
                $job_skills
            ),
            1
        );

    /* STUDENT SKILLS */

    $stmt = $pdo->prepare("

SELECT skill_id

FROM student_skills

WHERE user_id=?

");

    $stmt->execute([
        $student_id
    ]);

    $student_skills =
        $stmt->fetchAll(
            PDO::FETCH_COLUMN
        );

    $matched_skills =
        count(

            array_intersect(

                $job_skills,

                $student_skills

            )

        );

    $skill_match =
        $matched_skills
        /
        $total_skills;

    /* STUDENT DATA */

    $stmt = $pdo->prepare("

SELECT

rating,

completed_jobs

FROM users

WHERE id=?

");

    $stmt->execute([
        $student_id
    ]);

    $student =
        $stmt->fetch();

    $rating =
        $student['rating']
        ?? 0;

    $completed =
        $student['completed_jobs']
        ?? 0;

    /* PYTHON */

    $python =
        "C:\\xampp\\htdocs\\EL-ROI\\AI\\winvenv\\Scripts\\python.exe";

    $predict =
        "C:\\xampp\\htdocs\\EL-ROI\\AI\\predict.py";

    $command =

        "\"$python\" \"$predict\" "

        . $skill_match . " "

        . $matched_skills . " "

        . $rating . " "

        . $completed

        . " 2>&1";

    $ml =
        (float)

        trim(

            shell_exec(

                $command

            )

        );

    /* HYBRID */

    $final =

        ($skill_match * 70)

        +

        (($rating / 5) * 20)

        +

        min(
            $completed,
            10
        )

        +

        ($ml * 0.15);

    $final =
        round(

            min(
                100,
                $final
            ),

            2

        );
    /* INSERT APPLICATION */

    $stmt = $pdo->prepare("
    INSERT INTO applications
    (job_id, student_id, bid_amount, estimated_delivery_days, cover_letter_file, sample_file, ai_score, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
    ");

    $stmt->execute([
        $job_id,
        $student_id,
        $bid_amount,
        $delivery_days,
        $cover_letter_name,
        $sample_file,
        $final
    ]);

    header("Location: browse_jobs.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <title>Submit Proposal</title>

    <link rel="stylesheet" href="../dist/output.css">

</head>

<body class="bg-[#F7F8FA]">

    <div class="max-w-2xl mx-auto py-10 px-6">

        <h1 class="text-2xl font-semibold mb-6">
            Apply for: <?= htmlspecialchars($job['title']) ?>
        </h1>

        <div class="bg-white p-6 rounded-lg shadow">

            <p class="text-gray-600 mb-6">
                Project Budget: ₦<?= number_format($job['budget']) ?>
            </p>

            <form method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">

                <!-- Cover Letter -->

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Cover Letter (PDF)
                    </label>

                    <label class="flex items-center justify-between border border-gray-300 rounded-lg px-4 py-3 cursor-pointer hover:border-[#4B2E83] transition">

                        <span id="fileName" class="text-gray-500 text-sm">
                            Choose your cover letter
                        </span>

                        <span class="bg-[#4B2E83] text-white text-sm px-4 py-2 rounded-lg">
                            Browse
                        </span>

                        <input
                            type="file"
                            name="cover_letter"
                            accept=".pdf"
                            required
                            class="hidden"
                            onchange="updateFileName(this)">
                    </label>
                </div>


                <!-- Bid Amount -->

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Your Bid (₦)
                    </label>

                    <input
                        type="number"
                        name="bid_amount"
                        required
                        placeholder="Enter your bid amount"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3
                            focus:ring-2 focus:ring-[#4B2E83] focus:border-[#4B2E83]
                            outline-none transition">
                </div>


                <!-- Estimated Delivery -->

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Estimated Delivery Time (days)
                    </label>

                    <input
                        type="number"
                        name="estimated_delivery_days"
                        required
                        placeholder="How many days will you take?"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3
                            focus:ring-2 focus:ring-[#4B2E83] focus:border-[#4B2E83]
                            outline-none transition">
                </div>


                <!-- Portfolio -->

                <!-- <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Portfolio Link
                    </label>

                    <input
                        type="url"
                        name="portfolio_link"
                        placeholder="https://yourportfolio.com"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3
                            focus:ring-2 focus:ring-[#4B2E83] focus:border-[#4B2E83]
                            outline-none transition">
                </div> -->


                <!-- Sample Work -->

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Sample Work (Optional)
                    </label>

                    <label class="flex items-center justify-between border border-gray-300 rounded-lg px-4 py-3 cursor-pointer hover:border-[#4B2E83] transition">

                        <span id="sampleFileName" class="text-gray-500 text-sm">
                            Attach a sample of your work
                        </span>

                        <span class="bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg">
                            Browse
                        </span>

                        <input
                            type="file"
                            name="sample_file"
                            accept=".pdf,.png,.jpg,.jpeg,.zip,.doc,.docx"
                            class="hidden"
                            onchange="updateSampleFileName(this)">

                    </label>

                    <p class="text-xs text-gray-500 mt-1">
                        Optional: upload design files, writing samples, screenshots, etc.
                    </p>

                </div>



                <button
                    class="w-full bg-[#4B2E83] text-white py-3 rounded-lg font-medium
                    hover:bg-[#5d3a9e] transition">

                    Submit Proposal

                </button>

            </form>

        </div>

    </div>
    <script>
        function updateFileName(input) {

            const fileName = input.files[0]?.name || "Choose your cover letter (PDF)";

            document.getElementById("fileName").textContent = fileName;

        }

        function updateSampleFileName(input) {

            const fileName = input.files[0]?.name || "Attach a sample of your work";

            document.getElementById("sampleFileName").textContent = fileName;

        }
    </script>
</body>

</html>