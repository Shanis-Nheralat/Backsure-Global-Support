<?php
/**
 * Database Connection Test
 * Use this file to verify your database connection and table structure
 */

// Enable error reporting for troubleshooting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database configuration
require_once 'db_config.php';

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        h1 { color: #062767; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto; }
        .btn { 
            display: inline-block; 
            background: #062767; 
            color: white; 
            padding: 10px 15px; 
            text-decoration: none; 
            border-radius: 4px; 
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <h1>Database Connection Test</h1>";

try {
    // Test database connection
    $db = get_db_connection();
    echo "<p class='success'>✓ Database connection successful!</p>";
    echo "<p>Connected to database: <strong>{$db_name}</strong> as user: <strong>{$db_user}</strong></p>";
    
    // Check admin_users table
    $tables_stmt = $db->query("SHOW TABLES");
    $tables = $tables_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h2>Available Tables:</h2>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>{$table}</li>";
    }
    echo "</ul>";
    
    $admin_table_exists = in_array('admin_users', $tables);
    
    if ($admin_table_exists) {
        echo "<p class='success'>✓ admin_users table exists</p>";
        
        // Show admin_users table structure
        $stmt = $db->query("DESCRIBE admin_users");
        $columns = $stmt->fetchAll();
        
        echo "<h2>admin_users Table Structure:</h2>";
        echo "<pre>";
        print_r($columns);
        echo "</pre>";
        
        // Check if there are any admin users
        $stmt = $db->query("SELECT COUNT(*) FROM admin_users");
        $count = $stmt->fetchColumn();
        echo "<p>Found {$count} admin users in the database</p>";
        
        // If no admin users, create a default one
        if ($count == 0) {
            echo "<p>Creating default admin user...</p>";
            $password = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO admin_users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(['Admin User', 'admin@example.com', $password, 'admin', 'active']);
            echo "<p class='success'>✓ Default admin created with:</p>";
            echo "<p>Email: <strong>admin@example.com</strong><br>Password: <strong>admin123</strong></p>";
        } else {
            // Show admin emails (but not passwords)
            $stmt = $db->query("SELECT id, name, email, role, status FROM admin_users");
            $admins = $stmt->fetchAll();
            
            echo "<h2>Admin Users:</h2>";
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>";
            
            foreach ($admins as $admin) {
                echo "<tr>";
                echo "<td>{$admin['id']}</td>";
                echo "<td>{$admin['name']}</td>";
                echo "<td>{$admin['email']}</td>";
                echo "<td>{$admin['role']}</td>";
                echo "<td>{$admin['status']}</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        }
    } else {
        echo "<p class='error'>✗ admin_users table not found!</p>";
        echo "<p>Do you want to create the admin_users table now?</p>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='create_table' value='1'>";
        echo "<button type='submit'>Create admin_users Table</button>";
        echo "</form>";
        
        // Handle table creation request
        if (isset($_POST['create_table'])) {
            $db->exec("CREATE TABLE IF NOT EXISTS `admin_users` (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `password` varchar(255) NOT NULL,
                `role` varchar(20) NOT NULL DEFAULT 'admin',
                `status` varchar(20) NOT NULL DEFAULT 'active',
                `login_attempts` int(11) NOT NULL DEFAULT 0,
                `last_attempt_time` datetime DEFAULT NULL,
                `last_login` datetime DEFAULT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `admin_users_email_unique` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
            
            echo "<p class='success'>✓ admin_users table created successfully!</p>";
            echo "<p>Refresh this page to see the new table.</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database credentials in db_config.php:</p>";
    echo "<pre>
\$db_host = '{$db_host}';
\$db_name = '{$db_name}';
\$db_user = '{$db_user}';
\$db_pass = '********';
</pre>";
}

echo "<p><a href='admin-login.php' class='btn'>Go to Admin Login</a></p>";
echo "</body></html>";
