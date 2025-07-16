<?php
require_once 'includes/header.php';
?>

<div class="container privacy-page animated-fade-in">
    <section class="privacy-hero">
        <h1 class="privacy-title sexy-gradient-text"><i class="fas fa-user-secret"></i> Privacy Policy</h1>
        <p class="privacy-subtitle">Your privacy is important to us. Please read our policy below.</p>
    </section>
    <section class="privacy-content card animated-slide-up">
        <h2>1. Introduction</h2>
        <p>This Privacy Policy explains how FREE CHOISE collects, uses, and protects your personal information when you use our website.</p>
        <h2>2. Information We Collect</h2>
        <p>We may collect personal information such as your name, email address, phone number, shipping address, and payment details when you register, place an order, or contact us.</p>
        <h2>3. How We Use Your Information</h2>
        <p>Your information is used to process orders, provide customer support, improve our services, and send you updates or promotional offers (if you opt in).</p>
        <h2>4. Cookies & Tracking</h2>
        <p>We use cookies and similar technologies to enhance your browsing experience, analyze site traffic, and personalize content. You can control cookies through your browser settings.</p>
        <h2>5. Data Security</h2>
        <p>We implement industry-standard security measures to protect your data. However, no method of transmission over the Internet is 100% secure.</p>
        <h2>6. Sharing Your Information</h2>
        <p>We do not sell, trade, or rent your personal information to third parties. We may share data with trusted partners who assist in operating our website and conducting our business, as long as they agree to keep this information confidential.</p>
        <h2>7. Your Rights</h2>
        <p>You have the right to access, update, or delete your personal information. To exercise these rights, please <a href="contact.php">contact us</a>.</p>
        <h2>8. Changes to This Policy</h2>
        <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated effective date.</p>
        <h2>9. Contact Us</h2>
        <p>If you have any questions about this Privacy Policy, please <a href="contact.php">contact us</a>.</p>
    </section>
</div>

<style>
.privacy-page {
    max-width: 900px;
    margin: 0 auto 3rem auto;
    padding: 2.5rem 1.2rem 1.5rem 1.2rem;
}
.privacy-hero {
    text-align: center;
    margin-bottom: 2.5rem;
    animation: fadeInDown 1s cubic-bezier(0.4,0,0.2,1);
}
.privacy-title {
    font-size: 2.5em;
    font-weight: 800;
    background: linear-gradient(90deg, var(--primary-color), var(--accent-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-fill-color: transparent;
    margin-bottom: 0.5rem;
    letter-spacing: 1px;
}
.privacy-title i {
    color: var(--primary-color);
    margin-right: 0.4em;
}
.privacy-subtitle {
    color: var(--secondary-color);
    font-size: 1.15em;
    opacity: 0.85;
}
.privacy-content.card {
    background: var(--white);
    box-shadow: var(--shadow);
    border-radius: 16px;
    padding: 2.2rem 2rem 2rem 2rem;
    transition: box-shadow 0.3s, transform 0.3s;
    position: relative;
    animation: slideInUp 1.2s forwards;
}
.privacy-content h2 {
    color: var(--primary-color);
    font-size: 1.25em;
    margin-top: 1.5em;
    margin-bottom: 0.5em;
    font-weight: 700;
}
.privacy-content p {
    color: var(--text-color);
    font-size: 1.05em;
    margin-bottom: 1.1em;
    line-height: 1.7;
}
.privacy-content a {
    color: var(--primary-color);
    text-decoration: underline;
    transition: color 0.2s;
}
.privacy-content a:hover {
    color: var(--accent-color);
}
@media (max-width: 600px) {
    .privacy-page { padding: 1.2rem 0.3rem 1rem 0.3rem; }
    .privacy-title { font-size: 1.5em; }
    .privacy-content.card { padding: 1.2rem 0.7rem; }
}
</style>

<?php require_once 'includes/footer.php'; ?> 