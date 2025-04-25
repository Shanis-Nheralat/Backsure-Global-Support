<?php
/**
 * Admin Header Component
 * This file contains the header for the admin panel
 * It should be included in all admin pages
 */

// Set default values for notification and task counts
if (!isset($notification_count)) {
    $notification_count = 5;
}

if (!isset($task_count)) {
    $task_count = 2;
}

// Default page title if not set
if (!isset($page_title) || empty($page_title)) {
    $page_title = '';
}
?>
<!-- Top Navigation Bar -->
<header class="admin-header">
  <div class="header-left">
    <button id="sidebar-toggle" class="sidebar-toggle">
      <i class="fas fa-bars"></i>
    </button>
    <div class="breadcrumbs">
      <a href="admin-dashboard.php">Dashboard</a>
      <?php if(!empty($page_title) && $current_page != 'dashboard'): ?>
        <span> &gt; </span>
        <span><?php echo htmlspecialchars($page_title); ?></span>
      <?php endif; ?>
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
        <span class="badge"><?php echo intval($notification_count); ?></span>
      </button>
      
      <button class="action-btn task-btn">
        <i class="fas fa-check-circle"></i>
        <span class="badge"><?php echo intval($task_count); ?></span>
      </button>
      
      <button class="action-btn help-btn">
        <i class="fas fa-question-circle"></i>
      </button>
    </div>
  </div>
</header>
