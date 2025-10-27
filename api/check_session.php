<?php
// api/check_session.php
error_reporting(0); // Suppress potential notices if session isn't started yet elsewhere
header('Content-Type: application/json');

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Optionally set cookie parameters for security before starting
    // session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'domain' => '', 'secure' => isset($_SERVER['HTTPS']), 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// Check if the specific session variables we set during login exist and are true
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    // User is logged in
    echo json_encode([
        'loggedIn' => true,
        'userId' => $_SESSION['user_id'], // Send user ID
        'username' => $_SESSION['username'] // Send username
    ]);
} else {
    // User is not logged in or session data is incomplete
    echo json_encode(['loggedIn' => false]);
}
exit; // Ensure script stops here
?>

