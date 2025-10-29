
<?php
echo "PHP is working!<br>";
echo "Current PHP version: " . phpversion();

// Test database connection
include_once 'config/db.php';
$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "<br>Database connection: SUCCESS";
} else {
    echo "<br>Database connection: FAILED";
}
?>