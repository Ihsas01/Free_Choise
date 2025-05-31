<?php
session_start();
require_once '../config/database.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get unread notifications
$notifications_query = "SELECT n.*, o.total_amount, o.payment_method, o.status as order_status 
                      FROM admin_notifications n 
                      JOIN orders o ON n.order_id = o.order_id 
                      WHERE n.status = 'unread' 
                      ORDER BY n.created_at DESC";
$notifications_result = $conn->query($notifications_query);

// Include admin header
require_once 'includes/header.php';
?>

<div class="container">
    <h2>Order Notifications</h2>
    
    <div class="notifications-container">
        <?php if ($notifications_result->num_rows > 0): ?>
            <?php while ($notification = $notifications_result->fetch_assoc()): ?>
                <div class="notification-card" data-notification-id="<?php echo $notification['id']; ?>">
                    <div class="notification-header">
                        <h3>New Order #<?php echo $notification['order_id']; ?></h3>
                        <span class="notification-time">
                            <?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?>
                        </span>
                    </div>
                    <div class="notification-body">
                        <p><?php echo $notification['message']; ?></p>
                        <div class="order-details">
                            <p><strong>Total Amount:</strong> $<?php echo number_format($notification['total_amount'], 2); ?></p>
                            <p><strong>Payment Method:</strong> <?php echo ucfirst($notification['payment_method']); ?></p>
                            <p><strong>Status:</strong> <?php echo ucfirst($notification['order_status']); ?></p>
                        </div>
                    </div>
                    <div class="notification-actions">
                        <button class="btn btn-primary view-order" data-order-id="<?php echo $notification['order_id']; ?>">
                            View Order
                        </button>
                        <button class="btn btn-secondary mark-read" data-notification-id="<?php echo $notification['id']; ?>">
                            Mark as Read
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-notifications">No new notifications</p>
        <?php endif; ?>
    </div>
</div>

<style>
.notifications-container {
    margin-top: 2rem;
}

.notification-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1rem;
    padding: 1.5rem;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.notification-time {
    color: #6c757d;
    font-size: 0.9rem;
}

.notification-body {
    margin-bottom: 1rem;
}

.order-details {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 4px;
    margin-top: 1rem;
}

.notification-actions {
    display: flex;
    gap: 1rem;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.no-notifications {
    text-align: center;
    color: #6c757d;
    padding: 2rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle mark as read
    const markReadButtons = document.querySelectorAll('.mark-read');
    markReadButtons.forEach(button => {
        button.addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;
            markNotificationAsRead(notificationId);
        });
    });

    // Handle view order
    const viewOrderButtons = document.querySelectorAll('.view-order');
    viewOrderButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            window.location.href = `view_order.php?id=${orderId}`;
        });
    });

    // Function to mark notification as read
    function markNotificationAsRead(notificationId) {
        fetch('mark_notification_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ notification_id: notificationId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove notification card
                const notificationCard = document.querySelector(`[data-notification-id="${notificationId}"]`);
                notificationCard.remove();

                // Check if there are any notifications left
                const remainingNotifications = document.querySelectorAll('.notification-card');
                if (remainingNotifications.length === 0) {
                    const container = document.querySelector('.notifications-container');
                    container.innerHTML = '<p class="no-notifications">No new notifications</p>';
                }
            } else {
                alert('Failed to mark notification as read');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while marking the notification as read');
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?> 