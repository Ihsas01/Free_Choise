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

<div class="container">
    <div class="contact-content">
        <div class="contact-info">
            <h2>Contact Us</h2>
            <p>Have questions or concerns? We're here to help!</p>
            
            <div class="contact-details">
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h3>Address</h3>
                        <p>123 Shopping Street</p>
                        <p>New York, NY 10001</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h3>Phone</h3>
                        <p>+1 234 567 890</p>
                        <p>Mon-Fri: 9am-6pm</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h3>Email</h3>
                        <p>info@freechoise.com</p>
                        <p>support@freechoise.com</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-form-container">
            <h3>Send us a Message</h3>
            <?php if($message): ?>
                <div class="error-message"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" class="contact-form">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                
                <button type="submit" class="btn">Send Message</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 