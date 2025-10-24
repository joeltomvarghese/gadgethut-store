<?php
// --- Start Output Buffering ---
// This captures any stray output (like PHP warnings/errors)
ob_start();

// --- Force Error Reporting to JSON ---
error_reporting(E_ALL); // Report all errors
ini_set('display_errors', 0); // Prevent errors from printing directly

// Register shutdown function (still useful for fatal errors)
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        // If output buffering is active, clear it
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        // If headers haven't been sent yet
        if (!headers_sent()) {
             http_response_code(500);
             header('Content-Type: application/json'); // Ensure JSON header
             echo json_encode([
                 'success' => false, // Use success flag consistently
                 'error' => 'Fatal PHP Error: ' . $error['message'],
                 'file' => $error['file'],
                 'line' => $error['line']
             ]);
        }
    }
});

$response_data = []; // Initialize response data array

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

    // Prepare the SQL query
    $stmt = $pdo->prepare("
        SELECT 
            id, name, description, price, image_url, stock, rating, 
            `condition`, usage_duration, condition_notes 
        FROM products 
        ORDER BY id ASC
    ");
    
    if (!$stmt) {
         // Use the errorInfo for more details if prepare fails
         $errorInfo = $pdo->errorInfo();
         throw new PDOException("Failed to prepare product select statement: " . ($errorInfo[2] ?? 'Unknown error'));
    }

    // Execute the query
    if (!$stmt->execute()) {
        // Use the errorInfo for more details if execute fails
        $errorInfo = $stmt->errorInfo();
         throw new PDOException("Failed to execute product select statement: " . ($errorInfo[2] ?? 'Unknown error'));
    }

    // Fetch all products
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Set success data
    $response_data = $products; // Assign fetched products directly


} catch (PDOException $e) {
    http_response_code(500); 
    $response_data = ['error' => 'Database Query Error: ' . $e->getMessage()];

} catch (Exception $e) {
    http_response_code(500); 
    $response_data = ['error' => 'Server Error: ' . $e->getMessage()];
}

// --- Clean Output Buffer and Send JSON ---
// Discard anything that might have been outputted (errors, warnings, notices)
ob_end_clean(); 

// Set JSON header *again* just before output, in case it was overwritten
header('Content-Type: application/json');

// Send the final JSON response (either products or error message)
echo json_encode($response_data);
exit; // Ensure script stops here

?>

