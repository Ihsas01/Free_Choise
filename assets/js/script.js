document.addEventListener('DOMContentLoaded', function() {
    // Handle add to cart form submissions
    const addToCartForms = document.querySelectorAll('.add-to-cart-form');
    
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const productId = formData.get('product_id');
            const quantity = formData.get('quantity');
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Adding...';
            submitBtn.disabled = true;
            
            // Send AJAX request
            fetch('cart.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert('Product added to cart successfully!');
                    
                    // Update cart count if it exists
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
                    }
                } else {
                    // Show error message
                    alert(data.message || 'Failed to add product to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the product to cart');
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
        });
    });

    // Handle quantity input changes
    const quantityInputs = document.querySelectorAll('input[name="quantity"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Ensure quantity is at least 1
            if (this.value < 1) {
                this.value = 1;
            }
        });
    });
});

window.onbeforeunload = function () {
  window.scrollTo(0, 0);
}; 

// --- CHECKOUT PAGE ANIMATIONS & INTERACTIONS ---
document.addEventListener('DOMContentLoaded', function() {
    // Animate elements on scroll
    function animateOnScroll() {
        const animatedEls = document.querySelectorAll('.animated-fade-in, .animated-slide-up, .animated-slide-in, .animated-bounce-in');
        animatedEls.forEach(el => {
            const rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight - 60) {
                el.style.animationPlayState = 'running';
            }
        });
    }
    animateOnScroll();
    window.addEventListener('scroll', animateOnScroll);

    // Payment method toggle
    const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
    const cardPaymentSection = document.getElementById('cardPaymentSection');
    if (paymentMethodInputs.length && cardPaymentSection) {
        paymentMethodInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.value === 'card') {
                    cardPaymentSection.style.display = 'block';
                    cardPaymentSection.classList.add('animated-fade-in');
                } else {
                    cardPaymentSection.style.display = 'none';
                }
            });
        });
    }

    // Address type toggle
    const addressTypeInputs = document.querySelectorAll('input[name="address_type"]');
    const newAddressForm = document.getElementById('newAddressForm');
    const savedAddressForm = document.getElementById('savedAddressForm');
    if (addressTypeInputs.length && newAddressForm && savedAddressForm) {
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
    }

    // Card number formatting
    const cardNumberInput = document.getElementById('card_number');
    if (cardNumberInput) {
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
    }

    // Expiry date formatting
    const expiryInput = document.getElementById('expiry');
    if (expiryInput) {
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2);
            }
            e.target.value = value;
        });
    }

    // CVV formatting
    const cvvInput = document.getElementById('cvv');
    if (cvvInput) {
        cvvInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 4);
        });
    }

    // Checkout form AJAX (if present)
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Processing...';
            submitBtn.disabled = true;
            fetch('process_order.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
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
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
        });
    }

    // Order summary modal
    function showOrderSummary(summary) {
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
                        <button onclick="window.location.href='index.php'" class="btn btn-primary animated-glow">Continue Shopping</button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        document.body.style.overflow = 'hidden';
        document.getElementById('orderSummaryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.remove();
                document.body.style.overflow = '';
            }
        });
    }
}); 