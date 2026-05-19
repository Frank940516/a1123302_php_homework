<?php
$host = 'localhost';
$dbname = 'mail_system';
$username = 'root'; // XAMPP 預設
$password = '123';     // XAMPP 預設

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("資料庫連線失敗: " . $e->getMessage());
}
?>