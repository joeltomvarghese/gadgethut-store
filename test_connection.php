<?php
header('Content-Type: application/json');
require_once 'database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Test if we can query the users table
    $stmt = $db->query("SELECT COUNT(*) as user_count FROM users");
    $user_count = $stmt->fetch()['user_count'];
    
    // Check if we have any products
    $stmt = $db->query("SELECT COUNT(*) as product_count FROM products");
    $product_count = $stmt->fetch()['product_count'];
    
    // List all tables
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode([
        "status" => "success",
        "message" => "✅ Database connected successfully!",
        "user_count" => $user_count,
        "product_count" => $product_count,
        "tables" => $tables,
        "test_credentials" => "Try: admin / password"
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "❌ Connection failed: " . $e->getMessage()
    ]);
}
?>