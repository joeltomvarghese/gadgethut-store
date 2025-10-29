<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Create a NEW test user (won't conflict with existing demo_user)
$username = 'testuser';
$password_hash = password_hash('password123', PASSWORD_DEFAULT);

$stmt = $db->prepare("INSERT IGNORE INTO users (username, email, password_hash) VALUES (?, ?, ?)");
$stmt->execute([$username, 'test@email.com', $password_hash]);

echo "✅ Test user created!<br>";
echo "Login with: <strong>testuser / password123</strong><br>";
echo "Or try: <strong>demo_user / password123</strong> (if it works)";
?>