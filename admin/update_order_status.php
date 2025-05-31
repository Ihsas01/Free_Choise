<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

require_once '../includes/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    
    // Validate status
    $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        $_SESSION['error'] = 'Invalid status value';
        header('Location: order_details.php?id=' . $order_id);
        exit();
    }

    // Update order status
    $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        // Create notification for status change
        $message = "Order #$order_id status has been updated to " . ucfirst($status);
        $sql_notification = "INSERT INTO admin_notifications (order_id, message, type) VALUES (?, ?, 'status_update')";
        $stmt_notification = $conn->prepare($sql_notification);
        $stmt_notification->bind_param("is", $order_id, $message);
        $stmt_notification->execute();
        
        $_SESSION['success'] = 'Order status updated successfully';
    } else {
        $_SESSION['error'] = 'Failed to update order status';
    }
    
    header('Location: order_details.php?id=' . $order_id);
    exit();
} else {
    header('Location: dashboard.php');
    exit();
}
?> 