<?php
session_start(); // Start the session
require_once 'config/database.php'; // Include the database connection

// Get all categories for the filter
$categories_query = "SELECT * FROM categories";
$categories_result = $conn->query($categories_query);

// Get selected category from URL
$selected_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Get search term from URL
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build the products query
$products_query = "SELECT p.*, c.category_name \n                  FROM products p \n                  JOIN categories c ON p.category_id = c.category_id";

$where_clauses = [];
$bind_params = '';
$bind_values = [];

if($selected_category > 0) {
    $where_clauses[] = "p.category_id = ?";
    $bind_params .= 'i';
    $bind_values[] = $selected_category;
}

if(!empty($search_term)) {
    $where_clauses[] = "(p.product_name LIKE ? OR p.description LIKE ?)";
    $bind_params .= 'ss';
    $bind_values[] = '%' . $search_term . '%';
    $bind_values[] = '%' . $search_term . '%';
}

if(!empty($where_clauses)) {
    $products_query .= " WHERE " . implode(" AND ", $where_clauses);
}

$products_query .= " ORDER BY p.created_at DESC";

// Use prepared statement for products query
$stmt = $conn->prepare($products_query);

if (!empty($bind_values)) {
    $stmt->bind_param($bind_params, ...$bind_values);
}

$stmt->execute();
$products_result = $stmt->get_result();

// Close the statement after getting the result
$stmt->close();

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
:root {
    --cat-primary: #001F3F;
    --cat-secondary: #008080;
    --cat-accent: #008080;
    --cat-bg: linear-gradient(135deg, #f0f8ff 0%, #e6f3ff 100%);
    --cat-glass: rgba(255,255,255,0.7);
    --cat-glass-blur: blur(16px);
    --cat-shadow: 0 8px 32px rgba(0, 31, 63, 0.08);
    --cat-shadow-strong: 0 16px 48px rgba(0, 31, 63, 0.15);
    --cat-transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

body {
    background: var(--cat-bg);
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}

.categories-hero-bg {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    z-index: 0;
    pointer-events: none;
    background: linear-gradient(135deg, #001F3F 0%, #008080 100%);
    opacity: 0.08;
    filter: blur(40px);
    animation: heroParallax 20s linear infinite alternate;
}
@keyframes heroParallax {
    0% { background-position: 0% 0%; }
    100% { background-position: 100% 100%; }
}

.categories-section {
    position: relative;
    z-index: 2;
    padding: 60px 0 40px 0;
}

.categories-title {
    text-align: center;
    font-size: clamp(2.2rem, 5vw, 3.5rem);
    font-weight: 800;
    color: #2d3748;
    margin-bottom: 18px;
    letter-spacing: -0.02em;
    background: linear-gradient(90deg, #001F3F 0%, #008080 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    opacity: 0;
    transform: translateY(30px);
    transition: var(--cat-transition);
}
.categories-title.active {
    opacity: 1;
    transform: translateY(0);
}

.categories-desc {
    text-align: center;
    color: #64748b;
    font-size: 1.2rem;
    margin-bottom: 40px;
    opacity: 0;
    transform: translateY(30px);
    transition: var(--cat-transition);
}
.categories-desc.active {
    opacity: 1;
    transform: translateY(0);
}

.category-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding: 24px 32px;
    background: var(--cat-glass);
    border-radius: 24px;
    box-shadow: var(--cat-shadow);
    flex-wrap: wrap;
    gap: 24px;
    backdrop-filter: var(--cat-glass-blur);
    border: 1px solid rgba(0,31,63,0.08);
    position: relative;
    z-index: 2;
    opacity: 0;
    transform: translateY(30px);
    transition: var(--cat-transition);
}
.category-controls.active {
    opacity: 1;
    transform: translateY(0);
}

.category-filter label {
    font-weight: 600;
    color: #555;
    margin-right: 10px;
}
.category-filter select {
    padding: 10px 16px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1em;
    background: #f8f8fa;
    transition: var(--cat-transition);
}
.category-filter select:focus {
    border-color: var(--cat-primary);
    outline: none;
    box-shadow: 0 0 0 2px rgba(0,31,63,0.15);
}

.category-search input[type="text"] {
    padding: 10px 16px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1em;
    background: #f8f8fa;
    transition: var(--cat-transition);
    min-width: 180px;
}
.category-search input[type="text"]:focus {
    border-color: var(--cat-primary);
    outline: none;
    box-shadow: 0 0 0 2px rgba(0,31,63,0.15);
}

.search-form .btn {
    padding: 10px 18px;
    font-size: 1em;
    border-radius: 8px;
    background: linear-gradient(135deg, #001F3F 0%, #008080 100%);
    color: white;
    border: none;
    cursor: pointer;
    transition: var(--cat-transition);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,31,63,0.08);
    margin-left: 10px;
}
.search-form .btn:hover {
    background: linear-gradient(135deg, #008080 0%, #001F3F 100%);
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 8px 24px rgba(0,31,63,0.15);
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 40px;
    padding: 0;
    position: relative;
    z-index: 2;
}

.product-card {
    background: var(--cat-glass);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--cat-shadow);
    display: flex;
    flex-direction: column;
    position: relative;
    transition: var(--cat-transition);
    opacity: 0;
    transform: translateY(40px) scale(0.97);
}
.product-card.active {
    opacity: 1;
    transform: translateY(0) scale(1);
}
.product-card:hover {
    box-shadow: var(--cat-shadow-strong);
    transform: translateY(-8px) scale(1.03);
    background: rgba(255,255,255,0.95);
}

.product-image {
    width: 100%;
    height: 240px;
    object-fit: cover;
    transition: var(--cat-transition);
    background: #f8f8fa;
}
.product-card:hover .product-image {
    transform: scale(1.07) rotate(-1deg);
}

.product-info {
    padding: 28px 20px 20px 20px;
    text-align: center;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.product-title {
    font-size: 1.25em;
    margin-bottom: 8px;
    color: #2d3748;
    font-weight: 700;
    letter-spacing: -0.01em;
}
.product-category {
    color: #008080;
    font-size: 0.95em;
    margin-bottom: 12px;
    font-weight: 500;
}
.product-price {
    font-size: 1.3em;
    color: var(--cat-accent);
    font-weight: 700;
    margin-bottom: 18px;
}
.product-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}
.product-actions .btn {
    padding: 10px 22px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: var(--cat-transition);
    font-size: 1em;
    border: none;
    cursor: pointer;
    background: linear-gradient(135deg, #87CEEB 0%, #00FFFF 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(135,206,235,0.08);
    position: relative;
    z-index: 2;
}
.product-actions .btn:hover {
    background: linear-gradient(135deg, #00FFFF 0%, #87CEEB 100%);
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 8px 24px rgba(135,206,235,0.15);
}

@media (max-width: 900px) {
    .categories-section {
        padding: 40px 0 20px 0;
    }
    .category-controls {
        padding: 18px 10px;
        border-radius: 16px;
    }
    .product-grid {
        gap: 24px;
    }
    .product-card {
        border-radius: 14px;
    }
}
@media (max-width: 600px) {
    .categories-title {
        font-size: 2rem;
    }
    .category-controls {
        flex-direction: column;
        gap: 16px;
        padding: 12px 4px;
        border-radius: 10px;
    }
    .product-grid {
        grid-template-columns: 1fr;
        gap: 18px;
    }
    .product-card {
        border-radius: 8px;
    }
    .product-image {
        height: 180px;
    }
}
</style>

<div class="categories-hero-bg"></div>
<div class="categories-section">
    <div class="categories-title">Browse Our Categories</div>
    <div class="categories-desc">Find the perfect products for you. Filter by category or search for anything!</div>
    <div class="category-controls">
        <form class="filter-form category-filter" method="get" action="categories.php">
            <div class="form-group">
                <label for="category">Category:</label>
                <select name="category" id="category" onchange="this.form.submit()">
                    <option value="0" <?php if($selected_category == 0) echo 'selected'; ?>>All</option>
                    <?php while($cat = $categories_result->fetch_assoc()): ?>
                        <option value="<?php echo $cat['category_id']; ?>" <?php if($selected_category == $cat['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['category_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>
        <form class="search-form category-search" method="get" action="categories.php">
            <div class="form-group">
                <input type="hidden" name="category" value="<?php echo $selected_category; ?>">
                <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" class="btn" title="Search"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>
    <div class="product-grid">
        <?php if($products_result->num_rows > 0): ?>
            <?php $prod_index = 0; while($product = $products_result->fetch_assoc()): ?>
                <div class="product-card" style="transition-delay: <?php echo $prod_index * 80; ?>ms;">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image" loading="lazy">
                    <div class="product-info">
                        <div>
                            <div class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></div>
                            <div class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                            <div class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></div>
                        </div>
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
            <?php $prod_index++; endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; color: #888; font-size: 1.2em; padding: 60px 0;">No products found.</div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate hero title and description
    setTimeout(() => {
        document.querySelector('.categories-title').classList.add('active');
        document.querySelector('.categories-desc').classList.add('active');
        document.querySelector('.category-controls').classList.add('active');
    }, 200);

    // Animate product cards on scroll
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });
    document.querySelectorAll('.product-card').forEach(card => {
        observer.observe(card);
    });

    // Parallax effect for hero background
    const heroBg = document.querySelector('.categories-hero-bg');
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        heroBg.style.transform = `translateY(${scrolled * 0.15}px)`;
    });

    // Button hover effect
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.05)';
        });
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
</script>

<?php 
// Close the database connection if it was opened and not closed
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
?> 