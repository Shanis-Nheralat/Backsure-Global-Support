<?php
/**
 * Get Client Details
 * AJAX handler for fetching detailed client information
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
    
    // Get client basic info
    $stmt = $pdo->prepare('
        SELECT u.id, u.name, u.email, u.username, u.role, u.status, 
               u.email_verified_at, u.last_login, u.created_at, u.updated_at
        FROM users u
        WHERE u.id = ?
    ');
    $stmt->execute([$clientId]);
    $client = $stmt->fetch();
    
    if (!$client) {
        echo json_encode(['success' => false, 'message' => 'Client not found']);
        exit;
    }
    
    // Format dates for readability
    $client['created_at'] = date('M j, Y g:i A', strtotime($client['created_at']));
    $client['updated_at'] = date('M j, Y g:i A', strtotime($client['updated_at']));
    
    if ($client['last_login']) {
        $client['last_login'] = date('M j, Y g:i A', strtotime($client['last_login']));
    }
    
    if ($client['email_verified_at']) {
        $client['email_verified_at'] = date('M j, Y g:i A', strtotime($client['email_verified_at']));
    }
    
    // Get client metadata
    $stmt = $pdo->prepare('
        SELECT meta_key, meta_value
        FROM user_meta
        WHERE user_id = ?
    ');
    $stmt->execute([$clientId]);
    $metaResults = $stmt->fetchAll();
    
    // Convert metadata to associative array
    $metadata = [];
    foreach ($metaResults as $meta) {
        $metadata[$meta['meta_key']] = $meta['meta_value'];
    }
    
    // Add metadata to client array
    $client['company'] = $metadata['company_name'] ?? '';
    $client['phone'] = $metadata['phone_number'] ?? '';
    $client['company_size'] = $metadata['company_size'] ?? '';
    $client['registration_ip'] = $metadata['registration_ip'] ?? '';
    
    // Get recent activity (last 10 entries)
    $stmt = $pdo->prepare('
        SELECT action, ip_address, details, created_at
        FROM activity_logs
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 10
    ');
    $stmt->execute([$clientId]);
    $activities = $stmt->fetchAll();
    
    // Format activity dates
    foreach ($activities as &$activity) {
        $activity['created_at'] = date('M j, Y g:i A', strtotime($activity['created_at']));
    }
    
    // Add activities to client array
    $client['activities'] = $activities;
    
    // Log this view action
    logActivity($_SESSION['user_id'], 'client_profile_viewed', "Viewed client ID: {$clientId}");
    
    // Return client data
    echo json_encode([
        'success' => true,
        'client' => $client
    ]);
    
} catch (PDOException $e) {
    error_log('Get client details error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A database error occurred']);
}
