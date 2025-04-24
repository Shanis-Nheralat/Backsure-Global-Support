<?php
// Start session first thing - no whitespace before this
session_start();

// Block unauthorized users
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Check role permissions - only Super Admin and Marketing Admin can access this page
$allowed_roles = ['admin', 'superadmin', 'marketing'];
if (isset($_SESSION['admin_role']) && !in_array($_SESSION['admin_role'], $allowed_roles)) {
    header("Location: admin-dashboard.php?error=unauthorized");
    exit();
}

// Super Admins can edit and delete, Marketing Admins can only edit
$canDelete = (isset($_SESSION['admin_role']) && ($_SESSION['admin_role'] === 'admin' || $_SESSION['admin_role'] === 'superadmin'));

// Include database configuration
require_once 'db_config.php';

// Get admin information from session
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin User';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'Administrator';

// Initialize variables
$services = [];
$message = '';
$messageType = '';
$editingService = null;
$categories = ['Accounting', 'HR', 'IT', 'Marketing', 'Customer Support', 'Administration'];

// Database connection
try {
    $pdo = get_db_connection();
    
    // Create services table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        icon VARCHAR(50),
        image VARCHAR(255),
        category VARCHAR(100),
        tags TEXT,
        display_order INT DEFAULT 0,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Process form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Add/Edit Service
        if (isset($_POST['action']) && ($_POST['action'] === 'add' || $_POST['action'] === 'edit')) {
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $icon = trim($_POST['icon']);
            $category = $_POST['category'];
            $tags = trim($_POST['tags']);
            $display_order = (int)$_POST['display_order'];
            $status = isset($_POST['status']) ? $_POST['status'] : 'active';
            
            // Handle image upload
            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/services/';
                
                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = basename($_FILES['image']['name']);
                $targetFile = $uploadDir . time() . '_' . $fileName;
                
                // Check if image file is an actual image
                $check = getimagesize($_FILES['image']['tmp_name']);
                if ($check !== false) {
                    // Upload file
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                        $image = $targetFile;
                    }
                }
            } elseif (isset($_POST['existing_image']) && !empty($_POST['existing_image'])) {
                $image = $_POST['existing_image'];
            }
            
            // Validate inputs
            if (empty($title)) {
                $message = "Service title is required.";
                $messageType = "error";
            } else {
                if ($_POST['action'] === 'add') {
                    // Insert new service
                    $stmt = $pdo->prepare("INSERT INTO services (title, description, icon, image, category, tags, display_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute([$title, $description, $icon, $image, $category, $tags, $display_order, $status])) {
                        $message = "Service added successfully.";
                        $messageType = "success";
                    } else {
                        $message = "Error adding service.";
                        $messageType = "error";
                    }
                } else { // Edit
                    $serviceId = $_POST['service_id'];
                    
                    // Check if user has permission to edit
                    if ($_SESSION['admin_role'] === 'marketing' && !$canDelete) {
                        // Marketing admins can only update content, not status
                        $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, icon = ?, image = ?, category = ?, tags = ?, display_order = ? WHERE id = ?");
                        if ($stmt->execute([$title, $description, $icon, $image, $category, $tags, $display_order, $serviceId])) {
                            $message = "Service updated successfully.";
                            $messageType = "success";
                        } else {
                            $message = "Error updating service.";
                            $messageType = "error";
                        }
                    } else {
                        // Super admins can update everything
                        $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, icon = ?, image = ?, category = ?, tags = ?, display_order = ?, status = ? WHERE id = ?");
                        if ($stmt->execute([$title, $description, $icon, $image, $category, $tags, $display_order, $status, $serviceId])) {
                            $message = "Service updated successfully.";
                            $messageType = "success";
                        } else {
                            $message = "Error updating service.";
                            $messageType = "error";
                        }
                    }
                }
            }
        }
        
        // Delete Service
        if (isset($_POST['action']) && $_POST['action'] === 'delete' && $canDelete) {
            $serviceId = $_POST['service_id'];
            
            // Get service image before deleting
            $stmt = $pdo->prepare("SELECT image FROM services WHERE id = ?");
            $stmt->execute([$serviceId]);
            $serviceImage = $stmt->fetchColumn();
            
            // Delete service
            $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
            if ($stmt->execute([$serviceId])) {
                // Delete service image if exists
                if (!empty($serviceImage) && file_exists($serviceImage)) {
                    unlink($serviceImage);
                }
                
                $message = "Service deleted successfully.";
                $messageType = "success";
            } else {
                $message = "Error deleting service.";
                $messageType = "error";
            }
        }
    }
    
    // Handle edit request from GET
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $serviceId = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$serviceId]);
        $editingService = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get all services for display
    $stmt = $pdo->query("SELECT * FROM services ORDER BY display_order, title");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
  <title>Services Management | Backsure Global Support</title>
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

    textarea.form-control {
      min-height: 120px;
      resize: vertical;
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

    /* Services Grid */
    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
    }

    .service-card {
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      transition: transform 0.3s, box-shadow 0.3s;
      position: relative;
    }

    .service-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .service-image {
      height: 160px;
      background-color: var(--gray-200);
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      position: relative;
    }

    .service-image.no-image {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .service-image.no-image i {
      font-size: 48px;
      color: var(--gray-500);
    }

    .service-status {
      position: absolute;
      top: 10px;
      right: 10px;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.7rem;
      font-weight: 600;
      text-transform: uppercase;
    }

    .service-status.active {
      background-color: var(--success-color);
      color: white;
    }

    .service-status.inactive {
      background-color: var(--gray-500);
      color: white;
    }

    .service-content {
      padding: 20px;
    }

    .service-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin: 0 0 10px 0;
      color: var(--gray-800);
    }

    .service-icon {
      margin-right: 10px;
      color: var(--primary-color);
    }

    .service-category {
      font-size: 0.8rem;
      color: var(--gray-600);
      margin-bottom: 10px;
      display: inline-block;
      background-color: var(--gray-100);
      padding: 3px 8px;
      border-radius: 4px;
    }

    .service-description {
      color: var(--gray-700);
      margin-bottom: 15px;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .service-tags {
      font-size: 0.8rem;
      color: var(--gray-600);
      margin-bottom: 15px;
    }

    .service-tag {
      display: inline-block;
      background-color: var(--gray-100);
      padding: 2px 6px;
      border-radius: 4px;
      margin-right: 5px;
      margin-bottom: 5px;
    }

    .service-actions {
      display: flex;
      justify-content: flex-end;
      gap: 8px;
      border-top: 1px solid var(--gray-200);
      padding-top: 15px;
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
      margin: 5% auto;
      padding: 0;
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      width: 700px;
      max-width: 90%;
      max-height: 90vh;
      overflow-y: auto;
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
      position: sticky;
      top: 0;
      background-color: white;
      z-index: 1;
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
      position: sticky;
      bottom: 0;
      background-color: white;
      z-index: 1;
    }

    /* Preview image */
    .image-preview {
      max-width: 100%;
      height: 160px;
      background-color: var(--gray-100);
      background-position: center;
      background-size: cover;
      background-repeat: no-repeat;
      border-radius: 4px;
      margin-top: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--gray-500);
      font-size: 0.9rem;
    }

    /* Icon picker */
    .icon-picker {
      max-height: 200px;
      overflow-y: auto;
      border: 1px solid var(--gray-300);
      border-radius: 4px;
      padding: 10px;
      margin-top: 10px;
      display: grid;
      grid-template-columns: repeat(8, 1fr);
      gap: 10px;
    }

    .icon-option {
      width: 32px;
      height: 32px;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s;
    }

    .icon-option:hover {
      background-color: var(--gray-200);
    }

    .icon-option.selected {
      background-color: var(--primary-color);
      color: white;
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
      
      .services-grid {
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
          <li><a href="admin-profile.php"><i class="fas fa-user"></i> My Profile</a></li>
          <li><a href="admin-general.php"><i class="fas fa-cog"></i> Settings</a></li>
          <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
      </div>
      
      <nav class="sidebar-nav">
        <ul>
          <!-- Dashboard (Priority 1) -->
          <li>
            <a href="admin-dashboard.php">
              <i class="fas fa-tachometer-alt"></i>
              <span>Dashboard</span>
            </a>
          </li>
          
          <!-- Content Management (Priority 2) -->
          <li class="has-submenu open">
            <a href="javascript:void(0)">
              <i class="fas fa-edit"></i>
              <span>Content Management</span>
              <i class="fas fa-chevron-right submenu-icon"></i>
            </a>
            <ul class="submenu">
              <li><a href="admin-blog.php"><i class="fas fa-blog"></i> Blog Management</a></li>
              <li class="active"><a href="admin-services.php"><i class="fas fa-briefcase"></i> Services Editor</a></li>
              <li><a href="admin-testimonials.php"><i class="fas fa-star"></i> Testimonials & Logos</a></li>
              <li><a href="admin-faq.php"><i class="fas fa-question-circle"></i> FAQ Management</a></li>
              <li><a href="admin-solutions.php"><i class="fas fa-file-alt"></i> Solutions</a></li>
            </ul>
          </li>
          
          <!-- CRM / Clients (Priority 3) -->
          <li class="has-submenu">
            <a href="javascript:void(0)">
              <i class="fas fa-envelope"></i>
              <span>CRM</span>
              <i class="fas fa-chevron-right submenu-icon"></i>
            </a>
            <ul class="submenu">
              <li><a href="admin-enquiries.php"><i class="fas fa-envelope-open-text"></i> Client Inquiries</a></li>
              <li><a href="admin-subscribers.php"><i class="fas fa-envelope-open"></i> Subscribers</a></li>
              <li><a href="admin-clients.php"><i class="fas fa-users"></i> Clients</a></li>
            </ul>
          </li>
          
          <!-- HR Tools (Priority 4) -->
          <li class="has-submenu">
            <a href="javascript:void(0)">
              <i class="fas fa-user-tie"></i>
              <span>HR Tools</span>
              <i class="fas fa-chevron-right submenu-icon"></i>
            </a>
            <ul class="submenu">
              <li><a href="admin-candidate.php"><i class="fas fa-user-graduate"></i> Candidates</a></li>
              <li><a href="admin-candidate-notes.php"><i class="fas fa-sticky-note"></i> Candidate Notes</a></li>
            </ul>
          </li>
          
          <!-- Users & Roles (Priority 5) -->
          <li class="has-submenu">
            <a href="javascript:void(0)">
              <i class="fas fa-users-cog"></i>
              <span>User Management</span>
              <i class="fas fa-chevron-right submenu-icon"></i>
            </a>
            <ul class="submenu">
              <li><a href="admin-users.php"><i class="fas fa-user-friends"></i> All Users</a></li>
              <li><a href="admin-roles.php"><i class="fas fa-user-tag"></i> Roles & Permissions</a></li>
              <li><a href="admin-profile.php"><i class="fas fa-id-card"></i> My Profile</a></li>
            </ul>
          </li>
          
          <!-- Settings (Priority 6) -->
          <li class="has-submenu">
            <a href="javascript:void(0)">
              <i class="fas fa-cogs"></i>
              <span>Site Settings</span>
              <i class="fas fa-chevron-right submenu-icon"></i>
            </a>
            <ul class="submenu">
              <li><a href="admin-seo.php"><i class="fas fa-search"></i> SEO Settings</a></li>
              <li><a href="admin-general.php"><i class="fas fa-sliders-h"></i> General Settings</a></li>
              <li><a href="admin-integrations.php"><i class="fas fa-plug"></i> Integrations</a></li>
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
            <a href="admin-services.php">Services Management</a>
          </div>
        </div>
        
        <div class="header-right">
          <div class="admin-search">
            <input type="text" placeholder="Search services...">
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
          <h1>Services Management</h1>
          <div class="page-header-actions">
            <button id="add-service-btn" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Service</button>
          </div>
        </div>
        
        <?php if (!empty($message)): ?>
        <div class="message message-<?php echo $messageType; ?>">
          <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
          <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <!-- Services Grid -->
        <div class="services-grid">
          <?php foreach ($services as $service): ?>
          <div class="service-card">
            <?php if (!empty($service['image']) && file_exists($service['image'])): ?>
            <div class="service-image" style="background-image: url('<?php echo htmlspecialchars($service['image']); ?>');">
            <?php else: ?>
            <div class="service-image no-image">
              <i class="<?php echo !empty($service['icon']) ? htmlspecialchars($service['icon']) : 'fas fa-briefcase'; ?>"></i>
            <?php endif; ?>
              <div class="service-status <?php echo htmlspecialchars($service['status']); ?>">
                <?php echo htmlspecialchars(ucfirst($service['status'])); ?>
              </div>
            </div>
            <div class="service-content">
              <h3 class="service-title">
                <i class="service-icon <?php echo !empty($service['icon']) ? htmlspecialchars($service['icon']) : 'fas fa-briefcase'; ?>"></i>
                <?php echo htmlspecialchars($service['title']); ?>
              </h3>
              <div class="service-category">
                <?php echo htmlspecialchars($service['category']); ?>
              </div>
              <div class="service-description">
                <?php echo htmlspecialchars($service['description']); ?>
              </div>
              <?php if (!empty($service['tags'])): ?>
              <div class="service-tags">
                <?php 
                $tags = explode(',', $service['tags']);
                foreach ($tags as $tag): 
                  $tag = trim($tag);
                  if (!empty($tag)):
                ?>
                <span class="service-tag"><?php echo htmlspecialchars($tag); ?></span>
                <?php 
                  endif;
                endforeach; 
                ?>
              </div>
              <?php endif; ?>
              <div class="service-actions">
                <a href="admin-services.php?action=edit&id=<?php echo $service['id']; ?>" class="btn btn-info btn-sm"><i class="fas fa-edit"></i> Edit</a>
                
                <?php if ($canDelete): ?>
                <button class="btn btn-danger btn-sm delete-service-btn" data-id="<?php echo $service['id']; ?>" data-title="<?php echo htmlspecialchars($service['title']); ?>">
                  <i class="fas fa-trash"></i> Delete
                </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
          
          <?php if (empty($services)): ?>
          <div class="card" style="grid-column: 1 / -1;">
            <div class="card-body" style="text-align: center; padding: 50px 20px;">
              <i class="fas fa-briefcase" style="font-size: 48px; color: var(--gray-300); margin-bottom: 20px;"></i>
              <h3>No services found</h3>
              <p>Start by adding your first service using the button above.</p>
            </div>
          </div>
          <?php endif; ?>
        </div>
        
        <!-- Add/Edit Service Modal -->
        <div id="service-modal" class="modal">
          <div class="modal-content">
            <div class="modal-header">
              <h3><?php echo $editingService ? 'Edit Service' : 'Add New Service'; ?></h3>
              <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
              <form id="service-form" method="post" action="admin-services.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo $editingService ? 'edit' : 'add'; ?>">
                <?php if ($editingService): ?>
                <input type="hidden" name="service_id" value="<?php echo $editingService['id']; ?>">
                <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($editingService['image']); ?>">
                <?php endif; ?>
                
                <div class="form-row">
                  <div class="form-group">
                    <label for="title" class="form-label required">Service Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?php echo $editingService ? htmlspecialchars($editingService['title']) : ''; ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="category" class="form-label required">Category</label>
                    <select id="category" name="category" class="form-control" required>
                      <option value="">Select Category</option>
                      <?php foreach ($categories as $category): ?>
                      <option value="<?php echo htmlspecialchars($category); ?>" <?php echo ($editingService && $editingService['category'] === $category) ? 'selected' : ''; ?>><?php echo htmlspecialchars($category); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="description" class="form-label">Description</label>
                  <textarea id="description" name="description" class="form-control"><?php echo $editingService ? htmlspecialchars($editingService['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-row">
                  <div class="form-group">
                    <label for="icon" class="form-label">Icon (FontAwesome)</label>
                    <input type="text" id="icon" name="icon" class="form-control" value="<?php echo $editingService ? htmlspecialchars($editingService['icon']) : 'fas fa-briefcase'; ?>" placeholder="fas fa-briefcase">
                    <div class="form-text">Choose from the icon picker below or enter a FontAwesome class.</div>
                    
                    <div class="icon-picker">
                      <?php 
                      $icons = ['fas fa-briefcase', 'fas fa-chart-line', 'fas fa-users', 'fas fa-cogs', 'fas fa-laptop', 'fas fa-file-invoice-dollar', 'fas fa-headset', 'fas fa-shield-alt', 'fas fa-globe', 'fas fa-server', 'fas fa-chart-bar', 'fas fa-desktop', 'fas fa-mobile-alt', 'fas fa-envelope', 'fas fa-search', 'fas fa-database'];
                      foreach ($icons as $icon): 
                      ?>
                      <div class="icon-option <?php echo ($editingService && $editingService['icon'] === $icon) ? 'selected' : ''; ?>" data-icon="<?php echo $icon; ?>">
                        <i class="<?php echo $icon; ?>"></i>
                      </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="image" class="form-label">Image</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                    <div class="form-text">Recommended size: 600x400px. Leave empty to keep current image.</div>
                    
                    <?php if ($editingService && !empty($editingService['image']) && file_exists($editingService['image'])): ?>
                    <div class="image-preview" style="background-image: url('<?php echo htmlspecialchars($editingService['image']); ?>');"></div>
                    <?php else: ?>
                    <div class="image-preview">No image selected</div>
                    <?php endif; ?>
                  </div>
                </div>
                
                <div class="form-row">
                  <div class="form-group">
                    <label for="tags" class="form-label">Tags</label>
                    <input type="text" id="tags" name="tags" class="form-control" value="<?php echo $editingService ? htmlspecialchars($editingService['tags']) : ''; ?>" placeholder="Tag1, Tag2, Tag3">
                    <div class="form-text">Separate tags with commas.</div>
                  </div>
                  
                  <div class="form-group">
                    <label for="display_order" class="form-label">Display Order</label>
                    <input type="number" id="display_order" name="display_order" class="form-control" value="<?php echo $editingService ? (int)$editingService['display_order'] : 0; ?>" min="0">
                    <div class="form-text">Lower numbers will be displayed first.</div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="status" class="form-label">Status</label>
                  <select id="status" name="status" class="form-control" <?php echo ($_SESSION['admin_role'] === 'marketing' && !$canDelete) ? 'disabled' : ''; ?>>
                    <option value="active" <?php echo ($editingService && $editingService['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($editingService && $editingService['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                  </select>
                  <?php if ($_SESSION['admin_role'] === 'marketing' && !$canDelete): ?>
                  <div class="form-text">Marketing admins cannot change service status.</div>
                  <?php endif; ?>
                </div>
              
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
                  <button type="submit" class="btn btn-primary"><?php echo $editingService ? 'Update Service' : 'Add Service'; ?></button>
                </div>
              </form>
            </div>
          </div>
        </div>
        
        <!-- Delete Service Modal -->
        <div id="delete-service-modal" class="modal">
          <div class="modal-content">
            <div class="modal-header">
              <h3>Delete Service</h3>
              <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
              <p>Are you sure you want to delete the service <strong id="delete-service-title"></strong>?</p>
              <p>This action cannot be undone.</p>
              
              <form id="delete-service-form" method="post" action="admin-services.php">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="service_id" id="delete-service-id">
                
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
                  <button type="submit" class="btn btn-danger">Delete Service</button>
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
    // Initialize services management functionality
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
      
      // Show add service modal
      const addServiceBtn = document.getElementById('add-service-btn');
      if (addServiceBtn) {
        addServiceBtn.addEventListener('click', function() {
          showModal('service-modal');
        });
      }
      
      // Service edit modal was triggered from GET parameter
      <?php if ($editingService): ?>
      showModal('service-modal');
      <?php endif; ?>
      
      // Delete service modal
      const deleteServiceBtns = document.querySelectorAll('.delete-service-btn');
      deleteServiceBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          const serviceId = this.getAttribute('data-id');
          const serviceTitle = this.getAttribute('data-title');
          
          document.getElementById('delete-service-id').value = serviceId;
          document.getElementById('delete-service-title').textContent = serviceTitle;
          
          showModal('delete-service-modal');
        });
      });
      
      // Icon picker
      const iconPicker = document.querySelectorAll('.icon-option');
      const iconInput = document.getElementById('icon');
      
      iconPicker.forEach(icon => {
        icon.addEventListener('click', function() {
          // Remove selected class from all icons
          iconPicker.forEach(i => i.classList.remove('selected'));
          
          // Add selected class to clicked icon
          this.classList.add('selected');
          
          // Update input value
          iconInput.value = this.getAttribute('data-icon');
        });
      });
      
      // Image preview
      const imageInput = document.getElementById('image');
      const imagePreview = document.querySelector('.image-preview');
      
      if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
          if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
              imagePreview.style.backgroundImage = 'url(' + e.target.result + ')';
              imagePreview.textContent = '';
            };
            
            reader.readAsDataURL(this.files[0]);
          } else {
            imagePreview.style.backgroundImage = 'none';
            imagePreview.textContent = 'No image selected';
          }
        });
      }
      
      // Search functionality
      const searchInput = document.querySelector('.admin-search input');
      const serviceCards = document.querySelectorAll('.service-card');
      
      if (searchInput && serviceCards.length > 0) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          
          serviceCards.forEach(card => {
            const title = card.querySelector('.service-title').textContent.toLowerCase();
            const description = card.querySelector('.service-description').textContent.toLowerCase();
            const category = card.querySelector('.service-category').textContent.toLowerCase();
            
            let tags = '';
            const tagElements = card.querySelectorAll('.service-tag');
            tagElements.forEach(tag => {
              tags += tag.textContent.toLowerCase() + ' ';
            });
            
            if (title.includes(searchTerm) || description.includes(searchTerm) || category.includes(searchTerm) || tags.includes(searchTerm)) {
              card.style.display = 'block';
            } else {
              card.style.display = 'none';
            }
          });
        });
      }
    });
  </script>
</body>
</html>
