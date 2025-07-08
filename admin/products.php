<?php
session_start(); // Ensure session is started

// Check if user is admin
if(!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

$page_title = 'Manage Products'; // Set page title for admin header
require_once '../includes/db_config.php'; // Include database configuration

$message = '';
$success = false;

// Determine which view to show (list or add product)
$action = $_GET['action'] ?? 'list';

// Handle product actions (POST requests)
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'add':
                $product_name = $_POST['product_name'] ?? '';
                $category_id = $_POST['category_id'] ?? '';
                $price = $_POST['price'] ?? '';
                $stock_quantity = $_POST['stock_quantity'] ?? '';
                $description = $_POST['description'] ?? '';
                $special_offer = isset($_POST['special_offer']) ? 1 : 0;

                // Handle image upload
                $image_url = '';
                if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $target_dir = "../uploads/products/";
                    if(!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
                    $new_filename = uniqid() . '.' . $file_extension;
                    $target_file = $target_dir . $new_filename;
                    
                    // Validate file type (optional but recommended)
                    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
                    if(in_array($file_extension, $allowed_types)) {
                        if(move_uploaded_file($_FILES["image"]['tmp_name'], $target_file)) {
                            $image_url = 'uploads/products/' . $new_filename;
                        } else {
                            $message = 'Error uploading image.';
                            $success = false;
                        }
                    } else {
                         $message = 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.';
                         $success = false;
                    }
                } else if ($_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
                     // Handle other file upload errors
                     $message = 'File upload error: ' . $_FILES['image']['error'];
                     $success = false;
                }

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
                     // Prepare and execute the insert query
                     $query = "INSERT INTO products (product_name, category_id, price, stock_quantity, description, image_url, special_offer) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)";
                     $stmt = $conn->prepare($query);
                     // Ensure category_id is an integer
                     $category_id_int = (int)$category_id;
                     $stmt->bind_param("sidissi", $product_name, $category_id_int, $price, $stock_quantity, $description, $image_url, $special_offer);
                     
                     if($stmt->execute()) {
                         $message = 'Product added successfully.';
                         $success = true;
                         // Redirect to product list after adding
                         header('Location: products.php?message=' . urlencode($message) . '&success=' . $success);
                         exit();
                     } else {
                         $message = 'Error adding product: ' . $conn->error; // Add error detail for debugging
                         $success = false;
                     }
                      $stmt->close();
                 }

                 // If there was an error, set action back to 'add' to show the form again
                 if (!$success) {
                     $action = 'add';
                 }

                break;

            case 'delete':
                $product_id = (int)$_POST['product_id'];
                
                // Optional: Delete the image file before deleting the database record
                $image_query = "SELECT image_url FROM products WHERE product_id = ? LIMIT 1";
                $image_stmt = $conn->prepare($image_query);
                $image_stmt->bind_param("i", $product_id);
                $image_stmt->execute();
                $image_result = $image_stmt->get_result();
                if ($image_result->num_rows > 0) {
                    $image_path = '../' . $image_result->fetch_assoc()['image_url'];
                    if (file_exists($image_path) && is_file($image_path)) {
                        unlink($image_path);
                    }
                }
                $image_stmt->close();

                $delete_query = "DELETE FROM products WHERE product_id = ?";
                $delete_stmt = $conn->prepare($delete_query);
                $delete_stmt->bind_param("i", $product_id);
                
                if($delete_stmt->execute()) {
                    $message = 'Product deleted successfully';
                    $success = true;
                } else {
                    $message = 'Error deleting product: ' . $conn->error; // Add error detail
                    $success = false;
                }
                $delete_stmt->close();
                break;

            case 'toggle_special_offer':
                $product_id = (int)$_POST['product_id'];
                
                // Get current special_offer status
                $current_status_query = "SELECT special_offer FROM products WHERE product_id = ?";
                $current_status_stmt = $conn->prepare($current_status_query);
                $current_status_stmt->bind_param("i", $product_id);
                $current_status_stmt->execute();
                $current_status_result = $current_status_stmt->get_result();
                
                if ($current_status_result->num_rows > 0) {
                    $current_status = $current_status_result->fetch_assoc()['special_offer'];
                    $new_status = $current_status ? 0 : 1;
                    
                    // Update the special_offer status
                    $update_query = "UPDATE products SET special_offer = ? WHERE product_id = ?";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bind_param("ii", $new_status, $product_id);
                    
                    if($update_stmt->execute()) {
                        $status_text = $new_status ? 'added to' : 'removed from';
                        $message = "Product successfully $status_text special offers.";
                        $success = true;
                    } else {
                        $message = 'Error updating product: ' . $conn->error;
                        $success = false;
                    }
                    $update_stmt->close();
                } else {
                    $message = 'Product not found.';
                    $success = false;
                }
                $current_status_stmt->close();
                break;

            case 'delete_all_by_category':
                $category_id = (int)$_POST['category_id'];
                
                // Get all products in the selected category
                $category_products_query = "SELECT product_id, image_url FROM products WHERE category_id = ?";
                $category_stmt = $conn->prepare($category_products_query);
                $category_stmt->bind_param("i", $category_id);
                $category_stmt->execute();
                $category_result = $category_stmt->get_result();
                
                $deleted_count = 0;
                while($product = $category_result->fetch_assoc()) {
                    // Delete image file
                    if(!empty($product['image_url'])) {
                        $image_path = '../' . $product['image_url'];
                        if (file_exists($image_path) && is_file($image_path)) {
                            unlink($image_path);
                        }
                    }
                    $deleted_count++;
                }
                
                // Delete all products in the category
                $delete_category_query = "DELETE FROM products WHERE category_id = ?";
                $delete_category_stmt = $conn->prepare($delete_category_query);
                $delete_category_stmt->bind_param("i", $category_id);
                
                if($delete_category_stmt->execute()) {
                    $message = "Successfully deleted $deleted_count products from the selected category.";
                    $success = true;
                } else {
                    $message = 'Error deleting products: ' . $conn->error;
                    $success = false;
                }
                $delete_category_stmt->close();
                $category_stmt->close();
                break;

            case 'delete_all_products':
                // Get all products for image deletion
                $all_products_query = "SELECT product_id, image_url FROM products";
                $all_products_result = $conn->query($all_products_query);
                
                $deleted_count = 0;
                while($product = $all_products_result->fetch_assoc()) {
                    // Delete image file
                    if(!empty($product['image_url'])) {
                        $image_path = '../' . $product['image_url'];
                        if (file_exists($image_path) && is_file($image_path)) {
                            unlink($image_path);
                        }
                    }
                    $deleted_count++;
                }
                
                // Delete all products
                $delete_all_query = "DELETE FROM products";
                if($conn->query($delete_all_query)) {
                    $message = "Successfully deleted all $deleted_count products from all categories.";
                    $success = true;
                } else {
                    $message = 'Error deleting all products: ' . $conn->error;
                    $success = false;
                }
                break;
        }
    }
    
    // Handle GET actions (like delete from edit page)
    if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $product_id = (int)$_GET['id'];
        
        // Optional: Delete the image file before deleting the database record
        $image_query = "SELECT image_url FROM products WHERE product_id = ? LIMIT 1";
        $image_stmt = $conn->prepare($image_query);
        $image_stmt->bind_param("i", $product_id);
        $image_stmt->execute();
        $image_result = $image_stmt->get_result();
        if ($image_result->num_rows > 0) {
            $image_path = '../' . $image_result->fetch_assoc()['image_url'];
            if (file_exists($image_path) && is_file($image_path)) {
                unlink($image_path);
            }
        }
        $image_stmt->close();

        $delete_query = "DELETE FROM products WHERE product_id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $product_id);
        
        if($delete_stmt->execute()) {
            $message = 'Product deleted successfully';
            $success = true;
        } else {
            $message = 'Error deleting product: ' . $conn->error;
            $success = false;
        }
        $delete_stmt->close();
    }
    
    // After handling POST, check if there's a message in the URL for redirects
} else if (isset($_GET['message'])) {
    // Handle GET requests with message parameter (from redirects)
    $message = htmlspecialchars($_GET['message']);
    $success = filter_var($_GET['success'], FILTER_VALIDATE_BOOLEAN);
}


// Get all products for the list view
$products_query = "SELECT p.*, c.category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  ORDER BY p.created_at DESC";
$products_result = $conn->query($products_query);

// Get categories for the form
$categories_query = "SELECT * FROM categories ORDER BY category_name ASC";
$categories_result = $conn->query($categories_query);

// Include admin header
require_once 'includes/admin_header.php';
?>

<!-- Modern Products Page Styles -->
<style>
/* Modern Products Page Styles */
.products-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow-x: hidden;
}

.products-page::before {
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

.products-container {
    position: relative;
    z-index: 2;
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

/* Hero Section */
.products-hero {
    text-align: center;
    margin-bottom: 3rem;
    animation: fadeInUp 1s ease-out;
}

.products-hero h1 {
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

.products-hero p {
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

/* Add Product Form */
.add-product-section {
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

.add-product-section::before {
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

/* File Upload Styling */
.form-group input[type="file"] {
    padding: 0.8rem;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.form-group input[type="file"]::before {
    content: '📁 Choose Image';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    pointer-events: none;
}

.form-group input[type="file"]:hover {
    background: linear-gradient(135deg, #5a6fd8, #6a4190);
    transform: translateY(-2px);
}

/* Checkbox Styling */
.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    background: white;
    border: 2px solid #e1e8ed;
    border-radius: 4px;
    margin-right: 10px;
    position: relative;
    transition: all 0.3s ease;
}

.checkbox-label:hover .checkmark {
    border-color: #667eea;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-color: #667eea;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

/* Button Styles */
.btn-container {
    grid-column: 1 / -1;
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    animation: fadeInUp 0.8s ease-out 0.9s both;
}

.btn-admin {
    padding: 1rem 2rem;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
    overflow: hidden;
}

.btn-admin::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-admin:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: rgba(255,255,255,0.9);
    color: #2c3e50;
    border: 2px solid #e1e8ed;
}

.btn-secondary:hover {
    background: white;
    transform: translateY(-3px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
}

.btn-danger {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    box-shadow: 0 8px 32px rgba(231, 76, 60, 0.3);
}

.btn-danger:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(231, 76, 60, 0.4);
}

.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    box-shadow: 0 8px 32px rgba(40, 167, 69, 0.3);
}

.btn-success:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(40, 167, 69, 0.4);
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
    box-shadow: 0 8px 32px rgba(255, 193, 7, 0.3);
}

.btn-warning:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(255, 193, 7, 0.4);
}

/* Products List Section */
.products-list-section {
    animation: fadeInUp 0.8s ease-out 0.3s both;
}

.admin-actions {
    margin-bottom: 2rem;
    animation: fadeInUp 0.8s ease-out 0.4s both;
}

/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.product-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.2);
    position: relative;
    animation: fadeInUp 0.8s ease-out both;
}

.product-card:nth-child(1) { animation-delay: 0.1s; }
.product-card:nth-child(2) { animation-delay: 0.2s; }
.product-card:nth-child(3) { animation-delay: 0.3s; }
.product-card:nth-child(4) { animation-delay: 0.4s; }
.product-card:nth-child(5) { animation-delay: 0.5s; }
.product-card:nth-child(6) { animation-delay: 0.6s; }

.product-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.product-card:hover::before {
    transform: scaleX(1);
}

.product-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 30px 80px rgba(0,0,0,0.15);
}

.product-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: all 0.4s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-info {
    padding: 2rem;
}

.product-info h4 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
    line-height: 1.3;
}

.product-info p {
    margin-bottom: 0.8rem;
    color: #666;
    font-size: 0.95rem;
}

.product-info .category {
    color: #667eea;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.8rem;
}

.product-info .price {
    font-size: 1.2rem;
    font-weight: 700;
    color: #27ae60;
}

.product-info .stock {
    color: #f39c12;
    font-weight: 600;
}

.product-info .special-offer-status {
    color: #e74c3c;
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.product-info .special-offer-status .text-warning {
    color: #f39c12;
}

.product-info .special-offer-status .text-muted {
    color: #95a5a6;
}

.product-actions {
    display: flex;
    gap: 0.8rem;
    margin-top: 1.5rem;
    flex-wrap: wrap;
}

.product-actions .btn-admin {
    flex: 1;
    min-width: 120px;
    justify-content: center;
    font-size: 0.9rem;
    padding: 0.8rem 1.2rem;
}

/* No Products Message */
.no-products {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255,255,255,0.9);
    border-radius: 20px;
    backdrop-filter: blur(20px);
    animation: fadeInUp 0.8s ease-out;
}

.no-products p {
    font-size: 1.2rem;
    color: #666;
    margin-bottom: 2rem;
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
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes shimmer {
    0% {
        background-position: -200px 0;
    }
    100% {
        background-position: calc(200px + 100%) 0;
    }
}

/* Loading Animation */
.loading {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200px 100%;
    animation: shimmer 1.5s infinite;
}

/* Responsive Design */
@media (max-width: 768px) {
    .products-container {
        padding: 1rem;
    }
    
    .products-hero h1 {
        font-size: 2.5rem;
    }
    
    .add-product-section {
        padding: 2rem;
    }
    
    .product-form {
        grid-template-columns: 1fr;
    }
    
    .products-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .btn-container {
        flex-direction: column;
    }
    
    .product-actions {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .products-hero h1 {
        font-size: 2rem;
    }
    
    .add-product-section {
        padding: 1.5rem;
    }
    
    .product-info {
        padding: 1.5rem;
    }
}

/* Smooth Scrolling */
html {
    scroll-behavior: smooth;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.1);
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a6fd8, #6a4190);
}
</style>

<div class="products-page">
    <div class="products-container">
        <!-- Hero Section -->
        <div class="products-hero">
            <h1><?php echo ($action == 'add') ? 'Add New Product' : 'Manage Products'; ?></h1>
            <p><?php echo ($action == 'add') ? 'Create a new product for your store' : 'View and manage all your products'; ?></p>
        </div>
    
    <?php if($message): ?>
        <div class="admin-alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>">
                <i class="fas <?php echo $success ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($action == 'add'): ?>
        <!-- Add New Product Form -->
        <div class="add-product-section">
            <form method="POST" action="" enctype="multipart/form-data" class="product-form">
                <input type="hidden" name="action" value="add">
                    
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                        <input type="text" id="product_name" name="product_name" required value="<?php echo htmlspecialchars($_POST['product_name'] ?? ''); ?>" placeholder="Enter product name">
                </div>
                    
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">-- Select Category --</option>
                        <?php 
                        // Reset categories_result pointer or re-run query
                         $categories_result_form = $conn->query($categories_query);
                         while($category = $categories_result_form->fetch_assoc()): 
                         $selected = (isset($_POST['category_id']) && $_POST['category_id'] == $category['category_id']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $category['category_id']; ?>" <?php echo $selected; ?> >
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                    
                <div class="form-group">
                    <label for="price">Price</label>
                        <input type="number" id="price" name="price" step="0.01" required min="0" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" placeholder="0.00">
                </div>
                    
                 <div class="form-group">
                    <label for="stock_quantity">Stock Quantity</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" required min="0" value="<?php echo htmlspecialchars($_POST['stock_quantity'] ?? ''); ?>" placeholder="0">
                </div>
                    
                    <div class="form-group full-width">
                    <label for="description">Description</label>
                        <textarea id="description" name="description" required rows="4" placeholder="Enter product description"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                    
                    <div class="form-group full-width">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>
                
                <div class="form-group">
                    <label for="special_offer" class="checkbox-label">
                        <input type="checkbox" id="special_offer" name="special_offer" value="1" <?php echo (isset($_POST['special_offer']) && $_POST['special_offer'] == '1') ? 'checked' : ''; ?>>
                        <span class="checkmark"></span>
                        Add to Special Offers
                    </label>
                </div>
                    
                    <div class="btn-container">
                        <button type="submit" class="btn-admin btn-primary">
                            <i class="fas fa-plus"></i> Add Product
                        </button>
                        <a href="products.php" class="btn-admin btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                    </div>
            </form>
        </div>

    <?php else: ?>
        <!-- Products List -->
            <div class="products-list-section">
                <div class="admin-actions">
                    <a href="products.php?action=add" class="btn-admin btn-primary">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                    <button type="button" class="btn-admin btn-danger" onclick="showDeleteModal()">
                        <i class="fas fa-trash-alt"></i> Delete All Products
                    </button>
             </div>

            <?php if($products_result->num_rows > 0): ?>
                <div class="products-grid">
                    <?php while($product = $products_result->fetch_assoc()): ?>
                        <div class="product-card">
                                <?php $image_src = !empty($product['image_url']) ? '../' . $product['image_url'] : '../assets/images/placeholder.png'; ?>
                            <img src="<?php echo htmlspecialchars($image_src); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
                            <div class="product-info">
                                <h4><?php echo htmlspecialchars($product['product_name']); ?></h4>
                                <p class="category">Category: <?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></p>
                                <p class="price">Price: Rs. <?php echo number_format($product['price'], 2); ?></p>
                                <p class="stock">Stock: <?php echo $product['stock_quantity']; ?></p>
                                <p class="special-offer-status">
                                    <i class="fas <?php echo ($product['special_offer'] ?? 0) ? 'fa-star text-warning' : 'fa-star-o text-muted'; ?>"></i>
                                    <?php echo ($product['special_offer'] ?? 0) ? 'Special Offer' : 'Regular Product'; ?>
                                </p>
                                    <div class="product-actions">
                                        <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn-admin btn-secondary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" action="" style="display:inline-block;">
                                            <input type="hidden" name="action" value="toggle_special_offer">
                                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                            <button type="submit" class="btn-admin <?php echo ($product['special_offer'] ?? 0) ? 'btn-warning' : 'btn-success'; ?>">
                                                <i class="fas <?php echo ($product['special_offer'] ?? 0) ? 'fa-star-o' : 'fa-star'; ?>"></i>
                                                <?php echo ($product['special_offer'] ?? 0) ? 'Remove from Offers' : 'Add to Offers'; ?>
                                            </button>
                                        </form>
                                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this product?');" style="display:inline-block;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                            <button type="submit" class="btn-admin btn-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                    </form>
                                    </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                    <div class="no-products">
                        <i class="fas fa-box-open" style="font-size: 3rem; color: #667eea; margin-bottom: 1rem;"></i>
                        <p>No products found. Start by adding your first product!</p>
                        <a href="products.php?action=add" class="btn-admin btn-primary">
                            <i class="fas fa-plus"></i> Add Your First Product
                        </a>
                    </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</div>

<!-- Delete All Products Modal -->
<div id="deleteModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Delete All Products</h3>
            <button class="modal-close" onclick="hideDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Choose how you want to delete products:</p>
            
            <div class="delete-options">
                <div class="delete-option">
                    <h4><i class="fas fa-folder"></i> Delete by Category</h4>
                    <p>Delete all products from a specific category</p>
                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete all products from this category? This action cannot be undone.');">
                        <input type="hidden" name="action" value="delete_all_by_category">
                        <select name="category_id" required class="category-select">
                            <option value="">-- Select Category --</option>
                            <?php 
                            $categories_modal = $conn->query($categories_query);
                            while($category = $categories_modal->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $category['category_id']; ?>">
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <button type="submit" class="btn-admin btn-danger">
                            <i class="fas fa-trash"></i> Delete Category Products
                        </button>
                    </form>
                </div>
                
                <div class="delete-option">
                    <h4><i class="fas fa-trash-alt"></i> Delete All Products</h4>
                    <p>Delete all products from all categories</p>
                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete ALL products from ALL categories? This action cannot be undone.');">
                        <input type="hidden" name="action" value="delete_all_products">
                        <button type="submit" class="btn-admin btn-danger">
                            <i class="fas fa-trash-alt"></i> Delete All Products
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Styles -->
<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease-out;
}

.modal-content {
    background: white;
    border-radius: 20px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
    animation: slideInUp 0.3s ease-out;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2rem 2rem 1rem;
    border-bottom: 1px solid #e1e8ed;
}

.modal-header h3 {
    color: #2c3e50;
    font-size: 1.5rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.modal-header h3 i {
    color: #e74c3c;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #666;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.modal-close:hover {
    background: #f8f9fa;
    color: #e74c3c;
}

.modal-body {
    padding: 2rem;
}

.modal-body p {
    color: #666;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

.delete-options {
    display: grid;
    gap: 2rem;
}

.delete-option {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 2rem;
    border: 2px solid #e1e8ed;
    transition: all 0.3s ease;
}

.delete-option:hover {
    border-color: #e74c3c;
    background: #fff5f5;
}

.delete-option h4 {
    color: #2c3e50;
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.delete-option h4 i {
    color: #e74c3c;
}

.delete-option p {
    color: #666;
    margin-bottom: 1.5rem;
    font-size: 0.95rem;
}

.category-select {
    width: 100%;
    padding: 1rem 1.5rem;
    border: 2px solid #e1e8ed;
    border-radius: 12px;
    font-size: 1rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.category-select:focus {
    outline: none;
    border-color: #e74c3c;
    box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
}

.delete-option .btn-admin {
    width: 100%;
    justify-content: center;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 1rem;
    }
    
    .modal-header,
    .modal-body {
        padding: 1.5rem;
    }
    
    .delete-options {
        gap: 1.5rem;
    }
    
    .delete-option {
        padding: 1.5rem;
    }
}
</style>

<!-- JavaScript for Modal -->
<script>
function showDeleteModal() {
    document.getElementById('deleteModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function hideDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteModal');
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideDeleteModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
            hideDeleteModal();
        }
    });
});

// ... existing JavaScript code ...
</script>

<?php 
// Close the database connection if it was opened and not closed
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
// Include admin footer
require_once 'includes/admin_footer.php';
?> 