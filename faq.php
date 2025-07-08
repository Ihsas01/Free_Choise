<?php
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="faq-hero">
    <div class="hero-background">
        <div class="hero-overlay"></div>
        <div class="floating-elements">
            <div class="floating-element element-1">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="floating-element element-2">
                <i class="fas fa-search"></i>
            </div>
            <div class="floating-element element-3">
                <i class="fas fa-lightbulb"></i>
            </div>
            <div class="floating-element element-4">
                <i class="fas fa-comments"></i>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title" data-aos="fade-up">Frequently Asked Questions</h1>
            <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="200">Find answers to common questions about our services, products, and policies</p>
            <div class="search-container" data-aos="fade-up" data-aos-delay="400">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="faq-search" placeholder="Search for questions...">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Categories -->
<section class="faq-categories" data-aos="fade-up">
    <div class="container">
        <div class="categories-grid">
            <div class="category-card active" data-category="all" data-aos="zoom-in" data-aos-delay="100">
                <div class="category-icon">
                    <i class="fas fa-th-large"></i>
                </div>
                <h3>All Questions</h3>
                <span class="question-count">12</span>
            </div>
            
            <div class="category-card" data-category="ordering" data-aos="zoom-in" data-aos-delay="200">
                <div class="category-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3>Ordering & Payment</h3>
                <span class="question-count">3</span>
            </div>
            
            <div class="category-card" data-category="shipping" data-aos="zoom-in" data-aos-delay="300">
                <div class="category-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3>Shipping & Delivery</h3>
                <span class="question-count">3</span>
            </div>
            
            <div class="category-card" data-category="returns" data-aos="zoom-in" data-aos-delay="400">
                <div class="category-icon">
                    <i class="fas fa-undo"></i>
                </div>
                <h3>Returns & Refunds</h3>
                <span class="question-count">3</span>
            </div>
            
            <div class="category-card" data-category="account" data-aos="zoom-in" data-aos-delay="500">
                <div class="category-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3>Account & Security</h3>
                <span class="question-count">3</span>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Content -->
<section class="faq-content-section" data-aos="fade-up">
    <div class="container">
        <div class="faq-content">
            <!-- Ordering & Payment -->
            <div class="faq-section" data-category="ordering">
                <div class="section-header">
                    <h2 class="section-title" data-aos="fade-up">Ordering & Payment</h2>
                    <p class="section-subtitle" data-aos="fade-up" data-aos-delay="200">Everything you need to know about placing orders and payment methods</p>
                </div>
                
                <div class="faq-items">
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="100">
                        <div class="faq-question">
                            <h3>How do I place an order?</h3>
                            <div class="faq-toggle">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <p>To place an order, simply browse our products, add items to your cart, and proceed to checkout. You'll need to create an account or log in to complete your purchase. Our streamlined checkout process makes it easy to review your order and enter your shipping and payment information.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                        <div class="faq-question">
                            <h3>What payment methods do you accept?</h3>
                            <div class="faq-toggle">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <p>We accept all major credit cards (Visa, MasterCard, American Express), PayPal, and other secure payment methods. All transactions are encrypted and secure. We also offer flexible payment options including installment plans for eligible purchases.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                        <div class="faq-question">
                            <h3>Is my payment information secure?</h3>
                            <div class="faq-toggle">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <p>Yes, we use industry-standard SSL encryption to protect your payment information. We never store your complete credit card details on our servers. Your security is our top priority, and we follow strict security protocols to ensure your data is always protected.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping & Delivery -->
            <div class="faq-section" data-category="shipping">
                <div class="section-header">
                    <h2 class="section-title" data-aos="fade-up">Shipping & Delivery</h2>
                    <p class="section-subtitle" data-aos="fade-up" data-aos-delay="200">Learn about our shipping options and delivery times</p>
                </div>
                
                <div class="faq-items">
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="100">
                        <div class="faq-question">
                            <h3>How long does shipping take?</h3>
                            <div class="faq-toggle">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <p>Standard shipping typically takes 3-5 business days. Express shipping is available for 1-2 business day delivery. We also offer same-day delivery for select locations. You'll receive tracking information once your order ships.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                        <div class="faq-question">
                            <h3>Do you offer free shipping?</h3>
                            <div class="faq-toggle">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <p>Yes, we offer free shipping on all orders over Rs. 50. For orders under Rs. 50, standard shipping rates apply. We also have special promotions throughout the year with free shipping on all orders.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                        <div class="faq-question">
                            <h3>Do you ship internationally?</h3>
                            <div class="faq-toggle">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <p>Yes, we ship to most countries worldwide. International shipping rates and delivery times vary by location. We handle all customs documentation and provide tracking for international orders. Delivery typically takes 7-14 business days for international orders.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Returns & Refunds -->
            <div class="faq-section" data-category="returns">
                <div class="section-header">
                    <h2 class="section-title" data-aos="fade-up">Returns & Refunds</h2>
                    <p class="section-subtitle" data-aos="fade-up" data-aos-delay="200">Understanding our return policy and refund process</p>
                </div>
                
                <div class="faq-items">
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="100">
                        <div class="faq-question">
                            <h3>What is your return policy?</h3>
                            <div class="faq-toggle">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <p>We accept returns within 30 days of delivery. Items must be unused and in their original packaging. Please contact our customer service to initiate a return. We provide a prepaid return label for eligible returns to make the process as easy as possible.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                        <div class="faq-question">
                            <h3>How long do refunds take?</h3>
                            <div class="faq-toggle">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <p>Refunds are typically processed within 5-7 business days after we receive the returned item. The refund will be issued to your original payment method. You'll receive an email confirmation once the refund is processed.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                        <div class="faq-question">
                            <h3>Do I have to pay for return shipping?</h3>
                            <div class="faq-toggle">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <p>Return shipping is free for items that arrived damaged or were sent in error. For other returns, customers are responsible for return shipping costs. We provide detailed return instructions and can assist with any questions about the return process.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account & Security -->
            <div class="faq-section" data-category="account">
                <div class="section-header">
                    <h2 class="section-title" data-aos="fade-up">Account & Security</h2>
                    <p class="section-subtitle" data-aos="fade-up" data-aos-delay="200">Managing your account and ensuring your security</p>
                </div>
                
                <div class="faq-items">
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="100">
                        <div class="faq-question">
                            <h3>How do I create an account?</h3>
                            <div class="faq-toggle">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <p>Click on the "Register" link in the top right corner of the website. Fill in your details and follow the instructions to create your account. The registration process is quick and secure, and you'll have access to your account immediately.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                        <div class="faq-question">
                            <h3>I forgot my password. What should I do?</h3>
                            <div class="faq-toggle">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <p>Click on the "Login" link and then click "Forgot Password". Enter your email address and we'll send you instructions to reset your password. The reset link will be valid for 24 hours for security purposes.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                        <div class="faq-question">
                            <h3>How can I update my account information?</h3>
                            <div class="faq-toggle">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <p>Log in to your account and go to the "Profile" section. Here you can update your personal information, shipping address, and other account details. All changes are saved automatically and you can update your information at any time.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="faq-contact-section" data-aos="fade-up">
    <div class="container">
        <div class="contact-content">
            <div class="contact-text" data-aos="fade-right">
                <h2>Still have questions?</h2>
                <p>Can't find the answer you're looking for? Our customer support team is here to help you with any questions or concerns you may have.</p>
                <div class="contact-features">
                    <div class="feature-item">
                        <i class="fas fa-clock"></i>
                        <span>24/7 Support</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-comments"></i>
                        <span>Live Chat</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-envelope"></i>
                        <span>Email Support</span>
                    </div>
                </div>
            </div>
            <div class="contact-actions" data-aos="fade-left">
                <a href="contact.php" class="btn btn-primary">
                    <span class="btn-text">Contact Us</span>
                    <span class="btn-icon">
                        <i class="fas fa-envelope"></i>
                    </span>
                </a>
                <a href="chat.php" class="btn btn-outline">
                    <span class="btn-text">Live Chat</span>
                    <span class="btn-icon">
                        <i class="fas fa-comments"></i>
                    </span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- AOS Library for animations -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
// Initialize AOS
AOS.init({
    duration: 1000,
    easing: 'ease-in-out',
    once: true,
    offset: 100
});

// FAQ Accordion functionality
document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', function() {
        const faqItem = this.parentElement;
        const answer = faqItem.querySelector('.faq-answer');
        const toggle = this.querySelector('.faq-toggle i');
        
        // Close other open items
        document.querySelectorAll('.faq-item.active').forEach(item => {
            if (item !== faqItem) {
                item.classList.remove('active');
                item.querySelector('.faq-answer').style.maxHeight = '0px';
                item.querySelector('.faq-toggle i').className = 'fas fa-plus';
            }
        });
        
        // Toggle current item
        faqItem.classList.toggle('active');
        
        if (faqItem.classList.contains('active')) {
            answer.style.maxHeight = answer.scrollHeight + 'px';
            toggle.className = 'fas fa-minus';
        } else {
            answer.style.maxHeight = '0px';
            toggle.className = 'fas fa-plus';
        }
    });
});

// Category filtering
document.querySelectorAll('.category-card').forEach(card => {
    card.addEventListener('click', function() {
        const category = this.getAttribute('data-category');
        
        // Update active category
        document.querySelectorAll('.category-card').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        
        // Show/hide FAQ sections
        document.querySelectorAll('.faq-section').forEach(section => {
            if (category === 'all' || section.getAttribute('data-category') === category) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    });
});

// Search functionality
document.getElementById('faq-search').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    
    document.querySelectorAll('.faq-item').forEach(item => {
        const question = item.querySelector('h3').textContent.toLowerCase();
        const answer = item.querySelector('p').textContent.toLowerCase();
        
        if (question.includes(searchTerm) || answer.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

// Floating elements animation
const floatingElements = document.querySelectorAll('.floating-element');
floatingElements.forEach((element, index) => {
    element.style.animationDelay = `${index * 0.5}s`;
});

// Always scroll to top on page load (including refresh)
window.onbeforeunload = function () {
    window.scrollTo(0, 0);
};
</script>

<?php require_once 'includes/footer.php'; ?> 