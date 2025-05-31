<?php
// No database connection or session_start() here. Handled in the main script.

// Get the current page name (optional, for active link highlighting)
$current_page = basename($_SERVER['PHP_SELF']);

// Calculate cart count if user is logged in
if (isset($_SESSION['user_id']) && isset($conn)) {
    $cart_count_query = "SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?";
    $cart_count_stmt = $conn->prepare($cart_count_query);
    $cart_count_stmt->bind_param("i", $_SESSION['user_id']);
    $cart_count_stmt->execute();
    $cart_count_result = $cart_count_stmt->get_result();
    $cart_count_row = $cart_count_result->fetch_assoc();
    $cart_count = (int)($cart_count_row['total_items'] ?? 0);
} else {
    $cart_count = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FREE CHOISE<?php echo isset($page_title) ? ' - ' . $page_title : ''; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Add Font Awesome or other icon library if needed -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="<?php echo (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) ? 'admin-logged-in' : ''; ?>">

<header class="main-header">
    <div class="container">
        <div class="nav-container">
            <ul class="nav-list">
                <li><a href="index.php" class="site-title">FREE_CHOISE</a></li>
                <li><a href="index.php">Home</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="special-offers.php">Offers</a></li>
                <li><a href="about.php">About_Us</a></li>
                <li><a href="contact.php">Contact_Us</a></li>
                <li><a href="faq.php">FAQ</a></li>
                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <li><a href="admin/dashboard.php">Dashboard</a></li>
                <?php endif; ?>
            </ul>
            <div class="nav-right">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="profile-icon"><i class="fas fa-user"></i> </a>
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <?php
                         // Use the $cart_count variable set in the including script
                         // Database connection and cart count fetching should happen in the main script
                         // and the result passed to the header.
                        ?>
                         <span class="cart-count"><?php echo $cart_count; ?></span>
                    </a>
                    <?php if (!in_array($current_page, ['about.php', 'contact.php', 'faq.php'])): ?>
                        <a href="logout.php" class="btn">Logout</a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if(!isset($_SESSION['user_id']) || (isset($_SESSION['user_id']) && in_array($current_page, ['about.php', 'contact.php', 'faq.php']))): ?>
                    <a href="login.php" class="btn login-btn">Login</a>
                    <a href="register.php" class="btn register-btn">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<main class="main-content">
    <div class="container">
        <?php if(isset($message)): ?>
            <div class="message <?php echo $success ? 'success-message' : 'error-message'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>