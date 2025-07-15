<?php
require_once 'includes/header.php';
?>

<div class="container terms-page animated-fade-in">
    <section class="terms-hero">
        <h1 class="terms-title sexy-gradient-text"><i class="fas fa-file-contract"></i> Terms & Conditions</h1>
        <p class="terms-subtitle">Please read these terms and conditions carefully before using our website.</p>
    </section>
    <section class="terms-content card animated-slide-up">
        <h2>1. Acceptance of Terms</h2>
        <p>By accessing and using FREE CHOISE, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>
        <h2>2. Changes to Terms</h2>
        <p>We reserve the right to modify these terms at any time. Any changes will be effective immediately upon posting on this page. Your continued use of the site constitutes acceptance of those changes.</p>
        <h2>3. User Responsibilities</h2>
        <p>You agree to use the website only for lawful purposes and in a way that does not infringe the rights of, restrict or inhibit anyone else's use and enjoyment of the website.</p>
        <h2>4. Intellectual Property</h2>
        <p>All content, trademarks, and data on this website, including but not limited to software, databases, text, graphics, icons, and hyperlinks are the property of FREE CHOISE or its licensors.</p>
        <h2>5. Limitation of Liability</h2>
        <p>FREE CHOISE will not be liable for any direct, indirect, incidental, special, or consequential damages resulting from the use or inability to use the website or for the cost of procurement of substitute goods and services.</p>
        <h2>6. Governing Law</h2>
        <p>These terms shall be governed by and construed in accordance with the laws of Sri Lanka. Any disputes relating to these terms and conditions will be subject to the exclusive jurisdiction of the courts of Sri Lanka.</p>
        <h2>7. Contact Us</h2>
        <p>If you have any questions about these Terms, please <a href="contact.php">contact us</a>.</p>
    </section>
</div>

<style>
.terms-page {
    max-width: 900px;
    margin: 0 auto 3rem auto;
    padding: 2.5rem 1.2rem 1.5rem 1.2rem;
}
.terms-hero {
    text-align: center;
    margin-bottom: 2.5rem;
    animation: fadeInDown 1s cubic-bezier(0.4,0,0.2,1);
}
.terms-title {
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
.terms-title i {
    color: var(--primary-color);
    margin-right: 0.4em;
}
.terms-subtitle {
    color: var(--secondary-color);
    font-size: 1.15em;
    opacity: 0.85;
}
.terms-content.card {
    background: var(--white);
    box-shadow: var(--shadow);
    border-radius: 16px;
    padding: 2.2rem 2rem 2rem 2rem;
    transition: box-shadow 0.3s, transform 0.3s;
    position: relative;
    animation: slideInUp 1.2s forwards;
}
.terms-content h2 {
    color: var(--primary-color);
    font-size: 1.25em;
    margin-top: 1.5em;
    margin-bottom: 0.5em;
    font-weight: 700;
}
.terms-content p {
    color: var(--text-color);
    font-size: 1.05em;
    margin-bottom: 1.1em;
    line-height: 1.7;
}
.terms-content a {
    color: var(--primary-color);
    text-decoration: underline;
    transition: color 0.2s;
}
.terms-content a:hover {
    color: var(--accent-color);
}
@media (max-width: 600px) {
    .terms-page { padding: 1.2rem 0.3rem 1rem 0.3rem; }
    .terms-title { font-size: 1.5em; }
    .terms-content.card { padding: 1.2rem 0.7rem; }
}
</style>

<?php require_once 'includes/footer.php'; ?> 