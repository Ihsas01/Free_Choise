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
.category-controls {
    display: flex;
    justify-content: space-between; /* Distribute filter and search */
    align-items: center;
    margin-bottom: 40px; /* Add space below controls */
    padding: 20px; /* Add padding */
    background: #f8f8f8; /* Light background */
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 2px 5px rgba(0,0,0,0.05); /* Subtle shadow */
    flex-wrap: wrap; /* Wrap on smaller screens */
    gap: 20px; /* Gap between filter and search */
}

.category-filter,
.category-search {
    flex: 1; /* Allow items to grow */
    min-width: 200px; /* Reduced min-width to give search more space initially */
}

.category-search {
    flex: 2; /* Give search more flexibility to be larger */
}

.filter-form .form-group,
.search-form .form-group {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 0; /* Remove default form-group margin */
    width: 100%; /* Ensure form group takes full width of parent */
}

.category-filter label {
    font-weight: bold;
    color: #555;
}

.category-filter select {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1em;
    flex: 1; /* Allow select to grow */
}

.category-search input[type="text"] {
    padding: 8px 12px; /* Keep existing padding */
    border: 1px solid #ccc;
    border-radius: 4px; 
    font-size: 1em;
    flex: 1; /* Allow input to grow and take available space */
    min-width: 150px; /* Ensure input has a minimum size */
}

.search-form .btn {
    padding: 8px 12px; /* Adjust padding to make button smaller */
    font-size: 1em; /* Keep font size */
    border-radius: 4px; 
    background: #4CAF50; 
    color: white;
    border: none;
    cursor: pointer;
    transition: background 0.3s ease;
    display: flex; /* Use flex to center icon */
    align-items: center;
    justify-content: center;
    width: 40px; /* Fixed width for the icon button */
    height: 38px; /* Match height of input */
    flex-shrink: 0; /* Prevent button from shrinking */
}

.search-form .btn:hover {
    background: #45a049; 
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    padding: 0;
}

.product-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    transform: none; /* Removed transform on hover */
    box-shadow: 0 4px 12px rgba(0,0,0,0.08); /* Revert to normal shadow on hover */
}

.product-image {
    width: 100%;
    height: auto;
    object-fit: cover;
    
}

.product-card:hover .product-image {
    transform: none; /* Removed scale on hover */
}

.product-info {
    padding: 15px;
    text-align: center;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.product-title {
    font-size: 1.1em;
    margin-bottom: 5px;
    color: #333;
    font-weight: 600;
}

.product-category {
    color: #777;
    font-size: 0.8em;
    margin-bottom: 10px;
}

.product-price {
    font-size: 1.4em;
    color: #28a745;
    font-weight: bold;
    margin-bottom: 15px;
}

.product-actions {
    display: flex;
    gap: 8px;
    justify-content: center;
    margin-top: auto;
}

.product-actions .btn {
    flex: 1;
    text-align: center;
    padding: 8px 15px;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.3s ease, transform 0.1s ease;
    font-size: 0.9em;
}

.product-actions .btn:first-child {
    background: #007bff;
    color: white;
}

.product-actions .btn:last-child {
    background: #6c757d;
    color: white;
}

.product-actions .btn:first-child:hover {
     background: #0056b3;
     transform: translateY(-2px);
}

.product-actions .btn:last-child:hover {
    background: #545b62;
    transform: translateY(-2px);
}

.no-products {
    text-align: center;
    color: #777;
    font-size: 1.2em;
    margin-top: 40px;
}

@media (max-width: 768px) {
    .category-controls {
        flex-direction: column;
        align-items: stretch;
        padding: 15px;
        gap: 15px; /* Adjusted gap */
    }

    .category-filter,
    .category-search {
        min-width: unset;
        width: 100%;
    }

    .filter-form .form-group,
    .search-form .form-group {
         flex-direction: column;
         align-items: stretch;
         gap: 8px; /* Adjusted gap */
    }

    .category-filter label {
        margin-bottom: 5px; /* Added margin */
    }

    .category-filter select,
     .category-search input[type="text"] {
        width: 100%;
        padding: 10px 12px; /* Adjusted padding */
    }

     .search-form .btn {
        width: 100%;
        padding: 10px 12px; /* Adjusted padding */
        height: auto; /* Adjusted height */
    }

    .product-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .product-card {
        padding: 15px;
    }

    .product-image {
        height: 180px; /* Adjusted height for smaller screens */
    }

    .product-info {
        padding: 10px;
    }

    .product-actions {
        flex-direction: column;
        gap: 8px; /* Adjusted gap */
    }

    .product-actions .btn {
        padding: 10px 15px; /* Adjusted padding */
        font-size: 1em; /* Adjusted font size */
    }
}

@media (max-width: 480px) {
    .category-controls {
        padding: 10px;
        gap: 10px;
    }

    .category-filter select,
    .category-search input[type="text"],
    .search-form .btn {
        padding: 8px 10px; /* Further adjusted padding */
        font-size: 0.9em; /* Further adjusted font size */
    }

    .product-card {
        padding: 10px;
    }

    .product-image {
        height: 150px; /* Further adjusted height */
    }

    .product-info {
        padding: 8px;
    }

    .product-title {
        font-size: 1em; /* Further adjusted font size */
    }

    .product-price {
        font-size: 1.2em; /* Further adjusted font size */
    }

    .product-actions .btn {
        padding: 8px 10px; /* Further adjusted padding */
        font-size: 0.8em; /* Further adjusted font size */
    }
}

/* Animation Classes */





























</style>

<!-- Add loading overlay -->


<div class="container">
    <div class="category-controls">
        <div class="category-filter">
            <form method="GET" action="" class="filter-form">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="category">Category:</label>
                    <select name="category" id="category" onchange="this.form.submit()">
                        <option value="0">All Categories</option>
                        <?php while($category = $categories_result->fetch_assoc()): ?>
                            <option value="<?php echo $category['category_id']; ?>" 
                                    <?php echo $selected_category == $category['category_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                     <?php if(!empty($search_term)): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_term); ?>">
                    <?php endif; ?>
                </div>
            </form>
        </div>
         <div class="category-search">
            <form method="GET" action="" class="search-form">
                 <?php if($selected_category > 0): ?>
                    <input type="hidden" name="category" value="<?php echo $selected_category; ?>">
                <?php endif; ?>
                <div class="form-group" style="margin-bottom: 0;">
                    <input type="text" id="search" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search_term); ?>">
                    <button type="submit" class="btn btn-secondary" aria-label="Search"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="product-grid">
        <?php if($products_result->num_rows > 0): ?>
            <?php while($product = $products_result->fetch_assoc()): ?>
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
        <?php else: ?>
            <p class="no-products">No products found in this category.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Add this before closing body tag -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show loading animation
    const loading = document.querySelector('.loading');
    loading.classList.add('active');

    // Hide loading animation when page is loaded
    window.addEventListener('load', function() {
        loading.classList.remove('active');
    });

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
    document.querySelectorAll('.product-card, .filter-section, .sort-section').forEach(el => {
        observer.observe(el);
    });

    // Add stagger effect to product grid
    const productGrid = document.querySelector('.product-grid');
    if (productGrid) {
        const items = productGrid.children;
        Array.from(items).forEach((item, index) => {
            item.style.transitionDelay = `${index * 100}ms`;
        });
    }

    // Add hover effect to buttons
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mouseover', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        btn.addEventListener('mouseout', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Add animation to filter and sort sections
    const filterSection = document.querySelector('.filter-section');
    const sortSection = document.querySelector('.sort-section');
    
    if (filterSection) filterSection.classList.add('animate-on-scroll');
    if (sortSection) sortSection.classList.add('animate-on-scroll');

    // Add smooth scroll to top button
    const scrollToTop = document.querySelector('.scroll-to-top');
    if (scrollToTop) {
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollToTop.style.opacity = '1';
                scrollToTop.style.transform = 'translateY(0)';
            } else {
                scrollToTop.style.opacity = '0';
                scrollToTop.style.transform = 'translateY(20px)';
            }
        });
    }
});
</script>

<?php 
// Close the database connection if it was opened and not closed
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
require_once 'includes/footer.php'; 
?> 