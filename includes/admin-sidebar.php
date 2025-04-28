<!-- Sidebar Navigation -->
<div class="admin-sidebar">
    <div class="sidebar-header">
        <img src="assets/images/logo.png" alt="Backsure Global Logo" class="sidebar-logo">
        <h3>Admin Panel</h3>
    </div>
    <div class="admin-user">
        <div class="user-avatar">
            <img src="<?php echo $admin_avatar; ?>" alt="<?php echo $admin_username; ?>">
        </div>
        <div class="user-info">
            <h3><?php echo $admin_username; ?></h3>
            <span class="user-role"><?php echo $admin_role; ?></span>
        </div>
    </div>
    <div class="sidebar-menu">
        <ul>
            <li class="<?php echo is_menu_active($active_menu, 'dashboard'); ?>">
                <a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li class="has-submenu <?php echo is_menu_active($active_menu, 'content'); ?>">
                <a href="#"><i class="fas fa-edit"></i> Content Management</a>
                <ul class="submenu">
                    <li><a href="admin-blog.php" class="<?php echo is_submenu_active($active_submenu, 'blog'); ?>"><i class="fas fa-blog"></i> Blog Management</a></li>
                    <li><a href="admin-services.php" class="<?php echo is_submenu_active($active_submenu, 'services'); ?>"><i class="fas fa-briefcase"></i> Services Editor</a></li>
                    <li><a href="admin-testimonials.php" class="<?php echo is_submenu_active($active_submenu, 'testimonials'); ?>"><i class="fas fa-star"></i> Testimonials & Logos</a></li>
                    <li><a href="admin-faq.php" class="<?php echo is_submenu_active($active_submenu, 'faq'); ?>"><i class="fas fa-question-circle"></i> FAQ Management</a></li>
                </ul>
            </li>
            <li class="has-submenu <?php echo is_menu_active($active_menu, 'crm'); ?>">
                <a href="#"><i class="fas fa-envelope"></i> CRM</a>
                <ul class="submenu">
                    <li><a href="admin-enquiries.php" class="<?php echo is_submenu_active($active_submenu, 'enquiries'); ?>"><i class="fas fa-inbox"></i> Inquiries</a></li>
                    <li><a href="admin-subscribers.php" class="<?php echo is_submenu_active($active_submenu, 'subscribers'); ?>"><i class="fas fa-envelope-open"></i> Subscribers</a></li>
                    <li><a href="admin-clients.php" class="<?php echo is_submenu_active($active_submenu, 'clients'); ?>"><i class="fas fa-building"></i> Clients</a></li>
                </ul>
            </li>
            <li class="has-submenu <?php echo is_menu_active($active_menu, 'hr'); ?>">
                <a href="#"><i class="fas fa-user-tie"></i> HR Tools</a>
                <ul class="submenu">
                    <li><a href="admin-candidate.php" class="<?php echo is_submenu_active($active_submenu, 'candidate'); ?>"><i class="fas fa-user-graduate"></i> Candidates</a></li>
                    <li><a href="admin-candidate-notes.php" class="<?php echo is_submenu_active($active_submenu, 'candidate-notes'); ?>"><i class="fas fa-sticky-note"></i> Candidate Notes</a></li>
                </ul>
            </li>
            <li class="has-submenu <?php echo is_menu_active($active_menu, 'users'); ?>">
                <a href="#"><i class="fas fa-users-cog"></i> User Management</a>
                <ul class="submenu">
                    <li><a href="admin-users.php" class="<?php echo is_submenu_active($active_submenu, 'users-all'); ?>"><i class="fas fa-users"></i> All Users</a></li>
                    <li><a href="admin-roles.php" class="<?php echo is_submenu_active($active_submenu, 'roles'); ?>"><i class="fas fa-user-tag"></i> Roles & Permissions</a></li>
                </ul>
            </li>
            <li class="has-submenu <?php echo is_menu_active($active_menu, 'settings'); ?>">
                <a href="#"><i class="fas fa-cog"></i> Settings</a>
                <ul class="submenu">
                    <li><a href="admin-seo.php" class="<?php echo is_submenu_active($active_submenu, 'seo'); ?>"><i class="fas fa-search"></i> SEO Settings</a></li>
                    <li><a href="admin-integrations.php" class="<?php echo is_submenu_active($active_submenu, 'integrations'); ?>"><i class="fas fa-plug"></i> Integrations</a></li>
                    <li><a href="admin-general.php" class="<?php echo is_submenu_active($active_submenu, 'general'); ?>"><i class="fas fa-sliders-h"></i> General Settings</a></li>
                    <li><a href="admin-appearance.php" class="<?php echo is_submenu_active($active_submenu, 'appearance'); ?>"><i class="fas fa-palette"></i> Appearance</a></li>
                    <li><a href="admin-backup.php" class="<?php echo is_submenu_active($active_submenu, 'backup'); ?>"><i class="fas fa-database"></i> Backup & Restore</a></li>
                </ul>
            </li>
            <li class="<?php echo is_menu_active($active_menu, 'profile'); ?>">
                <a href="admin-profile.php"><i class="fas fa-user-circle"></i> My Profile</a>
            </li>
            <li>
                <a href="admin-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </div>
    <div class="sidebar-footer">
        <a href="index.html" target="_blank">
            <i class="fas fa-external-link-alt"></i> View Website
        </a>
    </div>
</div>
