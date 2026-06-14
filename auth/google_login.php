<?php
require_once '../vendor/autoload.php';

$config = json_decode(
    file_get_contents(__DIR__ . '/secret.json'),
    true
);

$client = new Google_Client();

$client->setClientId(
    $config['web']['client_id']
);

$client->setClientSecret(
    $config['web']['client_secret']
);

$client->setRedirectUri(
    $config['web']['redirect_uris'][0]
);

$client->addScope("openid");
$client->addScope("email");
$client->addScope("profile");

$client->setAccessType('online');

$authUrl = $client->createAuthUrl();

header('Location: ' . $authUrl);
exit();