<!DOCTYPE html>
<html lang="en">
<head>
<!-- Add this inside the <head> section -->
<style>
  .admin-sidebar {
    width: 250px;
    background-color: #062767;
    color: white;
    height: 100vh;
    overflow-y: auto;
    position: fixed;
    left: 0;
    top: 0;
  }
  
  .admin-main {
    margin-left: 250px;
  }
  
  /* Basic menu styling */
  .sidebar-nav ul {
    list-style: none;
    padding: 0;
  }
  
  .sidebar-nav ul li a {
    display: block;
    padding: 10px 20px;
    color: white;
    text-decoration: none;
  }
  
  .sidebar-nav ul li a:hover {
    background-color: rgba(255,255,255,0.1);
  }
</style>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Admin Panel'; ?> | Backsure Global Support</title>
  
  <!-- Favicon -->
  <link rel="shortcut icon" href="/assets/images/favicon.ico" type="image/x-icon">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  
  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  
  <!-- Core styles - FIXED PATHS -->
  <link rel="stylesheet" href="/admin-core.css">
  <link rel="stylesheet" href="/admin-themes.css">
  
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
  
  <!-- Extra CSS files -->
  <?php if (isset($extra_css) && is_array($extra_css)): ?>
    <?php foreach ($extra_css as $css_file): ?>
      <link rel="stylesheet" href="<?php echo $css_file; ?>">
    <?php endforeach; ?>
  <?php endif; ?>
  
  <!-- Core JavaScript - FIXED PATHS -->
  <script src="/admin-core.js" defer></script>
  <script src="/admin-theme-switcher.js" defer></script>
  
  <!-- Extra JavaScript files -->
  <?php if (isset($extra_js) && is_array($extra_js)): ?>
    <?php foreach ($extra_js as $js_file): ?>
      <script src="<?php echo $js_file; ?>" defer></script>
    <?php endforeach; ?>
  <?php endif; ?>
</head>
<body class="admin-body">
<div class="admin-container">
