<?php
// --- Force Error Reporting to JSON ---
error_reporting(E_ALL); // Report all errors
ini_set('display_errors', 0); // Prevent errors from printing directly to output
header('Content-Type: application/json'); // Set JSON header immediately

// Register a function to catch fatal errors and output JSON
register_shutdown_function(function() {
    $error = error_get_last();
    // Check if it's a fatal error type
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        // If headers haven't been sent yet (i.e., no JSON output started)
        if (!headers_sent()) {
             http_response_code(500); // Internal Server Error
             echo json_encode([
                 'success' => false,
                 'error' => 'Fatal PHP Error: ' . $error['message'],
                 'file' => $error['file'],
                 'line' => $error['line']
             ]);
        }
    }
});

// --- Main Script Logic ---
try {
    // Include the database connection file - Use __DIR__ for reliability
    require_once __DIR__ . '/../config/db.php';

    // Get the raw POST data
    $json_data = file_get_contents('php://input');
    if ($json_data === false) {
        throw new Exception("Could not read input data.");
    }

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
        throw new Exception('Database connection failed.'); // Throw exception instead of echo
    }

    // --- Database Operations ---

    // 1. Begin a transaction
    if (!$pdo->beginTransaction()) {
         throw new Exception("Failed to start database transaction.");
    }

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
        if (!$stmt_check_price) throw new PDOException("Failed to prepare price check statement.");
        if (!$stmt_check_price->execute($product_ids)) throw new PDOException("Failed to execute price check.");

        $db_products = $stmt_check_price->fetchAll(PDO::FETCH_KEY_PAIR); // Fetch as [id => price]

        if (count($db_products) !== count($product_ids)) {
             // Find which ID is missing
             $missing_ids = array_diff($product_ids, array_keys($db_products));
             throw new Exception("Product ID(s) " . implode(', ', $missing_ids) . " not found in database.");
        }


        foreach ($product_ids as $id) {
             // This check is now redundant due to count check above, but keep for clarity
             // if (!isset($db_products[$id])) {
             //     throw new Exception("Product ID {$id} not found in database.");
             // }
             $total_price += $db_products[$id] * $quantity_map[$id]; // Use DB price
        }
    } else {
         throw new Exception("Cart is empty after validation.");
    }


    // 3. Create the main order entry
    // Assume a user ID (e.g., 1 for a guest or logged-in user)
    $user_id = 1;
    $stmt_order = $pdo->prepare("INSERT INTO orders (user_id, total_amount, order_status) VALUES (?, ?, ?)");
    if (!$stmt_order) throw new PDOException("Failed to prepare order insert statement.");
    if (!$stmt_order->execute([$user_id, $total_price, 'Pending'])) throw new PDOException("Failed to execute order insert.");

    $order_id = $pdo->lastInsertId(); // Get the ID of the order just created
     if (!$order_id) throw new Exception("Failed to retrieve last insert ID for order.");


    // 4. Create entries for each item in the order_items table
    $stmt_items = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)");
    if (!$stmt_items) throw new PDOException("Failed to prepare order items insert statement.");

    foreach ($product_ids as $id) {
         if (isset($db_products[$id]) && isset($quantity_map[$id])) {
            if (!$stmt_items->execute([$order_id, $id, $quantity_map[$id], $db_products[$id]])) throw new PDOException("Failed to execute order items insert for product ID {$id}.");
         } else {
             // This case should ideally not happen due to earlier checks, but good to have
             throw new Exception("Mismatch in product data during order item insertion for ID {$id}.");
         }
    }

    // 5. Commit the transaction
    if (!$pdo->commit()) {
         throw new Exception("Failed to commit database transaction.");
    }

    // 6. Send success response
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    // Roll back transaction if a database error occurred
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500); // Internal Server Error
    // Log the detailed error: error_log("Database Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database operation failed: ' . $e->getMessage(), 'trace' => $e->getTraceAsString()]); // Send specific DB error + trace
    exit;

} catch (Exception $e) {
    // Roll back transaction if a general error occurred
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500); // Use 500 for general server-side issues unless it's clearly client data (400)
    // Log the detailed error: error_log("General Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to create order: ' . $e->getMessage(), 'trace' => $e->getTraceAsString()]); // Send specific general error + trace
    exit;
}

?>

