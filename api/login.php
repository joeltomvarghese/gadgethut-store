<?php
// api/login.php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

// *** IMPORTANT: Start session ***
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

// --- Error Handling Setup ---
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (!headers_sent()) { http_response_code(500); }
        // Ensure header again just in case
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Server error during login.']);
    }
});
// --- End Error Handling ---

try {
    $json_data = file_get_contents('php://input');
    $request_data = json_decode($json_data, true);

    // Basic Validation
    if ($request_data === null || empty($request_data['username']) || empty($request_data['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Username/Email and password are required.']);
        exit;
    }

    $usernameOrEmail = trim($request_data['username']);
    $password = $request_data['password'];

    $pdo = getDbConnection();
    if ($pdo === null) {
        // This will be caught by the catch block below
        throw new Exception('Database connection failed.');
    }

    // Find user by username OR email
    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?");
     if (!$stmt) {
         throw new PDOException("Failed to prepare user select statement.");
     }
    if (!$stmt->execute([$usernameOrEmail, $usernameOrEmail])) {
         throw new PDOException("Failed to execute user select statement.");
     }

    // Fetch the user using the CORRECTED constant
    // ===================================
    // === THIS LINE WAS FIXED (Line 57) ===
    // ===================================
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Use PDO::FETCH_ASSOC

    // Verify user exists and password is correct
    if ($user && password_verify($password, $user['password_hash'])) {
        // Password is correct! Start the session and store user data.

        // Regenerate session ID for security
        session_regenerate_id(true);

        // Store essential user info in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;

        echo json_encode(['success' => true, 'username' => $user['username']]);

    } else {
        // Invalid credentials
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'error' => 'Invalid username/email or password.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
     // Log error: error_log("Login DB Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error during login: ' . $e->getMessage()]); // More specific DB error
} catch (Exception $e) {
    http_response_code(500);
     // Log error: error_log("Login Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error during login: ' . $e->getMessage()]); // More specific general error
}

exit; // Ensure script stops here
?>

