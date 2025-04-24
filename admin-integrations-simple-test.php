<?php
// Basic test script
session_start();

// Force login for testing (remove in production)
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_username'] = 'Admin User';
$_SESSION['admin_role'] = 'admin';

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Output a simple message
echo '<html><head><title>Integration Test</title></head><body>';
echo '<h1>Integration Test Page</h1>';
echo '<p>If you can see this message, PHP is working correctly.</p>';
echo '<p>Current PHP version: ' . phpversion() . '</p>';
echo '<h2>Session Information:</h2>';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
echo '</body></html>';
?>
