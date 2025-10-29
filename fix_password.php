<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get ALL users from database
$stmt = $db->query("SELECT id, username, password_hash FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Updating ALL user passwords...</h3>";

foreach ($users as $user) {
    $new_hash = password_hash('password123', PASSWORD_DEFAULT);
    $update_stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $update_stmt->execute([$new_hash, $user['id']]);
    
    echo "✅ Updated password for: <strong>{$user['username']}</strong><br>";
}

echo "<hr>";
echo "<h3>✅ ALL PASSWORDS UPDATED!</h3>";
echo "<strong>Login with ANY username + password: password123</strong><br><br>";

// Show available users
$stmt = $db->query("SELECT username FROM users");
$all_users = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Available usernames:<br>";
foreach ($all_users as $username) {
    echo "- <strong>$username</strong> / password123<br>";
}
?>