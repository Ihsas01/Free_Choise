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
:root {
    --header-bg: rgba(255, 255, 255, 0.96);
    --header-shadow: 0 4px 24px rgba(102, 126, 234, 0.08);
    --nav-link-color: #2d3748;
    --nav-link-hover: #5a67d8;
    --nav-link-active: #5a67d8;
    --nav-link-bg-active: rgba(102, 126, 234, 0.10);
    --nav-link-bg-hover: rgba(102, 126, 234, 0.07);
    --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --search-bg: #f7f9fa;
    --search-border: #d1d9e6;
    --search-focus: #a3bffa;
    --btn-shadow: 0 2px 12px rgba(102, 126, 234, 0.10);
    --btn-shadow-strong: 0 4px 24px rgba(102, 126, 234, 0.18);
}

.main-header {
    background: var(--header-bg);
    box-shadow: var(--header-shadow);
    border-bottom: 1px solid #f0f4fa;
    height: 80px;
    display: flex;
    align-items: center;
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 1000;
    transition: var(--transition-smooth);
}

.nav-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2.5rem;
    height: 80px;
}

.site-title {
    font-size: 2rem;
    font-weight: 900;
    letter-spacing: -0.03em;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-fill-color: transparent;
    margin-right: 2.5rem;
    text-decoration: none;
    transition: var(--transition-smooth);
}
.site-title:hover {
    filter: brightness(1.1) drop-shadow(0 2px 8px #667eea22);
}

.nav-list {
    display: flex;
    align-items: center;
    gap: 2.2rem;
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-list a:not(.site-title) {
    color: var(--nav-link-color);
    font-weight: 600;
    font-size: 1.05rem;
    text-decoration: none;
    padding: 0.7rem 1.3rem;
    border-radius: 14px;
    background: transparent;
    transition: var(--transition-smooth);
    position: relative;
    box-shadow: none;
    letter-spacing: 0.02em;
}

.nav-list a:not(.site-title):hover {
    background: var(--nav-link-bg-hover);
    color: var(--nav-link-hover);
    box-shadow: 0 2px 8px #667eea11;
    transform: translateY(-2px) scale(1.04);
}

.nav-list a.active {
    color: var(--nav-link-active);
    background: var(--nav-link-bg-active);
    box-shadow: 0 4px 16px #667eea18;
    font-weight: 700;
}

.search-container {
    margin: 0 2rem;
    position: relative;
    flex: 1 1 320px;
    max-width: 350px;
    min-width: 220px;
    display: flex;
    align-items: center;
}
.search-input {
    width: 100%;
    padding: 0.8rem 1.1rem 0.8rem 2.5rem;
    border: 2px solid var(--search-border);
    border-radius: 25px;
    background: var(--search-bg);
    font-size: 1rem;
    transition: var(--transition-smooth);
    outline: none;
    box-shadow: var(--btn-shadow);
}
.search-input:focus {
    border-color: var(--search-focus);
    background: #fff;
    box-shadow: 0 0 0 3px #a3bffa33;
}
.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
    font-size: 1.1rem;
    pointer-events: none;
}

.nav-right {
    display: flex;
    align-items: center;
    gap: 1.1rem;
}

.btn {
    padding: 0.8rem 1.7rem;
    border-radius: 14px;
    font-weight: 700;
    font-size: 1rem;
    border: none;
    outline: none;
    cursor: pointer;
    transition: var(--transition-smooth);
    box-shadow: var(--btn-shadow);
    letter-spacing: 0.04em;
}
.login-btn {
    background: #fff;
    color: #2d3748;
    border: 2px solid #f093fb22;
}
.login-btn:hover {
    background: #f7f9fa;
    color: #764ba2;
    box-shadow: var(--btn-shadow-strong);
}
.register-btn {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border: none;
    box-shadow: var(--btn-shadow-strong);
}
.register-btn:hover {
    background: linear-gradient(90deg, #764ba2 0%, #667eea 100%);
    color: #fff;
    transform: scale(1.04);
}

@media (max-width: 1024px) {
    .nav-container { padding: 0 1.2rem; }
    .nav-list { gap: 1.2rem; }
    .search-container { margin: 0 0.7rem; }
}
@media (max-width: 768px) {
    .main-header, .nav-container { height: 64px; }
    .site-title { font-size: 1.3rem; margin-right: 1rem; }
    .search-container { display: none; }
    .nav-list { gap: 0.5rem; }
    .btn { padding: 0.6rem 1.1rem; font-size: 0.9rem; }
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
        
        <!-- Search Bar -->
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search products..." id="searchInput">
        </div>
        
        <div class="nav-right">
            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="user-menu">
                    <a href="profile.php" class="profile-icon" title="Profile">
                        <i class="fas fa-user"></i>
                    </a>
                    <div class="user-dropdown">
                        <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                        <a href="order-details.php"><i class="fas fa-shopping-bag"></i> My Orders</a>
                        <a href="chat.php"><i class="fas fa-comments"></i> Support Chat</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
                <a href="cart.php" class="cart-icon" title="Cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                </a>
            <?php else: ?>
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

<script>
// Header scroll behavior
let lastScrollTop = 0;
const header = document.querySelector('.main-header');

window.addEventListener('scroll', () => {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    if (scrollTop > 100) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
    
    if (scrollTop > lastScrollTop && scrollTop > 200) {
        header.classList.add('hidden');
    } else {
        header.classList.remove('hidden');
    }
    
    lastScrollTop = scrollTop;
});

// Mobile menu toggle
const mobileToggle = document.querySelector('.mobile-menu-toggle');
const navList = document.querySelector('.nav-list');

mobileToggle.addEventListener('click', () => {
    mobileToggle.classList.toggle('active');
    navList.classList.toggle('active');
});

// Search functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        if (query.length > 2) {
            // Add search functionality here
            console.log('Searching for:', query);
        }
    });
    
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            const query = e.target.value.trim();
            if (query) {
                // Redirect to search results page
                window.location.href = `categories.php?search=${encodeURIComponent(query)}`;
            }
        }
    });
}

// Show notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Cart count animation
const cartCount = document.querySelector('.cart-count');
if (cartCount && parseInt(cartCount.textContent) > 0) {
    cartCount.style.animation = 'pulse 2s infinite';
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add loading animation to buttons
document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click', function() {
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.style.transform = '';
        }, 150);
    });
});

// Auto-hide mobile menu when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('.nav-list') && !e.target.closest('.mobile-menu-toggle')) {
        mobileToggle.classList.remove('active');
        navList.classList.remove('active');
    }
});

// Keyboard navigation support
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        mobileToggle.classList.remove('active');
        navList.classList.remove('active');
    }
});
</script>
</main>
</body>
</html>