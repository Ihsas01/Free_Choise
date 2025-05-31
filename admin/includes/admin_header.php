<?php
// Admin specific header

// Ensure admin is logged in (basic check, full check should be at the start of each admin page)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

$page_title = $page_title ?? 'Admin Panel'; // Use provided page title or default
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel<?php echo isset($page_title) ? ' - ' . htmlspecialchars($page_title) : ''; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Include main style for basic resets/utilities if needed -->
    <link rel="stylesheet" href="../assets/css/admin.css"> <!-- Admin specific styles -->
    <!-- Add Font Awesome or other icon library if needed -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header class="admin-header">
    <div class="container">
        <h1>Admin Panel</h1>
        <nav>
            <ul class="admin-nav">
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="../index.php"><i class="fas fa-store"></i> View Website</a></li>
                <li><a href="../logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="admin-main-content"> <!-- Main content area for admin pages -->
    <div class="container">
        <?php if(isset($message)): ?>
            <div class="admin-alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?> 