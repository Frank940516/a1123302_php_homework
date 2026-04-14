<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?msg=' . urlencode('請先以管理者帳號登入。'));
    exit;
}
if (isset($_GET['clear_cookie'])) {
    setcookie('user_id', '', time() - 3600, '/');
    $cookieMessage = 'cookie 已刪除。';
}
$cookieId = isset($_COOKIE['user_id']) ? htmlspecialchars($_COOKIE['user_id'], ENT_QUOTES, 'UTF-8') : '';
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>管理者專區</title>
</head>
<body>
    <h1>管理者專區</h1>
    <p>歡迎，<strong><?= htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8') ?></strong>！</p>
    <p>您的角色：管理者</p>
    <?php if (!empty($cookieId)): ?>
        <p>cookie 中儲存的 ID：<strong><?= $cookieId ?></strong></p>
    <?php endif; ?>
    <?php if (!empty($cookieMessage)): ?>
        <p style="color: green;"><?= $cookieMessage ?></p>
    <?php endif; ?>
    <p><a href="admin.php?clear_cookie=1">刪除 cookie</a> | <a href="logout.php">登出</a></p>
</body>
</html>
