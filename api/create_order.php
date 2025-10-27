<?php
// api/create_order.php

// --- Start Session ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Force Error Reporting to JSON ---
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (!headers_sent()) { http_response_code(500); }
        // Ensure header again
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Fatal server error during order creation: ' . $error['message'] . ' in ' . $error['file'] . ' line ' . $error['line']]);
        exit; // Stop after fatal error
    }
});

$pdo = null; // Initialize PDO variable

// --- Main Script Logic ---
try {
    // --- Check if user is logged in ---
     if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_id'])) {
         http_response_code(401); // Unauthorized
         echo json_encode(['success' => false, 'error' => 'User not logged in. Please log in to place an order.']);
         exit;
     }
     $user_id = $_SESSION['user_id']; // Get user ID from session
     // --- End Login Check ---


    // Include the database connection file
    $db_config_path = __DIR__ . '/../config/db.php';
    if (!file_exists($db_config_path)) {
        throw new Exception("Database config file not found at: " . $db_config_path);
    }
    require_once $db_config_path;

    if (!function_exists('getDbConnection')) {
        throw new Exception('getDbConnection function is not defined. Check db.php.');
    }

    // Get the raw POST data
    $json_data = file_get_contents('php://input');
    if ($json_data === false) { throw new Exception("Could not read input data."); }

    // Decode the JSON data
    $request_data = json_decode($json_data, true);

    // Input Validation
    if ($request_data === null || !isset($request_data['cart']) || !is_array($request_data['cart']) || empty($request_data['cart'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid or empty cart data received.']);
        exit;
    }

    $cart_items = $request_data['cart'];
    $pdo = getDbConnection(); // Assign connection here

    if ($pdo === null) { throw new Exception('Database connection failed. Check config and server status.'); }

    // Begin a transaction
    if (!$pdo->beginTransaction()) { throw new Exception("Failed to start database transaction."); }

    // Calculate total price on the server-side
    $total_price = 0;
    $product_ids = [];
    $quantity_map = [];

    foreach ($cart_items as $item) {
        if (!isset($item['id']) || !isset($item['quantity']) || !is_numeric($item['id']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0) {
            throw new Exception("Invalid item data in cart.");
        }
        $product_ids[] = (int)$item['id'];
        $quantity_map[(int)$item['id']] = (int)$item['quantity'];
    }

    // Fetch product prices from DB
     if (!empty($product_ids)) {
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        $stmt_check_price = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
        if (!$stmt_check_price) throw new PDOException("Failed to prepare price check.");
        if (!$stmt_check_price->execute($product_ids)) throw new PDOException("Failed to execute price check.");

        $db_products = $stmt_check_price->fetchAll(PDO::FETCH_KEY_PAIR);

        if (count($db_products) !== count(array_unique($product_ids))) { // Check against unique IDs
             $missing_ids = array_diff($product_ids, array_keys($db_products));
             throw new Exception("Product ID(s) " . implode(', ', $missing_ids) . " not found in database during price check.");
        }

        foreach ($product_ids as $id) {
            if (!isset($db_products[$id]) || !isset($quantity_map[$id])) {
                throw new Exception("Data inconsistency for product ID {$id} during price calculation.");
            }
             $total_price += $db_products[$id] * $quantity_map[$id];
        }
    } else {
         throw new Exception("Cart is logically empty after validation.");
    }


    // Create the main order entry using the SESSION user_id
    $stmt_order = $pdo->prepare("INSERT INTO orders (user_id, total_amount, order_status) VALUES (?, ?, ?)");
    if (!$stmt_order) throw new PDOException("Failed to prepare order insert statement.");
    if (!$stmt_order->execute([$user_id, $total_price, 'Pending'])) {
         $errorInfo = $stmt_order->errorInfo();
         throw new PDOException("Failed to execute order insert: " . ($errorInfo[2] ?? 'Unknown error'));
    }

    $order_id = $pdo->lastInsertId();
     if (!$order_id || $order_id === '0') throw new Exception("Failed to retrieve last insert ID for order. Check table auto_increment.");


    // Create entries for each item in the order_items table
    $stmt_items = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)");
    if (!$stmt_items) throw new PDOException("Failed to prepare order items insert statement.");

    foreach ($product_ids as $id) {
         if (isset($db_products[$id]) && isset($quantity_map[$id])) {
            if (!$stmt_items->execute([$order_id, $id, $quantity_map[$id], $db_products[$id]])) {
                 $errorInfo = $stmt_items->errorInfo();
                 throw new PDOException("Failed to execute order items insert for product ID {$id}: " . ($errorInfo[2] ?? 'Unknown error'));
            }
         } else {
             // This case should ideally not happen due to earlier checks, but good to have
             throw new Exception("Mismatch in product data during order item insertion for ID {$id}.");
         }
    }

    // Commit the transaction
    if (!$pdo->commit()) { throw new Exception("Failed to commit database transaction."); }

    // Send success response
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    if ($pdo && $pdo->inTransaction()) { $pdo->rollBack(); }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database operation failed: ' . $e->getMessage()]);
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) { $pdo->rollBack(); }
    // Determine appropriate error code based on message content
    $errorCode = (strpos($e->getMessage(), 'Invalid item data') !== false || strpos($e->getMessage(), 'not found') !== false) ? 400 : 500;
    http_response_code($errorCode);
    echo json_encode(['success' => false, 'error' => 'Failed to create order: ' . $e->getMessage()]);
}

exit; // Ensure script stops here
?>

