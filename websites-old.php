<?php
include 'inc/auth.php';
include 'inc/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is installed

// List of websites to check
$websites = [
    "https://vascularlimbsalvage.com/",
    "https://www.thetouchclinic.com/",
    "https://www.indianvascularsurgery.com/",
    "https://cosmeticgynaeindia.com/",
    "https://nirogyacareathome.com/",
    "https://cwcvascularcare.in/",
    "https://drjayakrishna.in/",
    "https://leventor.com/",
    "https://physiosynapse.com/",
    "https://venous.in/",
    "https://callidora.in/",
    "https://365hiresolutions.com/",
    "https://codeapka.com/"
];

// Email settings for your domain
$admin_emails = ["uky171991@gmail.com", "umakant171991@gmail.com"]; // Multiple recipients in array
$from_email = "info@umakant.online"; // Your domain email
$from_name = "Website Monitor";
$smtp_host = "mail.umakant.online";  // Replace with your SMTP host
$smtp_username = "info@umakant.online"; // Your email address
$smtp_password = "Uma@171991"; // Your email password
$smtp_port = 465; // Use 587 for TLS or 465 for SSL
$smtp_secure = "ssl"; // Use 'ssl' for port 465, 'tls' for 587

$downWebsites = []; // Store down websites

// Function to check if a website is up or down
function checkWebsiteStatus($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);

    // Disable SSL verification (use for testing, remove in production)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    global $downWebsites;
    
    if (!empty($curlError)) {
        $downWebsites[] = "$url - Error: $curlError";
        return "<span class='text-warning'><strong>Error: $curlError ❗</strong></span>";
    }

    if ($httpCode >= 200 && $httpCode < 400) {
        return "<span class='text-success'><strong>UP ✅ (HTTP $httpCode)</strong></span>";
    } else {
        $downWebsites[] = "$url - HTTP Code: $httpCode";
        return "<span class='text-danger'><strong>DOWN ❌ (HTTP $httpCode)</strong></span>";
    }
}

$emailSent = False;
// ✅ **Check all websites first before sending an email**
foreach ($websites as $site) {
    checkWebsiteStatus($site);
}

// ✅ **Print down websites for debugging**
// echo "<pre>";
// print_r($downWebsites);
// echo "</pre>";

// ✅ **Send email only if at least one website is down**
if (!empty($downWebsites)) {
    $subject = "Website Down Alert! ". date('Y-m-d h:i:s');
    $message = "<h5>The following websites are DOWN:</h5><ul>";
    foreach ($downWebsites as $downSite) {
        $message .= "<li>$downSite</li>";
    }
    $message .= "</ul>";

    // Send email via PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        //$mail->SMTPDebug = 2; // Enable debugging (set to 0 in production)
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Port = $smtp_port;

        // Sender
        $mail->setFrom($from_email, $from_name);

        // ✅ **Multiple Recipients (Fix for comma-separated emails)**
        foreach ($admin_emails as $recipient) {
            $mail->addAddress($recipient);
        }

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        if ($mail->send()) {
            $emailSent = true;
            //echo "<br><b>Email Sent Successfully! ✅</b>";
        }
    } catch (Exception $e) {
        echo "<br><b>Email could not be sent. Error: {$mail->ErrorInfo}</b>";
    }
} else {
    echo "<br><b>No websites are down. No email sent.</b>";
}
?>





<!--
=========================================================
* Material Dashboard 3 - v3.2.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2024 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="assets/img/favicon.png">
  <title>
    Material Dashboard 3 by Creative Tim
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <!-- Nucleo Icons -->
  <link href="assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <!-- CSS Files -->
  <link id="pagestyle" href="assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <link href="assets/css/style.css" rel="stylesheet" />
</head>

<body class="g-sidenav-show  bg-gray-100">
    <?php include('navbar.php'); ?>
  
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php include('top.php'); ?>
    <!-- End Navbar -->
    <div class="container-fluid py-2">
        
  

        <div class="container mt-5">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Website Status Checker</h5>
                </div>
                <div class="card-body">
                    <?php if ($emailSent): ?>
                        <div class="alert alert-warning">⚠️ Email Alert Sent: Some websites are DOWN!</div>
                    <?php endif; ?>
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Website URL</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($websites as $index => $site): ?>
                                <tr>
                                    <td><?= $index + 1; ?></td>
                                    <td><a href="<?= $site; ?>" target="_blank"><?= $site; ?></a></td>
                                    <td><?= checkWebsiteStatus($site); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    
     
      <footer class="footer py-4  ">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted text-lg-start">
                © <script>
                  document.write(new Date().getFullYear())
                </script>,
                made with <i class="fa fa-heart"></i> by
                <a href="https://www.creative-tim.com" class="font-weight-bold" target="_blank">Creative Tim</a>
                for a better web.
              </div>
            </div>
            <div class="col-lg-6">
              <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                <li class="nav-item">
                  <a href="https://www.creative-tim.com" class="nav-link text-muted" target="_blank">Creative Tim</a>
                </li>
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/presentation" class="nav-link text-muted" target="_blank">About Us</a>
                </li>
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/blog" class="nav-link text-muted" target="_blank">Blog</a>
                </li>
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/license" class="nav-link pe-0 text-muted" target="_blank">License</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </main>
  
  <!--   Core JS Files   -->
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>

</html>
