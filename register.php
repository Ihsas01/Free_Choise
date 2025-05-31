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
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<div class="container">
    <div class="form-container">
        <h2>Register</h2>
        <?php if($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
        <p class="form-footer">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 