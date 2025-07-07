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
        
        // If order is cancelled, track the cancellation
        if ($status === 'cancelled') {
            // Get user_id from the order
            $user_query = "SELECT user_id FROM orders WHERE order_id = ?";
            $user_stmt = $conn->prepare($user_query);
            $user_stmt->bind_param("i", $order_id);
            $user_stmt->execute();
            $order = $user_stmt->get_result()->fetch_assoc();
            $user_stmt->close();
            
            if ($order) {
                // Track the cancellation
                $cancel_query = "INSERT INTO order_cancellations (user_id, order_id) VALUES (?, ?)";
                $cancel_stmt = $conn->prepare($cancel_query);
                $cancel_stmt->bind_param("ii", $order['user_id'], $order_id);
                $cancel_stmt->execute();
                $cancel_stmt->close();
                
                // Check if user should be banned (2+ cancellations today)
                $today = date('Y-m-d');
                $check_query = "SELECT COUNT(*) as count FROM order_cancellations 
                               WHERE user_id = ? AND DATE(cancelled_at) = ?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->bind_param("is", $order['user_id'], $today);
                $check_stmt->execute();
                $result = $check_stmt->get_result()->fetch_assoc();
                $check_stmt->close();
                
                if ($result['count'] >= 2) {
                    // Ban the user for 2 days
                    $ban_until = date('Y-m-d H:i:s', strtotime('+2 days'));
                    $ban_reason = "Multiple order cancellations in a single day";
                    
                    $ban_query = "UPDATE users SET is_banned = TRUE, ban_reason = ?, ban_until = ? WHERE user_id = ?";
                    $ban_stmt = $conn->prepare($ban_query);
                    $ban_stmt->bind_param("ssi", $ban_reason, $ban_until, $order['user_id']);
                    $ban_stmt->execute();
                    $ban_stmt->close();
                    
                    // Add to ban history
                    $history_query = "INSERT INTO user_bans (user_id, reason, banned_by, ban_until) VALUES (?, ?, ?, ?)";
                    $history_stmt = $conn->prepare($history_query);
                    $admin_id = $_SESSION['user_id'];
                    $history_stmt->bind_param("isis", $order['user_id'], $ban_reason, $admin_id, $ban_until);
                    $history_stmt->execute();
                    $history_stmt->close();
                    
                    $_SESSION['warning'] = 'User has been automatically banned for multiple cancellations';
                }
            }
        }
        
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