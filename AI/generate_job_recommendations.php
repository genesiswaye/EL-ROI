<?php

function generateJobRecommendations($job_id, $pdo)
{
    $python =
        "C:\\xampp\\htdocs\\EL-ROI\\AI\\winvenv\\Scripts\\python.exe";

    $predict =
        "C:\\xampp\\htdocs\\EL-ROI\\AI\\predict.py";

    /* JOB SKILLS */

    $stmt = $pdo->prepare("
        SELECT skill_id
        FROM job_skills
        WHERE job_id = ?
    ");

    $stmt->execute([$job_id]);

    $job_skills =
        array_map(
            'intval',
            $stmt->fetchAll(PDO::FETCH_COLUMN)
        );

    if (empty($job_skills)) {
        return;
    }

    $total_required_skills =
        max(count($job_skills), 1);

    /* GET ALL STUDENTS */

    $stmt = $pdo->prepare("
        SELECT
            id,
            rating,
            completed_jobs
        FROM users
        WHERE role = 'student'
    ");

    $stmt->execute();

    $students =
        $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($students as $student) {

        /* STUDENT SKILLS */

        $stmt = $pdo->prepare("
            SELECT skill_id
            FROM student_skills
            WHERE user_id = ?
        ");

        $stmt->execute([
            $student['id']
        ]);

        $student_skills =
            array_map(
                'intval',
                $stmt->fetchAll(PDO::FETCH_COLUMN)
            );

        /* MATCHING */

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
            $total_required_skills;

        /* PYTHON */

        $command =
            "\"$python\" \"$predict\" "
            . $skill_match . " "
            . $matched_skills . " "
            . ($student['rating'] ?? 0) . " "
            . ($student['completed_jobs'] ?? 0)
            . " 2>&1";

        $ml =
            (float) trim(
                shell_exec($command)
            );

        /* FINAL SCORE */

        $score =
            ($skill_match * 70)
            +
            ((($student['rating'] ?? 0) / 5) * 20)
            +
            min(
                ($student['completed_jobs'] ?? 0),
                10
            )
            +
            ($ml * 0.15);

        $score =
            round(
                min(100, $score),
                2
            );

        /* SAVE */

        $stmt = $pdo->prepare("
            INSERT INTO job_recommendations
            (
                student_id,
                job_id,
                ai_score
            )
            VALUES
            (
                ?,
                ?,
                ?
            )

            ON DUPLICATE KEY UPDATE

            ai_score = VALUES(ai_score),
            updated_at = CURRENT_TIMESTAMP
        ");

        $stmt->execute([
            $student['id'],
            $job_id,
            $score
        ]);
    }
}