<?php
// Set headers first
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Include database configuration
include_once '../config/db.php';

try {
    // Create database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if connection is successful
    if ($db == null) {
        throw new Exception("Failed to connect to database");
    }
    
    // SQL query to get products
    $sql = "SELECT id, name, description, price, product_condition, usage_duration, condition_notes, rating FROM products WHERE status = 'active'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    // Fetch all products
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add image URLs and format the data
    foreach ($products as &$product) {
        $product['condition'] = $product['product_condition'];
        $product['image_url'] = "https://placehold.co/600x400/333/fff?text=" . urlencode($product['name']);
    }
    
    // Return JSON response
    echo json_encode($products);
    
} catch (PDOException $e) {
    // Database error
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
} catch (Exception $e) {
    // General error
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>