<?php
// Direct login test - bypasses JavaScript
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'jake';
$_SESSION['logged_in'] = true;

echo "✅ Logged in as jake! <a href='index.html'>Go to Store</a>";
?>