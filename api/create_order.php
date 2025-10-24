<?php
// Set the response content type to JSON early to prevent HTML output on errors
header('Content-Type: application/json');

// Include the database connection file - Use __DIR__ for reliability
require_once __DIR__ . '/../config/db.php';

// Get the raw POST data
$json_data = file_get_contents('php://input');

// Decode the JSON data into a PHP associative array
$request_data = json_decode($json_data, true); // true for associative array

// --- Input Validation ---
if ($request_data === null || !isset($request_data['cart']) || !is_array($request_data['cart']) || empty($request_data['cart'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => 'Invalid or empty cart data received.']);
    exit;
}

$cart_items = $request_data['cart'];
$pdo = getDbConnection(); // Get PDO connection from config/db.php

if (!$pdo) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
    exit;
}

// --- Database Operations ---
try {
    // 1. Begin a transaction
    $pdo->beginTransaction();

    // 2. Calculate total price on the server-side for security
    $total_price = 0;
    $product_ids = [];
    $quantity_map = []; // Store quantity per product ID

    foreach ($cart_items as $item) {
        // Validate basic item structure
        if (!isset($item['id']) || !isset($item['quantity']) || !isset($item['price']) || !is_numeric($item['id']) || !is_numeric($item['quantity']) || !is_numeric($item['price']) || $item['quantity'] <= 0) {
            throw new Exception("Invalid item data in cart.");
        }
        $product_ids[] = (int)$item['id'];
        $quantity_map[(int)$item['id']] = (int)$item['quantity'];
        // Note: We'll fetch the price from the DB for security, not trust the client's price
    }

    // Fetch product prices from DB to verify and calculate total
     if (!empty($product_ids)) {
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        $stmt_check_price = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
        $stmt_check_price->execute($product_ids);
        $db_products = $stmt_check_price->fetchAll(PDO::FETCH_KEY_PAIR); // Fetch as [id => price]

        foreach ($product_ids as $id) {
            if (!isset($db_products[$id])) {
                throw new Exception("Product ID {$id} not found in database.");
            }
             $total_price += $db_products[$id] * $quantity_map[$id]; // Use DB price
        }
    } else {
         throw new Exception("Cart is empty after validation.");
    }


    // 3. Create the main order entry
    // Assume a user ID (e.g., 1 for a guest or logged-in user)
    $user_id = 1; 
    $stmt_order = $pdo->prepare("INSERT INTO orders (user_id, total_amount, order_status) VALUES (?, ?, ?)");
    $stmt_order->execute([$user_id, $total_price, 'Pending']);
    $order_id = $pdo->lastInsertId(); // Get the ID of the order just created

    // 4. Create entries for each item in the order_items table
    $stmt_items = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)");
    
    foreach ($product_ids as $id) {
         if (isset($db_products[$id]) && isset($quantity_map[$id])) {
            $stmt_items->execute([$order_id, $id, $quantity_map[$id], $db_products[$id]]);
         } else {
             // This case should ideally not happen due to earlier checks, but good to have
             throw new Exception("Mismatch in product data during order item insertion for ID {$id}.");
         }
    }

    // 5. Commit the transaction
    $pdo->commit();

    // 6. Send success response
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    // Roll back transaction if a database error occurred
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500); // Internal Server Error
    // Log the detailed error: error_log("Database Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database operation failed: ' . $e->getMessage()]); // Send specific DB error
    exit;

} catch (Exception $e) {
    // Roll back transaction if a general error occurred
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400); // Bad Request (likely invalid data) or 500
    // Log the detailed error: error_log("General Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to create order: ' . $e->getMessage()]); // Send specific general error
    exit;
}

?>

