<?php
/**
 * Login Files Conflict Diagnostic Tool
 */

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<html><head><title>Login Files Conflict Diagnostic</title>";
echo "<style>
    body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 1200px; margin: 0 auto; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    .section { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
    pre { background: #f5f5f5; padding: 10px; overflow: auto; border-radius: 4px; }
    code { background: #f5f5f5; padding: 2px 5px; border-radius: 3px; }
</style>";
echo "</head><body>";

echo "<h1>Login Files Conflict Diagnostic Report</h1>";
echo "<p>Generated on: " . date('Y-m-d H:i:s') . "</p>";

// SECTION 1: Check for login files
echo "<div class='section'>";
echo "<h2>1. Login Files Check</h2>";

$login_files = [
    'admin-login.php',
    'admin-login.html',
    'login.php',
    'login.html',
    'admin/login.php',
    'admin/login.html'
];

echo "<h3>Checking for login files:</h3>";
echo "<ul>";

foreach ($login_files as $file) {
    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $file;
    
    if (file_exists($full_path)) {
        echo "<li class='success'>Found: <code>$file</code> (Last modified: " . date('Y-m-d H:i:s', filemtime($full_path)) . ")</li>";
        
        // For HTML files, check form action
        if (pathinfo($file, PATHINFO_EXTENSION) === 'html') {
            $content = file_get_contents($full_path);
            
            // Try to find form action
            if (preg_match('/<form[^>]*action=["\']([^"\']*)["\'][^>]*>/i', $content, $matches)) {
                $form_action = $matches[1];
                echo "<li class='info'>HTML form action: <code>$form_action</code></li>";
                
                // Check if action exists
                $action_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $form_action;
                if (file_exists($action_path)) {
                    echo "<li class='success'>Form action file exists</li>";
                } else {
                    echo "<li class='warning'>Form action file does not exist at expected path</li>";
                }
            } else {
                echo "<li class='warning'>Could not determine form action in HTML file</li>";
            }
        }
    } else {
        echo "<li>Not found: <code>$file</code></li>";
    }
}

echo "</ul>";
echo "</div>";

// SECTION 2: Check for redirects in .htaccess
echo "<div class='section'>";
echo "<h2>2. Redirect Rules Check</h2>";

$htaccess_path = $_SERVER['DOCUMENT_ROOT'] . '/.htaccess';
if (file_exists($htaccess_path)) {
    echo "<p class='success'>Found .htaccess file</p>";
    
    // Check for redirect rules
    $htaccess_content = file_get_contents($htaccess_path);
    
    echo "<h3>Relevant redirect rules in .htaccess:</h3>";
    $found_redirects = false;
    
    // Look for redirects related to login pages
    if (preg_match_all('/Redirect.*login.*$/im', $htaccess_content, $matches)) {
        foreach ($matches[0] as $match) {
            echo "<pre>" . htmlspecialchars($match) . "</pre>";
            $found_redirects = true;
        }
    }
    
    if (preg_match_all('/RewriteRule.*login.*$/im', $htaccess_content, $matches)) {
        foreach ($matches[0] as $match) {
            echo "<pre>" . htmlspecialchars($match) . "</pre>";
            $found_redirects = true;
        }
    }
    
    if (!$found_redirects) {
        echo "<p>No login-related redirect rules found in .htaccess</p>";
    }
} else {
    echo "<p>No .htaccess file found in document root</p>";
}
echo "</div>";

// SECTION 3: Session state check
echo "<div class='section'>";
echo "<h2>3. Session State Check</h2>";

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h3>Current Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Check if logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    echo "<p class='success'>Session shows you are logged in as: " . ($_SESSION['admin_username'] ?? 'Unknown') . "</p>";
    echo "<p>Role in session: " . ($_SESSION['admin_role'] ?? 'None') . "</p>";
} else {
    echo "<p class='info'>Session shows you are not logged in</p>";
}

echo "</div>";

// SECTION 4: Test Login Form Submissions
echo "<div class='section'>";
echo "<h2>4. Login Form Test</h2>";

echo "<p>To test the login form submission target:</p>";

echo "<h3>4.1 HTML Login Form Action Test</h3>";
echo "<p>This will check where the admin-login.html form submits to:</p>";

// Try to find the HTML login file
$html_login_path = '';
foreach (['admin-login.html', 'login.html', 'admin/login.html'] as $file) {
    $path = $_SERVER['DOCUMENT_ROOT'] . '/' . $file;
    if (file_exists($path)) {
        $html_login_path = $path;
        break;
    }
}

if ($html_login_path) {
    $content = file_get_contents($html_login_path);
    
    // Extract form method and action
    if (preg_match('/<form[^>]*method=["\']([^"\']*)["\'][^>]*action=["\']([^"\']*)["\'][^>]*>/i', $content, $matches) || 
        preg_match('/<form[^>]*action=["\']([^"\']*)["\'][^>]*method=["\']([^"\']*)["\'][^>]*>/i', $content, $matches_alt)) {
        
        if (!empty($matches)) {
            $method = $matches[1];
            $action = $matches[2];
        } else {
            $method = $matches_alt[2];
            $action = $matches_alt[1];
        }
        
        echo "<p class='info'>Form method: <code>$method</code></p>";
        echo "<p class='info'>Form action: <code>$action</code></p>";
        
        // Check if action is a PHP file
        if (pathinfo($action, PATHINFO_EXTENSION) === 'php') {
            $action_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $action;
            if (file_exists($action_path)) {
                echo "<p class='success'>Form submits to a PHP file that exists: <code>$action</code></p>";
                
                // Check the content of the target file
                $action_content = file_get_contents($action_path);
                
                // Check if it sets session variables
                if (strpos($action_content, '$_SESSION') !== false) {
                    echo "<p class='success'>Target file handles session variables</p>";
                } else {
                    echo "<p class='warning'>Target file doesn't appear to set session variables</p>";
                }
            } else {
                echo "<p class='error'>Form submits to a PHP file that doesn't exist: <code>$action</code></p>";
            }
        } else {
            echo "<p class='warning'>Form doesn't submit to a PHP file, which may cause authentication issues</p>";
        }
    } else {
        echo "<p class='warning'>Could not parse form method and action from HTML login file</p>";
    }
} else {
    echo "<p>No HTML login file found for testing</p>";
}

echo "<h3>4.2 Login Process Test</h3>";
echo "<p>To test the login process directly, use this form:</p>";

// Create a form that will simply test the login without actually logging in
echo "<form method='post' action=''>";
echo "<input type='hidden' name='test_login_process' value='1'>";
echo "<button type='submit'>Test Login Process</button>";
echo "</form>";

// If test form was submitted, perform a harmless test
if (isset($_POST['test_login_process'])) {
    echo "<h4>Login Process Analysis:</h4>";
    
    // Check if admin-login.php exists
    $php_login_path = $_SERVER['DOCUMENT_ROOT'] . '/admin-login.php';
    if (file_exists($php_login_path)) {
        echo "<p class='success'>Found admin-login.php file</p>";
        
        // Check if it uses admin-auth.php
        $login_content = file_get_contents($php_login_path);
        if (strpos($login_content, 'admin-auth.php') !== false) {
            echo "<p class='success'>admin-login.php includes admin-auth.php</p>";
        } else {
            echo "<p class='warning'>admin-login.php does not appear to include admin-auth.php</p>";
        }
        
        // Check for login processing
        if (strpos($login_content, 'admin_login') !== false) {
            echo "<p class='success'>admin-login.php appears to use the admin_login() function</p>";
        } else {
            echo "<p class='warning'>admin-login.php does not appear to use the admin_login() function</p>";
        }
    } else {
        echo "<p class='error'>admin-login.php file not found</p>";
    }
    
    // Check for HTML login form
    $html_login_path = $_SERVER['DOCUMENT_ROOT'] . '/admin-login.html';
    if (file_exists($html_login_path)) {
        echo "<p class='success'>Found admin-login.html file</p>";
        
        // Analyze HTML form
        $html_content = file_get_contents($html_login_path);
        
        // Look for login action
        if (preg_match('/<form[^>]*action=["\']([^"\']*)["\'][^>]*>/i', $html_content, $matches)) {
            $action = $matches[1];
            echo "<p class='info'>HTML form submits to: <code>$action</code></p>";
            
            // Check if it's different from admin-login.php
            if ($action !== 'admin-login.php') {
                echo "<p class='warning'>HTML form submits to a different file than admin-login.php</p>";
                
                // Check if target file exists
                $action_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $action;
                if (file_exists($action_path)) {
                    echo "<p class='success'>Target file exists</p>";
                } else {
                    echo "<p class='error'>Target file does not exist</p>";
                }
            } else {
                echo "<p class='success'>HTML form properly submits to admin-login.php</p>";
            }
        } else {
            echo "<p class='warning'>Could not determine form action in HTML file</p>";
        }
    } else {
        echo "<p>admin-login.html file not found</p>";
    }
}

echo "</div>";

// SECTION 5: Conflict Analysis
echo "<div class='section'>";
echo "<h2>5. Conflict Analysis</h2>";

// Check for both files existing
$php_exists = file_exists($_SERVER['DOCUMENT_ROOT'] . '/admin-login.php');
$html_exists = file_exists($_SERVER['DOCUMENT_ROOT'] . '/admin-login.html');

if ($php_exists && $html_exists) {
    echo "<p class='warning'>Both admin-login.php and admin-login.html exist, which could cause conflicts</p>";
    
    // Compare modification times
    $php_time = filemtime($_SERVER['DOCUMENT_ROOT'] . '/admin-login.php');
    $html_time = filemtime($_SERVER['DOCUMENT_ROOT'] . '/admin-login.html');
    
    if ($php_time > $html_time) {
        echo "<p class='info'>admin-login.php is newer than admin-login.html</p>";
    } else {
        echo "<p class='info'>admin-login.html is newer than admin-login.php</p>";
    }
    
    // Check for any server-side redirects between them
    $htaccess_path = $_SERVER['DOCUMENT_ROOT'] . '/.htaccess';
    if (file_exists($htaccess_path)) {
        $htaccess_content = file_get_contents($htaccess_path);
        
        if (strpos($htaccess_content, 'admin-login.html') !== false && 
            strpos($htaccess_content, 'admin-login.php') !== false) {
            echo "<p class='warning'>Found potential redirect rules between login files in .htaccess</p>";
        }
    }
    
    // Check browser behavior - can we determine default?
    echo "<p>To check which file the server serves by default:</p>";
    echo "<ul>";
    echo "<li>Try visiting <a href='/admin-login' target='_blank'>/admin-login</a> (without extension)</li>";
    echo "<li>Check if it loads the HTML or PHP version</li>";
    echo "</ul>";
    
    // Check if HTML form submits to PHP login
    if ($html_exists) {
        $html_content = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/admin-login.html');
        
        if (preg_match('/<form[^>]*action=["\']([^"\']*)["\'][^>]*>/i', $html_content, $matches)) {
            $action = $matches[1];
            
            if ($action === 'admin-login.php') {
                echo "<p class='success'>HTML form correctly submits to PHP login handler</p>";
            } else {
                echo "<p class='warning'>HTML form does not submit to admin-login.php, which may cause login issues</p>";
            }
        }
    }
} else if ($php_exists) {
    echo "<p class='success'>Only admin-login.php exists (correct setup)</p>";
} else if ($html_exists) {
    echo "<p class='error'>Only admin-login.html exists, which won't properly handle authentication</p>";
} else {
    echo "<p class='error'>Neither admin-login.php nor admin-login.html found in expected locations</p>";
}

echo "</div>";

// SECTION 6: Recommendations
echo "<div class='section'>";
echo "<h2>6. Recommendations</h2>";

echo "<h3>Based on the diagnostic results, here are potential solutions:</h3>";
echo "<ol>";

if ($php_exists && $html_exists) {
    echo "<li class='warning'><strong>Resolve the dual login files conflict:</strong>";
    echo "<ul>";
    echo "<li>Option 1: Remove admin-login.html and use only the PHP version</li>";
    echo "<li>Option 2: Rename admin-login.html to something else if it's needed</li>";
    echo "<li>Option 3: Add a redirect in .htaccess to ensure admin-login.html redirects to admin-login.php</li>";
    echo "</ul></li>";
}

if ($html_exists) {
    echo "<li class='warning'><strong>If keeping the HTML version:</strong>";
    echo "<ul>";
    echo "<li>Ensure the form action points to admin-login.php</li>";
    echo "<li>Update the form to include any required hidden fields</li>";
    echo "</ul></li>";
}

echo "<li><strong>Check session handling:</strong>";
echo "<ul>";
echo "<li>Ensure session_start() is called before any output in all PHP files</li>";
echo "<li>Verify that admin-auth.php is included in admin-login.php</li>";
echo "<li>Make sure the login process properly sets all required session variables</li>";
echo "</ul></li>";

echo "<li><strong>Test login directly:</strong>";
echo "<ul>";
echo "<li>Log out completely (clear session)</li>";
echo "<li>Try logging in with admin-login.php directly</li>";
echo "<li>Verify that session variables are set correctly after login</li>";
echo "</ul></li>";

echo "</ol>";
echo "</div>";

echo "</body></html>";
?>
