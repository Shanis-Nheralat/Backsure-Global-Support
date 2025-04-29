<?php
/**
 * Navigation sidebar with explicit menu structure
 */
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
      <!-- Dashboard -->
      <li class="<?php echo ($current_page === 'dashboard') ? 'active' : ''; ?>">
        <a href="admin-dashboard.php">
          <i class="fas fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>
      
      <!-- Content Management -->
      <li class="has-submenu <?php echo (in_array($current_page, ['blog', 'services', 'solutions', 'media', 'testimonials', 'faq'])) ? 'active open' : ''; ?>">
        <a href="javascript:void(0)">
          <i class="fas fa-edit"></i>
          <span>Content Management</span>
          <i class="fas fa-chevron-right submenu-icon"></i>
        </a>
        <ul class="submenu">
          <li class="<?php echo ($current_page === 'blog') ? 'active' : ''; ?>">
            <a href="admin-blog.php">
              <i class="fas fa-blog"></i> Blog Management
            </a>
          </li>
          <li class="<?php echo ($current_page === 'services') ? 'active' : ''; ?>">
            <a href="admin-services.php">
              <i class="fas fa-briefcase"></i> Services Editor
            </a>
          </li>
          <li class="<?php echo ($current_page === 'solutions') ? 'active' : ''; ?>">
            <a href="admin-solutions.php">
              <i class="fas fa-project-diagram"></i> Solutions
            </a>
          </li>
          <li class="<?php echo ($current_page === 'media') ? 'active' : ''; ?>">
            <a href="admin-media.php">
              <i class="fas fa-images"></i> Media Library
            </a>
          </li>
          <li class="<?php echo ($current_page === 'testimonials') ? 'active' : ''; ?>">
            <a href="admin-testimonials.php">
              <i class="fas fa-star"></i> Testimonials & Logos
            </a>
          </li>
          <li class="<?php echo ($current_page === 'faq') ? 'active' : ''; ?>">
            <a href="admin-faq.php">
              <i class="fas fa-question-circle"></i> FAQ Management
            </a>
          </li>
        </ul>
      </li>
      
      <!-- CRM -->
      <li class="has-submenu <?php echo (in_array($current_page, ['clients', 'subscribers', 'inquiries', 'leads'])) ? 'active open' : ''; ?>">
        <a href="javascript:void(0)">
          <i class="fas fa-users"></i>
          <span>CRM</span>
          <i class="fas fa-chevron-right submenu-icon"></i>
        </a>
        <ul class="submenu">
          <li class="<?php echo ($current_page === 'clients') ? 'active' : ''; ?>">
            <a href="admin-clients.php">
              <i class="fas fa-user-tie"></i> Clients
            </a>
          </li>
          <li class="<?php echo ($current_page === 'subscribers') ? 'active' : ''; ?>">
            <a href="admin-subscribers.php">
              <i class="fas fa-envelope-open-text"></i> Subscribers
            </a>
          </li>
          <li class="<?php echo ($current_page === 'inquiries') ? 'active' : ''; ?>">
            <a href="admin-inquiries.php">
              <i class="fas fa-envelope"></i> Client Inquiries
              <?php if (isset($unread_inquiries) && $unread_inquiries > 0): ?>
                <span class="badge bg-danger"><?php echo $unread_inquiries; ?></span>
              <?php endif; ?>
            </a>
          </li>
          <li class="<?php echo ($current_page === 'leads') ? 'active' : ''; ?>">
            <a href="admin-leads.php">
              <i class="fas fa-funnel-dollar"></i> Lead Management
            </a>
          </li>
        </ul>
      </li>
      
      <!-- User Management -->
      <li class="has-submenu <?php echo (in_array($current_page, ['all_users', 'roles'])) ? 'active open' : ''; ?>">
        <a href="javascript:void(0)">
          <i class="fas fa-user-shield"></i>
          <span>User Management</span>
          <i class="fas fa-chevron-right submenu-icon"></i>
        </a>
        <ul class="submenu">
          <li class="<?php echo ($current_page === 'all_users') ? 'active' : ''; ?>">
            <a href="admin-users.php">
              <i class="fas fa-user-friends"></i> All Users
            </a>
          </li>
          <li class="<?php echo ($current_page === 'roles') ? 'active' : ''; ?>">
            <a href="admin-roles.php">
              <i class="fas fa-user-tag"></i> Roles & Permissions
            </a>
          </li>
        </ul>
      </li>
      
      <!-- Site Settings -->
      <li class="has-submenu <?php echo (in_array($current_page, ['general', 'appearance', 'seo', 'integrations', 'backup'])) ? 'active open' : ''; ?>">
        <a href="javascript:void(0)">
          <i class="fas fa-cogs"></i>
          <span>Site Settings</span>
          <i class="fas fa-chevron-right submenu-icon"></i>
        </a>
        <ul class="submenu">
          <li class="<?php echo ($current_page === 'general') ? 'active' : ''; ?>">
            <a href="admin-settings.php">
              <i class="fas fa-sliders-h"></i> General Settings
            </a>
          </li>
          <li class="<?php echo ($current_page === 'appearance') ? 'active' : ''; ?>">
            <a href="admin-appearance.php">
              <i class="fas fa-palette"></i> Appearance
            </a>
          </li>
          <li class="<?php echo ($current_page === 'seo') ? 'active' : ''; ?>">
            <a href="admin-seo.php">
              <i class="fas fa-search"></i> SEO Settings
            </a>
          </li>
          <li class="<?php echo ($current_page === 'integrations') ? 'active' : ''; ?>">
            <a href="admin-integrations.php">
              <i class="fas fa-plug"></i> Integrations
            </a>
          </li>
          <li class="<?php echo ($current_page === 'backup') ? 'active' : ''; ?>">
            <a href="admin-backup.php">
              <i class="fas fa-database"></i> Backup & Restore
            </a>
          </li>
        </ul>
      </li>
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

<!-- DO NOT include duplicate JavaScript here - it will conflict with the main js file -->
