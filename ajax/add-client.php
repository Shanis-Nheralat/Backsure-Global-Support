<?php
/**
 * Add New Client
 * AJAX handler for creating new client accounts by admin
 */

// Start session and check authentication
require_once '../session-check.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

// Check permission
if (!userHasPermission($_SESSION['user_id'], 'manage_users', true)) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to perform this action']);
    exit;
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

// Get and sanitize form data
$name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
$email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirmPassword = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : '';
$company = isset($_POST['company']) ? sanitizeInput($_POST['company']) : '';
$status = isset($_POST['status']) ? sanitizeInput($_POST['status']) : 'active';
$sendWelcome = isset($_POST['send_welcome']) ? true : false;

// Validate inputs
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required.';
}

if (empty($email)) {
    $errors[] = 'Email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if (empty($password)) {
    $errors[] = 'Password is required.';
} elseif (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters long.';
}

if ($password !== $confirmPassword) {
    $errors[] = 'Passwords do not match.';
}

// Validate status
$allowedStatuses = ['active', 'pending', 'blocked'];
if (!in_array($status, $allowedStatuses)) {
    $errors[] = 'Invalid status value.';
}

// Check password strength
$passwordStrength = checkPasswordStrength($password);
if ($passwordStrength < 3) { // Require at least strong strength
    $errors[] = 'Please choose a stronger password with a mix of uppercase, lowercase, numbers, and special characters.';
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
    
    // Check if email already exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'This email address is already registered.']);
        exit;
    }
    
    // Generate username from email if not provided
    $username = strtolower(explode('@', $email)[0]) . rand(100, 999);
    
    // Check if username exists and generate a new one if necessary
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() > 0) {
        $username = strtolower(explode('@', $email)[0]) . rand(1000, 9999);
    }
    
    // Generate password hash
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Generate email verification token if status is pending
    $verificationToken = null;
    if ($status === 'pending') {
        $verificationToken = generateToken(64);
    }
    
    // Set default role as 'client'
    $role = 'client';
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Insert user into database
    $stmt = $pdo->prepare('
        INSERT INTO users (name, email, username, password, role, status, verification_token, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ');
    $stmt->execute([
        $name,
        $email,
        $username,
        $passwordHash,
        $role,
        $status,
        $verificationToken
    ]);
    
    $userId = $pdo->lastInsertId();
    
    // Insert user meta data if company provided
    if (!empty($company)) {
        $stmt = $pdo->prepare('INSERT INTO user_meta (user_id, meta_key, meta_value) VALUES (?, ?, ?)');
        $stmt->execute([
            $userId,
            'company_name',
            $company
        ]);
    }
    
    // Store creation IP
    $stmt = $pdo->prepare('INSERT INTO user_meta (user_id, meta_key, meta_value) VALUES (?, ?, ?)');
    $stmt->execute([
        $userId,
        'admin_created',
        'Created by admin ID: ' . $_SESSION['user_id']
    ]);
    
    // Log the activity
    logActivity($_SESSION['user_id'], 'client_created', "Created client account: {$name} ({$email})");
    
    // Commit transaction
    $pdo->commit();
    
    // Send welcome email if requested and account is active
    if ($sendWelcome && $status === 'active') {
        $emailSubject = 'Welcome to Backsure Global Support';
        $emailBody = "
        <html>
        <body>
            <h2>Welcome to Backsure Global Support!</h2>
            <p>Hello {$name},</p>
            <p>Your account has been created by our administrative team.</p>
            <p>Here are your account details:</p>
            <ul>
                <li>Email: {$email}</li>
                <li>Username: {$username}</li>
            </ul>
            <p>You can log in using your email address and the password provided to you.</p>
            <p>If you have any questions, please contact our support team.</p>
            <p>Thanks,<br>
            Backsure Global Support Team</p>
        </body>
        </html>
        ";
        
        // Send the welcome email
        sendEmail($email, $emailSubject, $emailBody);
    }
    
    // Send verification email if status is pending
    if ($status === 'pending' && $verificationToken) {
        $baseUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $baseUrl .= $_SERVER['HTTP_HOST'];
        $verificationLink = $baseUrl . '/verify-client.php?email=' . urlencode($email) . '&token=' . $verificationToken;
        
        $emailSubject = 'Verify Your Email - Backsure Global Support';
        $emailBody = "
        <html>
        <body>
            <h2>Welcome to Backsure Global Support!</h2>
            <p>Hello {$name},</p>
            <p>Your account has been created by our administrative team. Please verify your email address to complete your registration.</p>
            <p><a href='{$verificationLink}' style='background-color:#062767;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;display:inline-block;'>Verify Email</a></p>
            <p>Or copy and paste this link into your browser:</p>
            <p>{$verificationLink}</p>
            <p>This link will expire in " . VERIFY_TOKEN_HOURS . " hours.</p>
            <p>Thanks,<br>
            Backsure Global Support Team</p>
        </body>
        </html>
        ";
        
        // Send the verification email
        sendEmail($email, $emailSubject, $emailBody);
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Client account has been created successfully.',
        'redirect' => 'admin-clients.php',
        'client' => [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'status' => $status
        ]
    ]);
    
} catch (PDOException $e) {
    // Rollback transaction on error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Log error
    error_log('Add client error: ' . $e->getMessage());
    
    echo json_encode(['success' => false, 'message' => 'A database error occurred. Please try again later.']);
}
