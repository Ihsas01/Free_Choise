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
                     $query = "INSERT INTO products (product_name, category_id, price, stock_quantity, description, image_url) 
                              VALUES (?, ?, ?, ?, ?, ?)";
                     $stmt = $conn->prepare($query);
                     // Ensure category_id is an integer
                     $category_id_int = (int)$category_id;
                     $stmt->bind_param("sidiss", $product_name, $category_id_int, $price, $stock_quantity, $description, $image_url);
                     
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
        }
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

<div class="admin-content">
    <h2><?php echo ($action == 'add') ? 'Add New Product' : 'Manage Products'; ?></h2>
    
    <?php if($message): ?>
        <div class="admin-alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>">
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
                    <input type="text" id="product_name" name="product_name" required value="<?php echo htmlspecialchars($_POST['product_name'] ?? ''); ?>">
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
                    <input type="number" id="price" name="price" step="0.01" required min="0" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
                </div>
                 <div class="form-group">
                    <label for="stock_quantity">Stock Quantity</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" required min="0" value="<?php echo htmlspecialchars($_POST['stock_quantity'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required rows="4"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>
                <button type="submit" class="btn-admin btn-primary"><i class="fas fa-plus"></i> Add Product</button>
                 <a href="products.php" class="btn-admin btn-secondary"><i class="fas fa-list"></i> Cancel</a>
            </form>
        </div>

    <?php else: ?>
        <!-- Products List -->
        <div class="products-list-section" style="margin-top: 1rem;">
             <div class="admin-actions" style="margin-bottom: 1rem;">
                 <a href="products.php?action=add" class="btn-admin btn-primary"><i class="fas fa-plus"></i> Add New Product</a>
             </div>

            <?php if($products_result->num_rows > 0): ?>
                <div class="products-grid">
                    <?php while($product = $products_result->fetch_assoc()): ?>
                        <div class="product-card">
                             <?php $image_src = !empty($product['image_url']) ? '../' . $product['image_url'] : '../assets/images/placeholder.png'; // Placeholder image ?>
                            <img src="<?php echo htmlspecialchars($image_src); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
                            <div class="product-info">
                                <h4><?php echo htmlspecialchars($product['product_name']); ?></h4>
                                <p class="category">Category: <?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></p>
                                <p class="price">Price: Rs. <?php echo number_format($product['price'], 2); ?></p>
                                <p class="stock">Stock: <?php echo $product['stock_quantity']; ?></p>
                                <div class="admin-actions">
                                     <!-- Edit button (add functionality later) -->
                                    <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn-admin btn-secondary"><i class="fas fa-edit"></i> Edit</a>
                                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this product?');" style="display:inline-block;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                        <button type="submit" class="btn-admin btn-danger"><i class="fas fa-trash"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-products admin-alert alert-warning">No products found.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php 
// Close the database connection if it was opened and not closed
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
// Include admin footer
require_once 'includes/admin_footer.php';
?> 