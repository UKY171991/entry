<?php
// pending-task.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="assets/img/favicon.png">
  <title>Pending Tasks | Admin Panel</title>
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="g-sidenav-show  bg-gray-100">
<?php include('navbar.php'); ?>
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <?php include('top.php'); ?>
    <div class="container-fluid py-2">
        <div class="row mb-3">
            <div class="col-12">
                <form id="filterForm" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="filterClient" class="form-label">Client Name</label>
                        <select class="form-control border" id="filterClient" name="client_name">
                          <option value="">All Clients</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filterStatus" class="form-label">Status</label>
                        <select class="form-control border" id="filterStatus" name="status">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header d-flex justify-content-between align-items-center p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-2 pb-2">
                            <h6 class="text-white text-capitalize ps-3">Pending Tasks</h6>
                        </div>
                        <button class="btn btn-primary m-3" id="addTaskBtn"><i class="fa fa-plus"></i> Add Task</button>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="tasksTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Task Name</th>
                                        <th>Client Name</th>
                                        <th>Description</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Payment Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="8" class="text-center">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <nav>
                            <ul class="pagination justify-content-center" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="taskForm" enctype="multipart/form-data">
            <div class="modal-header">
              <h5 class="modal-title" id="taskModalLabel">Add Task</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="id" id="taskId" class="border">
              <div class="mb-3">
                <label for="taskName" class="form-label">Task Name</label>
                <input type="text" class="form-control border" id="taskName" name="task_name" required>
              </div>
              <div class="mb-3">
                <label for="clientId" class="form-label">Client Name</label>
                <select class="form-control border" id="clientId" name="client_id" required>
                  <option value="">Select Client</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
              </div>
              <div class="mb-3">
                <label for="dueDate" class="form-label">Due Date</label>
                <input type="date" class="form-control border" id="dueDate" name="due_date">
              </div>
              <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control border" id="status" name="status">
                  <option value="pending">Pending</option>
                  <option value="completed">Completed</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="payment" class="form-label">Payment</label>
                <input type="number" class="form-control border" id="payment" name="payment" min="0" step="0.01" placeholder="Enter payment amount">
              </div>
              <div class="mb-3">
                <label for="payment_status" class="form-label">Payment Status</label>
                <select class="form-control border" id="payment_status" name="payment_status">
                  <option value="unpaid">Unpaid</option>
                  <option value="partial">Partial</option>
                  <option value="paid">Paid</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="taskImage" class="form-label">Task Image</label>
                <input type="file" class="form-control border" id="taskImage" name="image" accept="image/*">
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
let currentPage = 1;
let lastPage = 1;
function loadTasks(page = 1) {
  let filters = $('#filterForm').serializeArray();
  let data = {action: 'list', page: page};
  filters.forEach(f => data[f.name] = f.value);
  $.post('tasks-ajax.php', data, function(res) {
    let html = '';
    if(res.success && res.tasks.length) {
      res.tasks.forEach(function(task, idx) {
        html += `<tr>
          <td>${((page-1)*10)+(idx+1)}</td>
          <td>${task.image ? `<img src="${task.image}" alt="Task Image" style="width:40px;height:40px;object-fit:cover;border-radius:4px;">` : ''}</td>
          <td>${task.task_name}</td>
          <td>${task.client_name || ''}</td>
          <td>${task.description}</td>
          <td>${task.due_date || ''}</td>
          <td>${task.status.charAt(0).toUpperCase() + task.status.slice(1)}</td>
          <td>${task.payment !== undefined ? task.payment : ''}</td>
          <td>${task.payment_status ? task.payment_status.charAt(0).toUpperCase() + task.payment_status.slice(1) : ''}</td>
          <td>
            <button class="btn btn-info btn-sm editTaskBtn" data-id="${task.id}" data-task='${JSON.stringify(task)}'><i class="fa fa-edit"></i></button>
            <button class="btn btn-danger btn-sm deleteTaskBtn" data-id="${task.id}"><i class="fa fa-trash"></i></button>
          </td>
        </tr>`;
      });
    } else {
      html = '<tr><td colspan="8" class="text-center">No pending tasks to display.</td></tr>';
    }
    $('#tasksTable tbody').html(html);
    // Pagination
    let pagination = '';
    currentPage = res.page || 1;
    lastPage = res.last_page || 1;
    if (lastPage > 1) {
      for (let i = 1; i <= lastPage; i++) {
        pagination += `<li class="page-item${i==currentPage?' active':''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
      }
    }
    $('#pagination').html(pagination);
  }, 'json');
}

function loadClientsDropdown(selectedId = null) {
  $.post('clients-ajax.php', {action: 'list'}, function(res) {
    let options = '<option value="">Select Client</option>';
    if(res.success && res.clients.length) {
      res.clients.forEach(function(client) {
        options += `<option value="${client.id}" ${selectedId == client.id ? 'selected' : ''}>${client.client_name}</option>`;
      });
    }
    $('#clientId').html(options);
  }, 'json');
}

function loadClientFilterDropdown() {
  $.post('clients-ajax.php', {action: 'list'}, function(res) {
    let options = '<option value="">All Clients</option>';
    if(res.success && res.clients.length) {
      res.clients.forEach(function(client) {
        options += `<option value="${client.client_name}">${client.client_name}</option>`;
      });
    }
    $('#filterClient').html(options);
  }, 'json');
}

$(document).ready(function() {
  loadClientFilterDropdown();
  loadTasks();

  $('#filterForm').submit(function(e) {
    e.preventDefault();
    loadTasks(1);
  });

  $(document).on('click', '#pagination .page-link', function(e) {
    e.preventDefault();
    let page = $(this).data('page');
    if(page && page != currentPage) loadTasks(page);
  });

  $('#addTaskBtn').click(function() {
    $('#taskForm')[0].reset();
    $('#taskId').val('');
    $('#taskModalLabel').text('Add Task');
    loadClientsDropdown();
    $('#taskModal').modal('show');
  });

  $(document).on('click', '.editTaskBtn', function() {
    let task = $(this).data('task');
    $('#taskId').val(task.id);
    $('#taskName').val(task.task_name);
    loadClientsDropdown(task.client_id);
    $('#description').val(task.description);
    $('#dueDate').val(task.due_date);
    $('#status').val(task.status);
    $('#taskModalLabel').text('Edit Task');
    $('#taskModal').modal('show');
  });

  $('#taskForm').submit(function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    let action = $('#taskId').val() ? 'edit' : 'add';
    formData.append('action', action);
    $.ajax({
      url: 'tasks-ajax.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(res) {
        if(res.success) {
          $('#taskModal').modal('hide');
          loadTasks(currentPage);
        } else {
          alert('Error saving task');
        }
      }
    });
  });

  $(document).on('click', '.deleteTaskBtn', function() {
    if(confirm('Are you sure you want to delete this task?')) {
      let id = $(this).data('id');
      $.post('tasks-ajax.php', {action: 'delete', id: id}, function(res) {
        if(res.success) loadTasks(currentPage);
        else alert('Error deleting task');
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
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>
</html> 