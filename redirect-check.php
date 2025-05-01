<?php
// Start session
session_start();

// Initialize counter if not exists
if (!isset($_SESSION['redirect_count'])) {
    $_SESSION['redirect_count'] = 0;
}

// Increment counter
$_SESSION['redirect_count']++;

echo "Redirect count: " . $_SESSION['redirect_count'];
echo "<br>Current page: " . $_SERVER['PHP_SELF'];
echo "<br>Previous page: " . ($_SERVER['HTTP_REFERER'] ?? 'None');
echo "<p><a href='admin-login.php'>Go to login page</a></p>";
echo "<p><a href='redirect-check.php?reset=1'>Reset counter</a></p>";

// Reset counter if requested
if (isset($_GET['reset'])) {
    $_SESSION['redirect_count'] = 0;
    echo "<p>Counter reset.</p>";
}
?>
