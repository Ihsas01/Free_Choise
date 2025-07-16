<?php
require_once 'includes/header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
<style>
body, .terms-bg {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #e0e7ff 0%, #f0fdfa 100%);
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}
.terms-bg {
    position: relative;
    z-index: 1;
}
.terms-bg .floating-bg {
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
.terms-scroll-progress {
    position: fixed;
    top: 0;
    left: 0;
    width: 0%;
    height: 4px;
    background: linear-gradient(90deg, #6366f1 0%, #06b6d4 100%);
    z-index: 50;
    transition: width 0.2s;
}
.terms-sticky-nav {
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
.terms-sticky-nav.scrolled {
    box-shadow: 0 6px 24px 0 #6366f133;
    background: rgba(255,255,255,0.97);
}
.terms-sticky-nav ul {
    display: flex;
    gap: 1.5rem;
    list-style: none;
    margin: 0;
    padding: 0;
}
.terms-sticky-nav a {
    color: #334155;
    font-weight: 600;
    text-decoration: none;
    font-size: 1rem;
    padding: 0.3rem 0.7rem;
    border-radius: 6px;
    transition: background 0.2s, color 0.2s;
}
.terms-sticky-nav a.active, .terms-sticky-nav a:hover {
    background: linear-gradient(90deg, #6366f1 0%, #06b6d4 100%);
    color: #fff;
}
.terms-hero {
    text-align: center;
    margin: 2.5rem 0 2rem 0;
    position: relative;
    z-index: 2;
}
.terms-title {
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
.terms-title i {
    color: #6366f1;
    margin-right: 0.4em;
    font-size: 1.1em;
    transition: transform 0.5s cubic-bezier(0.4,0,0.2,1);
    will-change: transform;
}
.terms-subtitle {
    color: #334155;
    font-size: 1.15em;
    opacity: 0.85;
    font-family: 'Inter', sans-serif;
}
.terms-section {
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
.terms-section.visible {
    opacity: 1;
    transform: translateY(0);
}
.terms-section h2 {
    color: #6366f1;
    font-size: 1.25em;
    margin-top: 0;
    margin-bottom: 0.5em;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
}
.terms-section p {
    color: #334155;
    font-size: 1.05em;
    margin-bottom: 1.1em;
    line-height: 1.7;
    font-family: 'Inter', sans-serif;
}
.terms-section a {
    color: #06b6d4;
    text-decoration: underline;
    transition: color 0.2s;
}
.terms-section a:hover {
    color: #6366f1;
}
.terms-cta-btn {
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
.terms-cta-btn:hover {
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
.terms-back-to-top {
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
.terms-back-to-top.visible {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}
.terms-back-to-top:hover {
    background: linear-gradient(90deg, #06b6d4 0%, #6366f1 100%);
    box-shadow: 0 8px 32px #06b6d466;
}
@media (max-width: 600px) {
    .terms-title { font-size: 1.5em; }
    .terms-section { padding: 1.2rem 0.7rem; }
    .terms-back-to-top { right: 1rem; bottom: 1rem; width: 44px; height: 44px; font-size: 1.1em; }
    .terms-bg .floating-bg { width: 500px; height: 500px; }
}
</style>

<div class="terms-bg">
    <div class="floating-bg"></div>
    <div class="terms-scroll-progress" id="termsProgress"></div>
    <nav class="terms-sticky-nav" id="termsNav">
        <ul>
            <li><a href="#acceptance" class="terms-nav-link">Acceptance</a></li>
            <li><a href="#changes" class="terms-nav-link">Changes</a></li>
            <li><a href="#responsibilities" class="terms-nav-link">Responsibilities</a></li>
            <li><a href="#ip" class="terms-nav-link">Intellectual Property</a></li>
            <li><a href="#liability" class="terms-nav-link">Liability</a></li>
            <li><a href="#law" class="terms-nav-link">Law</a></li>
            <li><a href="#contact" class="terms-nav-link">Contact</a></li>
        </ul>
    </nav>
    <div class="container terms-page">
        <section class="terms-hero">
            <h1 class="terms-title sexy-gradient-text"><i class="fas fa-file-contract" id="termsHeroIcon"></i> Terms & Conditions</h1>
            <p class="terms-subtitle">Please read these terms and conditions carefully before using our website.</p>
        </section>
        <section class="terms-section" id="acceptance">
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing and using FREE CHOISE, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>
        </section>
        <section class="terms-section" id="changes">
            <h2>2. Changes to Terms</h2>
            <p>We reserve the right to modify these terms at any time. Any changes will be effective immediately upon posting on this page. Your continued use of the site constitutes acceptance of those changes.</p>
        </section>
        <section class="terms-section" id="responsibilities">
            <h2>3. User Responsibilities</h2>
            <p>You agree to use the website only for lawful purposes and in a way that does not infringe the rights of, restrict or inhibit anyone else's use and enjoyment of the website.</p>
        </section>
        <section class="terms-section" id="ip">
            <h2>4. Intellectual Property</h2>
            <p>All content, trademarks, and data on this website, including but not limited to software, databases, text, graphics, icons, and hyperlinks are the property of FREE CHOISE or its licensors.</p>
        </section>
        <section class="terms-section" id="liability">
            <h2>5. Limitation of Liability</h2>
            <p>FREE CHOISE will not be liable for any direct, indirect, incidental, special, or consequential damages resulting from the use or inability to use the website or for the cost of procurement of substitute goods and services.</p>
        </section>
        <section class="terms-section" id="law">
            <h2>6. Governing Law</h2>
            <p>These terms shall be governed by and construed in accordance with the laws of Sri Lanka. Any disputes relating to these terms and conditions will be subject to the exclusive jurisdiction of the courts of Sri Lanka.</p>
        </section>
        <section class="terms-section" id="contact">
            <h2>7. Contact Us</h2>
            <p>If you have any questions about these Terms, please <a href="contact.php">contact us</a>.</p>
            <a href="contact.php" class="terms-cta-btn">Contact Support</a>
        </section>
    </div>
    <button class="terms-back-to-top" id="termsBackToTop" title="Back to top"><i class="fas fa-arrow-up"></i></button>
</div>

<script>
// Smooth scroll for nav links
const navLinks = document.querySelectorAll('.terms-nav-link');
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
const termsNav = document.getElementById('termsNav');
function updateActiveNav() {
    const sections = document.querySelectorAll('.terms-section');
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
        termsNav.classList.add('scrolled');
    } else {
        termsNav.classList.remove('scrolled');
    }
}
window.addEventListener('scroll', updateActiveNav);
updateActiveNav();
// Fade-in animation for sections
function revealSections() {
    document.querySelectorAll('.terms-section').forEach(section => {
        const rect = section.getBoundingClientRect();
        if (rect.top < window.innerHeight - 60) {
            section.classList.add('visible');
        }
    });
}
window.addEventListener('scroll', revealSections);
window.addEventListener('DOMContentLoaded', revealSections);
// Scroll progress bar
const progressBar = document.getElementById('termsProgress');
window.addEventListener('scroll', function() {
    const scrollTop = window.scrollY;
    const docHeight = document.body.scrollHeight - window.innerHeight;
    const percent = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
    progressBar.style.width = percent + '%';
});
// Back to top button
const backToTop = document.getElementById('termsBackToTop');
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
const heroIcon = document.getElementById('termsHeroIcon');
window.addEventListener('scroll', function() {
    if (heroIcon) {
        const offset = window.scrollY * 0.15;
        heroIcon.style.transform = `translateY(${offset}px) scale(1.1)`;
    }
});
</script>

<?php require_once 'includes/footer.php'; ?> 