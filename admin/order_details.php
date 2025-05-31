<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

$page_title = 'Order Details';
require_once '../includes/db_config.php';

// Get order ID from URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    header('Location: dashboard.php');
    exit();
}

// Fetch order details
$sql = "SELECT o.*, u.username, u.email, a.full_name, a.address, a.city, a.state, a.zip_code, a.phone 
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        JOIN addresses a ON o.address_id = a.address_id 
        WHERE o.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    header('Location: dashboard.php');
    exit();
}

// Fetch order items
$sql_items = "SELECT oi.*, p.product_name, p.image_url 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.product_id 
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();

// Include header for admin section
require_once 'includes/admin_header.php';
?>

<div class="admin-dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h2>Order Details #<?php echo $order_id; ?></h2>
            <a href="dashboard.php" class="btn-admin">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="order-details">
            <div class="order-info">
                <h3>Order Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Order Date:</label>
                        <span><?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Status:</label>
                        <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <label>Payment Method:</label>
                        <span><?php echo strtoupper($order['payment_method']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Total Amount:</label>
                        <span>Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>

            <div class="customer-info">
                <h3>Customer Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Username:</label>
                        <span><?php echo htmlspecialchars($order['username']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Email:</label>
                        <span><?php echo htmlspecialchars($order['email']); ?></span>
                    </div>
                </div>
            </div>

            <div class="shipping-info">
                <h3>Shipping Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Full Name:</label>
                        <span><?php echo htmlspecialchars($order['full_name']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Address:</label>
                        <span><?php echo htmlspecialchars($order['address']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>City:</label>
                        <span><?php echo htmlspecialchars($order['city']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>State:</label>
                        <span><?php echo htmlspecialchars($order['state']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>ZIP Code:</label>
                        <span><?php echo htmlspecialchars($order['zip_code']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Phone:</label>
                        <span><?php echo htmlspecialchars($order['phone']); ?></span>
                    </div>
                </div>
            </div>

            <div class="order-items">
                <h3>Order Items</h3>
                <div class="items-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = $items_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="product-info">
                                        <?php if ($item['image_url']): ?>
                                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-thumbnail">
                                        <?php endif; ?>
                                        <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                                    </div>
                                </td>
                                <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="order-actions">
                <h3>Order Actions</h3>
                <form action="update_order_status.php" method="POST" class="status-form">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                    <div class="form-group">
                        <label for="status">Update Status:</label>
                        <select name="status" id="status" class="form-control">
                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-admin btn-primary">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.order-details {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 15px 0;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-item label {
    font-weight: bold;
    color: #666;
    margin-bottom: 5px;
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

.items-table {
    margin-top: 20px;
    overflow-x: auto;
}

.items-table table {
    width: 100%;
    border-collapse: collapse;
}

.items-table th,
.items-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.items-table th {
    background: #f8f9fa;
    font-weight: bold;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.product-thumbnail {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
}

.order-actions {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
}

.status-form {
    display: flex;
    gap: 15px;
    align-items: flex-end;
}

.form-group {
    flex: 1;
}

.form-control {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
</style>

<?php
// Include footer for admin section
require_once 'includes/admin_footer.php';
?> 