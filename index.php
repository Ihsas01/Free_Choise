<?php
session_start(); // Start the session
require_once 'config/database.php'; // Include the database connection
require_once 'includes/header.php';
require_once 'includes/ban_check.php'; // Include ban check functionality

// Get featured products
$featured_query = "SELECT p.*, c.category_name 
                  FROM products p 
                  JOIN categories c ON p.category_id = c.category_id 
                  ORDER BY p.created_at DESC 
                  LIMIT 8";
$featured_result = $conn->query($featured_query);

// Get all categories
$categories_query = "SELECT * FROM categories";
$categories_result = $conn->query($categories_query);

// Check if user is banned and display warning
$ban_warning = '';
if (isset($_SESSION['user_id'])) {
    $ban_info = isUserBanned($_SESSION['user_id']);
    if ($ban_info && $ban_info['banned']) {
        ob_start();
        displayBanWarning($ban_info);
        $ban_warning = ob_get_clean();
    }
}
?>

<style>
/* Modern CSS Variables */
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    --glass-bg: rgba(255, 255, 255, 0.1);
    --glass-border: rgba(255, 255, 255, 0.2);
    --shadow-soft: 0 8px 32px rgba(0, 0, 0, 0.1);
    --shadow-medium: 0 12px 40px rgba(0, 0, 0, 0.15);
    --shadow-strong: 0 20px 60px rgba(0, 0, 0, 0.2);
    --transition-smooth: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-fast: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --card-glass-bg: rgba(255,255,255,0.55);
    --card-glass-border: rgba(255,255,255,0.25);
    --card-shadow: 0 8px 32px rgba(102, 126, 234, 0.10);
    --card-shadow-hover: 0 16px 48px rgba(102, 126, 234, 0.18);
    --card-hover-scale: 1.045;
    --card-hover-brightness: 1.08;
    --category-badge-bg: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    --category-badge-color: #fff;
}

/* Global Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.6;
    color: #2d3748;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    overflow-x: hidden;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 30px;
}

/* Hero Section - Modern & Dynamic */
.hero-section {
    position: relative;
    min-height: 100vh;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-bottom: 0;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
    animation: float 20s ease-in-out infinite;
}

.hero-section::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
    animation: shimmer 3s ease-in-out infinite;
}

.hero-content {
    position: relative;
    z-index: 10;
    text-align: center;
    color: white;
    max-width: 800px;
    padding: 0 20px;
    opacity: 0;
    transform: translateY(50px);
    animation: heroFadeIn 1.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

.hero-content h1 {
    font-size: clamp(3rem, 8vw, 6rem);
    font-weight: 800;
    margin-bottom: 30px;
    background: linear-gradient(45deg, #fff, #f0f0f0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 0 30px rgba(255,255,255,0.3);
    letter-spacing: -0.02em;
    line-height: 1.1;
}

.hero-content p {
    font-size: clamp(1.2rem, 3vw, 1.8rem);
    margin-bottom: 50px;
    opacity: 0.9;
    font-weight: 300;
    line-height: 1.6;
}

.hero-btn {
    display: inline-block;
    padding: 18px 40px;
    background: var(--success-gradient);
    color: white;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: var(--transition-smooth);
    box-shadow: var(--shadow-medium);
    position: relative;
    overflow: hidden;
    transform: translateY(0);
}

.hero-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.6s;
}

.hero-btn:hover {
    transform: translateY(-8px) scale(1.05);
    box-shadow: var(--shadow-strong);
}

.hero-btn:hover::before {
    left: 100%;
}

/* Floating Elements */
.floating-shapes {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 1;
}

.shape {
    position: absolute;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

.shape:nth-child(1) {
    width: 80px;
    height: 80px;
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.shape:nth-child(2) {
    width: 120px;
    height: 120px;
    top: 60%;
    right: 10%;
    animation-delay: 2s;
}

.shape:nth-child(3) {
    width: 60px;
    height: 60px;
    bottom: 20%;
    left: 20%;
    animation-delay: 4s;
}

/* Sections Styling */
.section {
    padding: 120px 0;
    position: relative;
}

.section:nth-child(even) {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

.section-title {
    font-size: 2.2rem;
    font-weight: 800;
    margin: 60px 0 30px 0;
    text-align: center;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-fill-color: transparent;
    letter-spacing: -0.01em;
    opacity: 0;
    transform: translateY(40px);
    animation: fadeSlideIn 1.2s 0.2s cubic-bezier(0.4,0,0.2,1) forwards;
}

.section-title.active {
    opacity: 1;
    transform: translateY(0);
}

.section-title h2 {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 20px;
    position: relative;
}

.section-title h2::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: var(--primary-gradient);
    border-radius: 2px;
}

.section-title p {
    font-size: 1.2rem;
    color: #64748b;
    max-width: 600px;
    margin: 0 auto;
}

/* Categories Section */
.categories-section {
    background: white;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
    padding: 0 20px;
}

.category-card {
    background: white;
    border-radius: 20px;
    padding: 40px 30px;
    text-align: center;
    text-decoration: none;
    color: #2d3748;
    box-shadow: var(--shadow-soft);
    transition: var(--transition-smooth);
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(30px) scale(0.95);
}

.category-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--primary-gradient);
    opacity: 0;
    transition: var(--transition-smooth);
    z-index: 1;
}

.category-card:hover::before {
    opacity: 0.05;
}

.category-card.active {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.category-card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: var(--shadow-strong);
}

.category-card h3 {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 15px;
    color: #1a202c;
    position: relative;
    z-index: 2;
}

.category-card p {
    color: #64748b;
    font-size: 1rem;
    line-height: 1.6;
    position: relative;
    z-index: 2;
}

/* Featured Products Section */
.featured-products {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 40px;
    padding: 0 20px;
}

.product-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-soft);
    transition: var(--transition-smooth);
    position: relative;
    opacity: 0;
    transform: translateY(30px) scale(0.95);
}

.product-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--primary-gradient);
    opacity: 0;
    transition: var(--transition-smooth);
    z-index: 1;
}

.product-card:hover::before {
    opacity: 0.05;
}

.product-card.active {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.product-card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: var(--shadow-strong);
}

.product-image {
    width: 100%;
    height: 300px;
    object-fit: cover;
    transition: var(--transition-smooth);
    position: relative;
    z-index: 2;
}

.product-card:hover .product-image {
    transform: scale(1.1);
}

.product-info {
    padding: 30px;
    text-align: center;
    position: relative;
    z-index: 2;
}

.product-title {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: #1a202c;
}

.product-category {
    color: #64748b;
    font-size: 0.9rem;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-price {
    font-size: 1.8rem;
    color: #10b981;
    font-weight: 700;
    margin-bottom: 25px;
}

.product-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.product-actions .btn {
    padding: 12px 25px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition-fast);
    font-size: 0.9rem;
    border: none;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.product-actions .btn:first-child {
    background: var(--success-gradient);
    color: white;
}

.product-actions .btn:last-child {
    background: var(--accent-gradient);
    color: white;
}

.product-actions .btn:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: var(--shadow-medium);
}

/* Special Offers Section */
.special-offers {
    background: white;
}

.offers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 40px;
    padding: 0 20px;
}

.offer-card {
    background: var(--secondary-gradient);
    color: white;
    padding: 50px 40px;
    border-radius: 25px;
    text-align: center;
    box-shadow: var(--shadow-medium);
    transition: var(--transition-smooth);
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(30px) scale(0.95);
}

.offer-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
    transform: translateX(-100%);
    transition: transform 0.6s;
}

.offer-card:hover::before {
    transform: translateX(100%);
}

.offer-card.active {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.offer-card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: var(--shadow-strong);
}

.offer-card h3 {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 20px;
    position: relative;
    z-index: 2;
}

.offer-card p {
    font-size: 1.2rem;
    margin-bottom: 35px;
    opacity: 0.9;
    position: relative;
    z-index: 2;
}

.offer-card .btn {
    background: white;
    color: #f5576c;
    padding: 15px 35px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition-fast);
    position: relative;
    z-index: 2;
    display: inline-block;
}

.offer-card .btn:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: var(--shadow-medium);
}

/* Animations */
@keyframes heroFadeIn {
    0% {
        opacity: 0;
        transform: translateY(50px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 0 20px;
    }
    
    .hero-content h1 {
        font-size: clamp(2.5rem, 10vw, 4rem);
    }
    
    .hero-content p {
        font-size: clamp(1rem, 4vw, 1.4rem);
    }
    
    .section {
        padding: 80px 0;
    }
    
    .category-grid,
    .product-grid,
    .offers-grid {
        gap: 30px;
        padding: 0 10px;
    }
    
    .category-card,
    .product-card,
    .offer-card {
        padding: 30px 20px;
    }
    
    .offer-card h3 {
        font-size: 1.8rem;
    }
    
    .offer-card p {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .hero-content h1 {
        font-size: 2.5rem;
    }
    
    .hero-content p {
        font-size: 1.1rem;
    }
    
    .section-title h2 {
        font-size: 2rem;
    }
    
    .category-card h3 {
        font-size: 1.5rem;
    }
    
    .product-title {
        font-size: 1.2rem;
    }
    
    .product-price {
        font-size: 1.5rem;
    }
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Loading animation for images */
.product-image {
    opacity: 0;
    transition: opacity 0.6s ease-in-out;
}

.product-image.loaded {
    opacity: 1;
}

/* Stagger animation for grid items */
.stagger-item {
    opacity: 0;
    transform: translateY(30px);
    transition: var(--transition-smooth);
}

.stagger-item.active {
    opacity: 1;
    transform: translateY(0);
}

/* Parallax effect */
.parallax {
    transform: translateZ(0);
    will-change: transform;
}

/* Glass morphism effect */
.glass {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
}

/* Hover effects for interactive elements */
.interactive {
    transition: var(--transition-fast);
    cursor: pointer;
}

.interactive:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: var(--primary-gradient);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--secondary-gradient);
}

/* --- Product & Category Grid --- */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 2.2rem;
    margin-bottom: 60px;
    margin-top: 10px;
}

.card {
    background: var(--card-glass-bg);
    border: 1.5px solid var(--card-glass-border);
    border-radius: 22px;
    box-shadow: var(--card-shadow);
    padding: 2.2rem 1.5rem 1.5rem 1.5rem;
    position: relative;
    overflow: hidden;
    transition: var(--transition-smooth), box-shadow 0.3s;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    opacity: 0;
    transform: translateY(60px) scale(0.98);
    animation: fadeSlideIn 1.1s cubic-bezier(0.4,0,0.2,1) forwards;
}

.card:hover {
    box-shadow: var(--card-shadow-hover);
    transform: translateY(-8px) scale(var(--card-hover-scale));
    filter: brightness(var(--card-hover-brightness));
}

.card .category-badge {
    display: inline-block;
    background: var(--category-badge-bg);
    color: var(--category-badge-color);
    font-size: 0.85rem;
    font-weight: 700;
    border-radius: 8px;
    padding: 0.3rem 1rem;
    margin-bottom: 1.1rem;
    letter-spacing: 0.04em;
    box-shadow: 0 2px 8px #667eea22;
}

.card img {
    width: 100%;
    max-height: 180px;
    object-fit: cover;
    border-radius: 14px;
    margin-bottom: 1.2rem;
    box-shadow: 0 2px 12px #667eea11;
    transition: var(--transition-smooth);
}

.card h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.7rem;
    color: #2d3748;
}

.card p {
    font-size: 1rem;
    color: #4a5568;
    margin-bottom: 1.1rem;
    min-height: 48px;
}

.card .price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #764ba2;
    margin-bottom: 1.1rem;
}

.card .card-btn {
    display: inline-block;
    padding: 0.7rem 1.7rem;
    border-radius: 12px;
    background: var(--primary-gradient);
    color: #fff;
    font-weight: 700;
    font-size: 1rem;
    text-decoration: none;
    box-shadow: 0 2px 12px #667eea22;
    transition: var(--transition-smooth);
    border: none;
    outline: none;
    cursor: pointer;
    letter-spacing: 0.03em;
    position: relative;
    overflow: hidden;
}
.card .card-btn:hover {
    background: var(--secondary-gradient);
    transform: scale(1.05);
    box-shadow: 0 4px 24px #f093fb33;
}

/* --- Animations --- */
@keyframes fadeSlideIn {
    0% { opacity: 0; transform: translateY(60px) scale(0.98); }
    60% { opacity: 0.7; transform: translateY(-8px) scale(1.02); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}

/* Staggered animation for grid cards */
.card { animation-delay: var(--card-delay, 0s); }

/* --- Responsive --- */
@media (max-width: 900px) {
    .container { padding: 0 10px; }
    .grid { gap: 1.2rem; }
    .card { padding: 1.2rem 0.7rem 1rem 0.7rem; }
}
@media (max-width: 600px) {
    .hero-content h1 { font-size: 2.1rem; }
    .section-title { font-size: 1.3rem; }
    .card { padding: 1rem 0.5rem; }
}

/* --- Parallax effect for hero shapes --- */
.floating-shapes .shape {
    will-change: transform;
    transition: transform 0.5s cubic-bezier(0.4,0,0.2,1);
}
</style>

<!-- Hero Section with Floating Elements -->
<?php if ($ban_warning): ?>
    <div class="ban-warning-container" style="position: fixed; top: 80px; left: 0; right: 0; z-index: 1000; padding: 0 20px;">
        <?php echo $ban_warning; ?>
    </div>
<?php endif; ?>

<div class="hero-section">
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <div class="hero-content">
        <h1>WELCOME TO FREE CHOISE</h1>
        <p>Discover amazing products with unbeatable prices. Your ultimate shopping destination awaits.</p>
        <a href="categories.php" class="hero-btn">Explore Now</a>
    </div>
</div>

<!-- Categories Section -->
<section class="section categories-section">
    <div class="container">
        <div class="section-title">
            <h2>Shop by Category</h2>
            <p>Browse through our carefully curated categories to find exactly what you're looking for</p>
        </div>
        <div class="category-grid">
            <?php 
            $category_index = 0;
            while($category = $categories_result->fetch_assoc()): 
            ?>
                <a href="categories.php?category=<?php echo $category['category_id']; ?>" 
                   class="category-card stagger-item" 
                   style="transition-delay: <?php echo $category_index * 100; ?>ms;">
                    <h3><?php echo htmlspecialchars($category['category_name']); ?></h3>
                    <p><?php echo htmlspecialchars($category['description']); ?></p>
                </a>
            <?php 
            $category_index++;
            endwhile; 
            ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="section featured-products">
    <div class="container">
        <div class="section-title">
            <h2>Featured Products</h2>
            <p>Handpicked products that our customers love the most</p>
        </div>
        <div class="product-grid">
            <?php 
            $product_index = 0;
            while($product = $featured_result->fetch_assoc()): 
            ?>
                <div class="product-card stagger-item" 
                     style="transition-delay: <?php echo $product_index * 100; ?>ms;">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                         class="product-image"
                         loading="lazy">
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        <p class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></p>
                        <div class="product-actions">
                            <a href="product.php?id=<?php echo $product['product_id']; ?>" class="btn">View Details</a>
                            <?php if(isset($_SESSION['user_id']) && (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin'])): ?>
                                <form method="POST" action="cart.php" class="add-to-cart-form" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <input type="hidden" name="action" value="add">
                                    <button type="submit" class="btn">Add to Cart</button>
                                </form>
                            <?php else: ?>
                                <a href="login.php" class="btn">Login to Buy</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php 
            $product_index++;
            endwhile; 
            ?>
        </div>
    </div>
</section>

<!-- Special Offers Section -->
<section class="section special-offers">
    <div class="container">
        <div class="section-title">
            <h2>Special Offers</h2>
            <p>Don't miss out on these exclusive deals and discounts</p>
        </div>
        <div class="offers-grid">
            <div class="offer-card stagger-item" style="transition-delay: 0ms;">
                <h3>New Customer Discount</h3>
                <p>Get 10% off on your first purchase! Join thousands of satisfied customers.</p>
                <a href="register.php" class="btn">Sign Up Now</a>
            </div>
            <div class="offer-card stagger-item" style="transition-delay: 200ms;">
                <h3>Free Shipping</h3>
                <p>Enjoy free shipping on all orders over Rs. 500. Shop more, save more!</p>
                <a href="categories.php" class="btn">Shop Now</a>
            </div>
        </div>
    </div>
</section>

<!-- Add Chat Interface -->
<?php include 'chat.php'; ?>

<?php 
// Close the database connection if it was opened and not closed
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
require_once 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced Intersection Observer for scroll animations
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe all animated elements
    document.querySelectorAll('.stagger-item, .section-title').forEach(el => {
        observer.observe(el);
    });

    // Parallax effect for hero section
    const heroSection = document.querySelector('.hero-section');
    const shapes = document.querySelectorAll('.shape');
    
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;
        
        shapes.forEach((shape, index) => {
            const speed = 0.5 + (index * 0.1);
            shape.style.transform = `translateY(${rate * speed}px)`;
        });
    });

    // Image loading animation
    const images = document.querySelectorAll('.product-image');
    images.forEach(img => {
        img.addEventListener('load', function() {
            this.classList.add('loaded');
        });
        
        // If image is already loaded
        if (img.complete) {
            img.classList.add('loaded');
        }
    });

    // Enhanced hover effects for buttons
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Smooth reveal for sections with stagger effect
    const staggerReveal = (selector, delay = 100) => {
        const elements = document.querySelectorAll(selector);
        elements.forEach((el, index) => {
            setTimeout(() => {
                el.classList.add('active');
            }, index * delay);
        });
    };

    // Apply stagger effect when elements come into view
    const staggerObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const container = entry.target;
                const items = container.querySelectorAll('.stagger-item');
                items.forEach((item, index) => {
                    setTimeout(() => {
                        item.classList.add('active');
                    }, index * 150);
                });
                staggerObserver.unobserve(container);
            }
        });
    }, { threshold: 0.2 });

    document.querySelectorAll('.category-grid, .product-grid, .offers-grid').forEach(grid => {
        staggerObserver.observe(grid);
    });

    // Add floating animation to offer cards
    document.querySelectorAll('.offer-card').forEach((card, index) => {
        card.style.animationDelay = `${index * 0.5}s`;
        card.style.animation = 'float 6s ease-in-out infinite';
    });

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

    // Add glass morphism effect on scroll
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelectorAll('.parallax');
        
        parallax.forEach(element => {
            const speed = 0.5;
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });

    // Add pulse animation to important elements
    const pulseElements = document.querySelectorAll('.hero-btn, .offer-card .btn');
    pulseElements.forEach(el => {
        el.addEventListener('mouseenter', function() {
            this.style.animation = 'pulse 1s ease-in-out';
        });
        
        el.addEventListener('mouseleave', function() {
            this.style.animation = '';
        });
    });

    // Enhanced loading experience
    window.addEventListener('load', () => {
        document.body.classList.add('loaded');
        
        // Trigger initial animations
        setTimeout(() => {
            document.querySelectorAll('.section-title').forEach(title => {
                title.classList.add('active');
            });
        }, 500);
    });

    // Add interactive cursor effects
    document.querySelectorAll('.interactive').forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.cursor = 'pointer';
        });
    });

    // Performance optimization: Throttle scroll events
    let ticking = false;
    function updateOnScroll() {
        // Parallax and other scroll-based animations
        ticking = false;
    }

    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(updateOnScroll);
            ticking = true;
        }
    });

    // Parallax effect for floating shapes
    window.addEventListener('scroll', () => {
        const shapes = document.querySelectorAll('.floating-shapes .shape');
        const scrollY = window.scrollY;
        shapes.forEach((shape, i) => {
            shape.style.transform = `translateY(${scrollY * (0.08 + i*0.03)}px)`;
        });
    });

    // Staggered animation for grid cards
    window.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.grid .card').forEach((card, i) => {
            card.style.setProperty('--card-delay', `${0.12 * i + 0.2}s`);
        });
    });
});
</script> 