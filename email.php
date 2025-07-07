<?php
include 'db.php';
require 'vendor/autoload.php'; // If using Composer
// Or if manual: require 'path/to/PHPMailer/src/Exception.php';
// require 'path/to/PHPMailer/src/PHPMailer.php';
// require 'path/to/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if connection is established
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delete_sql = "DELETE FROM mail_to_clients WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    if ($delete_stmt) {
        $delete_stmt->bind_param("i", $delete_id);
        $delete_stmt->execute();
        $delete_stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}


// Email settings for your domain
$admin_emails = ["uky171991@gmail.com", "umakant171991@gmail.com"]; // Multiple recipients in array
$from_email = "info@umakant.online"; // Your domain email
$from_name = "Website Monitor";
$smtp_host = "mail.umakant.online";  // Replace with your SMTP host
$smtp_username = "info@umakant.online"; // Your email address
$smtp_password = "Uma@171991"; // Your email password
$smtp_port = 465; // Use 587 for TLS or 465 for SSL
$smtp_secure = "ssl"; // Use 'ssl' for port 465, 'tls' for 587

// Handle email sending
$emailSent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_email_id'])) {
    $client_id = $_POST['send_email_id'];
    
    // Get client details
    $client_sql = "SELECT * FROM mail_to_clients WHERE id = ?";
    $client_stmt = $conn->prepare($client_sql);
    $client_stmt->bind_param("i", $client_id);
    $client_stmt->execute();
    $client_result = $client_stmt->get_result();
    $client = $client_result->fetch_assoc();
    
    
    // Email template with mobile and WhatsApp numbers
        $message = '
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
                        © ' . date("Y") . ' <a href="https://codeapka.com/contact-us/">Codeapka</a> <br>
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
    
    // Send email using PHPMailer
    
    $currentDate = date('Y-m-d');
    
    // Adding 4 days
    $newDate = date('d M Y', strtotime($currentDate . ' +4 days'));

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        // $mail->SMTPDebug = 2; // Enable debugging (set to 0 in production)
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Port = $smtp_port;

        // Recipients
        $mail->setFrom($from_email, 'Codeapka');
        $mail->addAddress($client['email'], $client['client_name']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Exclusive Website Deal Limited time offer till '.$newDate;
        $mail->Body    = $message;
        $mail->AltBody = "\n\nBest regards,\n Team";

        $mail->send();
        $emailSent = true;
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    $client_stmt->close();
}

// Query to fetch clients
$sql = "SELECT * FROM mail_to_clients ORDER BY created_at DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="assets/img/favicon.png">
  <title>Material Dashboard 3 by Creative Tim</title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link id="pagestyle" href="assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <link href="assets/css/style.css" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-100">
    <?php include('navbar.php'); ?>
  
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php include('top.php'); ?>
    <div class="container-fluid py-2">
      <div class="row">
        <div class="col-6">
          <h4 class="text-dark">Clients Records</h4>
        </div>
        <div class="col-6 text-end">
          <a href="add-client.php" class="btn btn-dark">
            <i class="fa fa-plus"></i> Add Client
          </a>
        </div>
      </div>
      <div class="container mt-5">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Website Status Checker</h5>
          </div>
          <div class="card-body">
            <?php if ($emailSent): ?>
              <div class="alert alert-success">✅ Email Sent Successfully!</div>
            <?php endif; ?>
            <?php
            if ($result->num_rows > 0) {
                echo '<table class="table table-striped">';
                echo '<tr>';
                echo '<th>ID</th>';
                echo '<th>Client Name</th>';
                echo '<th>Email</th>';
                //echo '<th>Status</th>';
                echo '<th>Updated At</th>';
                echo '<th>Actions</th>';
                echo '</tr>';
                
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['client_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                    //echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
                    echo '<td>';
                    // Delete form
                    echo '<form method="POST" action="" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this client?\');">';
                    echo '<input type="hidden" name="delete_id" value="' . htmlspecialchars($row['id']) . '">';
                    echo '<button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</button>';
                    echo '</form>';
                    // Email form
                    echo '<form method="POST" action="" style="display:inline;" class="ms-2">';
                    echo '<input type="hidden" name="send_email_id" value="' . htmlspecialchars($row['id']) . '">';
                    echo '<button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-envelope"></i> Send Email</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
                
                echo '</table>';
            } else {
                echo '<p>No clients found in the database.</p>';
            }
            
            $conn->close();
            ?>
          </div>
        </div>
      </div>
    
      <footer class="footer py-4">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted text-lg-start">
                © <script>document.write(new Date().getFullYear())</script>,
                made with <i class="fa fa-heart"></i> by
                <a href="https://www.creative-tim.com" class="font-weight-bold" target="_blank">Creative Tim</a>
                for a better web.
              </div>
            </div>
            <div class="col-lg-6">
              <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                <li class="nav-item"><a href="https://www.creative-tim.com" class="nav-link text-muted" target="_blank">Creative Tim</a></li>
                <li class="nav-item"><a href="https://www.creative-tim.com/presentation" class="nav-link text-muted" target="_blank">About Us</a></li>
                <li class="nav-item"><a href="https://www.creative-tim.com/blog" class="nav-link text-muted" target="_blank">Blog</a></li>
                <li class="nav-item"><a href="https://www.creative-tim.com/license" class="nav-link pe-0 text-muted" target="_blank">License</a></li>
              </ul>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </main>
  
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = { damping: '0.5' };
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>
</html>