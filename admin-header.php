<?php
/**
 * Admin Header
 * 
 * Header template for all admin pages.
 * Requires admin-auth.php to be included before this file.
 */

// Ensure auth check has been performed
if (!isset($current_admin)) {
    die('Error: Authentication check not performed. Please include admin-auth.php before this file.');
}

// Get current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Admin Dashboard'; ?> | BSG Support</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #062767;
            --primary-dark: #051d4d;
            --accent-color: #B19763;
            --light-bg: #f8f9fa;
            --border-color: #e9ecef;
            --text-color: #343a40;
            --text-muted: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Open Sans', Arial, sans-serif;
            color: var(--text-color);
            background-color: var(--light-bg);
            line-height: 1.6;
        }
        
        a {
            text-decoration: none;
            color: var(--primary-color);
        }
        
        /* Layout */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: var(--primary-color);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        
        .sidebar-collapsed {
            width: 60px;
        }
        
        .content-wrapper {
            flex: 1;
            margin-left: 250px;
            transition: all 0.3s ease;
        }
        
        .content-wrapper-expanded {
            margin-left: 60px;
        }
        
        .topbar {
            background-color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .main-content {
            padding: 20px;
        }
        
        /* Sidebar Styles */
        .sidebar-header {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .logo {
            color: white;
            font-size: 1.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo-icon {
            font-size: 1.5rem;
        }
        
        .toggle-sidebar {
            cursor: pointer;
            font-size: 1.2rem;
            color: rgba(255,255,255,0.7);
            transition: color 0.3s;
        }
        
        .toggle-sidebar:hover {
            color: white;
        }
        
        .nav-menu {
            list-style: none;
            padding: 20px 0;
        }
        
        .nav-item {
            margin-bottom: 5px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: rgba(255,255,255,0.7);
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        
        .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--accent-color);
        }
        
        .nav-icon {
            margin-right: 15px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        
        .nav-text {
            flex: 1;
            transition: opacity 0.3s;
        }
        
        .sidebar-collapsed .nav-text,
        .sidebar-collapsed .logo-text {
            display: none;
            opacity: 0;
        }
        
        .sidebar-collapsed .nav-link {
            padding: 10px;
            justify-content: center;
        }
        
        .sidebar-collapsed .nav-icon {
            margin-right: 0;
            font-size: 1.2rem;
        }
        
        .sidebar-footer {
            padding: 15px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        
        .sidebar-footer a {
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
            display: block;
            margin-bottom: 5px;
            transition: color 0.3s;
        }
        
        .sidebar-footer a:hover {
            color: white;
        }
        
        .sidebar-collapsed .sidebar-footer {
            text-align: center;
            padding: 15px 0;
        }
        
        .sidebar-collapsed .sidebar-footer a span {
            display: none;
        }
        
        /* Topbar Styles */
        .left-section {
            display: flex;
            align-items: center;
        }
        
        .page-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .right-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .admin-dropdown {
            position: relative;
        }
        
        .admin-dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        
        .admin-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .admin-name {
            font-weight: 600;
        }
        
        .admin-role {
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        
        .admin-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 5px;
            min-width: 180px;
            margin-top: 10px;
            z-index: 1000;
            display: none;
        }
        
        .admin-dropdown-menu.show {
            display: block;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            color: var(--text-color);
            transition: background-color 0.3s;
        }
        
        .dropdown-item:hover {
            background-color: var(--light-bg);
        }
        
        .dropdown-divider {
            height: 1px;
            background-color: var(--border-color);
            margin: 5px 0;
        }
        
        /* Breadcrumbs */
        .breadcrumbs {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 5px;
        }
        
        .breadcrumb-item {
            display: flex;
            align-items: center;
        }
        
        .breadcrumb-item:not(:last-child)::after {
            content: '/';
            margin: 0 8px;
            color: var(--text-muted);
        }
        
        .breadcrumb-item a {
            color: var(--text-muted);
            transition: color 0.3s;
        }
        
        .breadcrumb-item a:hover {
            color: var(--primary-color);
        }
        
        .breadcrumb-item.active {
            color: var(--text-color);
            font-weight: 600;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }
            
            .content-wrapper {
                margin-left: 60px;
            }
            
            .sidebar .nav-text,
            .sidebar .logo-text {
                display: none;
            }
            
            .sidebar .nav-link {
                padding: 10px;
                justify-content: center;
            }
            
            .sidebar .nav-icon {
                margin-right: 0;
            }
            
            .sidebar-expanded {
                width: 250px;
            }
            
            .sidebar-expanded .nav-text,
            .sidebar-expanded .logo-text {
                display: block;
            }
            
            .sidebar-expanded .nav-link {
                padding: 10px 20px;
                justify-content: flex-start;
            }
            
            .sidebar-expanded .nav-icon {
                margin-right: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="admin-dashboard.php" class="logo">
                    <i class="fas fa-shield-alt logo-icon"></i>
                    <span class="logo-text">BSG Admin</span>
                </a>
                <div class="toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="admin-dashboard.php" class="nav-link <?php echo $current_page === 'admin-dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-candidates.php" class="nav-link <?php echo $current_page === 'admin-candidates.php' ? 'active' : ''; ?>">
                        <i class="fas fa-user-tie nav-icon"></i>
                        <span class="nav-text">Candidates</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-inquiries.php" class="nav-link <?php echo $current_page === 'admin-inquiries.php' ? 'active' : ''; ?>">
                        <i class="fas fa-envelope nav-icon"></i>
                        <span class="nav-text">Inquiries</span>
                    </a>
                </li>
                <?php if ($_SESSION['admin_role'] === 'admin'): ?>
                <li class="nav-item">
                    <a href="admin-users.php" class="nav-link <?php echo $current_page === 'admin-users.php' ? 'active' : ''; ?>">
                        <i class="fas fa-users-cog nav-icon"></i>
                        <span class="nav-text">Admin Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-settings.php" class="nav-link <?php echo $current_page === 'admin-settings.php' ? 'active' : ''; ?>">
                        <i class="fas fa-cog nav-icon"></i>
                        <span class="nav-text">Settings</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            
            <div class="sidebar-footer">
                <a href="admin-logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sign Out</span>
                </a>
                <a href="../index.html" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <span>View Website</span>
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="content-wrapper">
            <header class="topbar">
                <div class="left-section">
                    <h1 class="page-title"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1>
                    <?php if (isset($breadcrumbs)): ?>
                    <div class="breadcrumbs">
                        <div class="breadcrumb-item">
                            <a href="admin-dashboard.php">Dashboard</a>
                        </div>
                        <?php foreach ($breadcrumbs as $item): ?>
                        <div class="breadcrumb-item <?php echo isset($item['active']) && $item['active'] ? 'active' : ''; ?>">
                            <?php if (isset($item['link']) && !isset($item['active'])): ?>
                            <a href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a>
                            <?php else: ?>
                            <?php echo $item['title']; ?>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="right-section">
                    <div class="admin-dropdown">
                        <div class="admin-dropdown-toggle">
                            <div class="admin-avatar">
                                <?php echo strtoupper(substr($current_admin['username'], 0, 1)); ?>
                            </div>
                            <div>
                                <div class="admin-name"><?php echo htmlspecialchars($current_admin['username']); ?></div>
                                <div class="admin-role"><?php echo ucfirst(htmlspecialchars($current_admin['role'])); ?></div>
                            </div>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="admin-dropdown-menu">
                            <a href="admin-profile.php" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                My Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="admin-logout.php" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i>
                                Sign Out
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            
            <main class="main-content">
                <?php if ($admin_message): ?>
                <div class="alert alert-<?php echo $admin_message['type']; ?>">
                    <?php echo htmlspecialchars($admin_message['text']); ?>
                </div>
                <?php endif; ?>
                
                <!-- Page Content Starts Here -->