<!-- Admin Header -->
<header class="admin-header">
  <div class="header-left">
    <button id="sidebar-toggle" class="sidebar-toggle">
      <i class="fas fa-bars"></i>
    </button>
    
    <div class="breadcrumbs">
      <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
          <?php if ($index > 0): ?> <span class="breadcrumb-separator">/</span> <?php endif; ?>
          <a href="<?php echo $crumb['url']; ?>"><?php echo htmlspecialchars($crumb['title']); ?></a>
        <?php endforeach; ?>
      <?php else: ?>
        <a href="admin-dashboard.php">Dashboard</a>
      <?php endif; ?>
    </div>
  </div>
  
  <div class="header-right">
    <div class="admin-search">
      <input type="text" placeholder="Search...">
      <button type="submit"><i class="fas fa-search"></i></button>
    </div>
    
    <div class="header-actions">
      <div class="action-btn">
        <i class="fas fa-bell"></i>
        <span class="badge">3</span>
        <div class="dropdown-menu">
          <!-- Notification items would go here -->
        </div>
      </div>
      
      <div class="action-btn">
        <i class="fas fa-envelope"></i>
        <span class="badge">7</span>
        <div class="dropdown-menu">
          <!-- Message items would go here -->
        </div>
      </div>
      
      <div class="theme-settings">
        <select id="theme-switcher" class="form-control form-control-sm">
          <option value="default">Default Theme</option>
          <option value="dark">Dark Theme</option>
          <option value="blue">Blue Theme</option>
          <option value="green">Green Theme</option>
          <option value="purple">Purple Theme</option>
          <option value="high-contrast">High Contrast</option>
        </select>
        
        <div class="custom-control custom-switch ml-3">
          <input type="checkbox" class="custom-control-input" id="auto-dark-mode">
          <label class="custom-control-label" for="auto-dark-mode">Auto Dark Mode</label>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- Display notification messages -->
<?php echo display_admin_messages(); ?>
