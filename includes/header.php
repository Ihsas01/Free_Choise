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
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="<?php echo (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) ? 'admin-logged-in' : ''; ?>">

<style>
/* Modern Header Styles */
:root {
    --header-bg: rgba(255, 255, 255, 0.95);
    --header-border: rgba(255, 255, 255, 0.2);
    --header-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    --header-shadow-scrolled: 0 8px 32px rgba(0, 0, 0, 0.15);
    --nav-link-color: #2d3748;
    --nav-link-hover: #667eea;
    --nav-link-active: #4c51bf;
    --transition-smooth: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-fast: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.main-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background: var(--header-bg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--header-border);
    box-shadow: var(--header-shadow);
    transition: var(--transition-smooth);
    transform: translateY(0);
}

.main-header.scrolled {
    background: rgba(255, 255, 255, 0.98);
    box-shadow: var(--header-shadow-scrolled);
    transform: translateY(0);
}

.main-header.hidden {
    transform: translateY(-100%);
}

.nav-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    max-width: 1400px;
    margin: 0 auto;
    padding-left: 2rem;
    padding-right: 2rem;
}

/* Logo/Brand Styling */
.nav-list {
    list-style: none;
    display: flex;
    align-items: center;
    gap: 2rem;
    margin: 0;
    padding: 0;
}

.site-title {
    font-size: 1.8rem;
    font-weight: 800;
    color: #667eea;
    text-decoration: none;
    position: relative;
    transition: var(--transition-smooth);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -0.02em;
}

.site-title::before {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 0;
    height: 3px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transition: var(--transition-smooth);
    border-radius: 2px;
}

.site-title:hover::before {
    width: 100%;
}

.site-title:hover {
    transform: translateY(-2px);
}

/* Navigation Links */
.nav-list a:not(.site-title) {
    color: var(--nav-link-color);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95rem;
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    transition: var(--transition-smooth);
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.nav-list a:not(.site-title)::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
    transition: left 0.5s;
}

.nav-list a:not(.site-title):hover::before {
    left: 100%;
}

.nav-list a:not(.site-title):hover {
    color: var(--nav-link-hover);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.nav-list a.active {
    color: var(--nav-link-active);
    background: rgba(102, 126, 234, 0.1);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

/* Right Navigation Section */
.nav-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}

/* Icon Styling */
.profile-icon, .cart-icon {
    position: relative;
    color: var(--nav-link-color);
    text-decoration: none;
    font-size: 1.2rem;
    padding: 0.75rem;
    border-radius: 50%;
    transition: var(--transition-smooth);
    background: rgba(102, 126, 234, 0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
}

.profile-icon:hover, .cart-icon:hover {
    color: var(--nav-link-hover);
    background: rgba(102, 126, 234, 0.15);
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

/* Cart Count Badge */
.cart-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    min-width: 1.2rem;
    height: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
    box-shadow: 0 2px 8px rgba(245, 87, 108, 0.3);
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* Button Styling */
.nav-right .btn {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: var(--transition-smooth);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
    border: none;
    cursor: pointer;
}

.login-btn {
    color: var(--nav-link-color);
    background: rgba(102, 126, 234, 0.05);
    border: 2px solid rgba(102, 126, 234, 0.2);
}

.login-btn:hover {
    color: white;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: transparent;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.register-btn {
    color: white;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: 2px solid transparent;
}

.register-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

/* Mobile Menu Toggle */
.mobile-menu-toggle {
    display: none;
    flex-direction: column;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 8px;
    background: rgba(102, 126, 234, 0.05);
    transition: var(--transition-fast);
}

.mobile-menu-toggle:hover {
    background: rgba(102, 126, 234, 0.1);
}

.mobile-menu-toggle span {
    width: 25px;
    height: 3px;
    background: var(--nav-link-color);
    margin: 3px 0;
    transition: var(--transition-fast);
    border-radius: 2px;
}

.mobile-menu-toggle.active span:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}

.mobile-menu-toggle.active span:nth-child(2) {
    opacity: 0;
}

.mobile-menu-toggle.active span:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -6px);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .nav-container {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
    
    .nav-list {
        gap: 1.5rem;
    }
    
    .nav-list a:not(.site-title) {
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }
}

@media (max-width: 768px) {
    .nav-container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .mobile-menu-toggle {
        display: flex;
    }
    
    .nav-list {
        position: fixed;
        top: 100%;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        flex-direction: column;
        gap: 0;
        padding: 1rem 0;
        transform: translateY(-100%);
        opacity: 0;
        visibility: hidden;
        transition: var(--transition-smooth);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    .nav-list.active {
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
    }
    
    .nav-list a:not(.site-title) {
        width: 100%;
        text-align: center;
        padding: 1rem 2rem;
        border-radius: 0;
        border-bottom: 1px solid rgba(102, 126, 234, 0.1);
    }
    
    .nav-list a:not(.site-title):last-child {
        border-bottom: none;
    }
    
    .nav-right {
        gap: 0.5rem;
    }
    
    .nav-right .btn {
        padding: 0.6rem 1rem;
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    .site-title {
        font-size: 1.5rem;
    }
    
    .nav-right .btn {
        padding: 0.5rem 0.8rem;
        font-size: 0.75rem;
    }
    
    .profile-icon, .cart-icon {
        width: 2.2rem;
        height: 2.2rem;
        font-size: 1rem;
    }
}

/* Scroll-based animations */
.main-header {
    will-change: transform, background, box-shadow;
}

/* Add smooth scroll behavior */
html {
    scroll-behavior: smooth;
}

/* Loading animation for header */
@keyframes headerSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.main-header {
    animation: headerSlideIn 0.6s ease-out;
}
</style>

<header class="main-header">
    <div class="nav-container">
        <ul class="nav-list">
            <li><a href="index.php" class="site-title">FREE CHOISE</a></li>
            <li><a href="index.php" <?php echo $current_page == 'index.php' ? 'class="active"' : ''; ?>>Home</a></li>
            <li><a href="categories.php" <?php echo $current_page == 'categories.php' ? 'class="active"' : ''; ?>>Categories</a></li>
            <li><a href="special-offers.php" <?php echo $current_page == 'special-offers.php' ? 'class="active"' : ''; ?>>Offers</a></li>
            <li><a href="about.php" <?php echo $current_page == 'about.php' ? 'class="active"' : ''; ?>>About Us</a></li>
            <li><a href="contact.php" <?php echo $current_page == 'contact.php' ? 'class="active"' : ''; ?>>Contact Us</a></li>
            <li><a href="faq.php" <?php echo $current_page == 'faq.php' ? 'class="active"' : ''; ?>>FAQ</a></li>
            <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <li><a href="admin/dashboard.php" <?php echo strpos($current_page, 'admin') !== false ? 'class="active"' : ''; ?>>Dashboard</a></li>
            <?php endif; ?>
        </ul>
        
        <div class="nav-right">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="profile-icon" title="Profile">
                    <i class="fas fa-user"></i>
                </a>
                <a href="cart.php" class="cart-icon" title="Cart">
                    <i class="fas fa-shopping-cart"></i>
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
        
        <div class="mobile-menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</header>

<main class="main-content" style="margin-top: 80px;">
    <div class="container">
        <?php if(isset($message)): ?>
            <div class="message <?php echo $success ? 'success-message' : 'error-message'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>