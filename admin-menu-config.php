<?php
/**
 * Dynamic Menu Configuration System
 * 
 * This file provides functionality for dynamically generating the admin menu
 * based on user roles and permissions.
 * 
 * File: admin-menu-config.php
 */

// Menu structure with role permissions
$admin_menu = [
    [
        'id' => 'dashboard',
        'title' => 'Dashboard',
        'url' => 'admin-dashboard.php',
        'icon' => 'dashboard', // Icons could be class names or image paths
        'roles' => ['admin', 'editor', 'author', 'contributor'], // All roles can access
        'order' => 1, // Lower numbers appear first
        'badge' => '', // Optional badge (can be populated dynamically)
        'children' => [] // No sub-items
    ],
    [
        'id' => 'users',
        'title' => 'User Management',
        'url' => 'admin-users.php',
        'icon' => 'users',
        'roles' => ['admin', 'superadmin'], // Only admin and superadmin can access
        'order' => 2,
        'badge' => '',
        'children' => []
    ],
    [
        'id' => 'content',
        'title' => 'Content',
        'url' => '#', // # means it's a parent menu with submenu items
        'icon' => 'file-text',
        'roles' => ['admin', 'editor', 'author'],
        'order' => 3,
        'badge' => '',
        'children' => [
            [
                'id' => 'services',
                'title' => 'Services Editor',
                'url' => 'admin-services.php',
                'roles' => ['admin', 'editor'],
                'order' => 1,
                'badge' => ''
            ],
            [
                'id' => 'blog',
                'title' => 'Blog Management',
                'url' => 'admin-blog.php',
                'roles' => ['admin', 'editor', 'author'],
                'order' => 2,
                'badge' => ''
            ]
        ]
    ],
    [
        'id' => 'communication',
        'title' => 'Communication',
        'url' => '#',
        'icon' => 'message-circle',
        'roles' => ['admin', 'editor'],
        'order' => 4,
        'badge' => '',
        'children' => [
            [
                'id' => 'inquiries',
                'title' => 'Inquiries',
                'url' => 'admin-inquiries.php',
                'roles' => ['admin', 'editor'],
                'order' => 1,
                'badge' => 'get_unread_inquiries_count' // Function that returns the badge number
            ],
            [
                'id' => 'subscribers',
                'title' => 'Subscribers',
                'url' => 'admin-subscribers.php',
                'roles' => ['admin', 'editor'],
                'order' => 2,
                'badge' => ''
            ]
        ]
    ],
    [
        'id' => 'settings',
        'title' => 'Settings',
        'url' => 'admin-settings.php',
        'icon' => 'settings',
        'roles' => ['admin', 'superadmin'],
        'order' => 5,
        'badge' => '',
        'children' => []
    ]
];

/**
 * Get menu items filtered by user role
 *
 * @param string $role The role to filter by
 * @return array Filtered menu items
 */
function get_menu_by_role($role) {
    global $admin_menu;
    
    $filtered_menu = [];
    
    foreach ($admin_menu as $item) {
        // Check if user has permission for this menu item
        if (in_array($role, $item['roles'])) {
            $menu_item = $item;
            
            // Filter children by role as well
            if (!empty($item['children'])) {
                $filtered_children = [];
                
                foreach ($item['children'] as $child) {
                    if (in_array($role, $child['roles'])) {
                        // Process badge if it's a function name
                        if (!empty($child['badge']) && function_exists($child['badge'])) {
                            $badge_function = $child['badge'];
                            $child['badge'] = $badge_function();
                        }
                        
                        $filtered_children[] = $child;
                    }
                }
                
                // Sort children by order
                usort($filtered_children, function($a, $b) {
                    return $a['order'] <=> $b['order'];
                });
                
                $menu_item['children'] = $filtered_children;
            }
            
            // Process badge if it's a function name
            if (!empty($menu_item['badge']) && function_exists($menu_item['badge'])) {
                $badge_function = $menu_item['badge'];
                $menu_item['badge'] = $badge_function();
            }
            
            $filtered_menu[] = $menu_item;
        }
    }
    
    // Sort by order
    usort($filtered_menu, function($a, $b) {
        return $a['order'] <=> $b['order'];
    });
    
    return $filtered_menu;
}

/**
 * Check if a menu item should be marked as active
 *
 * @param array $item Menu item
 * @param string $current_page Current page identifier
 * @return bool True if active
 */
function is_menu_item_active($item, $current_page) {
    // Direct match
    if ($item['id'] === $current_page) {
        return true;
    }
    
    // Check children for matches
    if (!empty($item['children'])) {
        foreach ($item['children'] as $child) {
            if ($child['id'] === $current_page) {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * Render the menu HTML
 *
 * @param string $role User role
 * @param string $current_page Current page identifier
 * @return string HTML for the menu
 */
function render_admin_menu($role, $current_page) {
    $menu_items = get_menu_by_role($role);
    $html = '<ul class="admin-sidebar-nav">';
    
    foreach ($menu_items as $item) {
        $is_active = is_menu_item_active($item, $current_page);
        $has_children = !empty($item['children']);
        
        $html .= '<li class="' . ($is_active ? 'active' : '') . ($has_children ? ' has-children' : '') . '">';
        
        // Item link
        $html .= '<a href="' . $item['url'] . '" class="nav-link">';
        
        // Icon
        $html .= '<span class="nav-icon"><i class="icon-' . $item['icon'] . '"></i></span>';
        
        // Title
        $html .= '<span class="nav-title">' . $item['title'] . '</span>';
        
        // Badge
        if (!empty($item['badge'])) {
            $html .= '<span class="nav-badge">' . $item['badge'] . '</span>';
        }
        
        // Dropdown arrow for items with children
        if ($has_children) {
            $html .= '<span class="nav-arrow"></span>';
        }
        
        $html .= '</a>';
        
        // Submenu
        if ($has_children) {
            $html .= '<ul class="submenu' . ($is_active ? ' submenu-open' : '') . '">';
            
            foreach ($item['children'] as $child) {
                $child_is_active = ($child['id'] === $current_page);
                
                $html .= '<li class="' . ($child_is_active ? 'active' : '') . '">';
                $html .= '<a href="' . $child['url'] . '">';
                $html .= '<span class="nav-title">' . $child['title'] . '</span>';
                
                // Badge for child item
                if (!empty($child['badge'])) {
                    $html .= '<span class="nav-badge">' . $child['badge'] . '</span>';
                }
                
                $html .= '</a></li>';
            }
            
            $html .= '</ul>';
        }
        
        $html .= '</li>';
    }
    
    $html .= '</ul>';
    return $html;
}

/**
 * Example function to get unread inquiries count
 * This would typically query your database
 *
 * @return int|string Count of unread inquiries
 */
function get_unread_inquiries_count() {
    // This would be a database query in practice
    // Example: SELECT COUNT(*) FROM inquiries WHERE read = 0
    
    $count = 5; // Hardcoded for example, replace with actual query
    
    if ($count > 0) {
        return (string)$count;
    }
    
    return '';
}

/**
 * Usage in admin-sidebar.php:
 * 
 * <?php
 * // Include the menu configuration
 * require_once 'admin-menu-config.php';
 * 
 * // Get admin role from session or authentication system
 * $admin_role = $admin_user['role'];
 * 
 * // Render the menu
 * echo render_admin_menu($admin_role, $current_page);
 * ?>
 */
