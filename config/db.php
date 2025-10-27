<?php

function getDbConnection() {
    // --- Define Connection Details INSIDE the function ---
    // --- XAMPP SETTINGS ---
    $host = '127.0.0.1'; // Use 127.0.0.1 instead of 'localhost'
    $db_name = 'm_commerce_db'; // Make sure this matches phpMyAdmin
    $username = 'root'; // <<< MUST BE 'root' for default XAMPP
    $password = '';     // <<< MUST BE '' (empty) for default XAMPP (unless you set one!)

    /*
    // --- AWS EC2 SETTINGS (Use these when deploying) ---
    // $host = '127.0.0.1';
    // $db_name = 'm_commerce_db';
    // $username = 'm_commerce_user';
    // $password = 'YourStrongPassword123!';
    */
    // --- End Connection Details ---

    $dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
    ];

    try {
        // Use the $username and $password defined *inside* this function
        $pdo = new PDO($dsn, $username, $password, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Log the exact error
        error_log("!!! Database Connection Error in getDbConnection(): " . $e->getMessage());
        // Return null to indicate connection failure
        return null;
    }
}

?>

