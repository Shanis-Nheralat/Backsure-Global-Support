<?php
/**
 * Advanced Admin Files Diagnostic Tool
 * This tool scans your system to find all admin-related files, their relationships,
 * and potential conflicts
 */

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<html><head><title>Admin Files Diagnostic</title>";
echo "<style>
    body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 1200px; margin: 0 auto; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    .section { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
    pre { background: #f5f5f5; padding: 10px; overflow: auto; border-radius: 4px; }
    code { background: #f5f5f5; padding: 2px 5px; border-radius: 3px; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .file-map { font-family: monospace; white-space: pre; }
</style>";
echo "</head><body>";

echo "<h1>Advanced Admin Files Diagnostic</h1>";
echo "<p>Generated on: " . date('Y-m-d H:i:s') . "</p>";

// SECTION 1: Find All Admin Files
echo "<div class='section'>";
echo "<h2>1. Admin File Inventory</h2>";

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

// Get document root
$doc_root = $_SERVER['DOCUMENT_ROOT'];

// Find admin-related files
$admin_php_files = findFiles($doc_root, '*admin*.php');
$admin_html_files = findFiles($doc_root, '*admin*.html');
$login_php_files = findFiles($doc_root, '*login*.php');
$login_html_files = findFiles($doc_root, '*login*.html');

// Combine and deduplicate
$all_admin_files = array_unique(array_merge($admin_php_files, $admin_html_files, $login_php_files, $login_html_files));
sort($all_admin_files);

echo "<h3>Found " . count($all_admin_files) . " admin-related files:</h3>";
echo "<table>";
echo "<tr><th>File</th><th>Size</th><th>Last Modified</th><th>Type</th></tr>";

foreach ($all_admin_files as $file) {
    $rel_path = str_replace($doc_root, '', $file);
    $size = filesize($file);
    $modified = date('Y-m-d H:i:s', filemtime($file));
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $is_login = stripos($file, 'login') !== false;
    $is_dashboard = stripos($file, 'dashboard') !== false;
    $is_auth = stripos($file, 'auth') !== false;
    $is_process = stripos($file, 'process') !== false;
    
    $type = "";
    if ($is_login) $type .= "Login ";
    if ($is_dashboard) $type .= "Dashboard ";
    if ($is_auth) $type .= "Auth ";
    if ($is_process) $type .= "Process ";
    
    echo "<tr>";
    echo "<td><a href='$rel_path' target='_blank'>$rel_path</a></td>";
    echo "<td>" . number_format($size / 1024, 2) . " KB</td>";
    echo "<td>$modified</td>";
    echo "<td>$type</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// SECTION 2: File Content Analysis
echo "<div class='section'>";
echo "<h2>2. File Content Analysis</h2>";

echo "<h3>Login Flow Analysis:</h3>";

// Files to analyze in detail
$login_files = [];
foreach ($all_admin_files as $file) {
    if (stripos($file, 'login') !== false) {
        $login_files[] = $file;
    }
}

echo "<table>";
echo "<tr><th>File</th><th>Form Action</th><th>Session Variables</th><th>Includes</th><th>Database</th></tr>";

foreach ($login_files as $file) {
    $rel_path = str_replace($doc_root, '', $file);
    $content = file_get_contents($file);
    
    // Extract form action
    $form_action = "N/A";
    if (preg_match('/<form[^>]*action=["\']([^"\']*)["\'][^>]*>/i', $content, $matches)) {
        $form_action = $matches[1];
    }
    
    // Check for session variables
    $session_vars = [];
    if (preg_match_all('/\$_SESSION\[[\'"]([^\'"]+)[\'"]\]\s*=\s*([^;]+);/i', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $session_vars[] = $match[1] . ' = ' . trim($match[2]);
        }
    }
    
    // Check for includes/requires
    $includes = [];
    if (preg_match_all('/(include|require)(?:_once)?\s*\(?\s*[\'"]([^\'"]+)[\'"]\s*\)?;/i', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $includes[] = $match[2];
        }
    }
    
    // Check for database access
    $has_db = "No";
    if (stripos($content, 'mysqli') !== false || 
        stripos($content, 'PDO') !== false || 
        stripos($content, 'mysql_') !== false) {
        $has_db = "Yes";
    }
    
    echo "<tr>";
    echo "<td>$rel_path</td>";
    echo "<td>$form_action</td>";
    echo "<td>" . implode("<br>", $session_vars) . "</td>";
    echo "<td>" . implode("<br>", $includes) . "</td>";
    echo "<td>$has_db</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>Auth Flow Analysis:</h3>";

// Files to analyze for auth
$auth_files = [];
foreach ($all_admin_files as $file) {
    if (stripos($file, 'auth') !== false || stripos($file, 'admin-dash') !== false) {
        $auth_files[] = $file;
    }
}

echo "<table>";
echo "<tr><th>File</th><th>Auth Functions</th><th>Session Checks</th><th>Redirects</th><th>Includes</th></tr>";

foreach ($auth_files as $file) {
    $rel_path = str_replace($doc_root, '', $file);
    $content = file_get_contents($file);
    
    // Extract auth functions
    $auth_functions = [];
    if (preg_match_all('/function\s+([a-zA-Z0-9_]+auth[a-zA-Z0-9_]*|is_[a-zA-Z0-9_]*logged[a-zA-Z0-9_]*|require_[a-zA-Z0-9_]*role[a-zA-Z0-9_]*)\s*\(/i', $content, $matches)) {
        $auth_functions = $matches[1];
    }
    
    // Check for session checks
    $session_checks = [];
    if (preg_match_all('/\$_SESSION\[[\'"]([^\'"]+)[\'"]\]/', $content, $matches)) {
        $session_checks = array_unique($matches[1]);
    }
    
    // Check for redirects
    $redirects = [];
    if (preg_match_all('/header\s*\(\s*[\'"](Location|Refresh)[\'"]:\s*([^;]+);/i', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $redirects[] = trim($match[2]);
        }
    }
    
    // Check for includes/requires
    $includes = [];
    if (preg_match_all('/(include|require)(?:_once)?\s*\(?\s*[\'"]([^\'"]+)[\'"]\s*\)?;/i', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $includes[] = $match[2];
        }
    }
    
    echo "<tr>";
    echo "<td>$rel_path</td>";
    echo "<td>" . implode("<br>", $auth_functions) . "</td>";
    echo "<td>" . implode("<br>", $session_checks) . "</td>";
    echo "<td>" . implode("<br>", $redirects) . "</td>";
    echo "<td>" . implode("<br>", $includes) . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// SECTION 3: File Relationship Map
echo "<div class='section'>";
echo "<h2>3. File Relationship Map</h2>";

// Build dependency graph
$dependencies = [];
foreach ($all_admin_files as $file) {
    $rel_path = str_replace($doc_root, '', $file);
    $content = file_get_contents($file);
    
    // Find all includes and requires
    $deps = [];
    if (preg_match_all('/(include|require)(?:_once)?\s*\(?\s*[\'"]([^\'"]+)[\'"]\s*\)?;/i', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $deps[] = $match[2];
        }
    }
    
    // Find form actions
    if (preg_match('/<form[^>]*action=["\']([^"\']*)["\'][^>]*>/i', $content, $matches)) {
        $action = $matches[1];
        if (!empty($action) && $action != '#' && $action != '') {
            $deps[] = $action;
        }
    }
    
    // Find redirects
    if (preg_match_all('/header\s*\(\s*[\'"]Location:\s*([^;]+)/i', $content, $matches)) {
        foreach ($matches[1] as $redirect) {
            $redirect = trim($redirect);
            $redirect = trim($redirect, '\'"');
            if (!empty($redirect) && strpos($redirect, '?') !== 0) {  // Ignore query string only redirects
                $deps[] = $redirect;
            }
        }
    }
    
    $dependencies[$rel_path] = $deps;
}

// Display dependencies as a tree
echo "<h3>File Dependencies:</h3>";
echo "<div class='file-map'>";

// Entry points - files that are not included by any other files
$entry_points = array_keys($dependencies);
foreach ($dependencies as $file => $deps) {
    foreach ($deps as $dep) {
        foreach ($dependencies as $other_file => $other_deps) {
            if (basename($dep) == basename($other_file) || $dep == $other_file) {
                $entry_points = array_diff($entry_points, [$other_file]);
            }
        }
    }
}

// Print the dependency tree
function printTree($file, $dependencies, $indent = 0, $seen = []) {
    $rel_file = $file;
    
    // Check for circular dependencies
    if (in_array($rel_file, $seen)) {
        echo str_repeat("  ", $indent) . "$rel_file <span class='error'>(circular dependency!)</span>\n";
        return;
    }
    
    echo str_repeat("  ", $indent) . "$rel_file\n";
    
    if (isset($dependencies[$rel_file])) {
        $new_seen = array_merge($seen, [$rel_file]);
        foreach ($dependencies[$rel_file] as $dep) {
            // Check if dependency exists in the list
            $found = false;
            foreach (array_keys($dependencies) as $existing_file) {
                if (basename($dep) == basename($existing_file) || $dep == $existing_file) {
                    printTree($existing_file, $dependencies, $indent + 1, $new_seen);
                    $found = true;
                    break;
                }
            }
            
            if (!$found && !empty($dep)) {
                echo str_repeat("  ", $indent + 1) . "$dep <span class='warning'>(external or missing)</span>\n";
            }
        }
    }
}

// Print the dependency tree for each entry point
foreach ($entry_points as $entry) {
    printTree($entry, $dependencies);
    echo "\n";
}

echo "</div>";
echo "</div>";

// SECTION 4: Database Tables
echo "<div class='section'>";
echo "<h2>4. Database Tables Check</h2>";

// Try to connect to database
$db_connected = false;
$db_tables = [];

// Look for database credentials in the files
$db_config = [];
foreach ($all_admin_files as $file) {
    $content = file_get_contents($file);
    
    // Check for database credentials
    if (preg_match('/(?:host|hostname|server)\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
        $db_config['host'] = $matches[1];
    }
    
    if (preg_match('/(?:dbname|database|db)\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
        $db_config['dbname'] = $matches[1];
    }
    
    if (preg_match('/(?:username|user|dbuser)\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
        $db_config['user'] = $matches[1];
    }
    
    if (preg_match('/(?:password|pass|dbpass)\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
        $db_config['pass'] = $matches[1];
    }
    
    // If we have all credentials, break
    if (isset($db_config['host']) && isset($db_config['dbname']) && isset($db_config['user']) && isset($db_config['pass'])) {
        break;
    }
}

echo "<h3>Database Configuration Found:</h3>";
echo "<ul>";
foreach ($db_config as $key => $value) {
    if ($key == 'pass') {
        echo "<li>$key: <span class='warning'>(hidden for security)</span></li>";
    } else {
        echo "<li>$key: $value</li>";
    }
}
echo "</ul>";

echo "<p>To check database tables, please enter your database credentials:</p>";
echo "<form method='post' action=''>";
echo "<input type='hidden' name='action' value='check_db'>";
echo "<table>";
echo "<tr><td>Host:</td><td><input type='text' name='db_host' value='" . ($db_config['host'] ?? 'localhost') . "'></td></tr>";
echo "<tr><td>Database:</td><td><input type='text' name='db_name' value='" . ($db_config['dbname'] ?? '') . "'></td></tr>";
echo "<tr><td>Username:</td><td><input type='text' name='db_user' value='" . ($db_config['user'] ?? '') . "'></td></tr>";
echo "<tr><td>Password:</td><td><input type='password' name='db_pass' value='" . ($db_config['pass'] ?? '') . "'></td></tr>";
echo "</table>";
echo "<button type='submit'>Check Database Tables</button>";
echo "</form>";

// Process form if submitted
if (isset($_POST['action']) && $_POST['action'] == 'check_db') {
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    
    try {
        // Try to connect to the database
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p class='success'>Database connection successful!</p>";
        $db_connected = true;
        
        // Get list of tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h3>Tables Found:</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table ";
            
            // Check if it's an admin-related table
            if (stripos($table, 'admin') !== false || stripos($table, 'user') !== false) {
                echo "<span class='info'>(Likely admin-related)</span>";
                
                // Check table structure
                $stmt = $pdo->query("DESCRIBE $table");
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Check for role column
                $has_role = false;
                foreach ($columns as $column) {
                    if ($column['Field'] == 'role') {
                        $has_role = true;
                        echo " - Has role column: " . $column['Type'];
                        
                        // Check for superadmin value in role
                        if (stripos($column['Type'], 'enum') !== false) {
                            preg_match("/enum\(\'(.*)\'\)/", $column['Type'], $matches);
                            if (isset($matches[1])) {
                                $enum_values = explode("','", $matches[1]);
                                if (in_array('superadmin', $enum_values)) {
                                    echo " <span class='success'>(includes 'superadmin')</span>";
                                } else {
                                    echo " <span class='warning'>(does not include 'superadmin')</span>";
                                }
                            }
                        }
                    }
                }
                
                if (!$has_role) {
                    echo " <span class='warning'>(no role column found)</span>";
                }
                
                // Check for sample user data
                $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
                $count = $stmt->fetchColumn();
                echo " - Contains $count records";
                
                // Show superadmin users
                if ($has_role) {
                    $stmt = $pdo->prepare("SELECT * FROM $table WHERE role = 'superadmin'");
                    $stmt->execute();
                    $superadmins = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($superadmins)) {
                        echo " <span class='success'>(found superadmin users)</span>";
                    } else {
                        echo " <span class='warning'>(no superadmin users found)</span>";
                        
                        // Check if username col exists
                        $has_username = false;
                        foreach ($columns as $column) {
                            if ($column['Field'] == 'username') {
                                $has_username = true;
                                break;
                            }
                        }
                        
                        if ($has_username) {
                            // Get and show some usernames
                            $stmt = $pdo->query("SELECT username, role FROM $table LIMIT 5");
                            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            echo "<ul>";
                            foreach ($users as $user) {
                                echo "<li>Username: " . htmlspecialchars($user['username']) . ", Role: " . htmlspecialchars($user['role'] ?? 'None') . "</li>";
                            }
                            echo "</ul>";
                        }
                    }
                }
            }
            
            echo "</li>";
        }
        echo "</ul>";
        
    } catch (PDOException $e) {
        echo "<p class='error'>Database connection failed: " . $e->getMessage() . "</p>";
    }
}

echo "</div>";

// SECTION 5: Session Status
echo "<div class='section'>";
echo "<h2>5. Current Session Status</h2>";

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h3>Current Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Check login status
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    echo "<p class='success'>Session shows you are logged in</p>";
    
    // Check for essential session variables
    $missing = [];
    $required_vars = ['admin_id', 'admin_username', 'admin_role'];
    
    foreach ($required_vars as $var) {
        if (!isset($_SESSION[$var]) || empty($_SESSION[$var])) {
            $missing[] = $var;
        }
    }
    
    if (!empty($missing)) {
        echo "<p class='warning'>Missing required session variables: " . implode(', ', $missing) . "</p>";
    } else {
        echo "<p class='success'>All required session variables are present</p>";
    }
    
} else {
    echo "<p class='info'>Session shows you are not logged in</p>";
}

echo "</div>";

// SECTION 6: Redundant Files Analysis
echo "<div class='section'>";
echo "<h2>6. Redundant Files Analysis</h2>";

// Check for identical or very similar files
$file_content_hashes = [];
foreach ($all_admin_files as $file) {
    $rel_path = str_replace($doc_root, '', $file);
    $content = file_get_contents($file);
    
    // Generate a hash of the content
    $hash = md5($content);
    
    // Store the hash
    if (!isset($file_content_hashes[$hash])) {
        $file_content_hashes[$hash] = [];
    }
    $file_content_hashes[$hash][] = $rel_path;
}

// Find files with the same content
$duplicates = [];
foreach ($file_content_hashes as $hash => $files) {
    if (count($files) > 1) {
        $duplicates[$hash] = $files;
    }
}

if (!empty($duplicates)) {
    echo "<h3>Potential Duplicate Files:</h3>";
    echo "<ul>";
    foreach ($duplicates as $hash => $files) {
        echo "<li>The following files appear to be identical:<ul>";
        foreach ($files as $file) {
            echo "<li>$file</li>";
        }
        echo "</ul></li>";
    }
    echo "</ul>";
} else {
    echo "<p>No duplicate files found.</p>";
}

// Check for redundant login/auth files
$login_file_types = [
    'login_html' => [],
    'login_php' => [],
    'login_process' => [],
    'auth_php' => []
];

foreach ($all_admin_files as $file) {
    $rel_path = str_replace($doc_root, '', $file);
    $filename = basename($file);
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    
    if (stripos($filename, 'login') !== false) {
        if ($ext == 'html') {
            $login_file_types['login_html'][] = $rel_path;
        } else if ($ext == 'php') {
            if (stripos($filename, 'process') !== false) {
                $login_file_types['login_process'][] = $rel_path;
            } else {
                $login_file_types['login_php'][] = $rel_path;
            }
        }
    } else if (stripos($filename, 'auth') !== false && $ext == 'php') {
        $login_file_types['auth_php'][] = $rel_path;
    }
}

echo "<h3>Potentially Redundant Authentication Files:</h3>";
echo "<ul>";
foreach ($login_file_types as $type => $files) {
    if (count($files) > 1) {
        echo "<li class='warning'>Multiple $type files found (" . count($files) . "):<ul>";
        foreach ($files as $file) {
            echo "<li>$file</li>";
        }
        echo "</ul></li>";
    } else if (count($files) == 1) {
        echo "<li class='success'>One $type file found: " . $files[0] . "</li>";
    } else {
        echo "<li class='info'>No $type files found.</li>";
    }
}
echo "</ul>";

// Identify unused files
$used_files = [];
foreach ($dependencies as $file => $deps) {
    foreach ($deps as $dep) {
        foreach (array_keys($dependencies) as $existing_file) {
            if (basename($dep) == basename($existing_file) || $dep == $existing_file) {
                $used_files[] = $existing_file;
            }
        }
    }
}

// Files not used by any other file
$unused_files = array_diff(array_keys($dependencies), $used_files);

if (!empty($unused_files)) {
    echo "<h3>Files Not Used by Any Other File:</h3>";
    echo "<ul>";
    foreach ($unused_files as $file) {
        // Determine if it's a potential entry point
        $is_entry = false;
        if (stripos($file, 'login') !== false || 
            stripos($file, 'index') !== false || 
            stripos($file, 'dashboard') !== false) {
            $is_entry = true;
        }
        
        if ($is_entry) {
            echo "<li class='info'>$file <span class='info'>(likely an entry point)</span></li>";
        } else {
            echo "<li class='warning'>$file <span class='warning'>(may be unused)</span></li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>All files appear to be used by other files.</p>";
}

echo "</div>";

// SECTION 7: Recommendations
echo "<div class='section'>";
echo "<h2>7. Analysis and Recommendations</h2>";

echo "<h3>Identified Issues:</h3>";
echo "<ul>";

// Multiple login files
if (count($login_file_types['login_html']) > 0 && count($login_file_types['login_php']) > 0) {
    echo "<li class='warning'>You have both HTML and PHP login files, which can cause conflicts</li>";
}

if (count($login_file_types['login_process']) > 1) {
    echo "<li class='warning'>Multiple login processing files can cause session inconsistencies</li>";
}

// Check for circular dependencies
$has_circular = false;
foreach ($dependencies as $file => $deps) {
    foreach ($deps as $dep) {
        foreach ($dependencies as $other_file => $other_deps) {
            if ((basename($dep) == basename($other_file) || $dep == $other_file) && 
                in_array($file, $other_deps) || in_array(basename($file), $other_deps)) {
                echo "<li class='error'>Circular dependency detected between $file and $other_file</li>";
                $has_circular = true;
            }
        }
    }
}

// Missing role in database
if (isset($has_role) && !$has_role) {
    echo "<li class='error'>No role column found in user table</li>";
} else if (isset($has_role) && $has_role && empty($superadmins)) {
    echo "<li class='warning'>No superadmin users found in database</li>";
}

// Missing session variables
if (isset($missing) && !empty($missing)) {
    echo "<li class='warning'>Missing required session variables: " . implode(', ', $missing) . "</li>";
}

// Duplicate files
if (!empty($duplicates)) {
    echo "<li class='warning'>Duplicate files detected, which can cause confusion</li>";
}

echo "</ul>";

echo "<h3>Recommended Clean-up Actions:</h3>";
echo "<ol>";

// Consolidate login files
if (count($login_file_types['login_html']) > 0 && count($login_file_types['login_php']) > 0) {
    echo "<li class='warning'><strong>Consolidate login files:</strong>";
    echo "<ul>";
    echo "<li>Keep only the PHP login file: " . implode(', ', $login_file_types['login_php']) . "</li>";
    echo "<li>Rename or delete HTML login files: " . implode(', ', $login_file_types['login_html']) . "</li>";
    echo "</ul></li>";
}

// Consolidate login process files
if (count($login_file_types['login_process']) > 1) {
    echo "<li class='warning'><strong>Consolidate login process files:</strong>";
    echo "<ul>";
    echo "<li>Keep only one process file and update form actions to point to it</li>";
    echo "<li>Files to consolidate: " . implode(', ', $login_file_types['login_process']) . "</li>";
    echo "</ul></li>";
}

// Fix database roles
if (isset($has_role) && !$has_role) {
    echo "<li class='error'><strong>Add role column to user table:</strong>";
    echo "<ul>";
    echo "<li>Run SQL: ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'admin';</li>";
    echo "</ul></li>";
} else if (isset($has_role) && $has_role && empty($superadmins)) {
    echo "<li class='warning'><strong>Add superadmin role:</strong>";
    echo "<ul>";
    echo "<li>Run SQL: UPDATE users SET role = 'superadmin' WHERE username = 'shanisbsg';</li>";
    echo "</ul></li>";
}

// Remove duplicate files
if (!empty($duplicates)) {
    echo "<li class='warning'><strong>Remove duplicate files:</strong>";
    echo "<ul>";
    foreach ($duplicates as $hash => $files) {
        echo "<li>Keep one of these and remove the rest: " . implode(', ', $files) . "</li>";
    }
    echo "</ul></li>";
}

// Use the complete login solution
echo "<li class='success'><strong>Implement all-in-one login solution:</strong>";
echo "<ul>";
echo "<li>Replace your current login files with the complete solution you've already prepared</li>";
echo "<li>Make sure to update database credentials in the script</li>";
echo "<li>Delete or rename any conflicting login files</li>";
echo "</ul></li>";

echo "</ol>";

echo "<h3>Primary Solution:</h3>";
echo "<p>Based on the analysis, the most direct solution is:</p>";
echo "<ol>";
echo "<li>Replace your current admin-login.php with the all-in-one solution</li>";
echo "<li>Update the database credentials in the new file</li>";
echo "<li>Rename admin-login.html to admin-login.html.bak</li>";
echo "<li>Rename admin-login-process.php to admin-login-process.php.bak</li>";
echo "<li>Test the new login system</li>";
echo "</ol>";

// If database credentials are found
if (!empty($db_config)) {
    echo "<p class='info'>Use these database credentials in your new login file:</p>";
    echo "<pre>";
    echo "\$db_host = '" . ($db_config['host'] ?? 'localhost') . "';\n";
    echo "\$db_name = '" . ($db_config['dbname'] ?? '') . "';\n";
    echo "\$db_user = '" . ($db_config['user'] ?? '') . "';\n";
    echo "\$db_pass = '" . ($db_config['pass'] ?? '') . "';";
    echo "</pre>";
}

echo "</div>";

echo "</body></html>";
?>
