<?php
// This file contains the database connection settings.

// --- XAMPP (Local) Settings ---
// These are the default settings for a local XAMPP installation.
$host = '127.0.0.1'; // or 'localhost'
$db_name = 'm_commerce_db';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password is empty

// --- AWS EC2 (Live) Settings ---
// When you deploy to AWS, you will comment out the XAMPP settings
// and uncomment these. You must create this user and password in MySQL on your EC2 server.
/*
$host = '127.0.0.1'; // or 'localhost'
$db_name = 'm_commerce_db';
$username = 'm_commerce_user'; // The user you will create on AWS
$password = 'YOUR_STRONG_AWS_DB_PASSWORD'; // The password you will create
*/

$dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
];

try {
    // Create the PDO database connection instance
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // If connection fails, stop the script and show an error.
    // This is a critical error.
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'error' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit; // Stop the script
}

// The $pdo variable is now available to any PHP script that includes this file.
?>

