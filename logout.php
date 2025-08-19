<?php
session_start();

// Clear all session variables
$_SESSION = [];

// If you want to destroy the session cookie as well
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect user to login page au homepage
header("Location: login.php");
exit();
