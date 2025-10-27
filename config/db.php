<?php

// --- XAMPP SETTINGS (Default for Local Development) ---
$host = '127.0.0.1'; // Use 127.0.0.1 instead of 'localhost'
$db_name = 'm_commerce_db';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password is empty

/*
// --- AWS EC2 SETTINGS (Use these when deploying) ---
// Note: You'll need to create this user and database in MySQL on your EC2 instance.
$host = '127.0.0.1';
$db_name = 'm_commerce_db';
$username = 'm_commerce_user'; // Replace with your EC2 DB username
$password = 'YourStrongPassword123!'; // Replace with your EC2 DB password
*/

function getDbConnection() {
    global $host, $db_name, $username, $password;
    $dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
    ];

    try {
        $pdo = new PDO($dsn, $username, $password, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Log error instead of echoing directly
        error_log("Database Connection Error: " . $e->getMessage());
        // Return null to indicate connection failure
        return null;
    }
}

?>

    