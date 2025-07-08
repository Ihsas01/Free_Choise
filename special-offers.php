<?php
session_start(); // Start the session
require_once 'config/database.php'; // Include the database connection

// Get products with special offers
$offers_query = "SELECT p.*, c.category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.category_id 
                WHERE p.special_offer = 1 
                ORDER BY p.created_at DESC 
                LIMIT 12";
$offers_result = $conn->query($offers_query);

// Fetch cart count for header (if user is logged in)
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $cart_count_query = "SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?";
    $stmt_cart_count = $conn->prepare($cart_count_query);
    $stmt_cart_count->bind_param("i", $user_id);
    $stmt_cart_count->execute();
    $cart_count_result = $stmt_cart_count->get_result();
    $cart_count = $cart_count_result->fetch_assoc()['total_items'] ?? 0;
    $stmt_cart_count->close();
}

require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="offers-hero">
    <div class="hero-background">
        <div class="hero-overlay"></div>
        <div class="floating-elements">
            <div class="floating-element element-1">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="floating-element element-2">
                <i class="fas fa-gift"></i>
            </div>
            <div class="floating-element element-3">
                <i class="fas fa-truck"></i>
            </div>
            <div class="floating-element element-4">
                <i class="fas fa-star"></i>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title" data-aos="fade-up">Exclusive Special Offers</h1>
            <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="200">Discover incredible deals and amazing discounts on premium products</p>
            <div class="hero-stats" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-item">
                    <span class="stat-number">50%</span>
                    <span class="stat-label">Off</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">Free</span>
                    <span class="stat-label">Shipping</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Support</span>
                </div>
            </div>
            <div class="hero-cta" data-aos="fade-up" data-aos-delay="600">
                <a href="#featured-deals" class="btn btn-primary scroll-to-deals">View All Deals</a>
            </div>
        </div>
    </div>
</section>

<!-- Current Promotions Section -->
<section class="promotions-section" data-aos="fade-up">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title" data-aos="fade-up">Current Promotions</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="200">Limited time offers you don't want to miss</p>
        </div>
        
        <div class="promotions-grid">
            <div class="promotion-card" data-aos="zoom-in" data-aos-delay="100">
                <div class="card-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="card-content">
                    <h3>New Customer Discount</h3>
                    <p>Get 10% off on your first purchase! Exclusive offer for new customers.</p>
                    <div class="card-actions">
                        <a href="register.php" class="btn btn-outline">Sign Up Now</a>
                    </div>
                </div>
                <div class="card-badge">10% OFF</div>
            </div>

            <div class="promotion-card" data-aos="zoom-in" data-aos-delay="200">
                <div class="card-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="card-content">
                    <h3>Free Shipping</h3>
                    <p>Free shipping on all orders over $50. No hidden fees, no surprises.</p>
                    <div class="card-actions">
                        <a href="categories.php" class="btn btn-outline">Shop Now</a>
                    </div>
                </div>
                <div class="card-badge">FREE</div>
            </div>

            <div class="promotion-card" data-aos="zoom-in" data-aos-delay="300">
                <div class="card-icon">
                    <i class="fas fa-gift"></i>
                </div>
                <div class="card-content">
                    <h3>Buy One Get One</h3>
                    <p>Buy one item and get another at 50% off. Perfect for gifts!</p>
                    <div class="card-actions">
                        <a href="categories.php" class="btn btn-outline">View Products</a>
                    </div>
                </div>
                <div class="card-badge">BOGO</div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Deals Section -->
<section class="featured-deals-section" id="featured-deals" data-aos="fade-up">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title" data-aos="fade-up">Featured Deals</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-delay="200">Handpicked products with amazing discounts</p>
        </div>
        
        <div class="products-grid">
            <?php while($product = $offers_result->fetch_assoc()): ?>
                <div class="product-card" data-aos="fade-up" data-aos-delay="<?php echo rand(100, 500); ?>">
                    <div class="card-image-container">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                             class="product-image">
                        <div class="image-overlay">
                            <div class="overlay-actions">
                                <a href="product.php?id=<?php echo $product['product_id']; ?>" class="overlay-btn">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <form method="POST" action="cart.php" class="overlay-form">
                                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                        <input type="hidden" name="action" value="add">
                                        <button type="submit" class="overlay-btn">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="login.php" class="overlay-btn">
                                        <i class="fas fa-lock"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="discount-badge">
                            <?php 
                            $original_price_placeholder = $product['price'] * 1.5;
                            if ($original_price_placeholder > 0) {
                                $discount_percentage = round((($original_price_placeholder - $product['price']) / $original_price_placeholder) * 100);
                                echo $discount_percentage . '% OFF';
                            } else {
                                echo 'SALE';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="card-content">
                        <div class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                        <h3 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        
                        <div class="price-container">
                            <?php if ($original_price_placeholder > 0): ?>
                                <span class="original-price">Rs. <?php echo number_format($original_price_placeholder, 2); ?></span>
                            <?php endif; ?>
                            <span class="current-price">Rs. <?php echo number_format($product['price'], 2); ?></span>
                        </div>
                        
                        <div class="card-actions">
                            <a href="product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary">View Details</a>
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <form method="POST" action="cart.php" class="add-to-cart-form">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <input type="hidden" name="action" value="add">
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-secondary">
                                    <i class="fas fa-lock"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section" data-aos="fade-up">
    <div class="container">
        <div class="newsletter-content">
            <div class="newsletter-text">
                <h2 data-aos="fade-up">Stay Updated</h2>
                <p data-aos="fade-up" data-aos-delay="200">Subscribe to our newsletter to receive exclusive offers and updates before anyone else!</p>
            </div>
            <div class="newsletter-form" data-aos="fade-up" data-aos-delay="400">
                <form method="POST" action="">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Enter your email address" required>
                        <button type="submit" class="btn btn-primary">
                            <span class="btn-text">Subscribe</span>
                            <span class="btn-icon">
                                <i class="fas fa-paper-plane"></i>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Terms Section -->
<section class="terms-section" data-aos="fade-up">
    <div class="container">
        <div class="terms-content">
            <h2 data-aos="fade-up">Terms & Conditions</h2>
            <div class="terms-grid" data-aos="fade-up" data-aos-delay="200">
                <div class="term-item">
                    <i class="fas fa-clock"></i>
                    <h3>Limited Time</h3>
                    <p>All special offers and promotions are subject to availability and may be modified or discontinued at any time without notice.</p>
                </div>
                <div class="term-item">
                    <i class="fas fa-ban"></i>
                    <h3>No Combination</h3>
                    <p>Discounts cannot be combined with other offers. Only one promotion can be applied per order.</p>
                </div>
                <div class="term-item">
                    <i class="fas fa-shipping-fast"></i>
                    <h3>Shipping Terms</h3>
                    <p>Free shipping applies to standard delivery only. Express shipping may incur additional charges.</p>
                </div>
                <div class="term-item">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Exclusions</h3>
                    <p>Some products may be excluded from promotions. Please check individual product pages for details.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- AOS Library for animations -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
// Initialize AOS
AOS.init({
    duration: 1000,
    easing: 'ease-in-out',
    once: true,
    offset: 100
});

// Smooth scroll to deals
document.querySelector('.scroll-to-deals').addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelector('#featured-deals').scrollIntoView({
        behavior: 'smooth'
    });
});

// Product card hover effects
document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.querySelector('.image-overlay').style.opacity = '1';
    });
    
    card.addEventListener('mouseleave', function() {
        this.querySelector('.image-overlay').style.opacity = '0';
    });
});

// Newsletter form animation
document.querySelector('.newsletter-form button').addEventListener('click', function() {
    this.classList.add('sending');
    setTimeout(() => {
        this.classList.remove('sending');
    }, 2000);
});

// Floating elements animation
const floatingElements = document.querySelectorAll('.floating-element');
floatingElements.forEach((element, index) => {
    element.style.animationDelay = `${index * 0.5}s`;
});

// Always scroll to top on page load (including refresh)
window.onbeforeunload = function () {
    window.scrollTo(0, 0);
};
</script>

<?php 
// Close the database connection if it was opened and not closed
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
require_once 'includes/footer.php'; 
?>