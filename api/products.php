<?php
// This API endpoint fetches all products from the database.

// Set the response content type to JSON
header('Content-Type: application/json');

// Include the database connection file
require_once '../config/db.php';

try {
    // 1. Prepare the SQL query
    // We are selecting all columns from the products table.
    $stmt = $pdo->prepare("SELECT * FROM products ORDER BY id ASC");
    
    // 2. Execute the query
    $stmt->execute();
    
    // 3. Fetch all products
    // PDO::FETCH_ASSOC returns an array indexed by column name.
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 4. Send the products back as JSON
    // json_encode converts the PHP array into a JSON string.
    echo json_encode($products);

} catch (PDOException $e) {
    // 5. Handle any database errors
    // If the query fails, send a 500 server error status
    // and a JSON error message.
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'error' => 'Failed to fetch products: ' . $e->getMessage()
    ]);
}

?>

