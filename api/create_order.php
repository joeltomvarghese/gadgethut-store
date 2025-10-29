<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Please login to place order']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$cart = $input['cart'] ?? [];

if (empty($cart)) {
    echo json_encode(['success' => false, 'error' => 'Cart is empty']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Begin transaction
    $db->beginTransaction();
    
    // Calculate total
    $total = 0;
    foreach ($cart as $item) {
        $total += floatval($item['price']) * intval($item['quantity']);
    }
    
    // Create order
    $orderQuery = "INSERT INTO orders (user_id, total_amount) VALUES (?, ?)";
    $orderStmt = $db->prepare($orderQuery);
    $orderStmt->execute([$_SESSION['user_id'], $total]);
    $order_id = $db->lastInsertId();
    
    // Add order items
    $itemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $itemStmt = $db->prepare($itemQuery);
    
    foreach ($cart as $item) {
        $itemStmt->execute([
            $order_id, 
            intval($item['id']), 
            intval($item['quantity']), 
            floatval($item['price'])
        ]);
    }
    
    // Commit transaction
    $db->commit();
    
    echo json_encode(['success' => true, 'order_id' => $order_id, 'message' => 'Order placed successfully!']);
    
} catch (PDOException $e) {
    // Rollback on error
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Order creation error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to create order: ' . $e->getMessage()]);
}
?>