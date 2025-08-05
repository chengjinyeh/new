<?php
require_once 'vendor/autoload.php'; // 如果用Composer

$client = new Google_Client();
$client->setClientId('252515924130-28srma0olh5ednls8v1rkjl4esoui0jr.apps.googleusercontent.com');
$client->setRedirectUri('http://localhost/topicsproject1/index.php');
$client->addScope("email");
$client->addScope("profile");

$auth_url = $client->createAuthUrl();
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
exit;
?>
