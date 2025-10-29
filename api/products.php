<?php
header('Content-Type: application/json');

// Simple test data - remove this after testing
$testProducts = [
    [
        "id" => 1,
        "name" => "iPhone 14 Pro",
        "description" => "Latest iPhone with advanced camera system",
        "price" => 999.99,
        "image_url" => "https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=400",
        "condition" => "Pristine", 
        "usage_duration" => "6 months",
        "condition_notes" => "Like new condition",
        "rating" => 4.8,
        "stock_quantity" => 15
    ],
    [
        "id" => 2, 
        "name" => "Samsung Galaxy S23",
        "description" => "Premium Android smartphone",
        "price" => 849.99,
        "image_url" => "https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400",
        "condition" => "Very Good",
        "usage_duration" => "1 year", 
        "condition_notes" => "Minor scratches on back",
        "rating" => 4.5,
        "stock_quantity" => 12
    ]
];

echo json_encode($testProducts);
exit;
?>