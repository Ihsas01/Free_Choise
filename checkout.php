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

<div class="container">
    <h2>Checkout</h2>
    
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
        <div class="checkout-container">
            <div class="order-summary">
                <h3>Order Summary</h3>
                <?php while($item = $cart_result->fetch_assoc()): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <div class="checkout-item">
                        <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['product_name']; ?>" class="checkout-item-image">
                        <div class="checkout-item-details">
                            <h4><?php echo $item['product_name']; ?></h4>
                            <p>Quantity: <?php echo $item['quantity']; ?></p>
                            <p>Price: Rs. <?php echo number_format($item['price'], 2); ?></p>
                            <p>Subtotal: Rs. <?php echo number_format($subtotal, 2); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
                
                <div class="total-amount">
                    <h4>Total: Rs. <?php echo number_format($total, 2); ?></h4>
                </div>
            </div>

            <form action="process_order.php" method="POST" class="checkout-form" id="checkoutForm">
                <div class="payment-method">
                    <h3>Select Payment Method</h3>
                    <div class="payment-options">
                        <div class="payment-option">
                            <input type="radio" id="cod" name="payment_method" value="cod" required>
                            <label for="cod">Cash on Delivery</label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="card" name="payment_method" value="card" required>
                            <label for="card">Online Payment</label>
                        </div>
                    </div>
                </div>

                <!-- Card Payment Section -->
                <div id="cardPaymentSection" class="payment-section" style="display: none;">
                    <div class="card-type">
                        <h4>Select Card Type</h4>
                        <div class="card-options">
                            <div class="card-option">
                                <input type="radio" id="debit" name="card_type" value="debit">
                                <label for="debit">Debit Card</label>
                            </div>
                            <div class="card-option">
                                <input type="radio" id="credit" name="card_type" value="credit">
                                <label for="credit">Credit Card</label>
                            </div>
                            <div class="card-option">
                                <input type="radio" id="mastercard" name="card_type" value="mastercard">
                                <label for="mastercard">Master Card</label>
                            </div>
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
                </div>

                <!-- Address Section -->
                <div class="address-section">
                    <h3>Delivery Address</h3>
                    <div class="address-options">
                        <div class="form-group">
                            <input type="radio" id="new_address" name="address_type" value="new" checked>
                            <label for="new_address">Add New Address</label>
                        </div>
                        <div class="form-group">
                            <input type="radio" id="saved_address" name="address_type" value="saved">
                            <label for="saved_address">Use Saved Address</label>
                        </div>
                    </div>

                    <div id="newAddressForm">
                        <div class="form-group">
                            <label for="full_name">Full Name:</label>
                            <input type="text" id="full_name" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                        <div class="form-group">
                            <label for="city">City:</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                        <div class="form-group">
                            <label for="state">State:</label>
                            <input type="text" id="state" name="state" required>
                        </div>
                        <div class="form-group">
                            <label for="zip_code">ZIP Code:</label>
                            <input type="text" id="zip_code" name="zip_code" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number:</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                    </div>

                    <div id="savedAddressForm" style="display: none;">
                        <div class="form-group">
                            <label for="saved_address_select">Select Saved Address:</label>
                            <select id="saved_address_select" name="saved_address_id">
                                <!-- This will be populated with saved addresses -->
                                <option value="">Select an address</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Confirm Order</button>
            </form>
        </div>
    <?php else: ?>
        <p>Your cart is empty. <a href="categories.php">Continue shopping</a></p>
    <?php endif; ?>
</div>

<style>
.checkout-container {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 2rem;
    margin-top: 2rem;
}

.order-summary {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
}

.checkout-item {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #dee2e6;
}

.checkout-item-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 4px;
}

.checkout-form {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.payment-options, .card-options, .address-options {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.payment-option, .card-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.total-amount {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px solid #dee2e6;
}

.btn-primary {
    margin-top: 1rem;
    padding: 0.75rem 1.5rem;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.payment-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.address-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment method toggle
    const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
    const cardPaymentSection = document.getElementById('cardPaymentSection');

    paymentMethodInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value === 'card') {
                cardPaymentSection.style.display = 'block';
            } else {
                cardPaymentSection.style.display = 'none';
            }
        });
    });

    // Address type toggle
    const addressTypeInputs = document.querySelectorAll('input[name="address_type"]');
    const newAddressForm = document.getElementById('newAddressForm');
    const savedAddressForm = document.getElementById('savedAddressForm');

    addressTypeInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value === 'new') {
                newAddressForm.style.display = 'block';
                savedAddressForm.style.display = 'none';
            } else {
                newAddressForm.style.display = 'none';
                savedAddressForm.style.display = 'block';
            }
        });
    });

    // Card number formatting
    const cardNumberInput = document.getElementById('card_number');
    cardNumberInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        let formattedValue = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formattedValue += ' ';
            }
            formattedValue += value[i];
        }
        e.target.value = formattedValue;
    });

    // Expiry date formatting
    const expiryInput = document.getElementById('expiry');
    expiryInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.slice(0, 2) + '/' + value.slice(2);
        }
        e.target.value = value;
    });

    // CVV formatting
    const cvvInput = document.getElementById('cvv');
    cvvInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '').slice(0, 4);
    });

    // Handle form submission
    const checkoutForm = document.getElementById('checkoutForm');
    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = 'Processing...';
        submitBtn.disabled = true;

        // Send form data
        fetch('process_order.php', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show order summary modal
                showOrderSummary(data.order_summary);
            } else {
                alert(data.message || 'Failed to place order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your order');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        });
    });

    // Function to show order summary
    function showOrderSummary(summary) {
        // Create modal HTML
        const modalHTML = `
            <div class="modal" id="orderSummaryModal">
                <div class="modal-content">
                    <h2>Order Confirmation</h2>
                    <div class="order-details">
                        <p><strong>Order ID:</strong> #${summary.order_id}</p>
                        <p><strong>Payment Method:</strong> ${summary.payment_method}</p>
                        <h3>Order Items:</h3>
                        <div class="order-items">
                            ${summary.items.map(item => `
                                <div class="order-item">
                                    <p>${item.product_name}</p>
                                    <p>Quantity: ${item.quantity}</p>
                                    <p>Price: Rs. ${item.price}</p>
                                    <p>Subtotal: Rs. ${(item.price * item.quantity).toFixed(2)}</p>
                                </div>
                            `).join('')}
                        </div>
                        <div class="order-total">
                            <h3>Total Amount: Rs. ${summary.total.toFixed(2)}</h3>
                        </div>
                        ${summary.address ? `
                            <div class="shipping-address">
                                <h3>Shipping Address:</h3>
                                <p>${summary.address.full_name}</p>
                                <p>${summary.address.address}</p>
                                <p>${summary.address.city}, ${summary.address.state} ${summary.address.zip_code}</p>
                                <p>Phone: ${summary.address.phone}</p>
                            </div>
                        ` : ''}
                    </div>
                    <div class="modal-buttons">
                        <button onclick="window.location.href='index.php'" class="btn btn-primary">Continue Shopping</button>
                    </div>
                </div>
            </div>
        `;

        // Add modal to page
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // Add modal styles
        const modalStyles = `
            <style>
                .modal {
                    display: block;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0,0,0,0.5);
                    z-index: 1000;
                }

                .modal-content {
                    position: relative;
                    background-color: #fff;
                    margin: 5% auto;
                    padding: 20px;
                    width: 80%;
                    max-width: 600px;
                    border-radius: 8px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                }

                .order-details {
                    margin: 20px 0;
                }

                .order-item {
                    border-bottom: 1px solid #eee;
                    padding: 10px 0;
                }

                .order-total {
                    margin-top: 20px;
                    padding-top: 20px;
                    border-top: 2px solid #eee;
                }

                .shipping-address {
                    margin-top: 20px;
                    padding: 15px;
                    background-color: #f8f9fa;
                    border-radius: 4px;
                }

                .modal-buttons {
                    margin-top: 20px;
                    text-align: center;
                }
            </style>
        `;
        document.head.insertAdjacentHTML('beforeend', modalStyles);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?> 