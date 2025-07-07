<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php'; // Ensure this sets up $conn properly

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// SMTP Settings
$from_email = "info@umakant.online";
$from_name = "Codeapka";
$smtp_host = "mail.umakant.online";
$smtp_username = "info@umakant.online";
$smtp_password = "Uma@171991";
$smtp_port = 465;
$smtp_secure = "ssl";

// Log file path
$log_file = __DIR__ . '/email_log.txt';

// Log function
function logMessage($message, $log_file) {
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

// Update client email sent timestamp
function updateLastEmailSent($conn, $client_email) {
    $sql = "UPDATE mail_to_clients SET created_at = NOW() WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $client_email);
        $stmt->execute();
        $stmt->close();
    }
}

// Your provided email template function
function getCustomEmailTemplate() {
    $year = date("Y");
    return '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exclusive Website Deal</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Helvetica Neue, Arial, sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 650px;
            margin: 20px auto;
            background: linear-gradient(135deg, #ffffff, #f9f9f9);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .header {
            background: linear-gradient(90deg, #ff6f61, #ff9f1c);
            padding: 40px 20px;
            text-align: center;
            color: #ffffff;
            position: relative;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            font-size: 18px;
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 40px;
            text-align: center;
        }
        .content h2 {
            font-size: 26px;
            color: #ff6f61;
            margin-bottom: 20px;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }
        .highlight {
            background-color: #fff3e6;
            padding: 10px;
            border-radius: 8px;
            display: inline-block;
            margin: 10px 0;
            font-weight: bold;
            color: #ff6f61;
        }
        .features {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            text-align: center;
        }
        .feature-item {
            width: 30%;
            padding: 15px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        .feature-item:hover {
            transform: translateY(-5px);
        }
        .feature-item h3 {
            font-size: 18px;
            color: #333;
            margin: 10px 0;
        }
        .feature-item p {
            font-size: 14px;
            color: #777;
        }
        .cta-button {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(90deg, #ff6f61, #ff9f1c);
            color: #ffffff;
            text-decoration: none;
            border-radius: 50px;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            box-shadow: 0 5px 15px rgba(255, 111, 97, 0.4);
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            background: linear-gradient(90deg, #ff9f1c, #ff6f61);
            box-shadow: 0 8px 20px rgba(255, 111, 97, 0.6);
        }
        .footer {
            background-color: #333;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #ccc;
        }
        .footer a {
            color: #ff9f1c;
            text-decoration: none;
        }
        .contact-info {
            margin-top: 10px;
        }
        @media only screen and (max-width: 600px) {
            .features {
                flex-direction: column;
            }
            .feature-item {
                width: 100%;
                margin-bottom: 20px;
            }
            .header h1 {
                font-size: 24px;
            }
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Get Online Now!</h1>
            <p>A Stunning Website at an Unbelievable Price</p>
        </div>
        <div class="content">
            <h2>Launch Your Dream Website Today</h2>
            <p>
                Stand out with a custom-built website designed to captivate your audience. For a limited time, we’re offering our expert services at the <span class="highlight">lowest price ever</span>—because your success shouldn’t break the bank!
            </p>
            <div class="features">
                <div class="feature-item">
                    <h3>Eye-Catching Design</h3>
                    <p>Bold, modern layouts tailored to your brand.</p>
                </div>
                <div class="feature-item">
                    <h3>Lightning Fast</h3>
                    <p>Delivered in just 3-5 days.</p>
                </div>
                <div class="feature-item">
                    <h3>Steal of a Deal</h3>
                    <p>Starting at only $49!</p>
                </div>
            </div>
            <a href="mailto:uky171991@gmail.com?subject=Website%20Offer%20Inquiry" class="cta-button">Grab This Deal Now</a>
        </div>
        <div class="footer">
            <p>
                © ' . $year . ' <a href="https://codeapka.com/contact-us/">Codeapka</a> <br>
                Need help? Reach us at <a href="mailto:uky171991@gmail.com">uky171991@gmail.com</a>
            </p>
            <div class="contact-info">
                Mobile: <a href="tel:+919453619260">+91-9453619260</a> <br>
                WhatsApp: <a href="https://wa.me/919453619260">+91-9453619260</a>
            </div>
        </div>
    </div>
</body>
</html>';
}

// Send email to all valid clients
function sendEmailToAllClients($conn, $smtp_host, $smtp_username, $smtp_password, $smtp_port, $smtp_secure, $from_email, $from_name, $log_file) {
    $sql = "SELECT client_name, email FROM mail_to_clients";
    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        logMessage("No clients found in the database.", $log_file);
        return;
    }

    $offerDeadline = date('d M Y', strtotime('+4 days'));

    while ($row = $result->fetch_assoc()) {
        $client_name = $row['client_name'];
        $client_email = $row['email'];

        if (!filter_var($client_email, FILTER_VALIDATE_EMAIL) || str_contains($client_email, 'example.com')) {
            logMessage("Skipped invalid email: $client_email", $log_file);
            continue;
        }
        
        // if ($client_email !== "uky171991@gmail.com") {
        //     logMessage("Skipped test (not uky171991@gmail.com): $client_email", $log_file);
        //     continue;
        // }

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
            $mail->addAddress($client_email, $client_name);

            $mail->isHTML(true);
            $mail->Subject = "Website Deal - Valid till $offerDeadline!";
            $mail->Body = getCustomEmailTemplate();
            $mail->AltBody = "Hi $client_name,\nGet your stunning website for just $49! Offer valid till $offerDeadline. Contact us now.";

            $mail->send();
            updateLastEmailSent($conn, $client_email);
            logMessage("✅ Email sent to: $client_email", $log_file);
        } catch (Exception $e) {
            logMessage("❌ Failed to send email to $client_email. Error: {$mail->ErrorInfo}", $log_file);
        }
    }
}

// Start sending
sendEmailToAllClients($conn, $smtp_host, $smtp_username, $smtp_password, $smtp_port, $smtp_secure, $from_email, $from_name, $log_file);

$conn->close();
?>
