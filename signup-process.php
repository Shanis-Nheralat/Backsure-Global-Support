<?php
/**
 * signup-process.php
 * Processes user registration requests
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

// Get form data
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$email = isset($_POST['email']) ? trim(strtolower($_POST['email'])) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirmPassword = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : '';
$termsAccepted = isset($_POST['terms']) ? (bool)$_POST['terms'] : false;

// Validate inputs
$errors = [];

if (empty($fullname)) {
    $errors[] = 'Full name is required.';
}

if (empty($email)) {
    $errors[] = 'Email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if (empty($username)) {
    $errors[] = 'Username is required.';
} elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
    $errors[] = 'Username must be 3-20 characters long and contain only letters, numbers, and underscores.';
}

if (empty($password)) {
    $errors[] = 'Password is required.';
} elseif (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters long.';
}

if ($password !== $confirmPassword) {
    $errors[] = 'Passwords do not match.';
}

if (!$termsAccepted) {
    $errors[] = 'You must agree to the Terms & Conditions.';
}

// Check password strength
$passwordStrength = checkPasswordStrength($password);
if ($passwordStrength < 2) { // Require at least medium strength
    $errors[] = 'Please choose a stronger password.';
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
    
    // Check if username already exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'This username is already taken.']);
        exit;
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'This email address is already registered.']);
        exit;
    }
    
    // Generate password hash
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Generate email verification token
    $verificationToken = bin2hex(random_bytes(32));
    
    // Set default role as 'user'
    // Note: For admin dashboard access, roles would typically be assigned manually
    // by a super admin, but for demonstration we're showing the signup process
    $role = 'pending'; // Pending approval
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Insert user into database
    $stmt = $pdo->prepare('INSERT INTO users (name, email, username, password, role, verification_token, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
    $stmt->execute([
        $fullname,
        $email,
        $username,
        $passwordHash,
        $role,
        $verificationToken
    ]);
    
    $userId = $pdo->lastInsertId();
    
    // Insert user meta data
    $stmt = $pdo->prepare('INSERT INTO user_meta (user_id, meta_key, meta_value) VALUES (?, ?, ?)');
    
    // Store registration IP
    $stmt->execute([
        $userId,
        'registration_ip',
        $_SERVER['REMOTE_ADDR']
    ]);
    
    // Store user agent
    $stmt->execute([
        $userId,
        'user_agent',
        $_SERVER['HTTP_USER_AGENT']
    ]);
    
    // Commit transaction
    $pdo->commit();
    
    // Send verification email
    $verificationLink = 'https://' . $_SERVER['HTTP_HOST'] . '/admin-login.html?verify=' . urlencode($email) . '&token=' . $verificationToken;
    
    $emailSubject = 'Verify Your Email - Backsure Global Support';
    $emailBody = "
    <html>
    <body>
        <h2>Welcome to Backsure Global Support!</h2>
        <p>Thank you for signing up. Please verify your email address to complete your registration.</p>
        <p><a href='$verificationLink' style='background-color:#062767;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;display:inline-block;'>Verify Email</a></p>
        <p>Or copy and paste this link into your browser:</p>
        <p>$verificationLink</p>
        <p>This link will expire in 24 hours.</p>
        <p>If you did not sign up for an account, please ignore this email.</p>
        <p>Thanks,<br>
        Backsure Global Support Team</p>
    </body>
    </html>
    ";
    
    // In a real application, you would send this email
    // For demonstration, we'll just log it
    error_log('Verification email would be sent to: ' . $email);
    error_log('Verification link: ' . $verificationLink);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Your account has been created successfully! Please check your email to verify your account.',
        'debug' => [
            'verification_link' => $verificationLink // Only for demonstration
        ]
    ]);
    
} catch (PDOException $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Log error
    error_log('Signup error: ' . $e->getMessage());
    
    echo json_encode(['success' => false, 'message' => 'A database error occurred. Please try again later.']);
}

/**
 * Check password strength
 * Returns a score from 0-4
 * 0: Too weak, 1: Weak, 2: Medium, 3: Strong, 4: Very strong
 */
function checkPasswordStrength($password) {
    $score = 0;
    
    // Length check
    if (strlen($password) >= 8) $score++;
    if (strlen($password) >= 12) $score++;
    
    // Complexity checks
    if (preg_match('/[0-9]/', $password)) $score++;
    if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) $score++;
    if (preg_match('/[^a-zA-Z0-9]/', $password)) $score++;
    
    // Cap score at 4
    return min(4, $score);
}