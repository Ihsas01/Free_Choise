<?php
// Helper function to check if user is banned
function isUserBanned($user_id) {
    global $conn;
    
    $query = "SELECT is_banned, ban_until, ban_reason FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($result && $result['is_banned']) {
        // Check if ban has expired
        if ($result['ban_until'] && strtotime($result['ban_until']) < time()) {
            // Ban has expired, unban the user
            $unban_query = "UPDATE users SET is_banned = FALSE, ban_reason = NULL, ban_until = NULL WHERE user_id = ?";
            $unban_stmt = $conn->prepare($unban_query);
            $unban_stmt->bind_param("i", $user_id);
            $unban_stmt->execute();
            $unban_stmt->close();
            
            // Update ban history
            $update_history = "UPDATE user_bans SET is_active = FALSE WHERE user_id = ? AND is_active = TRUE";
            $update_stmt = $conn->prepare($update_history);
            $update_stmt->bind_param("i", $user_id);
            $update_stmt->execute();
            $update_stmt->close();
            
            return false; // User is no longer banned
        }
        
        return [
            'banned' => true,
            'until' => $result['ban_until'],
            'reason' => $result['ban_reason']
        ];
    }
    
    return false; // User is not banned
}

// Function to display ban warning
function displayBanWarning($ban_info) {
    if ($ban_info && $ban_info['banned']) {
        $until_date = date('M d, Y', strtotime($ban_info['until']));
        $reason = htmlspecialchars($ban_info['reason']);
        
        echo '<div class="ban-warning" style="
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin: 1rem 0;
            text-align: center;
            box-shadow: 0 8px 32px rgba(231, 76, 60, 0.3);
            animation: fadeInUp 0.5s ease-out;
        ">
            <i class="fas fa-ban" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
            <h3 style="margin: 0.5rem 0; font-size: 1.2rem;">Account Temporarily Suspended</h3>
            <p style="margin: 0.5rem 0; opacity: 0.9;">
                <strong>Reason:</strong> ' . $reason . '<br>
                <strong>Until:</strong> ' . $until_date . '
            </p>
            <p style="margin: 0.5rem 0; font-size: 0.9rem; opacity: 0.8;">
                You cannot place orders during this suspension period.
            </p>
        </div>';
    }
}

// Function to check if user can place orders
function canUserPlaceOrders($user_id) {
    $ban_info = isUserBanned($user_id);
    return !$ban_info || !$ban_info['banned'];
}
?> 