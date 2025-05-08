<?php
/**
 * Login Path Tracer
 * This script will add temporary logging to track which login files are being used
 */

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Create a log file
$log_file = 'login_trace.log';
file_put_contents($log_file, "=== Login Path Trace Started: " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);

// Get the current file path
$current_file = $_SERVER['SCRIPT_FILENAME'];
$filename = basename($current_file);

// Log the current file access
file_put_contents($log_file, "[ACCESS] " . $filename . " was accessed at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Log POST data if this is a form submission (with passwords obscured)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_data = $_POST;
    if (isset($post_data['password'])) {
        $post_data['password'] = '********';
    }
    file_put_contents($log_file, "[POST] Data: " . json_encode($post_data) . "\n", FILE_APPEND);
}

// Log the referer
if (isset($_SERVER['HTTP_REFERER'])) {
    file_put_contents($log_file, "[REFERER] " . $_SERVER['HTTP_REFERER'] . "\n", FILE_APPEND);
}

// Log current session data
session_start();
$session_data = $_SESSION;
// Remove sensitive data from log
if (isset($session_data['password'])) {
    $session_data['password'] = '********';
}
file_put_contents($log_file, "[SESSION] Data: " . json_encode($session_data) . "\n", FILE_APPEND);

// Return to normal execution
?>
