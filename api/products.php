<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/db.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Database connection failed");
    }
    
    $query = "SELECT * FROM products WHERE status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $products = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Use the correct column name 'product_condition' instead of 'condition'
        $row['condition'] = $row['product_condition'];
        
        // Check if image exists locally, otherwise use placeholder
        $base_url = "http://localhost/gadgethut-store";
        if (!empty($row['image']) && file_exists('../uploads/products/' . $row['image'])) {
            $row['image_url'] = $base_url . "/uploads/products/" . $row['image'];
        } else {
            $row['image_url'] = "https://placehold.co/600x400/333/fff?text=" . urlencode($row['name']);
        }
        
        $products[] = $row;
    }
    
    echo json_encode($products);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to load products: " . $e->getMessage()]);
}
?>