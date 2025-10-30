<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Log the request
error_log("Register endpoint hit");

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $json_input = file_get_contents("php://input");
    error_log("Raw input: " . $json_input);
    
    $input = json_decode($json_input, true);
    
    // Check if JSON decoding worked
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        echo json_encode(["success" => false, "message" => "Invalid JSON data"]);
        exit;
    }
    
    $username = $input['username'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
    error_log("Processing registration for: " . $username);
    
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Invalid email format"]);
        exit;
    }
    
    try {
        require_once 'database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if user already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            echo json_encode(["success" => false, "message" => "Username or email already exists"]);
            exit;
        }
        
        // Create new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);
        
        error_log("User registered successfully: " . $username);
        echo json_encode([
            "success" => true, 
            "message" => "User registered successfully"
        ]);
        
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        echo json_encode([
            "success" => false, 
            "message" => "Registration failed: " . $e->getMessage()
        ]);
    }
} else {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(["success" => false, "message" => "Only POST method allowed"]);
}
?>