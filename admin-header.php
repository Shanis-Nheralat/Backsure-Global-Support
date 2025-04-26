<?php
/**
 * Admin Header Component
 * This file contains the header for the admin panel
 */

// Set page title if not already set
if (!isset($page_title)) {
    $page_title = 'Admin Panel';
}

// Set breadcrumbs if not already set
if (!isset($breadcrumbs)) {
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => 'admin-dashboard.php']
    ];
}

// Handle notifications
$notification_count = 0; // In a real application, this would come from a database
?>
<!-- Top Navigation Bar -->
<header class="admin-header">
  <div class="header-left">
    <button id="sidebar-toggle" class="sidebar-toggle">
      <i class="fas fa-bars"></i>
    </button>

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
    
    <div class="breadcrumbs">
      <?php 
      $breadcrumb_count = count($breadcrumbs);
      foreach ($breadcrumbs as $index => $breadcrumb): 
          $is_last = ($index === $breadcrumb_count - 1);
      ?>
          <?php if (!$is_last): ?>
              <a href="<?php echo htmlspecialchars($breadcrumb['url']); ?>"><?php echo htmlspecialchars($breadcrumb['title']); ?></a>
          <?php else: ?>
              <span><?php echo htmlspecialchars($breadcrumb['title']); ?></span>
          <?php endif; ?>
          
          <?php if (!$is_last): ?>
              <span class="breadcrumb-separator">/</span>
          <?php endif; ?>
      <?php endforeach; ?>
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
        <?php if ($notification_count > 0): ?>
        <span class="badge"><?php echo $notification_count; ?></span>
        <?php endif; ?>
      </button>
      
      <button class="action-btn help-btn">
        <i class="fas fa-question-circle"></i>
      </button>
    </div>
  </div>
</header>
