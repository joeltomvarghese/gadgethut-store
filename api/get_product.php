<?php
// Set the response content type to JSON
header('Content-Type: application/json');

// Include the database connection file
require_once '../config/db.php';

// Check if an 'id' parameter is set in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Product ID is required.']);
    exit;
}

$product_id = $_GET['id'];

try {
    // Prepare the SQL query to fetch one product by its ID
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    
    // Execute the query with the product ID
    $stmt->execute([$product_id]);
    
    // Fetch the product
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Send the product as JSON
        echo json_encode($product);
    } else {
        // Product not found
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Product not found.']);
    }
    
} catch (PDOException $e) {
    // Handle any database errors
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Failed to fetch product: ' . $e->getMessage()]);
}
?>
