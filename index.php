<?php
session_start(); // Start the session
require_once 'config/database.php'; // Include the database connection
require_once 'includes/header.php';

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
?>

<style>
.hero-section {
    background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('images/hero-bg.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed; /* Add parallax effect */
    color: white;
    padding: 120px 20px; /* Increased padding */
    text-align: center;
    margin-bottom: 50px;
    position: relative; /* Needed for overlay effects if any */
    overflow: hidden; /* Hide overflow */
}

.hero-section::before { /* Optional: add subtle pattern or overlay */
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.05); /* Subtle white overlay */
    z-index: 1;
}

.hero-section .container {
    position: relative;
    z-index: 2; /* Ensure content is above overlay */
}

.hero-section h1 {
    font-size: 4em; /* Slightly larger font */
    margin-bottom: 25px;
    text-shadow: 3px 3px 6px rgba(0,0,0,0.7); /* Stronger shadow */
    font-weight: 700; /* Bolder font */
}

.hero-section p {
    font-size: 1.3em; /* Slightly larger font */
    margin-bottom: 40px;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
}

.hero-section .btn {
    background: linear-gradient(45deg, #4CAF50, #8bc34a); /* Gradient background */
    color: white;
    padding: 18px 40px; /* More padding */
    border-radius: 30px;
    text-decoration: none;
    font-weight: bold;
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease; /* Smoother transitions */
    font-size: 1.1em; /* Slightly larger font */
    display: inline-block; /* Allow fixed width */
    max-width: 250px; /* Use max-width instead of fixed width */
    width: 80%; /* Use a percentage width for flexibility */
    text-align: center; /* Center text */
}

.hero-section .btn:hover {
    transform: translateY(-5px); /* More pronounced lift */
    box-shadow: 0 10px 20px rgba(0,0,0,0.4); /* Stronger shadow */
    background: linear-gradient(45deg, #8bc34a, #4CAF50); /* Reverse gradient on hover */
}

.categories-section {
    padding: 60px 0;
}

.categories-section h2,
.featured-products h2,
.special-offers h2 {
     text-align: center;
    margin-bottom: 50px;
    font-size: 2.8em; /* Slightly larger section titles */
    color: #333;
    position: relative;
}

.categories-section h2::after,
.featured-products h2::after,
.special-offers h2::after {
    content: '';
    display: block;
    width: 60px;
    height: 4px;
    background: #4CAF50;
    margin: 10px auto 0;
    border-radius: 2px;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    padding: 0 20px;
}

.category-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    text-decoration: none;
    color: #333;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.category-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.25);
}

.category-card h3 {
    margin-bottom: 15px;
    color: #333; /* Darker color for readability */
    font-size: 1.5em;
}

.category-card p {
    color: #666;
    font-size: 1em;
}

.featured-products {
    padding: 60px 0;
    background: #f0f0f0;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    padding: 0 20px;
}

.product-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.25);
}

.product-image {
    width: 100%;
    height: 280px; /* Slightly larger image height */
    object-fit: cover;
    transition: transform 0.5s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05); /* Zoom effect on hover */
}

.product-info {
    padding: 20px;
    text-align: center; /* Center product info */
}

.product-title {
    font-size: 1.3em; /* Slightly larger title */
    margin-bottom: 8px;
    color: #333;
}

.product-category {
    color: #666;
    font-size: 0.9em;
    margin-bottom: 12px;
}

.product-price {
    font-size: 1.5em; /* Larger price */
    color: #4CAF50;
    font-weight: bold;
    margin-bottom: 15px;
}

.product-actions {
    display: flex;
    gap: 10px;
    justify-content: center; /* Center buttons */
}

.product-actions .btn {
    flex: none; /* Prevent flex stretching */
    width: auto; /* Allow content sizing */
    padding: 10px 20px; /* Adjusted padding */
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s ease;
    font-size: 1em;
}

.product-actions .btn:first-child {
    background: #4CAF50;
    color: white;
}

.product-actions .btn:last-child {
    background: #e0e0e0; /* Lighter grey */
    color: #333;
}

.product-actions .btn:first-child:hover {
     background: #45a049; /* Darker green on hover */
}

.product-actions .btn:last-child:hover {
    background: #d5d5d5; /* Darker grey on hover */
}

.special-offers {
    padding: 60px 0;
}

.offers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    padding: 0 20px;
}

.offer-card {
    background: linear-gradient(135deg, #4CAF50, #8bc34a); /* Green gradient */
    color: white;
    padding: 40px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    transition: transform 0.3s ease;
}

.offer-card:hover {
    transform: translateY(-10px);
}

.offer-card h3 {
    font-size: 2em; /* Larger font */
    margin-bottom: 15px;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
}

.offer-card p {
    font-size: 1.3em; /* Larger font */
    margin-bottom: 30px;
}

.offer-card .btn {
    background: white;
    color: #4CAF50;
    padding: 15px 35px; /* More padding */
    border-radius: 30px;
    text-decoration: none;
    font-weight: bold;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.offer-card .btn:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

@media (max-width: 768px) {
    .hero-section {
        padding: 80px 20px; /* Adjust padding for smaller screens */
    }
    
    .hero-section h1 {
        font-size: 2.5em; /* Adjusted for smaller screens */
    }
    
    .hero-section p {
        font-size: 1em; /* Adjusted for smaller screens */
    }
    
     .hero-section .btn {
        width: 90%; /* Adjust percentage width for smaller screens */
        padding: 15px 30px;
        max-width: 200px; /* Adjust max-width for smaller screens */
    }

    .categories-section h2,
    .featured-products h2,
    .special-offers h2 {
        font-size: 2em;
    }

    .category-grid,
    .product-grid,
    .offers-grid {
        gap: 20px;
        padding: 0 10px;
    }

    .category-card,
    .product-card,
    .offer-card {
        padding: 20px;
    }

     .offer-card h3 {
        font-size: 1.6em;
    }

    .offer-card p {
        font-size: 1.1em;
    }
}

@media (max-width: 480px) {
    .hero-section h1 {
        font-size: 2em; /* Further adjustment for very small screens */
    }
    
    .hero-section p {
        font-size: 0.9em; /* Further adjustment for very small screens */
    }

    .category-card h3 {
        font-size: 1.3em; /* Further adjustment for very small screens */
    }
    
    .product-title {
        font-size: 1em; /* Further adjustment for very small screens */
    }
    
    .product-price {
        font-size: 1.2em; /* Further adjustment for very small screens */
    }
}

/* Animation Classes */
.fade-in {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.fade-in.active {
    opacity: 1;
    transform: translateY(0);
}

.slide-in-left {
    opacity: 0;
    transform: translateX(-50px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.slide-in-left.active {
    opacity: 1;
    transform: translateX(0);
}

.slide-in-right {
    opacity: 0;
    transform: translateX(50px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.slide-in-right.active {
    opacity: 1;
    transform: translateX(0);
}

.scale-in {
    opacity: 0;
    transform: scale(0.9);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.scale-in.active {
    opacity: 1;
    transform: scale(1);
}

/* Add animation classes to elements */
.hero-content {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 1s ease-out forwards;
}

.category-card {
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.4s ease-out;
}

.category-card:hover {
    transform: translateY(-10px);
}

.product-card {
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.4s ease-out;
}

.product-card:hover {
    transform: translateY(-10px);
}

.special-offer {
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.4s ease-out;
}

.special-offer:hover {
    transform: translateY(-10px);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Enhanced Animation Classes */
.animate-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.animate-on-scroll.active {
    opacity: 1;
    transform: translateY(0);
}

.hero-section {
    position: relative;
    overflow: hidden;
}

.hero-content {
    opacity: 0;
    transform: translateY(50px);
    animation: heroFadeIn 1.2s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

.hero-section::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 100px;
    background: linear-gradient(to top, rgba(255,255,255,1), rgba(255,255,255,0));
    z-index: 1;
}

.category-card {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.category-card.active {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.category-card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.product-card {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.product-card.active {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.product-card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.product-image {
    transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.product-card:hover .product-image {
    transform: scale(1.1);
}

.offer-card {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.offer-card.active {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.offer-card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.section-title {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.section-title.active {
    opacity: 1;
    transform: translateY(0);
}

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
    0% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
    100% {
        transform: translateY(0px);
    }
}

.floating {
    animation: float 3s ease-in-out infinite;
}

/* Add parallax effect to hero section */
.hero-section {
    background-attachment: fixed;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
}

/* Add smooth scroll behavior */
html {
    scroll-behavior: smooth;
}
</style>

<div class="hero-section">
    <div class="container">
        <h1>WELCOME TO FREE CHOISE</h1>
        <p>Your one-stop destination for all your shopping needs</p>
        <a href="categories.php" class="btn">Shop Now</a>
    </div>
</div>

<div class="container">
    <section class="categories-section">
        <h2>Shop by Category</h2>
        <div class="category-grid">
            <?php while($category = $categories_result->fetch_assoc()): ?>
                <a href="categories.php?category=<?php echo $category['category_id']; ?>" class="category-card">
                    <h3><?php echo htmlspecialchars($category['category_name']); ?></h3>
                    <p><?php echo htmlspecialchars($category['description']); ?></p>
                </a>
            <?php endwhile; ?>
        </div>
    </section>

    <section class="featured-products">
        <h2>Featured Products</h2>
        <div class="product-grid">
            <?php while($product = $featured_result->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        <p class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></p>
                        <div class="product-actions">
                            <a href="product.php?id=<?php echo $product['product_id']; ?>" class="btn">View Details</a>
                            <?php if(isset($_SESSION['user_id']) && (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin'])): ?>
                                <form method="POST" action="cart.php" class="add-to-cart-form">
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
            <?php endwhile; ?>
        </div>
    </section>

    <section class="special-offers">
        <h2>Special Offers</h2>
        <div class="offers-grid">
            <div class="offer-card">
                <h3>New Customer Discount</h3>
                <p>Get 10% off on your first purchase!</p>
                <a href="register.php" class="btn">Sign Up Now</a>
            </div>
            <div class="offer-card">
                <h3>Free Shipping</h3>
                <p>On orders over $50</p>
                <a href="categories.php" class="btn">Shop Now</a>
            </div>
        </div>
    </section>
</div>

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
    // Intersection Observer for scroll animations
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
    document.querySelectorAll('.category-card, .product-card, .offer-card, .section-title').forEach(el => {
        observer.observe(el);
    });

    // Parallax effect for hero section
    const heroSection = document.querySelector('.hero-section');
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        if (heroSection) {
            heroSection.style.backgroundPositionY = scrolled * 0.5 + 'px';
        }
    });

    // Add floating animation to specific elements
    document.querySelectorAll('.offer-card').forEach(card => {
        card.classList.add('floating');
    });

    // Smooth reveal for sections
    const sections = document.querySelectorAll('section');
    sections.forEach(section => {
        const title = section.querySelector('h2');
        if (title) {
            title.classList.add('section-title');
        }
    });

    // Add stagger effect to grid items
    const staggerGrid = (gridSelector, delay = 100) => {
        const grid = document.querySelector(gridSelector);
        if (grid) {
            const items = grid.children;
            Array.from(items).forEach((item, index) => {
                item.style.transitionDelay = `${index * delay}ms`;
            });
        }
    };

    // Apply stagger effect to different grids
    staggerGrid('.category-grid');
    staggerGrid('.product-grid');
    staggerGrid('.offers-grid');

    // Add hover effect to buttons
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mouseover', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        btn.addEventListener('mouseout', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
</script> 