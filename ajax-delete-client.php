<?php
/**
 * Delete Client
 * AJAX handler for removing client accounts
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

// Check permission for deleting users - only super_admin or admin should have this
if (!userHasPermission($_SESSION['user_id'], 'manage_users', true)) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to perform this action']);
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

if (!isset($input['client_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing client ID parameter']);
    exit;
}

$clientId = filter_var($input['client_id'], FILTER_VALIDATE_INT);

if (!$clientId) {
    echo json_encode(['success' => false, 'message' => 'Invalid client ID']);
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
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Check if client exists
    $stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE id = ?');
    $stmt->execute([$clientId]);
    $client = $stmt->fetch();
    
    if (!$client) {
        echo json_encode(['success' => false, 'message' => 'Client not found']);
        exit;
    }
    
    // Log the deletion first, as user data will be removed
    $clientName = $client['name'];
    $clientEmail = $client['email'];
    
    logActivity($_SESSION['user_id'], 'client_deleted', "Deleted client: {$clientName} ({$clientEmail})");
    
    // Delete related data (in the correct order to respect foreign keys)
    
    // Delete remember tokens
    $stmt = $pdo->prepare('DELETE FROM remember_tokens WHERE user_id = ?');
    $stmt->execute([$clientId]);
    
    // Delete activity logs
    $stmt = $pdo->prepare('DELETE FROM activity_logs WHERE user_id = ?');
    $stmt->execute([$clientId]);
    
    // Delete login logs
    $stmt = $pdo->prepare('DELETE FROM login_logs WHERE user_id = ?');
    $stmt->execute([$clientId]);
    
    // Delete password reset tokens (if any)
    $stmt = $pdo->prepare('DELETE FROM password_resets WHERE email = ?');
    $stmt->execute([$clientEmail]);
    
    // Delete user metadata
    $stmt = $pdo->prepare('DELETE FROM user_meta WHERE user_id = ?');
    $stmt->execute([$clientId]);
    
    // Finally, delete the user
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$clientId]);
    
    // Commit the transaction
    $pdo->commit();
    
    // Return success
    echo json_encode([
        'success' => true,
        'message' => 'Client has been successfully deleted'
    ]);
    
} catch (PDOException $e) {
    // Rollback on error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log('Delete client error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A database error occurred. Please try again.']);
}