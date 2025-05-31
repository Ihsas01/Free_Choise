<?php
// Script to add the 'Other' category to the database if it doesn't exist.

// Include database configuration
require_once __DIR__ . '/../../includes/db_config.php';

// Category name to add
$category_name = 'Other';

// Check if the category already exists
$check_query = "SELECT category_id FROM categories WHERE category_name = ? LIMIT 1";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("s", $category_name);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo "Category '" . htmlspecialchars($category_name) . "' already exists.\n";
} else {
    // Insert the category if it doesn't exist
    $insert_query = "INSERT INTO categories (category_name) VALUES (?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("s", $category_name);
    
    if ($insert_stmt->execute()) {
        echo "Category '" . htmlspecialchars($category_name) . "' added successfully.\n";
    } else {
        echo "Error adding category: " . $conn->error . "\n";
    }
    $insert_stmt->close();
}

$check_stmt->close();

// Close the database connection
if (isset($conn) && $conn->ping()) {
    $conn->close();
}

?> 