<?php
/**
 * Admin Panel Diagnostic Script
 * Place this file in your admin directory and run it to diagnose common issues
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if file exists and is readable
function check_file($file) {
    if (!file_exists($file)) {
        return "ERROR: File '$file' not found.";
    }
    if (!is_readable($file)) {
        return "ERROR: File '$file' exists but is not readable.";
    }
    return "OK: File '$file' exists and is readable.";
}

// Function to check if directory exists and is writable
function check_dir($dir) {
    if (!file_exists($dir)) {
        return "ERROR: Directory '$dir' not found.";
    }
    if (!is_dir($dir)) {
        return "ERROR: '$dir' exists but is not a directory.";
    }
    if (!is_writable($dir)) {
        return "WARNING: Directory '$dir' exists but is not writable.";
    }
    return "OK: Directory '$dir' exists and is writable.";
}

// Output function
function output_section($title, $results) {
    echo "<h2>$title</h2>";
    echo "<ul>";
    foreach ($results as $item => $status) {
        $class = (strpos($status, 'ERROR') !== false) ? 'error' : 
                 ((strpos($status, 'WARNING') !== false) ? 'warning' : 'success');
        echo "<li class='$class'><strong>$item:</strong> $status</li>";
    }
    echo "</ul>";
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel Diagnostics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1 {
            background-color: #f4f4f4;
            padding: 10px;
            border-bottom: 2px solid #ddd;
        }
        h2 {
            margin-top: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 3px;
        }
        .success {
            background-color: #e8f5e9;
            border-left: 5px solid #4caf50;
        }
        .warning {
            background-color: #fff8e1;
            border-left: 5px solid #ff9800;
        }
        .error {
            background-color: #ffebee;
            border-left: 5px solid #f44336;
        }
        .btn {
            display: inline-block;
            background: #4caf50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 20px;
        }
        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            overflow: auto;
        }
    </style>
</head>
<body>
    <h1>Admin Panel Diagnostics</h1>

    <?php
    // Check PHP version
    $phpVersionResult = [
        'PHP Version' => 'Current version: ' . phpversion() . 
                        (version_compare(phpversion(), '7.4.0', '>=') ? 
                         ' (OK: Compatible)' : ' (WARNING: Recommended PHP 7.4 or higher)')
    ];
    output_section('PHP Environment', $phpVersionResult);

    // Check required files
    $requiredFiles = [
        'admin-auth.php',
        'admin-head.php',
        'admin-sidebar.php',
        'admin-header.php',
        'admin-footer.php',
        'admin-dashboard.php',
        'db_config.php'
    ];

    $fileResults = [];
    foreach ($requiredFiles as $file) {
        $fileResults[$file] = check_file($file);
    }
    output_section('Required Files', $fileResults);

    // Check asset directories
    $assetDirs = [
        'assets',
        'assets/css',
        'assets/js',
        'assets/images',
        'assets/lib'
    ];

    $dirResults = [];
    foreach ($assetDirs as $dir) {
        $dirResults[$dir] = check_dir($dir);
    }
    output_section('Asset Directories', $dirResults);

    // Check core assets
    $coreAssets = [
        'assets/css/admin-core.css',
        'assets/js/admin-core.js'
    ];

    $assetResults = [];
    foreach ($coreAssets as $asset) {
        $assetResults[$asset] = check_file($asset);
    }
    output_section('Core Assets', $assetResults);

    // Check session state
    echo "<h2>Session Information</h2>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";

    // Check database connection if possible
    echo "<h2>Database Connection</h2>";
    if (file_exists('db_config.php')) {
        try {
            require_once 'db_config.php';
            
            // This assumes your db_config.php creates a PDO connection named $pdo
            // Adjust as needed for your specific setup
            if (isset($pdo) && $pdo instanceof PDO) {
                try {
                    $test_query = $pdo->query("SELECT 1 as test");
                    $result = $test_query->fetch(PDO::FETCH_ASSOC);
                    if ($result['test'] == 1) {
                        echo "<p class='success'>Database connection successful!</p>";
                    } else {
                        echo "<p class='error'>Database connection test returned unexpected result.</p>";
                    }
                } catch (PDOException $e) {
                    echo "<p class='error'>Database query error: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p class='error'>PDO connection not found. Check your db_config.php file.</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>Error including db_config.php: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='error'>db_config.php file not found.</p>";
    }

    // Check error logs
    echo "<h2>PHP Errors</h2>";
    $errorLog = ini_get('error_log');
    if ($errorLog && file_exists($errorLog) && is_readable($errorLog)) {
        $logContent = file_get_contents($errorLog);
        $lastErrors = array_slice(explode("\n", $logContent), -20);
        echo "<p>Last 20 lines from error log ($errorLog):</p>";
        echo "<pre>" . implode("\n", $lastErrors) . "</pre>";
    } else {
        echo "<p class='warning'>Cannot access PHP error log. Check your server configuration.</p>";
    }
    ?>

    <a href="admin-dashboard.php" class="btn">Try Admin Dashboard</a>

</body>
</html>
