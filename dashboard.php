<?php
session_start();
include 'db.php';

// Fetch total expenses
$total_expenses_query = "SELECT SUM(amount) AS total_expenses FROM expenses";
$total_expenses_result = $conn->query($total_expenses_query);
$total_expenses = $total_expenses_result->fetch_assoc()['total_expenses'];

// Fetch total payments received
$total_received_query = "SELECT SUM(total_amount - pending_amount) AS total_received FROM payments";
$total_received_result = $conn->query($total_received_query);
$total_received = $total_received_result->fetch_assoc()['total_received'];

// Fetch total pending payments
$total_pending_query = "SELECT SUM(pending_amount) AS total_pending FROM payments";
$total_pending_result = $conn->query($total_pending_query);
$total_pending = $total_pending_result->fetch_assoc()['total_pending'];

// Fetch expenses data for the chart
$expenses_chart_query = "SELECT DATE_FORMAT(date_added, '%Y-%m') AS month, SUM(amount) AS total FROM expenses GROUP BY month ORDER BY month";
$expenses_chart_result = $conn->query($expenses_chart_query);
$expenses_chart_data = [];
while ($row = $expenses_chart_result->fetch_assoc()) {
    $expenses_chart_data[$row['month']] = $row['total'];
}

// Fetch payments data for the chart
$payments_chart_query = "SELECT DATE_FORMAT(date_added, '%Y-%m') AS month, SUM(total_amount) AS total FROM payments GROUP BY month ORDER BY month";
$payments_chart_result = $conn->query($payments_chart_query);
$payments_chart_data = [];
while ($row = $payments_chart_result->fetch_assoc()) {
    $payments_chart_data[$row['month']] = $row['total'];
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
</head>

<body class="g-sidenav-show  bg-gray-100">
  <?php include('navbar.php');?>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php include('top.php'); ?>
    <!-- End Navbar -->
    <div class="container-fluid py-2">
      <div class="row">
        <div class="ms-3">
          <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
          
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-primary text-white m-1">
                    <div class="card-body">
                        <h5>Total Expenses</h5>
                        <h3><?= number_format($total_expenses, 2) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white m-1">
                    <div class="card-body">
                        <h5>Total Payments Received</h5>
                        <h3><?= number_format($total_received, 2) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white m-1">
                    <div class="card-body">
                        <h5>Total Pending Payments</h5>
                        <h3><?= number_format($total_pending, 2) ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-warning text-white m-1">
                    <div class="card-body">
                        <h5>Total Revenue</h5>
                        <h3><?= number_format((($total_received - $total_expenses) - $total_pending ),2) ?></h3>
                    </div>
                </div>
            </div>
            
            
       
    
      </div>
      <div class="row">
          
          <!-- Charts -->
        <div class="row">
            <div class="col-md-6">
                <canvas id="expensesChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="paymentsChart"></canvas>
            </div>
        </div>
        
      </div>
    
    </div>
  </main>
 
  <!--   Core JS Files   -->
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="assets/js/plugins/chartjs.min.js"></script>
 

  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="assets/js/material-dashboard.min.js?v=3.2.0"></script>
  
  
  
  <script>
        const expensesData = {
            labels: <?= json_encode(array_keys($expenses_chart_data)) ?>,
            datasets: [{
                label: 'Monthly Expenses',
                data: <?= json_encode(array_values($expenses_chart_data)) ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        };
        
        const paymentsData = {
            labels: <?= json_encode(array_keys($payments_chart_data)) ?>,
            datasets: [{
                label: 'Monthly Payments',
                data: <?= json_encode(array_values($payments_chart_data)) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };
        
        new Chart(document.getElementById('expensesChart'), {
            type: 'bar',
            data: expensesData,
        });
        
        new Chart(document.getElementById('paymentsChart'), {
            type: 'line',
            data: paymentsData,
        });
    </script>
    
</body>

</html>