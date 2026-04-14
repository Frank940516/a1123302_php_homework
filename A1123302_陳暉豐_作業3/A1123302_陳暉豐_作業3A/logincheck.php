<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?msg=' . urlencode('請使用登入表單進入。'));
    exit;
}
$userId = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$users = [
    'student' => ['password' => 's123', 'role' => 'student'],
    'teacher' => ['password' => 't123', 'role' => 'teacher'],
    'admin' => ['password' => 'a123', 'role' => 'admin'],
];
if (isset($users[$userId]) && $users[$userId]['password'] === $password) {
    session_regenerate_id(true);
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $userId;
    $_SESSION['role'] = $users[$userId]['role'];
    setcookie('user_id', $userId, time() + 7 * 24 * 60 * 60, '/');
    switch ($_SESSION['role']) {
        case 'student':
            header('Location: student.php');
            break;
        case 'teacher':
            header('Location: teacher.php');
            break;
        case 'admin':
            header('Location: admin.php');
            break;
    }
    exit;
}
echo"<h1>登入失敗，請重試</h1>";
header("Refresh:2;url=index.php");
exit;
