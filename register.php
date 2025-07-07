<?php
require_once 'config/database.php';
require_once 'includes/header.php';

if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    // Validate password match
    if($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if email already exists
        $check_query = "SELECT * FROM users WHERE email = ? OR username = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ss", $email, $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if($check_result->num_rows > 0) {
            $error = 'Email or username already exists';
        } else {
            // Hash password and create user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $query = "INSERT INTO users (username, email, password, first_name, last_name) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssss", $username, $email, $hashed_password, $first_name, $last_name);
            
            if($stmt->execute()) {
                // Redirect to login page after successful registration
                header('Location: login.php?registered=1');
                exit();
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Free Choice</title>
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

        /* Register Page Container */
        .register-page {
            min-height: 100vh;
            background: var(--secondary-gradient);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 2rem 0;
        }

        /* Dynamic Background */
        .register-page::before {
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

        .register-page::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }

        /* Floating Elements */
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
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 15%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 70%;
            right: 15%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 25%;
            left: 20%;
            animation-delay: 4s;
        }

        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 45%;
            right: 25%;
            animation-delay: 6s;
        }

        .shape:nth-child(5) {
            width: 40px;
            height: 40px;
            top: 30%;
            left: 60%;
            animation-delay: 8s;
        }

        /* Register Form Container */
        .register-container {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 3rem;
            box-shadow: var(--shadow-strong);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 600px;
            width: 90%;
            animation: fadeInUp 1s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--accent-gradient);
            border-radius: 25px 25px 0 0;
        }

        /* Register Header */
        .register-header {
            text-align: center;
            margin-bottom: 2.5rem;
            animation: fadeInUp 1s 0.2s both;
        }

        .register-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .register-header p {
            color: #64748b;
            font-size: 1.1rem;
            font-weight: 400;
        }

        .register-icon {
            font-size: 3rem;
            color: #f093fb;
            margin-bottom: 1rem;
            animation: pulse 2s ease-in-out infinite;
        }

        /* Form Styles */
        .register-form {
            animation: fadeInUp 1s 0.4s both;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
            animation: fadeInUp 1s both;
        }

        .form-group:nth-child(1) { animation-delay: 0.6s; }
        .form-group:nth-child(2) { animation-delay: 0.8s; }
        .form-group:nth-child(3) { animation-delay: 1.0s; }
        .form-group:nth-child(4) { animation-delay: 1.2s; }
        .form-group:nth-child(5) { animation-delay: 1.4s; }
        .form-group:nth-child(6) { animation-delay: 1.6s; }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-group input {
            width: 100%;
            padding: 1rem 1.5rem;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 1rem;
            transition: var(--transition-smooth);
            background: white;
            color: #2d3748;
            position: relative;
        }

        .form-group input:focus {
            outline: none;
            border-color: #f093fb;
            box-shadow: 0 0 0 3px rgba(240, 147, 251, 0.1);
            transform: translateY(-2px);
        }

        .form-group input::placeholder {
            color: #a0aec0;
        }

        /* Input Icons */
        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            transition: var(--transition-fast);
        }

        .form-group input:focus + .input-icon {
            color: #f093fb;
        }

        /* Message Styles */
        .message {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            animation: slideInDown 0.5s ease-out;
            box-shadow: var(--shadow-medium);
        }

        .error-message {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .success-message {
            background: var(--success-gradient);
            color: white;
        }

        /* Register Button */
        .register-btn {
            width: 100%;
            padding: 1rem 2rem;
            background: var(--accent-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-medium);
            animation: fadeInUp 1s 1.8s both;
        }

        .register-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s;
        }

        .register-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: var(--shadow-strong);
        }

        .register-btn:hover::before {
            left: 100%;
        }

        .register-btn:active {
            transform: translateY(-1px) scale(1.01);
        }

        /* Form Footer */
        .form-footer {
            text-align: center;
            margin-top: 2rem;
            color: #64748b;
            font-size: 0.95rem;
            animation: fadeInUp 1s 2s both;
        }

        .form-footer a {
            color: #f093fb;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition-fast);
        }

        .form-footer a:hover {
            color: #f5576c;
            text-decoration: underline;
        }

        /* Progress Indicator */
        .progress-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
            animation: fadeInUp 1s 0.3s both;
        }

        .progress-step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e1e8ed;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-weight: 600;
            font-size: 0.8rem;
            transition: var(--transition-smooth);
            position: relative;
            z-index: 2;
        }

        .progress-step.active {
            background: var(--accent-gradient);
            color: white;
            transform: scale(1.1);
        }

        .progress-step.completed {
            background: var(--success-gradient);
            color: white;
        }

        .progress-line {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: #e1e8ed;
            transform: translateY(-50%);
            z-index: 1;
        }

        .progress-fill {
            height: 100%;
            background: var(--accent-gradient);
            width: 0%;
            transition: width 0.5s ease;
        }

        /* Animations */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInDown {
            0% {
                opacity: 0;
                transform: translateY(-30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
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
                transform: scale(1.05);
            }
        }

        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            25% {
                transform: translateX(-5px);
            }
            75% {
                transform: translateX(5px);
            }
        }

        /* 3D Hover Effect */
        .register-container:hover {
            transform: rotateY(2deg) rotateX(1deg);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .register-container {
                padding: 2rem;
                width: 95%;
            }

            .register-header h1 {
                font-size: 2rem;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .progress-bar {
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 1.5rem;
            }

            .register-header h1 {
                font-size: 1.8rem;
            }

            .form-group input {
                padding: 0.8rem 1.2rem;
            }

            .register-btn {
                padding: 0.8rem 1.5rem;
                font-size: 1rem;
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
            background: var(--secondary-gradient);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-gradient);
        }

        /* Password Strength Indicator */
        .password-strength {
            margin-top: 0.5rem;
            height: 4px;
            background: #e1e8ed;
            border-radius: 2px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            transition: width 0.3s ease, background-color 0.3s ease;
            width: 0%;
        }

        .strength-weak { background: #e74c3c; }
        .strength-medium { background: #f39c12; }
        .strength-strong { background: #27ae60; }
    </style>
</head>
<body>
    <div class="register-page">
        <!-- Floating Shapes -->
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>

        <!-- Register Container -->
        <div class="register-container">
            <div class="register-header">
                <div class="register-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>Join Free Choice</h1>
                <p>Create your account and start shopping today</p>
            </div>

            <!-- Progress Bar -->
            <div class="progress-bar">
                <div class="progress-line">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <div class="progress-step active" data-step="1">1</div>
                <div class="progress-step" data-step="2">2</div>
                <div class="progress-step" data-step="3">3</div>
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

            <form method="POST" action="" class="register-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">
                            <i class="fas fa-user" style="margin-right: 8px;"></i>
                            First Name
                        </label>
                        <input type="text" id="first_name" name="first_name" required 
                               value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>"
                               placeholder="Enter your first name">
                        <i class="fas fa-user input-icon"></i>
                    </div>

                    <div class="form-group">
                        <label for="last_name">
                            <i class="fas fa-user" style="margin-right: 8px;"></i>
                            Last Name
                        </label>
                        <input type="text" id="last_name" name="last_name" required 
                               value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>"
                               placeholder="Enter your last name">
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-at" style="margin-right: 8px;"></i>
                        Username
                    </label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           placeholder="Choose a username">
                    <i class="fas fa-at input-icon"></i>
                </div>

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

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock" style="margin-right: 8px;"></i>
                            Password
                        </label>
                        <input type="password" id="password" name="password" required 
                               placeholder="Create a password">
                        <i class="fas fa-lock input-icon"></i>
                        <div class="password-strength">
                            <div class="strength-bar" id="strengthBar"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">
                            <i class="fas fa-lock" style="margin-right: 8px;"></i>
                            Confirm Password
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               placeholder="Confirm your password">
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>

                <button type="submit" class="register-btn">
                    <i class="fas fa-user-plus" style="margin-right: 10px;"></i>
                    Create Account
                    <div class="loading"></div>
                </button>
            </form>

            <div class="form-footer">
                <p>Already have an account? <a href="login.php">Sign in here</a></p>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form submission with loading animation
        const registerForm = document.querySelector('.register-form');
        const registerBtn = document.querySelector('.register-btn');
        const loading = document.querySelector('.loading');

        registerForm.addEventListener('submit', function() {
            registerBtn.disabled = true;
            loading.style.display = 'inline-block';
            registerBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 10px;"></i>Creating Account...';
        });

        // Input focus effects
        const inputs = document.querySelectorAll('.form-group input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });

        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let strengthClass = '';

            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            const width = (strength / 5) * 100;

            if (strength <= 2) {
                strengthClass = 'strength-weak';
            } else if (strength <= 3) {
                strengthClass = 'strength-medium';
            } else {
                strengthClass = 'strength-strong';
            }

            strengthBar.style.width = width + '%';
            strengthBar.className = 'strength-bar ' + strengthClass;
        });

        // Progress bar functionality
        const progressSteps = document.querySelectorAll('.progress-step');
        const progressFill = document.getElementById('progressFill');
        let currentStep = 1;

        function updateProgress() {
            const progress = (currentStep / 3) * 100;
            progressFill.style.width = progress + '%';

            progressSteps.forEach((step, index) => {
                const stepNum = index + 1;
                step.classList.remove('active', 'completed');
                
                if (stepNum < currentStep) {
                    step.classList.add('completed');
                } else if (stepNum === currentStep) {
                    step.classList.add('active');
                }
            });
        }

        // Update progress based on form completion
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                const filledInputs = Array.from(inputs).filter(input => input.value.trim() !== '').length;
                currentStep = Math.min(Math.ceil(filledInputs / 2), 3);
                updateProgress();
            });
        });

        // 3D tilt effect on mouse move
        const registerContainer = document.querySelector('.register-container');
        registerContainer.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;
            
            this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
        });

        registerContainer.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale(1)';
        });

        // Parallax effect for floating shapes
        window.addEventListener('scroll', () => {
            const shapes = document.querySelectorAll('.shape');
            const scrolled = window.pageYOffset;
            shapes.forEach((shape, index) => {
                const speed = 0.5 + (index * 0.1);
                shape.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });

        // Form validation with real-time feedback
        const emailInput = document.getElementById('email');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const passwordInput = document.getElementById('password');

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        emailInput.addEventListener('input', function() {
            const isValid = validateEmail(this.value);
            this.style.borderColor = isValid ? '#f093fb' : '#e74c3c';
        });

        confirmPasswordInput.addEventListener('input', function() {
            const passwordsMatch = this.value === passwordInput.value;
            this.style.borderColor = passwordsMatch ? '#f093fb' : '#e74c3c';
        });

        // Smooth reveal animation for form elements
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

        document.querySelectorAll('.form-group, .register-btn, .form-footer').forEach(el => {
            observer.observe(el);
        });

        // Error message animation
        const errorMessage = document.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.addEventListener('animationend', function() {
                this.style.animation = 'none';
            });
        }
    });

    // Always scroll to top on page load (including refresh)
    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    };
    </script>
</body>
</html> 