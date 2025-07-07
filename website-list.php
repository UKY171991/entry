<?php
include 'db.php';  // Your database configuration
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // PHPMailer autoload

// Email settings
$admin_emails = ["uky171991@gmail.com", "umakant171991@gmail.com"];
$from_email = "info@umakant.online";
$from_name = "Website Monitor";
$smtp_host = "mail.umakant.online";
$smtp_username = "info@umakant.online";
$smtp_password = "Uma@171991";
$smtp_port = 465;
$smtp_secure = "ssl";

// Fetch websites from database
$stmt = $conn->prepare("SELECT id, client_name, url FROM website_list");
$stmt->execute();
$result = $stmt->get_result();
$websites = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$downWebsites = [];

// Function to check website status
function checkWebsiteStatus($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_NOBODY => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 100,
        CURLOPT_TIMEOUT => 100,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);

    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if (!empty($error)) {
        return "Error: $error";
    }
    return ($httpCode >= 200 && $httpCode < 400) ? 'UP' : "DOWN (HTTP $httpCode)";
}

// Check websites and update timestamps
foreach ($websites as $site) {
    $status = checkWebsiteStatus($site['url']);
    $now = date('Y-m-d H:i:s');

    // Update timestamp for checked site
    $updateStmt = $conn->prepare("UPDATE website_list SET date_updated = ? WHERE id = ?");
    $updateStmt->bind_param("si", $now, $site['id']);
    $updateStmt->execute();
    $updateStmt->close();

    if (strpos($status, 'DOWN') !== false || strpos($status, 'Error') !== false) {
        $downWebsites[] = "{$site['client_name']} ({$site['url']}) - Status: $status";
    }
}

// Send email alert if any website is down
if (!empty($downWebsites)) {
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
        foreach ($admin_emails as $email) {
            $mail->addAddress($email);
        }

        $mail->isHTML(true);
        $mail->Subject = 'Website Down Alert - ' . date('Y-m-d H:i');
        $mail->Body = '<h4>The following websites are down or have issues:</h4><ul><li>' 
                    . implode('</li><li>', $downWebsites) 
                    . '</li></ul>';

        $mail->send();
        echo "Alert email sent successfully.\n";
    } catch (Exception $e) {
        echo "Email could not be sent: {$mail->ErrorInfo}\n";
    }
} else {
    echo "All websites are up. No email sent.\n";
}

$conn->close();
?>
