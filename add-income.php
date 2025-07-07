<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_name = $_POST['client_name'];
    $total_amount = $_POST['total_amount'];
    $pending_amount = $_POST['pending_amount'];
    $date_added = $_POST['date_added'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO payments (client_name, total_amount, pending_amount, date_added, user_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sddsi", $client_name, $total_amount, $pending_amount, $date_added, $user_id);

    if ($stmt->execute()) {
        header("Location: income.php");
        exit();
        $success_message = "Payment added successfully.";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
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
  <?php  include('navbar.php'); ?>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php include('top.php'); ?>
    <!-- End Navbar -->
    <div class="container-fluid py-2">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <form  method="POST">
				  <div class="card my-4">
				    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
				      <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
				        <h6 class="text-white text-capitalize ps-3">Add Payment</h6>
				      </div>
				    </div>
				    <div class="card-body px-4 pb-2">
				        <?php if (isset($success_message)) { ?>
                            <div class="alert alert-success"><?= $success_message ?> <a href="dashboard.php">View Dashboard</a></div>
                        <?php } ?>
                        <?php if (isset($error_message)) { ?>
                            <div class="alert alert-danger"><?= $error_message ?></div>
                        <?php } ?>
				      <div class="row">
				        <div class="col-md-6">
				          <div class="form-group">
				            <label for="client_name">Client Name</label>
				            <input type="text" class="form-control border" id="client_name" name="client_name" placeholder="Enter client name" required>
				          </div>
				        </div>
				        <div class="col-md-6">
				          <div class="form-group">
				            <label for="total_amount">Total Amount</label>
				            <input type="number" class="form-control border" id="total_amount" name="total_amount" placeholder="Enter total amount" required>
				          </div>
				        </div>
				      </div>
				      <div class="row">
				        <div class="col-md-6">
				          <div class="form-group">
				            <label for="pending_amount">Pending Amount</label>
				            <input type="number" class="form-control border" id="pending_amount" name="pending_amount" placeholder="Enter pending amount" required>
				          </div>
				        </div>
				        <div class="col-md-6">
				          <div class="form-group">
				            <label for="date">Date</label>
				            <input type="date" class="form-control border" id="date_added" name="date_added" required>
				          </div>
				        </div>
				      </div>
				      <div class="text-end mt-3">
				        <button type="submit" class="btn btn-primary">Add Payment</button>
				      </div>
				    </div>
				  </div>
				</form>
				
              </div>
            </div>
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