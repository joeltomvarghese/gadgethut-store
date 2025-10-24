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
    $db_config_path = __DIR__ . '/../config/db.php';
    if (!file_exists($db_config_path)) {
        throw new Exception("Database config file not found at: " . $db_config_path);
    }
    require_once $db_config_path;

    // Check if the connection function exists
    if (!function_exists('getDbConnection')) {
        throw new Exception('getDbConnection function is not defined after including db.php. Check db.php for errors.');
    }

    $pdo = getDbConnection(); // Get PDO connection

    // Check if connection was successful
    if ($pdo === null) {
        throw new Exception('Database connection failed. Check config/db.php and MySQL server status.');
    }

    // Prepare the SQL query to select all products, ordered by ID
    // Added all necessary columns for the refurbished site
    $stmt = $pdo->prepare("
        SELECT 
            id, 
            name, 
            description, 
            price, 
            image_url, 
            stock, 
            rating, 
            `condition`,       -- Use backticks if 'condition' is a reserved word 
            usage_duration, 
            condition_notes 
        FROM products 
        ORDER BY id ASC
    ");
    
    if (!$stmt) {
         throw new PDOException("Failed to prepare product select statement.");
    }

    // Execute the query
    if (!$stmt->execute()) {
         throw new PDOException("Failed to execute product select statement.");
    }

    // Fetch all products as an associative array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Send the products back as JSON
    echo json_encode($products); // Directly encode the array

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error for database issues
    echo json_encode(['error' => 'Database Query Error: ' . $e->getMessage()]);
    exit;

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error for other issues
    echo json_encode(['error' => 'Server Error: ' . $e->getMessage()]);
    exit;
}

?>

