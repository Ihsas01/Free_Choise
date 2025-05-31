<?php
session_start(); // Ensure session is started
require_once 'includes/db_config.php'; // Include database configuration
require_once 'includes/header.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle profile update
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $update_query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone, $address, $user_id);
    
    if($update_stmt->execute()) {
        $message = 'Profile updated successfully';
    } else {
        $message = 'Error updating profile';
    }
}

// Get user details
$user_query = "SELECT * FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

// Get order history
$orders_query = "SELECT o.*, COUNT(oi.order_item_id) as item_count 
                FROM orders o 
                LEFT JOIN order_items oi ON o.order_id = oi.order_id 
                WHERE o.user_id = ? 
                GROUP BY o.order_id 
                ORDER BY o.created_at DESC";
$orders_stmt = $conn->prepare($orders_query);
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
?>

<div class="container">
    <h2>My Profile</h2>
    <?php if($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="profile-content">
        <div class="profile-section">
            <h3>Profile Information</h3>
            <form method="POST" action="" class="profile-form">
                <input type="hidden" name="action" value="update_profile">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" value="<?php echo $user['username']; ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo $user['first_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo $user['last_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo $user['phone']; ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?php echo $user['address']; ?></textarea>
                </div>
                <button type="submit" class="btn">Update Profile</button>
            </form>
            <div class="logout-section" style="margin-top: 30px;">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <div class="order-history-section">
            <h3>Order History</h3>
            <?php if($orders_result->num_rows > 0): ?>
                <div class="orders-list">
                    <?php while($order = $orders_result->fetch_assoc()): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <h4>Order #<?php echo $order['order_id']; ?></h4>
                                <span class="order-date"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                            </div>
                            <div class="order-details">
                                <p>Items: <?php echo $order['item_count']; ?></p>
                                <p>Total: $<?php echo number_format($order['total_amount'], 2); ?></p>
                                <p>Status: <span class="status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></p>
                            </div>
                            <a href="order-details.php?id=<?php echo $order['order_id']; ?>" class="btn btn-secondary">View Details</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-orders">You haven't placed any orders yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 