<?php
// 若需要會員判斷，可加入 session_start() 與權限檢查
// session_start();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>全民檢舉</title>
    <link rel="stylesheet" href="assets/css/report.css"> 
</head>
<body>
    <div class="container">
        <header>
            <nav>
                <ul>
                    <li><a href="view_log.php">檢舉紀錄查詢</a></li>
                    <li><a href="http://localhost/topicsproject1/index.php">回首頁</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <h1>全民檢舉</h1>
            <form action="#" method="post">
                <div class="form-group">
                    <label for="suspicious-url">可疑網址</label>
                    <input type="text" id="suspicious-url" name="suspicious_url" placeholder="請輸入可疑網址" required>
                    <p class="info-text">*檢舉結果可至會員專區查詢</p>
                </div>
                
                <div class="form-group">
                     <label for="select-type" class="type-label">檢舉類型</label>
                     <div class="form-row2">
                         <select id="select-type" name="type">
                             <option value="" disabled selected>請選擇類型</option>
                             <option value="phishing">釣魚網站</option>
                             <option value="fraud">詐騙</option>
                             <option value="malware">惡意軟體</option>
                         </select>
                         <button type="submit">送出</button>
                     </div>
                  </div>
            </form>

            <div class="stats">
                <p>今日檢舉次數：3</p>
            </div>

            <div class="map">
                <p>警政署防詐宣導圖</p>
                <img src="assets/img/police.png" alt="警政署宣導圖" style="max-width:100%;">
                <p>圖片來源:新北市政府警察局首頁</p>
            </div>
        </main>
    </div>
</body>
</html>