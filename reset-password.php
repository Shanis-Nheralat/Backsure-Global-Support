<?php
/**
 * Reset Password Form
 * Allows users to set a new password after requesting a reset
 */

// Include database configuration
require_once 'config.php';
require_once 'functions.php';

// Get reset parameters
$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$token = isset($_GET['token']) ? trim($_GET['token']) : '';

// Check if parameters are missing
if (empty($email) || empty($token)) {
    $error = 'Invalid password reset link. Please request a new password reset.';
    require 'reset-password-form.php';
    exit;
}

// Validate token
try {
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
    
    // Find token
    $stmt = $pdo->prepare('SELECT * FROM password_resets WHERE email = ? AND token = ? AND expires > NOW()');
    $stmt->execute([$email, $token]);
    $reset = $stmt->fetch();
    
    // If token not found or expired
    if (!$reset) {
        $error = 'Invalid or expired password reset link. Please request a new password reset.';
        require 'reset-password-form.php';
        exit;
    }
    
    // Token is valid, show the reset form
    require 'reset-password-form.php';
    
} catch (PDOException $e) {
    // Log error
    error_log('Reset password error: ' . $e->getMessage());
    
    $error = 'A database error occurred. Please try again later or contact support.';
    require 'reset-password-form.php';
}
?>