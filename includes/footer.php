    </main>
    
    <style>
    /* Modern Footer Styles */
    :root {
        --footer-bg: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
        --footer-text: #e2e8f0;
        --footer-text-muted: #a0aec0;
        --footer-accent: #667eea;
        --footer-hover: #4c51bf;
        --footer-border: rgba(255, 255, 255, 0.1);
        --footer-shadow: 0 -8px 32px rgba(0, 0, 0, 0.1);
        --transition-smooth: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        --transition-fast: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .main-footer {
        background: var(--footer-bg);
        color: var(--footer-text);
        position: relative;
        overflow: hidden;
        margin-top: 4rem;
    }

    .main-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    }

    .main-footer::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="footer-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23footer-pattern)"/></svg>');
        opacity: 0.3;
        pointer-events: none;
    }

    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 3rem;
        padding: 4rem 2rem 2rem;
        max-width: 1400px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }

    .footer-section {
        opacity: 0;
        transform: translateY(30px);
        transition: var(--transition-smooth);
    }

    .footer-section.animate {
        opacity: 1;
        transform: translateY(0);
    }

    .footer-section h3 {
        color: white;
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        position: relative;
        letter-spacing: -0.02em;
    }

    .footer-section h3::before {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 40px;
        height: 3px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
        transition: var(--transition-smooth);
    }

    .footer-section:hover h3::before {
        width: 60px;
    }

    .footer-section p {
        color: var(--footer-text-muted);
        font-size: 0.95rem;
        line-height: 1.7;
        margin-bottom: 1rem;
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
        margin-bottom: 0.8rem;
        opacity: 0;
        transform: translateX(-20px);
        transition: var(--transition-smooth);
    }

    .footer-section.animate ul li {
        opacity: 1;
        transform: translateX(0);
    }

    .footer-section ul li:nth-child(1) { transition-delay: 0.1s; }
    .footer-section ul li:nth-child(2) { transition-delay: 0.2s; }
    .footer-section ul li:nth-child(3) { transition-delay: 0.3s; }
    .footer-section ul li:nth-child(4) { transition-delay: 0.4s; }
    .footer-section ul li:nth-child(5) { transition-delay: 0.5s; }
    .footer-section ul li:nth-child(6) { transition-delay: 0.6s; }

    .footer-section a {
        color: var(--footer-text-muted);
        text-decoration: none;
        font-size: 0.95rem;
        transition: var(--transition-smooth);
        position: relative;
        display: inline-block;
        padding: 0.3rem 0;
    }

    .footer-section a::before {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: var(--transition-smooth);
        border-radius: 1px;
    }

    .footer-section a:hover {
        color: white;
        transform: translateX(8px);
    }

    .footer-section a:hover::before {
        width: 100%;
    }

    .social-links {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .social-links a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        color: var(--footer-text-muted);
        font-size: 1.2rem;
        transition: var(--transition-smooth);
        position: relative;
        overflow: hidden;
    }

    .social-links a::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: var(--transition-smooth);
        z-index: 1;
    }

    .social-links a:hover::before {
        left: 0;
    }

    .social-links a:hover {
        color: white;
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .social-links a i {
        position: relative;
        z-index: 2;
    }

    .footer-bottom {
        text-align: center;
        padding: 2rem;
        border-top: 1px solid var(--footer-border);
        position: relative;
        z-index: 2;
        background: rgba(0, 0, 0, 0.2);
    }

    .footer-bottom p {
        color: var(--footer-text-muted);
        font-size: 0.9rem;
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
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        animation: footerFloat 8s ease-in-out infinite;
    }

    .footer-shape:nth-child(1) {
        width: 60px;
        height: 60px;
        top: 20%;
        left: 10%;
        animation-delay: 0s;
    }

    .footer-shape:nth-child(2) {
        width: 40px;
        height: 40px;
        top: 60%;
        right: 15%;
        animation-delay: 2s;
    }

    .footer-shape:nth-child(3) {
        width: 80px;
        height: 80px;
        bottom: 30%;
        left: 20%;
        animation-delay: 4s;
    }

    @keyframes footerFloat {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-20px) rotate(180deg);
        }
    }

    /* Newsletter section */
    .newsletter-section {
        grid-column: 1 / -1;
        text-align: center;
        padding: 2rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        backdrop-filter: blur(10px);
        border: 1px solid var(--footer-border);
        margin-bottom: 2rem;
    }

    .newsletter-section h3 {
        margin-bottom: 1rem;
    }

    .newsletter-form {
        display: flex;
        gap: 1rem;
        max-width: 400px;
        margin: 0 auto;
        flex-wrap: wrap;
    }

    .newsletter-form input[type="email"] {
        flex: 1;
        padding: 0.8rem 1.2rem;
        border: 1px solid var(--footer-border);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 0.9rem;
        transition: var(--transition-smooth);
        min-width: 200px;
    }

    .newsletter-form input[type="email"]::placeholder {
        color: var(--footer-text-muted);
    }

    .newsletter-form input[type="email"]:focus {
        outline: none;
        border-color: var(--footer-accent);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .newsletter-form button {
        padding: 0.8rem 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition-smooth);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }

    .newsletter-form button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr;
            gap: 2rem;
            padding: 3rem 1.5rem 1.5rem;
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
            font-size: 1.2rem;
        }

        .footer-section p,
        .footer-section a {
            font-size: 0.9rem;
        }

        .social-links a {
            width: 2.5rem;
            height: 2.5rem;
            font-size: 1rem;
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
            transform: translateY(30px);
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
                <h3>About FREE CHOISE</h3>
                <p>Your one-stop destination for all your shopping needs. We provide quality products, great prices, and excellent customer service to ensure your shopping experience is nothing short of amazing.</p>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="categories.php">Categories</a></li>
                    <li><a href="special-offers.php">Special Offers</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Contact Us</h3>
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
                <h3>Stay Updated</h3>
                <p>Subscribe to our newsletter for the latest offers and updates</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Enter your email address" required>
                    <button type="submit">Subscribe</button>
                </form>
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
            rootMargin: '0px 0px -50px 0px'
        });

        footerSections.forEach(section => {
            footerObserver.observe(section);
        });

        // Newsletter form handling
        const newsletterForm = document.querySelector('.newsletter-form');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = this.querySelector('input[type="email"]').value;
                
                // Add success animation
                const button = this.querySelector('button');
                const originalText = button.textContent;
                button.textContent = 'Subscribed!';
                button.style.background = 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)';
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                    this.querySelector('input[type="email"]').value = '';
                }, 2000);
            });
        }

        // Social media hover effects
        document.querySelectorAll('.social-links a').forEach((link, index) => {
            link.style.animationDelay = `${index * 0.1}s`;
        });

        // Footer link hover effects
        document.querySelectorAll('.footer-section a').forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(8px)';
            });
            
            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });

        // Parallax effect for floating shapes
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const shapes = document.querySelectorAll('.footer-shape');
            
            shapes.forEach((shape, index) => {
                const speed = 0.5 + (index * 0.1);
                shape.style.transform = `translateY(${scrolled * speed}px) rotate(${scrolled * 0.1}deg)`;
            });
        });

        // Add glass morphism effect on scroll
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const footerRect = footer.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            
            if (footerRect.top < windowHeight) {
                const opacity = Math.min((windowHeight - footerRect.top) / 200, 0.1);
                footer.style.background = `linear-gradient(135deg, rgba(26, 32, 44, ${0.9 + opacity}) 0%, rgba(45, 55, 72, ${0.9 + opacity}) 100%)`;
            }
        });

        // Smooth reveal animation for footer elements
        const footerElements = document.querySelectorAll('.footer-section, .newsletter-section');
        footerElements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, index * 200);
        });
    });
    </script>
    
    <script src="assets/js/script.js"></script>
</body>
</html> 