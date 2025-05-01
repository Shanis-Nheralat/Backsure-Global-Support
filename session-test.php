<?php
session_start();
echo "Session ID: " . session_id();
echo "<br>Session data: <pre>" . print_r($_SESSION, true) . "</pre>";

// Set a test value
$_SESSION['test_value'] = time();
echo "<br>Set test value: " . $_SESSION['test_value'];
echo "<p><a href='session-test.php'>Reload this page</a> to see if the session persists.</p>";
?>
