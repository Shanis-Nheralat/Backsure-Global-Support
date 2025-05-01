<?php
/**
 * Redirect Loop Detector
 * 
 * This script identifies the exact cause of redirect loops in the admin login system
 * without modifying any existing code.
 */

// Turn on error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Disable any redirections that might happen
define('PREVENT_REDIRECTS', true);

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Track diagnostics run count
if (!isset($_SESSION['diagnostics_run_count'])) {
    $_SESSION['diagnostics_run_count'] = 1;
} else {
    $_SESSION['diagnostics_run_count']++;
}

// Function to check redirection attempts
function trace_redirect($file, $line) {
    // Create log directory if it doesn't exist
    if (!file_exists('logs')) {
        mkdir('logs', 0755, true);
    }
    
    // Log the redirect attempt
    $log_message = date('Y-m-d H:i:s') . " - Redirect attempted in $file at line $line\n";
    file_put_contents('logs/redirect_trace.log', $log_message, FILE_APPEND);
    
    // Display for diagnostics
    echo "<div style='margin: 5px 0; padding: 5px; border: 1px solid #dc3545; background-color: #f8d7da; color: #721c24;'>";
    echo "Redirect Detected: $file at line $line";
    echo "</div>";
    
    return true;
}

// Custom header function that logs instead of redirecting
function custom_header($string, $replace = true, $http_response_code = 0) {
    // Get debug backtrace to find where this was called
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
    $called_from = isset($trace[1]) ? $trace[1] : $trace[0];
    
    $file = isset($called_from['file']) ? $called_from['file'] : 'unknown';
    $line = isset($called_from['line']) ? $called_from['line'] : 0;
    
    // If this is a Location header (redirect), trace it
    if (strpos($string, 'Location:') === 0) {
        trace_redirect($file, $line);
        
        // Extract the URL
        $url = trim(substr($string, 9));
        echo "<p>Would redirect to: <a href='$url'>$url</a></p>";
        
        return;
    }
    
    // Otherwise set the actual header
    header($string, $replace, $http_response_code);
}

// Override the header function for diagnostic purposes
function header($string, $replace = true, $http_response_code = 0) {
    return custom_header($string, $replace, $http_response_code);
}

// Function to examine a file for potential redirection issues
function examine_file($file_path) {
    if (!file_exists($file_path)) {
        return [
            'exists' => false,
            'message' => "File $file_path does not exist."
        ];
    }
    
    $content = file_get_contents($file_path);
    $results = [];
    
    // Check for header("Location:...) calls
    preg_match_all('/header\s*\(\s*[\'"]Location:\s*([^\'"]+)[\'"]/', $content, $matches, PREG_OFFSET_CAPTURE);
    
    if (!empty($matches[0])) {
        $redirects = [];
        foreach ($matches[0] as $index => $match) {
            $offset = $match[1];
            $line_number = substr_count(substr($content, 0, $offset), "\n") + 1;
            $url = $matches[1][$index][0];
            
            $redirects[] = [
                'line' => $line_number,
                'url' => $url,
                'code' => trim($match[0])
            ];
        }
        
        $results['redirects'] = $redirects;
    }
    
    // Check if the file contains require_admin_auth
    if (preg_match('/require_admin_auth\s*\(\s*\)/', $content)) {
        $results['requires_auth'] = true;
    }
    
    // Check for session usage
    if (preg_match('/session_start\s*\(\s*\)/', $content)) {
        $results['starts_session'] = true;
    }
    
    // Check for login detection
    if (preg_match('/is_admin_logged_in\s*\(\s*\)/', $content)) {
        $results['checks_login'] = true;
    }
    
    // Check for is_login_page or similar function
    if (preg_match('/is_login_page|login_page|current_page\s*\=\=\s*[\'"]login/', $content)) {
        $results['checks_page_type'] = true;
    } else {
        $results['checks_page_type'] = false;
    }
    
    return $results;
}

// Create a test for header sending
function test_headers_sent() {
    // Test headers_sent
    $headers_already_sent = headers_sent($file, $line);
    
    if ($headers_already_sent) {
        echo "<div class='alert alert-danger'>";
        echo "Headers already sent at $file line $line. This can cause issues with session handling and redirects.";
        echo "</div>";
    } else {
        echo "<div class='alert alert-success'>";
        echo "Headers not sent yet. This is good for session and redirect handling.";
        echo "</div>";
    }
    
    return !$headers_already_sent;
}

// Check for .htaccess issues
function check_htaccess() {
    if (!file_exists('.htaccess')) {
        return [
            'exists' => false,
            'message' => '.htaccess file not found.'
        ];
    }
    
    $content = file_get_contents('.htaccess');
    $results = [
        'exists' => true,
        'redirect_rules' => []
    ];
    
    // Check for RewriteRule
    preg_match_all('/RewriteRule\s+\^([^\$]+)\$?\s+([^\s]+)(\s+\[([^\]]+)\])?/', $content, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $pattern = $match[1];
        $target = $match[2];
        $flags = isset($match[4]) ? $match[4] : '';
        
        // Only include rules that might cause redirects
        if (strpos($flags, 'R=') !== false || strpos($flags, 'R,') !== false || strpos($flags, ',R') !== false) {
            $results['redirect_rules'][] = [
                'pattern' => $pattern,
                'target' => $target,
                'flags' => $flags
            ];
        }
    }
    
    return $results;
}

// Function to check admin auth function implementation
function check_auth_implementation() {
    $auth_file = 'admin-auth.php';
    if (!file_exists($auth_file)) {
        return [
            'exists' => false,
            'message' => 'admin-auth.php file not found.'
        ];
    }
    
    // Include the file but intercept functions
    ob_start();
    include_once $auth_file;
    ob_end_clean();
    
    $results = [
        'exists' => true,
        'has_login_page_check' => function_exists('is_login_page'),
    ];
    
    // If require_admin_auth function exists, trace its implementation
    if (function_exists('require_admin_auth')) {
        $reflection = new ReflectionFunction('require_admin_auth');
        $start_line = $reflection->getStartLine();
        $end_line = $reflection->getEndLine();
        $length = $end_line - $start_line;
        
        $file = $reflection->getFileName();
        $content = file_get_contents($file);
        $lines = explode("\n", $content);
        
        // Extract the function code
        $function_code = implode("\n", array_slice($lines, $start_line - 1, $length + 1));
        
        $results['require_admin_auth'] = [
            'code' => $function_code,
            'checks_login_page' => strpos($function_code, 'login_page') !== false || 
                                  strpos($function_code, 'current_page') !== false,
            'has_redirect' => strpos($function_code, 'header') !== false && 
                             strpos($function_code, 'Location') !== false
        ];
    }
    
    return $results;
}

// Function to test login checks
function test_login_checks() {
    // Check if necessary functions exist
    $functions_exist = function_exists('is_admin_logged_in') && function_exists('require_admin_auth');
    
    if (!$functions_exist) {
        return [
            'success' => false,
            'message' => 'Authentication functions not found. Make sure admin-auth.php is included.',
        ];
    }
    
    // Test if login check works
    $logged_in = is_admin_logged_in();
    
    // Special handling for require_admin_auth as it might redirect
    $auth_result = null;
    try {
        // Temporarily disable exit
        $old_exit = 'exit';
        function exit($status = '') {
            global $auth_result;
            $auth_result = "Exit called with: " . $status;
            
            // Get debug backtrace to find where exit was called
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $called_from = isset($trace[1]) ? $trace[1] : $trace[0];
            
            $file = isset($called_from['file']) ? $called_from['file'] : 'unknown';
            $line = isset($called_from['line']) ? $called_from['line'] : 0;
            
            trace_redirect($file, $line);
            
            return $status;
        }
        
        // Call the function
        require_admin_auth();
        
    } catch (Exception $e) {
        $auth_result = "Exception: " . $e->getMessage();
    } finally {
        // Reset exit function would go here if possible
    }
    
    return [
        'success' => true,
        'logged_in' => $logged_in,
        'auth_result' => $auth_result
    ];
}

// Prepare results
$current_file = basename($_SERVER['SCRIPT_NAME']);
$current_url = $_SERVER['REQUEST_URI'];
$auth_file_analysis = examine_file('admin-auth.php');
$login_file_analysis = examine_file('admin-login.php');
$login_process_analysis = examine_file('admin-login-process.php');
$htaccess_analysis = check_htaccess();
$auth_implementation = check_auth_implementation();
$login_checks = test_login_checks();

// Check actual session values
$session_values = [
    'admin_logged_in' => isset($_SESSION['admin_logged_in']) ? $_SESSION['admin_logged_in'] : 'not set',
    'admin_id' => isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 'not set',
    'admin_username' => isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'not set',
    'admin_role' => isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'not set',
    'redirect_after_login' => isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'not set'
];

// Print results with clean formatting
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirect Loop Detector</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2, h3 {
            color: #0056b3;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #f8f9fa;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .card-body {
            padding: 20px;
        }
        .alert {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        code {
            background-color: #f8f9fa;
            padding: 2px 4px;
            border-radius: 3px;
            font-family: monospace;
        }
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-family: monospace;
            border: 1px solid #ddd;
        }
        .text-danger { color: #dc3545; }
        .text-success { color: #28a745; }
        .text-warning { color: #ffc107; }
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .status-success { background-color: #28a745; }
        .status-warning { background-color: #ffc107; }
        .status-danger { background-color: #dc3545; }
    </style>
</head>
<body>
    <h1>Redirect Loop Detector</h1>
    <p>This tool analyzes your admin login system to find the exact cause of redirect loops.</p>
    
    <div class="card">
        <div class="card-header">Current Environment</div>
        <div class="card-body">
            <table>
                <tr>
                    <th>Current File</th>
                    <td><?php echo $current_file; ?></td>
                </tr>
                <tr>
                    <th>Current URL</th>
                    <td><?php echo $current_url; ?></td>
                </tr>
                <tr>
                    <th>Test Run Count</th>
                    <td><?php echo $_SESSION['diagnostics_run_count']; ?></td>
                </tr>
                <tr>
                    <th>Headers Sent</th>
                    <td><?php echo headers_sent() ? '<span class="text-danger">Yes</span>' : '<span class="text-success">No</span>'; ?></td>
                </tr>
                <tr>
                    <th>Browser</th>
                    <td><?php echo htmlspecialchars($_SERVER['HTTP_USER_AGENT']); ?></td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">Session Data</div>
        <div class="card-body">
            <table>
                <?php foreach ($session_values as $key => $value): ?>
                <tr>
                    <th><?php echo htmlspecialchars($key); ?></th>
                    <td>
                        <?php 
                        if ($value === 'not set') {
                            echo '<span class="text-danger">Not set</span>';
                        } else if ($value === true) {
                            echo '<span class="text-success">true</span>';
                        } else if ($value === false) {
                            echo '<span class="text-warning">false</span>';
                        } else {
                            echo htmlspecialchars($value); 
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            
            <div class="alert alert-info">
                <strong>Note:</strong> The <code>admin_logged_in</code> session variable is critical. It should be set to <code>true</code> after successful login.
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">Authentication File Analysis (admin-auth.php)</div>
        <div class="card-body">
            <?php if (isset($auth_file_analysis['redirects'])): ?>
                <div class="alert alert-warning">
                    <strong>Found <?php echo count($auth_file_analysis['redirects']); ?> redirects in admin-auth.php</strong>
                </div>
                
                <table>
                    <tr>
                        <th>Line</th>
                        <th>URL</th>
                        <th>Code</th>
                    </tr>
                    <?php foreach ($auth_file_analysis['redirects'] as $redirect): ?>
                    <tr>
                        <td><?php echo $redirect['line']; ?></td>
                        <td><?php echo htmlspecialchars($redirect['url']); ?></td>
                        <td><code><?php echo htmlspecialchars($redirect['code']); ?></code></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <div class="alert alert-success">
                    No direct redirects found in admin-auth.php
                </div>
            <?php endif; ?>
            
            <h3>Authentication Function Check</h3>
            
            <?php if (isset($auth_implementation['require_admin_auth'])): ?>
                <div class="alert <?php echo $auth_implementation['require_admin_auth']['checks_login_page'] ? 'alert-success' : 'alert-danger'; ?>">
                    <?php if ($auth_implementation['require_admin_auth']['checks_login_page']): ?>
                        <strong>Good:</strong> The <code>require_admin_auth()</code> function appears to check if it's on the login page.
                    <?php else: ?>
                        <strong>Critical Issue:</strong> The <code>require_admin_auth()</code> function does NOT check if it's on the login page. This is likely causing the redirect loop.
                    <?php endif; ?>
                </div>
                
                <p>Here's the implementation of <code>require_admin_auth()</code>:</p>
                <pre><?php echo htmlspecialchars($auth_implementation['require_admin_auth']['code']); ?></pre>
            <?php endif; ?>
            
            <div class="alert <?php echo $auth_implementation['has_login_page_check'] ? 'alert-success' : 'alert-danger'; ?>">
                <?php if ($auth_implementation['has_login_page_check']): ?>
                    <strong>Good:</strong> Found <code>is_login_page()</code> function, which is needed to prevent redirect loops.
                <?php else: ?>
                    <strong>Critical Issue:</strong> No <code>is_login_page()</code> function found. This function is needed to prevent redirect loops.
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">Login Page Analysis (admin-login.php)</div>
        <div class="card-body">
            <?php if (isset($login_file_analysis['redirects'])): ?>
                <div class="alert alert-warning">
                    <strong>Found <?php echo count($login_file_analysis['redirects']); ?> redirects in admin-login.php</strong>
                </div>
                
                <table>
                    <tr>
                        <th>Line</th>
                        <th>URL</th>
                        <th>Code</th>
                    </tr>
                    <?php foreach ($login_file_analysis['redirects'] as $redirect): ?>
                    <tr>
                        <td><?php echo $redirect['line']; ?></td>
                        <td><?php echo htmlspecialchars($redirect['url']); ?></td>
                        <td><code><?php echo htmlspecialchars($redirect['code']); ?></code></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <div class="alert alert-success">
                    No direct redirects found in admin-login.php
                </div>
            <?php endif; ?>
            
            <div class="alert <?php echo isset($login_file_analysis['checks_login']) ? 'alert-success' : 'alert-warning'; ?>">
                <?php if (isset($login_file_analysis['checks_login'])): ?>
                    <strong>Good:</strong> Login page checks if user is already logged in.
                <?php else: ?>
                    <strong>Warning:</strong> Login page may not be checking if user is already logged in.
                <?php endif; ?>
            </div>
            
            <div class="alert <?php echo isset($login_file_analysis['starts_session']) ? 'alert-success' : 'alert-danger'; ?>">
                <?php if (isset($login_file_analysis['starts_session'])): ?>
                    <strong>Good:</strong> Login page starts a session.
                <?php else: ?>
                    <strong>Critical Issue:</strong> Login page does not appear to start a session.
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php if (isset($htaccess_analysis['redirect_rules']) && !empty($htaccess_analysis['redirect_rules'])): ?>
    <div class="card">
        <div class="card-header">.htaccess Redirect Rules</div>
        <div class="card-body">
            <div class="alert alert-warning">
                <strong>Found <?php echo count($htaccess_analysis['redirect_rules']); ?> redirect rules in .htaccess</strong>
            </div>
            
            <table>
                <tr>
                    <th>Pattern</th>
                    <th>Target</th>
                    <th>Flags</th>
                </tr>
                <?php foreach ($htaccess_analysis['redirect_rules'] as $rule): ?>
                <tr>
                    <td><?php echo htmlspecialchars($rule['pattern']); ?></td>
                    <td><?php echo htmlspecialchars($rule['target']); ?></td>
                    <td><?php echo htmlspecialchars($rule['flags']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">Root Cause Analysis</div>
        <div class="card-body">
            <?php 
            $possible_causes = [];
            
            // Check for missing login page detection
            if (!$auth_implementation['has_login_page_check'] || 
                (isset($auth_implementation['require_admin_auth']) && 
                 !$auth_implementation['require_admin_auth']['checks_login_page'])) {
                $possible_causes[] = [
                    'severity' => 'high',
                    'issue' => 'The authentication system does not check if it\'s already on the login page before redirecting.',
                    'solution' => 'Add a function to detect the login page and skip the redirect when already on it.'
                ];
            }
            
            // Check for session handling issues
            if (!isset($login_file_analysis['starts_session'])) {
                $possible_causes[] = [
                    'severity' => 'high',
                    'issue' => 'The login page does not appear to start a session.',
                    'solution' => 'Make sure session_start() is called at the beginning of admin-login.php.'
                ];
            }
            
            // Check for session variable issues
            if ($session_values['admin_logged_in'] === 'not set' || $session_values['admin_logged_in'] === false) {
                $possible_causes[] = [
                    'severity' => 'medium',
                    'issue' => 'The admin_logged_in session variable is not set or is false.',
                    'solution' => 'Make sure the login process correctly sets $_SESSION[\'admin_logged_in\'] = true after successful login.'
                ];
            }
            
            // Check for .htaccess redirect issues
            if (isset($htaccess_analysis['redirect_rules']) && !empty($htaccess_analysis['redirect_rules'])) {
                $possible_causes[] = [
                    'severity' => 'medium',
                    'issue' => 'Found redirect rules in .htaccess that might interfere with login page access.',
                    'solution' => 'Review .htaccess rules to ensure they don\'t create redirect loops with admin pages.'
                ];
            }
            
            // Check for multiple redirects in auth file
            if (isset($auth_file_analysis['redirects']) && count($auth_file_analysis['redirects']) > 1) {
                $possible_causes[] = [
                    'severity' => 'medium',
                    'issue' => 'Multiple redirects in admin-auth.php could be causing conflicts.',
                    'solution' => 'Consolidate redirect logic in admin-auth.php.'
                ];
            }
            
            // If no clear causes found
            if (empty($possible_causes)) {
                $possible_causes[] = [
                    'severity' => 'low',
                    'issue' => 'No obvious causes found. The issue might be more complex.',
                    'solution' => 'Add detailed logging to trace the exact flow of redirects.'
                ];
            }
            ?>
            
            <h3>Possible Causes of Redirect Loop</h3>
            
            <?php foreach ($possible_causes as $cause): ?>
                <div class="alert <?php 
                    echo $cause['severity'] === 'high' ? 'alert-danger' : 
                        ($cause['severity'] === 'medium' ? 'alert-warning' : 'alert-info'); 
                ?>">
                    <h4>
                        <span class="status-indicator <?php 
                            echo $cause['severity'] === 'high' ? 'status-danger' : 
                                ($cause['severity'] === 'medium' ? 'status-warning' : 'status-success'); 
                        ?>"></span>
                        <?php echo htmlspecialchars($cause['issue']); ?>
                    </h4>
                    <p><strong>Solution:</strong> <?php echo htmlspecialchars($cause['solution']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">Minimal Fix Recommendation</div>
        <div class="card-body">
            <?php if (!$auth_implementation['has_login_page_check']): ?>
                <div class="alert alert-info">
                    <p><strong>Add this to the top of your admin-auth.php file (just after the session_start):</strong></p>
                </div>
                
                <pre>/**
 * Check if current page is the login page
 * @return bool True if current page is login page
 */
function is_login_page() {
    $current_script = basename($_SERVER['SCRIPT_NAME']);
    return ($current_script === 'admin-login.php');
}

// Original require_admin_auth function
function require_admin_auth() {
    // Skip redirect if already on login page
    if (is_login_page()) {
        return;
    }
    
    // Only redirect if headers haven't been sent yet
    if (!is_admin_logged_in()) {
        if (!headers_sent()) {
            header("Location: admin-login.php");
            exit();
        } else {
            // If headers already sent, display error message instead
            echo '&lt;div class="auth-error"&gt;Authentication required. Please &lt;a href="admin-login.php"&gt;log in&lt;/a&gt; to continue.&lt;/div&gt;';
            // Optional: halt script execution
            die();
        }
    }
}</pre>
                
                <div class="alert alert-warning">
                    <p><strong>Important:</strong> Make sure you replace the existing <code>require_admin_auth()</code> function with this version.</p>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <p>Based on the analysis, your <code>require_admin_auth()</code> function may need to be updated to check if it's on the login page before redirecting.</p>
                    <p>Review the implementation and ensure it contains a check like:</p>
                </div>
                
                <pre>// Inside require_admin_auth() function
// Skip redirect if already on login page
if (is_login_page()) {
    return;
}

// Then proceed with normal authentication check</pre>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">Next Steps</div>
        <div class="card-body">
            <ol>
                <li>Apply the recommended minimal fix to your <code>admin-auth.php</code> file.</li>
                <li>Clear your browser cookies and session data.</li>
                <li>Test the login page again.</li>
                <li>If issues persist, run this tool again to see if new issues are detected.</li>
                <li>Consider adding temporary logging to track the exact flow of your authentication system.</li>
            </ol>
        </div>
    </div>
</body>
</html>
