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
      <!-- Dashboard -->
      <li <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php') ? 'class="active"' : ''; ?>>
        <a href="admin-dashboard.php">
          <i class="fas fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>
      
      <!-- User & Role Management -->
      <li class="has-submenu <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin-users.php', 'admin-roles.php', 'admin-profile.php'])) ? 'open' : ''; ?>">
        <a href="javascript:void(0)">
          <i class="fas fa-users"></i>
          <span>User Management</span>
          <i class="fas fa-chevron-right submenu-icon"></i>
        </a>
        <ul class="submenu">
          <li><a href="admin-users.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-users.php') ? 'class="active"' : ''; ?>><i class="fas fa-user-friends"></i> All Users</a></li>
          <li><a href="admin-roles.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-roles.php') ? 'class="active"' : ''; ?>><i class="fas fa-user-tag"></i> Roles & Permissions</a></li>
          <li><a href="admin-profile.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-profile.php') ? 'class="active"' : ''; ?>><i class="fas fa-id-card"></i> My Profile</a></li>
        </ul>
      </li>
      
      <!-- Content Management -->
      <li class="has-submenu <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin-blog.php', 'admin-services.php', 'admin-testimonials.php', 'admin-faq.php'])) ? 'open' : ''; ?>">
        <a href="javascript:void(0)">
          <i class="fas fa-edit"></i>
          <span>Content Management</span>
          <i class="fas fa-chevron-right submenu-icon"></i>
        </a>
        <ul class="submenu">
          <li><a href="admin-blog.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-blog.php') ? 'class="active"' : ''; ?>><i class="fas fa-blog"></i> Blog Management</a></li>
          <li><a href="admin-services.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-services.php') ? 'class="active"' : ''; ?>><i class="fas fa-briefcase"></i> Services Editor</a></li>
          <li><a href="admin-testimonials.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-testimonials.php') ? 'class="active"' : ''; ?>><i class="fas fa-star"></i> Testimonials & Logos</a></li>
          <li><a href="admin-faq.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-faq.php') ? 'class="active"' : ''; ?>><i class="fas fa-question-circle"></i> FAQ Management</a></li>
        </ul>
      </li>
      
      <!-- Client & CRM -->
      <li class="has-submenu <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin-enquiries.php', 'admin-subscribers.php', 'admin-clients.php'])) ? 'open' : ''; ?>">
        <a href="javascript:void(0)">
          <i class="fas fa-envelope"></i>
          <span>Client & CRM</span>
          <i class="fas fa-chevron-right submenu-icon"></i>
        </a>
        <ul class="submenu">
          <li><a href="admin-enquiries.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-enquiries.php') ? 'class="active"' : ''; ?>><i class="fas fa-inbox"></i> Inquiries</a></li>
          <li><a href="admin-subscribers.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-subscribers.php') ? 'class="active"' : ''; ?>><i class="fas fa-envelope-open-text"></i> Subscribers</a></li>
          <li><a href="admin-clients.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-clients.php') ? 'class="active"' : ''; ?>><i class="fas fa-building"></i> Clients</a></li>
        </ul>
      </li>
      
      <!-- HR/Admin Tools -->
      <li class="has-submenu <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin-candidate.php', 'admin-candidate-notes.php'])) ? 'open' : ''; ?>">
        <a href="javascript:void(0)">
          <i class="fas fa-user-tie"></i>
          <span>HR & Recruitment</span>
          <i class="fas fa-chevron-right submenu-icon"></i>
        </a>
        <ul class="submenu">
          <li><a href="admin-candidate.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-candidate.php') ? 'class="active"' : ''; ?>><i class="fas fa-user-plus"></i> Candidates</a></li>
          <li><a href="admin-candidate-notes.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-candidate-notes.php') ? 'class="active"' : ''; ?>><i class="fas fa-sticky-note"></i> Candidate Notes</a></li>
        </ul>
      </li>
      
      <!-- Admin Settings -->
      <li class="has-submenu <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin-seo.php', 'admin-general.php', 'admin-integrations.php'])) ? 'open' : ''; ?>">
        <a href="javascript:void(0)">
          <i class="fas fa-cogs"></i>
          <span>Settings</span>
          <i class="fas fa-chevron-right submenu-icon"></i>
        </a>
        <ul class="submenu">
          <li><a href="admin-seo.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-seo.php') ? 'class="active"' : ''; ?>><i class="fas fa-search"></i> SEO Settings</a></li>
          <li><a href="admin-general.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-general.php') ? 'class="active"' : ''; ?>><i class="fas fa-sliders-h"></i> General Settings</a></li>
          <li><a href="admin-integrations.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'admin-integrations.php') ? 'class="active"' : ''; ?>><i class="fas fa-plug"></i> Integrations</a></li>
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