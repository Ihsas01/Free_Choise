<?php
session_start();
require_once '../config/database.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$notification_id = $data['notification_id'] ?? null;

if (!$notification_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Notification ID is required']);
    exit();
}

try {
    // Update notification status
    $update_query = "UPDATE admin_notifications SET status = 'read' WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("i", $notification_id);
    $update_stmt->execute();

    if ($update_stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Notification not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error updating notification']);
} 