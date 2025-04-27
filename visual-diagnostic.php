<?php
/**
 * CSS and JavaScript Diagnostic Tool
 * This file helps troubleshoot missing visual elements
 */

// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Visual Elements Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        .section { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        table { border-collapse: collapse; width: 100%; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .resource-test { margin-top: 10px; padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>Visual Elements Diagnostic</h1>
    
    <div class="section">
        <h2>1. CSS Files Check</h2>
        <?php
        $css_files = [
            'admin-core.css',
            'admin-themes.css',
            'assets/css/admin-dashboard.css'
        ];
        
        echo "<table>";
        echo "<tr><th>CSS File</th><th>Status</th><th>Size</th><th>Last Modified</th></tr>";
        
        foreach ($css_files as $file) {
            echo "<tr>";
            echo "<td>$file</td>";
            
            if (file_exists($file)) {
                echo "<td class='success'>Found</td>";
                echo "<td>" . filesize($file) . " bytes</td>";
                echo "<td>" . date("Y-m-d H:i:s", filemtime($file)) . "</td>";
            } else {
                echo "<td class='error'>Missing</td>";
                echo "<td>-</td><td>-</td>";
            }
            
            echo "</tr>";
        }
        
        echo "</table>";
        ?>
        
        <h3>CSS Load Test</h3>
        <div class="resource-test">
            <p>The following box should be styled if admin-core.css is loading correctly:</p>
            <div class="btn btn-primary">This should look like a button</div>
        </div>
    </div>
    
    <div class="section">
        <h2>2. JavaScript Files Check</h2>
        <?php
        $js_files = [
            'admin-core.js',
            'admin-theme-switcher.js',
            'assets/js/admin-dashboard.js'
        ];
        
        echo "<table>";
        echo "<tr><th>JavaScript File</th><th>Status</th><th>Size</th><th>Last Modified</th></tr>";
        
        foreach ($js_files as $file) {
            echo "<tr>";
            echo "<td>$file</td>";
            
            if (file_exists($file)) {
                echo "<td class='success'>Found</td>";
                echo "<td>" . filesize($file) . " bytes</td>";
                echo "<td>" . date("Y-m-d H:i:s", filemtime($file)) . "</td>";
            } else {
                echo "<td class='error'>Missing</td>";
                echo "<td>-</td><td>-</td>";
            }
            
            echo "</tr>";
        }
        
        echo "</table>";
        ?>
        
        <h3>JavaScript Load Test</h3>
        <div class="resource-test">
            <p>Click the button below to test if JavaScript is loading:</p>
            <button id="js-test-button">Click Me</button>
            <div id="js-test-result">JavaScript is not working.</div>
            
            <script>
                document.getElementById('js-test-button').addEventListener('click', function() {
                    document.getElementById('js-test-result').innerHTML = '<span class="success">JavaScript is working correctly!</span>';
                });
                
                // Auto-update after 1 second to indicate if script loaded
                setTimeout(function() {
                    document.getElementById('js-test-result').innerHTML = '<span class="info">JavaScript loaded, but interaction test not completed. Click the button above.</span>';
                }, 1000);
            </script>
        </div>
    </div>
    
    <div class="section">
        <h2>3. External Libraries Check</h2>
        <?php
        $external_libraries = [
            'Font Awesome' => '<i class="fas fa-check"></i>',
            'Chart.js' => '<canvas id="chart-test" width="200" height="100"></canvas>',
            'Bootstrap' => '<button class="btn btn-primary">Bootstrap Button</button>'
        ];
        
        echo "<table>";
        echo "<tr><th>Library</th><th>Status</th><th>Test Element</th></tr>";
        
        foreach ($external_libraries as $library => $test_element) {
            echo "<tr>";
            echo "<td>$library</td>";
            echo "<td id='" . strtolower(str_replace(' ', '-', $library)) . "-status'>Testing...</td>";
            echo "<td>$test_element</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        ?>
        
        <script>
            // Test if Chart.js is loaded
            setTimeout(function() {
                if (typeof Chart !== 'undefined') {
                    document.getElementById('chart-js-status').innerHTML = '<span class="success">Loaded</span>';
                    // Create a test chart
                    new Chart(document.getElementById('chart-test'), {
                        type: 'bar',
                        data: {
                            labels: ['Test'],
                            datasets: [{
                                label: 'Test Chart',
                                data: [5],
                                backgroundColor: 'blue'
                            }]
                        }
                    });
                } else {
                    document.getElementById('chart-js-status').innerHTML = '<span class="error">Not Loaded</span>';
                }
            }, 1000);
            
            // Test if Font Awesome is loaded
            setTimeout(function() {
                const testIcon = document.querySelector('.fas.fa-check');
                if (testIcon && getComputedStyle(testIcon, ':before').content !== 'none') {
                    document.getElementById('font-awesome-status').innerHTML = '<span class="success">Loaded</span>';
                } else {
                    document.getElementById('font-awesome-status').innerHTML = '<span class="error">Not Loaded</span>';
                }
            }, 1000);
            
            // Test if Bootstrap is loaded
            setTimeout(function() {
                const testButton = document.querySelector('.btn.btn-primary');
                const computedStyle = window.getComputedStyle(testButton);
                if (computedStyle.backgroundColor !== 'rgba(0, 0, 0, 0)' && computedStyle.backgroundColor !== 'transparent') {
                    document.getElementById('bootstrap-status').innerHTML = '<span class="success">Loaded</span>';
                } else {
                    document.getElementById('bootstrap-status').innerHTML = '<span class="error">Not Loaded</span>';
                }
            }, 1000);
        </script>
    </div>
    
    <div class="section">
        <h2>4. CSS Path Test in admin-head.php</h2>
        <?php
        if (file_exists('admin-head.php')) {
            $head_content = file_get_contents('admin-head.php');
            echo "<p>Found admin-head.php. Analyzing CSS paths:</p>";
            echo "<pre>" . htmlspecialchars(preg_replace('/(.*css.*)/i', '<span style="background-color: yellow;">$1</span>', $head_content)) . "</pre>";
        } else {
            echo "<p class='error'>Could not find admin-head.php for analysis.</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>5. Browser Check</h2>
        <p>Your browser information:</p>
        <code id="user-agent"></code>
        <script>
            document.getElementById('user-agent').textContent = navigator.userAgent;
        </script>
    </div>
    
    <div class="section">
        <h2>6. Recommendation</h2>
        <p>Based on the tests above, here are some potential issues:</p>
        <ul id="recommendations">
            <li>Check if CSS files are being correctly linked in admin-head.php</li>
            <li>Ensure JavaScript files are correctly linked and loading</li>
            <li>Verify path prefixes - they might need to be absolute paths</li>
            <li>Check for console errors in your browser's developer tools</li>
        </ul>
    </div>
</body>
</html>
