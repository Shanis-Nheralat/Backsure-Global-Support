<?php
/**
 * Settings Test Adapter (Improved)
 * Works with your existing database configuration
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Define constant to prevent direct access error
define('ADMIN_PANEL', true);

// Load configuration (will try to find your existing db connection)
require_once 'config-loader.php';

// Function to output a message with styling
function display_message($message, $type = 'info') {
    $bg_color = '#d1ecf1';
    $text_color = '#0c5460';
    
    switch ($type) {
        case 'success':
            $bg_color = '#d4edda';
            $text_color = '#155724';
            break;
        case 'warning':
            $bg_color = '#fff3cd';
            $text_color = '#856404';
            break;
        case 'error':
            $bg_color = '#f8d7da';
            $text_color = '#721c24';
            break;
    }
    
    echo "<div style='margin: 10px 0; padding: 15px; border-radius: 4px; background-color: {$bg_color}; color: {$text_color};'>{$message}</div>";
}

// Function to create the upload directories
function create_upload_directories() {
    $dirs = [
        UPLOAD_DIR,
        UPLOAD_DIR . 'media/'
    ];
    
    $results = [];
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            $created = mkdir($dir, 0755, true);
            $results[] = [
                'dir' => $dir,
                'created' => $created,
                'writable' => is_dir($dir) && is_writable($dir)
            ];
        } else {
            $results[] = [
                'dir' => $dir,
                'exists' => true,
                'writable' => is_writable($dir)
            ];
        }
    }
    
    return $results;
}

// Check if we need to create a custom database connection
$db_connected = false;
$connection_message = "";

if (!isset($db) || !is_object($db)) {
    // We need the user to provide database credentials
    if ($_POST && isset($_POST['action']) && $_POST['action'] === 'connect_db') {
        $db_host = $_POST['db_host'];
        $db_user = $_POST['db_user'];
        $db_pass = $_POST['db_pass'];
        $db_name = $_POST['db_name'];
        
        // Try to connect
        try {
            $db = new mysqli($db_host, $db_user, $db_pass, $db_name);
            
            if ($db->connect_error) {
                $connection_message = "Connection failed: " . $db->connect_error;
            } else {
                $db_connected = true;
                $connection_message = "Connection successful!";
                
                // Save connection to session for future page loads
                session_start();
                $_SESSION['db_credentials'] = [
                    'host' => $db_host,
                    'user' => $db_user,
                    'pass' => $db_pass,
                    'name' => $db_name
                ];
            }
        } catch (Exception $e) {
            $connection_message = "Connection error: " . $e->getMessage();
        }
    } else {
        // Check if we have credentials in session
        session_start();
        if (isset($_SESSION['db_credentials'])) {
            $creds = $_SESSION['db_credentials'];
            try {
                $db = new mysqli($creds['host'], $creds['user'], $creds['pass'], $creds['name']);
                if (!$db->connect_error) {
                    $db_connected = true;
                    $connection_message = "Connected using saved credentials.";
                }
            } catch (Exception $e) {
                // Ignore, we'll just show the connection form
            }
        }
    }
} else {
    // We already have a database connection from config-loader.php
    $db_connected = true;
    $connection_message = "Using existing database connection from your configuration.";
}

// Create upload directories
$dir_results = create_upload_directories();

// Process form submission
$creation_result = null;
$test_result = null;

if ($db_connected && $_POST && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create_tables':
            // Include settings functions
            if (!function_exists('create_settings_table_if_needed')) {
                require_once 'settings-functions.php';
            }
            
            // Create tables
            $settings_table = create_settings_table_if_needed();
            $chatbot_tables = create_chatbot_tables_if_needed();
            
            if ($settings_table && $chatbot_tables) {
                $creation_result = [
                    'success' => true,
                    'message' => 'Database tables created successfully!'
                ];
            } else {
                $creation_result = [
                    'success' => false,
                    'message' => 'Error creating database tables. Check database permissions and structure.'
                ];
            }
            break;
            
        case 'insert_defaults':
            // Include settings functions
            if (!function_exists('insert_default_settings')) {
                require_once 'settings-functions.php';
            }
            
            // Insert default settings
            $result = insert_default_settings();
            
            if ($result) {
                $creation_result = [
                    'success' => true,
                    'message' => 'Default settings inserted successfully!'
                ];
            } else {
                $creation_result = [
                    'success' => false,
                    'message' => 'Error inserting default settings.'
                ];
            }
            break;
            
        case 'test_setting':
            // Include settings functions
            if (!function_exists('set_setting')) {
                require_once 'settings-functions.php';
            }
            
            // Test setting functions
            $test_group = 'test_group';
            $test_key = 'test_key';
            $test_value = 'test_value_' . time(); // Unique value
            
            // Set a test setting
            $set_result = set_setting($test_group, $test_key, $test_value);
            
            // Get the test setting
            $get_result = get_setting($test_group, $test_key);
            
            if ($set_result && $get_result === $test_value) {
                $test_result = [
                    'success' => true,
                    'message' => 'Setting functions working correctly!',
                    'details' => "Successfully set and retrieved test value: {$test_value}"
                ];
            } else {
                $test_result = [
                    'success' => false,
                    'message' => 'Error with setting functions.',
                    'details' => "Set result: " . ($set_result ? 'Success' : 'Failed') . ", Get result: {$get_result}"
                ];
            }
            break;
    }
}

// Check if settings table exists
$settings_table_exists = false;
$settings_count = 0;

if ($db_connected) {
    try {
        $result = $db->query("SHOW TABLES LIKE 'settings'");
        $settings_table_exists = ($result && $result->num_rows > 0);
        
        if ($settings_table_exists) {
            $result = $db->query("SELECT COUNT(*) as count FROM settings");
            if ($result && $row = $result->fetch_assoc()) {
                $settings_count = $row['count'];
            }
        }
    } catch (Exception $e) {
        // Ignore errors
    }

    // Check if chat tables exist
    $chat_sessions_exists = false;
    $chat_logs_exists = false;

    try {
        $result = $db->query("SHOW TABLES LIKE 'chat_sessions'");
        $chat_sessions_exists = ($result && $result->num_rows > 0);
        
        $result = $db->query("SHOW TABLES LIKE 'chat_logs'");
        $chat_logs_exists = ($result && $result->num_rows > 0);
    } catch (Exception $e) {
        // Ignore errors
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings System Adapter</title>
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
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .btn-success {
            background-color: #2ecc71;
        }
        .btn-success:hover {
            background-color: #27ae60;
        }
        .btn-warning {
            background-color: #f39c12;
        }
        .btn-warning:hover {
            background-color: #e67e22;
        }
        .btn-danger {
            background-color: #e74c3c;
        }
        .btn-danger:hover {
            background-color: #c0392b;
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
        .indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .indicator-success {
            background-color: #2ecc71;
        }
        .indicator-warning {
            background-color: #f39c12;
        }
        .indicator-danger {
            background-color: #e74c3c;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Settings System Adapter</h1>
        <p>This tool will help you set up the Settings System to work with your existing database.</p>
        
        <?php if ($connection_message): ?>
            <?php display_message($connection_message, $db_connected ? 'success' : 'error'); ?>
        <?php endif; ?>
        
        <?php if ($creation_result): ?>
            <?php display_message($creation_result['message'], $creation_result['success'] ? 'success' : 'error'); ?>
        <?php endif; ?>
        
        <?php if ($test_result): ?>
            <?php display_message($test_result['message'], $test_result['success'] ? 'success' : 'error'); ?>
            <?php if (isset($test_result['details'])): ?>
                <pre><?php echo $test_result['details']; ?></pre>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if (!$db_connected): ?>
        <div class="card">
            <h2>1. Database Connection</h2>
            <p>We couldn't find an existing database connection. Please provide your database credentials:</p>
            
            <form method="post">
                <input type="hidden" name="action" value="connect_db">
                
                <div class="form-group">
                    <label for="db_host">Database Host:</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                </div>
                
                <div class="form-group">
                    <label for="db_user">Database Username:</label>
                    <input type="text" id="db_user" name="db_user" required>
                </div>
                
                <div class="form-group">
                    <label for="db_pass">Database Password:</label>
                    <input type="password" id="db_pass" name="db_pass" required>
                </div>
                
                <div class="form-group">
                    <label for="db_name">Database Name:</label>
                    <input type="text" id="db_name" name="db_name" required>
                </div>
                
                <button type="submit" class="btn btn-success">Connect to Database</button>
            </form>
        </div>
        <?php else: ?>
        <div class="card">
            <h2>1. Database Connection</h2>
            <?php display_message("Database connection successful!", 'success'); ?>
        </div>
        
        <div class="card">
            <h2>2. Upload Directories</h2>
            <table>
                <tr>
                    <th>Directory</th>
                    <th>Status</th>
                    <th>Permissions</th>
                </tr>
                <?php foreach ($dir_results as $result): ?>
                    <tr>
                        <td><?php echo $result['dir']; ?></td>
                        <td>
                            <?php if (isset($result['exists']) && $result['exists']): ?>
                                <span class="indicator indicator-success"></span> Exists
                            <?php elseif (isset($result['created']) && $result['created']): ?>
                                <span class="indicator indicator-success"></span> Created
                            <?php else: ?>
                                <span class="indicator indicator-danger"></span> Failed to create
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($result['writable']): ?>
                                <span class="indicator indicator-success"></span> Writable
                            <?php else: ?>
                                <span class="indicator indicator-danger"></span> Not writable
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        
        <div class="card">
            <h2>3. Database Tables</h2>
            <table>
                <tr>
                    <th>Table</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <tr>
                    <td>settings</td>
                    <td>
                        <?php if ($settings_table_exists): ?>
                            <span class="indicator indicator-success"></span> Exists (<?php echo $settings_count; ?> settings)
                        <?php else: ?>
                            <span class="indicator indicator-danger"></span> Missing
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!$settings_table_exists): ?>
                            <form method="post">
                                <input type="hidden" name="action" value="create_tables">
                                <button type="submit" class="btn btn-success">Create Tables</button>
                            </form>
                        <?php elseif ($settings_table_exists && $settings_count == 0): ?>
                            <form method="post">
                                <input type="hidden" name="action" value="insert_defaults">
                                <button type="submit" class="btn btn-warning">Insert Default Settings</button>
                            </form>
                        <?php else: ?>
                            <span class="indicator indicator-success"></span> Ready
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>chat_sessions</td>
                    <td>
                        <?php if ($chat_sessions_exists): ?>
                            <span class="indicator indicator-success"></span> Exists
                        <?php else: ?>
                            <span class="indicator indicator-warning"></span> Missing
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!$chat_sessions_exists): ?>
                            <span class="indicator indicator-warning"></span> Will be created with settings table
                        <?php else: ?>
                            <span class="indicator indicator-success"></span> Ready
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>chat_logs</td>
                    <td>
                        <?php if ($chat_logs_exists): ?>
                            <span class="indicator indicator-success"></span> Exists
                        <?php else: ?>
                            <span class="indicator indicator-warning"></span> Missing
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!$chat_logs_exists): ?>
                            <span class="indicator indicator-warning"></span> Will be created with settings table
                        <?php else: ?>
                            <span class="indicator indicator-success"></span> Ready
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="card">
            <h2>4. Test Settings Functions</h2>
            <?php if ($settings_table_exists): ?>
                <form method="post">
                    <input type="hidden" name="action" value="test_setting">
                    <button type="submit" class="btn btn-success">Test Setting Functions</button>
                </form>
            <?php else: ?>
                <p>Please create the settings table first before testing the functions.</p>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>5. Next Steps</h2>
            <p>Once all the checks above show success, you are ready to use the Settings System.</p>
            <p>Make sure to:</p>
            <ol>
                <li>Update database credentials in your config file</li>
                <li>Define the UPLOAD_DIR and UPLOAD_URL constants in your config file</li>
                <li>Include settings-functions.php in your admin initialization</li>
            </ol>
            
            <div class="buttons">
                <a href="admin-seo.php" class="btn">SEO Settings</a>
                <a href="admin-integrations.php" class="btn">Integrations</a>
                <a href="admin-chat-settings.php" class="btn">Chatbot Settings</a>
                <a href="admin-settings.php" class="btn">Site Settings</a>
                <a href="admin-notification-settings.php" class="btn">Notification Settings</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
