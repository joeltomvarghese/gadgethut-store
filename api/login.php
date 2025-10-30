<?php
header("Content-Type: application/json");
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Username and password required"]);
        exit;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Login successful - remove password from response
        unset($user['password']);
        echo json_encode([
            "success" => true, 
            "message" => "Login successful", 
            "user" => $user
        ]);
    } else {
        echo json_encode([
            "success" => false, 
            "message" => "Invalid username or password"
        ]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Only POST method allowed"]);
}
?>