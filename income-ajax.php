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
        $pending_filter = $_POST['pending_amount'] ?? $_GET['pending_amount'] ?? '';
        $received_filter = $_POST['received_amount'] ?? $_GET['received_amount'] ?? '';
        $query = "SELECT p.*, c.client_name FROM payments p LEFT JOIN clients c ON p.client_id = c.id WHERE p.user_id = ? AND p.date_added LIKE ?";
        $params = [$user_id, "$month%"];
        $types = "is";
        if ($pending_filter !== '') {
            $query .= " AND p.pending_amount " . ($pending_filter == '0' ? "= ?" : "> ?");
            $params[] = 0;
            $types .= "d";
        }
        if ($received_filter !== '') {
            $query .= " AND (p.total_amount - p.pending_amount) " . ($received_filter == '0' ? "= ?" : "> ?");
            $params[] = 0;
            $types .= "d";
        }
        $query .= " ORDER BY p.id DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $payments = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['success' => true, 'payments' => $payments]);
        break;
    case 'add':
        $client_id = $_POST['client_id'] ?? 0;
        $total_amount = $_POST['total_amount'] ?? 0;
        $pending_amount = $_POST['pending_amount'] ?? 0;
        $date_added = $_POST['date_added'] ?? date('Y-m-d');
        $stmt = $conn->prepare("INSERT INTO payments (user_id, client_id, total_amount, pending_amount, date_added) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('iidss', $user_id, $client_id, $total_amount, $pending_amount, $date_added);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;
    case 'edit':
        $id = $_POST['id'] ?? 0;
        $client_id = $_POST['client_id'] ?? 0;
        $total_amount = $_POST['total_amount'] ?? 0;
        $pending_amount = $_POST['pending_amount'] ?? 0;
        $date_added = $_POST['date_added'] ?? date('Y-m-d');
        $stmt = $conn->prepare("UPDATE payments SET client_id=?, total_amount=?, pending_amount=?, date_added=? WHERE id=? AND user_id=?");
        $stmt->bind_param('iddsii', $client_id, $total_amount, $pending_amount, $date_added, $id, $user_id);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;
    case 'delete':
        $id = $_POST['id'] ?? 0;
        $stmt = $conn->prepare("DELETE FROM payments WHERE id=? AND user_id=?");
        $stmt->bind_param('ii', $id, $user_id);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
} 