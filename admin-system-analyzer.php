<?php
/**
 * Admin System Dependency Analyzer
 * Maps all admin file dependencies, configurations, and session variables
 */

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<html><head><title>Admin System Dependency Analysis</title>";
echo "<style>
    body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 1400px; margin: 0 auto; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    .highlight { background-color: #ffffcc; padding: 2px; }
    .code { font-family: monospace; background: #f5f5f5; padding: 10px; overflow: auto; border-radius: 4px; }
    .file-box { border: 1px solid #ddd; margin: 10px 0; padding: 10px; border-radius: 4px; }
    .file-header { background: #f5f5f5; padding: 5px; margin: -10px -10px 10px -10px; border-bottom: 1px solid #ddd; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .dependency-map { font-family: monospace; white-space: pre; margin: 20px 0; background: #f9f9f9; padding: 15px; border-radius: 4px; }
    .function-list { max-height: 200px; overflow-y: auto; }
    .tabs { display: flex; margin-bottom: 10px; }
    .tab { padding: 8px 16px; border: 1px solid #ddd; cursor: pointer; background: #f5f5f5; }
    .tab.active { background: #fff; border-bottom: 1px solid white; }
    .tab-content { display: none; border: 1px solid #ddd; padding: 15px; margin-top: -1px; }
    .tab-content.active { display: block; }
</style>";

// Add JavaScript for tabs
echo "<script>
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName('tab-content');
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = 'none';
    }
    tablinks = document.getElementsByClassName('tab');
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(' active', '');
    }
    document.getElementById(tabName).style.display = 'block';
    evt.currentTarget.className += ' active';
}
</script>";

echo "</head><body>";

echo "<h1>Admin System Dependency Analysis</h1>";
echo "<p>This tool analyzes your entire admin system to map all dependencies, configurations, and session variables.</p>";

// Get document root
$doc_root = $_SERVER['DOCUMENT_ROOT'];

// Function to recursively find files
function findFiles($dir, $pattern) {
    $result = [];
    $files = glob($dir . '/' . $pattern);
    foreach ($files as $file) {
        if (is_file($file)) {
            $result[] = $file;
        }
    }
    
    // Search in subdirectories
    $dirs = glob($dir . '/*', GLOB_ONLYDIR);
    foreach ($dirs as $subdir) {
        $subdir_files = findFiles($subdir, $pattern);
        $result = array_merge($result, $subdir_files);
    }
    
    return $result;
}

// Function to safely get file content
function getFileContent($file) {
    if (file_exists($file)) {
        return [
            'exists' => true,
            'content' => file_get_contents($file),
            'size' => filesize($file),
            'modified' => date('Y-m-d H:i:s', filemtime($file))
        ];
    }
    
    return ['exists' => false];
}

// Function to extract includes and requires
function extractIncludes($content) {
    $includes = [];
    if (preg_match_all('/(include|require)(?:_once)?\s*\(?\s*[\'"]([^\'"]+)[\'"]\s*\)?;/i', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $includes[] = trim($match[2]);
        }
    }
    return $includes;
}

// Function to extract session variables
function extractSessionVars($content) {
    $vars = [];
    if (preg_match_all('/\$_SESSION\[[\'"]([^\'"]+)[\'"]\]\s*=\s*([^;]+);/i', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $vars[$match[1]] = trim($match[2]);
        }
    }
    return $vars;
}

// Function to extract session variable reads
function extractSessionReads($content) {
    $vars = [];
    if (preg_match_all('/\$_SESSION\[[\'"]([^\'"]+)[\'"]\]/', $content, $matches)) {
        foreach ($matches[1] as $var) {
            if (!isset($vars[$var])) {
                $vars[$var] = 0;
            }
            $vars[$var]++;
        }
    }
    return $vars;
}

// Function to extract function definitions
function extractFunctions($content) {
    $functions = [];
    if (preg_match_all('/function\s+([a-zA-Z0-9_]+)\s*\(/i', $content, $matches)) {
        $functions = $matches[1];
    }
    return $functions;
}

// Function to extract function calls
function extractFunctionCalls($content, $functionList) {
    $calls = [];
    foreach ($functionList as $function) {
        if (preg_match_all('/\b' . preg_quote($function, '/') . '\s*\(/i', $content, $matches)) {
            $calls[$function] = count($matches[0]);
        }
    }
    return $calls;
}

// Function to extract database credential definitions
function extractDBCredentials($content) {
    $credentials = [];
    
    // Look for common credential variable patterns
    $patterns = [
        'host' => ['/\$(?:db_host|host|hostname|server|dbhost)\s*=\s*[\'"]([^\'"]+)[\'"]/i'],
        'name' => ['/\$(?:db_name|dbname|database|db)\s*=\s*[\'"]([^\'"]+)[\'"]/i'],
        'user' => ['/\$(?:db_user|user|username|dbuser)\s*=\s*[\'"]([^\'"]+)[\'"]/i'],
        'pass' => ['/\$(?:db_pass|pass|password|dbpass)\s*=\s*[\'"]([^\'"]+)[\'"]/i']
    ];
    
    foreach ($patterns as $key => $patternList) {
        foreach ($patternList as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $credentials[$key] = $matches[1];
                break;
            }
        }
    }
    
    // Look for PDO connection string
    if (preg_match('/new\s+PDO\s*\(\s*[\'"]mysql:host=([^;]+);dbname=([^;\'"\)]+)/i', $content, $matches)) {
        $credentials['host'] = $matches[1];
        $credentials['name'] = $matches[2];
    }
    
    return $credentials;
}

// Find all admin-related files
$admin_php_files = findFiles($doc_root, '*admin*.php');
$admin_html_files = findFiles($doc_root, '*admin*.html');
$config_files = findFiles($doc_root, '*config*.php');
$login_files = findFiles($doc_root, '*login*.php');
$login_html_files = findFiles($doc_root, '*login*.html');

// Merge all files and deduplicate
$all_files = array_unique(array_merge($admin_php_files, $admin_html_files, $config_files, $login_files, $login_html_files));
sort($all_files);

// Make relative paths
$rel_files = [];
foreach ($all_files as $file) {
    $rel_files[] = str_replace($doc_root, '', $file);
}

// Create file data array
$file_data = [];
foreach ($all_files as $file) {
    $rel_path = str_replace($doc_root, '', $file);
    $file_data[$rel_path] = getFileContent($file);
}

// Extract includes, functions, and session variables for each file
$includes_map = [];
$session_vars_map = [];
$session_reads_map = [];
$function_defs_map = [];
$function_calls_map = [];
$db_credentials_map = [];

foreach ($file_data as $rel_path => $data) {
    if ($data['exists']) {
        $includes_map[$rel_path] = extractIncludes($data['content']);
        $session_vars_map[$rel_path] = extractSessionVars($data['content']);
        $session_reads_map[$rel_path] = extractSessionReads($data['content']);
        $function_defs_map[$rel_path] = extractFunctions($data['content']);
        $db_credentials_map[$rel_path] = extractDBCredentials($data['content']);
    }
}

// Build dependency tree
$dependency_tree = [];
foreach ($includes_map as $file => $includes) {
    $dependency_tree[$file] = [];
    
    foreach ($includes as $include) {
        // Try to find the actual file
        $found = false;
        
        // Try direct match
        if (isset($file_data[$include])) {
            $dependency_tree[$file][] = $include;
            $found = true;
        } else {
            // Try relative path resolution
            $file_dir = dirname($file);
            $possible_path = $file_dir . '/' . $include;
            
            if (isset($file_data[$possible_path])) {
                $dependency_tree[$file][] = $possible_path;
                $found = true;
            } else {
                // Try matching basename
                $include_base = basename($include);
                foreach (array_keys($file_data) as $existing_file) {
                    if (basename($existing_file) === $include_base) {
                        $dependency_tree[$file][] = $existing_file;
                        $found = true;
                        break;
                    }
                }
            }
        }
        
        if (!$found) {
            $dependency_tree[$file][] = $include . ' (not found)';
        }
    }
}

// Now find all function calls
$all_functions = [];
foreach ($function_defs_map as $functions) {
    $all_functions = array_merge($all_functions, $functions);
}
$all_functions = array_unique($all_functions);

// Extract function calls from all files
foreach ($file_data as $rel_path => $data) {
    if ($data['exists']) {
        $function_calls_map[$rel_path] = extractFunctionCalls($data['content'], $all_functions);
    }
}

// Categorize files
$auth_files = [];
$config_file_list = [];
$login_file_list = [];
$dashboard_files = [];
$other_admin_files = [];

foreach (array_keys($file_data) as $file) {
    $basename = basename($file);
    
    if (stripos($basename, 'auth') !== false) {
        $auth_files[] = $file;
    } else if (stripos($basename, 'config') !== false) {
        $config_file_list[] = $file;
    } else if (stripos($basename, 'login') !== false) {
        $login_file_list[] = $file;
    } else if (stripos($basename, 'dashboard') !== false) {
        $dashboard_files[] = $file;
    } else if (stripos($basename, 'admin') !== false) {
        $other_admin_files[] = $file;
    }
}

// Tab navigation
echo "<div class='tabs'>";
echo "<div class='tab active' onclick='openTab(event, \"tab-files\")'>Files Overview</div>";
echo "<div class='tab' onclick='openTab(event, \"tab-config\")'>Config Files</div>";
echo "<div class='tab' onclick='openTab(event, \"tab-login\")'>Login System</div>";
echo "<div class='tab' onclick='openTab(event, \"tab-auth\")'>Auth System</div>";
echo "<div class='tab' onclick='openTab(event, \"tab-dashboard\")'>Dashboard & Admin</div>";
echo "<div class='tab' onclick='openTab(event, \"tab-session\")'>Session Analysis</div>";
echo "<div class='tab' onclick='openTab(event, \"tab-functions\")'>Function Analysis</div>";
echo "<div class='tab' onclick='openTab(event, \"tab-map\")'>Dependency Map</div>";
echo "<div class='tab' onclick='openTab(event, \"tab-solution\")'>Solution Plan</div>";
echo "</div>";

// Files Overview Tab
echo "<div id='tab-files' class='tab-content active'>";
echo "<h2>1. All Admin-Related Files</h2>";

echo "<p>Found " . count($file_data) . " admin-related files:</p>";
echo "<table>";
echo "<tr><th>File</th><th>Size</th><th>Last Modified</th><th>Category</th><th># Includes</th><th># Functions</th><th># Session Vars</th></tr>";

foreach ($file_data as $rel_path => $data) {
    if ($data['exists']) {
        $file_type = [];
        if (stripos($rel_path, 'auth') !== false) $file_type[] = "Auth";
        if (stripos($rel_path, 'login') !== false) $file_type[] = "Login";
        if (stripos($rel_path, 'dashboard') !== false) $file_type[] = "Dashboard";
        if (stripos($rel_path, 'config') !== false) $file_type[] = "Config";
        if (stripos($rel_path, 'admin') !== false && empty($file_type)) $file_type[] = "Admin";
        if (empty($file_type)) $file_type[] = "Other";
        
        $num_includes = count($includes_map[$rel_path] ?? []);
        $num_functions = count($function_defs_map[$rel_path] ?? []);
        $num_session_vars = count($session_vars_map[$rel_path] ?? []);
        
        echo "<tr>";
        echo "<td><a href='$rel_path' target='_blank'>$rel_path</a></td>";
        echo "<td>" . number_format($data['size']/1024, 2) . " KB</td>";
        echo "<td>" . $data['modified'] . "</td>";
        echo "<td>" . implode(", ", $file_type) . "</td>";
        echo "<td>" . $num_includes . "</td>";
        echo "<td>" . $num_functions . "</td>";
        echo "<td>" . $num_session_vars . "</td>";
        echo "</tr>";
    }
}

echo "</table>";
echo "</div>";

// Config Files Tab
echo "<div id='tab-config' class='tab-content'>";
echo "<h2>2. Configuration Files Analysis</h2>";

echo "<p>Found " . count($config_file_list) . " configuration files:</p>";
echo "<table>";
echo "<tr><th>File</th><th>DB Host</th><th>DB Name</th><th>DB User</th><th>DB Password</th></tr>";

foreach ($config_file_list as $file) {
    $creds = $db_credentials_map[$file] ?? [];
    
    echo "<tr>";
    echo "<td><a href='$file' target='_blank'>$file</a></td>";
    echo "<td>" . ($creds['host'] ?? '-') . "</td>";
    echo "<td>" . ($creds['name'] ?? '-') . "</td>";
    echo "<td>" . ($creds['user'] ?? '-') . "</td>";
    echo "<td>" . (isset($creds['pass']) ? '******' : '-') . "</td>";
    echo "</tr>";
}

echo "</table>";

// Check who includes config files
echo "<h3>Config File Dependencies</h3>";
echo "<p>Which files include configuration files:</p>";
echo "<table>";
echo "<tr><th>Config File</th><th>Included By</th></tr>";

foreach ($config_file_list as $config_file) {
    $included_by = [];
    
    foreach ($includes_map as $file => $includes) {
        foreach ($includes as $include) {
            if ($include === $config_file || basename($include) === basename($config_file)) {
                $included_by[] = $file;
            }
        }
    }
    
    echo "<tr>";
    echo "<td>$config_file</td>";
    echo "<td>" . (empty($included_by) ? 'Not included directly' : implode("<br>", $included_by)) . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// Login System Tab
echo "<div id='tab-login' class='tab-content'>";
echo "<h2>3. Login System Analysis</h2>";

echo "<p>Found " . count($login_file_list) . " login-related files:</p>";

echo "<h3>Login Files</h3>";
echo "<table>";
echo "<tr><th>File</th><th>Form Action</th><th>Session Variables Set</th><th>Redirects To</th></tr>";

foreach ($login_file_list as $file) {
    $content = $file_data[$file]['content'] ?? '';
    
    // Extract form action
    $form_action = "N/A";
    if (preg_match('/<form[^>]*action=["\']([^"\']*)["\'][^>]*>/i', $content, $matches)) {
        $form_action = $matches[1];
    }
    
    // Extract redirects
    $redirects = [];
    if (preg_match_all('/header\s*\(\s*[\'"](Location|Refresh):\s*([^;]+)/i', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $redirects[] = trim($match[2], '\'" ');
        }
    }
    
    // Get session variables
    $session_vars = $session_vars_map[$file] ?? [];
    
    echo "<tr>";
    echo "<td><a href='$file' target='_blank'>$file</a></td>";
    echo "<td>" . htmlspecialchars($form_action) . "</td>";
    echo "<td>";
    foreach ($session_vars as $key => $value) {
        echo "<code>$key = $value</code><br>";
    }
    echo "</td>";
    echo "<td>";
    foreach ($redirects as $redirect) {
        echo "<code>$redirect</code><br>";
    }
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

// Analyze the all-in-one solution
echo "<h3>All-in-One Login Solution Analysis</h3>";

$all_in_one_file = '/playground/complete-admin-login.php';
if (isset($file_data[$all_in_one_file])) {
    $all_in_one = $file_data[$all_in_one_file];
    $session_vars = $session_vars_map[$all_in_one_file] ?? [];
    
    // Required session vars for login
    $required_vars = ['admin_logged_in', 'admin_id', 'admin_username', 'admin_role'];
    $missing_vars = array_diff($required_vars, array_keys($session_vars));
    
    if (empty($missing_vars)) {
        echo "<p class='success'>The all-in-one solution sets all required session variables!</p>";
    } else {
        echo "<p class='warning'>The all-in-one solution is missing these session variables: " . implode(', ', $missing_vars) . "</p>";
    }
    
    echo "<h4>Session Variables Set:</h4>";
    echo "<ul>";
    foreach ($session_vars as $key => $value) {
        echo "<li><strong>$key</strong> = " . htmlspecialchars($value) . "</li>";
    }
    echo "</ul>";
    
    // Database credentials
    $creds = $db_credentials_map[$all_in_one_file] ?? [];
    
    echo "<h4>Database Credentials:</h4>";
    echo "<ul>";
    echo "<li>Host: " . ($creds['host'] ?? 'Not found') . "</li>";
    echo "<li>Database: " . ($creds['name'] ?? 'Not found') . "</li>";
    echo "<li>Username: " . ($creds['user'] ?? 'Not found') . "</li>";
    echo "<li>Password: " . (isset($creds['pass']) ? '******' : 'Not found') . "</li>";
    echo "</ul>";
    
} else {
    echo "<p class='error'>All-in-one solution file not found.</p>";
}

echo "</div>";

// Auth System Tab
echo "<div id='tab-auth' class='tab-content'>";
echo "<h2>4. Authentication System Analysis</h2>";

echo "<p>Found " . count($auth_files) . " authentication-related files:</p>";

// Display auth file functions
echo "<h3>Authentication Functions</h3>";
echo "<table>";
echo "<tr><th>File</th><th>Functions Defined</th><th>Session Vars Set</th><th>Session Vars Read</th></tr>";

foreach ($auth_files as $file) {
    $functions = $function_defs_map[$file] ?? [];
    $session_vars = $session_vars_map[$file] ?? [];
    $session_reads = $session_reads_map[$file] ?? [];
    
    echo "<tr>";
    echo "<td><a href='$file' target='_blank'>$file</a></td>";
    echo "<td class='function-list'>";
    foreach ($functions as $function) {
        if (stripos($function, 'login') !== false || 
            stripos($function, 'auth') !== false || 
            stripos($function, 'admin') !== false || 
            stripos($function, 'session') !== false ||
            stripos($function, 'user') !== false) {
            echo "<code class='highlight'>$function()</code><br>";
        } else {
            echo "<code>$function()</code><br>";
        }
    }
    echo "</td>";
    echo "<td>";
    foreach ($session_vars as $key => $value) {
        echo "<code>$key = $value</code><br>";
    }
    echo "</td>";
    echo "<td>";
    foreach ($session_reads as $key => $count) {
        echo "<code>$key</code> ($count times)<br>";
    }
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

// Who uses auth files
echo "<h3>Auth File Dependencies</h3>";
echo "<p>Which files include authentication files:</p>";
echo "<table>";
echo "<tr><th>Auth File</th><th>Included By</th></tr>";

foreach ($auth_files as $auth_file) {
    $included_by = [];
    
    foreach ($includes_map as $file => $includes) {
        foreach ($includes as $include) {
            if ($include === $auth_file || basename($include) === basename($auth_file)) {
                $included_by[] = $file;
            }
        }
    }
    
    echo "<tr>";
    echo "<td>$auth_file</td>";
    echo "<td>" . (empty($included_by) ? 'Not included directly' : implode("<br>", $included_by)) . "</td>";
    echo "</tr>";
}

echo "</table>";

// Critical auth functions
echo "<h3>Critical Auth Functions</h3>";
echo "<p>Where important authentication functions are defined and used:</p>";

$critical_functions = ['admin_login', 'require_admin_auth', 'is_admin_logged_in', 'require_admin_role', 'has_admin_role'];
echo "<table>";
echo "<tr><th>Function</th><th>Defined In</th><th>Called By</th></tr>";

foreach ($critical_functions as $function) {
    $defined_in = [];
    foreach ($function_defs_map as $file => $functions) {
        if (in_array($function, $functions)) {
            $defined_in[] = $file;
        }
    }
    
    $called_by = [];
    foreach ($function_calls_map as $file => $calls) {
        if (isset($calls[$function]) && $calls[$function] > 0) {
            $called_by[] = $file . " ({$calls[$function]} times)";
        }
    }
    
    echo "<tr>";
    echo "<td><code>$function()</code></td>";
    echo "<td>" . (empty($defined_in) ? 'Not defined' : implode("<br>", $defined_in)) . "</td>";
    echo "<td>" . (empty($called_by) ? 'Not called' : implode("<br>", $called_by)) . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// Dashboard & Admin Tab
echo "<div id='tab-dashboard' class='tab-content'>";
echo "<h2>5. Dashboard & Admin Pages Analysis</h2>";

echo "<p>Found " . count($dashboard_files) . " dashboard files and " . count($other_admin_files) . " other admin files.</p>";

// Dashboard file dependencies
echo "<h3>Dashboard Dependencies</h3>";
echo "<table>";
echo "<tr><th>Dashboard File</th><th>Includes</th><th>Session Vars Read</th><th>Auth Functions Used</th></tr>";

foreach ($dashboard_files as $file) {
    $includes = $includes_map[$file] ?? [];
    $session_reads = $session_reads_map[$file] ?? [];
    
    // Get auth function calls
    $auth_calls = [];
    foreach ($critical_functions as $function) {
        if (isset($function_calls_map[$file][$function]) && $function_calls_map[$file][$function] > 0) {
            $auth_calls[] = "$function() ({$function_calls_map[$file][$function]} times)";
        }
    }
    
    echo "<tr>";
    echo "<td><a href='$file' target='_blank'>$file</a></td>";
    echo "<td>";
    foreach ($includes as $include) {
        echo "<code>$include</code><br>";
    }
    echo "</td>";
    echo "<td>";
    foreach ($session_reads as $key => $count) {
        echo "<code>$key</code> ($count times)<br>";
    }
    echo "</td>";
    echo "<td>";
    foreach ($auth_calls as $call) {
        echo "<code>$call</code><br>";
    }
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

// Other admin pages
echo "<h3>Other Admin Pages Dependencies</h3>";
echo "<p>Session variables and auth functions used by other admin pages:</p>";

echo "<table>";
echo "<tr><th>Admin File</th><th>Session Vars Read</th><th>Auth Functions Used</th></tr>";

foreach ($other_admin_files as $file) {
    // Skip if already in dashboard files
    if (in_array($file, $dashboard_files)) continue;
    
    $session_reads = $session_reads_map[$file] ?? [];
    
    // Get auth function calls
    $auth_calls = [];
    foreach ($critical_functions as $function) {
        if (isset($function_calls_map[$file][$function]) && $function_calls_map[$file][$function] > 0) {
            $auth_calls[] = "$function() ({$function_calls_map[$file][$function]} times)";
        }
    }
    
    echo "<tr>";
    echo "<td><a href='$file' target='_blank'>$file</a></td>";
    echo "<td>";
    foreach ($session_reads as $key => $count) {
        echo "<code>$key</code> ($count times)<br>";
    }
    echo "</td>";
    echo "<td>";
    foreach ($auth_calls as $call) {
        echo "<code>$call</code><br>";
    }
    echo "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// Session Analysis Tab
echo "<div id='tab-session' class='tab-content'>";
echo "<h2>6. Session Variables Analysis</h2>";

// Collect all session variables
$all_session_vars = [];
foreach ($session_vars_map as $file => $vars) {
    foreach (array_keys($vars) as $var) {
        if (!isset($all_session_vars[$var])) {
            $all_session_vars[$var] = [];
        }
        $all_session_vars[$var][] = $file;
    }
}

// Collect all session reads
$all_session_reads = [];
foreach ($session_reads_map as $file => $vars) {
    foreach ($vars as $var => $count) {
        if (!isset($all_session_reads[$var])) {
            $all_session_reads[$var] = [];
        }
        $all_session_reads[$var][] = $file;
    }
}

// Session variables usage
echo "<h3>Session Variable Usage</h3>";
echo "<table>";
echo "<tr><th>Session Variable</th><th>Set By</th><th>Read By</th></tr>";

foreach (array_unique(array_merge(array_keys($all_session_vars), array_keys($all_session_reads))) as $var) {
    echo "<tr>";
    echo "<td><code>$var</code></td>";
    echo "<td>";
    if (isset($all_session_vars[$var])) {
        foreach ($all_session_vars[$var] as $file) {
            echo "$file<br>";
        }
    } else {
        echo "<span class='warning'>Not set in any file</span>";
    }
    echo "</td>";
    echo "<td>";
    if (isset($all_session_reads[$var])) {
        foreach ($all_session_reads[$var] as $file) {
            echo "$file<br>";
        }
    } else {
        echo "<span class='warning'>Not read in any file</span>";
    }
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

// Critical session variables for auth
echo "<h3>Critical Session Variables for Authentication</h3>";
$critical_vars = ['admin_logged_in', 'admin_id', 'admin_username', 'admin_role'];

echo "<p>These session variables are critical for the authentication system:</p>";
echo "<ul>";
foreach ($critical_vars as $var) {
    echo "<li><code>$var</code>";
    if (isset($all_session_vars[$var])) {
        echo " - Set by " . count($all_session_vars[$var]) . " files";
    } else {
        echo " - <span class='error'>Not set by any file</span>";
    }
    
    if (isset($all_session_reads[$var])) {
        echo ", Read by " . count($all_session_reads[$var]) . " files";
    } else {
        echo ", <span class='error'>Not read by any file</span>";
    }
    echo "</li>";
}
echo "</ul>";
echo "</div>";

// Function Analysis Tab
echo "<div id='tab-functions' class='tab-content'>";
echo "<h2>7. Function Usage Analysis</h2>";

// Most important functions
echo "<h3>Most Used Functions</h3>";

// Count function calls across all files
$function_usage = [];
foreach ($function_calls_map as $file => $calls) {
    foreach ($calls as $function => $count) {
        if (!isset($function_usage[$function])) {
            $function_usage[$function] = 0;
        }
        $function_usage[$function] += $count;
    }
}
arsort($function_usage);

echo "<table>";
echo "<tr><th>Function</th><th>Total Calls</th><th>Defined In</th></tr>";

$count = 0;
foreach ($function_usage as $function => $usage) {
    if ($usage < 2) continue; // Skip rarely used functions
    
    $defined_in = [];
    foreach ($function_defs_map as $file => $functions) {
        if (in_array($function, $functions)) {
            $defined_in[] = $file;
        }
    }
    
    echo "<tr>";
    echo "<td><code>$function()</code></td>";
    echo "<td>$usage</td>";
    echo "<td>" . (empty($defined_in) ? '<span class="warning">Not defined</span>' : implode("<br>", $defined_in)) . "</td>";
    echo "</tr>";
    
    $count++;
    if ($count >= 20) break; // Show only top 20
}

echo "</table>";

// Function definitions by file
echo "<h3>Function Definitions by File</h3>";
echo "<p>Files with significant function definitions:</p>";

$file_function_count = [];
foreach ($function_defs_map as $file => $functions) {
    $file_function_count[$file] = count($functions);
}
arsort($file_function_count);

echo "<table>";
echo "<tr><th>File</th><th># Functions</th><th>Key Functions</th></tr>";

$count = 0;
foreach ($file_function_count as $file => $func_count) {
    if ($func_count < 3) continue; // Skip files with few functions
    
    $functions = $function_defs_map[$file];
    $key_functions = [];
    
    // Identify key functions
    foreach ($functions as $function) {
        if (stripos($function, 'login') !== false || 
            stripos($function, 'auth') !== false || 
            stripos($function, 'admin') !== false || 
            stripos($function, 'session') !== false || 
            stripos($function, 'user') !== false) {
            $key_functions[] = $function;
        }
    }
    
    echo "<tr>";
    echo "<td><a href='$file' target='_blank'>$file</a></td>";
    echo "<td>$func_count</td>";
    echo "<td>";
    foreach ($key_functions as $function) {
        echo "<code>$function()</code><br>";
    }
    echo "</td>";
    echo "</tr>";
    
    $count++;
    if ($count >= 15) break; // Show only top 15
}

echo "</table>";
echo "</div>";

// Dependency Map Tab
echo "<div id='tab-map' class='tab-content'>";
echo "<h2>8. System Dependency Map</h2>";

// Print dependency tree for auth system
echo "<h3>Authentication System Dependency Tree</h3>";
echo "<div class='dependency-map'>";

function printTree($file, $dependency_tree, $indent = 0, $seen = []) {
    // Check for circular dependencies
    if (in_array($file, $seen)) {
        echo str_repeat("  ", $indent) . "$file <span class='error'>(circular dependency!)</span>\n";
        return;
    }
    
    echo str_repeat("  ", $indent) . "$file\n";
    
    if (isset($dependency_tree[$file])) {
        $new_seen = array_merge($seen, [$file]);
        foreach ($dependency_tree[$file] as $dep) {
            printTree($dep, $dependency_tree, $indent + 1, $new_seen);
        }
    }
}

// Start from auth files
foreach ($auth_files as $file) {
    printTree($file, $dependency_tree);
    echo "\n";
}

echo "</div>";

// Print login system dependency tree
echo "<h3>Login System Dependency Tree</h3>";
echo "<div class='dependency-map'>";

// Start from login files
foreach ($login_file_list as $file) {
    // Find HTML login files with form actions
    $content = $file_data[$file]['content'] ?? '';
    if (preg_match('/<form[^>]*action=["\']([^"\']*)["\'][^>]*>/i', $content, $matches)) {
        printTree($file, $dependency_tree);
        echo "\n";
    }
}

echo "</div>";

// Print dashboard dependency tree
echo "<h3>Dashboard Dependency Tree</h3>";
echo "<div class='dependency-map'>";

// Start from dashboard files
foreach ($dashboard_files as $file) {
    printTree($file, $dependency_tree);
    echo "\n";
}

echo "</div>";
echo "</div>";

// Solution Plan Tab
echo "<div id='tab-solution' class='tab-content'>";
echo "<h2>9. Complete Solution Plan</h2>";

// Session compatibility
$session_compatibility = true;
$incompatible_vars = [];

// Check if all-in-one solution sets all critical vars
foreach ($critical_vars as $var) {
    if (!isset($session_vars_map['/playground/complete-admin-login.php'][$var])) {
        $session_compatibility = false;
        $incompatible_vars[] = $var;
    }
}

// Check if all-in-one solution's vars match what admin pages expect
$used_session_vars = [];
foreach ($dashboard_files as $file) {
    $session_reads = $session_reads_map[$file] ?? [];
    foreach (array_keys($session_reads) as $var) {
        $used_session_vars[$var] = true;
    }
}

foreach ($other_admin_files as $file) {
    $session_reads = $session_reads_map[$file] ?? [];
    foreach (array_keys($session_reads) as $var) {
        $used_session_vars[$var] = true;
    }
}

foreach (array_keys($used_session_vars) as $var) {
    if (!isset($session_vars_map['/playground/complete-admin-login.php'][$var]) && 
        in_array($var, $critical_vars)) {
        $session_compatibility = false;
        $incompatible_vars[] = $var;
    }
}

// Auth function compatibility
$auth_compatibility = true;
$referenced_auth_functions = [];

// Check which auth functions are used by admin pages
foreach ($dashboard_files as $file) {
    foreach ($critical_functions as $function) {
        if (isset($function_calls_map[$file][$function]) && $function_calls_map[$file][$function] > 0) {
            $referenced_auth_functions[$function] = true;
        }
    }
}

foreach ($other_admin_files as $file) {
    foreach ($critical_functions as $function) {
        if (isset($function_calls_map[$file][$function]) && $function_calls_map[$file][$function] > 0) {
            $referenced_auth_functions[$function] = true;
        }
    }
}

// Find the auth functions that are actually needed
echo "<h3>System Analysis Results</h3>";

// Session compatibility
echo "<h4>Session Compatibility Check</h4>";
if ($session_compatibility) {
    echo "<p class='success'>The all-in-one login solution sets all session variables needed by the admin system.</p>";
} else {
    echo "<p class='warning'>The all-in-one login solution is missing these required session variables: " . implode(', ', array_unique($incompatible_vars)) . "</p>";
}

// Auth function compatibility
echo "<h4>Auth Function Compatibility Check</h4>";
echo "<p>Admin pages use these authentication functions:</p>";
echo "<ul>";
foreach (array_keys($referenced_auth_functions) as $function) {
    $defined_in = [];
    foreach ($function_defs_map as $file => $functions) {
        if (in_array($function, $functions)) {
            $defined_in[] = $file;
        }
    }
    
    echo "<li><code>$function()</code> - Defined in: " . implode(", ", $defined_in) . "</li>";
}
echo "</ul>";

// Configuration compatibility
echo "<h4>Configuration Compatibility Check</h4>";

// Find main database configuration
$main_db_config = [];
foreach ($config_file_list as $file) {
    $creds = $db_credentials_map[$file] ?? [];
    if (!empty($creds) && isset($creds['host']) && isset($creds['name'])) {
        $main_db_config = $creds;
        $main_config_file = $file;
        break;
    }
}

// Compare with all-in-one solution
$aio_creds = $db_credentials_map['/playground/complete-admin-login.php'] ?? [];

echo "<p>Primary database configuration from <code>$main_config_file</code>:</p>";
echo "<ul>";
echo "<li>Host: " . ($main_db_config['host'] ?? 'Not found') . "</li>";
echo "<li>Database: " . ($main_db_config['name'] ?? 'Not found') . "</li>";
echo "<li>Username: " . ($main_db_config['user'] ?? 'Not found') . "</li>";
echo "<li>Password: " . (isset($main_db_config['pass']) ? '******' : 'Not found') . "</li>";
echo "</ul>";

echo "<p>All-in-one solution configuration:</p>";
echo "<ul>";
echo "<li>Host: " . ($aio_creds['host'] ?? 'Not found') . "</li>";
echo "<li>Database: " . ($aio_creds['name'] ?? 'Not found') . "</li>";
echo "<li>Username: " . ($aio_creds['user'] ?? 'Not found') . "</li>";
echo "<li>Password: " . (isset($aio_creds['pass']) ? '******' : 'Not found') . "</li>";
echo "</ul>";

if (!empty($main_db_config) && !empty($aio_creds)) {
    $config_match = true;
    if ($main_db_config['host'] != $aio_creds['host']) $config_match = false;
    if ($main_db_config['name'] != $aio_creds['name']) $config_match = false;
    if ($main_db_config['user'] != $aio_creds['user']) $config_match = false;
    
    if ($config_match) {
        echo "<p class='success'>Database configurations match.</p>";
    } else {
        echo "<p class='warning'>Database configurations do not match. The all-in-one solution needs to be updated.</p>";
    }
}

// Final solution plan
echo "<h3>Final Solution Recommendations</h3>";

echo "<h4>Implementation Steps</h4>";
echo "<ol>";
echo "<li><strong>Update Database Credentials</strong> - Ensure the all-in-one login solution uses these credentials:";
echo "<pre class='code'>";
echo "\$db_host = '" . ($main_db_config['host'] ?? 'localhost') . "';\n";
echo "\$db_name = '" . ($main_db_config['name'] ?? 'bsg_support') . "';\n"; 
echo "\$db_user = '" . ($main_db_config['user'] ?? 'bsg_user') . "';\n";
echo "\$db_pass = '" . ($main_db_config['pass'] ?? 'password') . "';";
echo "</pre></li>";

echo "<li><strong>Session Variable Updates</strong> - ";
if (!$session_compatibility) {
    echo "Add these session variables to the all-in-one solution:";
    echo "<ul>";
    foreach (array_unique($incompatible_vars) as $var) {
        echo "<li><code>$var</code></li>";
    }
    echo "</ul>";
} else {
    echo "No changes needed, all required session variables are present.";
}
echo "</li>";

echo "<li><strong>File Changes</strong> - Execute these file operations:";
echo "<ul>";
echo "<li>Copy <code>/playground/complete-admin-login.php</code> to <code>/admin-login.php</code> (replace existing)</li>";
echo "<li>Rename <code>/admin-login.html</code> to <code>/admin-login.html.bak</code></li>";
echo "<li>Rename <code>/admin-login-process.php</code> to <code>/admin-login-process.php.bak</code></li>";
echo "</ul></li>";

echo "<li><strong>Testing Steps</strong>:";
echo "<ul>";
echo "<li>Clear browser cookies and cache</li>";
echo "<li>Navigate to <code>/admin-login.php</code></li>";
echo "<li>Log in with admin credentials</li>";
echo "<li>Verify successful redirect to dashboard</li>";
echo "<li>Check various admin pages for functionality</li>";
echo "</ul></li>";
echo "</ol>";

echo "<h4>Dependencies to Preserve</h4>";
echo "<p>The following files are critical to the admin system and must remain in place:</p>";
echo "<ul>";
foreach ($auth_files as $file) {
    echo "<li><code>$file</code> - Contains authentication functions</li>";
}
foreach ($config_file_list as $file) {
    echo "<li><code>$file</code> - Contains database configuration</li>";
}
echo "</ul>";

echo "</div>";

echo "</body></html>";
?>
