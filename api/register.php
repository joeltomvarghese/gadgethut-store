<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Debug: Log the received data
error_log("Registration attempt: username=$username, email=$email");

if (empty($username) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if username or email already exists
    $checkQuery = "SELECT id FROM users WHERE username = ? OR email = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([$username, $email]);
    
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Username or email already exists']);
        exit;
    }
    
    // Create new user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insertQuery = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
    $insertStmt = $db->prepare($insertQuery);
    
    if ($insertStmt->execute([$username, $email, $hashedPassword])) {
        echo json_encode(['success' => true, 'message' => 'Registration successful!']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to create account']);
    }
} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>