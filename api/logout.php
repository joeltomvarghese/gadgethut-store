<?php
// api/logout.php

// Start the session to access session variables - IMPORTANT!
// Needs to be started before session_destroy() can work effectively.
if (session_status() === PHP_SESSION_NONE) {
    // Optionally set cookie parameters for security before starting
    // session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'domain' => '', 'secure' => isset($_SERVER['HTTPS']), 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    // Set cookie expiration to the past to effectively delete it
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

// Send a JSON response indicating success
// This is helpful for JavaScript fetch calls to confirm logout
header('Content-Type: application/json');
echo json_encode(['success' => true]);
exit; // Ensure no further code execution

?>

