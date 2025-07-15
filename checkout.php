<?php
session_start();
require_once 'config/database.php';
require_once 'includes/ban_check.php'; // Include ban check functionality

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if user is banned
if (!canUserPlaceOrders($user_id)) {
    header('Location: cart.php');
    exit();
}

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

// Include header
require_once 'includes/header.php';
?>

<div class="checkout-hero animated-fade-in">
    <h2 class="checkout-title sexy-gradient-text">Checkout</h2>
    <p class="checkout-subtitle">Complete your order in style. Enjoy a smooth, secure, and delightful experience!</p>
</div>

<div class="container checkout-main animated-slide-up">
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
        <div class="checkout-grid">
            <section class="order-summary card animated-fade-in">
                <h3><i class="fas fa-receipt"></i> Order Summary</h3>
                <div class="checkout-items-list">
                <?php while($item = $cart_result->fetch_assoc()): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <div class="checkout-item animated-slide-in">
                        <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['product_name']; ?>" class="checkout-item-image">
                        <div class="checkout-item-details">
                            <h4><?php echo $item['product_name']; ?></h4>
                            <p>Quantity: <span class="badge badge-qty"><?php echo $item['quantity']; ?></span></p>
                            <p>Price: <span class="price">Rs. <?php echo number_format($item['price'], 2); ?></span></p>
                            <p>Subtotal: <span class="subtotal">Rs. <?php echo number_format($subtotal, 2); ?></span></p>
                        </div>
                    </div>
                <?php endwhile; ?>
                </div>
                <div class="total-amount highlight-glow">
                    <h4>Total: <span class="total-price">Rs. <?php echo number_format($total, 2); ?></span></h4>
                </div>
            </section>

            <form action="process_order.php" method="POST" class="checkout-form card animated-fade-in" id="checkoutForm">
                <section class="payment-method">
                    <h3><i class="fas fa-credit-card"></i> Select Payment Method</h3>
                    <div class="payment-options">
                        <label class="payment-option animated-bounce-in">
                            <input type="radio" id="cod" name="payment_method" value="cod" required>
                            <span class="custom-radio"></span> Cash on Delivery
                        </label>
                        <label class="payment-option animated-bounce-in">
                            <input type="radio" id="card" name="payment_method" value="card" required>
                            <span class="custom-radio"></span> Online Payment
                        </label>
                    </div>
                </section>

                <section id="cardPaymentSection" class="payment-section animated-fade-in" style="display: none;">
                    <div class="card-type">
                        <h4>Select Card Type</h4>
                        <div class="card-options">
                            <label class="card-option"><input type="radio" id="debit" name="card_type" value="debit"> Debit Card</label>
                            <label class="card-option"><input type="radio" id="credit" name="card_type" value="credit"> Credit Card</label>
                            <label class="card-option"><input type="radio" id="mastercard" name="card_type" value="mastercard"> Master Card</label>
                        </div>
                    </div>
                    <div class="card-details">
                        <div class="form-group">
                            <label for="card_number">Card Number:</label>
                            <input type="text" id="card_number" name="card_number" placeholder="XXXX XXXX XXXX XXXX" maxlength="19" pattern="\d{4}\s\d{4}\s\d{4}\s\d{4}">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiry">Expiry Date:</label>
                                <input type="text" id="expiry" name="expiry" placeholder="MM/YY" maxlength="5" pattern="\d{2}/\d{2}">
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV:</label>
                                <input type="text" id="cvv" name="cvv" placeholder="XXXX" maxlength="4" pattern="\d{4}">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="address-section animated-fade-in">
                    <h3><i class="fas fa-map-marker-alt"></i> Delivery Address</h3>
                    <div class="address-options">
                        <label class="form-group">
                            <input type="radio" id="new_address" name="address_type" value="new" checked>
                            <span class="custom-radio"></span> Add New Address
                        </label>
                        <label class="form-group">
                            <input type="radio" id="saved_address" name="address_type" value="saved">
                            <span class="custom-radio"></span> Use Saved Address
                        </label>
                    </div>
                    <div id="newAddressForm">
                        <div class="form-group"><label for="full_name">Full Name:</label><input type="text" id="full_name" name="full_name" required></div>
                        <div class="form-group"><label for="address">Address:</label><input type="text" id="address" name="address" required></div>
                        <div class="form-group"><label for="city">City:</label><input type="text" id="city" name="city" required></div>
                        <div class="form-group"><label for="state">State:</label><input type="text" id="state" name="state" required></div>
                        <div class="form-group"><label for="zip_code">ZIP Code:</label><input type="text" id="zip_code" name="zip_code" required></div>
                        <div class="form-group"><label for="phone">Phone Number:</label><input type="tel" id="phone" name="phone" required></div>
                    </div>
                    <div id="savedAddressForm" style="display: none;">
                        <div class="form-group">
                            <label for="saved_address_select">Select Saved Address:</label>
                            <select id="saved_address_select" name="saved_address_id">
                                <option value="">Select an address</option>
                            </select>
                        </div>
                    </div>
                </section>
                <button type="submit" class="btn btn-primary animated-glow">Confirm Order <i class="fas fa-arrow-right"></i></button>
            </form>
        </div>
    <?php else: ?>
        <div class="empty-cart-message animated-fade-in">
            <p>Your cart is empty. <a href="categories.php">Continue shopping</a></p>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?> 