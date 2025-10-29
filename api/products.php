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
    
    $query = "SELECT * FROM products WHERE status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $products = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Construct full image URL for AWS
        $base_url = "http://" . $_SERVER['HTTP_HOST'];
        $row['image_url'] = !empty($row['image']) ? 
            $base_url . "/uploads/products/" . $row['image'] : 
            $base_url . "/uploads/products/default.jpg";
        
        $products[] = $row;
    }
    
    echo json_encode($products);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to load products: " . $e->getMessage()]);
}
?>