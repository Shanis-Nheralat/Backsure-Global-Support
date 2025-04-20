<?php
/**
 * Logout Process
 * Handles user logout and session destruction
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration if needed for token deletion
require_once 'config.php';

// Get user info before destroying session
$userId = $_SESSION['user_id'] ?? null;
$isClient = isset($_SESSION['is_client']) && $_SESSION['is_client'] === true;

// Determine where to redirect after logout
$redirectUrl = $isClient ? 'client-login.html' : 'admin-login.html';

// Check for CSRF protection
if (isset($_GET['token'])) {
    if (!isset($_SESSION['token']) || $_GET['token'] !== $_SESSION['token']) {
        // Invalid CSRF token
        header("Location: $redirectUrl?error=invalid_token");
        exit;
    }
} 

// If this is an AJAX request, we'll return JSON
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Clean up remember_me token if exists
if (isset($_COOKIE['remember_me']) && $userId) {
    try {
        // Parse the remember_me cookie
        $parts = explode(':', $_COOKIE['remember_me']);
        if (count($parts) === 2) {
            $selector = $parts[0];
            
            // Connect to database
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            // Delete the token from database
            $stmt = $pdo->prepare('DELETE FROM remember_tokens WHERE user_id = ? AND selector = ?');
            $stmt->execute([$userId, $selector]);
        }
    } catch (PDOException $e) {
        error_log('Logout error: ' . $e->getMessage());
    }
    
    // Delete the cookie
    setcookie('remember_me', '', time() - 3600, '/', '', true, true);
}

// Log the logout event
if ($userId) {
    try {
        // Connect to database if not already connected
        if (!isset($pdo)) {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        }
        
        // Log the logout
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        
        $stmt = $pdo->prepare('INSERT INTO login_logs (user_id, ip_address, user_agent, action, success) VALUES (?, ?, ?, "logout", 1)');
        $stmt->execute([$userId, $ipAddress, $userAgent]);
        
    } catch (PDOException $e) {
        error_log('Logout log error: ' . $e->getMessage());
    }
}

// Destroy session
$_SESSION = [];

// Delete the session cookie
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

// Destroy the session
session_destroy();

// Return response based on request type
if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'You have been successfully logged out.',
        'redirect' => $redirectUrl
    ]);
} else {
    // Redirect to login page
    header("Location: $redirectUrl?message=logged_out");
}
exit;