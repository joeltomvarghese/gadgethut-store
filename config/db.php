<?php

// --- XAMPP SETTINGS (Default for Local Development) ---
$host = '127.0.0.1'; // Use 127.0.0.1 instead of 'localhost' for potential consistency
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

/**
 * Creates and returns a PDO database connection.
 * Returns null on connection failure.
 * @return PDO|null Database connection object or null on failure.
 */
function getDbConnection() {
    // Use global variables defined above
    global $host, $db_name, $username, $password;

    // Data Source Name (DSN)
    $dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";

    // PDO connection options
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on SQL errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays by default
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
    ];

    try {
        // Attempt to create a new PDO connection
        $pdo = new PDO($dsn, $username, $password, $options);
        return $pdo; // Return the connection object on success
    } catch (PDOException $e) {
        // Log the detailed error to the server's error log (more secure than echoing)
        error_log("Database Connection Error: " . $e->getMessage());

        // Return null to indicate that the connection failed
        // The calling script should check if the return value is null
        return null;
    }
}

?>

