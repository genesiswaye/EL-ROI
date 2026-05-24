<?php

function requireRole(array $roles)
{
    if (!isset($_SESSION['user_id'])) {

        header("Location: ../auth/login.php");
        exit();
    }

    if (!in_array($_SESSION['role'], $roles)) {

        header(
            "Location: ../errors/error.php?message=" .
            urlencode("Unauthorized access")
        );

        exit();
    }
}


require_once "../includes/authorize.php";

requireRole(['student']);

requireRole([
    'lecturer',
    'company'
]);

