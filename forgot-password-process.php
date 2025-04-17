<?php
/**
 * forgot-password-process.php
 * Processes password reset requests
 */

// Start session
session_start();

// Include database configuration
require_once 'config.php';

// Set content type to JSON for API responses
header('Content-Type: application/json');

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get email address
$email = isset($_POST['email']) ? trim(strtolower($_POST['email'])) : '';

// Validate email
if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email address is required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

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
    
    // Check if the email exists
    $stmt = $pdo->prepare('SELECT id, name, username FROM users WHERE email = ? AND active = 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Even if the email doesn't exist, don't reveal that to prevent email enumeration
    if (!$user) {
        // Log the attempt for security purposes
        logResetAttempt($pdo, $email, false, 'Email not found or user inactive');
        
        // Still return success to avoid email enumeration
        echo json_encode(['success' => true, 'message' => 'If your email address exists in our database, you will receive a password reset link shortly.']);
        exit;
    }
    
    // Generate password reset token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour
    
    // Store token in database
    $stmt = $pdo->prepare('INSERT INTO password_resets (user_id, token, expires_at, created_at) VALUES (?, ?, ?, NOW())');
    $stmt->execute([
        $user['id'],
        $token,
        $expires
    ]);
    
    // Create reset link
    $resetLink = 'https://' . $_SERVER['HTTP_HOST'] . '/admin-login.html?reset=' . urlencode($email) . '&token=' . $token;
    
    // Send password reset email
    $emailSubject = 'Password Reset - Backsure Global Support';
    $emailBody = "
    <html>
    <body>
        <h2>Password Reset Request</h2>
        <p>Hello " . htmlspecialchars($user['name']) . ",</p>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <p><a href='$resetLink' style='background-color:#062767;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;display:inline-block;'>Reset Password</a></p>
        <p>Or copy and paste this link into your browser:</p>
        <p>$resetLink</p>
        <p>This password reset link will expire in 60 minutes.</p>
        <p>If you did not request a password reset, no further action is required.</p>
        <p>Thanks,<br>
        Backsure Global Support Team</p>
    </body>
    </html>
    ";
    
    // In a real application, you would send this email
    // For demonstration, we'll just log it
    error_log('Password reset email would be sent to: ' . $email);
    error_log('Reset link: ' . $resetLink);
    
    // Log the reset attempt
    logResetAttempt($pdo, $email, true, 'Reset email sent');
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'If your email address exists in our database, you will receive a password reset link shortly.',
        'debug' => [
            'reset_link' => $resetLink // Only for demonstration
        ]
    ]);
    
} catch (PDOException $e) {
    // Log error
    error_log('Password reset error: ' . $e->getMessage());
    
    echo json_encode(['success' => false, 'message' => 'A database error occurred. Please try again later.']);
}

/**
 * Log password reset attempt
 */
function logResetAttempt($pdo, $email, $success, $notes = '') {
    try {
        $stmt = $pdo->prepare('INSERT INTO password_reset_logs (email, ip_address, user_agent, success, notes, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $email,
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'],
            $success ? 1 : 0,
            $notes
        ]);
    } catch (Exception $e) {
        error_log('Error logging password reset attempt: ' . $e->getMessage());
    }
}