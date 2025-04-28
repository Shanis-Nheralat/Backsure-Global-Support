<?php
// db-test.php - Simple script to show the exact database error

// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials to test
$db_host = 'localhost';
$db_name = 'backsure_admin';
$db_user = 'root';
$db_pass = 'password'; // Your actual password

echo "<h1>Database Connection Test</h1>";

try {
    // First try connecting without database selection
    echo "<h2>Testing MySQL server connection:</h2>";
    $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    echo "<p style='color:green'>✓ Connected to MySQL server successfully!</p>";
    
    // Check if the database exists
    echo "<h2>Testing if database exists:</h2>";
    $stmt = $pdo->query("SHOW DATABASES LIKE '$db_name'");
    $db_exists = $stmt->rowCount() > 0;
    
    if ($db_exists) {
        echo "<p style='color:green'>✓ Database '$db_name' exists</p>";
        
        // Try connecting to the specific database
        echo "<h2>Testing connection to specific database:</h2>";
        $db_pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
        echo "<p style='color:green'>✓ Connected to database '$db_name' successfully!</p>";
    } else {
        echo "<p style='color:red'>✗ Database '$db_name' does not exist</p>";
    }
    
    // Show available databases
    echo "<h2>Available databases:</h2>";
    $stmt = $pdo->query("SHOW DATABASES");
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>" . $row['Database'] . "</li>";
    }
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>✗ Error: " . $e->getMessage() . "</p>";
}
?>
