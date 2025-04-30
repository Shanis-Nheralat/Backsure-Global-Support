<?php
/**
 * Settings Test (Simple Version)
 * Tests the settings system functionality with minimal dependencies
 */

// Error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Define constant to prevent direct access
define('ADMIN_PANEL', true);

// Simple output function
function output_message($message, $type = 'info') {
    echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ccc; border-radius: 4px; background-color: ";
    
    switch ($type) {
        case 'success':
            echo "#d4edda; color: #155724";
            break;
        case 'error':
            echo "#f8d7da; color: #721c24";
            break;
        case 'warning':
            echo "#fff3cd; color: #856404";
            break;
        default:
            echo "#d1ecf1; color: #0c5460";
    }
    
    echo ";'>{$message}</div>";
}

// Check if required files exist
$required_files = [
    'settings-functions.php',
    'admin-seo.php',
    'admin-integrations.php',
    'admin-chat-settings.php',
    'admin-settings.php',
    'admin-notification-settings.php',
    'media-library.php'
];

$missing_files = [];
foreach ($required_files as $file) {
    if (!file_exists($file)) {
        $missing_files[] = $file;
    }
}

// Initial HTML
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .btn-secondary {
            background-color: #95a5a6;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        pre {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Settings System Test</h1>
        
        <?php if (!empty($missing_files)): ?>
        <div style="margin: 20px 0; padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;">
            <h3>Missing Files</h3>
            <p>The following required files are missing:</p>
            <ul>
                <?php foreach ($missing_files as $file): ?>
                <li><?php echo $file; ?></li>
                <?php endforeach; ?>
            </ul>
            <p>Please ensure all files are uploaded to the correct location.</p>
        </div>
        <?php else: ?>
        <div style="margin: 20px 0; padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;">
            <p>All required files are present.</p>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>1. Database Connection Test</h2>
            <?php
            // Simple database connection test
            try {
                // Define database connection here
                $db_host = 'localhost'; // Change as needed
                $db_name = 'your_database'; // Change to your database name
                $db_user = 'your_username'; // Change to your database username
                $db_pass = 'your_password'; // Change to your database password
                
                // Create connection with error checking
                $db = new mysqli($db_host, $db_user, $db_pass, $db_name);
                
                // Check connection
                if ($db->connect_error) {
                    throw new Exception("Connection failed: " . $db->connect_error);
                }
                
                output_message("Database connection successful!", "success");
                
                // Check if settings table exists
                $result = $db->query("SHOW TABLES LIKE 'settings'");
                if ($result && $result->num_rows > 0) {
                    output_message("Settings table exists in the database.", "success");
                    
                    // Get table structure
                    $result = $db->query("DESCRIBE settings");
                    if ($result && $result->num_rows > 0) {
                        echo "<h3>Settings Table Structure:</h3>";
                        echo "<table>";
                        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
                        
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['Field'] . "</td>";
                            echo "<td>" . $row['Type'] . "</td>";
                            echo "<td>" . $row['Null'] . "</td>";
                            echo "<td>" . $row['Key'] . "</td>";
                            echo "<td>" . ($row['Default'] !== null ? $row['Default'] : 'NULL') . "</td>";
                            echo "<td>" . $row['Extra'] . "</td>";
                            echo "</tr>";
                        }
                        
                        echo "</table>";
                    } else {
                        output_message("Could not retrieve settings table structure.", "error");
                    }
                    
                    // Count settings
                    $result = $db->query("SELECT COUNT(*) as count FROM settings");
                    if ($result && $row = $result->fetch_assoc()) {
                        output_message("Total settings in the database: " . $row['count'], "info");
                    }
                    
                    // Show sample settings
                    $result = $db->query("SELECT setting_group, setting_key, setting_value, type FROM settings LIMIT 5");
                    if ($result && $result->num_rows > 0) {
                        echo "<h3>Sample Settings:</h3>";
                        echo "<table>";
                        echo "<tr><th>Group</th><th>Key</th><th>Value</th><th>Type</th></tr>";
                        
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['setting_group'] . "</td>";
                            echo "<td>" . $row['setting_key'] . "</td>";
                            echo "<td>" . (strlen($row['setting_value']) > 50 ? substr($row['setting_value'], 0, 50) . '...' : $row['setting_value']) . "</td>";
                            echo "<td>" . $row['type'] . "</td>";
                            echo "</tr>";
                        }
                        
                        echo "</table>";
                    }
                } else {
                    output_message("Settings table does not exist in the database. Have you run the database setup script?", "error");
                }
                
                // Check other required tables
                $required_tables = ['chat_sessions', 'chat_logs'];
                foreach ($required_tables as $table) {
                    $result = $db->query("SHOW TABLES LIKE '{$table}'");
                    if ($result && $result->num_rows > 0) {
                        output_message("{$table} table exists in the database.", "success");
                    } else {
                        output_message("{$table} table does not exist in the database.", "warning");
                    }
                }
                
            } catch (Exception $e) {
                output_message("Database Error: " . $e->getMessage(), "error");
            }
            ?>
        </div>
        
        <div class="card">
            <h2>2. File System Test</h2>
            <?php
            // Check upload directory
            $upload_dir = __DIR__ . '/uploads/';
            if (!defined('UPLOAD_DIR')) {
                define('UPLOAD_DIR', $upload_dir);
            }
            
            if (is_dir(UPLOAD_DIR)) {
                output_message("Upload directory exists: " . UPLOAD_DIR, "success");
                
                // Check permissions
                if (is_writable(UPLOAD_DIR)) {
                    output_message("Upload directory is writable.", "success");
                } else {
                    output_message("Upload directory is not writable. Please set the correct permissions (755 for directories).", "error");
                }
                
                // Check media subdirectory
                $media_dir = UPLOAD_DIR . 'media/';
                if (is_dir($media_dir)) {
                    output_message("Media directory exists: " . $media_dir, "success");
                    
                    if (is_writable($media_dir)) {
                        output_message("Media directory is writable.", "success");
                    } else {
                        output_message("Media directory is not writable. Please set the correct permissions (755 for directories).", "error");
                    }
                } else {
                    output_message("Media directory does not exist: " . $media_dir . ". Please create it.", "warning");
                }
            } else {
                output_message("Upload directory does not exist: " . UPLOAD_DIR . ". Please create it.", "error");
            }
            
            // Check PHP limits
            echo "<h3>PHP Settings:</h3>";
            echo "<ul>";
            echo "<li>max_execution_time: " . ini_get('max_execution_time') . " seconds</li>";
            echo "<li>memory_limit: " . ini_get('memory_limit') . "</li>";
            echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
            echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
            echo "</ul>";
            
            // Check extensions
            $required_extensions = ['mysqli', 'gd', 'fileinfo', 'json'];
            $missing_extensions = [];
            
            foreach ($required_extensions as $ext) {
                if (!extension_loaded($ext)) {
                    $missing_extensions[] = $ext;
                }
            }
            
            if (empty($missing_extensions)) {
                output_message("All required PHP extensions are loaded.", "success");
            } else {
                output_message("Missing required PHP extensions: " . implode(', ', $missing_extensions), "error");
            }
            ?>
        </div>
        
        <div class="card">
            <h2>3. Environment Information</h2>
            <?php
            echo "<ul>";
            echo "<li>PHP Version: " . phpversion() . "</li>";
            echo "<li>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
            echo "<li>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
            echo "<li>Current Script Path: " . __FILE__ . "</li>";
            echo "</ul>";
            ?>
        </div>
        
        <div style="margin: 20px 0;">
            <a href="admin-dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
