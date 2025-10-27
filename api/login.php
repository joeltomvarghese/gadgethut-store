<?php
// api/login.php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

// *** IMPORTANT: Start session ***
if (session_status() === PHP_SESSION_NONE) {
    // Set session cookie parameters for better security if possible
    // session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'domain' => '', 'secure' => isset($_SERVER['HTTPS']), 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

require_once __DIR__ . '/../config/db.php';

// --- Error Handling Setup ---
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (!headers_sent()) {
            http_response_code(500);
            // Ensure header again just in case
             header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Server error during login.']);
        }
    }
});
// --- End Error Handling ---

try {
    $json_data = file_get_contents('php://input');
    if ($json_data === false) {
        throw new Exception("Could not read input data.");
    }
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
        throw new Exception('Database connection failed.');
    }

    // Find user by username OR email
    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?");
    if (!$stmt) throw new PDOException("Failed to prepare user select statement.");
    if (!$stmt->execute([$usernameOrEmail, $usernameOrEmail])) throw new PDOException("Failed to execute user select statement.");

    $user = $stmt->fetch(PDO_FETCH_ASSOC);

    // Verify user exists and password is correct
    if ($user && password_verify($password, $user['password_hash'])) {
        // Password is correct! Start the session and store user data.

        // Regenerate session ID for security (prevents session fixation)
        session_regenerate_id(true);

        // Store essential user info in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true; // Use a boolean flag

        echo json_encode(['success' => true, 'username' => $user['username']]);

    } else {
        // Invalid credentials (user not found or password incorrect)
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'error' => 'Invalid username/email or password.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
     // Log error: error_log("Login DB Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error during login.']);
} catch (Exception $e) {
    http_response_code(500);
     // Log error: error_log("Login Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

exit; // Ensure script stops here
?>

