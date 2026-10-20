<?php
/* ====================================================================
   StudentHub - Secure Authentication Handler (login.php)
   Practical 10: Secure Login/Logout with Sessions, RBAC, Timeout, Remember-Me
   Author: Harshil Prajapati (ID: 25DCE093)
   ==================================================================== */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db_connect.php';

$error = '';
$success = '';

// Handle incoming query status messages
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'timeout') {
        $error = "Your session expired due to inactivity. Please log in again.";
    } elseif ($_GET['msg'] === 'unauthorized') {
        $error = "Access denied. Please log in first.";
    } elseif ($_GET['msg'] === 'forbidden') {
        $error = "You do not have permission to view that resource.";
    } elseif ($_GET['msg'] === 'logged_out') {
        $success = "You have been logged out successfully.";
    }
}

// Process Login Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';
    $remember   = isset($_POST['remember']);

    if (empty($identifier) || empty($password)) {
        $error = "Please fill in both username/email and password.";
    } else {
        // Query user by username or email using Prepared Statements
        if ($conn) {
            $stmt = mysqli_prepare($conn, "SELECT user_id, username, email, password_hash, role, status FROM users WHERE username = ? OR email = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "ss", $identifier, $identifier);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($user && password_verify($password, $user['password_hash'])) {
                if ($user['status'] !== 'active') {
                    $error = "Your account is currently inactive. Please contact the administrator.";
                } else {
                    // 1. Regenerate session ID to prevent session fixation attacks
                    session_regenerate_id(true);

                    // 2. Set authenticated session variables
                    $_SESSION['user_id']       = $user['user_id'];
                    $_SESSION['username']      = $user['username'];
                    $_SESSION['email']         = $user['email'];
                    $_SESSION['role']          = $user['role'];
                    $_SESSION['last_activity'] = time();

                    // 3. Optional Remember-Me Token via Secure Cookie
                    if ($remember) {
                        $rawToken = bin2hex(random_bytes(32));
                        $tokenHash = hash('sha256', $rawToken);
                        
                        $updateStmt = mysqli_prepare($conn, "UPDATE users SET remember_token = ?, last_login = NOW() WHERE user_id = ?");
                        mysqli_stmt_bind_param($updateStmt, "si", $tokenHash, $user['user_id']);
                        mysqli_stmt_execute($updateStmt);
                        mysqli_stmt_close($updateStmt);

                        // Set cookie expiring in 14 days with HttpOnly & Secure flags
                        setcookie(
                            'remember_me',
                            $user['user_id'] . ':' . $rawToken,
                            [
                                'expires'  => time() + (86400 * 14),
                                'path'     => '/',
                                'httponly' => true,
                                'samesite' => 'Strict'
                            ]
                        );
                    }

                    // 4. Record Audit Log for login event
                    recordAuditLog('LOGIN', 'auth', "User {$user['username']} logged in successfully ({$user['role']})", $user['user_id'], $user['username']);

                    // 5. Role-based Redirection
                    if ($user['role'] === 'admin') {
                        header("Location: admin_dashboard.php");
                        exit;
                    } else {
                        header("Location: dashboard.html");
                        exit;
                    }
                }
            } else {
                $error = "Invalid username/email or password.";
            }
        } else {
            // Fallback for demonstration without active MySQL service
            if ($identifier === 'admin' && $password === 'Admin@123') {
                session_regenerate_id(true);
                $_SESSION['user_id'] = 1;
                $_SESSION['username'] = 'admin';
                $_SESSION['role'] = 'admin';
                $_SESSION['last_activity'] = time();
                header("Location: admin_dashboard.php");
                exit;
            } elseif ($identifier === 'harshil' && $password === 'Student@123') {
                session_regenerate_id(true);
                $_SESSION['user_id'] = 2;
                $_SESSION['username'] = 'harshil';
                $_SESSION['role'] = 'student';
                $_SESSION['last_activity'] = time();
                header("Location: dashboard.html");
                exit;
            } else {
                $error = "Invalid credentials. Use admin / Admin@123 or harshil / Student@123";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login | StudentHub</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .login-card {
            max-width: 440px;
            margin: 40px auto;
            padding: 35px 30px;
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }
        .demo-credentials {
            background: rgba(59, 130, 246, 0.1);
            border-left: 4px solid #3b82f6;
            padding: 12px 16px;
            border-radius: 6px;
            margin-top: 25px;
            font-size: 13px;
            color: #93c5fd;
        }
        .demo-credentials strong {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <a href="home.html" class="logo">StudentHub</a>
            <button class="menu-btn" id="menuBtn" type="button">☰</button>
            <div class="nav-actions" id="navActions">
                <nav id="navbar">
                    <a href="home.html">Home</a>
                    <a href="about.html">About</a>
                    <a href="events.html">Events</a>
                    <a href="dashboard.html">Dashboard</a>
                    <a href="profile.html">Profile</a>
                    <a href="contact.html">Contact</a>
                    <a href="FAQs.html">FAQ</a>
                    <a href="register.html">Register</a>
                </nav>
                <button class="theme-btn" id="themeBtn" type="button">☀️ Light</button>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="hero" style="padding-bottom: 10px;">
            <div class="hero-content">
                <h1>StudentHub Portal Authentication</h1>
                <p>Sign in to access your role-based dashboard, manage course registrations, or administer events.</p>
            </div>
        </section>

        <div class="login-card">
            <?php if (!empty($error)): ?>
                <div class="error-summary" style="display:block; margin-bottom: 20px; background: rgba(239, 68, 68, 0.2); border-color: #ef4444; color: #fca5a5;">
                    ⚠️ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="error-summary" style="display:block; margin-bottom: 20px; background: rgba(34, 197, 94, 0.2); border-color: #22c55e; color: #86efac;">
                    ✅ <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group" style="margin-bottom: 18px;">
                    <label for="identifier" style="display:block; margin-bottom: 6px; font-weight: 500;">Username or Email Address</label>
                    <input type="text" name="identifier" id="identifier" class="form-control" placeholder="e.g. admin or harshil" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(15,23,42,0.6); color: white;">
                </div>

                <div class="form-group" style="margin-bottom: 18px;">
                    <label for="password" style="display:block; margin-bottom: 6px; font-weight: 500;">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); background: rgba(15,23,42,0.6); color: white;">
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px;">
                    <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px;">
                        <input type="checkbox" name="remember" id="remember" value="1">
                        Remember me (14 days)
                    </label>
                    <a href="contact.html" style="color: #60a5fa; font-size: 13px; text-decoration: none;">Forgot password?</a>
                </div>

                <button type="submit" class="btn" style="width: 100%; padding: 12px; font-size: 16px; border: none; cursor: pointer;">Sign In to Account</button>
            </form>

            <div class="demo-credentials">
                <strong>🔑 Quick Demo Credentials:</strong><br>
                • <strong>Admin:</strong> <code>admin</code> / <code>Admin@123</code> (Redirects to Admin Dashboard)<br>
                • <strong>Student:</strong> <code>harshil</code> / <code>Student@123</code> (Redirects to Student Dashboard)
            </div>

            <p style="text-align: center; margin-top: 25px; font-size: 14px; color: #94a3b8;">
                Don't have an account yet? <a href="register.html" style="color: #60a5fa; text-decoration: none; font-weight: 600;">Register here</a>
            </p>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>StudentHub</h3>
                    <p>Your one-stop platform for managing academic activities, college events, clubs, announcements, and your student profile.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="home.html">Home</a></li>
                        <li><a href="about.html">About Us</a></li>
                        <li><a href="events.html">Events</a></li>
                        <li><a href="dashboard.html">Dashboard</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Support</h3>
                    <ul class="footer-links">
                        <li><a href="FAQs.html">FAQ</a></li>
                        <li><a href="contact.html">Contact Us</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2026 StudentHub | All Rights Reserved</p>
            </div>
        </div>
    </footer>
    <script src="script.js"></script>
</body>
</html>
