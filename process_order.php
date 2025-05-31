<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is NOT admin
if (!isset($_SESSION['user_id']) || (isset($_SESSION['is_admin']) && $_SESSION['is_admin'])) {
    $response = ['success' => false, 'message' => 'Admins cannot place orders.'];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user_id'];
$response = ['success' => false, 'message' => ''];

try {
    // Start transaction
    $conn->begin_transaction();

    // Get cart items
    $cart_query = "SELECT c.*, p.product_name, p.price 
                  FROM cart c 
                  JOIN products p ON c.product_id = p.product_id 
                  WHERE c.user_id = ?";
    $cart_stmt = $conn->prepare($cart_query);
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();

    if ($cart_result->num_rows === 0) {
        throw new Exception('Cart is empty');
    }

    // Calculate total
    $total = 0;
    $items = [];
    while ($item = $cart_result->fetch_assoc()) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        $items[] = $item;
    }

    // Get payment method and address details
    $payment_method = $_POST['payment_method'];
    $address_type = $_POST['address_type'];

    // Handle address
    if ($address_type === 'new') {
        $full_name = $_POST['full_name'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $zip_code = $_POST['zip_code'];
        $phone = $_POST['phone'];

        // Save new address
        $address_query = "INSERT INTO addresses (user_id, full_name, address, city, state, zip_code, phone) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
        $address_stmt = $conn->prepare($address_query);
        $address_stmt->bind_param("issssss", $user_id, $full_name, $address, $city, $state, $zip_code, $phone);
        $address_stmt->execute();
        $address_id = $conn->insert_id;
    } else {
        $address_id = $_POST['saved_address_id'];
    }

    // Create order
    $order_query = "INSERT INTO orders (user_id, address_id, total_amount, payment_method, status) 
                   VALUES (?, ?, ?, ?, 'pending')";
    $order_stmt = $conn->prepare($order_query);
    $order_stmt->bind_param("iids", $user_id, $address_id, $total, $payment_method);
    $order_stmt->execute();
    $order_id = $conn->insert_id;

    // Add order items
    $order_items_query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $order_items_stmt = $conn->prepare($order_items_query);

    foreach ($items as $item) {
        $order_items_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $order_items_stmt->execute();
    }

    // Clear cart
    $clear_cart_query = "DELETE FROM cart WHERE user_id = ?";
    $clear_cart_stmt = $conn->prepare($clear_cart_query);
    $clear_cart_stmt->bind_param("i", $user_id);
    $clear_cart_stmt->execute();

    // Create admin notification
    $notification_query = "INSERT INTO admin_notifications (order_id, message, type, status) 
                         VALUES (?, ?, 'new_order', 'unread')";
    $notification_stmt = $conn->prepare($notification_query);
    $message = "New order #$order_id received from user #$user_id";
    $notification_stmt->bind_param("is", $order_id, $message);
    $notification_stmt->execute();

    // Commit transaction
    $conn->commit();

    // Prepare order summary for response
    $order_summary = [
        'order_id' => $order_id,
        'total' => $total,
        'items' => $items,
        'payment_method' => $payment_method,
        'address' => $address_type === 'new' ? [
            'full_name' => $full_name,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'zip_code' => $zip_code,
            'phone' => $phone
        ] : null
    ];

    $response = [
        'success' => true,
        'message' => 'Order placed successfully!',
        'order_summary' => $order_summary
    ];

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $response['message'] = 'Error: ' . $e->getMessage();
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit(); 