<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Please log in to place an order"]);
    exit();
}

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid JSON data"]);
        exit();
    }
    
    $cart = $input['cart'] ?? [];
    $user_id = $_SESSION['user_id'];
    
    if (empty($cart)) {
        http_response_code(400);
        echo json_encode(["error" => "Cart is empty"]);
        exit();
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Start transaction
        $db->beginTransaction();
        
        // Create order
        $order_query = "INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (:user_id, :total_amount, 'pending', NOW())";
        $order_stmt = $db->prepare($order_query);
        
        // Calculate total
        $total_amount = 0;
        foreach ($cart as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }
        
        $order_stmt->bindParam(':user_id', $user_id);
        $order_stmt->bindParam(':total_amount', $total_amount);
        $order_stmt->execute();
        
        $order_id = $db->lastInsertId();
        
        // Add order items
        $item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
        $item_stmt = $db->prepare($item_query);
        
        foreach ($cart as $item) {
            $item_stmt->bindParam(':order_id', $order_id);
            $item_stmt->bindParam(':product_id', $item['id']);
            $item_stmt->bindParam(':quantity', $item['quantity']);
            $item_stmt->bindParam(':price', $item['price']);
            $item_stmt->execute();
        }
        
        // Commit transaction
        $db->commit();
        
        echo json_encode([
            "success" => true,
            "message" => "Order created successfully",
            "order_id" => $order_id
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollBack();
        http_response_code(500);
        echo json_encode(["error" => "Failed to create order: " . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
?>