<?php
/**
 * Advanced Database Diagnosis Script
 * This script performs multiple tests to determine exactly what's causing your database connection issue
 */

// Enable detailed error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Output as HTML with styling
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Advanced Database Connection Diagnosis</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1, h2, h3 { color: #333; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .section { margin-bottom: 30px; border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto; }
        table { border-collapse: collapse; width: 100%; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Advanced Database Connection Diagnosis</h1>
    <p>This tool performs multiple tests to identify database connection issues.</p>
    
    <div class="section">
        <h2>1. System Information</h2>
        <?php
        echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
        echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
        echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
        echo "<p><strong>Current Script Path:</strong> " . __FILE__ . "</p>";
        
        // Check PDO and mysqli extensions
        echo "<p><strong>PDO Installed:</strong> " . (extension_loaded('pdo') ? '<span class="success">Yes</span>' : '<span class="error">No</span>') . "</p>";
        echo "<p><strong>MySQLi Installed:</strong> " . (extension_loaded('mysqli') ? '<span class="success">Yes</span>' : '<span class="error">No</span>') . "</p>";
        
        if (extension_loaded('pdo')) {
            echo "<p><strong>PDO Drivers:</strong> ";
            $drivers = PDO::getAvailableDrivers();
            echo implode(', ', $drivers) . "</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>2. Configuration File Analysis</h2>
        <?php
        // Path to db.php file (adjust if needed)
        $db_file = __DIR__ . '/db.php';
        $db_config_file = __DIR__ . '/db_config.php';
        
        if (file_exists($db_file)) {
            echo "<p><strong>db.php:</strong> <span class='success'>File found</span></p>";
            echo "<p><strong>File permissions:</strong> " . substr(sprintf('%o', fileperms($db_file)), -4) . "</p>";
            echo "<p><strong>Last modified:</strong> " . date("Y-m-d H:i:s", filemtime($db_file)) . "</p>";
            
            // Extract credentials from db.php (safely, without executing)
            $db_content = file_get_contents($db_file);
            preg_match('/\$db_host\s*=\s*[\'"](.+?)[\'"]/', $db_content, $host_matches);
            preg_match('/\$db_name\s*=\s*[\'"](.+?)[\'"]/', $db_content, $name_matches);
            preg_match('/\$db_user\s*=\s*[\'"](.+?)[\'"]/', $db_content, $user_matches);
            
            echo "<p><strong>Host in db.php:</strong> " . (isset($host_matches[1]) ? $host_matches[1] : 'Not found') . "</p>";
            echo "<p><strong>Database name in db.php:</strong> " . (isset($name_matches[1]) ? $name_matches[1] : 'Not found') . "</p>";
            echo "<p><strong>Username in db.php:</strong> " . (isset($user_matches[1]) ? $user_matches[1] : 'Not found') . "</p>";
        } else {
            echo "<p><strong>db.php:</strong> <span class='error'>File not found at expected location</span></p>";
        }
        
        if (file_exists($db_config_file)) {
            echo "<p><strong>db_config.php:</strong> <span class='success'>File found</span></p>";
            echo "<p><strong>Last modified:</strong> " . date("Y-m-d H:i:s", filemtime($db_config_file)) . "</p>";
            
            // Extract credentials from db_config.php
            $db_config_content = file_get_contents($db_config_file);
            preg_match('/\$db_host\s*=\s*[\'"](.+?)[\'"]/', $db_config_content, $host_matches2);
            preg_match('/\$db_name\s*=\s*[\'"](.+?)[\'"]/', $db_config_content, $name_matches2);
            preg_match('/\$db_user\s*=\s*[\'"](.+?)[\'"]/', $db_config_content, $user_matches2);
            
            echo "<p><strong>Host in db_config.php:</strong> " . (isset($host_matches2[1]) ? $host_matches2[1] : 'Not found') . "</p>";
            echo "<p><strong>Database name in db_config.php:</strong> " . (isset($name_matches2[1]) ? $name_matches2[1] : 'Not found') . "</p>";
            echo "<p><strong>Username in db_config.php:</strong> " . (isset($user_matches2[1]) ? $user_matches2[1] : 'Not found') . "</p>";
        } else {
            echo "<p><strong>db_config.php:</strong> <span class='warning'>File not found at expected location</span></p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>3. Direct Database Connection Tests</h2>
        
        <h3>3.1 Testing Direct Connection with PDO</h3>
        <?php
        // Test with the credentials we extracted or use defaults
        $test_host = isset($host_matches[1]) ? $host_matches[1] : 'localhost';
        $test_name = isset($name_matches[1]) ? $name_matches[1] : 'backsure_admin';
        $test_user = isset($user_matches[1]) ? $user_matches[1] : 'shanis';
        $test_pass = 'Password123!';  // Example password, change as needed
        
        try {
            $start_time = microtime(true);
            $pdo = new PDO("mysql:host=$test_host", $test_user, $test_pass);
            $time_taken = round((microtime(true) - $start_time) * 1000, 2);
            
            echo "<p class='success'>✅ Connection to MySQL server successful ($time_taken ms)</p>";
            
            // Test specific database
            try {
                $start_time = microtime(true);
                $pdo2 = new PDO("mysql:host=$test_host;dbname=$test_name", $test_user, $test_pass);
                $time_taken = round((microtime(true) - $start_time) * 1000, 2);
                
                echo "<p class='success'>✅ Connection to database '$test_name' successful ($time_taken ms)</p>";
                
                // Check if we can run a simple query
                $stmt = $pdo2->query("SELECT database()");
                $result = $stmt->fetchColumn();
                echo "<p class='success'>✅ Query execution successful, connected to database: $result</p>";
                
                // List tables in database
                echo "<h4>Tables in $test_name database:</h4>";
                $tables = $pdo2->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                
                if (count($tables) > 0) {
                    echo "<ul>";
                    foreach ($tables as $table) {
                        echo "<li>$table</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p class='warning'>⚠️ No tables found in the database.</p>";
                }
                
            } catch (PDOException $e) {
                echo "<p class='error'>❌ Failed to connect to database '$test_name': " . $e->getMessage() . "</p>";
            }
            
        } catch (PDOException $e) {
            echo "<p class='error'>❌ Failed to connect to MySQL server: " . $e->getMessage() . "</p>";
        }
        ?>
        
        <h3>3.2 Testing Legacy MySQL Connection</h3>
        <?php
        // Test connection with mysqli
        try {
            $mysqli = new mysqli($test_host, $test_user, $test_pass, $test_name);
            
            if ($mysqli->connect_error) {
                echo "<p class='error'>❌ MySQLi connection failed: " . $mysqli->connect_error . "</p>";
            } else {
                echo "<p class='success'>✅ MySQLi connection successful</p>";
                
                // Check version
                $result = $mysqli->query("SELECT VERSION() as version");
                $row = $result->fetch_assoc();
                echo "<p>MySQL Server Version: " . $row['version'] . "</p>";
                
                $mysqli->close();
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ MySQLi connection exception: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>4. Database Server Information</h2>
        <?php
        // If we have a successful connection from above
        if (isset($pdo2) && $pdo2 instanceof PDO) {
            try {
                // Get database server info
                $server_info = $pdo2->getAttribute(PDO::ATTR_SERVER_VERSION);
                $client_info = $pdo2->getAttribute(PDO::ATTR_CLIENT_VERSION);
                $connection_status = $pdo2->getAttribute(PDO::ATTR_CONNECTION_STATUS);
                
                echo "<p><strong>Server Version:</strong> $server_info</p>";
                echo "<p><strong>Client Version:</strong> $client_info</p>";
                echo "<p><strong>Connection Status:</strong> $connection_status</p>";
                
                // Check server variables
                $stmt = $pdo2->query("SHOW VARIABLES LIKE 'max_connections'");
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "<p><strong>Max Connections:</strong> " . $row['Value'] . "</p>";
                
                $stmt = $pdo2->query("SHOW VARIABLES LIKE 'character_set%'");
                echo "<h4>Character Sets:</h4>";
                echo "<table>";
                echo "<tr><th>Variable</th><th>Value</th></tr>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr><td>" . $row['Variable_name'] . "</td><td>" . $row['Value'] . "</td></tr>";
                }
                echo "</table>";
            } catch (PDOException $e) {
                echo "<p class='error'>Error retrieving server information: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p class='warning'>Can't retrieve server information - no valid connection.</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>5. Checking Login Page Dependencies</h2>
        <?php
        // Check if login.php exists and includes db.php
        $login_file = __DIR__ . '/login.php';
        
        if (file_exists($login_file)) {
            echo "<p><strong>login.php:</strong> <span class='success'>File found</span></p>";
            echo "<p><strong>Last modified:</strong> " . date("Y-m-d H:i:s", filemtime($login_file)) . "</p>";
            
            // Check if login.php includes db.php
            $login_content = file_get_contents($login_file);
            
            if (strpos($login_content, 'require') !== false && strpos($login_content, 'db.php') !== false) {
                echo "<p class='success'>✅ login.php includes db.php</p>";
            } elseif (strpos($login_content, 'require') !== false && strpos($login_content, 'db_config.php') !== false) {
                echo "<p class='warning'>⚠️ login.php includes db_config.php instead of db.php</p>";
            } else {
                echo "<p class='warning'>⚠️ Could not confirm db.php inclusion in login.php</p>";
            }
            
            // Check admin-auth.php
            $admin_auth_file = __DIR__ . '/admin-auth.php';
            if (file_exists($admin_auth_file)) {
                echo "<p><strong>admin-auth.php:</strong> <span class='success'>File found</span></p>";
                
                $admin_auth_content = file_get_contents($admin_auth_file);
                if (strpos($admin_auth_content, 'require') !== false && strpos($admin_auth_content, 'db.php') !== false) {
                    echo "<p class='success'>✅ admin-auth.php includes db.php</p>";
                } elseif (strpos($admin_auth_content, 'require') !== false && strpos($admin_auth_content, 'db_config.php') !== false) {
                    echo "<p class='warning'>⚠️ admin-auth.php includes db_config.php instead of db.php</p>";
                }
            }
        } else {
            echo "<p><strong>login.php:</strong> <span class='error'>File not found at expected location</span></p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>6. Conclusion and Recommendations</h2>
        
        <h3>Summary of Findings:</h3>
        <ul>
            <?php
            // Generate conclusions based on the test results
            if (!extension_loaded('pdo') || !in_array('mysql', PDO::getAvailableDrivers())) {
                echo "<li class='error'>Missing required PHP extensions for database connection</li>";
            }
            
            if (!file_exists($db_file)) {
                echo "<li class='error'>db.php file not found at expected location</li>";
            }
            
            // Add more conclusions based on test results
            if (isset($pdo) && $pdo instanceof PDO && (!isset($pdo2) || !($pdo2 instanceof PDO))) {
                echo "<li class='warning'>Can connect to MySQL server but not to the specific database</li>";
            }
            
            if (isset($name_matches[1]) && isset($name_matches2[1]) && $name_matches[1] != $name_matches2[1]) {
                echo "<li class='warning'>Different database names in db.php vs db_config.php</li>";
            }
            
            if (isset($user_matches[1]) && strpos($user_matches[1], '@') !== false) {
                echo "<li class='error'>Username in db.php still contains @ symbol</li>";
            }
            ?>
        </ul>
        
        <h3>Recommendations:</h3>
        <ol>
            <?php
            // Generate recommendations based on the test results
            if (isset($user_matches[1]) && strpos($user_matches[1], '@') !== false) {
                echo "<li>Update db.php to use a simple username without @ symbol</li>";
            }
            
            if (isset($host_matches[1]) && isset($host_matches2[1]) && $host_matches[1] != $host_matches2[1]) {
                echo "<li>Ensure consistent host settings across config files</li>";
            }
            
            if (isset($name_matches[1]) && isset($name_matches2[1]) && $name_matches[1] != $name_matches2[1]) {
                echo "<li>Ensure consistent database name across config files</li>";
            }
            
            // Standard recommendations
            echo "<li>Verify MySQL user permissions (shanis@localhost should have ALL PRIVILEGES on backsure_admin.*)</li>";
            echo "<li>Ensure MySQL service is running and accepting connections</li>";
            echo "<li>Check for MySQL connection limits and timeouts</li>";
            
            if (file_exists($db_file) && file_exists($db_config_file)) {
                echo "<li>Consider removing one of the config files to avoid conflicts</li>";
            }
            ?>
        </ol>
    </div>
</body>
</html>
