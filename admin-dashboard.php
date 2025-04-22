<?php
// Start session first thing
session_start();

// ✅ Block unauthorized users
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Rest of your dashboard code remains unchanged
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="robots" content="noindex, nofollow">
  <title>Admin Dashboard | Backsure Global Support</title>
  <!-- Direct CSS link - no subdirectory -->
  <link rel="stylesheet" href="admin-style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <!-- Chart.js for analytics -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Additional dashboard CSS -->
  <link rel="stylesheet" href="admin-dashboard.css" />
  <style>
    /* Emergency inline styles in case external CSS fails */
    /* Your existing styles here */
  </style>
</head>
<body class="admin-body">
  <!-- Your existing dashboard HTML here -->
</body>
</html>
