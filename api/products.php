<?php
// --- Start Output Buffering ---
ob_start();

// --- Force Error Reporting to JSON ---
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

// --- Shutdown function for fatal errors ---
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (ob_get_level() > 0) ob_end_clean(); // Clean buffer if fatal error occurred
        if (!headers_sent()) {
             http_response_code(500);
             // Ensure header again just in case
             header('Content-Type: application/json');
             echo json_encode(['error' => 'Fatal PHP Error: ' . $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line']]);
        }
        exit; // Stop script execution after handling fatal error
    }
});

$response_data = []; // Initialize response

try {
    // --- Move Include and Connection Inside Try ---
    $db_config_path = __DIR__ . '/../config/db.php';
    if (!file_exists($db_config_path)) {
        throw new Exception("Database config file not found at: " . $db_config_path);
    }
    require_once $db_config_path;

    if (!function_exists('getDbConnection')) {
        throw new Exception('getDbConnection function is not defined after including db.php. Check db.php for syntax errors or ensure it was included correctly.');
    }

    $pdo = getDbConnection(); // Get PDO connection

    // Explicitly check if connection failed
    if ($pdo === null) {
        throw new Exception('Database connection failed. getDbConnection() returned null. Check config/db.php credentials and MySQL server status.');
    }
    // --- End Connection Logic ---


    // Prepare the SQL query to select ALL products
    $stmt = $pdo->prepare("
        SELECT
            id, name, description, price, image_url, stock, rating,
            `condition`, usage_duration, condition_notes
        FROM products
        ORDER BY id ASC
    ");

    if (!$stmt) {
         $errorInfo = $pdo->errorInfo();
         throw new PDOException("Failed to prepare product select statement: " . ($errorInfo[2] ?? 'Unknown PDO error'));
    }

    // Execute the query (no parameters needed for selecting all)
    if (!$stmt->execute()) {
        $errorInfo = $stmt->errorInfo();
         throw new PDOException("Failed to execute product select statement: " . ($errorInfo[2] ?? 'Unknown PDO error'));
    }

    // Fetch all products
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If fetch was successful (even if empty), set response
    $response_data = $products; // Assign fetched products

} catch (PDOException $e) {
    http_response_code(500);
    $response_data = ['error' => 'Database Query Error: ' . $e->getMessage()];

} catch (Exception $e) {
    http_response_code(500);
    $response_data = ['error' => 'Server Error: ' . $e->getMessage()];
}

// --- Clean Buffer & Send JSON ---
ob_end_clean(); // Discard any buffered output (like notices/warnings)

// Set header again just to be safe (it might have been cleared by ob_end_clean if output started)
if (!headers_sent()) {
    header('Content-Type: application/json');
}


// Send the final JSON response
echo json_encode($response_data);
exit; // Important to stop script execution here

?>

