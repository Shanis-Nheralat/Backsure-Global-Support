<?php
/**
 * Dynamic menu configuration
 * Defines the structure of the admin navigation menu
 */

// Initialize menu array if not already defined
if (!isset($admin_menu)) {
    $admin_menu = [];
}

// Dashboard
$admin_menu[] = [
    'id' => 'dashboard',
    'title' => 'Dashboard',
    'url' => 'admin-dashboard.php',
    'icon' => 'tachometer-alt',
    'roles' => ['admin', 'editor', 'author', 'user'],
    'order' => 1,
    'badge' => '',
    'children' => []
];

// Content Management
$admin_menu[] = [
    'id' => 'content',
    'title' => 'Content Management',
    'url' => 'javascript:void(0)',
    'icon' => 'edit',
    'roles' => ['admin', 'editor', 'author'],
    'order' => 2,
    'badge' => '',
    'children' => [
        [
            'id' => 'blog',
            'title' => 'Blog Management',
            'url' => 'admin-blog.php',
            'icon' => 'blog',
            'roles' => ['admin', 'editor', 'author'],
            'order' => 1,
            'badge' => ''
        ],
        [
            'id' => 'services',
            'title' => 'Services Editor',
            'url' => 'admin-services.php',
            'icon' => 'briefcase',
            'roles' => ['admin', 'editor'],
            'order' => 2,
            'badge' => ''
        ],
        [
            'id' => 'solutions',
            'title' => 'Solutions',
            'url' => 'admin-solutions.php',
            'icon' => 'project-diagram',
            'roles' => ['admin', 'editor'],
            'order' => 3,
            'badge' => ''
        ],
        [
            'id' => 'media',
            'title' => 'Media Library',
            'url' => 'admin-media.php',
            'icon' => 'images',
            'roles' => ['admin', 'editor', 'author'],
            'order' => 4,
            'badge' => ''
        ],
        [
            'id' => 'testimonials',
            'title' => 'Testimonials & Logos',
            'url' => 'admin-testimonials.php',
            'icon' => 'star',
            'roles' => ['admin', 'editor'],
            'order' => 5,
            'badge' => ''
        ],
        [
            'id' => 'faq',
            'title' => 'FAQ Management',
            'url' => 'admin-faq.php',
            'icon' => 'question-circle',
            'roles' => ['admin', 'editor'],
            'order' => 6,
            'badge' => ''
        ]
    ]
];

// CRM
$admin_menu[] = [
    'id' => 'crm',
    'title' => 'CRM',
    'url' => 'javascript:void(0)',
    'icon' => 'users',
    'roles' => ['admin', 'editor'],
    'order' => 3,
    'badge' => '',
    'children' => [
        [
            'id' => 'clients',
            'title' => 'Clients',
            'url' => 'admin-clients.php',
            'icon' => 'user-tie',
            'roles' => ['admin', 'editor'],
            'order' => 1,
            'badge' => ''
        ],
        [
            'id' => 'subscribers',
            'title' => 'Subscribers',
            'url' => 'admin-subscribers.php',
            'icon' => 'envelope-open-text',
            'roles' => ['admin', 'editor'],
            'order' => 2,
            'badge' => ''
        ],
        [
            'id' => 'inquiries',
            'title' => 'Client Inquiries',
            'url' => 'admin-inquiries.php',
            'icon' => 'envelope',
            'roles' => ['admin', 'editor'],
            'order' => 3,
            'badge' => '3'
        ],
        [
            'id' => 'leads',
            'title' => 'Lead Management',
            'url' => 'admin-leads.php',
            'icon' => 'funnel-dollar',
            'roles' => ['admin', 'editor'],
            'order' => 4,
            'badge' => ''
        ]
    ]
];

// User Management
$admin_menu[] = [
    'id' => 'users',
    'title' => 'User Management',
    'url' => 'javascript:void(0)',
    'icon' => 'user-shield',
    'roles' => ['admin'],
    'order' => 4,
    'badge' => '',
    'children' => [
        [
            'id' => 'all_users',
            'title' => 'All Users',
            'url' => 'admin-users.php',
            'icon' => 'user-friends',
            'roles' => ['admin'],
            'order' => 1,
            'badge' => ''
        ],
        [
            'id' => 'roles',
            'title' => 'Roles & Permissions',
            'url' => 'admin-roles.php',
            'icon' => 'user-tag',
            'roles' => ['admin'],
            'order' => 2,
            'badge' => ''
        ]
    ]
];

// Site Settings
$admin_menu[] = [
    'id' => 'settings',
    'title' => 'Site Settings',
    'url' => 'javascript:void(0)',
    'icon' => 'cogs',
    'roles' => ['admin'],
    'order' => 5,
    'badge' => '',
    'children' => [
        [
            'id' => 'general',
            'title' => 'General Settings',
            'url' => 'admin-settings.php',
            'icon' => 'sliders-h',
            'roles' => ['admin'],
            'order' => 1,
            'badge' => ''
        ],
        [
            'id' => 'appearance',
            'title' => 'Appearance',
            'url' => 'admin-appearance.php',
            'icon' => 'palette',
            'roles' => ['admin'],
            'order' => 2,
            'badge' => ''
        ],
        [
            'id' => 'seo',
            'title' => 'SEO Settings',
            'url' => 'admin-seo.php',
            'icon' => 'search',
            'roles' => ['admin'],
            'order' => 3,
            'badge' => ''
        ],
        [
            'id' => 'integrations',
            'title' => 'Integrations',
            'url' => 'admin-integrations.php',
            'icon' => 'plug',
            'roles' => ['admin'],
            'order' => 4,
            'badge' => ''
        ],
        [
            'id' => 'backup',
            'title' => 'Backup & Restore',
            'url' => 'admin-backup.php',
            'icon' => 'database',
            'roles' => ['admin'],
            'order' => 5,
            'badge' => ''
        ]
    ]
];

/**
 * Function to render admin menu
 * 
 * @param array $menu Menu structure
 * @param string $current_page Current page identifier
 * @param string $user_role Current user role
 * @return string HTML output of menu
 */
function render_admin_menu($menu, $current_page, $user_role) {
    $output = '';
    
    // Sort menu items by order
    usort($menu, function($a, $b) {
        return $a['order'] - $b['order'];
    });
    
    foreach ($menu as $item) {
        // Check if user has permission to see this menu item
        if (!in_array($user_role, $item['roles'])) {
            continue;
        }
        
        $has_children = !empty($item['children']);
        $is_active = ($current_page == $item['id']);
        $is_parent_of_active = false;
        
        // Check if this item is a parent of the current page
        if ($has_children) {
            foreach ($item['children'] as $child) {
                if ($current_page == $child['id'] && in_array($user_role, $child['roles'])) {
                    $is_parent_of_active = true;
                    break;
                }
            }
        }
        
        // Create CSS classes
        $classes = [];
        if ($has_children) {
            $classes[] = 'has-submenu';
        }
        if ($is_active || $is_parent_of_active) {
            $classes[] = 'active';
        }
        
        $class_attr = !empty($classes) ? ' class="' . implode(' ', $classes) . '"' : '';
        
        // Start menu item
        $output .= '<li' . $class_attr . '>';
        
        // Menu item link
        $output .= '<a href="' . $item['url'] . '">';
        $output .= '<i class="fas fa-' . $item['icon'] . '"></i>';
        $output .= '<span>' . $item['title'] . '</span>';
        
        // Badge if any
        if (!empty($item['badge'])) {
            $output .= '<span class="badge">' . $item['badge'] . '</span>';
        }
        
        // Arrow for submenu
        if ($has_children) {
            $output .= '<i class="fas fa-chevron-right submenu-icon"></i>';
        }
        
        $output .= '</a>';
        
        // Submenu if any
        if ($has_children) {
            // Sort children by order
            usort($item['children'], function($a, $b) {
                return $a['order'] - $b['order'];
            });
            
            $output .= '<ul class="submenu' . ($is_parent_of_active ? ' open' : '') . '">';
            
            foreach ($item['children'] as $child) {
                // Check if user has permission to see this submenu item
                if (!in_array($user_role, $child['roles'])) {
                    continue;
                }
                
                $child_is_active = ($current_page == $child['id']);
                $child_class = $child_is_active ? ' class="active"' : '';
                
                $output .= '<li' . $child_class . '>';
                $output .= '<a href="' . $child['url'] . '">';
                $output .= '<i class="fas fa-' . (isset($child['icon']) ? $child['icon'] : 'circle') . '"></i>';
                $output .= $child['title'];
                
                // Badge if any
                if (!empty($child['badge'])) {
                    $output .= '<span class="badge">' . $child['badge'] . '</span>';
                }
                
                $output .= '</a></li>';
            }
            
            $output .= '</ul>';
        }
        
        $output .= '</li>';
    }
    
    return $output;
}
