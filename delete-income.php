<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: payments.php");
    exit;
}

$payment_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Securely delete the payment record
$query = "DELETE FROM payments WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $payment_id, $user_id);

if ($stmt->execute()) {
    echo "<script>alert('Deleted susscefully!');</script>";
    header("Location: income.php?message=Payment deleted successfully");
    exit;
} else {
    header("Location: income.php?error=Error deleting record");
    exit;
}
?>
