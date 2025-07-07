<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include 'db.php'; // Assuming this sets up $conn

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust path if needed

// Initialize variables
$client_name = $email = $status = '';
$success_message = $error_message = '';

// Email settings for your domain
$admin_emails = ["uky171991@gmail.com", "umakant171991@gmail.com"]; // Multiple recipients in array
$from_email = "info@umakant.online"; // Your domain email
$from_name = "Website Monitor";
$smtp_host = "mail.umakant.online";  // Replace with your SMTP host
$smtp_username = "info@umakant.online"; // Your email address
$smtp_password = "Uma@171991"; // Your email password
$smtp_port = 465; // Use 587 for TLS or 465 for SSL
$smtp_secure = "ssl"; // Use 'ssl' for port 465, 'tls' for 587

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = trim($_POST['client_name']);
    $email = trim($_POST['email']);
    $status = trim($_POST['status']);
    
    // Basic validation
    if (empty($client_name) || empty($email)) {
        $error_message = "Client name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Check if email already exists
        $check_sql = "SELECT email FROM clients WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        
        if ($check_stmt) {
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_stmt->store_result();
            
            if ($check_stmt->num_rows > 0) {
                $error_message = "This email already exists in the database.";
            } else {
                // Proceed with insertion if email doesn't exist
                $sql = "INSERT INTO clients (client_name, email, status) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                
                if ($stmt) {
                    $stmt->bind_param("sss", $client_name, $email, $status);
                    
                    if ($stmt->execute()) {
                        $success_message = "Client added successfully!";
                        
                        // Email template
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
                                        Stand out with a custom-built website designed to captivate your audience. For a limited time, we\'re offering our expert services at the <span class="highlight">lowest price ever</span>—because your success shouldn\'t break the bank!
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
                        $mail = new PHPMailer(true);
                        
                        $currentDate = date('Y-m-d');
    
                        // Adding 4 days
                        $newDate = date('d M Y', strtotime($currentDate . ' +4 days'));
            
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
                            $mail->addAddress($email, $client_name); // Send to the new client's email

                            // Content
                            $mail->isHTML(true);
                            $mail->Subject = 'Exclusive Website Deal Limited time offer till '.$newDate;
                            $mail->Body = $message;
                            $mail->AltBody = "Welcome, $client_name!\nYour account has been created.\nStatus: " . ($status == '0' ? 'Active' : 'Inactive');

                            $mail->send();
                            $success_message .= " Email sent to $email.";
                        } catch (Exception $e) {
                            $error_message = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                        }

                        $client_name = $email = $status = ''; // Clear form
                    } else {
                        $error_message = "Error adding client: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $error_message = "Error preparing statement: " . $conn->error;
                }
            }
            $check_stmt->close();
        } else {
            $error_message = "Error preparing check statement: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="assets/img/favicon.png">
  <title>Client List | Admin Panel</title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link id="pagestyle" href="assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <link href="assets/css/style.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="g-sidenav-show  bg-gray-100">
<?php include('navbar.php'); ?>
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <?php include('top.php'); ?>
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header d-flex justify-content-between align-items-center p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-2 pb-2">
                            <h6 class="text-white text-capitalize ps-3">Client List</h6>
                        </div>
                        <button class="btn btn-primary m-3" id="addClientBtn"><i class="fa fa-plus"></i> Add Client</button>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="clientsTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Client Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="6" class="text-center">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="clientForm">
            <div class="modal-header">
              <h5 class="modal-title" id="clientModalLabel">Add Client</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="id" id="clientId" class="border">
              <div class="mb-3">
                <label for="clientName" class="form-label">Client Name</label>
                <input type="text" class="form-control border" id="clientName" name="client_name" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control border" id="email" name="email">
              </div>
              <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control border" id="phone" name="phone">
              </div>
              <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</main>
<script>
function loadClients() {
  $.post('clients-ajax.php', {action: 'list'}, function(res) {
    let html = '';
    if(res.success && res.clients.length) {
      res.clients.forEach(function(client, idx) {
        html += `<tr>
          <td>${idx+1}</td>
          <td>${client.client_name}</td>
          <td>${client.email || ''}</td>
          <td>${client.phone || ''}</td>
          <td>${client.address || ''}</td>
          <td>
            <button class="btn btn-info btn-sm editClientBtn" data-id="${client.id}" data-client='${JSON.stringify(client)}'><i class="fa fa-edit"></i></button>
            <button class="btn btn-danger btn-sm deleteClientBtn" data-id="${client.id}"><i class="fa fa-trash"></i></button>
          </td>
        </tr>`;
      });
    } else {
      html = '<tr><td colspan="6" class="text-center">No clients found.</td></tr>';
    }
    $('#clientsTable tbody').html(html);
  }, 'json');
}

$(document).ready(function() {
  loadClients();

  $('#addClientBtn').click(function() {
    $('#clientForm')[0].reset();
    $('#clientId').val('');
    $('#clientModalLabel').text('Add Client');
    $('#clientModal').modal('show');
  });

  $(document).on('click', '.editClientBtn', function() {
    let client = $(this).data('client');
    $('#clientId').val(client.id);
    $('#clientName').val(client.client_name);
    $('#email').val(client.email);
    $('#phone').val(client.phone);
    $('#address').val(client.address);
    $('#clientModalLabel').text('Edit Client');
    $('#clientModal').modal('show');
  });

  $('#clientForm').submit(function(e) {
    e.preventDefault();
    let formData = $(this).serializeArray();
    let action = $('#clientId').val() ? 'edit' : 'add';
    formData.push({name: 'action', value: action});
    $.post('clients-ajax.php', formData, function(res) {
      if(res.success) {
        $('#clientModal').modal('hide');
        loadClients();
      } else {
        alert('Error saving client');
      }
    }, 'json');
  });

  $(document).on('click', '.deleteClientBtn', function() {
    if(confirm('Are you sure you want to delete this client?')) {
      let id = $(this).data('id');
      $.post('clients-ajax.php', {action: 'delete', id: id}, function(res) {
        if(res.success) loadClients();
        else alert('Error deleting client');
      }, 'json');
    }
  });
});
</script>
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
<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>
</html>