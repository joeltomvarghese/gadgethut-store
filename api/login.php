<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If not POST request, return JSON error
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Username and password are required']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$username, $username]);
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;
        
        // Check if it's an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !empty($_POST['ajax']);
        
        if ($isAjax) {
            // AJAX request - return JSON
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'username' => $user['username']]);
        } else {
            // Direct form submission - REDIRECT to main store
            header('Location: ../index.html');
            exit;
        }
    } else {
        // Check if it's an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !empty($_POST['ajax']);
        
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
        } else {
            // For form submission, redirect back to login with error
            header('Location: ../login.html?error=Invalid username or password');
            exit;
        }
    }
} catch (PDOException $e) {
    // Check if it's an AJAX request
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !empty($_POST['ajax']);
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    } else {
        header('Location: ../login.html?error=Database error');
        exit;
    }
}
?>