<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

/* STUDENT */

if ($role === 'student') {

    $stmt = $pdo->prepare("
        SELECT id
        FROM student_profiles
        WHERE user_id = ?
    ");

    $stmt->execute([$user_id]);

    if (!$stmt->fetch()) {

        header(
            "Location: ../profile/student-profile-setup.php"
        );

        exit();
    }
}

/* LECTURER */

if ($role === 'lecturer') {

    $stmt = $pdo->prepare("
        SELECT id
        FROM lecturer_profiles
        WHERE user_id = ?
    ");

    $stmt->execute([$user_id]);

    if (!$stmt->fetch()) {

        header(
            "Location: ../profile/lecturer-profile-setup.php"
        );

        exit();
    }
}

/* COMPANY */

if ($role === 'company') {

    header(
        "Location: ../dashboard/overview.php"
    );

    exit();
}

header(
    "Location: ../dashboard/overview.php"
);

exit();