<?php
/**
 * AJAX endpoint to retrieve solution data for editing
 */

// Include database configuration
require_once 'db_config.php';

// Set headers
header('Content-Type: application/json');

// Check if a solution ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No solution ID provided'
    ]);
    exit;
}

// Sanitize input
$solution_id = $_GET['id'];

// Query to fetch solution data
$sql = "SELECT * FROM solutions WHERE solution_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $solution_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if solution exists
if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Solution not found'
    ]);
    exit;
}

// Get solution data
$solution = $result->fetch_assoc();
$stmt->close();

// Parse JSON feature lists if they exist
if (!empty($solution['feature2_features'])) {
    $solution['feature2_features'] = json_decode($solution['feature2_features'], true);
}

if (!empty($solution['feature3_features'])) {
    $solution['feature3_features'] = json_decode($solution['feature3_features'], true);
}

// Return success with data
echo json_encode([
    'success' => true,
    'data' => $solution
]);
