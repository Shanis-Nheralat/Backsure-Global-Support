<?php
/**
 * Admin Page Template
 * Replace this comment with specific page description
 */

// Start session
session_start();

// Block unauthorized users
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Get admin information from session
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin User';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'Administrator';

// Set current page for sidebar highlighting
$current_page = 'page-identifier'; // CHANGE THIS: Use one of: dashboard, blog, services, testimonials, faq, solutions, inquiries, subscribers, clients, candidates, candidate-notes, users, roles, profile, seo, settings, integrations
$page_title = 'Page Title'; // CHANGE THIS: Set the page title that appears in breadcrumbs

// Any page-specific variables
$notification_count = 5; // Example - set based on actual data
$task_count = 2; // Example - set based on actual data
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="robots" content="noindex, nofollow">
  <title>Page Title | Backsure Global Support</title> <!-- CHANGE THIS -->
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  
  <!-- Include any page-specific JS libraries here -->
  
  <!-- Admin CSS - External stylesheet -->
  <link rel="stylesheet" href="admin-dashboard.css">
  
  <!-- Any page-specific CSS -->
  <style>
    /* Add page-specific styles here if needed */
  </style>
</head>
<body class="admin-body">
  <div class="admin-container">
    <!-- Include the sidebar -->
    <?php include 'admin-sidebar.php'; ?>
    
    <!-- Main Content Area -->
    <main class="admin-main">
      <!-- Include the header -->
      <?php include 'admin-header.php'; ?>
      
      <!-- Page Content -->
      <div class="admin-content">
        <!-- Page header -->
        <div class="page-header">
          <h1>Page Title</h1> <!-- CHANGE THIS -->
          <!-- Add any page-specific header content -->
        </div>
        
        <!-- Main page content goes here -->
        <div class="content-section">
          <!-- Replace with actual page content -->
          <p>Content goes here...</p>
        </div>
      </div>
      
      <!-- Include the footer -->
      <?php include 'admin-footer.php'; ?>
    </main>
  </div>
  
  <!-- Page-specific scripts -->
  <script>
    // Add any page-specific JavaScript here
    document.addEventListener('DOMContentLoaded', function() {
      // Your page-specific code
    });
  </script>
</body>
</html>
