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
                 echo json_encode(['success' => false, 'error' => 'Fatal PHP Error: ' . $error['message'] . ' in ' . $error['file'] . ' line ' . $error['line']]);
            }
        }
    });

    try {
        // Include DB connection
        $db_config_path = __DIR__ . '/../config/db.php';
        if (!file_exists($db_config_path)) {
            throw new Exception("Database config file not found.");
        }
        require_once $db_config_path;

        if (!function_exists('getDbConnection')) {
            throw new Exception('getDbConnection function is not defined.');
        }

        // Check request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['success' => false, 'error' => 'Invalid request method. Only POST is allowed.']);
            exit;
        }

        // Get POST data
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $image_url = filter_input(INPUT_POST, 'image_url', FILTER_SANITIZE_URL); // Basic URL sanitization

        // Validate input
        if ($product_id === false || $product_id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid Product ID.']);
            exit;
        }
        if ($image_url === null || $image_url === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Image URL cannot be empty.']);
            exit;
        }
        // More robust URL validation if needed: filter_var($image_url, FILTER_VALIDATE_URL)
        if (filter_var($image_url, FILTER_VALIDATE_URL) === false) {
             http_response_code(400);
             echo json_encode(['success' => false, 'error' => 'Invalid Image URL format.']);
             exit;
        }


        $pdo = getDbConnection();
        if ($pdo === null) {
            throw new Exception('Database connection failed.');
        }

        // Prepare update statement
        $stmt = $pdo->prepare("UPDATE products SET image_url = ? WHERE id = ?");
        if (!$stmt) {
            $errorInfo = $pdo->errorInfo();
            throw new PDOException("Failed to prepare update statement: " . ($errorInfo[2] ?? 'Unknown error'));
        }

        // Execute update
        $success = $stmt->execute([$image_url, $product_id]);

        if ($success) {
            // Check if any row was actually updated
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Image URL updated successfully.']);
            } else {
                 http_response_code(404); // Not Found or No Change
                 echo json_encode(['success' => false, 'error' => 'Product ID not found or URL was the same.']);
            }
        } else {
             $errorInfo = $stmt->errorInfo();
            throw new PDOException("Failed to execute update statement: " . ($errorInfo[2] ?? 'Unknown error'));
        }

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database Update Error: ' . $e->getMessage()]);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Server Error: ' . $e->getMessage()]);
        exit;
    }

    ?>
    


