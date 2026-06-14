<?php
require_once '../vendor/autoload.php';
require_once '../config/database.php';

session_start();

// $_SESSION['is_admin'] = $user['is_admin'] ?? 0;

$client = new Google_Client();

$client->setClientId("840221080643-an8np874f00nb74n99ot34fe5fdbskib.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost/EL-ROI/auth/google_callback.php");

if (!isset($_GET['code'])) {
    die("Authorization code not found.");
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    die("Token error: " . $token['error']);
}

if (!isset($token['access_token'])) {
    die("Authentication failed.");
}

$client->setAccessToken($token['access_token']);

$oauth = new Google_Service_Oauth2($client);
$userInfo = $oauth->userinfo->get();

$email = $userInfo->email;
$name  = $userInfo->name;
$google_id = $userInfo->id;

$domain = substr(strrchr($email, "@"), 1);

if ($domain === "stu.cu.edu.ng" || $domain === "cu.edu.ng") {
    $role = "student";
} elseif ($domain === "covenantuniversity.edu.ng") {
    $role = "lecturer";
} else {
    die("Only Covenant University accounts allowed.");
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE google_id = ?");
$stmt->execute([$google_id]);
$user = $stmt->fetch();

if (!$user) {

    $stmt = $pdo->prepare("
        INSERT INTO users (role, full_name, email, google_id, password, is_email_verified)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $role,
        $name,
        $email,
        $google_id,
        NULL,
        1
    ]);

    $user_id = $pdo->lastInsertId();

    // CREATE WALLET FOR NEW USER
    $stmt = $pdo->prepare("
    INSERT INTO wallets (user_id, balance, held_balance, currency)
    VALUES (?, 0.00, 0.00, 'NGN')
");
    $stmt->execute([$user_id]);
} else {
    $user_id = $user['id'];

    $stmt = $pdo->prepare("SELECT id FROM wallets WHERE user_id = ?");
    $stmt->execute([$user_id]);

    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("
        INSERT INTO wallets (user_id, balance, held_balance, currency)
        VALUES (?, 0.00, 0.00, 'NGN')
    ");
        $stmt->execute([$user_id]);
    }

    $role = $user['role'];
}

$_SESSION['user_id'] = $user_id;
$_SESSION['role'] = $role;
$_SESSION['email'] = $email;
$_SESSION['name'] = $name;


$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

/* =========================
   TERMS & CONDITIONS CHECK
========================= */

if (
    !isset($user['terms_accepted']) ||
    (int)$user['terms_accepted'] === 0
) {

    header("Location: ../terms/terms.php");
    exit();
}

$_SESSION['is_admin'] = $user['is_admin'] ?? 0;

/* ---------- STUDENT ---------- */

if ($role === "student") {

    $stmt = $pdo->prepare("SELECT * FROM student_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch();

    if (!$profile) {
        header("Location: ../profile/student-profile-setup.php");
        exit();
    } else {
        header("Location: ../dashboard/overview.php");
        exit();
    }
}



/* ---------- LECTURER ---------- */ elseif ($role === "lecturer") {

    $stmt = $pdo->prepare("SELECT * FROM lecturer_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch();

    if (!$profile) {
        header("Location: ../profile/lecturer-profile-setup.php");
        exit();
    } else {
        header("Location: ../dashboard/overview.php");
        exit();
    }
}

exit();
