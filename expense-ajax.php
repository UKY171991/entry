<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $month = $_POST['month'] ?? $_GET['month'] ?? date('Y-m');
        $date = $_POST['date'] ?? $_GET['date'] ?? '';
        $expense_name = $_POST['expense_name'] ?? $_GET['expense_name'] ?? '';
        $query = "SELECT * FROM expenses WHERE user_id = ? AND date_added LIKE ?";
        $params = [$user_id, "$month%"];
        $types = "is";
        if (!empty($date)) {
            $query .= " AND DATE(date_added) = ?";
            $params[] = $date;
            $types .= "s";
        }
        if (!empty($expense_name)) {
            $query .= " AND expense_name LIKE ?";
            $params[] = "%$expense_name%";
            $types .= "s";
        }
        $query .= " ORDER BY date_added DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $expenses = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['success' => true, 'expenses' => $expenses]);
        break;
    case 'add':
        $expense_name = $_POST['expense_name'] ?? '';
        $amount = $_POST['amount'] ?? 0;
        $category = $_POST['category'] ?? '';
        $date_added = $_POST['date_added'] ?? date('Y-m-d');
        $stmt = $conn->prepare("INSERT INTO expenses (user_id, expense_name, amount, category, date_added) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('isdss', $user_id, $expense_name, $amount, $category, $date_added);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;
    case 'edit':
        $id = $_POST['id'] ?? 0;
        $expense_name = $_POST['expense_name'] ?? '';
        $amount = $_POST['amount'] ?? 0;
        $category = $_POST['category'] ?? '';
        $date_added = $_POST['date_added'] ?? date('Y-m-d');
        $stmt = $conn->prepare("UPDATE expenses SET expense_name=?, amount=?, category=?, date_added=? WHERE id=? AND user_id=?");
        $stmt->bind_param('sdssii', $expense_name, $amount, $category, $date_added, $id, $user_id);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;
    case 'delete':
        $id = $_POST['id'] ?? 0;
        $stmt = $conn->prepare("DELETE FROM expenses WHERE id=? AND user_id=?");
        $stmt->bind_param('ii', $id, $user_id);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
} 