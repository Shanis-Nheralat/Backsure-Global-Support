<?php
// Start session first thing - no whitespace before this
session_start();

// Block unauthorized users
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Check role permissions - only Super Admin can access this page
if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] !== 'admin' && $_SESSION['admin_role'] !== 'superadmin') {
    header("Location: admin-dashboard.php?error=unauthorized");
    exit();
}

// Include database configuration
require_once 'db_config.php';

// Get admin information from session
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin User';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'Administrator';

// Initialize variables
$users = [];
$message = '';
$messageType = '';
$editingUser = null;

// Database connection
try {
    $pdo = get_db_connection();
    
    // Process form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Add/Edit User
        if (isset($_POST['action']) && ($_POST['action'] === 'add' || $_POST['action'] === 'edit')) {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $role = $_POST['role'];
            $status = isset($_POST['status']) ? $_POST['status'] : 'active';
            
            // Validate inputs
            if (empty($username) || empty($email) || empty($role)) {
                $message = "All fields are required.";
                $messageType = "error";
            } else {
                if ($_POST['action'] === 'add') {
                    // Check if username or email already exists
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $email]);
                    $exists = $stmt->fetchColumn();
                    
                    if ($exists) {
                        $message = "Username or email already exists.";
                        $messageType = "error";
                    } else {
                        // Generate a random password
                        $password = bin2hex(random_bytes(4)); // 8 characters
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        
                        // Insert new user
                        $stmt = $pdo->prepare("INSERT INTO admins (username, email, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                        if ($stmt->execute([$username, $email, $hashedPassword, $role, $status])) {
                            $message = "User added successfully. Temporary password: {$password}";
                            $messageType = "success";
                        } else {
                            $message = "Error adding user.";
                            $messageType = "error";
                        }
                    }
                } else { // Edit
                    $userId = $_POST['user_id'];
                    // Update user
                    $stmt = $pdo->prepare("UPDATE admins SET username = ?, email = ?, role = ?, status = ? WHERE id = ?");
                    if ($stmt->execute([$username, $email, $role, $status, $userId])) {
                        $message = "User updated successfully.";
                        $messageType = "success";
                    } else {
                        $message = "Error updating user.";
                        $messageType = "error";
                    }
                }
            }
        }
        
        // Reset Password
        if (isset($_POST['action']) && $_POST['action'] === 'reset_password') {
            $userId = $_POST['user_id'];
            
            // Generate a new password
            $newPassword = bin2hex(random_bytes(4)); // 8 characters
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update password
            $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashedPassword, $userId])) {
                $message = "Password reset successfully. New password: {$newPassword}";
                $messageType = "success";
            } else {
                $message = "Error resetting password.";
                $messageType = "error";
            }
        }
        
        // Delete User
        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $userId = $_POST['user_id'];
            
            // Check if trying to delete yourself
            if ($userId == $_SESSION['admin_id']) {
                $message = "You cannot delete your own account.";
                $messageType = "error";
            } else {
                // Delete user
                $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
                if ($stmt->execute([$userId])) {
                    $message = "User deleted successfully.";
                    $messageType = "success";
                } else {
                    $message = "Error deleting user.";
                    $messageType = "error";
                }
            }
        }
    }
    
    // Handle edit request from GET
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $userId = $_GET['id'];
        $stmt = $pdo->prepare("SELECT id, username, email, role, status FROM admins WHERE id = ?");
        $stmt->execute([$userId]);
        $editingUser = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get all users for display
    $stmt = $pdo->query("SELECT id, username, email, role, status, created_at, last_login FROM admins ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $message = "Database error: " . $e->getMessage();
    $messageType = "error";
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="robots" content="noindex, nofollow">
  <title>User Management | Backsure Global Support</title>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <style>
    /* Reuse dashboard styles */
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

    .breadcrumbs a:after {
      content: '/';
      margin: 0 5px;
      color: var(--gray-400);
    }

    .breadcrumbs a:last-child:after {
      display: none;
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

    .page-header-actions {
      display: flex;
      gap: 10px;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 8px 16px;
      border-radius: 4px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
      border: none;
      font-family: inherit;
      font-size: 0.9rem;
    }

    .btn i {
      margin-right: 8px;
    }

    .btn-primary {
      background-color: var(--primary-color);
      color: white;
    }

    .btn-primary:hover {
      background-color: var(--primary-dark);
    }

    .btn-success {
      background-color: var(--success-color);
      color: white;
    }

    .btn-success:hover {
      background-color: #169b6b;
    }

    .btn-danger {
      background-color: var(--danger-color);
      color: white;
    }

    .btn-danger:hover {
      background-color: #c53030;
    }

    .btn-warning {
      background-color: var(--warning-color);
      color: white;
    }

    .btn-warning:hover {
      background-color: #d69e2e;
    }

    .btn-info {
      background-color: var(--info-color);
      color: white;
    }

    .btn-info:hover {
      background-color: #2c9faf;
    }

    .btn-secondary {
      background-color: var(--gray-500);
      color: white;
    }

    .btn-secondary:hover {
      background-color: var(--gray-600);
    }

    .btn-sm {
      padding: 5px 10px;
      font-size: 0.8rem;
    }
    
    /* Message styles */
    .message {
      padding: 15px;
      border-radius: 4px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
    }
    
    .message i {
      margin-right: 10px;
      font-size: 1.2rem;
    }
    
    .message-success {
      background-color: rgba(28, 200, 138, 0.1);
      color: var(--success-color);
      border-left: 4px solid var(--success-color);
    }
    
    .message-error {
      background-color: rgba(231, 74, 59, 0.1);
      color: var(--danger-color);
      border-left: 4px solid var(--danger-color);
    }
    
    .message-warning {
      background-color: rgba(246, 194, 62, 0.1);
      color: var(--warning-color);
      border-left: 4px solid var(--warning-color);
    }
    
    .message-info {
      background-color: rgba(54, 185, 204, 0.1);
      color: var(--info-color);
      border-left: 4px solid var(--info-color);
    }

    /* Card styles */
    .card {
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      margin-bottom: 20px;
    }

    .card-header {
      padding: 15px 20px;
      border-bottom: 1px solid var(--gray-200);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .card-header h2 {
      font-size: 1.2rem;
      margin: 0;
      color: var(--gray-800);
    }

    .card-body {
      padding: 20px;
    }

    .card-footer {
      padding: 15px 20px;
      border-top: 1px solid var(--gray-200);
    }

    /* Form styles */
    .form-row {
      display: flex;
      flex-wrap: wrap;
      margin: -10px;
      margin-bottom: 10px;
    }

    .form-group {
      flex: 1;
      min-width: 200px;
      padding: 10px;
    }

    .form-label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
      color: var(--gray-700);
    }

    .form-control {
      width: 100%;
      padding: 8px 12px;
      border: 1px solid var(--gray-300);
      border-radius: 4px;
      font-family: inherit;
      font-size: 0.9rem;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(6, 39, 103, 0.25);
    }

    select.form-control {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3E%3Cpath fill='%23333' d='M0 0l4 4 4-4z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      padding-right: 30px;
    }

    .form-text {
      font-size: 0.8rem;
      color: var(--gray-600);
      margin-top: 5px;
    }

    .form-check {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .form-check-input {
      margin-right: 10px;
    }

    .required:after {
      content: '*';
      color: var(--danger-color);
      margin-left: 2px;
    }

    /* Table styles */
    .table-container {
      overflow-x: auto;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
    }

    .table th,
    .table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid var(--gray-200);
    }

    .table th {
      font-weight: 600;
      color: var(--gray-700);
      background-color: var(--gray-100);
    }

    .table tbody tr:hover {
      background-color: var(--gray-50);
    }

    .table .actions {
      white-space: nowrap;
      display: flex;
      gap: 8px;
    }

    .status-badge {
      display: inline-flex;
      padding: 3px 10px;
      border-radius: 50px;
      font-size: 0.75rem;
      font-weight: 600;
    }

    .status-active {
      background-color: rgba(28, 200, 138, 0.1);
      color: var(--success-color);
    }

    .status-inactive {
      background-color: rgba(133, 135, 150, 0.1);
      color: var(--gray-600);
    }

    .status-blocked {
      background-color: rgba(231, 74, 59, 0.1);
      color: var(--danger-color);
    }
    
    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
    }
    
    .modal-content {
      background-color: white;
      margin: 10% auto;
      padding: 0;
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      width: 500px;
      max-width: 90%;
      animation: modal-open 0.3s ease-out;
    }
    
    @keyframes modal-open {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .modal-header {
      padding: 15px 20px;
      border-bottom: 1px solid var(--gray-200);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .modal-header h3 {
      margin: 0;
      font-size: 1.2rem;
    }
    
    .modal-close {
      background: none;
      border: none;
      font-size: 1.5rem;
      color: var(--gray-600);
      cursor: pointer;
    }
    
    .modal-body {
      padding: 20px;
    }
    
    .modal-footer {
      padding: 15px 20px;
      border-top: 1px solid var(--gray-200);
      display: flex;
      justify-content: flex-end;
      gap: 10px;
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
      .form-row {
        flex-direction: column;
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
      
      .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
      
      .page-header-actions {
        width: 100%;
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
            <a href="admin-dashboard.php">
              <i class="fas fa-tachometer-alt"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li class="has-submenu open">
            <a href="javascript:void(0)">
              <i class="fas fa-users"></i>
              <span>User Management</span>
              <i class="fas fa-chevron-right submenu-icon"></i>
            </a>
            <ul class="submenu">
              <li class="active"><a href="admin-users.php"><i class="fas fa-user-friends"></i> All Users</a></li>
              <li><a href="admin-roles.php"><i class="fas fa-user-tag"></i> Roles & Permissions</a></li>
            </ul>
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
              <li><a href="admin-services.php"><i class="fas fa-briefcase"></i> Services Editor</a></li>
              <li><a href="#"><i class="fas fa-images"></i> Media Library</a></li>
            </ul>
          </li>
          <li>
            <a href="admin-inquiries.php">
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
            <a href="admin-users.php">User Management</a>
          </div>
        </div>
        
        <div class="header-right">
          <div class="admin-search">
            <input type="text" placeholder="Search users...">
            <button type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
          
          <div class="header-actions">
            <button class="action-btn notification-btn">
              <i class="fas fa-bell"></i>
              <span class="badge">5</span>
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
          <h1>User Management</h1>
          <div class="page-header-actions">
            <button id="add-user-btn" class="btn btn-primary"><i class="fas fa-plus"></i> Add New User</button>
          </div>
        </div>
        
        <?php if (!empty($message)): ?>
        <div class="message message-<?php echo $messageType; ?>">
          <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
          <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <!-- User Management Section -->
        <div class="card">
          <div class="card-header">
            <h2>Admin Users</h2>
            <div class="card-header-actions">
              <select id="role-filter" class="form-control">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="superadmin">Super Admin</option>
                <option value="hr">HR Admin</option>
                <option value="marketing">Marketing Admin</option>
              </select>
            </div>
          </div>
          <div class="card-body">
            <div class="table-container">
              <table class="table" id="users-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($users as $user): ?>
                  <tr data-role="<?php echo htmlspecialchars($user['role']); ?>">
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                    <td>
                      <span class="status-badge status-<?php echo htmlspecialchars(strtolower($user['status'])); ?>">
                        <?php echo htmlspecialchars(ucfirst($user['status'])); ?>
                      </span>
                    </td>
                    <td><?php echo htmlspecialchars(date('M d, Y', strtotime($user['created_at']))); ?></td>
                    <td><?php echo $user['last_login'] ? htmlspecialchars(date('M d, Y', strtotime($user['last_login']))) : 'Never'; ?></td>
                    <td class="actions">
                      <a href="admin-users.php?action=edit&id=<?php echo $user['id']; ?>" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                      
                      <button class="btn btn-warning btn-sm reset-password-btn" data-id="<?php echo $user['id']; ?>" data-username="<?php echo htmlspecialchars($user['username']); ?>">
                        <i class="fas fa-key"></i>
                      </button>
                      
                      <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                      <button class="btn btn-danger btn-sm delete-user-btn" data-id="<?php echo $user['id']; ?>" data-username="<?php echo htmlspecialchars($user['username']); ?>">
                        <i class="fas fa-trash"></i>
                      </button>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
        <!-- Add/Edit User Modal -->
        <div id="user-modal" class="modal">
          <div class="modal-content">
            <div class="modal-header">
              <h3><?php echo $editingUser ? 'Edit User' : 'Add New User'; ?></h3>
              <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
              <form id="user-form" method="post" action="admin-users.php">
                <input type="hidden" name="action" value="<?php echo $editingUser ? 'edit' : 'add'; ?>">
                <?php if ($editingUser): ?>
                <input type="hidden" name="user_id" value="<?php echo $editingUser['id']; ?>">
                <?php endif; ?>
                
                <div class="form-row">
                  <div class="form-group">
                    <label for="username" class="form-label required">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo $editingUser ? htmlspecialchars($editingUser['username']) : ''; ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="email" class="form-label required">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo $editingUser ? htmlspecialchars($editingUser['email']) : ''; ?>" required>
                  </div>
                </div>
                
                <div class="form-row">
                  <div class="form-group">
                    <label for="role" class="form-label required">Role</label>
                    <select id="role" name="role" class="form-control" required>
                      <option value="">Select Role</option>
                      <option value="admin" <?php echo ($editingUser && $editingUser['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                      <option value="superadmin" <?php echo ($editingUser && $editingUser['role'] === 'superadmin') ? 'selected' : ''; ?>>Super Admin</option>
                      <option value="hr" <?php echo ($editingUser && $editingUser['role'] === 'hr') ? 'selected' : ''; ?>>HR Admin</option>
                      <option value="marketing" <?php echo ($editingUser && $editingUser['role'] === 'marketing') ? 'selected' : ''; ?>>Marketing Admin</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="status" class="form-label required">Status</label>
                    <select id="status" name="status" class="form-control" required>
                      <option value="active" <?php echo ($editingUser && $editingUser['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                      <option value="inactive" <?php echo ($editingUser && $editingUser['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                      <option value="blocked" <?php echo ($editingUser && $editingUser['status'] === 'blocked') ? 'selected' : ''; ?>>Blocked</option>
                    </select>
                  </div>
                </div>
                
                <?php if (!$editingUser): ?>
                <div class="form-text">
                  <p>A temporary password will be generated automatically and displayed after user creation.</p>
                </div>
                <?php endif; ?>
              
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
                  <button type="submit" class="btn btn-primary"><?php echo $editingUser ? 'Update User' : 'Add User'; ?></button>
                </div>
              </form>
            </div>
          </div>
        </div>
        
        <!-- Reset Password Modal -->
        <div id="reset-password-modal" class="modal">
          <div class="modal-content">
            <div class="modal-header">
              <h3>Reset Password</h3>
              <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
              <p>Are you sure you want to reset the password for <strong id="reset-username"></strong>?</p>
              <p>A new temporary password will be generated and displayed.</p>
              
              <form id="reset-password-form" method="post" action="admin-users.php">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="user_id" id="reset-user-id">
                
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
                  <button type="submit" class="btn btn-warning">Reset Password</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        
        <!-- Delete User Modal -->
        <div id="delete-user-modal" class="modal">
          <div class="modal-content">
            <div class="modal-header">
              <h3>Delete User</h3>
              <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
              <p>Are you sure you want to delete user <strong id="delete-username"></strong>?</p>
              <p>This action cannot be undone.</p>
              
              <form id="delete-user-form" method="post" action="admin-users.php">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="user_id" id="delete-user-id">
                
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
                  <button type="submit" class="btn btn-danger">Delete User</button>
                </div>
              </form>
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
    // Initialize user management functionality
    document.addEventListener('DOMContentLoaded', function() {
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
      
      // Modal functionality
      const modals = document.querySelectorAll('.modal');
      const modalCloseBtns = document.querySelectorAll('.modal-close, .modal-close-btn');
      
      // Show modal
      function showModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
      }
      
      // Hide modal
      function hideModal(modal) {
        modal.style.display = 'none';
      }
      
      // Close modal when clicking close button
      modalCloseBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          const modal = this.closest('.modal');
          hideModal(modal);
        });
      });
      
      // Close modal when clicking outside
      window.addEventListener('click', function(e) {
        modals.forEach(modal => {
          if (e.target === modal) {
            hideModal(modal);
          }
        });
      });
      
      // Show add user modal
      const addUserBtn = document.getElementById('add-user-btn');
      if (addUserBtn) {
        addUserBtn.addEventListener('click', function() {
          showModal('user-modal');
        });
      }
      
      // User edit modal was triggered from GET parameter
      <?php if ($editingUser): ?>
      showModal('user-modal');
      <?php endif; ?>
      
      // Reset password modal
      const resetPasswordBtns = document.querySelectorAll('.reset-password-btn');
      resetPasswordBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          const userId = this.getAttribute('data-id');
          const username = this.getAttribute('data-username');
          
          document.getElementById('reset-user-id').value = userId;
          document.getElementById('reset-username').textContent = username;
          
          showModal('reset-password-modal');
        });
      });
      
      // Delete user modal
      const deleteUserBtns = document.querySelectorAll('.delete-user-btn');
      deleteUserBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          const userId = this.getAttribute('data-id');
          const username = this.getAttribute('data-username');
          
          document.getElementById('delete-user-id').value = userId;
          document.getElementById('delete-username').textContent = username;
          
          showModal('delete-user-modal');
        });
      });
      
      // Role filter functionality
      const roleFilter = document.getElementById('role-filter');
      roleFilter.addEventListener('change', function() {
        const selectedRole = this.value;
        const rows = document.querySelectorAll('#users-table tbody tr');
        
        rows.forEach(row => {
          const rowRole = row.getAttribute('data-role');
          if (selectedRole === '' || rowRole === selectedRole) {
            row.style.display = 'table-row';
          } else {
            row.style.display = 'none';
          }
        });
      });
    });
  </script>
</body>
</html>