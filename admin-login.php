<?php
/**
 * Admin Login
 * 
 * Handles authentication for admin users
 * IMPROVED VERSION: Enhanced session handling and redirect logic
 */

// Start session first thing - with error handling for better reliability
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include auth system for consistent session handling
require_once 'admin-auth.php';

// Check if user is already logged in using the auth system function
// This ensures consistency with how authentication is checked throughout the system
if (is_admin_logged_in()) {
    // Get redirect location - either from session or default to dashboard
    $redirect_to = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'admin-dashboard.php';
    
    // Clear the redirect URL from session to prevent future issues
    unset($_SESSION['redirect_after_login']);
    
    // Redirect to admin dashboard or saved page
    header('Location: ' . $redirect_to);
    exit;
}

// Set page title - same as original but with variable for flexibility
$page_title = 'Admin Login | Backsure Global Support';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        body {
            font-family: 'Open Sans', Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            background-color: #062767;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .login-header h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        .login-body {
            padding: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #062767;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: inherit;
        }
        .form-group input:focus {
            border-color: #B19763;
            outline: none;
            box-shadow: 0 0 0 2px rgba(177, 151, 99, 0.2);
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .login-button {
            background-color: #062767;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 12px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            font-family: inherit;
            font-size: 1rem;
            transition: background-color 0.2s;
        }
        .login-button:hover {
            background-color: #051d4d;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #062767;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .form-icon {
            position: relative;
        }
        .form-icon i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 10px;
            color: #6c757d;
        }
        .form-icon input {
            padding-left: 35px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>BSG Support Admin</h1>
        </div>
        <div class="login-body">
            <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
            <?php endif; ?>
            
            <form method="post" action="admin-login-process.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="form-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" required autofocus>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="form-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>
                
                <button type="submit" class="login-button">Sign In</button>
            </form>
            
            <div class="back-link">
                <a href="index.html">&larr; Back to Website</a>
            </div>
        </div>
    </div>
</body>
</html>
