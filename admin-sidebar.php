<!-- Sidebar Navigation -->
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <img src="Logo.png" alt="BSG Support Logo" class="admin-logo">
        <h2>Admin Panel</h2>
    </div>
    
    <div class="admin-user">
        <div class="user-avatar">
            <img src="avatar.webp" alt="<?php echo htmlspecialchars($admin_username); ?>">
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
            <li class="<?php echo is_menu_active($active_menu, 'dashboard'); ?>">
                <a href="admin-dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Content Management (Priority 2) -->
            <li class="has-submenu <?php echo is_menu_active($active_menu, 'content'); ?>">
                <a href="javascript:void(0)">
                    <i class="fas fa-edit"></i>
                    <span>Content Management</span>
                    <i class="fas fa-chevron-right submenu-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="admin-blog.php" class="<?php echo is_submenu_active($active_submenu, 'blog'); ?>"><i class="fas fa-blog"></i> Blog Management</a></li>
                    <li><a href="admin-services.php" class="<?php echo is_submenu_active($active_submenu, 'services'); ?>"><i class="fas fa-briefcase"></i> Services Editor</a></li>
                    <li><a href="admin-testimonials.php" class="<?php echo is_submenu_active($active_submenu, 'testimonials'); ?>"><i class="fas fa-star"></i> Testimonials & Logos</a></li>
                    <li><a href="admin-faq.php" class="<?php echo is_submenu_active($active_submenu, 'faq'); ?>"><i class="fas fa-question-circle"></i> FAQ Management</a></li>
                    <li><a href="admin-solutions.php" class="<?php echo is_submenu_active($active_submenu, 'solutions'); ?>"><i class="fas fa-file-alt"></i> Solutions</a></li>
                </ul>
            </li>
            
            <!-- CRM / Clients (Priority 3) -->
            <li class="has-submenu <?php echo is_menu_active($active_menu, 'crm'); ?>">
                <a href="javascript:void(0)">
                    <i class="fas fa-envelope"></i>
                    <span>CRM</span>
                    <i class="fas fa-chevron-right submenu-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="admin-enquiries.php" class="<?php echo is_submenu_active($active_submenu, 'enquiries'); ?>"><i class="fas fa-envelope-open-text"></i> Client Inquiries</a></li>
                    <li><a href="admin-subscribers.php" class="<?php echo is_submenu_active($active_submenu, 'subscribers'); ?>"><i class="fas fa-envelope-open"></i> Subscribers</a></li>
                    <li><a href="admin-clients.php" class="<?php echo is_submenu_active($active_submenu, 'clients'); ?>"><i class="fas fa-users"></i> Clients</a></li>
                </ul>
            </li>
            
            <!-- HR Tools (Priority 4) -->
            <li class="has-submenu <?php echo is_menu_active($active_menu, 'hr'); ?>">
                <a href="javascript:void(0)">
                    <i class="fas fa-user-tie"></i>
                    <span>HR Tools</span>
                    <i class="fas fa-chevron-right submenu-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="admin-candidate.php" class="<?php echo is_submenu_active($active_submenu, 'candidate'); ?>"><i class="fas fa-user-graduate"></i> Candidates</a></li>
                    <li><a href="admin-candidate-notes.php" class="<?php echo is_submenu_active($active_submenu, 'candidate-notes'); ?>"><i class="fas fa-sticky-note"></i> Candidate Notes</a></li>
                </ul>
            </li>
            
            <!-- Users & Roles (Priority 5) -->
            <li class="has-submenu <?php echo is_menu_active($active_menu, 'users'); ?>">
                <a href="javascript:void(0)">
                    <i class="fas fa-users-cog"></i>
                    <span>User Management</span>
                    <i class="fas fa-chevron-right submenu-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="admin-users.php" class="<?php echo is_submenu_active($active_submenu, 'users-all'); ?>"><i class="fas fa-user-friends"></i> All Users</a></li>
                    <li><a href="admin-roles.php" class="<?php echo is_submenu_active($active_submenu, 'roles'); ?>"><i class="fas fa-user-tag"></i> Roles & Permissions</a></li>
                    <li><a href="admin-profile.php" class="<?php echo is_submenu_active($active_submenu, 'profile'); ?>"><i class="fas fa-id-card"></i> My Profile</a></li>
                </ul>
            </li>
            
            <!-- Settings (Priority 6) -->
            <li class="has-submenu <?php echo is_menu_active($active_menu, 'settings'); ?>">
                <a href="javascript:void(0)">
                    <i class="fas fa-cogs"></i>
                    <span>Site Settings</span>
                    <i class="fas fa-chevron-right submenu-icon"></i>
                </a>
                <ul class="submenu">
                    <li><a href="admin-seo.php" class="<?php echo is_submenu_active($active_submenu, 'seo'); ?>"><i class="fas fa-search"></i> SEO Settings</a></li>
                    <li><a href="admin-general.php" class="<?php echo is_submenu_active($active_submenu, 'general'); ?>"><i class="fas fa-sliders-h"></i> General Settings</a></li>
                    <li><a href="admin-integrations.php" class="<?php echo is_submenu_active($active_submenu, 'integrations'); ?>"><i class="fas fa-plug"></i> Integrations</a></li>
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
