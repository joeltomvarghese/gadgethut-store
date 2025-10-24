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

        // Check if the connection function exists
        if (!function_exists('getDbConnection')) {
            throw new Exception('getDbConnection function is not defined. Check db.php.');
        }

        // Get product ID from query string
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Product ID is required and must be numeric.']);
            exit;
        }
        $product_id = (int)$_GET['id'];

        $pdo = getDbConnection(); // Get PDO connection

        if ($pdo === null) {
            throw new Exception('Database connection failed.');
        }

        // Prepare the SQL query to select a single product by ID
        $stmt = $pdo->prepare("
            SELECT 
                id, name, description, price, image_url, stock, rating, 
                `condition`, usage_duration, condition_notes 
            FROM products 
            WHERE id = ?
        ");
        
        if (!$stmt) {
             $errorInfo = $pdo->errorInfo();
             throw new PDOException("Failed to prepare product select statement: " . ($errorInfo[2] ?? 'Unknown error'));
        }

        // Execute the query
        if (!$stmt->execute([$product_id])) {
            $errorInfo = $stmt->errorInfo();
             throw new PDOException("Failed to execute product select statement: " . ($errorInfo[2] ?? 'Unknown error'));
        }

        // Fetch the product
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            http_response_code(404); // Not Found
            echo json_encode(['error' => 'Product not found.']);
            exit;
        }

        // Send the product back as JSON
        echo json_encode($product);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database Query Error: ' . $e->getMessage()]);
        exit;

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server Error: ' . $e->getMessage()]);
        exit;
    }

    ?>
    

