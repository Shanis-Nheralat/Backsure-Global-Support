<?php
/**
 * Admin Login Process
 * Handles admin authentication with enhanced security
 */

// Start session
session_start();

// Include database configuration
require_once 'config.php';
require_once 'functions.php';

// Set content type to JSON for API responses
header('Content-Type: application/json');

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get login data
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$remember = isset($_POST['remember']) ? (bool)$_POST['remember'] : false;

// Validate inputs
$errors = [];

if (empty($username)) {
    $errors[] = 'Username is required.';
}

if (empty($password)) {
    $errors[] = 'Password is required.';
}

// If there are validation errors, return them
if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
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
    
    // Check if username is an email
    $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);
    
    // Find admin by username or email
    if ($isEmail) {
        $stmt = $pdo->prepare('SELECT id, name, email, username, password, role, status, login_attempts, last_attempt_time FROM admin_users WHERE email = ?');
    } else {
        $stmt = $pdo->prepare('SELECT id, name, email, username, password, role, status, login_attempts, last_attempt_time FROM admin_users WHERE username = ?');
    }
    
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    // If admin not found or is blocked
    if (!$admin) {
        // For security, use the same message for non-existent accounts to prevent user enumeration
        echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
        
        // Log failed login attempt for non-existent account
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        
        $stmt = $pdo->prepare('INSERT INTO login_logs (email, ip_address, user_agent, action, success) VALUES (?, ?, ?, "login", 0)');
        $stmt->execute([$isEmail ? $username : 'unknown', $ipAddress, $userAgent]);
        
        exit;
    }
    
    // Check if account is blocked
    if ($admin['status'] === 'blocked') {
        echo json_encode(['success' => false, 'message' => 'Your account has been blocked. Please contact support.']);
        
        // Log blocked account attempt
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        
        $stmt = $pdo->prepare('INSERT INTO login_logs (user_id, email, ip_address, user_agent, action, success) VALUES (?, ?, ?, ?, "login_blocked", 0)');
        $stmt->execute([$admin['id'], $admin['email'], $ipAddress, $userAgent]);
        
        exit;
    }
    
    // Check for too many login attempts
    $maxAttempts = MAX_LOGIN_ATTEMPTS; // From config
    $lockoutTime = LOCKOUT_MINUTES * 60; // Convert to seconds
    
    $currentTime = time();
    $lastAttemptTime = strtotime($admin['last_attempt_time'] ?? '0');
    $timeSinceLastAttempt = $currentTime - $lastAttemptTime;
    
    // If account was locked but the lockout time has passed, reset the attempts
    if ($timeSinceLastAttempt > $lockoutTime && $admin['login_attempts'] >= $maxAttempts) {
        $stmt = $pdo->prepare('UPDATE admin_users SET login_attempts = 0 WHERE id = ?');
        $stmt->execute([$admin['id']]);
        $admin['login_attempts'] = 0;
    }
    
    // Check if account is temporarily locked
    if ($admin['login_attempts'] >= $maxAttempts && $timeSinceLastAttempt <= $lockoutTime) {
        $remainingTime = ceil(($lockoutTime - $timeSinceLastAttempt) / 60);
        echo json_encode([
            'success' => false, 
            'message' => "Too many failed login attempts. Your account is temporarily locked. Please try again in {$remainingTime} minutes."
        ]);
        
        // Log lockout attempt
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        
        $stmt = $pdo->prepare('INSERT INTO login_logs (user_id, email, ip_address, user_agent, action, success) VALUES (?, ?, ?, ?, "login_locked", 0)');
        $stmt->execute([$admin['id'], $admin['email'], $ipAddress, $userAgent]);
        
        exit;
    }
    
    // Verify password
    if (!password_verify($password, $admin['password'])) {
        // Increment login attempts and update last attempt time
        $stmt = $pdo->prepare('UPDATE admin_users SET login_attempts = login_attempts + 1, last_attempt_time = NOW() WHERE id = ?');
        $stmt->execute([$admin['id']]);
        
        $attemptsLeft = $maxAttempts - ($admin['login_attempts'] + 1);
        
        // Log failed login
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        
        $stmt = $pdo->prepare('INSERT INTO login_logs (user_id, email, ip_address, user_agent, action, success) VALUES (?, ?, ?, ?, "login", 0)');
        $stmt->execute([$admin['id'], $admin['email'], $ipAddress, $userAgent]);
        
        if ($attemptsLeft <= 0) {
            echo json_encode([
                'success' => false, 
                'message' => 'Invalid username or password. Your account has been temporarily locked due to too many failed attempts.'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => "Invalid username or password. You have {$attemptsLeft} attempts left before your account is temporarily locked."
            ]);
        }
        exit;
    }
    
    // Reset login attempts on successful login
    $stmt = $pdo->prepare('UPDATE admin_users SET login_attempts = 0, last_login = NOW() WHERE id = ?');
    $stmt->execute([$admin['id']]);
    
    // Set session variables
    $_SESSION['user_id'] = $admin['id'];
    $_SESSION['user_name'] = $admin['name'];
    $_SESSION['user_email'] = $admin['email'];
    $_SESSION['user_role'] = $admin['role'];
    $_SESSION['is_admin'] = true;
    $_SESSION['logged_in'] = true;
    
    // Set current IP for session security
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
    
    // Generate session token for CSRF protection
    $_SESSION['token'] = bin2hex(random_bytes(32));
    
    // Record last regeneration time
    $_SESSION['last_regeneration'] = time();
    
    // Set remember me cookie if requested
    if ($remember) {
        $selector = bin2hex(random_bytes(16));
        $authenticator = bin2hex(random_bytes(32));
        
        // Set cookie that expires in 30 days
        setcookie(
            'remember_me',
            $selector . ':' . $authenticator,
            time() + 60 * 60 * 24 * REMEMBER_ME_DAYS,
            '/',
            '',
            SECURE_COOKIES, // Only transmit over HTTPS
            true  // HTTP only (not accessible via JavaScript)
        );
        
        // Hash the authenticator before storing
        $hashedAuthenticator = password_hash($authenticator, PASSWORD_DEFAULT);
        
        // Delete any existing tokens for this user
        $stmt = $pdo->prepare('DELETE FROM remember_tokens WHERE user_id = ?');
        $stmt->execute([$admin['id']]);
        
        // Store the token
        $expiry = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * REMEMBER_ME_DAYS);
        $stmt = $pdo->prepare('INSERT INTO remember_tokens (user_id, selector, token, expires) VALUES (?, ?, ?, ?)');
        $stmt->execute([$admin['id'], $selector, $hashedAuthenticator, $expiry]);
    }
    
    // Log successful login
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    $stmt = $pdo->prepare('INSERT INTO login_logs (user_id, email, ip_address, user_agent, action, success) VALUES (?, ?, ?, ?, "login", 1)');
    $stmt->execute([$admin['id'], $admin['email'], $ipAddress, $userAgent]);
    
    // Determine redirect based on role
    $redirectUrl = 'admin-dashboard.php';
    
    if ($admin['role'] === 'super_admin') {
        $redirectUrl = 'admin-dashboard.php';
    } elseif ($admin['role'] === 'hr_manager') {
        $redirectUrl = 'admin-hr.php';
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Login successful. Redirecting to dashboard...',
        'redirect' => $redirectUrl
    ]);
    
} catch (PDOException $e) {
    // Log error
    error_log('Admin login error: ' . $e->getMessage());
    
    echo json_encode(['success' => false, 'message' => 'A database error occurred. Please try again later.']);
}
