<?php
// Set page title
$page_title = 'Admin Diagnostic';

// Start with basic HTML structure
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; }
        h1 { color: #333; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        h2 { color: #0066cc; margin-top: 30px; }
        .test-section { background: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .file-info { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .test-box { width: 200px; height: 50px; margin: 15px 0; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; }
        .code-block { background: #f8f8f8; padding: 10px; border: 1px solid #ddd; border-radius: 3px; overflow-x: auto; }
        .highlight { background-color: yellow; }
    </style>
</head>
<body>
    <h1>Admin Panel Diagnostic</h1>
    
    <div class="test-section">
        <h2>1. Server Environment</h2>
        <?php
        // Check PHP version
        echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
        
        // Check server software
        echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
        
        // Check document root
        echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
        
        // Check script path
        echo "<p><strong>Script Path:</strong> " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
        
        // Check if mod_rewrite is available
        echo "<p><strong>mod_rewrite:</strong> ";
        if (function_exists('apache_get_modules')) {
            echo in_array('mod_rewrite', apache_get_modules()) ? "<span class='success'>Available</span>" : "<span class='warning'>Not available</span>";
        } else {
            echo "<span class='warning'>Cannot determine (not running on Apache or mod_rewrite check not available)</span>";
        }
        echo "</p>";
        ?>
    </div>
    
    <div class="test-section">
        <h2>2. Core Files Check</h2>
        <?php
        // Define core files to check
        $core_files = [
            'admin-auth.php',
            'admin-head.php',
            'admin-sidebar.php',
            'admin-header.php',
            'admin-footer.php',
            'admin-core.css',
            'admin-themes.css',
            'admin-core.js',
            'admin-theme-switcher.js',
            'admin-dashboard.php',
            'admin-notifications.php',
            'admin-analytics.php'
        ];
        
        echo "<table>";
        echo "<tr><th>File</th><th>Status</th><th>Size</th><th>Last Modified</th></tr>";
        
        foreach ($core_files as $file) {
            echo "<tr>";
            echo "<td>{$file}</td>";
            
            if (file_exists($file)) {
                $size = filesize($file);
                $size_formatted = $size > 1024 ? round($size / 1024, 2) . " KB" : $size . " bytes";
                $modified = date("Y-m-d H:i:s", filemtime($file));
                
                echo "<td><span class='success'>Found</span></td>";
                echo "<td>{$size_formatted}</td>";
                echo "<td>{$modified}</td>";
            } else {
                echo "<td><span class='error'>Missing</span></td>";
                echo "<td>-</td><td>-</td>";
            }
            
            echo "</tr>";
        }
        
        echo "</table>";
        ?>
    </div>
    
    <div class="test-section">
        <h2>3. CSS Loading Test</h2>
        <p>The box below should be styled if admin-core.css is loading correctly:</p>
        
        <div class="test-box" id="css-test-box">This should look like a styled button</div>
        
        <script>
            // Check if the box has styling from admin-core.css
            window.addEventListener('load', function() {
                const box = document.getElementById('css-test-box');
                const style = window.getComputedStyle(box);
                
                // Add inline result based on styling
                const resultDiv = document.createElement('div');
                
                if (style.backgroundColor !== 'rgba(0, 0, 0, 0)' && 
                    style.backgroundColor !== 'transparent' &&
                    style.backgroundColor !== 'rgb(255, 255, 255)') {
                    resultDiv.innerHTML = "<span class='success'>CSS loaded successfully!</span>";
                } else {
                    resultDiv.innerHTML = "<span class='error'>CSS not loading properly!</span>";
                }
                
                box.after(resultDiv);
            });
        </script>
    </div>
    
    <div class="test-section">
        <h2>4. JavaScript Loading Test</h2>
        <p>Click the button below to test if JavaScript files are loading:</p>
        
        <button id="js-test-button">Test JavaScript</button>
        <div id="js-test-result">JavaScript not loaded or not working</div>
        
        <script>
            document.getElementById('js-test-button').addEventListener('click', function() {
                const result = document.getElementById('js-test-result');
                
                // Check if core admin JS functions exist
                if (typeof initSidebar === 'function') {
                    result.innerHTML = "<span class='success'>JavaScript loaded and functions available!</span>";
                } else {
                    result.innerHTML = "<span class='error'>Core JavaScript functions not found!</span>";
                }
            });
        </script>
    </div>
    
    <div class="test-section">
        <h2>5. External Libraries Check</h2>
        <?php
        // List of expected libraries
        $external_libs = [
            'Font Awesome' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css',
            'Chart.js' => 'https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js'
        ];
        
        echo "<table>";
        echo "<tr><th>Library</th><th>URL</th><th>Status</th></tr>";
        
        foreach ($external_libs as $lib_name => $lib_url) {
            echo "<tr>";
            echo "<td>{$lib_name}</td>";
            echo "<td>{$lib_url}</td>";
            
            // We'll check these with JavaScript after the page loads
            echo "<td id='lib-status-" . str_replace('.', '-', $lib_name) . "'>Checking...</td>";
            
            echo "</tr>";
        }
        
        echo "</table>";
        ?>
        
        <div style="margin-top: 15px;">
            <p>Font Awesome Icon Test: <span id="fa-test"><i class="fas fa-check"></i> This should be an icon</span></p>
            <p>Chart.js Test: <canvas id="chart-test" width="200" height="50"></canvas></p>
        </div>
        
        <script>
            window.addEventListener('load', function() {
                // Check Font Awesome
                const faIcon = document.querySelector('#fa-test i');
                const faStatus = document.getElementById('lib-status-Font-Awesome');
                
                if (getComputedStyle(faIcon).fontFamily.includes('Font Awesome') || 
                    getComputedStyle(faIcon).width !== getComputedStyle(faIcon).height) {
                    faStatus.innerHTML = "<span class='success'>Loaded</span>";
                } else {
                    faStatus.innerHTML = "<span class='error'>Not Loaded</span>";
                }
                
                // Check Chart.js
                const chartStatus = document.getElementById('lib-status-Chart-js');
                
                if (typeof Chart !== 'undefined') {
                    chartStatus.innerHTML = "<span class='success'>Loaded</span>";
                    
                    // Create a test chart
                    new Chart(document.getElementById('chart-test'), {
                        type: 'bar',
                        data: {
                            labels: ['Test'],
                            datasets: [{
                                label: 'Test Chart',
                                data: [5],
                                backgroundColor: '#0066cc'
                            }]
                        },
                        options: {
                            responsive: false,
                            maintainAspectRatio: false
                        }
                    });
                } else {
                    chartStatus.innerHTML = "<span class='error'>Not Loaded</span>";
                }
            });
        </script>
    </div>
    
    <div class="test-section">
        <h2>6. Path Analysis</h2>
        <?php
        // Check for admin-head.php file to analyze
        if (file_exists('admin-head.php')) {
            $head_content = file_get_contents('admin-head.php');
            
            echo "<p class='success'>Found admin-head.php. Analyzing CSS/JS paths:</p>";
            
            // Extract CSS and JS file paths
            preg_match_all('/<link[^>]*href=["\'](.*?)["\'][^>]*>/i', $head_content, $css_matches);
            preg_match_all('/<script[^>]*src=["\'](.*?)["\'][^>]*>/i', $head_content, $js_matches);
            
            echo "<div class='code-block'>";
            // Highlight the CSS paths
            $highlighted_content = preg_replace('/(<link[^>]*href=["\'](.*?)["\'"][^>]*>)/i', '<span class="highlight">$1</span>', $head_content);
            // Highlight the JS paths
            $highlighted_content = preg_replace('/(<script[^>]*src=["\'](.*?)["\'"][^>]*>)/i', '<span class="highlight">$1</span>', $highlighted_content);
            
            echo nl2br(htmlspecialchars($highlighted_content));
            echo "</div>";
            
            // Analyze path types
            $absolute_paths = 0;
            $relative_paths = 0;
            
            $all_paths = array_merge($css_matches[1], $js_matches[1]);
            foreach ($all_paths as $path) {
                if (strpos($path, 'http') === 0 || strpos($path, '//') === 0) {
                    // External URL
                    continue;
                } elseif (strpos($path, '/') === 0) {
                    $absolute_paths++;
                } else {
                    $relative_paths++;
                }
            }
            
            echo "<p><strong>Path Analysis:</strong> Found {$absolute_paths} absolute paths and {$relative_paths} relative paths.</p>";
            
            if ($absolute_paths > 0) {
                echo "<p class='warning'>Warning: You're using absolute paths (starting with /) which may need to be adjusted based on your server configuration.</p>";
            }
        } else {
            echo "<p class='error'>Could not find admin-head.php for analysis.</p>";
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>7. Browser Info</h2>
        <p><strong>User Agent:</strong> <span id="user-agent">JavaScript required to display</span></p>
        <p><strong>Screen Resolution:</strong> <span id="screen-res">JavaScript required to display</span></p>
        <p><strong>Window Size:</strong> <span id="window-size">JavaScript required to display</span></p>
        <p><strong>Local Storage Available:</strong> <span id="local-storage">JavaScript required to display</span></p>
        
        <script>
            document.getElementById('user-agent').textContent = navigator.userAgent;
            document.getElementById('screen-res').textContent = `${screen.width} x ${screen.height}`;
            document.getElementById('window-size').textContent = `${window.innerWidth} x ${window.innerHeight}`;
            
            try {
                localStorage.setItem('test', 'test');
                localStorage.removeItem('test');
                document.getElementById('local-storage').innerHTML = "<span class='success'>Yes</span>";
            } catch (e) {
                document.getElementById('local-storage').innerHTML = "<span class='error'>No</span>";
            }
        </script>
    </div>
    
    <div class="test-section">
        <h2>8. Recommendations</h2>
        <div id="recommendations">
            <p>Analyzing your configuration...</p>
        </div>
        
        <script>
            window.addEventListener('load', function() {
                const recommendations = document.getElementById('recommendations');
                let recHTML = '<ul>';
                
                // File existence recommendations
                const missingFiles = document.querySelectorAll('td .error');
                if (missingFiles.length > 0) {
                    recHTML += '<li><strong>Missing Files:</strong> Some core files appear to be missing. Ensure all required files are in the correct location.</li>';
                }
                
                // CSS loading recommendation
                if (!document.querySelector('#css-test-box + div .success')) {
                    recHTML += '<li><strong>Fix CSS Loading:</strong> Your CSS isn\'t loading correctly. Check file paths in admin-head.php and ensure they match your server directory structure.</li>';
                }
                
                // JS loading recommendation
                const jsTest = document.getElementById('js-test-result').innerHTML;
                if (!jsTest.includes('success')) {
                    recHTML += '<li><strong>Fix JavaScript Loading:</strong> JavaScript files aren\'t loading properly. Check file paths and for any console errors.</li>';
                }
                
                // External libraries recommendation
                const libErrors = document.querySelectorAll('td[id^="lib-status-"] .error');
                if (libErrors.length > 0) {
                    recHTML += '<li><strong>External Libraries:</strong> Some external libraries aren\'t loading. Check your internet connection and make sure CDN URLs are accessible.</li>';
                }
                
                // Path recommendation
                if (document.querySelector('.path-analysis .warning')) {
                    recHTML += '<li><strong>Path Configuration:</strong> Adjust file paths in admin-head.php to match your server configuration. Try using relative paths instead of absolute if you\'re having issues.</li>';
                }
                
                // Add a general recommendation
                recHTML += '<li><strong>Check Browser Console:</strong> Open your browser\'s developer tools (F12) and check the Console tab for any errors that might provide more details about issues.</li>';
                
                recHTML += '</ul>';
                recommendations.innerHTML = recHTML;
            });
        </script>
    </div>
</body>
</html>
