<?php
session_start();
$cookieId = isset($_COOKIE['user_id']) ? trim($_COOKIE['user_id']) : '';
$message = '';
if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8');
}
if (isset($_GET['clear_cookie'])) {
    setcookie('user_id', '', time() - 3600, '/');
    $message = '已刪除 cookie 中的使用者 ID。';
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>登入頁面</title>
</head>
<body>
    <h1>網站登入</h1>
    <?php if ($message): ?>
        <p style="color: green;"><?= $message ?></p>
    <?php endif; ?>
    <?php if ($cookieId): ?>
        <p>您的 ID：<strong><?= htmlspecialchars($cookieId, ENT_QUOTES, 'UTF-8') ?></strong></p>
        <p><a href="?clear_cookie=1">刪除已儲存的 cookie</a></p>
    <?php endif; ?>
    <form action="logincheck.php" method="post">
        <label>使用者 ID：<input type="text" name="user_id" required></label><br><br>
        <label>密碼：<input type="password" name="password" required></label><br><br>
        <button type="submit">登入</button>
    </form>
    <p>測試帳號：</p>
    <ul>
        <li>學生：student / s123</li>
        <li>教師：teacher / t123</li>
        <li>管理者：admin / a123</li>
    </ul>
</body>
</html>
