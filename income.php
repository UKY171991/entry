<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$month_filter = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$pending_filter = isset($_GET['pending_amount']) ? $_GET['pending_amount'] : '';
$received_filter = isset($_GET['received_amount']) ? $_GET['received_amount'] : '';

// Build the query dynamically
$query = "SELECT * FROM payments WHERE user_id = ? AND date_added LIKE ?";
$params = [$user_id, "$month_filter%"];
$types = "is";

if ($pending_filter !== '') {
    $query .= " AND pending_amount " . ($pending_filter == '0' ? "= ?" : "> ?");
    $params[] = 0;
    $types .= "d";
}

if ($received_filter !== '') {
    $query .= " AND (total_amount - pending_amount) " . ($received_filter == '0' ? "= ?" : "> ?");
    $params[] = 0;
    $types .= "d";
}

$query .= " ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Calculate totals
$total_amount = 0;
$pending_amount = 0;
$received_amount = 0;
$payments = [];
while ($row = $result->fetch_assoc()) {
    $payments[] = $row;
    $total_amount += $row['total_amount'];
    $pending_amount += $row['pending_amount'];
    $received_amount += ($row['total_amount'] - $row['pending_amount']);
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
    <?php include('navbar.php');?>
  
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php include('top.php'); ?>
    <!-- End Navbar -->
    <div class="container-fluid py-2">
        
        <form id="filterForm" method="GET">
            <div class="row">
                <div class="col-md-2">
                    <label for="month">Month</label>
                    <input type="month" name="month" class="form-control border" value="<?= htmlspecialchars($month_filter) ?>">
                </div>
                <div class="col-md-2">
                    <label for="pending_amount">Pending Amount</label>
                    <select name="pending_amount" class="form-control border">
                        <option value="">All</option>
                        <option value="0" <?= ($pending_filter === '0') ? 'selected' : '' ?>>0 (Paid)</option>
                        <option value="1" <?= ($pending_filter === '1') ? 'selected' : '' ?>>Not 0 (Pending)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="received_amount">Received Amount</label>
                    <select name="received_amount" class="form-control border">
                        <option value="">All</option>
                        <option value="0" <?= ($received_filter === '0') ? 'selected' : '' ?>>0 (Not Received)</option>
                        <option value="1" <?= ($received_filter === '1') ? 'selected' : '' ?>>Not 0 (Received)</option>
                    </select>
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="submit" class="btn btn-primary "><i class="fas fa-filter"></i> Filter</button>
                </div>
            </div>
        </form>
        <!-- Add/Edit Modal -->
        <div class="modal fade" id="incomeModal" tabindex="-1" aria-labelledby="incomeModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <form id="incomeForm">
                <div class="modal-header">
                  <h5 class="modal-title" id="incomeModalLabel">Add Income</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="id" id="incomeId" class="border">
                  <div class="mb-3">
                    <label for="client_id" class="form-label">Client Name</label>
                    <select class="form-control border" id="client_id" name="client_id" required>
                      <option value="">Select Client</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="total_amount" class="form-label">Total Amount</label>
                    <input type="number" step="0.01" class="form-control border" id="total_amount" name="total_amount" required>
                  </div>
                  <div class="mb-3">
                    <label for="pending_amount" class="form-label">Pending Amount</label>
                    <input type="number" step="0.01" class="form-control border" id="pending_amount" name="pending_amount" required>
                  </div>
                  <div class="mb-3">
                    <label for="date_added" class="form-label">Date</label>
                    <input type="date" class="form-control border" id="date_added" name="date_added" required>
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
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
        // Function to load clients into dropdown
        function loadClientsDropdown(selectedId = '') {
          $.post('clients-ajax.php', {action: 'list'}, function(res) {
            let options = '<option value="">Select Client</option>';
            if(res.success && res.clients.length) {
              res.clients.forEach(function(client) {
                options += `<option value="${client.id}" ${selectedId == client.id ? 'selected' : ''}>${client.client_name}</option>`;
              });
            }
            $('#client_id').html(options);
          }, 'json');
        }

        // AJAX logic for add/edit/filter/delete and table rendering
        let editingId = null;
        function renderTable(payments) {
          let html = '';
          let total = 0, pending = 0, received = 0;
          if (payments.length) {
            payments.forEach(row => {
              const receivedAmt = (parseFloat(row.total_amount) - parseFloat(row.pending_amount)).toFixed(2);
              html += `<tr>
                <td>${row.client_name || ''}</td>
                <td>${parseFloat(row.total_amount).toFixed(2)}</td>
                <td>${parseFloat(row.pending_amount).toFixed(2)}</td>
                <td>${receivedAmt}</td>
                <td>${row.date_added ? new Date(row.date_added).toLocaleDateString() : ''}</td>
                <td>
                  <button class='btn btn-info btn-sm editIncomeBtn' data-id='${row.id}' data-row='${encodeURIComponent(JSON.stringify(row))}'><i class='fas fa-edit'></i> Edit</button>
                  <button class='btn btn-danger btn-sm deleteIncomeBtn' data-id='${row.id}'><i class='fas fa-trash'></i> Delete</button>
                </td>
              </tr>`;
              total += parseFloat(row.total_amount);
              pending += parseFloat(row.pending_amount);
              received += parseFloat(receivedAmt);
            });
          } else {
            html = `<tr><td colspan='6' class='text-center'>No records found.</td></tr>`;
          }
          $(".table tbody").html(html);
          // Update totals
          $(".table tfoot td").eq(1).html(`<strong>${total.toFixed(2)}</strong>`);
          $(".table tfoot td").eq(2).html(`<strong>${pending.toFixed(2)}</strong>`);
          $(".table tfoot td").eq(3).html(`<strong>${received.toFixed(2)}</strong>`);
        }
        function loadTable(filters = {}) {
          $.post('income-ajax.php', {action: 'list', ...filters}, function(res) {
            if (res.success) renderTable(res.payments);
          }, 'json');
        }
        $(document).ready(function() {
          // Load clients dropdown when page loads
          loadClientsDropdown();
          
          // Initial load of table data
          loadTable();
          
          // Load clients dropdown when modal opens
          $('#incomeModal').on('show.bs.modal', function() {
            loadClientsDropdown();
          });

          // When editing, set the selected client
          $(document).on('click', '.editIncomeBtn', function() {
            let row = JSON.parse(decodeURIComponent($(this).data('row')));
            editingId = row.id;
            $('#incomeId').val(row.id);
            loadClientsDropdown(row.client_id);
            $('#total_amount').val(row.total_amount);
            $('#pending_amount').val(row.pending_amount);
            $('#date_added').val(row.date_added);
            $('#incomeModalLabel').text('Edit Income');
            $('#incomeModal').modal('show');
          });
          // Filter
          $('#filterForm').submit(function(e) {
            e.preventDefault();
            const filters = $(this).serializeArray().reduce((a, x) => { a[x.name] = x.value; return a; }, {});
            loadTable(filters);
          });
          // Add Income button
          $(document).on('click', '.btn-dark', function(e) {
            e.preventDefault();
            editingId = null;
            $('#incomeForm')[0].reset();
            $('#incomeId').val('');
            $('#incomeModalLabel').text('Add Income');
            $('#incomeModal').modal('show');
          });
          // Save (add/edit)
          $('#incomeForm').submit(function(e) {
            e.preventDefault();
            let formData = {
              id: $('#incomeId').val(),
              client_id: $('#client_id').val(),
              total_amount: $('#total_amount').val(),
              pending_amount: $('#pending_amount').val(),
              date_added: $('#date_added').val(),
              action: editingId ? 'edit' : 'add'
            };
            $.post('income-ajax.php', formData, function(res) {
              if (res.success) {
                $('#incomeModal').modal('hide');
                loadTable();
              } else {
                alert('Error saving income');
              }
            }, 'json');
          });
          // Delete
          $(document).on('click', '.deleteIncomeBtn', function() {
            if (confirm('Are you sure?')) {
              const id = $(this).data('id');
              $.post('income-ajax.php', {action: 'delete', id: id}, function(res) {
                if (res.success) {
                  loadTable($('#filterForm').serializeArray().reduce((a, x) => { a[x.name] = x.value; return a; }, {}));
                } else {
                  alert('Error deleting record');
                }
              }, 'json');
            }
          });
        });
        </script>
      <div class="row">
          <div class="col-6">
              <h4 class="text-dark">Income Records</h4>
          </div>
          <div class="col-6 text-end">
              <a href="add-income.php" class="btn btn-dark">
                  <i class="fa fa-plus"></i> Add Income
              </a>
          </div>
      </div>
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-2 pb-2">
                <h6 class="text-white text-capitalize ps-3">Income table</h6>
              </div>
            </div>
            <?php if (isset($success_message)) { ?>
                <div class="alert alert-success"><?= $success_message ?> <a href="dashboard.php">View Dashboard</a></div>
            <?php } ?>
            <?php if (isset($error_message)) { ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php } ?>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Client Name</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Amount</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pending Amount</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Received Amount</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                      <th class="text-secondary opacity-7">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                  <tfoot>
                    <tr>
                        <td colspan="1" class="text-end"><strong>Totals:</strong></td>
                        <td><strong>0.00</strong></td>
                        <td><strong>0.00</strong></td>
                        <td><strong>0.00</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                </table>

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