<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$month_filter = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';
$expense_name_filter = isset($_GET['expense_name']) ? $_GET['expense_name'] : ''; // New expense name filter

$query = "SELECT * FROM expenses WHERE user_id = ? AND date_added LIKE ?";
// Add date filter condition if provided
if (!empty($date_filter)) {
    $query .= " AND DATE(date_added) = ?";
}
// Add expense name filter condition if provided
if (!empty($expense_name_filter)) {
    $query .= " AND expense_name LIKE ?";
}
$query .= " ORDER BY date_added DESC";

$stmt = $conn->prepare($query);
$month_filter_param = "$month_filter%";

// Bind parameters dynamically based on filters provided
if (!empty($date_filter) && !empty($expense_name_filter)) {
    $expense_name_param = "%$expense_name_filter%"; // Allow partial matches
    $stmt->bind_param("isss", $user_id, $month_filter_param, $date_filter, $expense_name_param);
} elseif (!empty($date_filter)) {
    $stmt->bind_param("iss", $user_id, $month_filter_param, $date_filter);
} elseif (!empty($expense_name_filter)) {
    $expense_name_param = "%$expense_name_filter%"; // Allow partial matches
    $stmt->bind_param("iss", $user_id, $month_filter_param, $expense_name_param);
} else {
    $stmt->bind_param("is", $user_id, $month_filter_param);
}

$stmt->execute();
$result = $stmt->get_result();

$total_amount = 0;
$expenses = [];
while ($row = $result->fetch_assoc()) {
    $expenses[] = $row;
    $total_amount += $row['amount'];
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
        
        <form id="filterForm" method="GET">
            <div class="row">
                <div class="col-md-2">
                    <input type="month" name="month" class="form-control border" value="<?= htmlspecialchars($month_filter) ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" class="form-control border" value="<?= htmlspecialchars($date_filter) ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" name="expense_name" class="form-control border" placeholder="Expense Name" value="<?= htmlspecialchars($expense_name_filter) ?>">
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                </div>
            </div>
        </form>
        <!-- Add/Edit Modal -->
        <div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <form id="expenseForm">
                <div class="modal-header">
                  <h5 class="modal-title" id="expenseModalLabel">Add Expense</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="id" id="expenseId" class="border">
                  <div class="mb-3">
                    <label for="expense_name" class="form-label">Expense Name</label>
                    <input type="text" class="form-control border" id="expense_name" name="expense_name" required>
                  </div>
                  <div class="mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" step="0.01" class="form-control border" id="amount" name="amount" required>
                  </div>
                  <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-control" id="category" name="category" required>
                      <option value="Personal">Personal</option>
                      <option value="Business">Business</option>
                      <option value="Other">Other</option>
                    </select>
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
        // AJAX logic for add/edit/filter/delete and table rendering
        let editingId = null;
        function renderTable(expenses) {
          let html = '';
          let total = 0;
          if (expenses.length) {
            expenses.forEach((row, idx) => {
              html += `<tr>
                <td>${idx + 1}</td>
                <td>${row.expense_name}</td>
                <td>${parseFloat(row.amount).toFixed(2)}</td>
                <td>${row.category}</td>
                <td>${row.date_added ? new Date(row.date_added).toLocaleDateString() : ''}</td>
                <td>
                  <button class='btn btn-info btn-sm editExpenseBtn' data-id='${row.id}' data-row='${encodeURIComponent(JSON.stringify(row))}'><i class='fas fa-edit'></i> Edit</button>
                  <button class='btn btn-danger btn-sm deleteExpenseBtn' data-id='${row.id}'><i class='fas fa-trash'></i> Delete</button>
                </td>
              </tr>`;
              total += parseFloat(row.amount);
            });
          } else {
            html = `<tr><td colspan='6' class='text-center'>No expenses found for this month.</td></tr>`;
          }
          $(".table tbody").html(html);
          // Update total
          $(".table tfoot td").eq(1).html(`<strong>${total.toFixed(2)}</strong>`);
        }
        function loadTable(filters = {}) {
          $.post('expense-ajax.php', {action: 'list', ...filters}, function(res) {
            if (res.success) renderTable(res.expenses);
          }, 'json');
        }
        $(document).ready(function() {
          // Initial load
          loadTable();
          // Filter
          $('#filterForm').submit(function(e) {
            e.preventDefault();
            const filters = $(this).serializeArray().reduce((a, x) => { a[x.name] = x.value; return a; }, {});
            loadTable(filters);
          });
          // Add Expense button
          $(document).on('click', '.btn-dark', function(e) {
            e.preventDefault();
            editingId = null;
            $('#expenseForm')[0].reset();
            $('#expenseId').val('');
            $('#expenseModalLabel').text('Add Expense');
            $('#expenseModal').modal('show');
          });
          // Edit button
          $(document).on('click', '.editExpenseBtn', function() {
            const row = JSON.parse(decodeURIComponent($(this).data('row')));
            editingId = row.id;
            $('#expenseId').val(row.id);
            $('#expense_name').val(row.expense_name);
            $('#amount').val(row.amount);
            $('#category').val(row.category);
            $('#date_added').val(row.date_added);
            $('#expenseModalLabel').text('Edit Expense');
            $('#expenseModal').modal('show');
          });
          // Save (add/edit)
          $('#expenseForm').submit(function(e) {
            e.preventDefault();
            const formData = $(this).serializeArray();
            let action = editingId ? 'edit' : 'add';
            formData.push({name: 'action', value: action});
            $.post('expense-ajax.php', formData, function(res) {
              if (res.success) {
                $('#expenseModal').modal('hide');
                loadTable($('#filterForm').serializeArray().reduce((a, x) => { a[x.name] = x.value; return a; }, {}));
              } else {
                alert('Error saving record');
              }
            }, 'json');
          });
          // Delete
          $(document).on('click', '.deleteExpenseBtn', function() {
            if (confirm('Are you sure?')) {
              const id = $(this).data('id');
              $.post('expense-ajax.php', {action: 'delete', id: id}, function(res) {
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
              <a href="add-expense.php" class="btn btn-dark">
                  <i class="fa fa-plus"></i> Add Expense
              </a>
          </div>
      </div>
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-2 pb-2">
                <h6 class="text-white text-capitalize ps-3">Expenses table</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Expense Name</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Category</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                      <th class="text-secondary opacity-7">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                  <tfoot>
                    <tr>
                        <td class="text-end"><strong>Total Amount:</strong></td>
                        <td colspan="4"><strong>0.00</strong></td>
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