<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include authentication middleware
require_once __DIR__ . '/../api/middleware/auth_middleware.php';

// Log the logout activity if user is authenticated
if(isset($_SESSION['user_id'])) {
    Auth::logActivity("logout");
}

// Clear all session variables
$_SESSION = array();

// Get session cookie parameters 
$params = session_get_cookie_params();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Clear any other auth cookies that might be set
setcookie('remember_me', '', time() - 3600, '/');
setcookie('auth_token', '', time() - 3600, '/');

// Add a small delay to ensure cookie deletion processes
usleep(10000);

// Redirect to login page with cache control headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Location: /auth/login.php?logged_out=1");
exit;
?>