<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id == 0) {
    header("Location: dashboard.php");
    exit;
}

// Fetch existing data
$stmt = $conn->prepare("SELECT client_name, url FROM website_list WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    header("Location: dashboard.php");
    exit;
}
$site = $result->fetch_assoc();
$stmt->close();

$success_message = '';
$error_message = '';

// Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = trim($_POST['client_name']);
    $url = trim($_POST['url']);

    if (!empty($client_name) && !empty($url)) {
        $update_stmt = $conn->prepare("UPDATE website_list SET client_name = ?, url = ? WHERE id = ?");
        $update_stmt->bind_param("ssi", $client_name, $url, $id);

        if ($update_stmt->execute()) {
            $success_message = "Website details updated successfully.";
        } else {
            $error_message = "Error updating details: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        $error_message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Website</title>
    
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
<body class="bg-gray-100">
    <?php include('navbar.php'); ?>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <?php include('top.php'); ?>

        <div class="container py-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Edit Website Details</h5>
                </div>
                <div class="card-body">
                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?= $success_message ?></div>
                    <?php endif; ?>
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?= $error_message ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-group">
                            <label>Client Name:</label>
                            <input type="text" class="form-control border" name="client_name" value="<?= htmlspecialchars($site['client_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Website URL:</label>
                            <input type="url" class="form-control border" name="url" value="<?= htmlspecialchars($site['url']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-success mt-3">Update</button>
                        <a href="dashboard.php" class="btn btn-secondary mt-3">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <?php include('footer.php'); ?>
    </main>

    <!--   Core JS Files   -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>