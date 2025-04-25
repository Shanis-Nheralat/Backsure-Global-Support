<?php
/**
 * Admin Sidebar Component
 * This file contains the sidebar navigation for the admin panel
 * It should be included in all admin pages
 */
// If current_page is not set, default to empty
if (!isset($current_page)) {
    $current_page = '';
}
?>
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
      <li><a href="admin-settings.php"><i class="fas fa-cog"></i> Settings</a></li>
      <li><a href="admin-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>
  
  <nav class="sidebar-nav">
    <ul>
      <!-- Dashboard (Priority 1) -->
      <li<?php echo ($current_page == 'dashboard') ? ' class="active"' : ''; ?>>
        <a href="admin-dashboard.php">
          <i class="fas fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>
      
      <!-- Content Management (Priority 2) -->
      <li class="has-submenu<?php echo in_array($current_page, ['blog', 'services', 'testimonials', 'faq', 'solutions']) ? ' open' : ''; ?>">
        <a href="javascript:void(0)">
          <i class="fas fa-edit"></i>
          <span>Content Management</span>
          <i class="fas fa-chevron-right submenu-icon"></i>
        </a>
        <ul class="submenu" <?php echo in_array($current_page, ['blog', 'services', 'testimonials', 'faq', 'solutions']) ? ' style="max-height: 1000px;"' : ''; ?>>
          <li<?php echo ($current_page == 'blog') ? ' class="active"' : ''; ?>><a href="admin-blog.php"><i class="fas fa-blog"></i> Blog Management</a></li>
          <li<?php echo ($current_page == 'services') ? ' class="active"' : ''; ?>><a href="admin-services.php"><i class="fas fa-briefcase"></i> Services Editor</a></li>
          <li<?php echo ($current_page == 'testimonials') ? ' class="active"' : ''; ?>><a href="admin-testimonials.php"><i class="fas fa-star"></i> Testimonials & Logos</a></li>
          <li<?php echo ($current_page == 'faq') ? ' class="active"' : ''; ?>><a href="admin-faq.php"><i class="fas fa-question-circle"></i> FAQ Management</a></li>
          <li<?php echo ($current_page == 'solutions') ? ' class="active"' : ''; ?>><a href="admin-solutions.php"><i class="fas fa-file-alt"></i> Solutions</a></li>
        </ul>
      </li>
      
      <!-- CRM / Clients (Priority 3) -->
      <li class="has-submenu<?php echo in_array($current_page, ['inquiries', 'subscribers', 'clients']) ? ' open' : ''; ?>">
        <a href="javascript:void(0)">
          <i class="fas fa-envelope"></i>
          <span>CRM</span>
          <i class="fas fa-chevron-right submenu-icon"></i>
        </a>
        <ul class="submenu" <?php echo in_array($current_page, ['inquiries', 'subscribers', 'clients']) ? ' style="max-height: 1000px;"' : ''; ?>>
          <li<?php echo ($current_page == 'inquiries') ? ' class="active"' : ''; ?>><a href="admin-inquiries.php"><i class="fas fa-envelope-open-text"></i> Client Inquiries</a></li>
          <li<?php echo ($current_page == 'subscribers') ? ' class="active"' : ''; ?>><a href="admin-subscribers.php"><i class="fas fa-envelope-open"></i> Subscribers</a></li>
          <li<?php echo ($current_page == 'clients') ? ' class="active"' : ''; ?>><a href="admin-clients.php"><i class="fas fa-users"></i> Clients</a></li>
        </ul>
      </li>
      
      <!-- HR Tools (Priority 4) -->
      <li class="has-submenu<?php echo in_array($current_page, ['candidates', 'candidate-notes']) ? ' open' : ''; ?>">
        <a href="javascript:void(0)">
          <i class="fas fa-user-tie"></i>
          <span>HR Tools</span>
          <i class="fas fa-chevron-right submenu-icon"></i>
        </a>
        <ul class="submenu" <?php echo in_array($current_page, ['candidates', 'candidate-notes']) ? ' style="max-height: 1000px;"' : ''; ?>>
          <li<?php echo ($current_page == 'candidates') ? ' class="active"' : ''; ?>><a href="admin-candidates.php"><i class="fas fa-user-graduate"></i> Candidates</a></li>
          <li<?php echo ($current_page == 'candidate-notes') ? ' class="active"' : ''; ?>><a href="admin-candidate-notes.php"><i class="fas fa-sticky-note"></i> Candidate Notes</a></li>
        </ul>
      </li>
      
      <!-- Users & Roles (Priority 5) -->
      <li class="has-submenu<?php echo in_array($current_page, ['users', 'roles', 'profile']) ? ' open' : ''; ?>">
        <a href="javascript:void(0)">
          <i class="fas fa-users-cog"></i>
          <span>User Management</span>
          <i class="fas fa-chevron-right submenu-icon"></i>
        </a>
        <ul class="submenu" <?php echo in_array($current_page, ['users', 'roles', 'profile']) ? ' style="max-height: 1000px;"' : ''; ?>>
          <li<?php echo ($current_page == 'users') ? ' class="active"' : ''; ?>><a href="admin-users.php"><i class="fas fa-user-friends"></i> All Users</a></li>
          <li<?php echo ($current_page == 'roles') ? ' class="active"' : ''; ?>><a href="admin-roles.php"><i class="fas fa-user-tag"></i> Roles & Permissions</a></li>
          <li<?php echo ($current_page == 'profile') ? ' class="active"' : ''; ?>><a href="admin-profile.php"><i class="fas fa-id-card"></i> My Profile</a></li>
        </ul>
      </li>
      
      <!-- Settings (Priority 6) -->
      <li class="has-submenu<?php echo in_array($current_page, ['seo', 'settings', 'integrations']) ? ' open' : ''; ?>">
        <a href="javascript:void(0)">
          <i class="fas fa-cogs"></i>
          <span>Site Settings</span>
          <i class="fas fa-chevron-right submenu-icon"></i>
        </a>
        <ul class="submenu" <?php echo in_array($current_page, ['seo', 'settings', 'integrations']) ? ' style="max-height: 1000px;"' : ''; ?>>
          <li<?php echo ($current_page == 'seo') ? ' class="active"' : ''; ?>><a href="admin-seo.php"><i class="fas fa-search"></i> SEO Settings</a></li>
          <li<?php echo ($current_page == 'settings') ? ' class="active"' : ''; ?>><a href="admin-settings.php"><i class="fas fa-sliders-h"></i> General Settings</a></li>
          <li<?php echo ($current_page == 'integrations') ? ' class="active"' : ''; ?>><a href="admin-integrations.php"><i class="fas fa-plug"></i> Integrations</a></li>
        </ul>
      </li>
    </ul>
  </nav>
  
  <div class="sidebar-footer">
    <a href="index.php" target="_blank">
      <i class="fas fa-external-link-alt"></i>
      <span>View Website</span>
    </a>
  </div>
</aside>
