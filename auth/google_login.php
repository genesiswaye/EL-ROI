<?php
require_once '../vendor/autoload.php';

$client = new Google_Client();

$client->setClientId("840221080643-an8np874f00nb74n99ot34fe5fdbskib.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost/EL-ROI/auth/google_callback.php");

$client->addScope("openid");
$client->addScope("email");
$client->addScope("profile");

$client->setAccessType('online');

$authUrl = $client->createAuthUrl();

header('Location: ' . $authUrl);
exit();