<?php
// path-tester.php - Save this as a new file in your playground directory
// This script tests various path configurations to determine what works in your environment

// Set error reporting to maximum to see all issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Current timestamp to prevent caching
$timestamp = time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Path Testing Tool</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 1200px; margin: 0 auto; }
        h1, h2 { color: #333; }
        h2 { margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .test-container { background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 30px; }
        .test-block { background: white; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .success { color: green; font-weight: bold; }
        .failure { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .info { color: blue; }
        .test-item { display: flex; margin-bottom: 10px; }
        .test-label { width: 300px; flex-shrink: 0; }
        .test-result { flex-grow: 1; }
        .test-box { width: 100px; height: 50px; background: #eee; display: flex; align-items: center; justify-content: center; margin: 10px 0; }
        code { background: #f5f5f5; padding: 2px 4px; border-radius: 3px; font-family: monospace; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        th { background: #f5f5f5; }
        .path-tester { display: flex; gap: 10px; align-items: center; margin-bottom: 10px; }
        .path-input { flex-grow: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0069d9; }
        #urlTestResult { margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Path Testing Tool</h1>
    
    <div class="test-container">
        <h2>1. Server Information</h2>
        <div class="test-block">
            <?php
            $server_info = [
                'PHP Version' => phpversion(),
                'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'Document Root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
                'Script Filename' => $_SERVER['SCRIPT_FILENAME'] ?? 'Unknown',
                'Script Name' => $_SERVER['SCRIPT_NAME'] ?? 'Unknown',
                'Request URI' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
                'HTTP Host' => $_SERVER['HTTP_HOST'] ?? 'Unknown',
                'Current File' => __FILE__,
                'Current Directory' => __DIR__,
                'Parent Directory' => dirname(__DIR__),
            ];
            
            foreach ($server_info as $key => $value) {
                echo "<div class='test-item'>";
                echo "<div class='test-label'><strong>$key:</strong></div>";
                echo "<div class='test-result'>$value</div>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
    
    <div class="test-container">
        <h2>2. Path Construction Tests</h2>
        <div class="test-block">
            <p>This section shows how different path constructions resolve in your environment.</p>
            
            <?php
            // Get the directory name from the current script path
            $dirName = basename(dirname($_SERVER['SCRIPT_NAME']));
            $scriptPath = $_SERVER['SCRIPT_NAME'];
            $parentDir = dirname($scriptPath);
            $baseUrl = rtrim($parentDir, '/') . '/';
            if ($dirName == '') $dirName = 'root';
            
            $path_constructions = [
                'No Slash (Current Directory)' => 'admin-core.css',
                'Leading Slash (Site Root)' => '/admin-core.css',
                'Directory + File' => "$dirName/admin-core.css",
                'Parent + Directory + File' => "$parentDir/admin-core.css",
                'Full URL with Host' => 'https://' . $_SERVER['HTTP_HOST'] . $scriptPath,
                'PHP dirname(__FILE__)' => dirname(__FILE__) . '/admin-core.css',
                'Parent Directory' => '../admin-core.css',
                'Path from DOCUMENT_ROOT' => str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__) . '/admin-core.css',
                'Using baseUrl' => $baseUrl . 'admin-core.css',
            ];
            
            foreach ($path_constructions as $desc => $path) {
                echo "<div class='test-item'>";
                echo "<div class='test-label'><strong>$desc:</strong></div>";
                echo "<div class='test-result'><code>$path</code></div>";
                echo "</div>";
            }
            ?>
            
            <div style="margin-top: 20px;">
                <p><strong>Constructed Base URL:</strong> <code><?php echo $baseUrl; ?></code></p>
                <p>This URL is constructed from the script path and can be used as a base for all your relative URLs.</p>
            </div>
        </div>
    </div>
    
    <div class="test-container">
        <h2>3. File Existence Tests</h2>
        <div class="test-block">
            <p>Testing if files exist using different path constructions.</p>
            
            <table>
                <tr>
                    <th>Path Construction</th>
                    <th>Result</th>
                </tr>
                <?php
                $test_files = ['admin-core.css', 'admin-themes.css', 'admin-core.js', 'admin-dashboard.php'];
                $path_prefixes = [
                    'No Prefix (Current Dir)' => '',
                    'Leading Slash' => '/',
                    'Parent Dir' => '../',
                    'Full Server Path' => $_SERVER['DOCUMENT_ROOT'] . '/',
                    'Script Dir Path' => __DIR__ . '/',
                    'Constructed Base Path' => rtrim($_SERVER['DOCUMENT_ROOT'] . $baseUrl, '/').'/',
                ];
                
                foreach ($test_files as $file) {
                    echo "<tr><td colspan='2' style='background:#eee;'><strong>Testing File: $file</strong></td></tr>";
                    
                    foreach ($path_prefixes as $desc => $prefix) {
                        $full_path = $prefix . $file;
                        $exists = file_exists($full_path);
                        $readable = is_readable($full_path);
                        
                        echo "<tr>";
                        echo "<td>$desc<br><code>$full_path</code></td>";
                        
                        if ($exists && $readable) {
                            echo "<td class='success'>File exists and is readable";
                            if ($prefix === '') {
                                echo "<br>Size: " . filesize($full_path) . " bytes";
                                echo "<br>Modified: " . date("Y-m-d H:i:s", filemtime($full_path));
                            }
                            echo "</td>";
                        } else if ($exists && !$readable) {
                            echo "<td class='warning'>File exists but is not readable!</td>";
                        } else {
                            echo "<td class='failure'>File not found!</td>";
                        }
                        
                        echo "</tr>";
                    }
                }
                ?>
            </table>
        </div>
    </div>
    
    <div class="test-container">
        <h2>4. Dynamic URL Tester</h2>
        <div class="test-block">
            <p>Test if a specific resource URL is accessible:</p>
            
            <div class="path-tester">
                <input type="text" id="resourceUrl" class="path-input" value="admin-core.css" placeholder="Enter resource path (e.g., admin-core.css)">
                <button onclick="testResourceUrl()">Test URL</button>
            </div>
            
            <div id="urlTestResult"></div>
            
            <script>
                function testResourceUrl() {
                    const url = document.getElementById('resourceUrl').value;
                    const resultDiv = document.getElementById('urlTestResult');
                    
                    // Create an image element for testing (works for CSS too due to 404 behavior)
                    const tester = new Image();
                    resultDiv.innerHTML = '<p>Testing URL: <code>' + url + '</code>...</p>';
                    
                    // Test using HEAD request with fetch API
                    fetch(url, { method: 'HEAD' })
                        .then(response => {
                            if (response.ok) {
                                resultDiv.innerHTML += '<p class="success">Resource is accessible! Status: ' + response.status + '</p>';
                            } else {
                                resultDiv.innerHTML += '<p class="failure">Resource returned an error! Status: ' + response.status + '</p>';
                            }
                        })
                        .catch(error => {
                            resultDiv.innerHTML += '<p class="failure">Error testing resource: ' + error.message + '</p>';
                        });
                }
            </script>
        </div>
    </div>
    
    <div class="test-container">
        <h2>5. CSS Test</h2>
        <div class="test-block">
            <p>Testing different CSS file inclusion methods:</p>
            
            <h3>1. Relative path (no slash)</h3>
            <link id="css-test-1" rel="stylesheet" href="admin-core.css?v=<?php echo $timestamp; ?>">
            <div class="test-box css-test-box-1">Test Box 1</div>
            
            <h3>2. Root-relative path (with slash)</h3>
            <link id="css-test-2" rel="stylesheet" href="/admin-core.css?v=<?php echo $timestamp; ?>">
            <div class="test-box css-test-box-2">Test Box 2</div>
            
            <h3>3. Directory-specific path</h3>
            <link id="css-test-3" rel="stylesheet" href="<?php echo $baseUrl; ?>admin-core.css?v=<?php echo $timestamp; ?>">
            <div class="test-box css-test-box-3">Test Box 3</div>
            
            <script>
                window.addEventListener('load', function() {
                    // Test CSS loading for each method
                    testCssLoading('css-test-1', 'css-test-box-1', '1. Relative path');
                    testCssLoading('css-test-2', 'css-test-box-2', '2. Root-relative path');
                    testCssLoading('css-test-3', 'css-test-box-3', '3. Directory-specific path');
                });
                
                function testCssLoading(linkId, boxId, methodName) {
                    const box = document.querySelector('.' + boxId);
                    const style = window.getComputedStyle(box);
                    
                    // Create result element
                    const result = document.createElement('div');
                    
                    // Check if styles from admin-core.css are applied
                    // We'll look for non-default background color or border which would indicate styling
                    if (style.backgroundColor !== 'rgb(238, 238, 238)' || 
                        style.border !== '1px solid rgb(221, 221, 221)') {
                        result.innerHTML = '<span class="success">SUCCESS: ' + methodName + ' is working!</span>';
                    } else {
                        result.innerHTML = '<span class="failure">FAILURE: ' + methodName + ' not loading CSS!</span>';
                    }
                    
                    box.after(result);
                }
            </script>
        </div>
    </div>
    
    <div class="test-container">
        <h2>6. JavaScript Test</h2>
        <div class="test-block">
            <p>Testing different JS file inclusion methods:</p>
            
            <h3>1. Relative path (no slash)</h3>
            <button onclick="testJsMethod('js-result-1', 'admin-core.js')">Test Relative Path JS</button>
            <div id="js-result-1" class="test-result"></div>
            
            <h3>2. Root-relative path (with slash)</h3>
            <button onclick="testJsMethod('js-result-2', '/admin-core.js')">Test Root-Relative Path JS</button>
            <div id="js-result-2" class="test-result"></div>
            
            <h3>3. Directory-specific path</h3>
            <button onclick="testJsMethod('js-result-3', '<?php echo $baseUrl; ?>admin-core.js')">Test Directory Path JS</button>
            <div id="js-result-3" class="test-result"></div>
            
            <script>
                function testJsMethod(resultId, jsPath) {
                    const resultElement = document.getElementById(resultId);
                    resultElement.innerHTML = '<p>Testing JS file: <code>' + jsPath + '</code>...</p>';
                    
                    // Create script element
                    const script = document.createElement('script');
                    script.src = jsPath + '?v=<?php echo $timestamp; ?>';
                    
                    // Handle loading success
                    script.onload = function() {
                        // Test if core functions exist
                        if (typeof initSidebar === 'function') {
                            resultElement.innerHTML += '<p class="success">SUCCESS: JavaScript loaded and functions available!</p>';
                        } else {
                            resultElement.innerHTML += '<p class="warning">WARNING: JavaScript loaded but expected functions not found!</p>';
                        }
                    };
                    
                    // Handle loading error
                    script.onerror = function() {
                        resultElement.innerHTML += '<p class="failure">FAILURE: Could not load JavaScript file!</p>';
                    };
                    
                    // Add to document
                    document.head.appendChild(script);
                }
            </script>
        </div>
    </div>
    
    <div class="test-container">
        <h2>7. Recommended Fix</h2>
        <div class="test-block">
            <h3>Based on the tests above, here's the recommended code for your admin-head.php:</h3>
            <pre><code>
&lt;!DOCTYPE html&gt;
&lt;html lang="en"&gt;
&lt;head&gt;
  &lt;meta charset="UTF-8"&gt;
  &lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;
  &lt;title&gt;&lt;?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Admin Panel'; ?&gt; | Backsure Global Support&lt;/title&gt;
  
  &lt;!-- Get the base URL dynamically --&gt;
  &lt;?php
  // Base URL construction
  $scriptPath = $_SERVER['SCRIPT_NAME'];
  $parentDir = dirname($scriptPath);
  $baseUrl = rtrim($parentDir, '/') . '/';
  ?&gt;
  
  &lt;!-- Favicon --&gt;
  &lt;link rel="shortcut icon" href="&lt;?php echo $baseUrl; ?&gt;assets/images/favicon.ico" type="image/x-icon"&gt;
  
  &lt;!-- Font Awesome --&gt;
  &lt;link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"&gt;
  
  &lt;!-- Google Fonts --&gt;
  &lt;link rel="preconnect" href="https://fonts.googleapis.com"&gt;
  &lt;link rel="preconnect" href="https://fonts.gstatic.com" crossorigin&gt;
  &lt;link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet"&gt;
  
  &lt;!-- Core styles - USING CONSTRUCTED BASE URL --&gt;
  &lt;link rel="stylesheet" href="&lt;?php echo $baseUrl; ?&gt;admin-core.css"&gt;
  &lt;link rel="stylesheet" href="&lt;?php echo $baseUrl; ?&gt;admin-themes.css"&gt;
  
  &lt;!-- Chart.js --&gt;
  &lt;script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"&gt;&lt;/script&gt;
  
  &lt;!-- Extra CSS files --&gt;
  &lt;?php if (isset($extra_css) && is_array($extra_css)): ?&gt;
    &lt;?php foreach ($extra_css as $css_file): ?&gt;
      &lt;link rel="stylesheet" href="&lt;?php echo $baseUrl . $css_file; ?&gt;"&gt;
    &lt;?php endforeach; ?&gt;
  &lt;?php endif; ?&gt;
  
  &lt;!-- Core JavaScript - USING CONSTRUCTED BASE URL --&gt;
  &lt;script src="&lt;?php echo $baseUrl; ?&gt;admin-core.js" defer&gt;&lt;/script&gt;
  &lt;script src="&lt;?php echo $baseUrl; ?&gt;admin-theme-switcher.js" defer&gt;&lt;/script&gt;
  
  &lt;!-- Extra JavaScript files --&gt;
  &lt;?php if (isset($extra_js) && is_array($extra_js)): ?&gt;
    &lt;?php foreach ($extra_js as $js_file): ?&gt;
      &lt;script src="&lt;?php echo $baseUrl . $js_file; ?&gt;" defer&gt;&lt;/script&gt;
    &lt;?php endforeach; ?&gt;
  &lt;?php endif; ?&gt;
&lt;/head&gt;
&lt;body class="admin-body"&gt;
&lt;div class="admin-container"&gt;
            </code></pre>
        </div>
    </div>
</body>
</html>
