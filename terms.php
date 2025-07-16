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
    opacity: 0.22;
    z-index: 0;
    pointer-events: none;
    animation: floatBg 18s ease-in-out infinite alternate;
}
.terms-bg .floating-svg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    pointer-events: none;
    z-index: 1;
    opacity: 0.13;
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
    border-radius: 0 0 8px 8px;
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
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    outline: none;
}
.terms-sticky-nav a.active, .terms-sticky-nav a:hover, .terms-sticky-nav a:focus {
    background: linear-gradient(90deg, #6366f1 0%, #06b6d4 100%);
    color: #fff;
    box-shadow: 0 2px 12px #6366f144;
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
    background: linear-gradient(270deg, #6366f1, #06b6d4, #6366f1);
    background-size: 200% 200%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-fill-color: transparent;
    margin-bottom: 0.5rem;
    letter-spacing: 1px;
    font-family: 'Inter', sans-serif;
    display: inline-block;
    position: relative;
    animation: gradientMove 5s ease-in-out infinite alternate;
}
@keyframes gradientMove {
    0% { background-position: 0% 50%; }
    100% { background-position: 100% 50%; }
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
    box-shadow: 0 8px 40px 0 #6366f122, 0 1.5px 8px 0 #06b6d422, 0 0 0 4px transparent;
    margin: 2.2rem 0;
    padding: 2.2rem 2rem 2rem 2rem;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 0.7s cubic-bezier(0.4,0,0.2,1), transform 0.7s cubic-bezier(0.4,0,0.2,1), box-shadow 0.3s;
    backdrop-filter: blur(12px) saturate(1.2);
    border: 1.5px solid #e0e7ff88;
    position: relative;
    overflow: hidden;
}
.terms-section.visible {
    opacity: 1;
    transform: translateY(0);
    box-shadow: 0 12px 48px 0 #6366f144, 0 1.5px 8px 0 #06b6d422, 0 0 0 4px #6366f1cc;
}
.terms-section .section-badge {
    position: absolute;
    top: -22px;
    left: 24px;
    background: linear-gradient(90deg, #6366f1 0%, #06b6d4 100%);
    color: #fff;
    font-weight: 700;
    font-size: 1.1em;
    border-radius: 50%;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 12px #6366f144;
    border: 3px solid #fff;
    z-index: 2;
    animation: badgePulse 2.5s infinite alternate;
}
@keyframes badgePulse {
    0% { box-shadow: 0 2px 12px #6366f144; }
    100% { box-shadow: 0 8px 32px #06b6d466; }
}
.terms-section h2 {
    color: #6366f1;
    font-size: 1.25em;
    margin-top: 0;
    margin-bottom: 0.5em;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    padding-left: 56px;
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
    transition: background 0.3s, box-shadow 0.3s, transform 0.2s, box-shadow 0.4s;
    position: relative;
    outline: none;
    border: none;
    margin-top: 1.5em;
    padding: 0.9em 2.2em;
    cursor: pointer;
    animation: none;
    border: 2px solid transparent;
}
.terms-cta-btn:hover, .terms-cta-btn:focus {
    animation: pulse 0.7s;
    background: linear-gradient(90deg, #06b6d4 0%, #6366f1 100%);
    box-shadow: 0 8px 32px #06b6d466, 0 0 0 4px #6366f1cc;
    transform: translateY(-2px) scale(1.03);
    border: 2px solid #06b6d4;
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
.terms-divider {
    width: 100%;
    height: 36px;
    margin: -18px 0 0 0;
    z-index: 2;
    position: relative;
    pointer-events: none;
}
@media (max-width: 600px) {
    .terms-title { font-size: 1.5em; }
    .terms-section { padding: 1.2rem 0.7rem; }
    .terms-back-to-top { right: 1rem; bottom: 1rem; width: 44px; height: 44px; font-size: 1.1em; }
    .terms-bg .floating-bg { width: 500px; height: 500px; }
    .terms-section .section-badge { left: 10px; width: 36px; height: 36px; font-size: 1em; }
    .terms-section h2 { padding-left: 44px; }
}
</style>

<div class="terms-bg">
    <div class="floating-bg"></div>
    <svg class="floating-svg" viewBox="0 0 1440 320"><path fill="#6366f1" fill-opacity="0.2" d="M0,160L60,176C120,192,240,224,360,229.3C480,235,600,213,720,197.3C840,181,960,171,1080,176C1200,181,1320,203,1380,213.3L1440,224L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path></svg>
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
            <span class="section-badge">1</span>
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing and using FREE CHOISE, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>
        </section>
        <div class="terms-divider">
            <svg viewBox="0 0 1440 36" width="100%" height="36"><path fill="#06b6d4" fill-opacity="0.13" d="M0,16L60,20C120,24,240,32,360,32C480,32,600,24,720,20C840,16,960,16,1080,20C1200,24,1320,32,1380,34L1440,36L1440,36L1380,36C1320,36,1200,36,1080,36C960,36,840,36,720,36C600,36,480,36,360,36C240,36,120,36,60,36L0,36Z"></path></svg>
        </div>
        <section class="terms-section" id="changes">
            <span class="section-badge">2</span>
            <h2>2. Changes to Terms</h2>
            <p>We reserve the right to modify these terms at any time. Any changes will be effective immediately upon posting on this page. Your continued use of the site constitutes acceptance of those changes.</p>
        </section>
        <div class="terms-divider">
            <svg viewBox="0 0 1440 36" width="100%" height="36"><path fill="#6366f1" fill-opacity="0.13" d="M0,16L60,20C120,24,240,32,360,32C480,32,600,24,720,20C840,16,960,16,1080,20C1200,24,1320,32,1380,34L1440,36L1440,36L1380,36C1320,36,1200,36,1080,36C960,36,840,36,720,36C600,36,480,36,360,36C240,36,120,36,60,36L0,36Z"></path></svg>
        </div>
        <section class="terms-section" id="responsibilities">
            <span class="section-badge">3</span>
            <h2>3. User Responsibilities</h2>
            <p>You agree to use the website only for lawful purposes and in a way that does not infringe the rights of, restrict or inhibit anyone else's use and enjoyment of the website.</p>
        </section>
        <div class="terms-divider">
            <svg viewBox="0 0 1440 36" width="100%" height="36"><path fill="#06b6d4" fill-opacity="0.13" d="M0,16L60,20C120,24,240,32,360,32C480,32,600,24,720,20C840,16,960,16,1080,20C1200,24,1320,32,1380,34L1440,36L1440,36L1380,36C1320,36,1200,36,1080,36C960,36,840,36,720,36C600,36,480,36,360,36C240,36,120,36,60,36L0,36Z"></path></svg>
        </div>
        <section class="terms-section" id="ip">
            <span class="section-badge">4</span>
            <h2>4. Intellectual Property</h2>
            <p>All content, trademarks, and data on this website, including but not limited to software, databases, text, graphics, icons, and hyperlinks are the property of FREE CHOISE or its licensors.</p>
        </section>
        <div class="terms-divider">
            <svg viewBox="0 0 1440 36" width="100%" height="36"><path fill="#6366f1" fill-opacity="0.13" d="M0,16L60,20C120,24,240,32,360,32C480,32,600,24,720,20C840,16,960,16,1080,20C1200,24,1320,32,1380,34L1440,36L1440,36L1380,36C1320,36,1200,36,1080,36C960,36,840,36,720,36C600,36,480,36,360,36C240,36,120,36,60,36L0,36Z"></path></svg>
        </div>
        <section class="terms-section" id="liability">
            <span class="section-badge">5</span>
            <h2>5. Limitation of Liability</h2>
            <p>FREE CHOISE will not be liable for any direct, indirect, incidental, special, or consequential damages resulting from the use or inability to use the website or for the cost of procurement of substitute goods and services.</p>
        </section>
        <div class="terms-divider">
            <svg viewBox="0 0 1440 36" width="100%" height="36"><path fill="#06b6d4" fill-opacity="0.13" d="M0,16L60,20C120,24,240,32,360,32C480,32,600,24,720,20C840,16,960,16,1080,20C1200,24,1320,32,1380,34L1440,36L1440,36L1380,36C1320,36,1200,36,1080,36C960,36,840,36,720,36C600,36,480,36,360,36C240,36,120,36,60,36L0,36Z"></path></svg>
        </div>
        <section class="terms-section" id="law">
            <span class="section-badge">6</span>
            <h2>6. Governing Law</h2>
            <p>These terms shall be governed by and construed in accordance with the laws of Sri Lanka. Any disputes relating to these terms and conditions will be subject to the exclusive jurisdiction of the courts of Sri Lanka.</p>
        </section>
        <div class="terms-divider">
            <svg viewBox="0 0 1440 36" width="100%" height="36"><path fill="#6366f1" fill-opacity="0.13" d="M0,16L60,20C120,24,240,32,360,32C480,32,600,24,720,20C840,16,960,16,1080,20C1200,24,1320,32,1380,34L1440,36L1440,36L1380,36C1320,36,1200,36,1080,36C960,36,840,36,720,36C600,36,480,36,360,36C240,36,120,36,60,36L0,36Z"></path></svg>
        </div>
        <section class="terms-section" id="contact">
            <span class="section-badge">7</span>
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