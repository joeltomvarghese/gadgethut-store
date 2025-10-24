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
                 header('Content-Type: application/json'); // Ensure header
                 echo json_encode(['success' => false, 'error' => 'Fatal PHP Error: ' . $error['message']]);
            }
        }
    });
    
    // --- Main Script Logic ---
    try {
        // Include the database connection file
        $db_config_path = __DIR__ . '/../config/db.php';
        if (!file_exists($db_config_path)) {
            throw new Exception("Database config file not found.");
        }
        require_once $db_config_path;
    
        if (!function_exists('getDbConnection')) {
            throw new Exception('getDbConnection function is not defined.');
        }
    
        // Get the raw POST data
        $json_data = file_get_contents('php://input');
        if ($json_data === false) {
            throw new Exception("Could not read input data.");
        }
        $request_data = json_decode($json_data, true);
    
        // Validate input
        if ($request_data === null || !isset($request_data['product_id']) || !isset($request_data['image_url']) || !is_numeric($request_data['product_id']) || !filter_var($request_data['image_url'], FILTER_VALIDATE_URL)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'error' => 'Invalid input: Product ID must be numeric and Image URL must be a valid URL.']);
            exit;
        }
    
        $product_id = (int)$request_data['product_id'];
        $image_url = $request_data['image_url'];
    
        $pdo = getDbConnection();
        if ($pdo === null) {
            throw new Exception('Database connection failed.');
        }
    
        // Prepare the UPDATE statement
        $stmt = $pdo->prepare("UPDATE products SET image_url = ? WHERE id = ?");
        
        if (!$stmt) {
             $errorInfo = $pdo->errorInfo();
             throw new PDOException("Failed to prepare update statement: " . ($errorInfo[2] ?? 'Unknown PDO error'));
        }
    
        // Execute the update
        if (!$stmt->execute([$image_url, $product_id])) {
            $errorInfo = $stmt->errorInfo();
            throw new PDOException("Failed to execute update statement: " . ($errorInfo[2] ?? 'Unknown PDO error'));
        }
    
        // Check if any row was actually updated
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            // This might happen if the product ID doesn't exist
            http_response_code(404); // Not Found
            echo json_encode(['success' => false, 'error' => 'Product not found or image URL was already the same.']);
        }
    
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database Error: ' . $e->getMessage()]);
        exit;
    
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Server Error: ' . $e->getMessage()]);
        exit;
    }
    
    ?>
    

