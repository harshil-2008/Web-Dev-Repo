<?php
/* ====================================================================
   StudentHub - Authentication & Session Middleware (auth.php)
   Practical 10: Secure Login/Logout with Sessions, RBAC, Timeout
   Author: Harshil Prajapati (ID: 25DCE093)
   ==================================================================== */

// Configure secure session cookie settings before session_start()
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
    session_start();
}

// Inactivity Timeout threshold (15 minutes = 900 seconds)
define('SESSION_TIMEOUT_SECONDS', 900);

function checkSessionTimeout() {
    if (isset($_SESSION['last_activity'])) {
        $inactiveTime = time() - $_SESSION['last_activity'];
        if ($inactiveTime > SESSION_TIMEOUT_SECONDS) {
            // Inactivity timeout exceeded: flush and destroy session
            session_unset();
            session_destroy();
            header("Location: login.php?msg=timeout");
            exit;
        }
    }
    // Update last activity timestamp on every valid request
    $_SESSION['last_activity'] = time();
}

// Enforce login requirement on protected pages
function requireLogin() {
    checkSessionTimeout();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?msg=unauthorized");
        exit;
    }
}

// Enforce Role-Based Access Control (RBAC)
function requireRole($requiredRole) {
    requireLogin();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $requiredRole) {
        header("Location: login.php?msg=forbidden");
        exit;
    }
}

// Helper: Check if user is logged in
function isLoggedIn() {
    checkSessionTimeout();
    return isset($_SESSION['user_id']);
}

// Helper: Check current role
function getCurrentRole() {
    return $_SESSION['role'] ?? null;
}

// Helper: Log administrative actions to audit_logs
function recordAuditLog($action, $entity, $details, $userId = null, $username = null) {
    require_once __DIR__ . '/db_connect.php';
    global $conn;

    $uid = $userId ?? ($_SESSION['user_id'] ?? null);
    $uname = $username ?? ($_SESSION['username'] ?? 'Anonymous');
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    if ($conn) {
        $stmt = mysqli_prepare($conn, "INSERT INTO audit_logs (user_id, username, action_type, target_entity, details, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "isssss", $uid, $uname, $action, $entity, $details, $ip);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}
?>
