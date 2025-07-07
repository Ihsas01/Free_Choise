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
                $current_admin_id = $_SESSION['user_id'];
                
                // Don't allow deleting yourself
                if($user_id == $current_admin_id) {
                    $message = 'You cannot delete your own account';
                    $success = false;
                } else {
                    // Check if target user is admin
                    $check_query = "SELECT is_admin, username FROM users WHERE user_id = ?";
                    $check_stmt = $conn->prepare($check_query);
                    $check_stmt->bind_param("i", $user_id);
                    $check_stmt->execute();
                    $user = $check_stmt->get_result()->fetch_assoc();
                    $check_stmt->close();
                    
                    if($user) {
                        $delete_query = "DELETE FROM users WHERE user_id = ?";
                        $delete_stmt = $conn->prepare($delete_query);
                        $delete_stmt->bind_param("i", $user_id);
                        
                        if($delete_stmt->execute()) {
                            $user_type = $user['is_admin'] ? 'Admin' : 'User';
                            $message = "$user_type deleted successfully";
                            $success = true;
                        } else {
                            $message = 'Error deleting user: ' . $conn->error;
                            $success = false;
                        }
                        $delete_stmt->close();
                    } else {
                        $message = 'User not found';
                        $success = false;
                    }
                }
                break;

            case 'toggle_admin':
                $user_id = (int)$_POST['user_id'];
                $current_admin_id = $_SESSION['user_id'];
                
                // Don't allow demoting yourself
                if($user_id == $current_admin_id) {
                    $message = 'You cannot change your own admin status';
                    $success = false;
                } else {
                    // Get current admin status
                    $check_query = "SELECT is_admin, username FROM users WHERE user_id = ?";
                    $check_stmt = $conn->prepare($check_query);
                    $check_stmt->bind_param("i", $user_id);
                    $check_stmt->execute();
                    $user = $check_stmt->get_result()->fetch_assoc();
                    $check_stmt->close();
                    
                    if($user) {
                        $new_admin_status = $user['is_admin'] ? 0 : 1;
                        $action_text = $user['is_admin'] ? 'demoted to user' : 'promoted to admin';
                        
                        $update_query = "UPDATE users SET is_admin = ? WHERE user_id = ?";
                        $update_stmt = $conn->prepare($update_query);
                        $update_stmt->bind_param("ii", $new_admin_status, $user_id);
                        
                        if($update_stmt->execute()) {
                            $message = "User {$user['username']} has been $action_text successfully";
                            $success = true;
                        } else {
                            $message = 'Error updating user: ' . $conn->error;
                            $success = false;
                        }
                        $update_stmt->close();
                    } else {
                        $message = 'User not found';
                        $success = false;
                    }
                }
                break;

            case 'ban_user':
                $user_id = (int)$_POST['user_id'];
                $ban_duration = (int)$_POST['ban_duration']; // Days
                $ban_reason = $_POST['ban_reason'] ?? 'Admin ban';
                $current_admin_id = $_SESSION['user_id'];
                
                // Don't allow banning yourself
                if($user_id == $current_admin_id) {
                    $message = 'You cannot ban your own account';
                    $success = false;
                } else {
                    // Check if target user exists
                    $check_query = "SELECT is_admin, username FROM users WHERE user_id = ?";
                    $check_stmt = $conn->prepare($check_query);
                    $check_stmt->bind_param("i", $user_id);
                    $check_stmt->execute();
                    $user = $check_stmt->get_result()->fetch_assoc();
                    $check_stmt->close();
                    
                    if($user) {
                        // Calculate ban end date
                        $ban_until = date('Y-m-d H:i:s', strtotime("+$ban_duration days"));
                        
                        // Update user ban status
                        $ban_query = "UPDATE users SET is_banned = TRUE, ban_reason = ?, ban_until = ? WHERE user_id = ?";
                        $ban_stmt = $conn->prepare($ban_query);
                        $ban_stmt->bind_param("ssi", $ban_reason, $ban_until, $user_id);
                        
                        if($ban_stmt->execute()) {
                            // Add to ban history
                            $history_query = "INSERT INTO user_bans (user_id, reason, banned_by, ban_until) VALUES (?, ?, ?, ?)";
                            $history_stmt = $conn->prepare($history_query);
                            $admin_id = $_SESSION['user_id'];
                            $history_stmt->bind_param("isis", $user_id, $ban_reason, $admin_id, $ban_until);
                            $history_stmt->execute();
                            $history_stmt->close();
                            
                            $user_type = $user['is_admin'] ? 'Admin' : 'User';
                            $message = "$user_type {$user['username']} banned successfully for $ban_duration days";
                            $success = true;
                        } else {
                            $message = 'Error banning user: ' . $conn->error;
                            $success = false;
                        }
                        $ban_stmt->close();
                    } else {
                        $message = 'User not found';
                        $success = false;
                    }
                }
                break;

            case 'unban_user':
                $user_id = (int)$_POST['user_id'];
                
                // Update user ban status
                $unban_query = "UPDATE users SET is_banned = FALSE, ban_reason = NULL, ban_until = NULL WHERE user_id = ?";
                $unban_stmt = $conn->prepare($unban_query);
                $unban_stmt->bind_param("i", $user_id);
                
                if($unban_stmt->execute()) {
                    // Update ban history
                    $update_history = "UPDATE user_bans SET is_active = FALSE WHERE user_id = ? AND is_active = TRUE";
                    $update_stmt = $conn->prepare($update_history);
                    $update_stmt->bind_param("i", $user_id);
                    $update_stmt->execute();
                    $update_stmt->close();
                    
                    $message = 'User unbanned successfully';
                    $success = true;
                } else {
                    $message = 'Error unbanning user: ' . $conn->error;
                    $success = false;
                }
                $unban_stmt->close();
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
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
    z-index: 1;
}

.users-page::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.2) 0%, transparent 50%);
    pointer-events: none;
    z-index: 1;
    animation: gradientShift 15s ease-in-out infinite;
}

@keyframes gradientShift {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 0.8; }
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
    position: relative;
}

.users-hero::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    transform: translate(-50%, -50%);
    border-radius: 50%;
    animation: pulse 4s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.3; }
    50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.6; }
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
    position: relative;
    z-index: 2;
}

.users-hero p {
    font-size: 1.2rem;
    color: rgba(255,255,255,0.9);
    margin-bottom: 2rem;
    position: relative;
    z-index: 2;
}

.admin-alert {
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    font-weight: 500;
    animation: slideInDown 0.5s ease-out;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    position: relative;
    overflow: hidden;
}

.admin-alert::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
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
    transform-style: preserve-3d;
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

.add-user-section::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.user-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    position: relative;
    z-index: 2;
}

.form-group {
    position: relative;
    animation: fadeInUp 0.8s ease-out both;
    transform-style: preserve-3d;
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
    position: relative;
}

.form-group label::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transition: width 0.3s ease;
}

.form-group:focus-within label::after {
    width: 100%;
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
    position: relative;
    z-index: 1;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    transform: translateY(-2px) scale(1.02);
}

.form-group input[type="checkbox"] {
    width: auto;
    margin-right: 0.5rem;
    transform: scale(1.2);
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
    transform-style: preserve-3d;
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

.btn-admin:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 12px 40px rgba(0,0,0,0.2);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: rgba(255,255,255,0.9);
    color: #2c3e50;
    border: 2px solid #e1e8ed;
}

.btn-secondary:hover {
    background: white;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
}

.btn-danger {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    box-shadow: 0 8px 32px rgba(231, 76, 60, 0.3);
}

.btn-danger:hover {
    box-shadow: 0 12px 40px rgba(231, 76, 60, 0.4);
}

.btn-warning {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
    box-shadow: 0 8px 32px rgba(243, 156, 18, 0.3);
}

.btn-warning:hover {
    box-shadow: 0 12px 40px rgba(243, 156, 18, 0.4);
}

.btn-success {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    color: white;
    box-shadow: 0 8px 32px rgba(39, 174, 96, 0.3);
}

.btn-success:hover {
    box-shadow: 0 12px 40px rgba(39, 174, 96, 0.4);
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
    position: relative;
}

.admin-table::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.admin-table thead {
    background: linear-gradient(90deg, #667eea, #764ba2);
    color: white;
    position: relative;
}

.admin-table thead::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: rgba(255,255,255,0.2);
}

.admin-table th, .admin-table td {
    padding: 1.2rem 1rem;
    text-align: left;
    position: relative;
}

.admin-table th {
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.95rem;
    position: relative;
}

.admin-table th::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: rgba(255,255,255,0.1);
}

.admin-table tbody tr {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.admin-table tbody tr::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.05), transparent);
    transition: left 0.5s;
}

.admin-table tbody tr:hover::before {
    left: 100%;
}

.admin-table tbody tr:hover {
    background: #f5f7fa;
    box-shadow: 0 4px 24px rgba(102, 126, 234, 0.08);
    transform: scale(1.01) translateY(-2px);
}

.btn-admin-small {
    padding: 0.6rem 1.2rem;
    font-size: 0.95rem;
}

.btn-info {
    background: linear-gradient(135deg, #17a2b8, #138496);
    border: 1px solid #17a2b8;
    color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-info:hover {
    background: linear-gradient(135deg, #138496, #117a8b);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(23, 162, 184, 0.4);
}

.alert-warning {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    border: 1px solid #ffc107;
    color: #856404;
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 1rem;
    font-weight: 600;
    animation: pulse 2s ease-in-out infinite;
}

.user-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.user-actions .btn-admin {
    margin: 0;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
}

.status-badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.status-badge:hover::before {
    left: 100%;
}

.status-badge.active {
    background: rgba(39, 174, 96, 0.1);
    color: #27ae60;
    border: 1px solid rgba(39, 174, 96, 0.3);
    animation: pulse 2s ease-in-out infinite;
}

.status-badge.banned {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
    border: 1px solid rgba(231, 76, 60, 0.3);
}

.no-users {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255,255,255,0.9);
    border-radius: 20px;
    backdrop-filter: blur(20px);
    animation: fadeInUp 0.8s ease-out;
    position: relative;
    overflow: hidden;
}

.no-users::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
    transform: translate(-50%, -50%);
    border-radius: 50%;
    animation: pulse 4s ease-in-out infinite;
}

.no-users p {
    font-size: 1.2rem;
    color: #666;
    margin-bottom: 2rem;
    position: relative;
    z-index: 2;
}

/* Modal Enhancements */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease-out;
}

.modal-content {
    background: white;
    border-radius: 20px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
    animation: slideInUp 0.3s ease-out;
    position: relative;
    overflow: hidden;
}

.modal-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2rem 2rem 1rem;
    border-bottom: 1px solid #e1e8ed;
    position: relative;
}

.modal-header h3 {
    color: #2c3e50;
    font-size: 1.5rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.modal-header h3 i {
    color: #e74c3c;
    animation: pulse 2s ease-in-out infinite;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #666;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.modal-close:hover {
    background: #f8f9fa;
    color: #e74c3c;
    transform: scale(1.1);
}

.modal-body {
    padding: 2rem;
}

.modal-body p {
    color: #666;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

.delete-options {
    display: grid;
    gap: 2rem;
}

.delete-option {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 2rem;
    border: 2px solid #e1e8ed;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.delete-option::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(231, 76, 60, 0.05), transparent);
    transition: left 0.5s;
}

.delete-option:hover::before {
    left: 100%;
}

.delete-option:hover {
    border-color: #e74c3c;
    background: #fff5f5;
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(231, 76, 60, 0.1);
}

.delete-option h4 {
    color: #2c3e50;
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.delete-option h4 i {
    color: #e74c3c;
}

.delete-option p {
    color: #666;
    margin-bottom: 1.5rem;
    font-size: 0.95rem;
}

.category-select {
    width: 100%;
    padding: 1rem 1.5rem;
    border: 2px solid #e1e8ed;
    border-radius: 12px;
    font-size: 1rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.category-select:focus {
    outline: none;
    border-color: #e74c3c;
    box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    transform: translateY(-2px);
}

.delete-option .btn-admin {
    width: 100%;
    justify-content: center;
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

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
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
    .user-actions {
        flex-direction: column;
        gap: 0.3rem;
    }
    .user-actions .btn-admin {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
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

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.1);
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a6fd8, #6a4190);
}

/* Ripple Effect */
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

/* Enhanced form focus effects */
.form-group:focus-within {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
}

/* Table row selection effect */
.admin-table tbody tr {
    cursor: pointer;
}

.admin-table tbody tr:active {
    transform: scale(0.98);
    transition: transform 0.1s ease;
}

/* Status badge pulse animation */
.status-badge.active {
    animation: pulse 2s ease-in-out infinite;
}

/* Floating elements animation */
@keyframes float {
    0%, 100% { 
        transform: translateY(0px) rotate(0deg); 
    }
    50% { 
        transform: translateY(-20px) rotate(180deg); 
    }
}

/* Shimmer effect for loading states */
.shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200px 100%;
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% { background-position: -200px 0; }
    100% { background-position: calc(200px + 100%) 0; }
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
            <div class="admin-alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>" id="alertMessage">
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
                            <th>Status</th>
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
                                <td>
                                    <?php if($user['is_banned']): ?>
                                        <span class="status-badge banned">
                                            <i class="fas fa-ban"></i> Banned
                                        </span>
                                        <?php if($user['ban_until']): ?>
                                            <br><small>Until: <?php echo date('M d, Y', strtotime($user['ban_until'])); ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="status-badge active">
                                            <i class="fas fa-check"></i> Active
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <div class="user-actions">
                                        <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn-admin btn-secondary btn-admin-small">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        
                                        <?php if($user['is_admin']): ?>
                                            <!-- Admin Management Actions -->
                                            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to demote this admin to regular user?');" style="display:inline-block;">
                                                <input type="hidden" name="action" value="toggle_admin">
                                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                <button type="submit" class="btn-admin btn-info btn-admin-small">
                                                    <i class="fas fa-user-minus"></i> Demote
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <!-- Promote to Admin -->
                                            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to promote this user to admin?');" style="display:inline-block;">
                                                <input type="hidden" name="action" value="toggle_admin">
                                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                <button type="submit" class="btn-admin btn-info btn-admin-small">
                                                    <i class="fas fa-user-plus"></i> Promote
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <!-- Ban/Unban Actions (for all users including admins) -->
                                        <?php if($user['is_banned']): ?>
                                            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to unban this user?');" style="display:inline-block;">
                                                <input type="hidden" name="action" value="unban_user">
                                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                <button type="submit" class="btn-admin btn-success btn-admin-small">
                                                    <i class="fas fa-unlock"></i> Unban
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button type="button" class="btn-admin btn-warning btn-admin-small" onclick="showBanModal(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', <?php echo $user['is_admin'] ? 'true' : 'false'; ?>)">
                                                <i class="fas fa-ban"></i> Ban
                                            </button>
                                        <?php endif; ?>
                                        
                                        <!-- Delete Action (for all users including admins) -->
                                        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');" style="display:inline-block;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                            <button type="submit" class="btn-admin btn-danger btn-admin-small">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
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
    // Auto-hide success messages with enhanced animation
    const alertMessage = document.getElementById('alertMessage');
    if (alertMessage && alertMessage.classList.contains('alert-success')) {
        setTimeout(function() {
            alertMessage.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
            alertMessage.style.opacity = '0';
            alertMessage.style.transform = 'translateY(-30px) scale(0.95)';
            alertMessage.style.filter = 'blur(5px)';
            setTimeout(function() {
                alertMessage.style.display = 'none';
            }, 800);
        }, 3000); // Hide after 3 seconds
    }

    // Enhanced parallax with multiple layers
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('.users-page');
        const hero = document.querySelector('.users-hero');
        
        if (parallax) {
            const speed = scrolled * 0.5;
            parallax.style.transform = `translateY(${speed}px)`;
        }
        
        if (hero) {
            const heroSpeed = scrolled * 0.3;
            hero.style.transform = `translateY(${heroSpeed}px)`;
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

    // Enhanced button hover effects with ripple
    const buttons = document.querySelectorAll('.btn-admin');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
            this.style.boxShadow = '0 15px 50px rgba(0,0,0,0.3)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '';
        });

        // Add ripple effect on click
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
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

// Ban Modal Functions
function showBanModal(userId, username, isAdmin) {
    document.getElementById('banModal').style.display = 'flex';
    document.getElementById('banUserId').value = userId;
    document.getElementById('banUsername').textContent = username;
    
    // Update modal title and warning based on user type
    const modalTitle = document.querySelector('#banModal .modal-header h3');
    const modalBody = document.querySelector('#banModal .modal-body');
    
    if (isAdmin) {
        modalTitle.innerHTML = '<i class="fas fa-ban"></i> Ban Admin User';
        // Add warning for admin bans
        const warningDiv = document.createElement('div');
        warningDiv.className = 'alert alert-warning';
        warningDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> You are about to ban an admin user. This will restrict their access to the admin panel.';
        modalBody.insertBefore(warningDiv, modalBody.firstChild);
    } else {
        modalTitle.innerHTML = '<i class="fas fa-ban"></i> Ban User';
        // Remove any existing warning
        const existingWarning = modalBody.querySelector('.alert-warning');
        if (existingWarning) {
            existingWarning.remove();
        }
    }
    
    document.body.style.overflow = 'hidden';
}

function hideBanModal() {
    document.getElementById('banModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}
</script>

<!-- Ban User Modal -->
<div id="banModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-ban"></i> Ban User</h3>
            <button class="modal-close" onclick="hideBanModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Ban user: <strong id="banUsername"></strong></p>
            
            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to ban this user?');">
                <input type="hidden" name="action" value="ban_user">
                <input type="hidden" name="user_id" id="banUserId">
                
                <div class="form-group">
                    <label for="ban_duration">Ban Duration (Days)</label>
                    <select name="ban_duration" id="ban_duration" required class="category-select">
                        <option value="1">1 Day</option>
                        <option value="2" selected>2 Days</option>
                        <option value="3">3 Days</option>
                        <option value="7">1 Week</option>
                        <option value="14">2 Weeks</option>
                        <option value="30">1 Month</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="ban_reason">Ban Reason</label>
                    <textarea name="ban_reason" id="ban_reason" rows="3" placeholder="Enter ban reason..." class="category-select">Multiple order cancellations in a single day</textarea>
                </div>
                
                <div class="btn-container">
                    <button type="submit" class="btn-admin btn-warning">
                        <i class="fas fa-ban"></i> Ban User
                    </button>
                    <button type="button" class="btn-admin btn-secondary" onclick="hideBanModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
// Close the database connection if it was opened and not closed
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
// Include admin footer
require_once 'includes/admin_footer.php';
?> 