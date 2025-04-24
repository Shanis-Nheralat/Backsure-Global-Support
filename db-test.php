<?php
// ====================================================================
// DATABASE CONNECTION TEST SCRIPT
// ====================================================================
// Place this file in your public_html directory and access it via browser
// Delete after testing for security reasons
// ====================================================================

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Set proper content type
header('Content-Type: text/html; charset=utf-8');

// Start output buffering to prevent "headers already sent" errors
ob_start();

// Simple HTML header
echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1 { color: #333; }
        h2 { color: #555; margin-top: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow: auto; }
        .container { max-width: 900px; margin: 0 auto; }
        table { border-collapse: collapse; width: 100%; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<div class='container'>
    <h1>Database Connection Test</h1>";

// Function to display results
function showResult($test, $result, $message = '', $type = 'success') {
    echo "<p><strong>$test:</strong> <span class='$type'>$result</span>";
    if (!empty($message)) {
        echo " - $message";
    }
    echo "</p>";
}

// Function to check if file exists and is readable
function checkFile($filepath) {
    if (file_exists($filepath)) {
        if (is_readable($filepath)) {
            return array(true, "File exists and is readable");
        } else {
            return array(false, "File exists but is not readable");
        }
    } else {
        return array(false, "File does not exist");
    }
}

// ====================================================================
// 1. Check for db_config.php file
// ====================================================================
echo "<h2>1. Database Configuration File</h2>";

$configPath = __DIR__ . '/db_config.php';
list($configExists, $configMessage) = checkFile($configPath);

if ($configExists) {
    showResult("Configuration file", "FOUND", $configMessage);
    
    // Try to include the file
    try {
        include_once($configPath);
        showResult("Include config file", "SUCCESS", "File was included without errors");
    } catch (Exception $e) {
        showResult("Include config file", "FAILED", "Error: " . $e->getMessage(), "error");
    }
} else {
    showResult("Configuration file", "NOT FOUND", $configMessage, "error");
    echo "<p>Expected config file at: $configPath</p>";
}

// ====================================================================
// 2. Check for database connection variables
// ====================================================================
echo "<h2>2. Database Connection Variables</h2>";

// This assumes your db_config.php uses standard variable names
// Adjust these if your configuration uses different variable names
$expected_vars = array(
    'db_host' => 'Database host',
    'db_name' => 'Database name',
    'db_user' => 'Database username',
    'db_password' => 'Database password'
);

// Try to detect common variable names
$possible_vars = array(
    'host' => array('db_host', 'dbhost', 'hostname', 'DB_HOST', 'host', 'server', 'db_server'),
    'name' => array('db_name', 'dbname', 'database', 'DB_NAME', 'db_database', 'database_name'),
    'user' => array('db_user', 'dbuser', 'username', 'DB_USER', 'user', 'db_username'),
    'pass' => array('db_password', 'dbpassword', 'password', 'DB_PASSWORD', 'pass', 'db_pass')
);

// Check if the expected variables exist
$missing_vars = false;
$actual_vars = array();

echo "<table>
    <tr>
        <th>Variable Type</th>
        <th>Status</th>
        <th>Variable Name Found</th>
        <th>Value (Partially Hidden)</th>
    </tr>";

foreach ($possible_vars as $type => $var_names) {
    $found = false;
    $var_name = '';
    $var_value = '';
    
    foreach ($var_names as $name) {
        if (isset($$name)) {
            $found = true;
            $var_name = $name;
            $var_value = $$name;
            $actual_vars[$type] = $var_value;
            break;
        }
    }
    
    echo "<tr>";
    echo "<td>{$expected_vars['db_' . $type]}</td>";
    
    if ($found) {
        echo "<td class='success'>FOUND</td>";
        echo "<td>$var_name</td>";
        
        // Obfuscate sensitive data
        if ($type == 'pass') {
            $hidden_value = strlen($var_value) > 0 ? str_repeat('*', strlen($var_value) - 2) . substr($var_value, -2) : '';
            echo "<td>$hidden_value</td>";
        } else if ($type == 'user') {
            $hidden_value = strlen($var_value) > 3 ? substr($var_value, 0, 3) . str_repeat('*', strlen($var_value) - 3) : $var_value;
            echo "<td>$hidden_value</td>";
        } else {
            echo "<td>$var_value</td>";
        }
    } else {
        $missing_vars = true;
        echo "<td class='error'>NOT FOUND</td>";
        echo "<td>None of: " . implode(', ', $var_names) . "</td>";
        echo "<td>N/A</td>";
    }
    
    echo "</tr>";
}

echo "</table>";

if ($missing_vars) {
    echo "<p class='warning'>Warning: Some expected database variables were not found. This may cause connection issues.</p>";
    
    // Try to extract variables from the config file content
    echo "<h3>Config File Analysis</h3>";
    echo "<p>Analyzing the config file for potential database variables:</p>";
    
    if ($configExists) {
        $config_content = file_get_contents($configPath);
        echo "<pre>";
        
        // Look for common database connection patterns
        if (preg_match_all('/\$([\w]+)\s*=\s*[\'"]([^\'"]*)[\'"]/', $config_content, $matches)) {
            echo "Found variable assignments:\n";
            for ($i = 0; $i < count($matches[0]); $i++) {
                $var = $matches[1][$i];
                $value = $matches[2][$i];
                
                // Hide passwords
                if (stripos($var, 'pass') !== false) {
                    $value = str_repeat('*', strlen($value));
                }
                
                echo "\${$var} = \"{$value}\"\n";
            }
        } else {
            echo "No simple variable assignments found.";
        }
        
        // Look for PDO or mysqli connection strings
        if (preg_match('/new\s+PDO\s*\(\s*[\'"]mysql:host=([^;]+);dbname=([^\'"]+)[\'"]/', $config_content, $matches)) {
            echo "\nFound PDO connection:\n";
            echo "Host: {$matches[1]}\n";
            echo "Database: {$matches[2]}\n";
        }
        
        if (preg_match('/mysqli_connect\s*\(\s*[\'"]([^\'"]+)[\'"],\s*[\'"]([^\'"]+)[\'"]/', $config_content, $matches)) {
            echo "\nFound MySQLi connection:\n";
            echo "Host: {$matches[1]}\n";
            echo "User: {$matches[2]}\n";
        }
        
        echo "</pre>";
    }
}

// ====================================================================
// 3. Test the database connection
// ====================================================================
echo "<h2>3. Database Connection Test</h2>";

$connection = false;
$connection_error = '';

// Try to detect which connection method is used in your application
if (isset($actual_vars['host']) && isset($actual_vars['name']) && isset($actual_vars['user'])) {
    // We have the necessary variables, attempt connection
    try {
        // Try MySQLi first
        $conn = new mysqli($actual_vars['host'], $actual_vars['user'], $actual_vars['pass'], $actual_vars['name']);
        
        if ($conn->connect_error) {
            $connection_error = "MySQLi Error: " . $conn->connect_error;
            
            // If MySQLi fails, try PDO
            try {
                $dsn = "mysql:host={$actual_vars['host']};dbname={$actual_vars['name']}";
                $pdo = new PDO($dsn, $actual_vars['user'], $actual_vars['pass']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $connection = true;
                showResult("Database connection (PDO)", "SUCCESS", "Connected successfully to {$actual_vars['name']} on {$actual_vars['host']}");
                
                // Use PDO for subsequent tests
                $connectionType = 'PDO';
                
            } catch (PDOException $e) {
                $connection_error .= "<br>PDO Error: " . $e->getMessage();
                showResult("Database connection", "FAILED", $connection_error, "error");
            }
        } else {
            $connection = true;
            showResult("Database connection (MySQLi)", "SUCCESS", "Connected successfully to {$actual_vars['name']} on {$actual_vars['host']}");
            
            // Use MySQLi for subsequent tests
            $connectionType = 'MySQLi';
        }
    } catch (Exception $e) {
        $connection_error = "Error: " . $e->getMessage();
        showResult("Database connection", "FAILED", $connection_error, "error");
    }
} else {
    showResult("Database connection", "SKIPPED", "Missing required connection variables", "warning");
}

// ====================================================================
// 4. Test a sample query
// ====================================================================
echo "<h2>4. Basic Query Test</h2>";

if ($connection) {
    try {
        if ($connectionType == 'PDO') {
            // Using PDO
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            showResult("Query execution", "SUCCESS", "Retrieved " . count($tables) . " tables");
            
            if (count($tables) > 0) {
                echo "<p>Tables in database:</p>";
                echo "<ul>";
                foreach ($tables as $table) {
                    echo "<li>$table</li>";
                }
                echo "</ul>";
                
                // Check for user/admin related tables
                $userTables = array();
                foreach ($tables as $table) {
                    if (stripos($table, 'user') !== false || 
                        stripos($table, 'admin') !== false || 
                        stripos($table, 'account') !== false ||
                        stripos($table, 'auth') !== false) {
                        $userTables[] = $table;
                    }
                }
                
                if (count($userTables) > 0) {
                    echo "<p>Potential user/authentication related tables:</p>";
                    echo "<ul>";
                    foreach ($userTables as $table) {
                        echo "<li>$table</li>";
                    }
                    echo "</ul>";
                    
                    // Examine structure of the first user table
                    $userTable = $userTables[0];
                    $stmt = $pdo->query("DESCRIBE `$userTable`");
                    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo "<p>Structure of '$userTable' table:</p>";
                    echo "<table>";
                    echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
                    foreach ($columns as $column) {
                        echo "<tr>";
                        echo "<td>{$column['Field']}</td>";
                        echo "<td>{$column['Type']}</td>";
                        echo "<td>{$column['Key']}</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            }
        } else {
            // Using MySQLi
            $result = $conn->query("SHOW TABLES");
            $tables = array();
            while ($row = $result->fetch_array()) {
                $tables[] = $row[0];
            }
            
            showResult("Query execution", "SUCCESS", "Retrieved " . count($tables) . " tables");
            
            if (count($tables) > 0) {
                echo "<p>Tables in database:</p>";
                echo "<ul>";
                foreach ($tables as $table) {
                    echo "<li>$table</li>";
                }
                echo "</ul>";
                
                // Check for user/admin related tables
                $userTables = array();
                foreach ($tables as $table) {
                    if (stripos($table, 'user') !== false || 
                        stripos($table, 'admin') !== false || 
                        stripos($table, 'account') !== false ||
                        stripos($table, 'auth') !== false) {
                        $userTables[] = $table;
                    }
                }
                
                if (count($userTables) > 0) {
                    echo "<p>Potential user/authentication related tables:</p>";
                    echo "<ul>";
                    foreach ($userTables as $table) {
                        echo "<li>$table</li>";
                    }
                    echo "</ul>";
                    
                    // Examine structure of the first user table
                    $userTable = $userTables[0];
                    $result = $conn->query("DESCRIBE `$userTable`");
                    
                    echo "<p>Structure of '$userTable' table:</p>";
                    echo "<table>";
                    echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['Field']}</td>";
                        echo "<td>{$row['Type']}</td>";
                        echo "<td>{$row['Key']}</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            }
        }
    } catch (Exception $e) {
        showResult("Query execution", "FAILED", "Error: " . $e->getMessage(), "error");
    }
} else {
    showResult("Query execution", "SKIPPED", "No database connection available", "warning");
}

// ====================================================================
// 5. Login query simulation
// ====================================================================
echo "<h2>5. Login Query Simulation</h2>";

if ($connection && isset($userTables) && count($userTables) > 0) {
    $userTable = $userTables[0];
    
    // Find username and password columns
    $usernameCol = 'username';
    $passwordCol = 'password';
    $idCol = 'id';
    
    foreach ($columns as $column) {
        $field = strtolower($column['Field']);
        
        if ($field == 'username' || $field == 'user' || $field == 'email' || $field == 'login') {
            $usernameCol = $column['Field'];
        }
        
        if ($field == 'password' || $field == 'pass' || $field == 'passwd') {
            $passwordCol = $column['Field'];
        }
        
        if ($field == 'id' || $field == 'user_id' || $field == 'userid') {
            $idCol = $column['Field'];
        }
    }
    
    echo "<p>Based on table structure, a login query would likely use:</p>";
    echo "<ul>";
    echo "<li>Table: <code>$userTable</code></li>";
    echo "<li>Username column: <code>$usernameCol</code></li>";
    echo "<li>Password column: <code>$passwordCol</code></li>";
    echo "<li>ID column: <code>$idCol</code></li>";
    echo "</ul>";
    
    echo "<p>A typical login query would look like:</p>";
    echo "<pre>
SELECT * FROM `$userTable` 
WHERE `$usernameCol` = ? 
AND `$passwordCol` = ?</pre>";

    echo "<p>A sample login verification code in PHP would be:</p>";
    echo "<pre>
// Assuming form submission with username and password
\$username = \$_POST['username'];
\$password = \$_POST['password']; // Or hash: password_hash(\$_POST['password'], PASSWORD_DEFAULT)

// Using PDO
\$stmt = \$pdo->prepare(\"SELECT * FROM `$userTable` WHERE `$usernameCol` = ?\");
\$stmt->execute([\$username]);
\$user = \$stmt->fetch(PDO::FETCH_ASSOC);

if (\$user) {
    // For plain text passwords (not recommended)
    if (\$user['$passwordCol'] === \$password) {
        \$_SESSION['user_id'] = \$user['$idCol'];
        // Successful login, redirect
        header('Location: dashboard.php');
        exit;
    }
    
    // OR for hashed passwords (recommended)
    if (password_verify(\$password, \$user['$passwordCol'])) {
        \$_SESSION['user_id'] = \$user['$idCol'];
        // Successful login, redirect
        header('Location: dashboard.php');
        exit;
    }
}

// Invalid login
\$_SESSION['error'] = 'Invalid username or password';
header('Location: login.php');
exit;</pre>";

    // Try to check if the password column is likely hashed
    try {
        if ($connectionType == 'PDO') {
            $stmt = $pdo->query("SELECT `$passwordCol` FROM `$userTable` LIMIT 1");
            $passwordSample = $stmt->fetchColumn();
        } else {
            $result = $conn->query("SELECT `$passwordCol` FROM `$userTable` LIMIT 1");
            $row = $result->fetch_assoc();
            $passwordSample = $row[$passwordCol];
        }
        
        $isHashed = (strlen($passwordSample) > 20);
        $hashType = "Unknown";
        
        if ($isHashed) {
            if (strlen($passwordSample) == 32) {
                $hashType = "MD5 (not secure)";
            } elseif (strlen($passwordSample) == 40) {
                $hashType = "SHA1 (not secure)";
            } elseif (strlen($passwordSample) == 60 && substr($passwordSample, 0, 4) == '$2y$') {
                $hashType = "bcrypt (secure)";
            } elseif (strlen($passwordSample) == 255 && substr($passwordSample, 0, 7) == '$argon2') {
                $hashType = "Argon2 (secure)";
            }
            
            showResult("Password storage", "HASHED", "Looks like passwords are hashed using $hashType", "success");
        } else {
            showResult("Password storage", "PLAIN TEXT", "Passwords appear to be stored as plain text (security risk!)", "error");
        }
    } catch (Exception $e) {
        showResult("Password check", "FAILED", "Error examining password column: " . $e->getMessage(), "warning");
    }
} else {
    showResult("Login simulation", "SKIPPED", "No user tables found or no database connection", "warning");
}

// ====================================================================
// 6. Session configuration check
// ====================================================================
echo "<h2>6. Session Configuration</h2>";

echo "<table>";
echo "<tr><th>Setting</th><th>Value</th><th>Notes</th></tr>";

$sessionSettings = array(
    'session.save_path' => 'Where session files are stored',
    'session.use_cookies' => 'Whether cookies are used for sessions',
    'session.use_only_cookies' => 'Whether only cookies are used for sessions',
    'session.use_trans_sid' => 'Whether transparent SID support is enabled',
    'session.cookie_httponly' => 'Whether HttpOnly flag is set for session cookies',
    'session.cookie_secure' => 'Whether Secure flag is set for session cookies',
    'session.cookie_lifetime' => 'Lifetime of session cookies in seconds',
    'session.gc_maxlifetime' => 'Garbage collection max lifetime'
);

foreach ($sessionSettings as $setting => $description) {
    $value = ini_get($setting);
    $notes = '';
    $class = '';
    
    // Add notes for certain settings
    if ($setting == 'session.save_path' && empty($value)) {
        $notes = 'Empty path may cause session issues';
        $class = 'warning';
    } elseif ($setting == 'session.save_path') {
        if (is_dir($value) && is_writable($value)) {
            $notes = 'Directory exists and is writable';
            $class = 'success';
        } else {
            $notes = 'Directory does not exist or is not writable';
            $class = 'error';
        }
    } elseif ($setting == 'session.use_only_cookies' && $value != 1) {
        $notes = 'Should be enabled for security';
        $class = 'warning';
    } elseif ($setting == 'session.use_trans_sid' && $value == 1) {
        $notes = 'Should be disabled for security';
        $class = 'warning';
    } elseif ($setting == 'session.cookie_httponly' && $value != 1) {
        $notes = 'Should be enabled for security';
        $class = 'warning';
    } elseif ($setting == 'session.cookie_secure' && $value != 1) {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $notes = 'Should be enabled on HTTPS sites';
            $class = 'warning';
        }
    }
    
    echo "<tr>";
    echo "<td>$setting</td>";
    echo "<td>$value</td>";
    echo "<td class='$class'>$notes</td>";
    echo "</tr>";
}

echo "</table>";

// ====================================================================
// 7. Recommendations
// ====================================================================
echo "<h2>7. Login System Recommendations</h2>";

echo "<ol>";
echo "<li>Make sure <code>session_start()</code> is called before any output in all admin pages</li>";
echo "<li>Use output buffering with <code>ob_start()</code> at the beginning of your PHP files</li>";
echo "<li>Ensure all PHP files have consistent line endings (Unix LF is recommended)</li>";

if (isset($passwordSample) && !$isHashed) {
    echo "<li class='error'><strong>SECURITY RISK:</strong> Use password hashing with password_hash() and password_verify()</li>";
}

if (ini_get('session.use_only_cookies') != 1 || ini_get('session.cookie_httponly') != 1) {
    echo "<li>Improve session security by setting appropriate PHP INI settings</li>";
}

echo "<li>Check for errors in login process by adding debug logging</li>";
echo "<li>Verify that session variables are correctly set and checked across pages</li>";
echo "</ol>";

// Close HTML
echo "</div>
</body>
</html>";

// End output buffering
ob_end_flush();
