<?php
/**
 * Navigation sidebar using the dynamic menu
 */

// Get menu structure from configuration
require_once 'admin-menu-config.php';
?>
<!-- Sidebar Navigation -->
<aside class="admin-sidebar">
  <div class="sidebar-header">
    <img src="<?php echo $baseUrl; ?>assets/images/logo.png" alt="BSG Support Logo" class="admin-logo">
    <h2>Admin Panel</h2>
  </div>
  
  <div class="admin-user">
    <div class="user-avatar">
      <?php 
      $avatar = isset($admin_user['avatar']) && !empty($admin_user['avatar']) 
          ? $admin_user['avatar'] 
          : 'avatar.webp';
      ?>
      <img src="<?php echo $baseUrl; ?>assets/images/<?php echo $avatar; ?>" alt="<?php echo htmlspecialchars($admin_username); ?>">
    </div>
    <div class="user-info">
      <h3><?php echo htmlspecialchars($admin_username); ?></h3>
      <span class="user-role"><?php echo ucfirst(htmlspecialchars($admin_role)); ?></span>
    </div>
    <button id="user-dropdown-toggle" class="dropdown-toggle">
      <i class="fas fa-chevron-down"></i>
    </button>
    <ul id="user-dropdown" class="dropdown-menu">
      <li><a href="admin-profile.php"><i class="fas fa-user"></i> My Profile</a></li>
      <li><a href="admin-settings.php"><i class="fas fa-cog"></i> Settings</a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>
  
  <nav class="sidebar-nav">
    <ul>
      <?php 
      // Use the render_admin_menu function from admin-menu-config.php
      if (function_exists('render_admin_menu')) {
        echo render_admin_menu($admin_menu, $current_page, $admin_role);
      } 
      ?>
    </ul>
  </nav>
  
  <div class="sidebar-footer">
    <a href="<?php echo $baseUrl; ?>index.php" target="_blank">
      <i class="fas fa-external-link-alt"></i>
      <span>View Website</span>
    </a>
  </div>
</aside>

<!-- For mobile sidebar backdrop -->
<div class="sidebar-backdrop"></div>
