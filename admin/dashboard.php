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

// Total Revenue (last 30 days)
$sql_revenue = "SELECT SUM(total_amount) AS total_revenue FROM orders WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND status != 'cancelled'";
$result_revenue = $conn->query($sql_revenue);
$total_revenue = $result_revenue->fetch_assoc()['total_revenue'] ?? 0;

// Fetch latest orders with customer info
$sql_latest_orders = "SELECT o.*, u.username, u.email
                     FROM orders o
                     JOIN users u ON o.user_id = u.user_id 
                     ORDER BY o.created_at DESC
                     LIMIT 5";
$result_latest_orders = $conn->query($sql_latest_orders);

// Fetch recent activity (last 10 orders)
$sql_recent_activity = "SELECT o.*, u.username 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.user_id 
                       ORDER BY o.created_at DESC 
                       LIMIT 10";
$result_recent_activity = $conn->query($sql_recent_activity);

// Close database connection
$conn->close();

// Include header for admin section
require_once 'includes/admin_header.php';
?>

<!-- Modern Dashboard with Advanced Styling -->
<div class="admin-dashboard-modern">
    <!-- Hero Section with Parallax -->
    <div class="dashboard-hero">
        <div class="hero-background"></div>
        <div class="hero-content">
            <div class="welcome-section">
                <h1 class="hero-title">
                    <span class="greeting">Welcome back,</span>
                    <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                </h1>
                <p class="hero-subtitle">Here's your store's performance overview</p>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="stat-number"><?php echo $total_products; ?></span>
                        <span class="stat-label">Products</span>
                    </div>
                    <div class="hero-stat">
                        <span class="stat-number"><?php echo $total_users; ?></span>
                        <span class="stat-label">Customers</span>
                    </div>
                    <div class="hero-stat">
                        <span class="stat-number"><?php echo $recent_orders; ?></span>
                        <span class="stat-label">Recent Orders</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="dashboard-container">
        <!-- Statistics Cards with Animations -->
        <div class="stats-grid">
            <div class="stat-card modern" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Products</h3>
                    <div class="stat-number"><?php echo $total_products; ?></div>
                    <div class="stat-trend positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+12% this month</span>
                    </div>
                </div>
            </div>

            <div class="stat-card modern" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Users</h3>
                    <div class="stat-number"><?php echo $total_users; ?></div>
                    <div class="stat-trend positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+8% this month</span>
                    </div>
                </div>
            </div>

            <div class="stat-card modern" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                    <h3>Recent Orders</h3>
                    <div class="stat-number"><?php echo $recent_orders; ?></div>
                    <div class="stat-trend positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+15% this week</span>
                    </div>
                </div>
            </div>

            <div class="stat-card modern" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Revenue</h3>
                    <div class="stat-number">$<?php echo number_format($total_revenue, 2); ?></div>
                    <div class="stat-trend positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+23% this month</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions" data-aos="fade-up" data-aos-delay="500">
            <h2>Quick Actions</h2>
            <div class="actions-grid">
                <a href="products.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3>Manage Products</h3>
                    <p>Add, edit, or remove products from your store</p>
                </a>
                
                <a href="users.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Manage Users</h3>
                    <p>View and manage customer accounts</p>
                </a>
                
                <a href="notifications.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Notifications</h3>
                    <p>Check and manage system notifications</p>
                </a>
                
                <a href="special-offers.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <h3>Special Offers</h3>
                    <p>Create and manage promotional offers</p>
                </a>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="recent-orders-modern" data-aos="fade-up" data-aos-delay="600">
            <div class="section-header">
                <h2>Latest Orders</h2>
                <a href="orders.php" class="view-all-btn">View All Orders</a>
            </div>
            <div class="orders-table-modern">
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
                        <tr class="order-row" data-aos="fade-in">
                            <td>
                                <span class="order-id">#<?php echo $order['order_id']; ?></span>
                            </td>
                            <td>
                                <div class="customer-info-modern">
                                    <div class="customer-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="customer-details">
                                        <span class="username"><?php echo htmlspecialchars($order['username']); ?></span>
                                        <span class="email"><?php echo htmlspecialchars($order['email']); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="amount">$<?php echo number_format($order['total_amount'], 2); ?></span>
                            </td>
                            <td>
                                <span class="status-badge-modern status-<?php echo strtolower($order['status']); ?>">
                                    <i class="fas fa-circle"></i>
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="order-date"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></span>
                            </td>
                            <td>
                                <a href="order_details.php?id=<?php echo $order['order_id']; ?>" class="btn-view-details">
                                    <i class="fas fa-eye"></i>
                                    View
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="activity-feed" data-aos="fade-up" data-aos-delay="700">
            <h2>Recent Activity</h2>
            <div class="activity-list">
                <?php 
                $result_recent_activity->data_seek(0);
                while ($activity = $result_recent_activity->fetch_assoc()): 
                ?>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="activity-content">
                        <p><strong><?php echo htmlspecialchars($activity['username']); ?></strong> placed order #<?php echo $activity['order_id']; ?></p>
                        <span class="activity-time"><?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?></span>
                    </div>
                    <div class="activity-amount">
                        $<?php echo number_format($activity['total_amount'], 2); ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<!-- AOS Animation Library -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
// Initialize AOS animations
AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true
});

// Parallax effect for hero section
window.addEventListener('scroll', function() {
    const scrolled = window.pageYOffset;
    const heroBackground = document.querySelector('.hero-background');
    if (heroBackground) {
        heroBackground.style.transform = `translateY(${scrolled * 0.5}px)`;
    }
});

// Add smooth hover effects
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to stat cards
    const statCards = document.querySelectorAll('.stat-card.modern');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Add hover effects to action cards
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add hover effects to order rows
    const orderRows = document.querySelectorAll('.order-row');
    orderRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(52, 152, 219, 0.05)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = 'transparent';
        });
    });
});
</script>

<?php
// Include footer for admin section
require_once 'includes/admin_footer.php';
?> 