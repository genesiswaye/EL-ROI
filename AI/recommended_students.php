<?php

require "../config/database.php";

if (!isset($_GET['job_id'])) {
    die("Job ID missing");
}

$job_id = $_GET['job_id'];

$stmt = $pdo->prepare("
    SELECT skill_id
    FROM job_skills
    WHERE job_id = ?
");

$stmt->execute([$job_id]);

$job_skills = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($job_skills)) {
    die("This job has no skills assigned");
}

$total_required_skills = count($job_skills);

$placeholders = implode(
    ',',
    array_fill(0, count($job_skills), '?')
);

$sql = "

SELECT 
    users.id,
    users.full_name,
    users.rating,
    users.completed_jobs,


    COUNT(*) AS matched_skills

FROM student_skills

JOIN users
ON users.id = student_skills.user_id

WHERE student_skills.skill_id IN ($placeholders)

AND users.role = 'student'

GROUP BY users.id

ORDER BY matched_skills DESC

";

$stmt = $pdo->prepare($sql);

$stmt->execute($job_skills);

$recommended_students = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommended Students</title>
</head>

<body>

    <?php

    foreach ($recommended_students as $student) {

        $matched_skills = $student['matched_skills'];

        $skill_match = 0;

        if ($total_required_skills > 0) {

            $skill_match =
                $matched_skills / $total_required_skills;
        }

        $match_percentage =
            round($skill_match * 100);

        $python =
            "C:\\xampp\\htdocs\\EL-ROI\\AI\\winvenv\\Scripts\\python.exe";

        $predict =
            "C:\\xampp\\htdocs\\EL-ROI\\AI\\predict.py";

        $rating =
            $student['rating'];

        $completed_jobs =
            $student['completed_jobs'];

        $command =
"\"$python\" \"$predict\" "
. $skill_match . " "
. $matched_skills . " "
// . $total_required_skills . " "
. $rating . " "
. $completed_jobs
. " 2>&1";

        $ai_score =
            shell_exec($command);


    ?>

        <div>

            <h3>
                <?= htmlspecialchars($student['full_name']); ?>
            </h3>

            <p>
                AI Match Score:
                <?= trim($ai_score) ?>%
            </p>

        </div>

        <hr>

    <?php
    }
    ?>

</body>

</html>