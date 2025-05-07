<?php
/**
 * Database Connection Test
 * Verifies database connection and important user settings
 */

// Include database config
require_once 'db_config.php';

// Start session
session_start();

echo "<h2>Database Connection Test</h2>";

try {
    // Test connection
    $db = get_db_connection();
    echo "✅ Database connection successful<br>";
    
    // Check admins table
    $tableResult = $db->query("SHOW TABLES LIKE 'admins'");
    if ($tableResult->rowCount() > 0) {
        echo "✅ 'admins' table exists<br>";
        
        // Check for shanisbsg user
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = 'shanisbsg'");
        $stmt->execute();
        $user = $stmt->fetch();
        
        if ($user) {
            echo "✅ Found user 'shanisbsg' in database<br>";
            echo "<h3>User Information:</h3>";
            echo "<pre>";
            print_r($user);
            echo "</pre>";
            
            if (empty($user['role'])) {
                echo "⚠️ User has no role assigned!<br>";
                echo "<p>Run <a href='set-role.php'>set-role.php</a> to fix this issue.</p>";
            } else {
                echo "✅ User role is set to: " . htmlspecialchars($user['role']) . "<br>";
            }
        } else {
            echo "❌ User 'shanisbsg' not found in database<br>";
        }
    } else {
        echo "❌ 'admins' table does not exist<br>";
    }
    
    echo "<h3>Current Session Variables:</h3>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}
?>

<h3>Quick Links</h3>
<ul>
    <li><a href="set-role.php">Fix User Role</a></li>
    <li><a href="admin-login.php">Go to Login Page</a></li>
    <li><a href="admin-dashboard.php">Go to Dashboard</a></li>
</ul>
