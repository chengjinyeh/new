<?php
// SQL Server 隡箸??典?蝔望? IP
$serverName = "localhost,1433"; // 憒??舀璈?閮剖?嚗遣霅啣?銝?,1433
// $serverName = "192.168.1.100,1433"; // 憒?閬??嗡?璈???嚗隡箸??函? IP

// ????賊?
$connectionOptions = [
    "Database" => "Wsbp",         // 雿?鞈?摨怠?蝔?
    "Uid" => "wsbpinfo",         // 雿? SQL Server 撣唾?
    "PWD" => "5members",     // 雿? SQL Server 撖Ⅳ
    "CharacterSet" => "UTF-8"     // ?踹?銝剜?鈭Ⅳ
];

// ?岫撱箇????
$conn = sqlsrv_connect($serverName, $connectionOptions);

// 瑼Ｘ?臬??
if ($conn === false) {
    die(" ?⊥????鞈?摨恬?" . print_r(sqlsrv_errors(), true));
} else {
    echo " 鞈?摨恍????嚗?;
}


// ?? Google 鞈?
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET') ?: '');
$client->setRedirectUri('http://localhost/雿???.php');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $google_oauth = new Google_Service_Oauth2($client);
    $user_info = $google_oauth->userinfo->get();

    $email = $user_info->email;
    $name = $user_info->name;

    // ?嗅??脰?鞈?摨怠???
}
?>
