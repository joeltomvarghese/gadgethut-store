<?php

// --- XAMPP SETTINGS (Default for Local Development) ---
$host = '127.0.0.1'; // Use 127.0.0.1 instead of 'localhost'
$db_name = 'm_commerce_db'; // Double-check this matches phpMyAdmin exactly
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password is empty (LEAVE EMPTY unless you SET one)

/*
// --- AWS EC2 SETTINGS (Use these when deploying) ---
$host = '127.0.0.1';
$db_name = 'm_commerce_db';
$username = 'm_commerce_user';
$password = 'YourStrongPassword123!';
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
        // --- ADDED DETAILED LOGGING ---
        // This will write the exact connection error to PHP's error log
        error_log("!!! Database Connection Error in getDbConnection(): " . $e->getMessage());
        // --- END ADDED LOGGING ---

        // Return null to indicate connection failure
        return null;
    }
}

?>

