<?php
session_start();
require_once '../includes/db_config.php';

// This script should be run via cron job or manually to check for users who cancel orders twice in a day

function checkAndBanUsers() {
    global $conn;
    
    // Get today's date
    $today = date('Y-m-d');
    
    // Find users who cancelled orders twice or more today
    $query = "SELECT user_id, COUNT(*) as cancellation_count 
              FROM order_cancellations 
              WHERE DATE(cancelled_at) = ? 
              GROUP BY user_id 
              HAVING cancellation_count >= 2";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $banned_users = [];
    
    while($row = $result->fetch_assoc()) {
        $user_id = $row['user_id'];
        
        // Check if user is already banned
        $check_ban = "SELECT is_banned FROM users WHERE user_id = ?";
        $ban_stmt = $conn->prepare($check_ban);
        $ban_stmt->bind_param("i", $user_id);
        $ban_stmt->execute();
        $user = $ban_stmt->get_result()->fetch_assoc();
        $ban_stmt->close();
        
        if($user && !$user['is_banned']) {
            // Ban the user for 2 days
            $ban_until = date('Y-m-d H:i:s', strtotime('+2 days'));
            $ban_reason = "Multiple order cancellations in a single day";
            
            // Update user ban status
            $ban_query = "UPDATE users SET is_banned = TRUE, ban_reason = ?, ban_until = ? WHERE user_id = ?";
            $ban_stmt = $conn->prepare($ban_query);
            $ban_stmt->bind_param("ssi", $ban_reason, $ban_until, $user_id);
            
            if($ban_stmt->execute()) {
                // Add to ban history
                $history_query = "INSERT INTO user_bans (user_id, reason, banned_by, ban_until) VALUES (?, ?, ?, ?)";
                $history_stmt = $conn->prepare($history_query);
                $admin_id = 1; // Default admin ID, you can change this
                $history_stmt->bind_param("isis", $user_id, $ban_reason, $admin_id, $ban_until);
                $history_stmt->execute();
                $history_stmt->close();
                
                $banned_users[] = $user_id;
            }
            $ban_stmt->close();
        }
    }
    
    $stmt->close();
    
    return $banned_users;
}

// Run the check if this script is called directly
if(php_sapi_name() === 'cli' || isset($_GET['run'])) {
    $banned = checkAndBanUsers();
    if(count($banned) > 0) {
        echo "Banned " . count($banned) . " users for multiple order cancellations.\n";
        echo "User IDs: " . implode(', ', $banned) . "\n";
    } else {
        echo "No users found with multiple cancellations today.\n";
    }
}
?> 