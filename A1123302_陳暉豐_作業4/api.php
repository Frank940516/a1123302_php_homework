<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'db.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

// A. 新增 Email 到資料庫
if ($action === 'add_email') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        echo json_encode(['success' => false, 'message' => '無效的 Email 格式']);
        exit;
    }
    try {
        $stmt = $pdo->prepare("INSERT INTO recipients (email) VALUES (:email)");
        $stmt->execute(['email' => $email]);
        echo json_encode(['success' => true, 'message' => 'Email 新增成功']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // 重複的 Email
            echo json_encode(['success' => false, 'message' => '此 Email 已存在於名單中']);
        } else {
            echo json_encode(['success' => false, 'message' => '資料庫錯誤: ' . $e->getMessage()]);
        }
    }
    exit;
}

// B-1. 撈取發送目標名單 (全部或隨機)
if ($action === 'get_targets') {
    $mode = $_POST['mode'] ?? 'all';
    $limit = intval($_POST['limit'] ?? 0);

    if ($mode === 'random' && $limit > 0) {
        // 隨機撈取指定筆數
        $stmt = $pdo->prepare("SELECT email FROM recipients ORDER BY RAND() LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    } else {
        // 撈取全部
        $stmt = $pdo->prepare("SELECT email FROM recipients ORDER BY id DESC");
    }
    
    $stmt->execute();
    $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode(['success' => true, 'emails' => $emails]);
    exit;
}

// B-2. 執行單筆寄信
if ($action === 'send_single_mail') {
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $content = $_POST['content'] ?? '';

    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => '收件人不能為空']);
        exit;
    }

    $mail = new PHPMailer(true);
    try {
        // 伺服器設定 (使用您提供的設定)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'frankch.940516@gmail.com';
        $mail->Password   = 'itff vkbc kovg dthf'; // 您的 Google 應用程式密碼
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // 收發件人
        $mail->setFrom('frankch.940516@gmail.com', '排程系統');
        $mail->addAddress($email);

        // 內容
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br($content);

        $mail->send();
        echo json_encode(['success' => true, 'message' => "已成功寄給 $email"]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => "寄送至 $email 失敗。錯誤: {$mail->ErrorInfo}"]);
    }
    exit;
}