<?php
// Set the response content type to JSON
header('Content-Type: application/json');

// Include the database connection file
require_once '../config/db.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only POST method is allowed.']);
    exit;
}

try {
    // 1. Get the raw POST data
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    // 2. Validate data
    if (empty($data['product_id']) || empty($data['image_url'])) {
        throw new Exception("Product ID and Image URL are required.");
    }

    $product_id = $data['product_id'];
    $image_url = $data['image_url'];

    // 3. Prepare and execute the UPDATE query
    $sql = "UPDATE products SET image_url = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$image_url, $product_id]);

    // 4. Check if any row was actually updated
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => "Product $product_id image updated successfully."]);
    } else {
        throw new Exception("Product ID not found or image is already set to this URL.");
    }

} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
