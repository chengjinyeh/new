<?php
// 引入 Google API 客戶端庫並啟動 session
require_once __DIR__ . '/vendor/autoload.php';
session_start();

// 初始化 Google Client
$client = new Google_Client();
$client->setClientId(getenv('GOOGLE_CLIENT_ID') ?: '');
$client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET') ?: '');
$client->setRedirectUri('http://localhost/topicsproject1/index.php');
$client->addScope('email');
$client->addScope('profile');
// ========= 處理 Google 登入回調的核心程式碼 =========
// 當 Google 授權後，會將使用者導回此頁面並附上 ?code=...
if (isset($_GET['code'])) {
    try {
        // 用收到的 code 換取 access token
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        // 如果成功取得 token，就設定到 client 中
        if (!isset($token['error'])) {
            $client->setAccessToken($token['access_token']);

            // 【***** 主要修正點 *****】
            // 使用完整的命名空間來實例化 Oauth2 服務，以避免類別找不到的錯誤
            $google_oauth = new \Google\Service\Oauth2($client);
            
            // 取得使用者的 Google 帳戶資料
            $google_account_info = $google_oauth->userinfo->get();
            
            // 將使用者資訊存入 Session
            $_SESSION['user_name'] = $google_account_info->name;
            $_SESSION['user_email'] = $google_account_info->email;
            
            $serverName = "localhost,1433"; // 或你的 SQL Server 埠號
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

            // 取得 Google 帳戶資訊
            $email = $google_account_info->email;
            $name = $google_account_info->name;

            // 查詢資料庫是否已有此使用者
            $sql = "SELECT * FROM [User] WHERE email = ?";
            $params = [$email];
            $stmt = sqlsrv_query($conn, $sql, $params);

            if (!sqlsrv_has_rows($stmt)) {
                // 若使用者尚未註冊，則自動寫入資料庫
                $insert = "INSERT INTO [User] (uid, email, password, registration_date)
                        VALUES (?, ?, '', GETDATE())";
                $insertParams = [$name, $email];
                sqlsrv_query($conn, $insert, $insertParams);
            }
            
            // 登入成功後，重新導向到 index.php 的乾淨 URL (移除 code 參數)
            header('Location: index.php');
            exit;
        }
    } catch (Exception $e) {
        // 如果出錯，可以記錄錯誤或顯示錯誤訊息
        die('處理 Google 登入時發生錯誤: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html> <!-- 定義為 HTML5 文件 -->
<html lang="zh-TW"> <!-- 設定頁面語言為繁體中文 -->
<head>
    <meta charset="UTF-8"> <!-- 指定編碼為 UTF-8，支援中英文等多語系 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- 響應式設計，適應各種裝置 -->
    <!--=============== FAVICON ===============-->
    <link rel="shortcut icon" href="" type="image/x-icon"> <!-- 網站小圖示 -->
    <!--=============== BOXICON ===============-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'> <!-- 載入 Boxicons 圖示字型 -->

    <!--=============== SWIPER CSS ===============-->
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css"> <!-- 載入 Swiper 輪播元件 CSS -->
    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="assets/css/style.css"> <!-- 載入網站主要 CSS 樣式表 -->
    <title>WS&BP website security</title> <!-- 網頁標題 -->
    <!--=============== Google 登入 ===============-->
    <script src="https://accounts.google.com/gsi/client" async defer></script> <!-- Google 登入所需腳本 -->
</head>
<body>
    <!--==================== HEADER ====================-->
    <header class="header" id="header">
        <nav class="nav container">
            <a href="http://localhost/topics%20project/" class="nav__logo">
                ws&bp <i class='bx bx-home-alt-2'></i>
            </a>
            <div class="nav__menu">
                <ul class="nav__list">
                    <li class="nav__item">
                        <a href="#popular" class="nav__link ">
                            <i class='bx bx-play-circle'></i>
                            <span>Video</span>
                        </a>
                    </li>
                    <li class="nav__item">
                        <a href="#subscribe" class="nav__link">
                            <i class='bx bx-error'></i>
                            <span>全民檢舉</span>
                        </a>
                    </li>
                    <?php if(isset($_SESSION['user_name'])): ?>
                    <li class="nav__item">
                        <a href="logout.php" class="nav__link">
                            <i class='bx bx-log-out'></i>
                            <span>登出</span>
                        </a>
                    </li>
                    <?php else: ?>
                        <li class="nav__item">
                            <a href="#subscribe" class="nav__link">
                                <i class='bx bx-search'></i>
                                <span>登入</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav__item">
                        <a href="#footer" class="nav__link">
                            <i class='bx bx-cog'></i>
                            <span>關於我們</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Theme change button -->
            <i class='bx bx-moon change-theme' id="theme-button"></i>
        </nav>
    </header>
    <!--==================== MAIN ====================-->
    <main class="main"> <!-- 主要內容區 -->
        <!--==================== HOME ====================-->
        <section class="home section" id="home"> <!-- 首頁大區塊 -->
            <div class="home__container container grid"> <!-- 首頁內容容器，使用格線排版 -->
                <div class="home__data"> <!-- 文字與按鈕內容區 -->
                    <h2 class="home__subtitle">WS&BP</h2> <!-- 副標題 -->
                    <h2 class="home__title">
                        守護你的網站安全
                    </h2>
                    <p class="home__description">以最先進的技術，提供全面性網站安全防護服務。</p>

                    <div class="home__download-row">
                            <span class="download-label">插件下載</span>
                            <i class="bx bx-right-arrow-alt download-arrow"></i>
                            <a href="/topicsproject1/my-chrome-extension.zip" download="my-chrome-extension.zip" class="button">
                                download
                        </a>
                    </div>

                    
                </div>
                <div class="home__images">
                    <div class="home__img-wrapper">
                        <img src="assets/img/logo1.png" alt="Logo" class="home__logo-img">
                    </div>
                </div>
            </div>
        </section>
        <!--==================== POPULAR ====================-->
        <section class="popular section" id="popular"> <!-- Video 輪播區 -->
            <div class="container">
                <h2 class="section__title">
                    Video
                </h2>
                <div class="popular__container swiper"> <!-- Swiper 輪播容器 -->
                    <div class="swiper-wrapper"> <!-- 輪播內容包裝 -->
                        <!-- 以下為單張輪播卡片，可複製多份 -->
                        <article class="popular__card swiper-slide">
                            <img src="assets/img/popular1.jpg" alt="" class="popular__img">
                            <div class="popular__data">
                                <h2 class="popular__price">
                                    <span>$</span>66,356
                                </h2>
                                <h3 class="popular__title">
                                    Garden City Assat
                                </h3>
                                <p class="popular__description">
                                    Street The Garden City Of Miraflores,
                                    Lima - Peru Av. Sol #9876
                                </p>
                            </div>
                        </article>
                        <article class="popular__card swiper-slide">
                            <img src="assets/img/popular2.jpg" alt="" class="popular__img">
                            <div class="popular__data">
                                <h2 class="popular__price">
                                    <span>$</span>35,159
                                </h2>
                                <h3 class="popular__title">
                                    Garden Luxurious House
                                </h3>
                                <p class="popular__description">
                                    Street The Garden City Of Miraflores,
                                    Lima - Peru Av. Sol #9876
                                </p>
                            </div>
                        </article>
                        <article class="popular__card swiper-slide">
                            <img src="assets/img/popular3.jpg" alt="" class="popular__img">
                            <div class="popular__data">
                                <h2 class="popular__price">
                                    <span>$</span>75,043
                                </h2>
                                <h3 class="popular__title">
                                    Garden Orchard City
                                </h3>
                                <p class="popular__description">
                                    Street The Garden City Of Miraflores,
                                    Lima - Peru Av. Sol #9876
                                </p>
                            </div>
                        </article>
                        <article class="popular__card swiper-slide">
                            <img src="assets/img/popular4.jpg" alt="" class="popular__img">
                            <div class="popular__data">
                                <h2 class="popular__price">
                                    <span>$</span>62,024
                                </h2>
                                <h3 class="popular__title">
                                    Luxurious City Garden
                                </h3>
                                <p class="popular__description">
                                    Street The Garden City Of Miraflores,
                                    Lima - Peru Av. Sol #9876
                                </p>
                            </div>
                        </article>
                        <article class="popular__card swiper-slide">
                            <img src="assets/img/popular5.jpg" alt="" class="popular__img">
                            <div class="popular__data">
                                <h2 class="popular__price">
                                    <span>$</span>47,043
                                </h2>
                                <h3 class="popular__title">
                                    Aliva Private Garden
                                </h3>
                                <p class="popular__description">
                                    Street The Garden City Of Miraflores,
                                    Lima - Peru Av. Sol #9876
                                </p>
                            </div>
                        </article>
                    </div>
                    <div class="swiper-button-next">
                        <i class='bx bx-chevron-right'></i> <!-- 右切換箭頭 -->
                    </div>
                    <div class="swiper-button-prev">
                        <i class='bx bx-chevron-left'></i> <!-- 左切換箭頭 -->
                    </div>
                </div>
            </div>
        </section>
        
        <!--==================== SUBSCRIBE ====================-->
        <section class="subscribe section" id="subscribe"> <!-- 全民檢舉區塊 -->
          <div class="subscribe__container container">
            <h1 class="subscribe__title">
              全民檢舉
            </h1>
            <div style="list-style:none;">
              <?php if(!isset($_SESSION['user_name'])): ?>
                <a href="login.php" id="google-login-btn" class="google-btn" style="text-decoration: none; color: #444; background: #fff; padding: 10px 20px; border-radius: 5px; display: inline-flex; align-items: center; gap: 10px; font-weight: bold;">
                  <span class="google-logo-svg" style="width:22px;height:22px;">
                    <svg width="22" height="22" viewBox="0 0 48 48"><g><path fill="#4285F4" d="M44.5 20H24v8.5h11.7C34.6 33.4 29.8 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c2.4 0 4.7.7 6.6 2.1l6.4-6.4C33.6 5.2 29 3.5 24 3.5 12.8 3.5 3.5 12.8 3.5 24S12.8 44.5 24 44.5 44.5 35.2 44.5 24c0-1.3-.1-2.1-.3-3z"/><path fill="#34A853" d="M6.9 14.1l7 5.1C15.9 16.2 19.6 13.5 24 13.5c2.4 0 4.7.7 6.6 2.1l6.4-6.4C33.6 5.2 29 3.5 24 3.5c-7.7 0-14.3 4.5-17.1 10.6z"/><path fill="#FBBC05" d="M24 44.5c5.8 0 11.4-2.1 15.6-6l-7.2-5.9c-2.2 1.5-5.1 2.4-8.4 2.4-5.8 0-10.7-3.9-12.4-9.1l-7 5.4C7.9 40.4 15.4 44.5 24 44.5z"/><path fill="#EA4335" d="M44.5 24c0-1.2-.1-2.1-.3-3H24v8.5h11.7c-.5 2.6-2.1 4.7-4.1 6.2l7.2 5.9C41.5 40.6 44.5 33.8 44.5 24z"/></g></svg>
                  </span>
                  <span>使用 Google 帳戶登入</span>
                </a>
              <?php else: ?>
                <div style="color:#fff; font-weight:bold; margin-bottom: 20px; font-size: 1.2rem;">
                    歡迎您，<?php echo htmlspecialchars($_SESSION['user_name']); ?>！您現在可以檢舉可疑網站。
                </div>
                <button type="button" class="button" onclick="location.href='report.php'" style="padding: 10px 20px; border-radius: 5px; border: none; color: #ffffff; cursor: pointer; font-size: 1rem;">
                    點我檢舉
                </button>
              <?php endif; ?>
            </div>
          </div>
        </section>

    <!--==================== FOOTER ====================-->
    <footer class="footer section"  id="footer">
    <div class="footer__container container grid">
        <div>
            <a href="#" class="footer__logo">
                關於我們 <i class="bx bxs-home-alt-2"></i>
            </a>
            <p class="footer__description">
                WS&BP 網站安全專家 <br>
                專為用戶打造最安心的網路環境 <br>
                WS&BP, your trusted security partner.
            </p>
        </div>
        <div>
            <h3 class="footer__title">使用說明</h3>
            <ul class="footer__links">
                <li>
                    <a href="Plug-in.html">
                       <i class='bx bx-wrench'></i> 插件使用說明
                   </a>
                </li>
                <li>
                    <a href="#home">
                       <i class='bx bx-download'></i> 前往下載
                   </a>
                </li>
             </ul>
        </div>
        <div>
            <h3 class="footer__title">聯絡客服</h3>
            <ul class="footer__links">
                <li>
                   <a href="customer.html">
                       <i class='bx bx-file'></i> 客服指南
                   </a>
                </li>
                <li>
                    <i class='bx bx-envelope'></i>
                   客服信箱：<a href="mailto:wsbp.info@gmail.com">wsbp.info@gmail.com</a>
                </li>
                <li>
                    <i class='bx bx-time'></i>
                    服務時間：週一至週五 09:00~17:00
                </li>
            </ul>
        </div>
        <div>
            <h3 class="footer__title">
                Follow us
            </h3>
            <ul class="footer__social">
                <a href="https://www.facebook.com/" class="footer_social-link">
                    <i class="bx bxl-facebook-circle"></i>
                </a>
                <a href="https://www.instagram.com/" class="footer_social-link">
                    <i class="bx bxl-instagram-alt"></i>
                </a>
            </ul>
        </div>
    </div>
    <div class="footer__info container">
        <span class="footer__copy">
            &#169; Bedimcode. All rigths reserved
        </span>
        <div class="footer__privacy">
            <a href="#">Terms & Agreements</a>
            <a href="#">Privacy Policy</a>
        </div>
    </div>
</footer>

  
    <!--========== SCROLL UP ==========-->
    <a href="#" class="scrollup" id="scroll-up">
        <i class='bx bx-chevrons-up'></i> <!-- 回到頂部的箭頭按鈕 -->
    </a>
    <!--=============== SCROLLREVEAL ===============-->
    <script src="assets/js/scrollreveal.min.js"></script> <!-- 載入 ScrollReveal.js，滾動出現動畫 -->
    <!--=============== SWIPER JS ===============-->
    <script src="assets/js/swiper-bundle.min.js"></script> <!-- 載入 Swiper.js 輪播功能 -->
    <!--=============== MAIN JS ===============-->
    <script src="assets/js/main.js"></script> <!-- 載入主要自訂 JS 程式 -->
</body>
</html>
