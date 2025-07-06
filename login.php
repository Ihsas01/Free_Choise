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

<div class="container">
    <div class="form-container">
        <h2>Login</h2>
        <?php if($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <p class="form-footer">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 