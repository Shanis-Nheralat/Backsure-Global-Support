<?php
/**
 * Settings Test Script
 * Tests the settings system functionality
 */

// Define constants for this page
define('ADMIN_PANEL', true);

// Authentication and permissions
require_once 'admin-auth.php';
require_admin_auth();
require_admin_role(['admin']);

// Include notifications system
require_once 'admin-notifications.php';

// Include settings functions
require_once 'settings-functions.php';

// Set page title and header
$page_title = 'Settings System Test';

// Create test log function
function log_test($message, $type = 'info') {
    echo "<div class='alert alert-{$type}'>{$message}</div>";
}

// Initialize test results
$test_results = [
    'passed' => 0,
    'failed' => 0,
    'total' => 0
];

// Process tests if requested
$tests_complete = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_tests'])) {
    $tests_complete = true;
    
    // Start testing process
    log_test("Starting settings system tests...", 'primary');
    
    // Test 1: Database connection
    $test_results['total']++;
    try {
        global $db;
        if ($db && $db->ping()) {
            log_test("✅ Test 1: Database connection successful", 'success');
            $test_results['passed']++;
        } else {
            log_test("❌ Test 1: Database connection failed", 'danger');
            $test_results['failed']++;
        }
    } catch (Exception $e) {
        log_test("❌ Test 1: Database connection failed - " . $e->getMessage(), 'danger');
        $test_results['failed']++;
    }
    
    // Test 2: Settings table exists
    $test_results['total']++;
    $table_check = $db->query("SHOW TABLES LIKE 'settings'");
    if ($table_check && $table_check->num_rows > 0) {
        log_test("✅ Test 2: Settings table exists", 'success');
        $test_results['passed']++;
    } else {
        log_test("❌ Test 2: Settings table not found", 'danger');
        $test_results['failed']++;
    }
    
    // Test 3: Create a test setting
    $test_results['total']++;
    $test_value = 'test_value_' . time();
    if (set_setting('test_group', 'test_key', $test_value, 'text')) {
        log_test("✅ Test 3: Test setting created successfully", 'success');
        $test_results['passed']++;
    } else {
        log_test("❌ Test 3: Failed to create test setting", 'danger');
        $test_results['failed']++;
    }
    
    // Test 4: Retrieve the test setting
    $test_results['total']++;
    $retrieved_value = get_setting('test_group', 'test_key');
    if ($retrieved_value === $test_value) {
        log_test("✅ Test 4: Test setting retrieved successfully (value: {$retrieved_value})", 'success');
        $test_results['passed']++;
    } else {
        log_test("❌ Test 4: Failed to retrieve test setting correctly (expected: {$test_value}, got: {$retrieved_value})", 'danger');
        $test_results['failed']++;
    }
    
    // Test 5: Update the test setting
    $test_results['total']++;
    $updated_value = 'updated_value_' . time();
    if (set_setting('test_group', 'test_key', $updated_value, 'text')) {
        log_test("✅ Test 5: Test setting updated successfully", 'success');
        $test_results['passed']++;
    } else {
        log_test("❌ Test 5: Failed to update test setting", 'danger');
        $test_results['failed']++;
    }
    
    // Test 6: Retrieve the updated test setting
    $test_results['total']++;
    $retrieved_value = get_setting('test_group', 'test_key');
    if ($retrieved_value === $updated_value) {
        log_test("✅ Test 6: Updated test setting retrieved successfully (value: {$retrieved_value})", 'success');
        $test_results['passed']++;
    } else {
        log_test("❌ Test 6: Failed to retrieve updated test setting correctly (expected: {$updated_value}, got: {$retrieved_value})", 'danger');
        $test_results['failed']++;
    }
    
    // Test 7: Get setting with default value
    $test_results['total']++;
    $default_value = 'default_test_value';
    $retrieved_value = get_setting('test_group', 'non_existent_key', $default_value);
    if ($retrieved_value === $default_value) {
        log_test("✅ Test 7: Default value returned correctly for non-existent setting", 'success');
        $test_results['passed']++;
    } else {
        log_test("❌ Test 7: Failed to return default value for non-existent setting", 'danger');
        $test_results['failed']++;
    }
    
    // Test 8: Get settings by group
    $test_results['total']++;
    $group_settings = get_settings_by_group('test_group');
    if (is_array($group_settings) && isset($group_settings['test_key']) && $group_settings['test_key'] === $updated_value) {
        log_test("✅ Test 8: Settings retrieved by group successfully", 'success');
        $test_results['passed']++;
    } else {
        log_test("❌ Test 8: Failed to retrieve settings by group", 'danger');
        $test_results['failed']++;
    }
    
    // Test 9: Test boolean setting
    $test_results['total']++;
    set_setting('test_group', 'bool_test', true, 'boolean');
    $bool_value = get_setting('test_group', 'bool_test');
    if ($bool_value === true) {
        log_test("✅ Test 9: Boolean setting handled correctly", 'success');
        $test_results['passed']++;
    } else {
        log_test("❌ Test 9: Failed to handle boolean setting correctly (expected: true, got: " . var_export($bool_value, true) . ")", 'danger');
        $test_results['failed']++;
    }
    
    // Test 10: Test JSON setting
    $test_results['total']++;
    $json_data = ['key1' => 'value1', 'key2' => 'value2'];
    set_setting('test_group', 'json_test', $json_data, 'json');
    $json_value = get_setting('test_group', 'json_test');
    if (is_array($json_value) && $json_value['key1'] === 'value1' && $json_value['key2'] === 'value2') {
        log_test("✅ Test 10: JSON setting handled correctly", 'success');
        $test_results['passed']++;
    } else {
        log_test("❌ Test 10: Failed to handle JSON setting correctly", 'danger');
        $test_results['failed']++;
    }
    
    // Test 11: File upload directories
    $test_results['total']++;
    if (defined('UPLOAD_DIR') && is_dir(UPLOAD_DIR) && is_writable(UPLOAD_DIR)) {
        log_test("✅ Test 11: Upload directory exists and is writable", 'success');
        $test_results['passed']++;
    } else {
        log_test("❌ Test 11: Upload directory issues - " . (defined('UPLOAD_DIR') ? UPLOAD_DIR : 'UPLOAD_DIR not defined'), 'danger');
        $test_results['failed']++;
    }
    
    // Test 12: Notification system
    $test_results['total']++;
    try {
        set_success_message("Test notification message");
        log_test("✅ Test 12: Notification system is working", 'success');
        $test_results['passed']++;
    } catch (Exception $e) {
        log_test("❌ Test 12: Notification system error - " . $e->getMessage(), 'danger');
        $test_results['failed']++;
    }
    
    // Test 13: Check settings pages
    $test_results['total']++;
    $settings_pages = [
        'admin-seo.php',
        'admin-integrations.php',
        'admin-chat-settings.php',
        'admin-settings.php',
        'admin-notification-settings.php'
    ];
    
    $missing_pages = [];
    foreach ($settings_pages as $page) {
        if (!file_exists($page)) {
            $missing_pages[] = $page;
        }
    }
    
    if (empty($missing_pages)) {
        log_test("✅ Test 13: All settings pages exist", 'success');
        $test_results['passed']++;
    } else {
        log_test("❌ Test 13: Missing settings pages: " . implode(', ', $missing_pages), 'danger');
        $test_results['failed']++;
    }
    
    // Test 14: Check menu configuration
    $test_results['total']++;
    if (file_exists('admin-menu-config.php')) {
        include_once 'admin-menu-config.php';
        
        $settings_menu_found = false;
        foreach ($admin_menu as $menu_item) {
            if ($menu_item['id'] === 'settings') {
                $settings_menu_found = true;
                break;
            }
        }
        
        if ($settings_menu_found) {
            log_test("✅ Test 14: Settings menu exists in configuration", 'success');
            $test_results['passed']++;
        } else {
            log_test("❌ Test 14: Settings menu not found in configuration", 'danger');
            $test_results['failed']++;
        }
    } else {
        log_test("❌ Test 14: Menu configuration file not found", 'danger');
        $test_results['failed']++;
    }
    
    // Final summary
    log_test("Testing completed. Passed: {$test_results['passed']}/{$test_results['total']} tests", $test_results['passed'] === $test_results['total'] ? 'success' : 'warning');
}

// Include the admin header
include 'admin-head.php';
include 'admin-sidebar.php';
include 'admin-header.php';
?>

<main class="admin-main">
  <div class="admin-content container-fluid py-4">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <h1><?php echo $page_title; ?></h1>
    </div>
    
    <?php display_notifications(); ?>
    
    <?php if ($tests_complete): ?>
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Test Results</h6>
      </div>
      <div class="card-body">
        <div class="test-results mb-4">
          <div class="alert <?php echo $test_results['passed'] === $test_results['total'] ? 'alert-success' : 'alert-warning'; ?>">
            <h5 class="alert-heading">
              <?php echo $test_results['passed'] === $test_results['total'] ? '✅ All tests passed!' : '⚠️ Some tests failed'; ?>
            </h5>
            <p>Passed: <?php echo $test_results['passed']; ?> / <?php echo $test_results['total']; ?> tests</p>
            <?php if ($test_results['failed'] > 0): ?>
            <p>Failed: <?php echo $test_results['failed']; ?> tests</p>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="d-flex mt-4">
          <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-primary">
            <i class="fas fa-sync-alt me-2"></i> Run Tests Again
          </a>
          <a href="admin-dashboard.php" class="btn btn-secondary ms-2">
            <i class="fas fa-home me-2"></i> Return to Dashboard
          </a>
          
          <?php if ($test_results['failed'] > 0): ?>
          <div class="ms-auto">
            <a href="settings-troubleshoot.php" class="btn btn-warning">
              <i class="fas fa-wrench me-2"></i> Troubleshooting Guide
            </a>
          </div>
          <?php endif; ?>
