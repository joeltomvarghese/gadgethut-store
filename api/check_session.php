<?php
session_start();
header('Content-Type: application/json');

// Set CORS headers if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo json_encode([
        'loggedIn' => true,
        'username' => $_SESSION['username'] ?? 'User'
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>