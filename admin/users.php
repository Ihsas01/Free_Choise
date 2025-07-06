<?php
session_start(); // Ensure session is started

// Check if user is admin
if(!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

$page_title = 'Manage Users'; // Set page title for admin header
require_once '../includes/db_config.php'; // Include database configuration

$message = '';
$success = false;

// Determine which view to show (list or add user)
$action = $_GET['action'] ?? 'list';

// Handle user actions (POST requests)
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'add_user':
                $username = $_POST['username'] ?? '';
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $first_name = $_POST['first_name'] ?? '';
                $last_name = $_POST['last_name'] ?? '';
                $is_admin = isset($_POST['is_admin']) ? 1 : 0;
                
                // Basic validation (add more as needed)
                if (empty($username) || empty($email) || empty($password)) {
                    $message = 'Username, Email, and Password are required.';
                    $success = false;
                } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                     $message = 'Invalid email format.';
                    $success = false;
                } else {
                    // Check if email or username already exists
                    $check_query = "SELECT user_id FROM users WHERE username = ? OR email = ? LIMIT 1";
                    $check_stmt = $conn->prepare($check_query);
                    $check_stmt->bind_param("ss", $username, $email);
                    $check_stmt->execute();
                    $check_result = $check_stmt->get_result();
                    
                    if ($check_result->num_rows > 0) {
                        $message = 'Username or Email already exists.';
                        $success = false;
                    } else {
                        // Hash the password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        // Insert new user into the database
                        $insert_query = "INSERT INTO users (username, email, password, first_name, last_name, is_admin) 
                                         VALUES (?, ?, ?, ?, ?, ?)";
                        $insert_stmt = $conn->prepare($insert_query);
                        $insert_stmt->bind_param("sssssi", $username, $email, $hashed_password, $first_name, $last_name, $is_admin);
                        
                        if($insert_stmt->execute()) {
                            $message = 'User added successfully.';
                            $success = true;
                            // Redirect to user list after adding (optional)
                             header('Location: users.php?message=' . urlencode($message) . '&success=' . $success);
                             exit();
                        } else {
                            $message = 'Error adding user: ' . $conn->error; // Add error detail
                            $success = false;
                        }
                        $insert_stmt->close();
                    }
                     $check_stmt->close();
                }
                 // Set action back to 'add' if there was an error to show the form again
                 if (!$success) {
                     $action = 'add';
                 }
                break;

            case 'delete':
                $user_id = (int)$_POST['user_id'];
                
                // Don't allow deleting admin users
                $check_query = "SELECT is_admin FROM users WHERE user_id = ?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->bind_param("i", $user_id);
                $check_stmt->execute();
                $user = $check_stmt->get_result()->fetch_assoc();
                 $check_stmt->close();
            
                if(isset($user['is_admin']) && $user['is_admin']) {
                    $message = 'Cannot delete admin users';
                    $success = false;
                } else {
                    $delete_query = "DELETE FROM users WHERE user_id = ?";
                    $delete_stmt = $conn->prepare($delete_query);
                    $delete_stmt->bind_param("i", $user_id);
                    
                    if($delete_stmt->execute()) {
                        $message = 'User deleted successfully';
                        $success = true;
                    } else {
                        $message = 'Error deleting user: ' . $conn->error; // Add error detail
                        $success = false;
                    }
                    $delete_stmt->close();
                }
                break;
        }
         // After handling POST, check if there's a message in the URL for redirects
    } else if (isset($_GET['message'])) {
        $message = htmlspecialchars($_GET['message']);
        $success = filter_var($_GET['success'], FILTER_VALIDATE_BOOLEAN);
    }
} else if (isset($_GET['message'])) {
     // Handle GET requests with message parameter (from redirects)
    $message = htmlspecialchars($_GET['message']);
    $success = filter_var($_GET['success'], FILTER_VALIDATE_BOOLEAN);
}

// Get all users for the list view
$users_query = "SELECT * FROM users ORDER BY created_at DESC";
$users_result = $conn->query($users_query);

// Include admin header
require_once 'includes/admin_header.php';
?>

<!-- Modern Users Page Styles -->
<style>
.users-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow-x: hidden;
}

.users-page::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grain\" width=\"100\" height=\"100\" patternUnits=\"userSpaceOnUse\"><circle cx=\"50\" cy=\"50\" r=\"1\" fill=\"rgba(255,255,255,0.05)\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grain)\"/></svg>');
    pointer-events: none;
    z-index: 1;
}

.users-container {
    position: relative;
    z-index: 2;
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.users-hero {
    text-align: center;
    margin-bottom: 3rem;
    animation: fadeInUp 1s ease-out;
}

.users-hero h1 {
    font-size: 3.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
    text-shadow: 0 4px 8px rgba(0,0,0,0.3);
    background: linear-gradient(45deg, #fff, #f0f0f0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.users-hero p {
    font-size: 1.2rem;
    color: rgba(255,255,255,0.9);
    margin-bottom: 2rem;
}

.admin-alert {
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    font-weight: 500;
    animation: slideInDown 0.5s ease-out;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}

.alert-success {
    background: rgba(39, 174, 96, 0.9);
    color: white;
    box-shadow: 0 8px 32px rgba(39, 174, 96, 0.3);
}

.alert-danger {
    background: rgba(231, 76, 60, 0.9);
    color: white;
    box-shadow: 0 8px 32px rgba(231, 76, 60, 0.3);
}

.add-user-section {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    padding: 3rem;
    margin-bottom: 3rem;
    box-shadow: 0 20px 60px rgba(0,0,0,0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.2);
    animation: fadeInUp 0.8s ease-out 0.2s both;
    position: relative;
    overflow: hidden;
}

.add-user-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.user-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.form-group {
    position: relative;
    animation: fadeInUp 0.8s ease-out both;
}

.form-group:nth-child(1) { animation-delay: 0.3s; }
.form-group:nth-child(2) { animation-delay: 0.4s; }
.form-group:nth-child(3) { animation-delay: 0.5s; }
.form-group:nth-child(4) { animation-delay: 0.6s; }
.form-group:nth-child(5) { animation-delay: 0.7s; }
.form-group:nth-child(6) { animation-delay: 0.8s; }

.form-group label {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 1rem 1.5rem;
    border: 2px solid #e1e8ed;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
    color: #2c3e50;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
}

.btn-container {
    grid-column: 1 / -1;
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    animation: fadeInUp 0.8s ease-out 0.9s both;
}

.btn-admin {
    padding: 1rem 2rem;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
    overflow: hidden;
}

.btn-admin::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-admin:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: rgba(255,255,255,0.9);
    color: #2c3e50;
    border: 2px solid #e1e8ed;
}

.btn-secondary:hover {
    background: white;
    transform: translateY(-3px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
}

.btn-danger {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    box-shadow: 0 8px 32px rgba(231, 76, 60, 0.3);
}

.btn-danger:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(231, 76, 60, 0.4);
}

.users-list {
    animation: fadeInUp 0.8s ease-out 0.3s both;
}

.admin-actions {
    margin-bottom: 2rem;
    animation: fadeInUp 0.8s ease-out 0.4s both;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(255,255,255,0.95);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.2);
    margin-bottom: 2rem;
    font-size: 1rem;
}

.admin-table thead {
    background: linear-gradient(90deg, #667eea, #764ba2);
    color: white;
}

.admin-table th, .admin-table td {
    padding: 1.2rem 1rem;
    text-align: left;
}

.admin-table th {
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.95rem;
}

.admin-table tbody tr {
    transition: background 0.3s, box-shadow 0.3s, transform 0.3s;
}

.admin-table tbody tr:hover {
    background: #f5f7fa;
    box-shadow: 0 4px 24px rgba(102, 126, 234, 0.08);
    transform: scale(1.01);
}

.btn-admin-small {
    padding: 0.6rem 1.2rem;
    font-size: 0.95rem;
}

.user-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.user-actions .btn-admin {
    margin: 0;
}

@media (max-width: 768px) {
    .user-actions {
        flex-direction: column;
        gap: 0.3rem;
    }
    
    .user-actions .btn-admin {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
    }
}

.no-users {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255,255,255,0.9);
    border-radius: 20px;
    backdrop-filter: blur(20px);
    animation: fadeInUp 0.8s ease-out;
}

.no-users p {
    font-size: 1.2rem;
    color: #666;
    margin-bottom: 2rem;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .users-container {
        padding: 1rem;
    }
    .users-hero h1 {
        font-size: 2.5rem;
    }
    .add-user-section {
        padding: 2rem;
    }
    .user-form {
        grid-template-columns: 1fr;
    }
    .admin-table th, .admin-table td {
        padding: 0.8rem 0.5rem;
    }
    .btn-container {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .users-hero h1 {
        font-size: 2rem;
    }
    .add-user-section {
        padding: 1.5rem;
    }
}

html {
    scroll-behavior: smooth;
}
</style>

<div class="users-page">
    <div class="users-container">
        <!-- Hero Section -->
        <div class="users-hero">
            <h1><?php echo ($action == 'add') ? 'Add New User' : 'Manage Users'; ?></h1>
            <p><?php echo ($action == 'add') ? 'Create a new user for your store' : 'View and manage all users'; ?></p>
        </div>
        
        <?php if($message): ?>
            <div class="admin-alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>">
                <i class="fas <?php echo $success ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($action == 'add'): ?>
            <!-- Add New User Form -->
            <div class="add-user-section">
                <form method="POST" action="" class="user-form">
                    <input type="hidden" name="action" value="add_user">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" placeholder="Enter username">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Enter password">
                    </div>
                    <div class="form-group">
                        <label for="first_name">First Name (Optional)</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" placeholder="First name">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name (Optional)</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" placeholder="Last name">
                    </div>
                    <div class="form-group">
                        <label for="is_admin">Admin User?</label>
                        <input type="checkbox" id="is_admin" name="is_admin" value="1" <?php echo isset($_POST['is_admin']) ? 'checked' : ''; ?>> Yes
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn-admin btn-primary">
                            <i class="fas fa-user-plus"></i> Add User
                        </button>
                        <a href="users.php" class="btn-admin btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </form>
            </div>

        <?php else: ?>
            <!-- Users List -->
            <div class="users-list">
                <div class="admin-actions">
                    <a href="users.php?action=add" class="btn-admin btn-primary">
                        <i class="fas fa-user-plus"></i> Add New User
                    </a>
                </div>

                <?php if($users_result->num_rows > 0): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($user = $users_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                    <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                                                    <td>
                                    <div class="user-actions">
                                        <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn-admin btn-secondary btn-admin-small">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <?php if(!$user['is_admin']): ?>
                                            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display:inline-block;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                <button type="submit" class="btn-admin btn-danger btn-admin-small">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-users">
                        <i class="fas fa-users" style="font-size: 3rem; color: #667eea; margin-bottom: 1rem;"></i>
                        <p>No users found. Start by adding your first user!</p>
                        <a href="users.php?action=add" class="btn-admin btn-primary">
                            <i class="fas fa-user-plus"></i> Add Your First User
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript for Enhanced Interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Parallax effect for hero section
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('.users-page');
        if (parallax) {
            const speed = scrolled * 0.5;
            parallax.style.transform = `translateY(${speed}px)`;
        }
    });

    // Smooth reveal animations on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all table rows, form groups, and sections
    document.querySelectorAll('.admin-table tbody tr, .form-group, .add-user-section, .users-list').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s ease-out';
        observer.observe(el);
    });

    // Enhanced form interactions
    const formInputs = document.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });

    // Button hover effects
    const buttons = document.querySelectorAll('.btn-admin');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Table row interactions
    const tableRows = document.querySelectorAll('.admin-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.boxShadow = '0 4px 24px rgba(102, 126, 234, 0.08)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
        });
    });
});
</script>

<?php 
// Close the database connection if it was opened and not closed
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
// Include admin footer
require_once 'includes/admin_footer.php';
?> 