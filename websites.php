<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include 'db.php'; // Database connection assumed

// Fetch websites from database
$stmt = $conn->prepare("SELECT * FROM website_list ORDER BY date_added DESC");
$stmt->execute();
$result = $stmt->get_result();
$websites = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Function to check website status
function checkWebsiteStatus($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if (!empty($error)) {
        return "Error ❗";
    }

    return ($httpCode >= 200 && $httpCode < 400) ? "UP ✅" : "DOWN ❌";
}

// Handle AJAX request for testing URL
if (isset($_POST['test_url'])) {
    echo checkWebsiteStatus($_POST['test_url']);
    exit;
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
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Client Name</th>
                                <th>Website URL</th>
                                <th>Status</th>
                                <!--<th>Date Added</th>-->
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($websites as $index => $site): ?>
                                <tr>
                                    <td><?= $index + 1; ?></td>
                                    <td><?= htmlspecialchars($site['client_name']); ?></td>
                                    <td><?= htmlspecialchars($site['url']); ?></td>
                                    <td id="status-<?= $site['id']; ?>">N/A</td>
                                    <!--<td><?//=date('d-m-Y  h:i:s A', strtotime($site['date_added'])); ?></td>-->
                                    <td><?= date('d-m-Y h:i:s A', strtotime($site['date_updated'])); ?></td>
                                    <td>
                                        <a href="edit-website.php?id=<?= $site['id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <a href="<?= htmlspecialchars($site['url']); ?>" target="_blank" class="btn btn-sm btn-secondary">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        <button class="btn btn-sm btn-info" onclick="testWebsite(<?= $site['id']; ?>, '<?= htmlspecialchars($site['url']); ?>')">
                                            <i class="fa fa-globe"></i> Test
                                        </button>
                                    </td>
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
  

    <script>
        
        function testWebsite(id, url) {
            $.ajax({
                method: 'POST',
                url: '',
                data: { test_url: url },
                success: function(response) {
                    $('#status-' + id).html(response);
                },
                error: function() {
                    $('#status-' + id).html('Error ❗');
                }
            });
        }
        
        $(document).ready(function(){
            <?php foreach ($websites as $site): ?>
              testWebsite(<?= $site['id']; ?>, '<?= htmlspecialchars($site['url']); ?>');
            <?php endforeach; ?>
          });
    </script>

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
