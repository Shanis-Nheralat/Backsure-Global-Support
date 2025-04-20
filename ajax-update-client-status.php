<?php
/**
 * Update Client Status
 * AJAX handler for blocking/unblocking clients
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

if (!isset($input['client_id']) || !isset($input['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$clientId = filter_var($input['client_id'], FILTER_VALIDATE_INT);
$status = trim($input['status']);

if (!$clientId) {
    echo json_encode(['success' => false, 'message' => 'Invalid client ID']);
    exit;
}

// Validate status
$allowedStatuses = ['active', 'blocked'];
if (!in_array($status, $allowedStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status value']);
    exit;
}

// Check permission
if (!userHasPermission($_SESSION['user_id'], 'block_users', true)) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to perform this action']);
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
    
    // Check if client exists
    $stmt = $pdo->prepare('SELECT id, name, email, status FROM users WHERE id = ?');
    $stmt->execute([$clientId]);
    $client = $stmt->fetch();
    
    if (!$client) {
        echo json_encode(['success' => false, 'message' => 'Client not found']);
        exit;
    }
    
    // Skip update if status is already set
    if ($client['status'] === $status) {
        echo json_encode(['success' => true, 'message' => 'Status already set to ' . $status]);
        exit;
    }
    
    // Update client status
    $stmt = $pdo->prepare('UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([$status, $clientId]);
    
    // Log the action
    $actionType = $status === 'active' ? 'client_activated' : 'client_blocked';
    $actionDetails = "Client status changed to {$status}";
    
    logActivity($_SESSION['user_id'], $actionType, $actionDetails);
    
    // Also log in the client's activity
    logActivity($clientId, 'status_changed', "Account {$status} by admin");
    
    // Return success
    echo json_encode([
        'success' => true,
        'message' => 'Client status updated successfully',
        'status' => $status
    ]);
    
} catch (PDOException $e) {
    error_log('Update client status error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A database error occurred']);
}