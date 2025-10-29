<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    echo json_encode([
        "loggedIn" => true,
        "username" => $_SESSION['username'],
        "email" => $_SESSION['email'] ?? '',
        "user_id" => $_SESSION['user_id']
    ]);
} else {
    echo json_encode([
        "loggedIn" => false
    ]);
}
?>