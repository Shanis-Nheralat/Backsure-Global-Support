<?php
/**
 * AJAX endpoint to save solution data
 */

// Include database configuration
require_once 'db_config.php';

// Set headers
header('Content-Type: application/json');

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get POST data
$solution_id = $_POST['solution_id'] ?? '';
$hero_title = $_POST['hero_title'] ?? '';
$hero_description = $_POST['hero_description'] ?? '';
$hero_image_url = $_POST['hero_image_url'] ?? '';
$intro_text = $_POST['intro_text'] ?? '';
$feature1_title = $_POST['feature1_title'] ?? '';
$feature1_description = $_POST['feature1_description'] ?? '';
$feature1_image_url = $_POST['feature1_image_url'] ?? '';
$feature2_title = $_POST['feature2_title'] ?? '';
$feature2_image_url = $_POST['feature2_image_url'] ?? '';
$feature3_title = $_POST['feature3_title'] ?? '';
$feature3_image_url = $_POST['feature3_image_url'] ?? '';
$summary_title = $_POST['summary_title'] ?? '';
$summary_text = $_POST['summary_text'] ?? '';
$cta_title = $_POST['cta_title'] ?? '';
$cta_text = $_POST['cta_text'] ?? '';
$cta_button1_text = $_POST['cta_button1_text'] ?? '';
$cta_button1_link = $_POST['cta_button1_link'] ?? '';
$cta_button2_text = $_POST['cta_button2_text'] ?? '';
$cta_button2_link = $_POST['cta_button2_link'] ?? '';

// Handle feature lists (convert to JSON)
$feature2_features = [];
if (isset($_POST['feature2_features']) && is_array($_POST['feature2_features'])) {
    $feature2_features = $_POST['feature2_features'];
}

$feature3_features = [];
if (isset($_POST['feature3_features']) && is_array($_POST['feature3_features'])) {
    $feature3_features = $_POST['feature3_features'];
}

// Encode feature lists as JSON
$feature2_features_json = json_encode($feature2_features);
$feature3_features_json = json_encode($feature3_features);

// Validate required fields
if (empty($solution_id) || empty($hero_title)) {
    echo json_encode([
        'success' => false,
        'message' => 'Solution ID and Hero Title are required'
    ]);
    exit;
}

// Check if solution exists (to determine insert or update)
$check_sql = "SELECT solution_id FROM solutions WHERE solution_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $solution_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$solution_exists = $check_result->num_rows > 0;
$check_stmt->close();

// Prepare SQL statement based on whether solution exists
if ($solution_exists) {
    // Update existing solution
    $sql = "UPDATE solutions SET 
            hero_title = ?, 
            hero_description = ?, 
            hero_image_url = ?,
            intro_text = ?,
            feature1_title = ?,
            feature1_description = ?,
            feature1_image_url = ?,
            feature2_title = ?,
            feature2_features = ?,
            feature2_image_url = ?,
            feature3_title = ?,
            feature3_features = ?,
            feature3_image_url = ?,
            summary_title = ?,
            summary_text = ?,
            cta_title = ?,
            cta_text = ?,
            cta_button1_text = ?,
            cta_button1_link = ?,
            cta_button2_text = ?,
            cta_button2_link = ?
            WHERE solution_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssssssssssssss",
        $hero_title,
        $hero_description,
        $hero_image_url,
        $intro_text,
        $feature1_title,
        $feature1_description,
        $feature1_image_url,
        $feature2_title,
        $feature2_features_json,
        $feature2_image_url,
        $feature3_title,
        $feature3_features_json,
        $feature3_image_url,
        $summary_title,
        $summary_text,
        $cta_title,
        $cta_text,
        $cta_button1_text,
        $cta_button1_link,
        $cta_button2_text,
        $cta_button2_link,
        $solution_id
    );
} else {
    // Insert new solution
    $sql = "INSERT INTO solutions (
            solution_id,
            hero_title,
            hero_description,
            hero_image_url,
            intro_text,
            feature1_title,
            feature1_description,
            feature1_image_url,
            feature2_title,
            feature2_features,
            feature2_image_url,
            feature3_title,
            feature3_features,
            feature3_image_url,
            summary_title,
            summary_text,
            cta_title,
            cta_text,
            cta_button1_text,
            cta_button1_link,
            cta_button2_text,
            cta_button2_link
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssssssssssssss",
        $solution_id,
        $hero_title,
        $hero_description,
        $hero_image_url,
        $intro_text,
        $feature1_title,
        $feature1_description,
        $feature1_image_url,
        $feature2_title,
        $feature2_features_json,
        $feature2_image_url,
        $feature3_title,
        $feature3_features_json,
        $feature3_image_url,
        $summary_title,
        $summary_text,
        $cta_title,
        $cta_text,
        $cta_button1_text,
        $cta_button1_link,
        $cta_button2_text,
        $cta_button2_link
    );
}

// Execute statement
if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => $solution_exists ? 'Solution updated successfully' : 'Solution created successfully',
        'solution_id' => $solution_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $stmt->error
    ]);
}

$stmt->close();