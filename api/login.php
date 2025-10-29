<?php
session_start();
header('Content-Type: application/json');

// Debug: Log received data
error_log("Login attempt - POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

error_log("Login attempt - Username: $username, Password: " . (empty($password) ? 'empty' : 'provided'));

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Username and password are required']);
    exit;
}

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$username, $username]); // Try both username and email
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;
        
        error_log("Login successful for user: " . $user['username']);
        echo json_encode(['success' => true, 'username' => $user['username']]);
    } else {
        error_log("Login failed - Invalid credentials for: $username");
        echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
    }
} catch (PDOException $e) {
    error_log("Login database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>