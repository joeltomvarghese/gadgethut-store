<?php
echo "<h1>PHP is Working!</h1>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Testing API connection...</p>";

// Test if we can include API files
try {
    include 'api/products.php';
} catch (Exception $e) {
    echo "<p style='color: red;'>API Error: " . $e->getMessage() . "</p>";
}
?>