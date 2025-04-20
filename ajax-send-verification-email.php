<?php
/**
 * Send Verification Email
 * AJAX handler for resending verification emails to pending clients
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

// Verify CSRF token
$headers = getallheaders();
$csrfToken = isset($headers['X-CSRF-Token']) ? $headers['X-CSRF-Token'] : '';

if (!verifyCSRFToken($csrfToken)) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

// Check if the request is POST and has required data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get and validate input data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['client_id']) || !isset($input['email'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$clientId = filter_var($input['client_id'], FILTER_VALIDATE_INT);
$email = filter_var($input['email'], FILTER_VALIDATE_EMAIL);

if (!$clientId || !$email) {
    echo json_encode(['success' => false, 'message' => 'Invalid client ID or email']);
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
    
    // Check if client exists and is pending
    $stmt = $pdo->prepare('SELECT id, name, email, status FROM users WHERE id = ? AND email = ?');
    $stmt->execute([$clientId, $email]);
    $client = $stmt->fetch();
    
    if (!$client) {
        echo json_encode(['success' => false, 'message' => 'Client not found']);
        exit;
    }
    
    if ($client['status'] !== 'pending') {
        echo json_encode(['success' => false, 'message' => 'Client account is not pending verification']);
        exit;
    }
    
    // Generate new verification token
    $verificationToken = generateToken(64);
    $tokenExpiry = date('Y-m-d H:i:s', time() + (VERIFY_TOKEN_HOURS * 3600));
    
    // Update client with new token
    $stmt = $pdo->prepare('UPDATE users SET verification_token = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([$verificationToken, $clientId]);
    
    // Generate the verification link
    $baseUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $baseUrl .= $_SERVER['HTTP_HOST'];
    $verificationLink = $baseUrl . '/verify-client.php?email=' . urlencode($email) . '&token=' . $verificationToken;
    
    // Prepare verification email
    $emailSubject = 'Verify Your Email - Backsure Global Support';
    $emailBody = "
    <html>
    <body>
        <h2>Welcome to Backsure Global Support!</h2>
        <p>Hello {$client['name']},</p>
        <p>Your account needs to be verified. Please click the button below to verify your email address.</p>
        <p><a href='{$verificationLink}' style='background-color:#062767;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;display:inline-block;'>Verify Email</a></p>
        <p>Or copy and paste this link into your browser:</p>
        <p>{$verificationLink}</p>
        <p>This link will expire in " . VERIFY_TOKEN_HOURS . " hours.</p>
        <p>If you did not sign up for an account, please ignore this email.</p>
        <p>Thanks,<br>
        Backsure Global Support Team</p>
    </body>
    </html>
    ";
    
    // Send the verification email
    $emailSent = sendEmail($email, $emailSubject, $emailBody);
    
    // For demonstration, log it
    error_log('Verification email would be sent to: ' . $email);
    error_log('Verification link: ' . $verificationLink);
    
    // Log the action
    logActivity($_SESSION['user_id'], 'verification_email_sent', "Sent to client ID: {$clientId}");
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Verification email has been sent to ' . $email,
        'debug' => [
            'verification_link' => $verificationLink // Only for demonstration
        ]
    ]);
    
} catch (PDOException $e) {
    error_log('Send verification email error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A database error occurred']);
}