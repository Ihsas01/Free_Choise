<?php
require_once 'includes/header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
<style>
body, .privacy-bg {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #e0e7ff 0%, #f0fdfa 100%);
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}
.privacy-bg {
    position: relative;
    z-index: 1;
}
.privacy-bg .floating-bg {
    position: fixed;
    top: -120px;
    left: 50%;
    transform: translateX(-50%);
    width: 900px;
    height: 900px;
    background: radial-gradient(circle at 60% 40%, #6366f1cc 0%, #06b6d4bb 60%, transparent 100%);
    filter: blur(80px) saturate(1.2);
    opacity: 0.25;
    z-index: 0;
    pointer-events: none;
    animation: floatBg 18s ease-in-out infinite alternate;
}
@keyframes floatBg {
    0% { transform: translateX(-50%) scale(1) translateY(0); }
    100% { transform: translateX(-50%) scale(1.08) translateY(40px); }
}
.privacy-scroll-progress {
    position: fixed;
    top: 0;
    left: 0;
    width: 0%;
    height: 4px;
    background: linear-gradient(90deg, #6366f1 0%, #06b6d4 100%);
    z-index: 50;
    transition: width 0.2s;
}
.privacy-sticky-nav {
    position: sticky;
    top: 0;
    z-index: 20;
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(8px);
    border-bottom: 1px solid #e5e7eb;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    display: flex;
    justify-content: center;
    padding: 0.5rem 0;
    transition: background 0.3s, box-shadow 0.3s;
}
.privacy-sticky-nav.scrolled {
    box-shadow: 0 6px 24px 0 #6366f133;
    background: rgba(255,255,255,0.97);
}
.privacy-sticky-nav ul {
    display: flex;
    gap: 1.5rem;
    list-style: none;
    margin: 0;
    padding: 0;
}
.privacy-sticky-nav a {
    color: #334155;
    font-weight: 600;
    text-decoration: none;
    font-size: 1rem;
    padding: 0.3rem 0.7rem;
    border-radius: 6px;
    transition: background 0.2s, color 0.2s;
}
.privacy-sticky-nav a.active, .privacy-sticky-nav a:hover {
    background: linear-gradient(90deg, #6366f1 0%, #06b6d4 100%);
    color: #fff;
}
.privacy-hero {
    text-align: center;
    margin: 2.5rem 0 2rem 0;
    position: relative;
    z-index: 2;
}
.privacy-title {
    font-size: 2.5em;
    font-weight: 800;
    background: linear-gradient(90deg, #6366f1 0%, #06b6d4 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-fill-color: transparent;
    margin-bottom: 0.5rem;
    letter-spacing: 1px;
    font-family: 'Inter', sans-serif;
    display: inline-block;
    position: relative;
}
.privacy-title i {
    color: #06b6d4;
    margin-right: 0.4em;
    font-size: 1.1em;
    transition: transform 0.5s cubic-bezier(0.4,0,0.2,1);
    will-change: transform;
}
.privacy-subtitle {
    color: #334155;
    font-size: 1.15em;
    opacity: 0.85;
    font-family: 'Inter', sans-serif;
}
.privacy-section {
    background: rgba(255,255,255,0.7);
    border-radius: 1.25rem;
    box-shadow: 0 8px 40px 0 #6366f122, 0 1.5px 8px 0 #06b6d422;
    margin: 2.2rem 0;
    padding: 2.2rem 2rem 2rem 2rem;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 0.7s cubic-bezier(0.4,0,0.2,1), transform 0.7s cubic-bezier(0.4,0,0.2,1);
    backdrop-filter: blur(12px) saturate(1.2);
    border: 1.5px solid #e0e7ff88;
}
.privacy-section.visible {
    opacity: 1;
    transform: translateY(0);
}
.privacy-section h2 {
    color: #06b6d4;
    font-size: 1.25em;
    margin-top: 0;
    margin-bottom: 0.5em;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
}
.privacy-section p {
    color: #334155;
    font-size: 1.05em;
    margin-bottom: 1.1em;
    line-height: 1.7;
    font-family: 'Inter', sans-serif;
}
.privacy-section a {
    color: #6366f1;
    text-decoration: underline;
    transition: color 0.2s;
}
.privacy-section a:hover {
    color: #06b6d4;
}
.privacy-cta-btn {
    display: inline-block;
    background: linear-gradient(90deg, #6366f1 0%, #06b6d4 100%);
    color: #fff;
    font-weight: 700;
    font-size: 1.1em;
    border-radius: 30px;
    box-shadow: 0 4px 16px #6366f122;
    transition: background 0.3s, box-shadow 0.3s, transform 0.2s;
    position: relative;
    outline: none;
    border: none;
    margin-top: 1.5em;
    padding: 0.9em 2.2em;
    cursor: pointer;
    animation: none;
}
.privacy-cta-btn:hover {
    animation: pulse 0.7s;
    background: linear-gradient(90deg, #06b6d4 0%, #6366f1 100%);
    box-shadow: 0 8px 32px #06b6d466;
    transform: translateY(-2px) scale(1.03);
}
@keyframes pulse {
    0% { transform: scale(1); box-shadow: 0 4px 16px #6366f122; }
    50% { transform: scale(1.07); box-shadow: 0 8px 32px #06b6d466; }
    100% { transform: scale(1); box-shadow: 0 4px 16px #6366f122; }
}
.privacy-back-to-top {
    position: fixed;
    right: 2.2rem;
    bottom: 2.2rem;
    z-index: 100;
    background: linear-gradient(90deg, #6366f1 0%, #06b6d4 100%);
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 52px;
    height: 52px;
    box-shadow: 0 4px 24px #6366f144;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5em;
    cursor: pointer;
    opacity: 0;
    transform: translateY(40px);
    pointer-events: none;
    transition: opacity 0.4s, transform 0.4s;
}
.privacy-back-to-top.visible {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}
.privacy-back-to-top:hover {
    background: linear-gradient(90deg, #06b6d4 0%, #6366f1 100%);
    box-shadow: 0 8px 32px #06b6d466;
}
@media (max-width: 600px) {
    .privacy-title { font-size: 1.5em; }
    .privacy-section { padding: 1.2rem 0.7rem; }
    .privacy-back-to-top { right: 1rem; bottom: 1rem; width: 44px; height: 44px; font-size: 1.1em; }
    .privacy-bg .floating-bg { width: 500px; height: 500px; }
}
</style>

<div class="privacy-bg">
    <div class="floating-bg"></div>
    <div class="privacy-scroll-progress" id="privacyProgress"></div>
    <nav class="privacy-sticky-nav" id="privacyNav">
        <ul>
            <li><a href="#intro" class="privacy-nav-link">Introduction</a></li>
            <li><a href="#info" class="privacy-nav-link">Information</a></li>
            <li><a href="#use" class="privacy-nav-link">Use</a></li>
            <li><a href="#cookies" class="privacy-nav-link">Cookies</a></li>
            <li><a href="#security" class="privacy-nav-link">Security</a></li>
            <li><a href="#sharing" class="privacy-nav-link">Sharing</a></li>
            <li><a href="#rights" class="privacy-nav-link">Your Rights</a></li>
            <li><a href="#changes" class="privacy-nav-link">Changes</a></li>
            <li><a href="#contact" class="privacy-nav-link">Contact</a></li>
        </ul>
    </nav>
    <div class="container privacy-page">
        <section class="privacy-hero">
            <h1 class="privacy-title sexy-gradient-text"><i class="fas fa-user-secret" id="privacyHeroIcon"></i> Privacy Policy</h1>
            <p class="privacy-subtitle">Your privacy is important to us. Please read our policy below.</p>
        </section>
        <section class="privacy-section" id="intro">
            <h2>1. Introduction</h2>
            <p>This Privacy Policy explains how FREE CHOISE collects, uses, and protects your personal information when you use our website.</p>
        </section>
        <section class="privacy-section" id="info">
            <h2>2. Information We Collect</h2>
            <p>We may collect personal information such as your name, email address, phone number, shipping address, and payment details when you register, place an order, or contact us.</p>
        </section>
        <section class="privacy-section" id="use">
            <h2>3. How We Use Your Information</h2>
            <p>Your information is used to process orders, provide customer support, improve our services, and send you updates or promotional offers (if you opt in).</p>
        </section>
        <section class="privacy-section" id="cookies">
            <h2>4. Cookies & Tracking</h2>
            <p>We use cookies and similar technologies to enhance your browsing experience, analyze site traffic, and personalize content. You can control cookies through your browser settings.</p>
        </section>
        <section class="privacy-section" id="security">
            <h2>5. Data Security</h2>
            <p>We implement industry-standard security measures to protect your data. However, no method of transmission over the Internet is 100% secure.</p>
        </section>
        <section class="privacy-section" id="sharing">
            <h2>6. Sharing Your Information</h2>
            <p>We do not sell, trade, or rent your personal information to third parties. We may share data with trusted partners who assist in operating our website and conducting our business, as long as they agree to keep this information confidential.</p>
        </section>
        <section class="privacy-section" id="rights">
            <h2>7. Your Rights</h2>
            <p>You have the right to access, update, or delete your personal information. To exercise these rights, please <a href="contact.php">contact us</a>.</p>
        </section>
        <section class="privacy-section" id="changes">
            <h2>8. Changes to This Policy</h2>
            <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated effective date.</p>
        </section>
        <section class="privacy-section" id="contact">
            <h2>9. Contact Us</h2>
            <p>If you have any questions about this Privacy Policy, please <a href="contact.php">contact us</a>.</p>
            <a href="contact.php" class="privacy-cta-btn">Contact Support</a>
        </section>
    </div>
    <button class="privacy-back-to-top" id="privacyBackToTop" title="Back to top"><i class="fas fa-arrow-up"></i></button>
</div>

<script>
// Smooth scroll for nav links
const navLinks = document.querySelectorAll('.privacy-nav-link');
navLinks.forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            window.scrollTo({
                top: target.getBoundingClientRect().top + window.scrollY - 70,
                behavior: 'smooth'
            });
        }
    });
});
// Sticky nav active state and shadow
const privacyNav = document.getElementById('privacyNav');
function updateActiveNav() {
    const sections = document.querySelectorAll('.privacy-section');
    let index = sections.length - 1;
    for (let i = 0; i < sections.length; i++) {
        if (window.scrollY + 80 < sections[i].offsetTop) {
            index = i - 1;
            break;
        }
    }
    navLinks.forEach(link => link.classList.remove('active'));
    if (index >= 0) navLinks[index].classList.add('active');
    // Add shadow to nav on scroll
    if (window.scrollY > 10) {
        privacyNav.classList.add('scrolled');
    } else {
        privacyNav.classList.remove('scrolled');
    }
}
window.addEventListener('scroll', updateActiveNav);
updateActiveNav();
// Fade-in animation for sections
function revealSections() {
    document.querySelectorAll('.privacy-section').forEach(section => {
        const rect = section.getBoundingClientRect();
        if (rect.top < window.innerHeight - 60) {
            section.classList.add('visible');
        }
    });
}
window.addEventListener('scroll', revealSections);
window.addEventListener('DOMContentLoaded', revealSections);
// Scroll progress bar
const progressBar = document.getElementById('privacyProgress');
window.addEventListener('scroll', function() {
    const scrollTop = window.scrollY;
    const docHeight = document.body.scrollHeight - window.innerHeight;
    const percent = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
    progressBar.style.width = percent + '%';
});
// Back to top button
const backToTop = document.getElementById('privacyBackToTop');
window.addEventListener('scroll', function() {
    if (window.scrollY > 300) {
        backToTop.classList.add('visible');
    } else {
        backToTop.classList.remove('visible');
    }
});
backToTop.addEventListener('click', function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});
// Parallax hero icon
const heroIcon = document.getElementById('privacyHeroIcon');
window.addEventListener('scroll', function() {
    if (heroIcon) {
        const offset = window.scrollY * 0.15;
        heroIcon.style.transform = `translateY(${offset}px) scale(1.1)`;
    }
});
</script>

<?php require_once 'includes/footer.php'; ?> 