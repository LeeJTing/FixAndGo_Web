<?php
// contact_handler.php - Place this file in the same directory as your contact page

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// 引入 PHPMailer
require __DIR__ . '/../../vendor/PHPMailer/src/Exception.php';
require __DIR__ . '/../../vendor/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../../vendor/PHPMailer/src/SMTP.php';

// Response array
$response = [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'errors' => ['Invalid request method']
    ]);
    exit;
}

// =======================
// 获取 & 验证表单数据
// =======================
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];

if (strlen($name) < 2) {
    $errors[] = 'Please enter a valid name (at least 2 characters)';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address';
}
if (strlen($subject) < 5) {
    $errors[] = 'Please enter a valid subject (at least 5 characters)';
}
if (strlen($message) < 20) {
    $errors[] = 'Please enter a valid message (at least 20 characters)';
}

if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'errors' => $errors
    ]);
    exit;
}

// =======================
// 组织邮件内容
// =======================
$to = 'leekeezhan@gmail.com';

$email_subject = 'New Contact Form Submission: ' . $subject;

$email_body  = "You have received a new message from the contact form.\n\n";
$email_body .= "Name: $name\n";
$email_body .= "Email: $email\n";
$email_body .= "Subject: $subject\n\n";
$email_body .= "Message:\n$message\n\n";
$email_body .= "--------------------------\n";
$email_body .= "Sent from: " . $_SERVER['HTTP_HOST'] . "\n";
$email_body .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
$email_body .= "Date: " . date('Y-m-d H:i:s') . "\n";

// =======================
// 使用 PHPMailer + Gmail SMTP 发送
// =======================
try {
    $mail = new PHPMailer(true);

    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'error_log';

    // SMTP 配置
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;

    // 🔴 改这里（你的 Gmail）
    $mail->Username   = 'leekeezhan@gmail.com';

    // 🔴 改这里（你的 App Password，无空格）
    $mail->Password   = 'ckcbjyujkkakzqjz';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // 发件人 & 收件人
    $mail->setFrom($mail->Username, 'FixAndGo Contact');
    $mail->addAddress($to);
    $mail->addReplyTo($email, $name);

    // 内容
    $mail->isHTML(false);
    $mail->Subject = $email_subject;
    $mail->Body    = $email_body;

    // 发送
    $mail->send();

    // 成功响应
    echo json_encode([
        'success' => true,
        'message' => "Thank you for contacting us! We'll get back to you within 24 hours."
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'errors' => [
            'Mailer Error: ' . $mail->ErrorInfo
        ]
    ]);
}

exit;