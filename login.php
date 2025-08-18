<?php
require_once __DIR__ . '/vendor/autoload.php';

$client = new Google_Client();
$client->setClientId(getenv('GOOGLE_CLIENT_ID') ?: '');
$client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET') ?: '');
$client->setRedirectUri('http://localhost/topicsproject1/index.php');
$client->addScope('email');
$client->addScope('profile');
