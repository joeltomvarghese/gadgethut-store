<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Product ID required']);
    exit;
}

$product_id = intval($_GET['id']);

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        // Map the new column names to expected frontend names
        $mappedProduct = [
            'id' => $product['id'],
            'name' => $product['name'],
            'description' => $product['description'],
            'price' => $product['price'],
            'image_url' => $product['image_url'],
            'condition' => $product['product_condition'], // Map to frontend expected name
            'usage_duration' => $product['usage_duration'],
            'condition_notes' => $product['condition_notes'],
            'rating' => $product['rating'],
            'stock_quantity' => $product['stock_quantity']
        ];
        echo json_encode($mappedProduct);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
} catch (PDOException $e) {
    error_log("Product fetch error: " . $e->getMessage());
    echo json_encode(['error' => 'Failed to fetch product']);
}
?>