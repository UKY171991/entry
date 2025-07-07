<?php
session_start();
include 'db.php';
$expense_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$query = "DELETE FROM expenses WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $expense_id, $user_id);
$stmt->execute();
echo "<script>alert('Deleted susscefully!');</script>";
header("Location: expense.php");
exit;
?>
