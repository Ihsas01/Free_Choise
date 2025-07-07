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

<style>
/* General Section Styling */
.special-offers-content section {
    margin-bottom: 50px;
    padding: 30px 0;
}

.special-offers-content h1,
.special-offers-content h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #333;
}

.special-offers-content h1 {
    font-size: 3em;
}

.special-offers-content h2 {
    font-size: 2.5em;
    position: relative;
}

.special-offers-content h2::after {
    content: '';
    display: block;
    width: 60px;
    height: 4px;
    background: #4CAF50;
    margin: 10px auto 0;
    border-radius: 2px;
}

/* Offers Hero Section */
.offers-hero {
    background: linear-gradient(rgba(76, 175, 80, 0.8), rgba(139, 195, 74, 0.8)), url('images/offers-bg.jpg'); /* Add background image */
    background-size: cover;
    background-position: center;
    color: white;
    padding: 80px 20px;
    text-align: center;
    border-radius: 8px; /* Rounded corners */
    margin-bottom: 50px;
}

.offers-hero p {
    font-size: 1.2em;
}

/* Promotions Grid */
.promotions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    padding: 0 20px;
}

.promotion-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* Space out content */
    align-items: center;
}

.promotion-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.promotion-icon {
    font-size: 3em;
    color: #4CAF50;
    margin-bottom: 15px;
}

.promotion-card h3 {
    font-size: 1.4em;
    margin-bottom: 10px;
    color: #333;
}

.promotion-card p {
    color: #666;
    font-size: 1em;
    margin-bottom: 20px;
    flex-grow: 1; /* Allow paragraph to take space */
}

/* Promotion Button Styling */
.promotion-card .btn {
    display: inline-block;
    padding: 8px 20px; /* Smaller padding */
    font-size: 0.9em; /* Smaller font */
    border-radius: 20px; /* Rounded buttons */
    text-decoration: none;
    font-weight: bold;
    background: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.1s ease;
}

.promotion-card .btn:hover {
    background: #45a049; /* Darker green on hover */
    transform: translateY(-2px); /* Subtle press effect */
}

/* Featured Deals - Product Grid (Reusing styles from categories.php if applicable) */
/* Ensure these styles are consistent or adjusted as needed */

.featured-deals .product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    padding: 0 20px;
}

.featured-deals .product-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
     display: flex;
    flex-direction: column;
}

.featured-deals .product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.featured-deals .product-image {
    width: 100%;
    height: 200px; 
    object-fit: cover; 
    transition: transform 0.5s ease;
}

.featured-deals .product-card:hover .product-image {
    transform: scale(1.03);
}

.featured-deals .product-info {
    padding: 15px;
    text-align: center;
    flex-grow: 1; 
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.featured-deals .product-title {
    font-size: 1.1em;
    margin-bottom: 5px;
    color: #333;
    font-weight: 600;
}

.featured-deals .product-category {
    color: #777;
    font-size: 0.8em;
    margin-bottom: 10px;
}

.featured-deals .price-info {
    margin-bottom: 15px;
}

.featured-deals .original-price {
    color: #999;
    text-decoration: line-through;
    font-size: 0.9em;
    margin-bottom: 5px;
}

.featured-deals .product-price {
    font-size: 1.4em;
    color: #28a745;
    font-weight: bold;
    margin: 0;
}

.featured-deals .discount-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #dc3545; /* Bootstrap danger red */
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
    z-index: 10;
}

.featured-deals .product-actions {
    display: flex;
    gap: 8px;
    justify-content: center;
     margin-top: auto;
}

/* Featured Deals - Product Action Button Styling (Adjusted for smaller size) */
.featured-deals .product-actions .btn {
    flex: 1;
    text-align: center;
    padding: 8px 15px; /* Smaller padding */
    border-radius: 20px; /* Rounded buttons */
    text-decoration: none;
    font-weight: 600;
    transition: background 0.3s ease, transform 0.1s ease;
    font-size: 0.9em; /* Smaller font */
}

.featured-deals .product-actions .btn:first-child {
    background: #007bff; /* Bootstrap primary blue */
    color: white;
}

.featured-deals .product-actions .btn:last-child {
    background: #6c757d; /* Bootstrap secondary grey */
    color: white;
}

.featured-deals .product-actions .btn:first-child:hover {
     background: #0056b3;
     transform: translateY(-2px);
}

.featured-deals .product-actions .btn:last-child:hover {
    background: #545b62;
    transform: translateY(-2px);
}

/* Newsletter Section */
.newsletter-section {
    background: #e9ecef; /* Light grey background */
    padding: 40px 20px;
    border-radius: 8px;
    text-align: center;
}

.newsletter-section h2 {
    margin-bottom: 15px;
}

.newsletter-section p {
    font-size: 1.1em;
    color: #555;
    margin-bottom: 25px;
}

.newsletter-form .form-group {
    display: flex;
    justify-content: center;
    gap: 10px;
    max-width: 500px; /* Limit form width */
    margin: 0 auto;
}

.newsletter-form input[type="email"] {
    padding: 10px 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1em;
    flex-grow: 1;
}

/* Newsletter Button Styling */
.newsletter-form .btn {
    padding: 10px 20px; /* Button padding */
    font-size: 1em; /* Button font size */
    border-radius: 4px; /* Match input border-radius */
    background: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    transition: background 0.3s ease;
}

.newsletter-form .btn:hover {
     background: #45a049;
}

/* Terms Section */
.terms-section {
    padding: 30px 0;
}

.terms-section h2 {
    margin-bottom: 20px;
}

.terms-content {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.terms-content p {
    color: #555;
    margin-bottom: 15px;
}

.terms-content ul {
    list-style: disc;
    margin-left: 20px;
    color: #555;
}

.terms-content li {
    margin-bottom: 8px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .special-offers-content section {
        padding: 20px 0;
        margin-bottom: 30px;
    }

    .special-offers-content h1 {
        font-size: 2.5em;
    }

    .special-offers-content h2 {
        font-size: 2em;
    }

    .offers-hero {
        padding: 60px 15px;
        margin-bottom: 30px;
    }

    .promotions-grid,
    .featured-deals .product-grid {
        gap: 20px;
        padding: 0 10px;
    }

     .promotion-card,
     .featured-deals .product-card {
        padding: 20px;
     }

     .promotion-card h3 {
        font-size: 1.2em;
     }

     .promotion-card p {
        font-size: 0.9em;
     }

    .promotion-card .btn,
    .featured-deals .product-actions .btn,
    .newsletter-form .btn {
        padding: 10px 15px; /* Adjust button padding */
        font-size: 0.8em; /* Adjust button font size */
    }

    .newsletter-form .form-group {
        flex-direction: column;
        gap: 10px;
    }

    .newsletter-form input[type="email"] {
        padding: 12px 15px;
    }

    .terms-content {
         padding: 15px;
    }
}

</style>

<div class="container">
    <div class="special-offers-content">
        <section class="offers-hero">
            <h1>Special Offers</h1>
            <p>Discover amazing deals and discounts on our products</p>
        </section>

        <section class="current-offers">
            <h2>Current Promotions</h2>
            <div class="promotions-grid">
                <div class="promotion-card">
                    <div class="promotion-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <h3>New Customer Discount</h3>
                    <p>Get 10% off on your first purchase!</p>
                    <a href="register.php" class="btn">Sign Up Now</a>
                </div>

                <div class="promotion-card">
                    <div class="promotion-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Free Shipping</h3>
                    <p>Free shipping on all orders over $50</p>
                    <a href="categories.php" class="btn">Shop Now</a>
                </div>

                <div class="promotion-card">
                    <div class="promotion-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h3>Buy One Get One</h3>
                    <p>Buy one item and get another at 50% off</p>
                    <a href="categories.php" class="btn">View Products</a>
                </div>
            </div>
        </section>

        <section class="featured-deals">
            <h2>Featured Deals</h2>
            <div class="product-grid">
                <?php while($product = $offers_result->fetch_assoc()): ?>
                    <div class="product-card">
                         <div class="discount-badge">
                            <?php 
                            $original_price_placeholder = $product['price'] * 1.5; // Placeholder calculation
                            if ($original_price_placeholder > 0) {
                                $discount_percentage = round((($original_price_placeholder - $product['price']) / $original_price_placeholder) * 100);
                                echo $discount_percentage . '% OFF';
                            } else {
                                echo 'Discount';
                            }
                            ?>
                        </div>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
                        <div class="product-info">
                            <h3 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            <div class="price-info">
                                 <?php if ($original_price_placeholder > 0): ?>
                                    <p class="original-price">Rs. <?php echo number_format($original_price_placeholder, 2); ?></p>
                                <?php endif; ?>
                                <p class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></p>
                            </div>
                            <div class="product-actions">
                                <a href="product.php?id=<?php echo $product['product_id']; ?>" class="btn">View Details</a>
                                <?php if(isset($_SESSION['user_id'])): ?>
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

        <section class="newsletter-section">
            <h2>Stay Updated</h2>
            <p>Subscribe to our newsletter to receive exclusive offers and updates</p>
            <form method="POST" action="" class="newsletter-form">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <button type="submit" class="btn">Subscribe</button>
                </div>
            </form>
        </section>

        <section class="terms-section">
            <h2>Terms & Conditions</h2>
            <div class="terms-content">
                <p>All special offers and promotions are subject to availability and may be modified or discontinued at any time without notice.</p>
                <ul>
                    <li>Discounts cannot be combined with other offers</li>
                    <li>Free shipping applies to standard delivery only</li>
                    <li>Some products may be excluded from promotions</li>
                    <li>Offers are valid while supplies last</li>
                </ul>
            </div>
        </section>
    </div>
</div>

<?php 
// Close the database connection if it was opened and not closed
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
require_once 'includes/footer.php'; 
?>