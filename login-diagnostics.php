<?php
/**
 * Login System Diagnostic Tool
 * 
 * This file helps diagnose issues with the login system by checking:
 * - Database connections
 * - Session handling
 * - Authentication flow
 * - Redirect issues
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to test database connection with direct credentials
function test_direct_db_connection() {
    $db_host = 'localhost';
    $db_name = 'backzvsg_playground';
    $db_user = 'backzvsg_site';
    $db_pass = 'Pc*C^y]_ZnzU';
    
    try {
        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        $pdo = new PDO($dsn, $db_user, $db_pass, $options);
        return [
            'success' => true,
            'message' => 'Direct connection successful',
            'connection' => $pdo
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Direct connection failed: ' . $e->getMessage()
        ];
    }
}

// Function to test database connection through db_config.php
function test_db_config_connection() {
    try {
        if (!file_exists('db_config.php')) {
            return [
                'success' => false,
                'message' => 'db_config.php file not found'
            ];
        }
        
        // Include db_config.php
        require_once 'db_config.php';
        
        // Check if get_db_connection function exists
        if (!function_exists('get_db_connection')) {
            return [
                'success' => false,
                'message' => 'get_db_connection function not found in db_config.php'
            ];
        }
        
        // Try to get connection
        $db = get_db_connection();
        return [
            'success' => true,
            'message' => 'Connection via db_config.php successful',
            'connection' => $db
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Connection via db_config.php failed: ' . $e->getMessage()
        ];
    }
}

// Function to check config.php for root credentials
function check_config_for_root() {
    if (!file_exists('config.php')) {
        return [
            'success' => false,
            'message' => 'config.php file not found'
        ];
    }
    
    $content = file_get_contents('config.php');
    $has_root = preg_match('/[\'"]root[\'"]/', $content);
    
    if ($has_root) {
        // Find lines containing 'root'
        $lines = explode("\n", $content);
        $root_lines = [];
        
        foreach ($lines as $i => $line) {
            if (stripos($line, 'root') !== false) {
                $root_lines[] = 'Line ' . ($i + 1) . ': ' . trim($line);
            }
        }
        
        return [
            'success' => true,
            'has_root' => true,
            'message' => 'Found root credentials in config.php',
            'lines' => $root_lines
        ];
    } else {
        return [
            'success' => true,
            'has_root' => false,
            'message' => 'No root credentials found in config.php'
        ];
    }
}

// Function to check if admins table exists and has proper structure
function check_admins_table($pdo) {
    try {
        // Check if admins table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'admins'");
        $table_exists = $stmt->fetchColumn();
        
        if (!$table_exists) {
            return [
                'success' => false,
                'message' => 'admins table does not exist'
            ];
        }
        
        // Get table structure
        $stmt = $pdo->query("DESCRIBE admins");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Check for required columns
        $required_columns = ['id', 'username', 'password', 'email', 'role'];
        $missing_columns = array_diff($required_columns, $columns);
        
        if (!empty($missing_columns)) {
            return [
                'success' => false,
                'message' => 'admins table is missing required columns: ' . implode(', ', $missing_columns),
                'columns' => $columns
            ];
        }
        
        // Count users
        $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
        $user_count = $stmt->fetchColumn();
        
        // Get a sample user
        $stmt = $pdo->query("SELECT id, username, email, role FROM admins LIMIT 1");
        $sample_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check password hash format
        $stmt = $pdo->query("SELECT LEFT(password, 7) as hash_start FROM admins LIMIT 1");
        $hash_start = $stmt->fetchColumn();
        $valid_hash = ($hash_start === '$2y$10$');
        
        return [
            'success' => true,
            'message' => 'admins table exists and has proper structure',
            'columns' => $columns,
            'user_count' => $user_count,
            'sample_user' => $sample_user,
            'valid_hash' => $valid_hash,
            'hash_start' => $hash_start
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error checking admins table: ' . $e->getMessage()
        ];
    }
}

// Function to test login with specific credentials
function test_login_credentials($pdo, $username, $password) {
    try {
        // Find user by username
        $stmt = $pdo->prepare("SELECT id, username, password, email, role FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }
        
        // Verify password
        $password_correct = password_verify($password, $user['password']);
        
        return [
            'success' => true,
            'user_found' => true,
            'password_correct' => $password_correct,
            'message' => $password_correct ? 'Login credentials are correct' : 'Password is incorrect',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error testing login credentials: ' . $e->getMessage()
        ];
    }
}

// Function to check admin-auth.php for redirect handling
function check_auth_redirect_handling() {
    if (!file_exists('admin-auth.php')) {
        return [
            'success' => false,
            'message' => 'admin-auth.php file not found'
        ];
    }
    
    $content = file_get_contents('admin-auth.php');
    
    // Check for is_login_page or similar function
    $has_login_page_check = (
        stripos($content, 'is_login_page') !== false || 
        stripos($content, 'login_page') !== false ||
        preg_match('/if\s*\(\s*.*login.*\s*\)\s*{/i', $content)
    );
    
    // Check for require_admin_auth function
    $has_require_auth = stripos($content, 'require_admin_auth') !== false;
    
    // Check for redirect prevention logic
    $has_redirect_prevention = (
        stripos($content, 'redirect loop') !== false || 
        (stripos($content, 'login') !== false && stripos($content, 'skip') !== false) ||
        (stripos($content, 'login') !== false && stripos($content, 'return') !== false)
    );
    
    return [
        'success' => true,
        'has_login_page_check' => $has_login_page_check,
        'has_require_auth' => $has_require_auth,
        'has_redirect_prevention' => $has_redirect_prevention,
        'message' => 'Checked auth redirect handling'
    ];
}

// Function to analyze login process
function check_login_process() {
    if (!file_exists('admin-login-process.php')) {
        return [
            'success' => false,
            'message' => 'admin-login-process.php file not found'
        ];
    }
    
    $content = file_get_contents('admin-login-process.php');
    
    // Check session handling
    $sets_admin_logged_in = stripos($content, "admin_logged_in") !== false;
    $sets_admin_id = stripos($content, "admin_id") !== false;
    $sets_admin_username = stripos($content, "admin_username") !== false;
    $sets_admin_role = stripos($content, "admin_role") !== false;
    
    // Check redirect handling
    $redirects_to_dashboard = stripos($content, "admin-dashboard.php") !== false;
    
    return [
        'success' => true,
        'sets_admin_logged_in' => $sets_admin_logged_in,
        'sets_admin_id' => $sets_admin_id,
        'sets_admin_username' => $sets_admin_username,
        'sets_admin_role' => $sets_admin_role,
        'redirects_to_dashboard' => $redirects_to_dashboard,
        'message' => 'Checked login process'
    ];
}

// Function to handle form submission for session setting
function handle_session_action() {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'set_session') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $_POST['admin_id'] ?? 1;
            $_SESSION['admin_username'] = $_POST['username'] ?? 'shanisbsg';
            $_SESSION['admin_role'] = $_POST['role'] ?? 'admin';
            
            return [
                'success' => true,
                'message' => 'Session variables set successfully'
            ];
        } else if ($_POST['action'] === 'clear_session') {
            session_unset();
            session_destroy();
            
            return [
                'success' => true,
                'message' => 'Session cleared successfully'
            ];
        } else if ($_POST['action'] === 'test_login') {
            $direct_db = test_direct_db_connection();
            
            if (!$direct_db['success']) {
                return [
                    'success' => false,
                    'message' => 'Database connection failed: ' . $direct_db['message']
                ];
            }
            
            return test_login_credentials(
                $direct_db['connection'], 
                $_POST['username'] ?? 'shanisbsg', 
                $_POST['password'] ?? 'a14c65f3'
            );
        }
    }
    
    return null;
}

// Run diagnostics
$direct_db = test_direct_db_connection();
$db_config = test_db_config_connection();
$config_check = check_config_for_root();
$admins_table = $direct_db['success'] ? check_admins_table($direct_db['connection']) : ['success' => false, 'message' => 'Database connection failed'];
$auth_check = check_auth_redirect_handling();
$login_process = check_login_process();
$action_result = handle_session_action();

// HTML Output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System Diagnostic</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        h1, h2, h3 {
            color: #333;
        }
        .section {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .failure {
            color: red;
            font-weight: bold;
        }
        .warning {
            color: orange;
            font-weight: bold;
        }
        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            overflow: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background: #f5f5f5;
        }
        .form-section {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: inline-block;
            width: 100px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"],
        input[type="number"] {
            padding: 8px;
            width: 250px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 10px 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .fix {
            background: #f0f8ff;
            padding: 15px;
            border-left: 5px solid #1890ff;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Login System Diagnostic</h1>
    
    <?php if ($action_result): ?>
    <div class="section">
        <h2>Action Result</h2>
        <p class="<?php echo $action_result['success'] ? 'success' : 'failure'; ?>">
            <?php echo $action_result['message']; ?>
        </p>
        
        <?php if (isset($action_result['user'])): ?>
        <h3>User Information</h3>
        <table>
            <tr>
                <th>ID</th>
                <td><?php echo htmlspecialchars($action_result['user']['id']); ?></td>
            </tr>
            <tr>
                <th>Username</th>
                <td><?php echo htmlspecialchars($action_result['user']['username']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($action_result['user']['email']); ?></td>
            </tr>
            <tr>
                <th>Role</th>
                <td><?php echo htmlspecialchars($action_result['user']['role']); ?></td>
            </tr>
        </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <div class="section">
        <h2>1. Database Connection</h2>
        
        <h3>Direct Connection</h3>
        <p class="<?php echo $direct_db['success'] ? 'success' : 'failure'; ?>">
            <?php echo $direct_db['message']; ?>
        </p>
        
        <h3>Connection via db_config.php</h3>
        <p class="<?php echo $db_config['success'] ? 'success' : 'failure'; ?>">
            <?php echo $db_config['message']; ?>
        </p>
    </div>
    
    <div class="section">
        <h2>2. Configuration Check</h2>
        
        <h3>config.php</h3>
        <?php if ($config_check['success']): ?>
            <?php if ($config_check['has_root']): ?>
                <p class="warning">Found 'root' credentials in config.php:</p>
                <pre><?php echo implode("\n", $config_check['lines']); ?></pre>
                <div class="fix">
                    <h4>Fix</h4>
                    <p>Comment out these lines in config.php to prevent them from overriding your correct credentials:</p>
                    <pre>
/*
define('DB_HOST', 'localhost');
define('DB_NAME', 'backzvsg_playground');
define('DB_USER', 'root');
define('DB_PASSWORD', 'password');
*/</pre>
                </div>
            <?php else: ?>
                <p class="success"><?php echo $config_check['message']; ?></p>
            <?php endif; ?>
        <?php else: ?>
            <p class="failure"><?php echo $config_check['message']; ?></p>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2>3. Database Structure</h2>
        
        <h3>admins Table</h3>
        <?php if ($admins_table['success']): ?>
            <p class="success"><?php echo $admins_table['message']; ?></p>
            
            <h4>Columns</h4>
            <pre><?php echo implode(", ", $admins_table['columns']); ?></pre>
            
            <h4>User Count</h4>
            <p><?php echo $admins_table['user_count']; ?> user(s) found</p>
            
            <?php if (isset($admins_table['sample_user'])): ?>
            <h4>Sample User</h4>
            <pre><?php print_r($admins_table['sample_user']); ?></pre>
            <?php endif; ?>
            
            <h4>Password Hash</h4>
            <p class="<?php echo $admins_table['valid_hash'] ? 'success' : 'warning'; ?>">
                Hash starts with: <?php echo htmlspecialchars($admins_table['hash_start']); ?>
                (<?php echo $admins_table['valid_hash'] ? 'Looks correct (bcrypt)' : 'Does not look like bcrypt!'; ?>)
            </p>
        <?php else: ?>
            <p class="failure"><?php echo $admins_table['message']; ?></p>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2>4. Authentication Code</h2>
        
        <h3>admin-auth.php</h3>
        <?php if ($auth_check['success']): ?>
            <table>
                <tr>
                    <th>Login Page Check</th>
                    <td class="<?php echo $auth_check['has_login_page_check'] ? 'success' : 'warning'; ?>">
                        <?php echo $auth_check['has_login_page_check'] ? 'Found' : 'Not found'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Require Auth Function</th>
                    <td class="<?php echo $auth_check['has_require_auth'] ? 'success' : 'warning'; ?>">
                        <?php echo $auth_check['has_require_auth'] ? 'Found' : 'Not found'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Redirect Prevention</th>
                    <td class="<?php echo $auth_check['has_redirect_prevention'] ? 'success' : 'warning'; ?>">
                        <?php echo $auth_check['has_redirect_prevention'] ? 'Found' : 'Not found'; ?>
                    </td>
                </tr>
            </table>
            
            <?php if (!$auth_check['has_redirect_prevention']): ?>
            <div class="fix">
                <h4>Potential Fix for Redirect Loops</h4>
                <p>Your admin-auth.php might be missing logic to prevent redirect loops. Add this to your file:</p>
                <pre>
/**
 * Check if current page is the login page
 * Prevents redirect loops by identifying if we're already on the login page
 * @return bool True if current page is login page
 */
function is_login_page() {
    $current_script = basename($_SERVER['SCRIPT_NAME']);
    $login_pages = ['admin-login.php', 'login.php', 'admin/login.php'];
    
    return in_array($current_script, $login_pages);
}

/**
 * Require authentication - redirects to login page if not logged in
 * Use at top of admin pages
 */
function require_admin_auth() {
    // Skip redirect if already on login page to prevent loops
    if (is_login_page()) {
        return;
    }

    // Only redirect if not logged in and headers haven't been sent yet
    if (!is_admin_logged_in()) {
        if (!headers_sent()) {
            // Store current URL for redirect after login
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            
            header("Location: admin-login.php");
            exit();
        } else {
            // Display error message instead
            echo '<div class="auth-error">Authentication required. Please <a href="admin-login.php">log in</a> to continue.</div>';
            die();
        }
    }
}</pre>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <p class="failure"><?php echo $auth_check['message']; ?></p>
        <?php endif; ?>
        
        <h3>admin-login-process.php</h3>
        <?php if ($login_process['success']): ?>
            <table>
                <tr>
                    <th>Sets admin_logged_in</th>
                    <td class="<?php echo $login_process['sets_admin_logged_in'] ? 'success' : 'warning'; ?>">
                        <?php echo $login_process['sets_admin_logged_in'] ? 'Yes' : 'No'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Sets admin_id</th>
                    <td class="<?php echo $login_process['sets_admin_id'] ? 'success' : 'warning'; ?>">
                        <?php echo $login_process['sets_admin_id'] ? 'Yes' : 'No'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Sets admin_username</th>
                    <td class="<?php echo $login_process['sets_admin_username'] ? 'success' : 'warning'; ?>">
                        <?php echo $login_process['sets_admin_username'] ? 'Yes' : 'No'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Sets admin_role</th>
                    <td class="<?php echo $login_process['sets_admin_role'] ? 'success' : 'warning'; ?>">
                        <?php echo $login_process['sets_admin_role'] ? 'Yes' : 'No'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Redirects to Dashboard</th>
                    <td class="<?php echo $login_process['redirects_to_dashboard'] ? 'success' : 'warning'; ?>">
                        <?php echo $login_process['redirects_to_dashboard'] ? 'Yes' : 'No'; ?>
                    </td>
                </tr>
            </table>
        <?php else: ?>
            <p class="failure"><?php echo $login_process['message']; ?></p>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2>5. Current Session State</h2>
        
        <pre><?php print_r($_SESSION); ?></pre>
        
        <div class="form-section">
            <h3>Set Session Variables</h3>
            <form method="post">
                <div class="form-group">
                    <label for="admin_id">Admin ID:</label>
                    <input type="number" id="admin_id" name="admin_id" value="1">
                </div>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="shanisbsg">
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <input type="text" id="role" name="role" value="admin">
                </div>
                <input type="hidden" name="action" value="set_session">
                <button type="submit">Set Session Variables</button>
            </form>
            
            <form method="post" style="margin-top: 15px;">
                <input type="hidden" name="action" value="clear_session">
                <button type="submit" style="background: #f44336;">Clear Session</button>
            </form>
        </div>
    </div>
    
    <div class="section">
        <h2>6. Test Login Credentials</h2>
        
        <div class="form-section">
            <form method="post">
                <div class="form-group">
                    <label for="login_username">Username:</label>
                    <input type="text" id="login_username" name="username" value="shanisbsg">
                </div>
                <div class="form-group">
                    <label for="login_password">Password:</label>
                    <input type="password" id="login_password" name="password" value="a14c65f3">
                </div>
                <input type="hidden" name="action" value="test_login">
                <button type="submit">Test Login</button>
            </form>
        </div>
    </div>
    
    <div class="section">
        <h2>7. Navigation</h2>
        
        <ul>
            <li><a href="admin-login.php">Go to Login Page</a></li>
            <li><a href="admin-dashboard.php">Go to Dashboard</a></li>
            <li><a href="admin-login-process.php">Go to Login Process</a> (not recommended)</li>
        </ul>
    </div>
    
    <div class="section">
        <h2>8. Recommendations</h2>
        
        <?php if ($config_check['success'] && $config_check['has_root']): ?>
        <div class="fix">
            <h3>1. Fix config.php</h3>
            <p>Comment out the database credentials in config.php to prevent them from overriding your correct credentials.</p>
        </div>
        <?php endif; ?>
        
        <?php if ($auth_check['success'] && !$auth_check['has_redirect_prevention']): ?>
        <div class="fix">
            <h3>2. Fix Redirect Loop in admin-auth.php</h3>
            <p>Add code to prevent redirect loops by checking if the current page is the login page before redirecting.</p>
        </div>
        <?php endif; ?>
        
        <div class="fix">
            <h3>3. General Troubleshooting</h3>
            <ol>
                <li>Clear your browser cookies and cache</li>
                <li>Use the "Set Session Variables" form above to manually set your session</li>
                <li>Try accessing the dashboard directly after setting session variables</li>
                <li>If all else fails, create a temporary bypass file:
                    <pre>
&lt;?php
// File: bypass.php
session_start();
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_username'] = 'shanisbsg';
$_SESSION['admin_role'] = 'admin';
echo 'Session set. &lt;a href="admin-dashboard.php"&gt;Go to Dashboard&lt;/a&gt;';
?&gt;</pre>
                </li>
            </ol>
        </div>
    </div>
    
    <footer style="margin-top: 30px; text-align: center; color: #777;">
        <p>Login System Diagnostic Tool</p>
        <p><strong>Important:</strong> After resolving your issue, delete this file for security reasons.</p>
    </footer>
</body>
</html>
