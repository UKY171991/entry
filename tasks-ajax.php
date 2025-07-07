<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        $status = $_POST['status'] ?? '';
        $client_name = $_POST['client_name'] ?? '';
        $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
        $per_page = 10;
        $offset = ($page - 1) * $per_page;
        $where = "WHERE t.user_id = ?";
        $params = [$user_id];
        $types = 'i';
        if ($status !== '') {
            $where .= " AND t.status = ?";
            $params[] = $status;
            $types .= 's';
        }
        if ($client_name !== '') {
            $where .= " AND c.client_name LIKE ?";
            $params[] = "%$client_name%";
            $types .= 's';
        }
        // Count total
        $count_sql = "SELECT COUNT(*) FROM tasks t LEFT JOIN clients c ON t.client_id = c.id $where";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param($types, ...$params);
        $count_stmt->execute();
        $count_stmt->bind_result($total);
        $count_stmt->fetch();
        $count_stmt->close();
        $last_page = ceil($total / $per_page);
        // Get paginated results
        $sql = "SELECT t.*, c.client_name FROM tasks t LEFT JOIN clients c ON t.client_id = c.id $where ORDER BY t.created_at DESC LIMIT $per_page OFFSET $offset";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $tasks = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode([
            'success' => true,
            'tasks' => $tasks,
            'page' => $page,
            'last_page' => $last_page
        ]);
        break;
    case 'add':
        $task_name = $_POST['task_name'] ?? '';
        $client_id = $_POST['client_id'] ?? null;
        $description = $_POST['description'] ?? '';
        $due_date = $_POST['due_date'] ?? null;
        $status = $_POST['status'] ?? 'pending';
        $payment = $_POST['payment'] ?? 0;
        $payment_status = $_POST['payment_status'] ?? 'unpaid';
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('task_', true) . '.' . $ext;
            $target_dir = 'assets/img/task-images/';
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $target_file = $target_dir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = $target_file;
            }
        }
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, task_name, client_id, description, due_date, status, payment, payment_status, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('isisssdss', $user_id, $task_name, $client_id, $description, $due_date, $status, $payment, $payment_status, $image_path);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;
    case 'edit':
        $id = $_POST['id'] ?? 0;
        $task_name = $_POST['task_name'] ?? '';
        $client_id = $_POST['client_id'] ?? null;
        $description = $_POST['description'] ?? '';
        $due_date = $_POST['due_date'] ?? null;
        $status = $_POST['status'] ?? 'pending';
        $payment = $_POST['payment'] ?? 0;
        $payment_status = $_POST['payment_status'] ?? 'unpaid';
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('task_', true) . '.' . $ext;
            $target_dir = 'assets/img/task-images/';
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $target_file = $target_dir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = $target_file;
            }
        }
        if ($image_path) {
            $stmt = $conn->prepare("UPDATE tasks SET task_name=?, client_id=?, description=?, due_date=?, status=?, payment=?, payment_status=?, image=? WHERE id=? AND user_id=?");
            $stmt->bind_param('sisssdssii', $task_name, $client_id, $description, $due_date, $status, $payment, $payment_status, $image_path, $id, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE tasks SET task_name=?, client_id=?, description=?, due_date=?, status=?, payment=?, payment_status=? WHERE id=? AND user_id=?");
            $stmt->bind_param('sisssdssii', $task_name, $client_id, $description, $due_date, $status, $payment, $payment_status, $id, $user_id);
        }
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;
    case 'delete':
        $id = $_POST['id'] ?? 0;
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id=? AND user_id=?");
        $stmt->bind_param('ii', $id, $user_id);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
} 