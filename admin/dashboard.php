<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

$page_title = 'Admin Dashboard';
require_once '../includes/db_config.php';

// Fetch dashboard statistics
// Total Products
$sql_products = "SELECT COUNT(*) AS total_products FROM products";
$result_products = $conn->query($sql_products);
$total_products = $result_products->fetch_assoc()['total_products'];

// Total Users (excluding admin)
$sql_users = "SELECT COUNT(*) AS total_users FROM users WHERE is_admin = 0";
$result_users = $conn->query($sql_users);
$total_users = $result_users->fetch_assoc()['total_users'];

// Recent Orders (e.g., last 7 days)
$sql_recent_orders = "SELECT COUNT(*) AS recent_orders FROM orders WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
$result_recent_orders = $conn->query($sql_recent_orders);
$recent_orders = $result_recent_orders->fetch_assoc()['recent_orders'];

// Fetch latest orders with customer info
$sql_latest_orders = "SELECT o.*, u.username, u.email 
                     FROM orders o 
                     JOIN users u ON o.user_id = u.user_id 
                     ORDER BY o.created_at DESC 
                     LIMIT 5";
$result_latest_orders = $conn->query($sql_latest_orders);

// Close database connection
$conn->close();

// Include header for admin section
require_once 'includes/admin_header.php';
?>

<!-- Add admin CSS -->
<!-- The CSS link is now included in admin_header.php -->

<div class="admin-dashboard">
    <!-- Admin header is now included from admin_header.php -->

    <div class="container">
        <div class="dashboard-welcome">
            <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p>Here's an overview of your store's performance.</p>
        </div>

        <div class="admin-stats">
            <div class="stat-card">
                <h3>Total Products</h3>
                <div class="number"><?php echo $total_products; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="number"><?php echo $total_users; ?></div>
            </div>
            <div class="stat-card">
                <h3>Recent Orders</h3>
                <div class="number"><?php echo $recent_orders; ?></div>
            </div>
        </div>

        <div class="recent-orders">
            <h3>Latest Orders</h3>
            <div class="orders-table">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $result_latest_orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td>
                                <div class="customer-info">
                                    <span class="username"><?php echo htmlspecialchars($order['username']); ?></span>
                                    <span class="email"><?php echo htmlspecialchars($order['email']); ?></span>
                                </div>
                            </td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="order_details.php?id=<?php echo $order['order_id']; ?>" class="btn-admin btn-sm">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-content">
            <div class="admin-actions">
                <a href="products.php" class="btn-admin btn-primary">
                    <i class="fas fa-box"></i> Manage Products
                </a>
                <a href="users.php" class="btn-admin btn-success">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <a href="../index.php" class="btn-admin">
                    <i class="fas fa-store"></i> View Website
                </a>
                <a href="../logout.php" class="btn-admin btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>

            <div class="admin-quick-links">
                <h3>Quick Actions</h3>
                <div class="admin-actions">
                    <a href="products.php?action=add" class="btn-admin btn-primary">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                    <a href="users.php?action=add" class="btn-admin btn-success">
                        <i class="fas fa-user-plus"></i> Add New User
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.recent-orders {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.orders-table {
    margin-top: 15px;
    overflow-x: auto;
}

.orders-table table {
    width: 100%;
    border-collapse: collapse;
}

.orders-table th,
.orders-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.orders-table th {
    background: #f8f9fa;
    font-weight: bold;
}

.customer-info {
    display: flex;
    flex-direction: column;
}

.customer-info .username {
    font-weight: bold;
}

.customer-info .email {
    font-size: 0.9em;
    color: #666;
}

.status-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.9em;
    font-weight: bold;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-processing { background: #cce5ff; color: #004085; }
.status-shipped { background: #d4edda; color: #155724; }
.status-delivered { background: #d1e7dd; color: #0f5132; }
.status-cancelled { background: #f8d7da; color: #721c24; }

.btn-sm {
    padding: 5px 10px;
    font-size: 0.9em;
}
</style>

<?php
// Include footer for admin section
require_once 'includes/admin_footer.php';
?> 