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

// Include database configuration
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid JSON data"]);
        exit();
    }
    
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(["error" => "Username and password are required"]);
        exit();
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT id, username, email, password FROM users WHERE username = :username OR email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password
            if (password_verify($password, $row['password'])) {
                // Start session and set user data
                session_start();
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email'];
                
                echo json_encode([
                    "success" => true,
                    "message" => "Login successful",
                    "user" => [
                        "id" => $row['id'],
                        "username" => $row['username'],
                        "email" => $row['email']
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(["error" => "Invalid credentials"]);
            }
        } else {
            http_response_code(401);
            echo json_encode(["error" => "User not found"]);
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