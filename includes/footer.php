    </main>
    
    <style>
    /* Modern Footer Styles */
    :root {
        --footer-bg: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        --footer-text: #f1f5f9;
        --footer-text-muted: #94a3b8;
        --footer-accent: #667eea;
        --footer-hover: #4c51bf;
        --footer-border: rgba(255, 255, 255, 0.08);
        --footer-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
        --transition-smooth: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        --transition-fast: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        --newsletter-bg: rgba(255,255,255,0.06);
        --newsletter-border: rgba(255,255,255,0.12);
        --newsletter-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .main-footer {
        background: var(--footer-bg);
        color: var(--footer-text);
        position: relative;
        overflow: hidden;
        margin-top: 3rem;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    .main-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
    }

    .main-footer::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="footer-pattern" width="25" height="25" patternUnits="userSpaceOnUse"><circle cx="12.5" cy="12.5" r="0.8" fill="rgba(255,255,255,0.03)"/></pattern></defs><rect width="100" height="100" fill="url(%23footer-pattern)"/></svg>');
        opacity: 0.4;
        pointer-events: none;
    }

    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        padding: 3rem 2rem 2rem;
        max-width: 1400px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }

    .footer-section {
        opacity: 0;
        transform: translateY(20px);
        transition: var(--transition-smooth);
    }

    .footer-section.animate {
        opacity: 1;
        transform: translateY(0);
    }

    .footer-section h3 {
        color: white;
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        position: relative;
        letter-spacing: -0.01em;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .footer-section h3::before {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 0;
        width: 30px;
        height: 2px;
        background: var(--gradient-primary);
        border-radius: 1px;
        transition: var(--transition-smooth);
    }

    .footer-section:hover h3::before {
        width: 45px;
    }

    .footer-section p {
        color: var(--footer-text-muted);
        font-size: 0.85rem;
        line-height: 1.6;
        margin-bottom: 0.8rem;
        transition: var(--transition-fast);
    }

    .footer-section:hover p {
        color: var(--footer-text);
    }

    .footer-section ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-section ul li {
        margin-bottom: 0.6rem;
        opacity: 0;
        transform: translateX(-15px);
        transition: var(--transition-smooth);
    }

    .footer-section.animate ul li {
        opacity: 1;
        transform: translateX(0);
    }

    .footer-section ul li:nth-child(1) { transition-delay: 0.1s; }
    .footer-section ul li:nth-child(2) { transition-delay: 0.15s; }
    .footer-section ul li:nth-child(3) { transition-delay: 0.2s; }
    .footer-section ul li:nth-child(4) { transition-delay: 0.25s; }
    .footer-section ul li:nth-child(5) { transition-delay: 0.3s; }
    .footer-section ul li:nth-child(6) { transition-delay: 0.35s; }

    .footer-section a {
        color: var(--footer-text-muted);
        text-decoration: none;
        font-size: 0.8rem;
        transition: var(--transition-smooth);
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0;
    }

    .footer-section a::before {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 0;
        height: 1px;
        background: var(--gradient-primary);
        transition: var(--transition-smooth);
        border-radius: 0.5px;
    }

    .footer-section a:hover {
        color: white;
        transform: translateX(5px);
    }

    .footer-section a:hover::before {
        width: 100%;
    }

    .social-links {
        display: flex;
        gap: 0.8rem;
        margin-top: 1.2rem;
    }

    .social-links a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        color: var(--footer-text-muted);
        font-size: 1rem;
        transition: var(--transition-smooth);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .social-links a::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--gradient-primary);
        transition: var(--transition-smooth);
        z-index: 1;
    }

    .social-links a:hover::before {
        left: 0;
    }

    .social-links a:hover {
        color: white;
        transform: translateY(-3px) scale(1.1);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.25);
    }

    .social-links a i {
        position: relative;
        z-index: 2;
    }

    .footer-bottom {
        text-align: center;
        padding: 1.5rem 2rem;
        border-top: 1px solid var(--footer-border);
        position: relative;
        z-index: 2;
        background: rgba(0, 0, 0, 0.15);
    }

    .footer-bottom p {
        color: var(--footer-text-muted);
        font-size: 0.8rem;
        margin: 0;
        transition: var(--transition-fast);
    }

    .footer-bottom:hover p {
        color: var(--footer-text);
    }

    /* Floating elements */
    .footer-floating {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        pointer-events: none;
        z-index: 1;
    }

    .footer-shape {
        position: absolute;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 50%;
        animation: footerFloat 10s ease-in-out infinite;
    }

    .footer-shape:nth-child(1) {
        width: 40px;
        height: 40px;
        top: 15%;
        left: 8%;
        animation-delay: 0s;
    }

    .footer-shape:nth-child(2) {
        width: 30px;
        height: 30px;
        top: 65%;
        right: 12%;
        animation-delay: 3s;
    }

    .footer-shape:nth-child(3) {
        width: 50px;
        height: 50px;
        bottom: 25%;
        left: 15%;
        animation-delay: 6s;
    }

    @keyframes footerFloat {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-15px) rotate(180deg);
        }
    }

    /* Newsletter section */
    .newsletter-section {
        grid-column: 1 / -1;
        text-align: center;
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 16px;
        backdrop-filter: blur(10px);
        border: 1px solid var(--footer-border);
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .newsletter-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
        transition: var(--transition-smooth);
    }

    .newsletter-section:hover::before {
        left: 100%;
    }

    .newsletter-section h3 {
        margin-bottom: 0.8rem;
        justify-content: center;
    }

    .newsletter-form {
        display: flex;
        gap: 0.6rem;
        margin-top: 1.2rem;
        background: var(--newsletter-bg);
        border: 1px solid var(--newsletter-border);
        border-radius: 12px;
        padding: 0.4rem 0.6rem;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.08);
        align-items: center;
        max-width: 350px;
        transition: var(--transition-smooth);
        margin-left: auto;
        margin-right: auto;
    }

    .newsletter-form:hover {
        border-color: rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.12);
    }

    .newsletter-form input[type="email"] {
        flex: 1;
        border: none;
        background: transparent;
        color: var(--footer-text);
        font-size: 0.85rem;
        padding: 0.6rem 0.5rem;
        outline: none;
    }

    .newsletter-form input[type="email"]::placeholder {
        color: var(--footer-text-muted);
    }

    .newsletter-form button {
        background: var(--footer-accent);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.1rem;
        font-weight: 600;
        font-size: 0.8rem;
        cursor: pointer;
        transition: var(--transition-smooth);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .newsletter-form button:hover {
        background: var(--footer-hover);
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .newsletter-success {
        background: var(--newsletter-success);
        color: #fff;
        border-radius: 8px;
        padding: 0.6rem 1.1rem;
        margin-top: 0.8rem;
        font-weight: 600;
        font-size: 0.8rem;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
        animation: fadeSlideIn 0.6s;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
    }

    /* Social icon animation */
    .social-links a {
        position: relative;
        z-index: 1;
        overflow: hidden;
    }

    .social-links a .fa {
        position: relative;
        z-index: 2;
        transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
    }

    .social-links a:hover .fa {
        transform: scale(1.15) rotate(-5deg);
    }

    /* Footer fade/slide-in for all elements */
    .footer-section, .newsletter-form, .social-links a {
        opacity: 0;
        transform: translateY(20px);
        transition: var(--transition-smooth);
    }

    .footer-section.animate, .newsletter-form.animate, .social-links a.animate {
        opacity: 1;
        transform: translateY(0);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr;
            gap: 1.5rem;
            padding: 2.5rem 1.5rem 1.5rem;
        }

        .footer-section {
            text-align: center;
        }

        .footer-section h3::before {
            left: 50%;
            transform: translateX(-50%);
        }

        .social-links {
            justify-content: center;
        }

        .newsletter-form {
            flex-direction: column;
            align-items: center;
            gap: 0.8rem;
        }

        .newsletter-form input[type="email"] {
            width: 100%;
            min-width: unset;
        }
    }

    @media (max-width: 480px) {
        .footer-content {
            padding: 2rem 1rem 1rem;
        }

        .footer-section h3 {
            font-size: 1rem;
        }

        .footer-section p,
        .footer-section a {
            font-size: 0.75rem;
        }

        .social-links a {
            width: 2.2rem;
            height: 2.2rem;
            font-size: 0.9rem;
        }

        .newsletter-form {
            padding: 0.3rem 0.5rem;
        }

        .newsletter-form button {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 600px) {
        .footer-content { 
            gap: 1rem; 
            padding: 2rem 0.5rem 1rem; 
        }
        .newsletter-form { 
            flex-direction: column; 
            align-items: stretch; 
        }
        .newsletter-form button { 
            width: 100%; 
        }
    }

    /* Scroll-triggered animations */
    .footer-section {
        will-change: transform, opacity;
    }

    /* Loading animation */
    @keyframes footerSlideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeSlideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .main-footer {
        animation: footerSlideIn 0.8s ease-out;
    }
    </style>

    <footer class="main-footer">
        <div class="footer-floating">
            <div class="footer-shape"></div>
            <div class="footer-shape"></div>
            <div class="footer-shape"></div>
        </div>
        
        <div class="footer-content">
            <div class="footer-section">
                <h3><i class="fas fa-shopping-bag"></i> About FREE CHOISE</h3>
                <p>Your one-stop destination for all your shopping needs. We provide quality products, great prices, and excellent customer service to ensure your shopping experience is nothing short of amazing.</p>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-link"></i> Quick Links</h3>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="categories.php"><i class="fas fa-th-large"></i> Categories</a></li>
                    <li><a href="special-offers.php"><i class="fas fa-tags"></i> Special Offers</a></li>
                    <li><a href="about.php"><i class="fas fa-info-circle"></i> About Us</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                    <li><a href="faq.php"><i class="fas fa-question-circle"></i> FAQ</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><i class="fas fa-address-card"></i> Contact Us</h3>
                <p><i class="fas fa-envelope"></i> info@freechoise.com</p>
                <p><i class="fas fa-phone"></i> +1 234 567 890</p>
                <p><i class="fas fa-map-marker-alt"></i> 123 Shopping Street, City</p>
                <div class="social-links">
                    <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div class="newsletter-section">
                <h3><i class="fas fa-bell"></i> Stay Updated</h3>
                <p>Subscribe to our newsletter for the latest offers and updates</p>
                <div class="newsletter-form">
                    <input type="email" placeholder="Your email address" required>
                    <button type="button" onclick="showNewsletterSuccess()">
                        <i class="fas fa-paper-plane"></i> Subscribe
                    </button>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> FREE CHOISE. All rights reserved. | Designed with ❤️ for amazing shopping experiences</p>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const footerSections = document.querySelectorAll('.footer-section');
        const footer = document.querySelector('.main-footer');
        
        // Intersection Observer for footer animations
        const footerObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -30px 0px'
        });

        footerSections.forEach(section => {
            footerObserver.observe(section);
        });

        // Newsletter form handling with enhanced UX
        const newsletterForm = document.querySelector('.newsletter-form');
        if (newsletterForm) {
            const input = newsletterForm.querySelector('input[type="email"]');
            const button = newsletterForm.querySelector('button');
            
            // Email validation
            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
            
            input.addEventListener('input', function() {
                const email = this.value.trim();
                if (email && validateEmail(email)) {
                    button.style.background = 'var(--newsletter-success)';
                    button.style.transform = 'scale(1.02)';
                } else {
                    button.style.background = 'var(--footer-accent)';
                    button.style.transform = 'scale(1)';
                }
            });
            
            button.addEventListener('click', function() {
                const email = input.value.trim();
                
                if (!email) {
                    showNotification('Please enter your email address', 'error');
                    return;
                }
                
                if (!validateEmail(email)) {
                    showNotification('Please enter a valid email address', 'error');
                    return;
                }
                
                // Add success animation
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i> Subscribed!';
                button.style.background = 'var(--newsletter-success)';
                button.style.transform = 'scale(1.05)';
                
                showNotification('Thank you for subscribing!', 'success');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.style.background = 'var(--footer-accent)';
                    button.style.transform = 'scale(1)';
                    input.value = '';
                }, 2000);
            });
        }

        // Enhanced social media hover effects
        document.querySelectorAll('.social-links a').forEach((link, index) => {
            link.style.animationDelay = `${index * 0.1}s`;
            
            // Add hover sound effect (optional)
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px) scale(1.1)';
            });
            
            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Enhanced footer link hover effects
        document.querySelectorAll('.footer-section a').forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
            });
            
            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });

        // Parallax effect for floating shapes with improved performance
        let ticking = false;
        function updateParallax() {
            const scrolled = window.pageYOffset;
            const shapes = document.querySelectorAll('.footer-shape');
            
            shapes.forEach((shape, index) => {
                const speed = 0.3 + (index * 0.1);
                shape.style.transform = `translateY(${scrolled * speed}px) rotate(${scrolled * 0.05}deg)`;
            });
            ticking = false;
        }

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        });

        // Add glass morphism effect on scroll
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const footerRect = footer.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            
            if (footerRect.top < windowHeight) {
                const opacity = Math.min((windowHeight - footerRect.top) / 200, 0.1);
                footer.style.background = `linear-gradient(135deg, rgba(30, 41, 59, ${0.9 + opacity}) 0%, rgba(51, 65, 85, ${0.9 + opacity}) 100%)`;
            }
        });

        // Smooth reveal animation for footer elements with stagger
        const footerElements = document.querySelectorAll('.footer-section, .newsletter-section');
        footerElements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, index * 150);
        });

        // Add hover effects to newsletter section
        const newsletterSection = document.querySelector('.newsletter-section');
        if (newsletterSection) {
            newsletterSection.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
            });
            
            newsletterSection.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        }
    });

    // Enhanced newsletter form success animation
    function showNewsletterSuccess() {
        const form = document.querySelector('.newsletter-form');
        if (form) {
            form.style.display = 'none';
            const success = document.createElement('div');
            success.className = 'newsletter-success';
            success.innerHTML = '<i class="fas fa-check-circle"></i> Thank you for subscribing!';
            form.parentNode.appendChild(success);
            
            // Add animation
            success.style.animation = 'fadeSlideIn 0.6s ease-out';
        }
    }

    // Enhanced notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 0.8rem 1.2rem;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            transform: translateX(100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            max-width: 280px;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 4000);
    }
    </script>
    
    <script src="assets/js/script.js"></script>
</body>
</html> 