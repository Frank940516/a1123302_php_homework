<?php
session_start();
$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === 'student' && $password === '2026') {
        $_SESSION['logged_in'] = true;
        header('Location: page.php');
        exit;
    }

    $login_error = '登入失敗：帳號或密碼錯誤，請再試一次。';
}
?>
<html>
    <head>
        <meta charset="utf-8">
        <title>登入頁面</title>
    </head>
    <body bgcolor="#F0F8FF">
        <center>
            <h1>夏令營報名系統登入</h1>
            <?php if ($login_error): ?>
                <p style="color:red; font-size:18px;"><?php echo htmlspecialchars($login_error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <form action="login.php" method="post">
                <h2>帳號：<input type="text" name="username" required></h2>
                <h2>密碼：<input type="password" name="password" required></h2>
                <input type="submit" value="登入">
                <input type="reset" value="清除">
            </form>
            <p>測試帳號：student  /  密碼：2026</p>
        </center>
    </body>
</html>
