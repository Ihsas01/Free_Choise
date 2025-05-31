<?php
session_start(); // Ensure session is started
require_once 'config/database.php'; // Include database configuration
require_once 'includes/header.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get order ID from URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    // Redirect if no valid order ID is provided
    header('Location: profile.php');
    exit();
}

// Fetch order details for the logged-in user
$sql = "SELECT o.*, a.full_name, a.address, a.city, a.state, a.zip_code, a.phone 
        FROM orders o 
        JOIN addresses a ON o.address_id = a.address_id 
        WHERE o.order_id = ? AND o.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    // Redirect if order is not found or does not belong to the user
    header('Location: profile.php');
    exit();
}

// Fetch order items for the order
$sql_items = "SELECT oi.*, p.product_name, p.image_url 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.product_id 
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();

?>

<div class="container">
    <h2>Order Details #<?php echo $order['order_id']; ?></h2>

    <div class="order-details-section">
        <div class="order-info">
            <h3>Order Information</h3>
            <p><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
            <p><strong>Status:</strong> <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span></p>
            <p><strong>Payment Method:</strong> <?php echo strtoupper($order['payment_method']); ?></p>
            <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
        </div>

        <div class="shipping-info">
            <h3>Shipping Information</h3>
            <p><?php echo htmlspecialchars($order['full_name']); ?></p>
            <p><?php echo htmlspecialchars($order['address']); ?></p>
            <p><?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> <?php echo htmlspecialchars($order['zip_code']); ?></p>
            <p>Phone: <?php echo htmlspecialchars($order['phone']); ?></p>
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
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="back-link" style="margin-top: 20px;">
            <a href="profile.php" class="btn btn-secondary">Back to Order History</a>
        </div>
    </div>
</div>

<style>
.order-details-section {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.order-info,
.shipping-info,
.order-items {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.order-info:last-child,
.shipping-info:last-child,
.order-items:last-child {
    border-bottom: none;
}

.order-details-section h3 {
    color: #333;
    margin-bottom: 15px;
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
    margin-top: 15px;
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

</style>

<?php require_once 'includes/footer.php'; ?> 