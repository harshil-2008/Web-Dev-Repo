<?php
/* ====================================================================
   StudentHub - Secure Session Termination (logout.php)
   Practical 10: Secure Login/Logout with Sessions
   Author: Harshil Prajapati (ID: 25DCE093)
   ==================================================================== */

require_once __DIR__ . '/auth.php';

// Record audit log before clearing session
if (isset($_SESSION['user_id'])) {
    recordAuditLog('LOGOUT', 'auth', "User {$_SESSION['username']} logged out", $_SESSION['user_id'], $_SESSION['username']);
}

// 1. Unset all session variables
$_SESSION = [];

// 2. Clear session cookie from the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Clear remember-me cookie if present
if (isset($_COOKIE['remember_me'])) {
    setcookie('remember_me', '', time() - 3600, '/');
}

// 4. Destroy the session on server
session_destroy();

// 5. Redirect back to login with success feedback
header("Location: login.php?msg=logged_out");
exit;
?>
