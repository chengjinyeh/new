<?php
require_once 'vendor/autoload.php';

// Google 驗證
$client = new Google_Client();
$client->setClientId('你的ClientID');
$client->setClientSecret('你的Secret');
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

    // 連接 SQL Server
    $serverName = "localhost,1433";
    $connectionOptions = [
        "Database" => "Wsbp",
        "Uid" => "wsbpinfo",
        "PWD" => "5members",
        "CharacterSet" => "UTF-8"
    ];
    $conn = sqlsrv_connect($serverName, $connectionOptions);

    if ($conn === false) {
        die("資料庫連線失敗：" . print_r(sqlsrv_errors(), true));
    }

    // 檢查是否已存在
    $sql = "SELECT * FROM [User] WHERE email = ?";
    $params = [$email];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if (!sqlsrv_has_rows($stmt)) {
        // 新使用者，自動註冊
        $insert = "INSERT INTO [User] (uid, email, password, registration_date)
                   VALUES (?, ?, '', GETDATE())";
        $params = [$name, $email];
        sqlsrv_query($conn, $insert, $params);
    }

    echo "登入成功，歡迎 $name（$email）";
}
?>
