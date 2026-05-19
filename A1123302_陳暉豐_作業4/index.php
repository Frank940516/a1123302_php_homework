<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>垃圾郵件發送系統</title>
    <style>
        body { font-family: "Helvetica Neue", Arial, sans-serif; background-color: #f5f7fa; color: #333; margin: 0; padding: 20px; }
        .wrapper { max-width: 800px; margin: 0 auto; }
        .card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); margin-bottom: 20px; }
        h2 { margin-top: 0; color: #2c3e50; border-bottom: 2px solid #eaedf1; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; font-size: 14px; }
        input[type="text"], input[type="email"], input[type="number"], textarea, select {
            width: 100%; padding: 10px; border: 1px solid #dcdfe6; border-radius: 4px; box-sizing: border-box; font-size: 14px;
        }
        .inline-group { display: flex; gap: 15px; }
        .inline-group .form-group { flex: 1; }
        button { background-color: #409eff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; transition: 0.2s; }
        button:hover { background-color: #66b1ff; }
        button.btn-success { background-color: #67c23a; }
        button.btn-success:hover { background-color: #85ce61; }
        
        /* 進度條樣式 */
        .progress-container { margin: 20px 0; display: none; }
        .progress-bar-bg { background-color: #ebeef5; border-radius: 100px; height: 20px; overflow: hidden; position: relative; }
        .progress-bar-fill { background-color: #67c23a; height: 100%; width: 0%; transition: width 0.3s ease; }
        .progress-text { text-align: center; font-weight: bold; margin-top: 5px; font-size: 14px; }
        
        /* 即時日誌 */
        #log-console { background-color: #303133; color: #fff; font-family: monospace; padding: 15px; border-radius: 4px; height: 150px; overflow-y: auto; font-size: 12px; margin-top: 15px; }
        .log-item { margin-bottom: 5px; }
        .log-success { color: #67c23a; }
        .log-error { color: #f56c6c; }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="card">
        <form id="add-email-form">
            <div class="form-group">
                <label>新增 Email 位址</label>
                <div class="inline-group">
                    <input type="email" id="new-email" placeholder="請輸入電子郵件" required>
                    <button type="submit">加入資料庫</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <form id="mail-sender-form">
            <div class="inline-group">
                <div class="form-group">
                    <label>發送模式</label>
                    <select id="send-mode">
                        <option value="all">全部寄送</option>
                        <option value="random">隨機寄送幾筆</option>
                    </select>
                </div>
                <div class="form-group" id="random-count-group" style="display: none;">
                    <label>隨機發送筆數</label>
                    <input type="number" id="random-limit" min="1" value="5">
                </div>
                <div class="form-group">
                    <label>發送間隔 (秒)</label>
                    <input type="number" id="send-interval" min="0" value="2">
                </div>
            </div>

            <div class="form-group">
                <label>郵件主旨</label>
                <input type="text" id="mail-subject" value="" required>
            </div>

            <div class="form-group">
                <label>郵件內容 (支援 HTML)</label>
                <textarea id="mail-content" rows="6" required></textarea>
            </div>

            <button type="button" class="btn-success" id="start-send-btn">開始批量寄信</button>
            <button type="button" id="pause-btn" style="background-color: #e6a23c">暫停寄送</button>
        </form>

        <div class="progress-container" id="progress-area">
            <label>發送進度</label>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" id="progress-fill"></div>
            </div>
            <div class="progress-text" id="progress-label">0% (0 / 0)</div>
            
            <div id="log-console"></div>
        </div>
    </div>
</div>

<script>
    // 控制隨機筆數輸入框的顯示與隱藏
    document.getElementById('send-mode').addEventListener('change', function() {
        const randomGroup = document.getElementById('random-count-group');
        randomGroup.style.display = this.value === 'random' ? 'block' : 'none';
    });

    // AJAX A: 新增 Email 進入資料庫
    document.getElementById('add-email-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const emailInput = document.getElementById('new-email');
        const formData = new FormData();
        formData.append('email', emailInput.value);

        fetch('api.php?action=add_email', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if(data.success) emailInput.value = '';
        });
    });

    // AJAX B: 核心排程發送邏輯
    document.getElementById('start-send-btn').addEventListener('click', async function() {
        const mode = document.getElementById('send-mode').value;
        const limit = document.getElementById('random-limit').value;
        const interval = parseInt(document.getElementById('send-interval').value) * 1000; // 轉毫秒
        const subject = document.getElementById('mail-subject').value;
        const content = document.getElementById('mail-content').value;

        if(!subject || !content) {
            alert('請填寫郵件主旨與內容！');
            return;
        }

        // 1. 取得目標名單
        const targetData = new FormData();
        targetData.append('mode', mode);
        targetData.append('limit', limit);

        const targetRes = await fetch('api.php?action=get_targets', { method: 'POST', body: targetData });
        const targetJson = await targetRes.json();
        
        if(!targetJson.success || targetJson.emails.length === 0) {
            alert('名單撈取失敗或資料庫內無任何 Email 名單！');
            return;
        }

        const emailList = targetJson.emails;
        const total = emailList.length;

        // 初始化進度條與日誌區
        document.getElementById('progress-area').style.display = 'block';
        const logConsole = document.getElementById('log-console');
        logConsole.innerHTML = '<div class="log-item">初始化發送隊列，總計 ' + total + ' 筆...</div>';
        updateProgress(0, total);

        // 禁用按鈕防止重複點擊
        const sendBtn = document.getElementById('start-send-btn');
        sendBtn.disabled = true;
        sendBtn.innerText = '發送中...';

        // 2. 依序迴圈發送（核心間隔與進度控制）
        for (let i = 0; i < total; i++) {
            const currentEmail = emailList[i];
            
            const mailData = new FormData();
            mailData.append('email', currentEmail);
            mailData.append('subject', subject);
            mailData.append('content', content);

            // 呼叫單筆寄信 API
            try {
                const sendRes = await fetch('api.php?action=send_single_mail', { method: 'POST', body: mailData });
                const sendJson = await sendRes.json();
                
                const logItem = document.createElement('div');
                logItem.className = 'log-item ' + (sendJson.success ? 'log-success' : 'log-error');
                logItem.innerText = `[${i+1}/${total}] ` + sendJson.message;
                logConsole.appendChild(logItem);
            } catch(err) {
                const logItem = document.createElement('div');
                logItem.className = 'log-item log-error';
                logItem.innerText = `[${i+1}/${total}] 連線 API 發生嚴重錯誤。`;
                logConsole.appendChild(logItem);
            }

            // 捲動日誌到最下方
            logConsole.scrollTop = logConsole.scrollHeight;

            // 更新進度條
            updateProgress(i + 1, total);

            // 如果不是最後一封，且設定了間隔時間，則等待
            if (i < total - 1 && interval > 0) {
                await new Promise(resolve => setTimeout(resolve, interval));
            }
        }

        // 發送完畢恢復按鈕
        alert('批量寄信排程執行完畢！');
        sendBtn.disabled = false;
        sendBtn.innerText = '開始批量寄信';
    });

    // 計算與更新進度條函數
    function updateProgress(current, total) {
        const percentage = total > 0 ? Math.round((current / total) * 100) : 0;
        document.getElementById('progress-fill').style.width = percentage + '%';
        document.getElementById('progress-label').innerText = `${percentage}% (${current} / ${total})`;
    }
</script>

</body>
</html>