<?php
require_once 'config/database.php';

// Read the SQL file
$sql = file_get_contents('database.sql');

// Split the SQL file into individual statements
$statements = array_filter(array_map('trim', explode(';', $sql)));

try {
    // Begin transaction
    $conn->begin_transaction();

    // Execute each statement
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            if (!$conn->query($statement)) {
                throw new Exception("Error executing statement: " . $conn->error);
            }
        }
    }

    // Commit transaction
    $conn->commit();
    echo "Database tables created successfully!";
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo "Error: " . $e->getMessage();
} 