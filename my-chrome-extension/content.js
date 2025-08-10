
// 檢查當前網頁是否使用 HTTPS
if (window.location.protocol !== 'https:') {
    // 顯示警告：該網頁不安全，建議使用 HTTPS
    alert('警告：您正在瀏覽的網站並非使用 HTTPS 協議，可能存在安全風險！');
  }


// 檢查網頁中是否存在 "隱私政策" 或 "Privacy Policy" 這樣的詞語
let privacyPolicyKeywords = ['privacy policy', '隱私政策'];
let pageText = document.body.innerText.toLowerCase();

let foundPrivacyPolicy = privacyPolicyKeywords.some(keyword => pageText.includes(keyword));


if (!foundPrivacyPolicy) {
    alert('警告：該網站未提供隱私政策，請謹慎輸入個人資訊！');
    // 提供隱私政策範本鏈接
    let privacyPolicyLink = 'https://www.example.com/privacy-policy';
    console.log('請查看隱私政策: ' + privacyPolicyLink);
  }


// 檢查頁面是否加載了 HTTP 資源（混合內容）
let resources = document.querySelectorAll('img[src^="http://"], script[src^="http://"], link[href^="http://"]');

if (resources.length > 0) {
  // 顯示警告：該頁面存在混合內容，這可能導致安全風險。
  alert('警告：該頁面包含不安全的 HTTP 資源，可能存在安全風險！');
}

let trackers = ['google-analytics', 'facebook.com', 'twitter.com'];
let scripts = document.querySelectorAll('script[src], img[src]');

scripts.forEach(script => {
  if (trackers.some(tracker => script.src.includes(tracker))) {
    script.remove();
    alert('警告：此網站包含可疑的追蹤器！');
  }

  let sensitiveKeywords = ['api_key', 'secret', 'password'];
let pageText = document.body.innerText.toLowerCase();

sensitiveKeywords.forEach(keyword => {
  if (pageText.includes(keyword)) {
    alert('警告：該頁面可能包含敏感信息，如 API 密鑰或密碼！');
  }

  let maliciousDomains = ['example.com', 'malicious.com'];
let currentDomain = window.location.hostname;

if (maliciousDomains.includes(currentDomain)) {
  alert('警告：此網站是已知的惡意網站，請勿提供敏感信息！');
}

// Example: Use a third-party API to get website security score
let websiteUrl = window.location.href;

fetch(`https://api.website-security.com/score?url=${websiteUrl}`)
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok ' + response.statusText);
    }
    return response.json();
  })
  .then(data => {
    const securityScore = data.score;
    // 顯示評分
    alert(`該網站的安全評分為：${securityScore}`);

    // 根據安全評分顯示不同的訊息
    if (securityScore < 50) {
      alert('警告：該網站的安全評分較低，請謹慎操作！');
    }

    // 顯示在頁面上
    const scoreElement = document.createElement('div');
    scoreElement.innerHTML = `網站安全評分: ${securityScore}`;
    scoreElement.style.position = 'fixed';
    scoreElement.style.bottom = '10px';
    scoreElement.style.left = '10px';
    scoreElement.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
    scoreElement.style.color = 'white';
    scoreElement.style.padding = '10px';
    scoreElement.style.borderRadius = '5px';
    document.body.appendChild(scoreElement);
  })
  .catch(error => {
    console.error('Fetch error: ', error);
    alert('警告：無法檢查網站的安全評分，請稍後再試。');
  });

    });

});

// 創建通知
chrome.runtime.sendMessage({ type: 'displayNotification', message: '警告：該網站存在安全風險！' });
