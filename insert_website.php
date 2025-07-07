<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include 'db.php'; // Database connection assumed

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_name = trim($_POST['client_name']);
    $url = trim($_POST['url']);

    if (!empty($client_name) && !empty($url)) {
        $stmt = $conn->prepare("INSERT INTO website_list (client_name, url) VALUES (?, ?)");
        $stmt->bind_param("ss", $client_name, $url);

        if ($stmt->execute()) {
            $success_message ="<div class='alert alert-success'>Website added successfully!</div>";
        } else {
            $error_message = "<div class='alert alert-danger'>Error adding website: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        $error_message = "<div class='alert alert-warning'>All fields are required.</div>";
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
  <title>Material Dashboard 3 by Creative Tim</title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link id="pagestyle" href="assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <link href="assets/css/style.css" rel="stylesheet" />
  <style>
    .message.success { background-color: #dff0d8; color: #3c763d; padding: 10px; margin-bottom: 15px; }
    .message.error { background-color: #f2dede; color: #a94442; padding: 10px; margin-bottom: 15px; }
  </style>
</head>
<body class="g-sidenav-show bg-gray-100">
    <?php include('navbar.php'); ?>
  
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php include('top.php'); ?>
    <div class="container-fluid py-2">
      
      <div class="container mt-5">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Add New Website</h5>
          </div>
          <div class="card-body">
            <?php if ($success_message): ?>
              <?php echo $success_message; ?>
            <?php endif; ?>
            <?php if ($error_message): ?>
              <?php echo $error_message; ?>
            <?php endif; ?>
            
            <form method="POST" action="">
              <div class="mb-3">
                <label for="client_name" class="form-label">Client Name</label>
                <input type="text" class="form-control border" id="client_name" name="client_name" required>
              </div>
              <div class="mb-3">
                <label for="url" class="form-label">Website URL</label>
                <input type="url" class="form-control border" id="url" name="url" required>
              </div>
              <button type="submit" class="btn btn-primary">Submit</button>
            </form>
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

<?php $conn->close(); ?>