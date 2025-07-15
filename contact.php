<?php
require_once 'includes/header.php';

$message = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message_text = $_POST['message'];

    $query = "INSERT INTO contact_inquiries (name, email, subject, message) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $name, $email, $subject, $message_text);
    
    if($stmt->execute()) {
        $success = 'Your message has been sent successfully. We will get back to you soon.';
    } else {
        $message = 'Error sending message. Please try again.';
    }
}
?>

<!-- Hero Section -->
<section class="contact-hero">
    <div class="hero-background">
        <div class="hero-overlay"></div>
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title" data-aos="fade-up">Get in Touch</h1>
            <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="200">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            <div class="hero-cta" data-aos="fade-up" data-aos-delay="400">
                <a href="#contact-form" class="btn btn-primary scroll-to-form">Start Conversation</a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Content -->
<div class="container">
    <div class="contact-content">
        <!-- Contact Information -->
        <div class="contact-info-section" data-aos="fade-right">
            <div class="contact-info-card">
                <div class="card-header">
                    <h2>Let's Connect</h2>
                    <p>We're here to help and answer any questions you might have. We look forward to hearing from you.</p>
                </div>
                
                <div class="contact-details">
                    <div class="contact-item" data-aos="zoom-in" data-aos-delay="100">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-text">
                            <h3>Visit Us</h3>
                            <p>123 main street colombo , Srilanka</p>
                        </div>
                    </div>
                    
                    <div class="contact-item" data-aos="zoom-in" data-aos-delay="200">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-text">
                            <h3>Call Us</h3>
                            <p>076 xxx xxxx</p>
                        </div>
                    </div>
                    
                    <div class="contact-item" data-aos="zoom-in" data-aos-delay="300">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-text">
                            <h3>Email Us</h3>
                            <p>mohamedihsas001@gmail.com</p>
                        </div>
                    </div>
                </div>

                <div class="social-links-contact" data-aos="fade-up" data-aos-delay="400">
                    <h3>Follow Us</h3>
                    <div class="social-icons">
                        <a href="#" class="social-icon" data-aos="zoom-in" data-aos-delay="500">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon" data-aos="zoom-in" data-aos-delay="600">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon" data-aos="zoom-in" data-aos-delay="700">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-icon" data-aos="zoom-in" data-aos-delay="800">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form-section" data-aos="fade-left">
            <div class="contact-form-card" id="contact-form">
                <div class="card-header">
                    <h3>Send us a Message</h3>
                    <p>Fill out the form below and we'll get back to you as soon as possible.</p>
                </div>

                <?php if($message): ?>
                    <div class="message error-message" data-aos="shake">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="message success-message" data-aos="fade-in">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="contact-form">
                    <div class="form-row">
                        <div class="form-group" data-aos="fade-up" data-aos-delay="100">
                            <label for="name">
                                <i class="fas fa-user"></i>
                                Your Name
                            </label>
                            <input type="text" id="name" name="name" required>
                            <div class="input-focus-border"></div>
                        </div>
                        
                        <div class="form-group" data-aos="fade-up" data-aos-delay="200">
                            <label for="email">
                                <i class="fas fa-envelope"></i>
                                Your Email
                            </label>
                            <input type="email" id="email" name="email" required>
                            <div class="input-focus-border"></div>
                        </div>
                    </div>
                    
                    <div class="form-group" data-aos="fade-up" data-aos-delay="300">
                        <label for="subject">
                            <i class="fas fa-tag"></i>
                            Subject
                        </label>
                        <input type="text" id="subject" name="subject" required>
                        <div class="input-focus-border"></div>
                    </div>
                    
                    <div class="form-group" data-aos="fade-up" data-aos-delay="400">
                        <label for="message">
                            <i class="fas fa-comment"></i>
                            Message
                        </label>
                        <textarea id="message" name="message" rows="6" required></textarea>
                        <div class="input-focus-border"></div>
                    </div>
                    
                    <div class="form-actions" data-aos="fade-up" data-aos-delay="500">
                        <button type="submit" class="btn btn-primary btn-submit">
                            <span class="btn-text">Send Message</span>
                            <span class="btn-icon">
                                <i class="fas fa-paper-plane"></i>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Map Section -->
<section class="map-section" data-aos="fade-up">
    <div class="container">
        <div class="map-container">
            <div class="map-placeholder">
                <div class="map-content">
                    <i class="fas fa-map-marked-alt"></i>
                    <h3>Our Location</h3>
                    <p>123 main street colombo , Srilanka</p>
                    <a href="#" class="btn btn-outline">Get Directions</a>
                </div>
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

// Smooth scroll to form
document.querySelector('.scroll-to-form').addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelector('#contact-form').scrollIntoView({
        behavior: 'smooth'
    });
});

// Form animations
document.querySelectorAll('.contact-form input, .contact-form textarea').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        if (!this.value) {
            this.parentElement.classList.remove('focused');
        }
    });
});

// Button animation
document.querySelector('.btn-submit').addEventListener('click', function() {
    this.classList.add('sending');
    setTimeout(() => {
        this.classList.remove('sending');
    }, 2000);
});
</script>

<?php require_once 'includes/footer.php'; ?> 