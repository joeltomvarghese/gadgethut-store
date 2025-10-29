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

if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Product ID is required"]);
    exit();
}

$product_id = $_GET['id'];

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM products WHERE id = :id AND status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    
    if ($stmt->rowCount() == 1) {
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Construct full image URL for AWS
        $base_url = "http://" . $_SERVER['HTTP_HOST'];
        $product['image_url'] = !empty($product['image']) ? 
            $base_url . "/uploads/products/" . $product['image'] : 
            $base_url . "/uploads/products/default.jpg";
        
        echo json_encode($product);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Product not found"]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to load product: " . $e->getMessage()]);
}
?>