<?php
session_start(); // Ensure session is started

// Check if user is admin
if(!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

$page_title = 'Edit Product'; // Set page title for admin header
require_once '../includes/db_config.php'; // Include database configuration

$message = '';
$success = false;
$product = null;

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($product_id <= 0) {
    header('Location: products.php?message=Invalid product ID&success=false');
    exit();
}

// Get product data
$product_query = "SELECT * FROM products WHERE product_id = ?";
$product_stmt = $conn->prepare($product_query);
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();

if($product_result->num_rows === 0) {
    header('Location: products.php?message=Product not found&success=false');
    exit();
}

$product = $product_result->fetch_assoc();
$product_stmt->close();

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock_quantity = $_POST['stock_quantity'] ?? '';
    $description = $_POST['description'] ?? '';
    $special_offer = isset($_POST['special_offer']) ? 1 : 0;

    // Basic validation
    if (empty($product_name) || empty($category_id) || $price === '' || $stock_quantity === '' || empty($description)) {
        $message = 'Please fill in all required fields.';
        $success = false;
    } else if (!is_numeric($price) || $price < 0) {
        $message = 'Price must be a non-negative number.';
        $success = false;
    } else if (!is_numeric($stock_quantity) || $stock_quantity < 0) {
        $message = 'Stock quantity must be a non-negative integer.';
        $success = false;
    } else {
        // Handle image upload if new image is provided
        $image_url = $product['image_url']; // Keep existing image by default
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/products/";
            if(!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            // Validate file type
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if(in_array($file_extension, $allowed_types)) {
                if(move_uploaded_file($_FILES["image"]['tmp_name'], $target_file)) {
                    // Delete old image if it exists
                    if(!empty($product['image_url'])) {
                        $old_image_path = '../' . $product['image_url'];
                        if (file_exists($old_image_path) && is_file($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                    $image_url = 'uploads/products/' . $new_filename;
                } else {
                    $message = 'Error uploading image.';
                    $success = false;
                }
            } else {
                $message = 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.';
                $success = false;
            }
        }

        if($success !== false) {
            // Update the product
            $update_query = "UPDATE products SET product_name = ?, category_id = ?, price = ?, stock_quantity = ?, description = ?, image_url = ?, special_offer = ? WHERE product_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $category_id_int = (int)$category_id;
            $update_stmt->bind_param("sidissii", $product_name, $category_id_int, $price, $stock_quantity, $description, $image_url, $special_offer, $product_id);
            
            if($update_stmt->execute()) {
                $message = 'Product updated successfully.';
                $success = true;
                // Redirect to product list after updating
                header('Location: products.php?message=' . urlencode($message) . '&success=' . $success);
                exit();
            } else {
                $message = 'Error updating product: ' . $conn->error;
                $success = false;
            }
            $update_stmt->close();
        }
    }
}

// Get categories for the form
$categories_query = "SELECT * FROM categories ORDER BY category_name ASC";
$categories_result = $conn->query($categories_query);

// Include admin header
require_once 'includes/admin_header.php';
?>

<!-- Modern Edit Product Page Styles -->
<style>
.edit-product-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow-x: hidden;
}

.edit-product-page::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
    z-index: 1;
}

.edit-product-container {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

/* Hero Section */
.edit-product-hero {
    text-align: center;
    margin-bottom: 3rem;
    animation: fadeInUp 1s ease-out;
}

.edit-product-hero h1 {
    font-size: 3.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
    text-shadow: 0 4px 8px rgba(0,0,0,0.3);
    background: linear-gradient(45deg, #fff, #f0f0f0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.edit-product-hero p {
    font-size: 1.2rem;
    color: rgba(255,255,255,0.9);
    margin-bottom: 2rem;
}

/* Alert Messages */
.admin-alert {
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    font-weight: 500;
    animation: slideInDown 0.5s ease-out;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}

.alert-success {
    background: rgba(39, 174, 96, 0.9);
    color: white;
    box-shadow: 0 8px 32px rgba(39, 174, 96, 0.3);
}

.alert-danger {
    background: rgba(231, 76, 60, 0.9);
    color: white;
    box-shadow: 0 8px 32px rgba(231, 76, 60, 0.3);
}

/* Edit Product Form */
.edit-product-section {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    padding: 3rem;
    margin-bottom: 3rem;
    box-shadow: 0 20px 60px rgba(0,0,0,0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.2);
    animation: fadeInUp 0.8s ease-out 0.2s both;
    position: relative;
    overflow: hidden;
}

.edit-product-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.product-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.form-group {
    position: relative;
    animation: fadeInUp 0.8s ease-out both;
}

.form-group:nth-child(1) { animation-delay: 0.3s; }
.form-group:nth-child(2) { animation-delay: 0.4s; }
.form-group:nth-child(3) { animation-delay: 0.5s; }
.form-group:nth-child(4) { animation-delay: 0.6s; }
.form-group:nth-child(5) { animation-delay: 0.7s; }
.form-group:nth-child(6) { animation-delay: 0.8s; }

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 1rem 1.5rem;
    border: 2px solid #e1e8ed;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
    color: #2c3e50;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

/* Current Image Display */
.current-image {
    margin-top: 1rem;
    text-align: center;
}

.current-image img {
    max-width: 200px;
    max-height: 200px;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border: 3px solid #fff;
}

.current-image p {
    margin-top: 0.5rem;
    color: #666;
    font-size: 0.9rem;
}

/* Checkbox Styling */
.checkbox-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.checkbox-group input[type="checkbox"] {
    width: auto;
    margin: 0;
    transform: scale(1.2);
}

.checkbox-group label {
    margin: 0;
    font-size: 0.9rem;
    color: #2c3e50;
    text-transform: none;
    letter-spacing: normal;
}

/* Form Actions */
.form-actions {
    grid-column: 1 / -1;
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
    animation: fadeInUp 0.8s ease-out 0.9s both;
}

.btn-admin {
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
}

.btn-danger {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
}

.btn-danger:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(231, 76, 60, 0.3);
}

/* Animations */
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

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .edit-product-container {
        padding: 1rem;
    }
    
    .edit-product-hero h1 {
        font-size: 2.5rem;
    }
    
    .edit-product-section {
        padding: 2rem;
    }
    
    .product-form {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .form-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn-admin {
        justify-content: center;
    }
}
</style>

<div class="edit-product-page">
    <div class="edit-product-container">
        <!-- Hero Section -->
        <div class="edit-product-hero">
            <h1>Edit Product</h1>
            <p>Update product information and settings</p>
        </div>

        <!-- Alert Messages -->
        <?php if(!empty($message)): ?>
            <div class="admin-alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Edit Product Form -->
        <div class="edit-product-section">
            <form method="POST" enctype="multipart/form-data" class="product-form">
                <div class="form-group">
                    <label for="product_name">Product Name *</label>
                    <input type="text" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php while($category = $categories_result->fetch_assoc()): ?>
                            <option value="<?php echo $category['category_id']; ?>" <?php echo $category['category_id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">Price (Rs.) *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="stock_quantity">Stock Quantity *</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="<?php echo $product['stock_quantity']; ?>" required>
                </div>

                <div class="form-group full-width">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    
                    <?php if(!empty($product['image_url'])): ?>
                        <div class="current-image">
                            <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current Product Image">
                            <p>Current image (leave empty to keep current image)</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="special_offer" name="special_offer" <?php echo $product['special_offer'] ? 'checked' : ''; ?>>
                        <label for="special_offer">Special Offer</label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-admin btn-primary">
                        <i class="fas fa-save"></i> Update Product
                    </button>
                    <a href="products.php" class="btn-admin btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                    <a href="products.php?action=delete&id=<?php echo $product_id; ?>" class="btn-admin btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                        <i class="fas fa-trash"></i> Delete Product
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?> 