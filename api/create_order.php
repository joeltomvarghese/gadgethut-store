<?php
// --- Force Error Reporting to JSON ---
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (!headers_sent()) {
             http_response_code(500);
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
    // Include the database connection file
    $db_config_path = __DIR__ . '/../config/db.php';
    if (!file_exists($db_config_path)) {
        throw new Exception("Database config file not found at: " . $db_config_path);
    }
    require_once $db_config_path;

    // *** ADDED CHECK: Verify function exists immediately after include ***
    if (!function_exists('getDbConnection')) {
        throw new Exception('getDbConnection function is not defined after including db.php. Check db.php for errors.');
    }

    // Get the raw POST data
    $json_data = file_get_contents('php://input');
    if ($json_data === false) {
        throw new Exception("Could not read input data.");
    }

    // Decode the JSON data
    $request_data = json_decode($json_data, true);

    // --- Input Validation ---
    if ($request_data === null || !isset($request_data['cart']) || !is_array($request_data['cart']) || empty($request_data['cart'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid or empty cart data received.']);
        exit;
    }

    $cart_items = $request_data['cart'];
    $pdo = getDbConnection(); // Call the function

    // Check if connection was successful (getDbConnection returns null on failure now)
    if ($pdo === null) {
        throw new Exception('Database connection failed. Check config/db.php and MySQL server status.');
    }

    // --- Database Operations ---
    if (!$pdo->beginTransaction()) {
         throw new Exception("Failed to start database transaction.");
    }

    // Calculate total price and prepare IDs
    $total_price = 0;
    $product_ids = [];
    $quantity_map = [];

    foreach ($cart_items as $item) {
        if (!isset($item['id'], $item['quantity'], $item['price']) || !is_numeric($item['id']) || !is_numeric($item['quantity']) || !is_numeric($item['price']) || $item['quantity'] <= 0) {
            throw new Exception("Invalid item data in cart.");
        }
        $product_ids[] = (int)$item['id'];
        $quantity_map[(int)$item['id']] = (int)$item['quantity'];
    }

    // Fetch product prices from DB
     if (!empty($product_ids)) {
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        $stmt_check_price = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
        if (!$stmt_check_price) throw new PDOException("Failed to prepare price check statement.");
        if (!$stmt_check_price->execute($product_ids)) throw new PDOException("Failed to execute price check.");

        $db_products = $stmt_check_price->fetchAll(PDO::FETCH_KEY_PAIR);

        if (count($db_products) !== count($product_ids)) {
             $missing_ids = array_diff($product_ids, array_keys($db_products));
             throw new Exception("Product ID(s) " . implode(', ', $missing_ids) . " not found in database.");
        }

        foreach ($product_ids as $id) {
             $total_price += $db_products[$id] * $quantity_map[$id];
        }
    } else {
         throw new Exception("Cart is empty after validation.");
    }

    // Create the main order entry
    $user_id = 1; // Assume user ID 1
    $stmt_order = $pdo->prepare("INSERT INTO orders (user_id, total_amount, order_status) VALUES (?, ?, ?)");
    if (!$stmt_order) throw new PDOException("Failed to prepare order insert statement.");
    if (!$stmt_order->execute([$user_id, $total_price, 'Pending'])) throw new PDOException("Failed to execute order insert.");

    $order_id = $pdo->lastInsertId();
     if (!$order_id) throw new Exception("Failed to retrieve last insert ID for order.");

    // Create order items entries
    $stmt_items = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)");
    if (!$stmt_items) throw new PDOException("Failed to prepare order items insert statement.");

    foreach ($product_ids as $id) {
         if (isset($db_products[$id], $quantity_map[$id])) {
            if (!$stmt_items->execute([$order_id, $id, $quantity_map[$id], $db_products[$id]])) throw new PDOException("Failed to execute order items insert for product ID {$id}.");
         } else {
             throw new Exception("Mismatch in product data during order item insertion for ID {$id}.");
         }
    }

    // Commit the transaction
    if (!$pdo->commit()) {
         throw new Exception("Failed to commit database transaction.");
    }

    // Send success response
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    if ($pdo && $pdo->inTransaction()) { // Check if $pdo is set before calling inTransaction
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database operation failed: ' . $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    exit;

} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) { // Check if $pdo is set
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to create order: ' . $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    exit;
}

?>

