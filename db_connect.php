<?php
// SQL Server 伺服器名稱或 IP
$serverName = "localhost,1433"; // 如果是本機預設埠，建議加上 ,1433
// $serverName = "192.168.1.100,1433"; // 如果要從其他機器連線，用伺服器的 IP

// 連線選項
$connectionOptions = [
    "Database" => "Wsbp",         // 你的資料庫名稱
    "Uid" => "wsbpinfo",         // 你的 SQL Server 帳號
    "PWD" => "5members",     // 你的 SQL Server 密碼
    "CharacterSet" => "UTF-8"     // 避免中文亂碼
];

// 嘗試建立連線
$conn = sqlsrv_connect($serverName, $connectionOptions);

// 檢查是否成功
if ($conn === false) {
    die(" 無法連線資料庫：" . print_r(sqlsrv_errors(), true));
} else {
    echo " 資料庫連線成功！";
}


// 取得 Google 資訊
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/你的回呼頁面.php');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $google_oauth = new Google_Service_Oauth2($client);
    $user_info = $google_oauth->userinfo->get();

    $email = $user_info->email;
    $name = $user_info->name;

    // 然後才進行資料庫存取
}
?>