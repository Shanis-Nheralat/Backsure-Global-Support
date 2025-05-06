// File: set-role.php (temporary file to update your session)
<?php
session_start();
$_SESSION['admin_role'] = 'admin';  // Set a valid role
echo "Role set to 'admin'. <a href='admin-dashboard.php'>Go to Dashboard</a>";
