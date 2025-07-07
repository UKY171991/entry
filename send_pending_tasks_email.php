<?php
date_default_timezone_set('Asia/Kolkata');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// SMTP Settings
$from_email = "info@umakant.online";
$from_name = "My Pending Task - Codeapka";
$smtp_host = "mail.umakant.online";
$smtp_username = "info@umakant.online";
$smtp_password = "Uma@171991";
$smtp_port = 465;
$smtp_secure = "ssl";

$to = 'uky171991@gmail.com'; // Admin/recipient email
$subject = 'Pending Tasks Reminder '. date('Y-m-d h:i:s a');

// Query all pending tasks with user info
$sql = "SELECT t.*, u.username, u.email, c.client_name FROM tasks t LEFT JOIN users u ON t.user_id = u.id LEFT JOIN clients c ON t.client_id = c.id WHERE t.status = 'pending' ORDER BY t.due_date ASC, t.id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $body = "<h3>The following tasks are still pending: ". date('Y-m-d h:i:s a')."</h3><ul>";
    while ($row = $result->fetch_assoc()) {
        $body .= "<li><b>Task:</b> {$row['task_name']}<br>";
        $body .= "<b>Client:</b> {$row['client_name']}<br>";
        $body .= "<b>User:</b> {$row['username']} ({$row['email']})<br>";
        $body .= "<b>Due Date:</b> {$row['due_date']}<br>--------------------------</li>";
    }
    $body .= "</ul>";

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Port = $smtp_port;
        $mail->setFrom($from_email, $from_name);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />', '<li>', '</li>', '<ul>', '</ul>'], ["\n", "\n", "\n", "- ", "\n", "", ""], $body));
        $mail->send();
        echo "Pending tasks email sent successfully.";
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "No pending tasks found.";
} 