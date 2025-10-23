<?php
// This API endpoint receives cart data and creates a new order in the database.

// Set the response content type to JSON
header('Content-Type: application/json');

// Include the database connection file
require_once '../config/db.php';

// 1. Get the raw POST data
// The cart data is sent as a JSON string in the body of the POST request.
// file_get_contents('php://input') reads this raw body.
$json_data = file_get_contents('php://input');

// 2. Decode the JSON data
// json_decode converts the JSON string into a PHP associative array.
$cart = json_decode($json_data, true); // true for associative array

// 3. Validate the data
// Check if the cart data is valid (it's an array and not empty).
if (!is_array($cart) || empty($cart)) {
    // If data is bad, send a 400 Bad Request status.
    http_response_code(400);
    echo json_encode(['error' => 'Invalid cart data.']);
    exit;
}

// We will use a database transaction.
// This ensures that ALL queries must succeed, or NONE of them will.
// This prevents creating an order (in 'orders' table) without order items
// (in 'order_items' table).
try {
    // 4. Begin the transaction
    $pdo->beginTransaction();

    // 5. Create the main order entry
    // We assume a user_id of 1 (for a test user) and calculate total price on the server.
    
    // Server-side price calculation
    $total_price = 0;
    $product_ids = array_keys($cart); // Get all product IDs from the cart
    
    // Create placeholders for the IN() clause (e.g., ?,?,?)
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    // Fetch the real prices from the database to prevent client-side tampering
    $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $products_from_db = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // Fetches as [id => price]

    // Calculate the total price securely
    foreach ($cart as $product_id => $item) {
        if (isset($products_from_db[$product_id])) {
            $total_price += $products_from_db[$product_id] * $item['quantity'];
        } else {
            // If a product ID from the cart doesn't exist in the DB, abort.
            throw new Exception("Product ID $product_id not found.");
        }
    }

    // Insert the main order into the 'orders' table
    $order_stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
    $order_stmt->execute([1, $total_price]); // Using 1 as a hardcoded user_id for this demo

    // 6. Get the ID of the order we just created
    $order_id = $pdo->lastInsertId();

    // 7. Insert each cart item into the 'order_items' table
    // We prepare one statement and execute it multiple times for efficiency.
    $item_stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price_per_item) 
        VALUES (?, ?, ?, ?)
    ");

    foreach ($cart as $product_id => $item) {
        // Use the price we fetched from the database
        $price_per_item = $products_from_db[$product_id];
        
        $item_stmt->execute([
            $order_id,
            $product_id,
            $item['quantity'],
            $price_per_item
        ]);
    }

    // 8. If all queries were successful, commit the transaction
    $pdo->commit();

    // 9. Send a success response back to the client
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully!',
        'order_id' => $order_id
    ]);

} catch (Exception $e) {
    // 10. If any query failed, roll back the transaction
    // This undoes all the changes made during this transaction.
    $pdo->rollBack();

    // Send a 500 server error status and the error message
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to create order: ' . $e->getMessage()
    ]);
}

?>

