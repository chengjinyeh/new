chrome.runtime.onInstalled.addListener(() => {
    console.log("擴充功能已安裝！");
  });
  

  chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    if (message.type === 'displayNotification') {
      chrome.notifications.create({
        type: 'basic',
        iconUrl: 'icons/icon48.png',
        title: '安全警告',
        message: message.message,
        priority: 2
    }, (notificationId) => {
        if (chrome.runtime.lastError) {
          console.error('通知創建失敗：', chrome.runtime.lastError);
        }
      });
    }
  });
  