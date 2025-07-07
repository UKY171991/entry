<?php
include 'db.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        $sql = "SELECT * FROM clients ORDER BY created_at DESC, id DESC";
        $result = $conn->query($sql);
        $clients = [];
        while ($row = $result->fetch_assoc()) {
            $clients[] = $row;
        }
        echo json_encode(['success' => true, 'clients' => $clients]);
        break;
    case 'add':
        $client_name = $_POST['client_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $stmt = $conn->prepare("INSERT INTO clients (client_name, email, phone, address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $client_name, $email, $phone, $address);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;
    case 'edit':
        $id = $_POST['id'] ?? 0;
        $client_name = $_POST['client_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $stmt = $conn->prepare("UPDATE clients SET client_name=?, email=?, phone=?, address=? WHERE id=?");
        $stmt->bind_param('ssssi', $client_name, $email, $phone, $address, $id);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;
    case 'delete':
        $id = $_POST['id'] ?? 0;
        $stmt = $conn->prepare("DELETE FROM clients WHERE id=?");
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
} 