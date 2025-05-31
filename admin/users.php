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

<div class="admin-content">
    <h2><?php echo ($action == 'add') ? 'Add New User' : 'Manage Users'; ?></h2>
    
    <?php if($message): ?>
        <div class="admin-alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>">
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
                    <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                 <div class="form-group">
                    <label for="first_name">First Name (Optional)</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name (Optional)</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="is_admin">Admin User?</label>
                    <input type="checkbox" id="is_admin" name="is_admin" value="1" <?php echo isset($_POST['is_admin']) ? 'checked' : ''; ?>> Yes
                </div>
                <button type="submit" class="btn-admin btn-primary"><i class="fas fa-user-plus"></i> Add User</button>
                 <a href="users.php" class="btn-admin btn-secondary"><i class="fas fa-list"></i> Cancel</a>
            </form>
        </div>

    <?php else: ?>
        <!-- Users List -->
        <div class="users-list">
             <div class="admin-actions" style="margin-bottom: 1rem;">
                 <a href="users.php?action=add" class="btn-admin btn-primary"><i class="fas fa-user-plus"></i> Add New User</a>
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
                                    <?php if(!$user['is_admin']): ?>
                                        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display:inline-block;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                            <button type="submit" class="btn-admin btn-danger btn-admin-small"><i class="fas fa-trash"></i> Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-users admin-alert alert-warning">No users found.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php 
// Close the database connection if it was opened and not closed
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
// Include admin footer
require_once 'includes/admin_footer.php';
?> 