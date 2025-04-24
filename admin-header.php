<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="robots" content="noindex, nofollow">
  <title><?php echo $page_title; ?> | Backsure Global Support</title>
  
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  
  <!-- Chart.js for analytics -->
  <?php if ($active_menu == 'dashboard'): ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <?php endif; ?>
  
  <!-- Include your CSS files -->
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/admin-style.css">
</head>
<body class="admin-body">
  <div class="admin-container">
