<?php
session_start(); // Ensure session is started at the very beginning
require_once 'includes/db_config.php'; // Add database configuration

if(isset($_SESSION['user_id'])) {
    // Check if user is admin and redirect to admin dashboard
    if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
        header('Location: admin/dashboard.php');
        exit();
    } else {
        // Redirect regular users to index if already logged in
        header('Location: index.php');
        exit();
    }
}

$error = '';
$success = '';

// Check if user was redirected from registration
if(isset($_GET['registered']) && $_GET['registered'] == '1') {
    $success = 'Registration successful! You can now login with your credentials.';
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statement to prevent SQL injection
    $query = "SELECT user_id, username, password, is_admin FROM users WHERE email = ? LIMIT 1"; // Select user_id and is_admin
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if(password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Redirect based on admin status
            if($user['is_admin'] == 1) {
                header('Location: admin/dashboard.php');
                exit();
            } else {
                header('Location: index.php');
                exit();
            }
        } else {
            $error = 'Invalid email or password'; // Generic error for security
        }
    } else {
        $error = 'Invalid email or password'; // Generic error for security
    }
     $stmt->close();
}
// Close the database connection if it was opened and not closed
if (isset($conn) && $conn->ping()) {
    $conn->close();
}

require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Free Choice</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Modern CSS Variables */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-soft: 0 8px 32px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 12px 40px rgba(0, 0, 0, 0.15);
            --shadow-strong: 0 20px 60px rgba(0, 0, 0, 0.2);
            --transition-smooth: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-fast: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #2d3748;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* Login Page Container */
        .login-page {
            min-height: 100vh;
            background: var(--primary-gradient);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Enhanced Dynamic Background */
        .login-page::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.3) 0%, transparent 50%);
            animation: backgroundShift 15s ease-in-out infinite;
        }

        .login-page::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
            animation: float 20s ease-in-out infinite;
        }

        /* Enhanced Floating Elements */
        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .shape {
            position: absolute;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
            backdrop-filter: blur(5px);
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
            background: linear-gradient(45deg, rgba(255,255,255,0.2), rgba(255,255,255,0.1));
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
            background: linear-gradient(45deg, rgba(255,255,255,0.15), rgba(255,255,255,0.05));
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
        }

        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 40%;
            right: 20%;
            animation-delay: 6s;
            background: linear-gradient(45deg, rgba(255,255,255,0.12), rgba(255,255,255,0.08));
        }

        .shape:nth-child(5) {
            width: 40px;
            height: 40px;
            top: 30%;
            left: 60%;
            animation-delay: 8s;
            background: linear-gradient(45deg, rgba(255,255,255,0.08), rgba(255,255,255,0.03));
        }

        /* Enhanced Login Form Container */
        .login-container {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(25px);
            border-radius: 30px;
            padding: 3.5rem;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.2);
            max-width: 480px;
            width: 90%;
            animation: fadeInUp 1.2s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
            perspective: 1000px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--success-gradient);
            border-radius: 30px 30px 0 0;
        }

        .login-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            border-radius: 30px;
            z-index: -1;
        }

        /* Enhanced Login Header */
        .login-header {
            text-align: center;
            margin-bottom: 3rem;
            animation: fadeInUp 1.2s 0.3s both;
        }

        .login-header h1 {
            font-size: 2.8rem;
            font-weight: 800;
            background: var(--success-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.8rem;
            letter-spacing: -0.02em;
            text-shadow: 0 0 30px rgba(67, 233, 123, 0.3);
        }

        .login-header p {
            color: #64748b;
            font-size: 1.2rem;
            font-weight: 400;
            opacity: 0.9;
        }

        .login-icon {
            font-size: 3.5rem;
            color: #43e97b;
            margin-bottom: 1.5rem;
            animation: pulse 2s ease-in-out infinite;
            filter: drop-shadow(0 0 20px rgba(67, 233, 123, 0.5));
        }

        /* Enhanced Form Styles */
        .login-form {
            animation: fadeInUp 1.2s 0.5s both;
        }

        .form-group {
            margin-bottom: 2rem;
            position: relative;
            animation: fadeInUp 1.2s both;
        }

        .form-group:nth-child(1) { animation-delay: 0.7s; }
        .form-group:nth-child(2) { animation-delay: 0.9s; }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.8rem;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            transition: var(--transition-fast);
        }

        .form-group input {
            width: 100%;
            padding: 1.2rem 1.5rem;
            border: 2px solid #e1e8ed;
            border-radius: 15px;
            font-size: 1.1rem;
            transition: var(--transition-smooth);
            background: rgba(255, 255, 255, 0.9);
            color: #2d3748;
            position: relative;
            backdrop-filter: blur(10px);
        }

        .form-group input:focus {
            outline: none;
            border-color: #43e97b;
            box-shadow: 0 0 0 4px rgba(67, 233, 123, 0.15);
            transform: translateY(-3px);
            background: rgba(255, 255, 255, 1);
        }

        .form-group input::placeholder {
            color: #a0aec0;
            font-weight: 400;
        }

        /* Enhanced Input Icons */
        .input-icon {
            position: absolute;
            right: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            transition: var(--transition-fast);
            font-size: 1.1rem;
        }

        .form-group input:focus + .input-icon {
            color: #43e97b;
            transform: translateY(-50%) scale(1.1);
        }

        /* Enhanced Error Message */
        .error-message {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 1.2rem 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            font-weight: 500;
            animation: shake 0.6s ease-in-out;
            box-shadow: 0 8px 32px rgba(231, 76, 60, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        /* Enhanced Success Message */
        .success-message {
            background: var(--success-gradient);
            color: white;
            padding: 1.2rem 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            font-weight: 500;
            animation: slideInDown 0.6s ease-in-out;
            box-shadow: 0 8px 32px rgba(67, 233, 123, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        /* Enhanced Login Button */
        .login-btn {
            width: 100%;
            padding: 1.2rem 2rem;
            background: var(--success-gradient);
            color: white;
            border: none;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1.2rem;
            cursor: pointer;
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-medium);
            animation: fadeInUp 1.2s 1.1s both;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.8s;
        }

        .login-btn:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 60px rgba(67, 233, 123, 0.4);
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:active {
            transform: translateY(-2px) scale(1.01);
        }

        /* Enhanced Form Footer */
        .form-footer {
            text-align: center;
            margin-top: 2.5rem;
            color: #64748b;
            font-size: 1rem;
            animation: fadeInUp 1.2s 1.3s both;
        }

        .form-footer a {
            color: #43e97b;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition-fast);
            position: relative;
        }

        .form-footer a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--success-gradient);
            transition: width 0.3s ease;
        }

        .form-footer a:hover::after {
            width: 100%;
        }

        .form-footer a:hover {
            color: #38f9d7;
        }

        /* Enhanced Social Login Options */
        .social-login {
            margin-top: 2.5rem;
            text-align: center;
            animation: fadeInUp 1.2s 1.5s both;
        }

        .social-login p {
            color: #64748b;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 500;
        }

        .social-buttons {
            display: flex;
            gap: 1.2rem;
            justify-content: center;
        }

        .social-btn {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            border: 2px solid #e1e8ed;
            background: rgba(255, 255, 255, 0.9);
            color: #43e97b;
            font-size: 1.3rem;
            cursor: pointer;
            transition: var(--transition-smooth);
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .social-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--success-gradient);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 50%;
        }

        .social-btn:hover::before {
            opacity: 1;
        }

        .social-btn:hover {
            transform: translateY(-8px) scale(1.15);
            border-color: #43e97b;
            box-shadow: 0 15px 40px rgba(67, 233, 123, 0.3);
            color: white;
        }

        .social-btn i {
            position: relative;
            z-index: 2;
        }

        /* Enhanced Animations */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(40px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-25px) rotate(5deg);
            }
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.08);
            }
        }

        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            25% {
                transform: translateX(-8px);
            }
            75% {
                transform: translateX(8px);
            }
        }

        @keyframes backgroundShift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        /* Enhanced 3D Hover Effect */
        .login-container:hover {
            transform: rotateY(3deg) rotateX(2deg);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                padding: 2.5rem;
                width: 95%;
            }

            .login-header h1 {
                font-size: 2.2rem;
            }

            .social-buttons {
                gap: 1rem;
            }

            .social-btn {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem;
            }

            .login-header h1 {
                font-size: 2rem;
            }

            .form-group input {
                padding: 1rem 1.2rem;
                font-size: 1rem;
            }

            .login-btn {
                padding: 1rem 1.5rem;
                font-size: 1.1rem;
            }
        }

        /* Loading Animation */
        .loading {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--success-gradient);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-gradient);
        }

        /* Particle Effects */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            animation: particleFloat 8s linear infinite;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) translateX(100px);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="login-page">
        <!-- Enhanced Floating Shapes -->
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>

        <!-- Particle Effects -->
        <div class="particles" id="particles"></div>

        <!-- Enhanced Login Container -->
        <div class="login-container">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h1>Welcome Back</h1>
                <p>Sign in to your account to continue</p>
            </div>

            <?php if($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="login-form">
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope" style="margin-right: 8px;"></i>
                        Email Address
                    </label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           placeholder="Enter your email">
                    <i class="fas fa-envelope input-icon"></i>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock" style="margin-right: 8px;"></i>
                        Password
                    </label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Enter your password">
                    <i class="fas fa-lock input-icon"></i>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt" style="margin-right: 10px;"></i>
                    Sign In
                    <div class="loading"></div>
                </button>
            </form>

            <div class="form-footer">
                <p>Don't have an account? <a href="register.php">Create one here</a></p>
            </div>

            <div class="social-login">
                <p>Or continue with</p>
                <div class="social-buttons">
                    <button class="social-btn" title="Google">
                        <i class="fab fa-google"></i>
                    </button>
                    <button class="social-btn" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </button>
                    <button class="social-btn" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Create particle effects
        const particlesContainer = document.getElementById('particles');
        for (let i = 0; i < 50; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 8 + 's';
            particle.style.animationDuration = (Math.random() * 3 + 5) + 's';
            particlesContainer.appendChild(particle);
        }

        // Enhanced form submission with loading animation
        const loginForm = document.querySelector('.login-form');
        const loginBtn = document.querySelector('.login-btn');
        const loading = document.querySelector('.loading');

        loginForm.addEventListener('submit', function() {
            loginBtn.disabled = true;
            loading.style.display = 'inline-block';
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 10px;"></i>Signing In...';
        });

        // Enhanced input focus effects
        const inputs = document.querySelectorAll('.form-group input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-3px)';
                this.parentElement.style.filter = 'drop-shadow(0 10px 20px rgba(67, 233, 123, 0.2))';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
                this.parentElement.style.filter = 'none';
            });
        });

        // Enhanced password visibility toggle
        const passwordInput = document.getElementById('password');
        const passwordIcon = passwordInput.nextElementSibling;
        
        passwordIcon.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Enhanced 3D tilt effect on mouse move
        const loginContainer = document.querySelector('.login-container');
        loginContainer.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 15;
            const rotateY = (centerX - x) / 15;
            
            this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.03)`;
        });

        loginContainer.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale(1)';
        });

        // Enhanced parallax effect for floating shapes
        window.addEventListener('scroll', () => {
            const shapes = document.querySelectorAll('.shape');
            const scrolled = window.pageYOffset;
            shapes.forEach((shape, index) => {
                const speed = 0.5 + (index * 0.1);
                shape.style.transform = `translateY(${scrolled * speed}px) rotate(${scrolled * 0.1}deg)`;
            });
        });

        // Enhanced social button hover effects
        const socialBtns = document.querySelectorAll('.social-btn');
        socialBtns.forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.15)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Enhanced error message animation
        const errorMessage = document.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.addEventListener('animationend', function() {
                this.style.animation = 'none';
            });
        }

        // Enhanced form validation with real-time feedback
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        emailInput.addEventListener('input', function() {
            const isValid = validateEmail(this.value);
            this.style.borderColor = isValid ? '#43e97b' : '#e74c3c';
            this.style.boxShadow = isValid ? '0 0 0 4px rgba(67, 233, 123, 0.15)' : '0 0 0 4px rgba(231, 76, 60, 0.15)';
        });

        passwordInput.addEventListener('input', function() {
            const isValid = this.value.length >= 6;
            this.style.borderColor = isValid ? '#43e97b' : '#e74c3c';
            this.style.boxShadow = isValid ? '0 0 0 4px rgba(67, 233, 123, 0.15)' : '0 0 0 4px rgba(231, 76, 60, 0.15)';
        });

        // Enhanced smooth reveal animation for form elements
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.form-group, .login-btn, .form-footer, .social-login').forEach(el => {
            observer.observe(el);
        });

        // Add typing effect to header
        const headerText = document.querySelector('.login-header h1');
        const originalText = headerText.textContent;
        headerText.textContent = '';
        
        let i = 0;
        const typeWriter = () => {
            if (i < originalText.length) {
                headerText.textContent += originalText.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        };
        
        setTimeout(typeWriter, 1000);
    });

    // Always scroll to top on page load (including refresh)
    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    };
    </script>
</body>
</html> 