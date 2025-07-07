<?php
session_start();
require_once 'config/database.php';
require_once 'includes/ban_check.php'; // Include ban check functionality

// Check if user is logged in and is NOT admin
if (!isset($_SESSION['user_id']) || (isset($_SESSION['is_admin']) && $_SESSION['is_admin'])) {
    // Redirect or show an error if user is not logged in or is an admin
    $is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($is_ajax) {
        http_response_code(403);
        sendJsonResponse(false, 'Admins cannot use the cart.');
    } else {
        // For non-AJAX requests, you might redirect or show a message
        // For now, let's redirect to the homepage or a specific message page
        header('Location: index.php'); // Or a page explaining the restriction
        exit();
    }
}

// Check if it's an AJAX request
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Function to send JSON response
function sendJsonResponse($success, $message, $cart_count = 0) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'cart_count' => $cart_count
    ]);
    exit();
}

// Handle AJAX requests
if ($is_ajax) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        sendJsonResponse(false, 'User not logged in.');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        sendJsonResponse(false, 'Method not allowed.');
    }

    $user_id = $_SESSION['user_id'];
    $action = $_POST['action'] ?? '';
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

    if (!$product_id) {
        sendJsonResponse(false, 'Invalid product ID.');
    }

    // Check if user is banned
    if (!canUserPlaceOrders($user_id)) {
        sendJsonResponse(false, 'Your account is temporarily suspended. You cannot add items to cart.');
    }

    switch($action) {
        case 'add':
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            // Check if product already in cart
            $check_query = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("ii", $user_id, $product_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if($check_result->num_rows > 0) {
                // Update quantity
                $update_query = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("iii", $quantity, $user_id, $product_id);
                $update_stmt->execute();
            } else {
                // Add new item
                $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
                $insert_stmt->execute();
            }

            // Get updated cart count
            $cart_count_query = "SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?";
            $cart_count_stmt = $conn->prepare($cart_count_query);
            $cart_count_stmt->bind_param("i", $user_id);
            $cart_count_stmt->execute();
            $cart_count_result = $cart_count_stmt->get_result();
            $cart_count_row = $cart_count_result->fetch_assoc();
            $new_cart_count = (int)($cart_count_row['total_items'] ?? 0);

            sendJsonResponse(true, 'Product added to cart', $new_cart_count);
            break;

        default:
            sendJsonResponse(false, 'Invalid action.');
    }
    exit(); // Stop script execution after AJAX handling
}

// Handle regular page load (non-AJAX)
if (!$is_ajax) {
    if(!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    $user_id = $_SESSION['user_id'] ?? null; // Use null coalescing for safety
    $message = '';

    // Handle regular POST requests (non-AJAX)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $action = $_POST['action'];
        $product_id = (int)$_POST['product_id'];

        switch($action) {
            case 'update':
                $quantity = (int)$_POST['quantity'];
                if($quantity > 0) {
                    $update_query = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bind_param("iii", $quantity, $user_id, $product_id);
                    $update_stmt->execute();
                    $message = 'Cart updated';
                }
                break;

            case 'remove':
                $delete_query = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
                $delete_stmt = $conn->prepare($delete_query);
                $delete_stmt->bind_param("ii", $user_id, $product_id);
                $delete_stmt->execute();
                $message = 'Product removed from cart';
                break;
        }
    }

    // Include header for regular page load
    require_once 'includes/header.php';

    // Get cart items
    $cart_query = "SELECT c.*, p.product_name, p.price, p.image_url 
                   FROM cart c 
                   JOIN products p ON c.product_id = p.product_id 
                   WHERE c.user_id = ?";
    $cart_stmt = $conn->prepare($cart_query);
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();

    $total = 0;
?>

<div class="container">
    <h2>Shopping Cart</h2>
    <?php if($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php 
    // Check if user is banned and display warning
    if (isset($_SESSION['user_id'])) {
        $ban_info = isUserBanned($_SESSION['user_id']);
        if ($ban_info && $ban_info['banned']) {
            displayBanWarning($ban_info);
        }
    }
    ?>

    <?php if($cart_result->num_rows > 0): ?>
        <div class="cart-items">
            <?php while($item = $cart_result->fetch_assoc()): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <div class="cart-item">
                    <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['product_name']; ?>" class="cart-item-image">
                    <div class="cart-item-details">
                        <h3><?php echo $item['product_name']; ?></h3>
                        <p class="price">Rs. <?php echo number_format($item['price'], 2); ?></p>
                        <form method="POST" action="" class="quantity-form">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                            <label for="quantity">Quantity:</label>
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" onchange="this.form.submit()">
                        </form>
                        <p class="subtotal">Subtotal: Rs. <?php echo number_format($subtotal, 2); ?></p>
                        <form method="POST" action="" class="remove-form">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                            <button type="submit" class="btn btn-danger">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <p class="total">Rs. <?php echo number_format($total, 2); ?></p>
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
            </div>
        </div>
    <?php else: ?>
        <p class="empty-cart">Your cart is empty. <a href="categories.php">Continue shopping</a></p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
<?php } // Close the if (!$is_ajax) block ?> 