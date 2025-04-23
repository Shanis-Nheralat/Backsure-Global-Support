<?php
// Start session first thing - no whitespace before this
session_start();

// Block unauthorized users
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Get admin information from session
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin User';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'Administrator';
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="robots" content="noindex, nofollow">
  <title>Admin Dashboard | Backsure Global Support</title>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <!-- Chart.js for analytics -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Emergency inline styles in case external CSS fails */
    :root {
      --primary-color: #062767;
      --primary-light: #3a5ca2;
      --primary-dark: #041c4a;
      --accent-color: #b19763;
      --accent-light: #cdb48e;
      --accent-dark: #97814c;
      --success-color: #1cc88a;
      --info-color: #36b9cc;
      --warning-color: #f6c23e;
      --danger-color: #e74a3b;
      --dark-color: #333333;
      --light-color: #f8f9fc;
      --gray-100: #f8f9fc;
      --gray-200: #eaecf4;
      --gray-300: #dddfeb;
      --gray-400: #d1d3e2;
      --gray-500: #b7b9cc;
      --gray-600: #858796;
      --gray-700: #6e707e;
      --gray-800: #5a5c69;
      --gray-900: #3a3b45;
      
      --sidebar-width: 250px;
      --sidebar-collapsed-width: 80px;
      --header-height: 60px;
      --transition-speed: 0.3s;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Open Sans', sans-serif;
      font-size: 14px;
      line-height: 1.6;
      color: var(--gray-800);
      background-color: var(--gray-100);
    }

    .admin-body {
      min-height: 100vh;
    }

    .admin-container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar Styles */
    .admin-sidebar {
      width: var(--sidebar-width);
      background-color: var(--primary-color);
      color: white;
      position: fixed;
      left: 0;
      top: 0;
      height: 100vh;
      overflow-y: auto;
      transition: width var(--transition-speed);
      z-index: 100;
      display: flex;
      flex-direction: column;
    }

    .admin-main {
      flex: 1;
      margin-left: var(--sidebar-width);
      transition: margin-left var(--transition-speed);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .admin-container.sidebar-collapsed .admin-sidebar {
      width: var(--sidebar-collapsed-width);
    }

    .admin-container.sidebar-collapsed .admin-main {
      margin-left: var(--sidebar-collapsed-width);
    }

    .admin-container.sidebar-collapsed .sidebar-header h2,
    .admin-container.sidebar-collapsed .admin-user .user-info,
    .admin-container.sidebar-collapsed .admin-user .dropdown-toggle,
    .admin-container.sidebar-collapsed .sidebar-nav ul li a span,
    .admin-container.sidebar-collapsed .sidebar-nav ul li a .submenu-icon,
    .admin-container.sidebar-collapsed .sidebar-footer a span {
      display: none;
    }

    .sidebar-header {
      padding: 15px 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
    }

    .admin-logo {
      height: 40px;
      margin-bottom: 10px;
    }

    .sidebar-header h2 {
      color: white;
      font-size: 1.2rem;
      text-align: center;
      margin: 0;
    }

    .admin-user {
      padding: 15px 20px;
      display: flex;
      align-items: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      position: relative;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      overflow: hidden;
      margin-right: 10px;
    }

    .user-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .user-info {
      flex: 1;
    }

    .user-info h3 {
      margin: 0;
      font-size: 0.9rem;
      color: white;
    }

    .user-role {
      font-size: 0.8rem;
      color: rgba(255, 255, 255, 0.7);
    }

    .dropdown-toggle {
      background: none;
      border: none;
      color: white;
      cursor: pointer;
      padding: 5px;
    }

    .dropdown-menu {
      display: none;
      position: absolute;
      top: 100%;
      right: 10px;
      background-color: white;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      border-radius: 5px;
      min-width: 180px;
      z-index: 101;
      overflow: hidden;
    }

    .dropdown-menu.show {
      display: block;
    }

    .dropdown-menu li {
      border-bottom: 1px solid var(--gray-200);
    }

    .dropdown-menu li:last-child {
      border-bottom: none;
    }

    .dropdown-menu li a {
      color: var(--gray-700);
      display: flex;
      align-items: center;
      padding: 10px 15px;
      text-decoration: none;
      transition: background-color 0.2s;
    }

    .dropdown-menu li a i {
      margin-right: 10px;
      width: 16px;
      text-align: center;
    }

    .dropdown-menu li a:hover {
      background-color: var(--gray-100);
    }

    .sidebar-nav {
      flex: 1;
      padding: 15px 0;
    }

    .sidebar-nav ul {
      list-style: none;
    }

    .sidebar-nav ul li {
      margin-bottom: 2px;
    }

    .sidebar-nav ul li a {
      color: rgba(255, 255, 255, 0.8);
      display: flex;
      align-items: center;
      padding: 10px 20px;
      transition: all 0.3s;
      text-decoration: none;
    }

    .sidebar-nav ul li a i {
      width: 20px;
      margin-right: 10px;
      text-align: center;
    }

    .sidebar-nav ul li a .submenu-icon {
      margin-left: auto;
      transition: transform 0.3s;
    }

    .sidebar-nav ul li.open > a .submenu-icon {
      transform: rotate(90deg);
    }

    .sidebar-nav ul li a:hover {
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
    }

    .sidebar-nav ul li.active > a {
      background-color: var(--accent-color);
      color: white;
    }

    .sidebar-nav ul li .submenu {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease-out;
    }

    .sidebar-nav ul li.open .submenu {
      max-height: 1000px;
    }

    .sidebar-nav ul li .submenu li a {
      padding-left: 50px;
    }

    .badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background-color: var(--danger-color);
      color: white;
      border-radius: 10px;
      font-size: 0.7rem;
      padding: 2px 6px;
      margin-left: 8px;
    }

    .sidebar-footer {
      padding: 15px 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-footer a {
      color: rgba(255, 255, 255, 0.7);
      display: flex;
      align-items: center;
      text-decoration: none;
      font-size: 0.9rem;
    }

    .sidebar-footer a i {
      margin-right: 10px;
    }

    .sidebar-footer a:hover {
      color: white;
    }

    /* Header Styles */
    .admin-header {
      height: var(--header-height);
      background-color: white;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      position: sticky;
      top: 0;
      z-index: 99;
    }

    .header-left {
      display: flex;
      align-items: center;
    }

    .sidebar-toggle {
      background: none;
      border: none;
      color: var(--gray-600);
      font-size: 1.2rem;
      cursor: pointer;
      margin-right: 15px;
    }

    .breadcrumbs {
      display: flex;
      align-items: center;
    }

    .breadcrumbs a {
      color: var(--gray-600);
      text-decoration: none;
    }

    .header-right {
      display: flex;
      align-items: center;
    }

    .admin-search {
      position: relative;
      margin-right: 20px;
    }

    .admin-search input {
      background-color: var(--gray-100);
      border: none;
      border-radius: 4px;
      padding: 8px 30px 8px 10px;
      width: 200px;
      font-family: inherit;
    }

    .admin-search button {
      background: none;
      border: none;
      color: var(--gray-600);
      position: absolute;
      right: 5px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
    }

    .header-actions {
      display: flex;
      align-items: center;
    }

    .action-btn {
      background: none;
      border: none;
      color: var(--gray-600);
      font-size: 1rem;
      margin-left: 15px;
      position: relative;
      cursor: pointer;
    }

    .action-btn .badge {
      position: absolute;
      top: -5px;
      right: -5px;
      width: 18px;
      height: 18px;
      padding: 0;
    }

    /* Main Content */
    .admin-content {
      padding: 20px;
      flex: 1;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .page-header h1 {
      margin: 0;
      color: var(--primary-color);
      font-size: 1.8rem;
    }

    .date-display {
      color: var(--gray-600);
      font-size: 0.9rem;
    }

    .date-display i {
      margin-right: 5px;
    }

    /* Stats Cards */
    .stats-overview {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      padding: 20px;
      display: flex;
      align-items: center;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      color: white;
      font-size: 1.5rem;
    }

    .stat-content h3 {
      margin: 0 0 5px 0;
      font-size: 0.9rem;
      color: var(--gray-600);
    }

    .stat-value {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 5px;
      color: var(--gray-800);
    }

    .stat-change {
      font-size: 0.8rem;
      display: flex;
      align-items: center;
    }

    .stat-change i {
      margin-right: 5px;
    }

    .stat-change.positive {
      color: var(--success-color);
    }

    .stat-change.negative {
      color: var(--danger-color);
    }

    .stat-change.neutral {
      color: var(--gray-600);
    }

    .blue { background-color: var(--primary-color); }
    .green { background-color: var(--success-color); }
    .purple { background-color: #6f42c1; }
    .orange { background-color: var(--warning-color); }

    /* Analytics Section */
    .analytics-section {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 20px;
      margin-bottom: 30px;
    }

    .chart-container {
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      padding: 20px;
    }

    .chart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    .chart-header h2 {
      margin: 0;
      font-size: 1.2rem;
      color: var(--gray-800);
    }

    .chart-controls button {
      background: none;
      border: 1px solid var(--gray-300);
      border-radius: 4px;
      padding: 5px 10px;
      margin-left: 5px;
      font-size: 0.8rem;
      color: var(--gray-600);
      cursor: pointer;
    }

    .chart-controls button.active {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      color: white;
    }

    .chart-body {
      height: 300px;
      position: relative;
    }

    .analytics-sidebar {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .widget {
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      padding: 20px;
    }

    .widget h3 {
      margin: 0 0 15px 0;
      font-size: 1.1rem;
      color: var(--gray-800);
    }

    .source-list, .page-list {
      list-style: none;
    }

    .source-list li, .page-list li {
      margin-bottom: 10px;
    }

    .source-info, .page-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 5px;
    }

    .source-name, .page-name {
      font-weight: 600;
      color: var(--gray-700);
    }

    .source-value, .page-views {
      color: var(--gray-600);
      font-size: 0.9rem;
    }

    .progress-bar {
      height: 5px;
      background-color: var(--gray-200);
      border-radius: 10px;
      overflow: hidden;
    }

    .progress {
      height: 100%;
      border-radius: 10px;
    }

    /* Activities Section */
    .activities-section {
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      padding: 20px;
      margin-bottom: 30px;
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .section-header h2 {
      margin: 0;
      font-size: 1.2rem;
      color: var(--gray-800);
    }

    .view-all {
      font-size: 0.9rem;
      color: var(--primary-color);
      text-decoration: none;
    }

    .activity-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
    }

    .activity-item {
      display: flex;
      border: 1px solid var(--gray-200);
      border-radius: 8px;
      padding: 15px;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .activity-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .activity-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      color: white;
    }

    .activity-icon.inquiry {
      background-color: var(--primary-color);
    }

    .activity-icon.user {
      background-color: var(--success-color);
    }

    .activity-icon.content {
      background-color: var(--info-color);
    }

    .activity-icon.testimonial {
      background-color: var(--warning-color);
    }

    .activity-content h4 {
      margin: 0 0 5px 0;
      font-size: 1rem;
    }

    .activity-content p {
      margin: 0 0 10px 0;
      color: var(--gray-600);
      font-size: 0.9rem;
    }

    .activity-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.8rem;
    }

    .activity-time {
      color: var(--gray-500);
    }

    .activity-action {
      color: var(--primary-color);
      text-decoration: none;
    }

    /* Quick Access Section */
    .quick-access-section {
      display: grid;
      grid-template-columns: 1fr 2fr;
      gap: 20px;
      margin-bottom: 30px;
    }

    .quick-actions, .recent-inquiries {
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      padding: 20px;
    }

    .action-buttons {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
      gap: 15px;
      margin-top: 20px;
    }

    .quick-action-btn {
      background-color: var(--gray-100);
      color: var(--gray-700);
      border: 1px solid var(--gray-200);
      border-radius: 8px;
      padding: 15px;
      text-align: center;
      text-decoration: none;
      transition: all 0.3s;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .quick-action-btn:hover {
      background-color: var(--primary-color);
      color: white;
      border-color: var(--primary-color);
    }

    .quick-action-btn i {
      font-size: 1.5rem;
      margin-bottom: 10px;
    }

    .admin-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    .admin-table th, .admin-table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid var(--gray-200);
    }

    .admin-table th {
      background-color: var(--gray-100);
      font-weight: 600;
      color: var(--gray-700);
    }

    .status-badge {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: 600;
    }

    .status-badge.new {
      background-color: rgba(28, 200, 138, 0.1);
      color: var(--success-color);
    }

    .status-badge.replied {
      background-color: rgba(54, 185, 204, 0.1);
      color: var(--info-color);
    }

    .status-badge.closed {
      background-color: rgba(133, 135, 150, 0.1);
      color: var(--gray-600);
    }

    .table-actions {
      display: flex;
      gap: 10px;
    }

    .table-actions a {
      color: var(--gray-600);
      font-size: 1rem;
    }

    .table-actions a:hover {
      color: var(--primary-color);
    }

    /* Footer */
    .admin-footer {
      background-color: white;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-top: 1px solid var(--gray-200);
      font-size: 0.9rem;
      color: var(--gray-600);
    }

    /* Responsive styles */
    @media (max-width: 1200px) {
      .analytics-section,
      .quick-access-section {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .admin-main {
        margin-left: 0;
      }
      
      .admin-sidebar {
        left: -250px;
      }
      
      .admin-container.sidebar-collapsed .admin-sidebar {
        left: 0;
      }
      
      .activity-container {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body class="admin-body">
  <div class="admin-container">
    <!-- Sidebar Navigation -->
    <aside class="admin-sidebar">
      <div class="sidebar-header">
        <img src="Logo.png" alt="BSG Support Logo" class="admin-logo">
        <h2>Admin Panel</h2>
      </div>
      
      <div class="admin-user">
        <div class="user-avatar">
          <img src="avatar.webp" alt="Admin User">
        </div>
        <div class="user-info">
          <h3><?php echo htmlspecialchars($admin_username); ?></h3>
          <span class="user-role"><?php echo htmlspecialchars($admin_role); ?></span>
        </div>
        <button id="user-dropdown-toggle" class="dropdown-toggle">
          <i class="fas fa-chevron-down"></i>
        </button>
        <ul id="user-dropdown" class="dropdown-menu">
          <li><a href="#"><i class="fas fa-user"></i> My Profile</a></li>
          <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
          <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
      </div>
      
      <nav class="sidebar-nav">
        <ul>
         <li>
  <a href="admin-users.php">
    <i class="fas fa-user-friends"></i>
    <span>All Users</span>
  </a>
</li>

<li>
  <a href="admin-services.php">
    <i class="fas fa-briefcase"></i>
    <span>Manage Services</span>
  </a>
</li>

<li>
  <a href="admin-enquiries.php">
    <i class="fas fa-envelope-open-text"></i>
    <span>Client Inquiries</span>
  </a>
</li>
          <li class="active">
            <a href="admin-dashboard.php">
              <i class="fas fa-tachometer-alt"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li class="has-submenu">
            <a href="javascript:void(0)">
              <i class="fas fa-edit"></i>
              <span>Content Management</span>
              <i class="fas fa-chevron-right submenu-icon"></i>
            </a>
            <ul class="submenu">
              <li><a href="#"><i class="fas fa-file-alt"></i> Pages Editor</a></li>
              <li><a href="#"><i class="fas fa-blog"></i> Blog Management</a></li>
              <li><a href="#"><i class="fas fa-briefcase"></i> Services Editor</a></li>
              <li><a href="#"><i class="fas fa-images"></i> Media Library</a></li>
            </ul>
          </li>
          <li class="has-submenu">
            <a href="javascript:void(0)">
              <i class="fas fa-users"></i>
              <span>User Management</span>
              <i class="fas fa-chevron-right submenu-icon"></i>
            </a>
            <ul class="submenu">
              <li><a href="#"><i class="fas fa-user-friends"></i> All Users</a></li>
              <li><a href="#"><i class="fas fa-user-tag"></i> Roles & Permissions</a></li>
            </ul>
          </li>
          <li>
            <a href="#">
              <i class="fas fa-envelope"></i>
              <span>Lead Management</span>
              <span class="badge">3</span>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="fas fa-star"></i>
              <span>Testimonials & Logos</span>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="fas fa-question-circle"></i>
              <span>FAQ Management</span>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="fas fa-envelope-open-text"></i>
              <span>Subscribers</span>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="fas fa-search"></i>
              <span>SEO Settings</span>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="fas fa-plug"></i>
              <span>Integrations</span>
            </a>
          </li>
          <li class="has-submenu">
            <a href="javascript:void(0)">
              <i class="fas fa-cogs"></i>
              <span>Site Settings</span>
              <i class="fas fa-chevron-right submenu-icon"></i>
            </a>
            <ul class="submenu">
              <li><a href="#"><i class="fas fa-sliders-h"></i> General Settings</a></li>
              <li><a href="#"><i class="fas fa-palette"></i> Appearance</a></li>
              <li><a href="#"><i class="fas fa-database"></i> Backup & Restore</a></li>
            </ul>
          </li>
        </ul>
      </nav>
      
      <div class="sidebar-footer">
        <a href="index.html" target="_blank">
          <i class="fas fa-external-link-alt"></i>
          <span>View Website</span>
        </a>
      </div>
    </aside>
    
    <!-- Main Content Area -->
    <main class="admin-main">
      <!-- Top Navigation Bar -->
      <header class="admin-header">
        <div class="header-left">
          <button id="sidebar-toggle" class="sidebar-toggle">
            <i class="fas fa-bars"></i>
          </button>
          <div class="breadcrumbs">
            <a href="admin-dashboard.php">Dashboard</a>
          </div>
        </div>
        
        <div class="header-right">
          <div class="admin-search">
            <input type="text" placeholder="Search...">
            <button type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
          
          <div class="header-actions">
            <button class="action-btn notification-btn">
              <i class="fas fa-bell"></i>
              <span class="badge">5</span>
            </button>
            
            <button class="action-btn task-btn">
              <i class="fas fa-check-circle"></i>
              <span class="badge">2</span>
            </button>
            
            <button class="action-btn help-btn">
              <i class="fas fa-question-circle"></i>
            </button>
          </div>
        </div>
      </header>
      
      <!-- Dashboard Content -->
      <div class="admin-content">
        <div class="page-header">
          <h1>Dashboard Overview</h1>
          <div class="date-display">
            <i class="far fa-calendar-alt"></i>
            <span id="current-date">Loading date...</span>
          </div>
        </div>
        
        <!-- Stats Overview -->
        <div class="stats-overview">
          <div class="stat-card">
            <div class="stat-icon blue">
              <i class="fas fa-eye"></i>
            </div>
            <div class="stat-content">
              <h3>Website Visitors</h3>
              <div class="stat-value">2,458</div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <span>15.3%</span> vs last month
              </div>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon green">
              <i class="fas fa-envelope-open"></i>
            </div>
            <div class="stat-content">
              <h3>New Inquiries</h3>
              <div class="stat-value">37</div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <span>8.2%</span> vs last month
              </div>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon purple">
              <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
              <h3>Blog Articles</h3>
              <div class="stat-value">14</div>
              <div class="stat-change neutral">
                <i class="fas fa-minus"></i>
                <span>0%</span> vs last month
              </div>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon orange">
              <i class="fas fa-user-plus"></i>
            </div>
            <div class="stat-content">
              <h3>New Subscribers</h3>
              <div class="stat-value">28</div>
              <div class="stat-change negative">
                <i class="fas fa-arrow-down"></i>
                <span>5.7%</span> vs last month
              </div>
            </div>
          </div>
        </div>
        
        <!-- Charts & Analytics -->
        <div class="analytics-section">
          <div class="chart-container">
            <div class="chart-header">
              <h2>Website Traffic</h2>
              <div class="chart-controls">
                <button class="active">Weekly</button>
                <button>Monthly</button>
                <button>Yearly</button>
              </div>
            </div>
            <div class="chart-body">
              <canvas id="traffic-chart"></canvas>
            </div>
          </div>
          
          <div class="analytics-sidebar">
            <div class="widget traffic-sources">
              <h3>Traffic Sources</h3>
              <ul class="source-list">
                <li>
                  <div class="source-info">
                    <span class="source-name">Direct</span>
                    <span class="source-value">45%</span>
                  </div>
                  <div class="progress-bar">
                    <div class="progress" style="width: 45%; background-color: #4e73df;"></div>
                  </div>
                </li>
                <li>
                  <div class="source-info">
                    <span class="source-name">Organic Search</span>
                    <span class="source-value">30%</span>
                  </div>
                  <div class="progress-bar">
                    <div class="progress" style="width: 30%; background-color: #1cc88a;"></div>
                  </div>
                </li>
                <li>
                  <div class="source-info">
                    <span class="source-name">Social Media</span>
                    <span class="source-value">15%</span>
                  </div>
                  <div class="progress-bar">
                    <div class="progress" style="width: 15%; background-color: #36b9cc;"></div>
                  </div>
                </li>
                <li>
                  <div class="source-info">
                    <span class="source-name">Referral</span>
                    <span class="source-value">10%</span>
                  </div>
                  <div class="progress-bar">
                    <div class="progress" style="width: 10%; background-color: #f6c23e;"></div>
                  </div>
                </li>
              </ul>
            </div>
            
            <div class="widget page-performance">
              <h3>Top Pages</h3>
              <ul class="page-list">
                <li>
                  <div class="page-info">
                    <span class="page-name">Home</span>
                    <span class="page-views">1,245 views</span>
                  </div>
                </li>
                <li>
                  <div class="page-info">
                    <span class="page-name">Finance & Accounting</span>
                    <span class="page-views">842 views</span>
                  </div>
                </li>
                <li>
                  <div class="page-info">
                    <span class="page-name">Contact Us</span>
                    <span class="page-views">625 views</span>
                  </div>
                </li>
                <li>
                  <div class="page-info">
                    <span class="page-name">Dedicated Teams</span>
                    <span class="page-views">418 views</span>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        
        <!-- Recent Activities -->
        <div class="activities-section">
          <div class="section-header">
            <h2>Recent Activities</h2>
            <a href="#" class="view-all">View All</a>
          </div>
          
          <div class="activity-container">
            <div class="activity-item">
              <div class="activity-icon inquiry">
                <i class="fas fa-envelope"></i>
              </div>
              <div class="activity-content">
                <h4>New Inquiry Received</h4>
                <p>John Smith submitted a contact form inquiry about Dedicated Teams.</p>
                <div class="activity-meta">
                  <span class="activity-time">2 hours ago</span>
                  <a href="#" class="activity-action">View Details</a>
                </div>
              </div>
            </div>
            
            <div class="activity-item">
              <div class="activity-icon user">
                <i class="fas fa-user-plus"></i>
              </div>
              <div class="activity-content">
                <h4>New Admin User Added</h4>
                <p>Sarah Johnson was added as a Marketing Admin.</p>
                <div class="activity-meta">
                  <span class="activity-time">Yesterday</span>
                  <a href="#" class="activity-action">View User</a>
                </div>
              </div>
            </div>
            
            <div class="activity-item">
              <div class="activity-icon content">
                <i class="fas fa-edit"></i>
              </div>
              <div class="activity-content">
                <h4>Page Content Updated</h4>
                <p>The Home page content was updated by Mark Wilson.</p>
                <div class="activity-meta">
                  <span class="activity-time">2 days ago</span>
                  <a href="#" class="activity-action">View Page</a>
                </div>
              </div>
            </div>
            
            <div class="activity-item">
              <div class="activity-icon testimonial">
                <i class="fas fa-star"></i>
              </div>
              <div class="activity-content">
                <h4>New Testimonial Added</h4>
                <p>A new testimonial from ABC Company was published.</p>
                <div class="activity-meta">
                  <span class="activity-time">3 days ago</span>
                  <a href="#" class="activity-action">View Testimonial</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Quick Actions & Recent Inquiries -->
        <div class="quick-access-section">
          <div class="quick-actions">
            <div class="section-header">
              <h2>Quick Actions</h2>
            </div>
            <div class="action-buttons">
              <a href="#" class="quick-action-btn">
                <i class="fas fa-plus"></i>
                <span>New Blog Post</span>
              </a>
              <a href="#" class="quick-action-btn">
                <i class="fas fa-edit"></i>
                <span>Edit Pages</span>
              </a>
              <a href="#" class="quick-action-btn">
                <i class="fas fa-upload"></i>
                <span>Upload Media</span>
              </a>
              <a href="#" class="quick-action-btn">
                <i class="fas fa-user-plus"></i>
                <span>Add User</span>
              </a>
              <a href="#" class="quick-action-btn">
                <i class="fas fa-briefcase"></i>
                <span>Manage Services</span>
              </a>
              <a href="#" class="quick-action-btn">
                <i class="fas fa-download"></i>
                <span>Backup Data</span>
              </a>
            </div>
          </div>
          
          <div class="recent-inquiries">
            <div class="section-header">
              <h2>Recent Inquiries</h2>
              <a href="#" class="view-all">View All</a>
            </div>
            <div class="inquiries-table-container">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>John Smith</td>
                    <td>john.smith@example.com</td>
                    <td>Dedicated Teams Inquiry</td>
                    <td>Apr 17, 2025</td>
                    <td><span class="status-badge new">New</span></td>
                    <td>
                      <div class="table-actions">
                        <a href="#" class="view-btn" title="View"><i class="fas fa-eye"></i></a>
                        <a href="#" class="reply-btn" title="Reply"><i class="fas fa-reply"></i></a>
                        <a href="#" class="delete-btn" title="Delete"><i class="fas fa-trash-alt"></i></a>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>Emma Johnson</td>
                    <td>emma.j@example.com</td>
                    <td>Business Care Plans</td>
                    <td>Apr 16, 2025</td>
                    <td><span class="status-badge new">New</span></td>
                    <td>
                      <div class="table-actions">
                        <a href="#" class="view-btn" title="View"><i class="fas fa-eye"></i></a>
                        <a href="#" class="reply-btn" title="Reply"><i class="fas fa-reply"></i></a>
                        <a href="#" class="delete-btn" title="Delete"><i class="fas fa-trash-alt"></i></a>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>Michael Chen</td>
                    <td>m.chen@example.com</td>
                    <td>Insurance Support</td>
                    <td>Apr 15, 2025</td>
                    <td><span class="status-badge replied">Replied</span></td>
                    <td>
                      <div class="table-actions">
                        <a href="#" class="view-btn" title="View"><i class="fas fa-eye"></i></a>
                        <a href="#" class="reply-btn" title="Reply"><i class="fas fa-reply"></i></a>
                        <a href="#" class="delete-btn" title="Delete"><i class="fas fa-trash-alt"></i></a>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>Sarah Davis</td>
                    <td>sarah.d@example.com</td>
                    <td>Finance & Accounting</td>
                    <td>Apr 12, 2025</td>
                    <td><span class="status-badge closed">Closed</span></td>
                    <td>
                      <div class="table-actions">
                        <a href="#" class="view-btn" title="View"><i class="fas fa-eye"></i></a>
                        <a href="#" class="reply-btn" title="Reply"><i class="fas fa-reply"></i></a>
                        <a href="#" class="delete-btn" title="Delete"><i class="fas fa-trash-alt"></i></a>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Admin Footer -->
      <footer class="admin-footer">
        <div class="footer-left">
          <p>&copy; 2025 Backsure Global Support. All rights reserved.</p>
        </div>
        <div class="footer-right">
          <span>Admin Panel v1.0</span>
        </div>
      </footer>
    </main>
  </div>
  
  <!-- JavaScript for Admin Dashboard -->
  <script>
    // Initialize dashboard functionality
    document.addEventListener('DOMContentLoaded', function() {
      // Set current date
      const now = new Date();
      document.getElementById('current-date').innerText = now.toLocaleDateString('en-US', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
      });
      
      // Traffic chart
      const trafficCtx = document.getElementById('traffic-chart').getContext('2d');
      const trafficChart = new Chart(trafficCtx, {
        type: 'line',
        data: {
          labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
          datasets: [{
            label: 'Visitors',
            data: [320, 420, 395, 450, 380, 285, 310],
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            borderColor: 'rgba(78, 115, 223, 1)',
            pointBackgroundColor: 'rgba(78, 115, 223, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
            borderWidth: 2,
            tension: 0.3,
            fill: true
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });
      
      // Sidebar toggle
      const sidebarToggle = document.getElementById('sidebar-toggle');
      const adminContainer = document.querySelector('.admin-container');
      
      sidebarToggle.addEventListener('click', function() {
        adminContainer.classList.toggle('sidebar-collapsed');
      });
      
      // User dropdown
      const userDropdownToggle = document.getElementById('user-dropdown-toggle');
      const userDropdown = document.getElementById('user-dropdown');
      
      if (userDropdownToggle && userDropdown) {
        userDropdownToggle.addEventListener('click', function(e) {
          e.stopPropagation();
          userDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
          if (!userDropdownToggle.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.classList.remove('show');
          }
        });
      }
      
      // Submenu toggle
      const submenuItems = document.querySelectorAll('.has-submenu > a');
      
      submenuItems.forEach(item => {
        item.addEventListener('click', function(e) {
          e.preventDefault();
          const parent = this.parentElement;
          
          // Close other open submenus
          const openItems = document.querySelectorAll('.has-submenu.open');
          openItems.forEach(openItem => {
            if (openItem !== parent) {
              openItem.classList.remove('open');
              const submenu = openItem.querySelector('.submenu');
              if (submenu) {
                submenu.style.maxHeight = null;
              }
            }
          });
          
          // Toggle current submenu
          parent.classList.toggle('open');
          const submenu = parent.querySelector('.submenu');
          
          if (submenu) {
            if (parent.classList.contains('open')) {
              submenu.style.maxHeight = submenu.scrollHeight + 'px';
            } else {
              submenu.style.maxHeight = null;
            }
          }
        });
      });
      
      // Initialize any other interactive elements
      const chartControls = document.querySelectorAll('.chart-controls button');
      chartControls.forEach(button => {
        button.addEventListener('click', function() {
          chartControls.forEach(btn => btn.classList.remove('active'));
          this.classList.add('active');
          // Here you would update the chart data based on the selected time period
        });
      });
    });
  </script>
</body>
</html>
