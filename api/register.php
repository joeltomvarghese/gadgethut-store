<?php
// api/register.php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors directly
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';

// --- Error Handling Setup ---
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (!headers_sent()) { http_response_code(500); }
        echo json_encode(['success' => false, 'error' => 'Server error during registration.']);
    }
});
// --- End Error Handling ---

try {
    $json_data = file_get_contents('php://input');
    $request_data = json_decode($json_data, true);

    // Basic Validation
    if ($request_data === null || empty($request_data['username']) || empty($request_data['email']) || empty($request_data['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Username, email, and password are required.']);
        exit;
    }

    $username = trim($request_data['username']);
    $email = trim($request_data['email']);
    $password = $request_data['password']; // Get raw password

    // More validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid email format.']);
        exit;
    }
    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters long.']);
        exit;
    }

    $pdo = getDbConnection();
    if ($pdo === null) {
        throw new Exception('Database connection failed.');
    }

    // Check if username or email already exists
    $stmt_check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    if (!$stmt_check) throw new PDOException("Failed to prepare check statement.");
    if (!$stmt_check->execute([$username, $email])) throw new PDOException("Failed to execute check statement.");

    if ($stmt_check->fetch()) {
        http_response_code(409); // Conflict
        echo json_encode(['success' => false, 'error' => 'Username or email already exists.']);
        exit;
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    if ($password_hash === false) {
         throw new Exception('Failed to hash password.');
    }


    // Insert the new user
    $stmt_insert = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    if (!$stmt_insert) throw new PDOException("Failed to prepare insert statement.");
    if (!$stmt_insert->execute([$username, $email, $password_hash])) {
        $errorInfo = $stmt_insert->errorInfo();
        throw new PDOException("Failed to register user: " . ($errorInfo[2] ?? 'Unknown DB error'));
    }

    // Success
    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    http_response_code(500);
    // Log error: error_log("Registration DB Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error during registration.']);
} catch (Exception $e) {
    http_response_code(500);
     // Log error: error_log("Registration Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]); // More specific general error
}

exit; // Ensure script stops here
?>

