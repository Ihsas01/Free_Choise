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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="<?php echo (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) ? 'admin-logged-in' : ''; ?>">

<style>
:root {
    --header-bg: rgba(255, 255, 255, 0.98);
    --header-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
    --nav-link-color: #374151;
    --nav-link-hover: #667eea;
    --nav-link-active: #667eea;
    --nav-link-bg-active: rgba(102, 126, 234, 0.08);
    --nav-link-bg-hover: rgba(102, 126, 234, 0.05);
    --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --search-bg: #f8fafc;
    --search-border: #e2e8f0;
    --search-focus: #667eea;
    --btn-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
    --btn-shadow-strong: 0 4px 16px rgba(102, 126, 234, 0.25);
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-secondary: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.main-header {
    background: var(--header-bg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: var(--header-shadow);
    border-bottom: 1px solid rgba(226, 232, 240, 0.8);
    height: 65px;
    display: flex;
    align-items: center;
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 1000;
    transition: var(--transition-smooth);
}

.main-header.scrolled {
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
}

.main-header.hidden {
    transform: translateY(-100%);
}

.nav-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
    height: 65px;
}

.site-title {
    font-size: 1.4rem;
    font-weight: 900;
    letter-spacing: -0.02em;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-fill-color: transparent;
    margin-right: 2rem;
    text-decoration: none;
    transition: var(--transition-smooth);
    position: relative;
}

.site-title::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--gradient-primary);
    transition: var(--transition-smooth);
    border-radius: 1px;
}

.site-title:hover::after {
    width: 100%;
}

.site-title:hover {
    transform: scale(1.02);
}

.nav-list {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    list-style: none;
    margin: 0;
    padding: 0;
    flex: 1;
    justify-content: center;
}

.nav-list a:not(.site-title) {
    color: var(--nav-link-color);
    font-weight: 500;
    font-size: 0.85rem;
    text-decoration: none;
    padding: 0.5rem 0.8rem;
    border-radius: 8px;
    background: transparent;
    transition: var(--transition-smooth);
    position: relative;
    letter-spacing: 0.01em;
    white-space: nowrap;
}

.nav-list a:not(.site-title)::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: var(--gradient-primary);
    transition: var(--transition-smooth);
    transform: translateX(-50%);
    border-radius: 1px;
}

.nav-list a:not(.site-title):hover {
    background: var(--nav-link-bg-hover);
    color: var(--nav-link-hover);
    transform: translateY(-1px);
}

.nav-list a:not(.site-title):hover::before {
    width: 60%;
}

.nav-list a.active {
    color: var(--nav-link-active);
    background: var(--nav-link-bg-active);
    font-weight: 600;
}

.nav-list a.active::before {
    width: 60%;
}

.search-container {
    margin: 0 1.5rem;
    position: relative;
    flex: 0 1 280px;
    max-width: 320px;
    min-width: 200px;
    display: flex;
    align-items: center;
}

.search-input {
    width: 100%;
    padding: 0.6rem 1rem 0.6rem 2.2rem;
    border: 1.5px solid var(--search-border);
    border-radius: 20px;
    background: var(--search-bg);
    font-size: 0.85rem;
    transition: var(--transition-smooth);
    outline: none;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.search-input:focus {
    border-color: var(--search-focus);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    transform: scale(1.02);
}

.search-icon {
    position: absolute;
    left: 0.8rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 0.9rem;
    pointer-events: none;
    transition: var(--transition-smooth);
}

.search-input:focus + .search-icon {
    color: var(--search-focus);
}

.nav-right {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.btn {
    padding: 0.5rem 1.2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.8rem;
    border: none;
    outline: none;
    cursor: pointer;
    transition: var(--transition-smooth);
    box-shadow: var(--btn-shadow);
    letter-spacing: 0.02em;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.login-btn {
    background: #fff;
    color: #374151;
    border: 1.5px solid #e5e7eb;
}

.login-btn:hover {
    background: #f9fafb;
    color: #667eea;
    border-color: #d1d5db;
    transform: translateY(-1px);
    box-shadow: var(--btn-shadow-strong);
}

.register-btn {
    background: var(--gradient-primary);
    color: #fff;
    border: none;
    box-shadow: var(--btn-shadow-strong);
}

.register-btn:hover {
    background: var(--gradient-secondary);
    color: #fff;
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
}

.user-menu {
    position: relative;
    display: flex;
    align-items: center;
}

.profile-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.2rem;
    height: 2.2rem;
    background: var(--gradient-primary);
    color: #fff;
    border-radius: 50%;
    text-decoration: none;
    transition: var(--transition-smooth);
    font-size: 0.9rem;
    box-shadow: var(--btn-shadow);
}

.profile-icon:hover {
    transform: scale(1.1);
    box-shadow: var(--btn-shadow-strong);
}

.user-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    padding: 0.5rem;
    min-width: 180px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: var(--transition-smooth);
    border: 1px solid #f3f4f6;
    z-index: 1001;
}

.user-menu:hover .user-dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.user-dropdown a {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.6rem 0.8rem;
    color: #374151;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
    border-radius: 8px;
    transition: var(--transition-smooth);
}

.user-dropdown a:hover {
    background: #f9fafb;
    color: #667eea;
    transform: translateX(3px);
}

.cart-icon {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.2rem;
    height: 2.2rem;
    background: #fef3c7;
    color: #d97706;
    border-radius: 50%;
    text-decoration: none;
    transition: var(--transition-smooth);
    font-size: 0.9rem;
    box-shadow: 0 2px 8px rgba(217, 119, 6, 0.2);
}

.cart-icon:hover {
    background: #fde68a;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.3);
}

.cart-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ef4444;
    color: #fff;
    border-radius: 50%;
    width: 1.2rem;
    height: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 700;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.mobile-menu-toggle {
    display: none;
    flex-direction: column;
    gap: 3px;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 8px;
    transition: var(--transition-smooth);
}

.mobile-menu-toggle span {
    width: 20px;
    height: 2px;
    background: #374151;
    transition: var(--transition-smooth);
    border-radius: 1px;
}

.mobile-menu-toggle:hover span {
    background: #667eea;
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
    .nav-container { padding: 0 1.5rem; }
    .nav-list { gap: 0.6rem; }
    .search-container { margin: 0 1rem; flex: 0 1 240px; }
    .nav-list a:not(.site-title) { font-size: 0.8rem; padding: 0.4rem 0.6rem; }
}

@media (max-width: 768px) {
    .main-header, .nav-container { height: 60px; }
    .site-title { font-size: 1.2rem; margin-right: 1rem; }
    .search-container { display: none; }
    .nav-list { 
        position: fixed;
        top: 60px;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        flex-direction: column;
        padding: 1rem;
        gap: 0.5rem;
        transform: translateY(-100%);
        opacity: 0;
        visibility: hidden;
        transition: var(--transition-smooth);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        border-bottom: 1px solid #f3f4f6;
    }
    
    .nav-list.active {
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
    }
    
    .nav-list a:not(.site-title) {
        width: 100%;
        text-align: center;
        padding: 0.8rem;
        border-radius: 8px;
    }
    
    .mobile-menu-toggle { display: flex; }
    .btn { padding: 0.4rem 1rem; font-size: 0.75rem; }
}

@media (max-width: 480px) {
    .nav-container { padding: 0 1rem; }
    .site-title { font-size: 1.1rem; }
    .btn { padding: 0.3rem 0.8rem; font-size: 0.7rem; }
    .profile-icon, .cart-icon { width: 2rem; height: 2rem; font-size: 0.8rem; }
}

/* Animations */
@keyframes fadeInDown {
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
    animation: fadeInDown 0.6s ease-out;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.cart-count {
    animation: pulse 2s infinite;
}

/* Smooth transitions for all interactive elements */
.nav-list a, .btn, .profile-icon, .cart-icon, .search-input {
    will-change: transform, box-shadow, background-color;
}
</style>

<header class="main-header">
    <div class="nav-container">
        <ul class="nav-list">
            <li><a href="index.php" class="site-title">FREE CHOISE</a></li>
            <li><a href="index.php" <?php echo $current_page == 'index.php' ? 'class="active"' : ''; ?>><i class="fas fa-home"></i> Home</a></li>
            <li><a href="categories.php" <?php echo $current_page == 'categories.php' ? 'class="active"' : ''; ?>><i class="fas fa-th-large"></i> Categories</a></li>
            <li><a href="special-offers.php" <?php echo $current_page == 'special-offers.php' ? 'class="active"' : ''; ?>><i class="fas fa-tags"></i> Offers</a></li>
            <li><a href="about.php" <?php echo $current_page == 'about.php' ? 'class="active"' : ''; ?>><i class="fas fa-info-circle"></i> About</a></li>
            <li><a href="contact.php" <?php echo $current_page == 'contact.php' ? 'class="active"' : ''; ?>><i class="fas fa-envelope"></i> Contact</a></li>
            <li><a href="faq.php" <?php echo $current_page == 'faq.php' ? 'class="active"' : ''; ?>><i class="fas fa-question-circle"></i> FAQ</a></li>
            <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <li><a href="admin/dashboard.php" <?php echo strpos($current_page, 'admin') !== false ? 'class="active"' : ''; ?>><i class="fas fa-cog"></i> Dashboard</a></li>
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
                    <?php if($cart_count > 0): ?>
                        <span class="cart-count"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            <?php else: ?>
                <a href="login.php" class="btn login-btn"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="register.php" class="btn register-btn"><i class="fas fa-user-plus"></i> Register</a>
            <?php endif; ?>
        </div>
        
        <div class="mobile-menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</header>

<main class="main-content" style="margin-top: 65px;">
    <div class="container">
        <?php if(isset($message)): ?>
            <div class="message <?php echo $success ? 'success-message' : 'error-message'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

<script>
// Header scroll behavior with improved performance
let lastScrollTop = 0;
const header = document.querySelector('.main-header');
let ticking = false;

function updateHeader() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    if (scrollTop > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
    
    if (scrollTop > lastScrollTop && scrollTop > 100) {
        header.classList.add('hidden');
    } else {
        header.classList.remove('hidden');
    }
    
    lastScrollTop = scrollTop;
    ticking = false;
}

window.addEventListener('scroll', () => {
    if (!ticking) {
        requestAnimationFrame(updateHeader);
        ticking = true;
    }
});

// Mobile menu toggle with smooth animations
const mobileToggle = document.querySelector('.mobile-menu-toggle');
const navList = document.querySelector('.nav-list');

mobileToggle.addEventListener('click', () => {
    mobileToggle.classList.toggle('active');
    navList.classList.toggle('active');
    
    // Add stagger animation to nav items
    const navItems = navList.querySelectorAll('a:not(.site-title)');
    navItems.forEach((item, index) => {
        if (navList.classList.contains('active')) {
            item.style.animationDelay = `${index * 0.1}s`;
            item.style.animation = 'fadeInDown 0.5s ease-out forwards';
        } else {
            item.style.animation = '';
        }
    });
});

// Enhanced search functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    let searchTimeout;
    
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();
        
        searchTimeout = setTimeout(() => {
            if (query.length > 2) {
                // Add search functionality here
                console.log('Searching for:', query);
            }
        }, 300);
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
    
    // Add focus effects
    searchInput.addEventListener('focus', () => {
        searchInput.parentElement.style.transform = 'scale(1.02)';
    });
    
    searchInput.addEventListener('blur', () => {
        searchInput.parentElement.style.transform = 'scale(1)';
    });
}

// Enhanced notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        font-weight: 500;
        transform: translateX(100%);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        max-width: 300px;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 4000);
}

// Enhanced button click animations
document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        // Create ripple effect
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        `;
        
        this.style.position = 'relative';
        this.style.overflow = 'hidden';
        this.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

// Add ripple animation
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

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

// Intersection Observer for smooth animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements for animation
document.querySelectorAll('.nav-list a, .btn, .search-container').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
    observer.observe(el);
});
</script>
</main>
</body>
</html>