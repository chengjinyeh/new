<?php
require_once 'vendor/autoload.php';

// Google 撽?
$client = new Google_Client();
$client->setClientId('雿?ClientID');
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

    // ?? SQL Server
    $serverName = "localhost,1433";
    $connectionOptions = [
        "Database" => "Wsbp",
        "Uid" => "wsbpinfo",
        "PWD" => "5members",
        "CharacterSet" => "UTF-8"
    ];
    $conn = sqlsrv_connect($serverName, $connectionOptions);

    if ($conn === false) {
        die("鞈?摨恍??憭望?嚗? . print_r(sqlsrv_errors(), true));
    }

    // 瑼Ｘ?臬撌脣???
    $sql = "SELECT * FROM [User] WHERE email = ?";
    $params = [$email];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if (!sqlsrv_has_rows($stmt)) {
        // ?唬蝙?刻??芸?閮餃?
        $insert = "INSERT INTO [User] (uid, email, password, registration_date)
                   VALUES (?, ?, '', GETDATE())";
        $params = [$name, $email];
        sqlsrv_query($conn, $insert, $params);
    }

    echo "?餃??嚗迭餈?$name嚗?email嚗?;
}
?>

