<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid JSON data"]);
        exit();
    }
    
    $username = $input['username'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $confirm_password = $input['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(["error" => "All fields are required"]);
        exit();
    }
    
    if ($password !== $confirm_password) {
        http_response_code(400);
        echo json_encode(["error" => "Passwords do not match"]);
        exit();
    }
    
    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(["error" => "Password must be at least 6 characters"]);
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid email format"]);
        exit();
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if user already exists
        $check_query = "SELECT id FROM users WHERE username = :username OR email = :email";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':username', $username);
        $check_stmt->bindParam(':email', $email);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            http_response_code(400);
            echo json_encode(["error" => "Username or email already exists"]);
            exit();
        }
        
        // Insert new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_query = "INSERT INTO users (username, email, password, created_at) VALUES (:username, :email, :password, NOW())";
        $insert_stmt = $db->prepare($insert_query);
        $insert_stmt->bindParam(':username', $username);
        $insert_stmt->bindParam(':email', $email);
        $insert_stmt->bindParam(':password', $hashed_password);
        
        if ($insert_stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Registration successful"
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Registration failed"]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Server error: " . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
?>