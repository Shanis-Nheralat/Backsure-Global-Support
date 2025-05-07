<?php
/**
 * Admin Roles Management
 * Manages roles and permissions for the admin panel
 */

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once 'db_config.php';
require_once 'admin-auth.php';

// Check for authentication
require_admin_auth();

// Only allow superadmin/admin access to this page
require_admin_role(['superadmin', 'admin']);

// Get database connection
$db = get_db_connection();

// Function to get roles from database
function get_roles($db) {
    try {
        $stmt = $db->query("SELECT r.*, 
            (SELECT COUNT(*) FROM admins WHERE role = r.name) as users_count
            FROM roles r ORDER BY r.id");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching roles: " . $e->getMessage());
        return [];
    }
}

// Function to get permissions from database
function get_permissions_matrix($db) {
    try {
        // Get all roles
        $rolesStmt = $db->query("SELECT name FROM roles ORDER BY id");
        $roles = $rolesStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get all features/modules
        $featuresStmt = $db->query("SELECT DISTINCT feature FROM permissions ORDER BY feature");
        $features = $featuresStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Build permissions matrix
        $permissions = [];
        foreach ($features as $feature) {
            $permissions[$feature] = [];
            
            // For each role, get its permissions for this feature
            foreach ($roles as $role) {
                $permStmt = $db->prepare("SELECT permission FROM permissions 
                    WHERE feature = ? AND role = ?");
                $permStmt->execute([$feature, $role]);
                $perms = $permStmt->fetchAll(PDO::FETCH_COLUMN);
                
                $permissions[$feature][$role] = $perms;
            }
        }
        
        return $permissions;
    } catch (PDOException $e) {
        error_log("Error fetching permissions: " . $e->getMessage());
        return [];
    }
}

// Check if roles table exists, if not create it
try {
    $tableCheck = $db->query("SHOW TABLES LIKE 'roles'")->rowCount();
    if ($tableCheck == 0) {
        // Create roles table
        $db->exec("CREATE TABLE roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) UNIQUE NOT NULL,
            description TEXT,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Insert default roles
        $db->exec("INSERT INTO roles (name, description) VALUES 
            ('superadmin', 'Full access to all system features and settings'),
            ('admin', 'General admin with access to most features'),
            ('HR Admin', 'Access to leads, inquiries, and HR-related functions'),
            ('Marketing Admin', 'Manages blog, testimonials, FAQ and marketing content')
        ");
        
        // Create permissions table
        $db->exec("CREATE TABLE permissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            role VARCHAR(50) NOT NULL,
            feature VARCHAR(50) NOT NULL,
            permission VARCHAR(20) NOT NULL,
            UNIQUE KEY role_feature_perm (role, feature, permission)
        )");
        
        // Insert default permissions (simplified example)
        $db->exec("INSERT INTO permissions (role, feature, permission) VALUES 
            ('superadmin', 'dashboard', 'view'),
            ('superadmin', 'users', 'view'),
            ('superadmin', 'users', 'add'),
            ('superadmin', 'users', 'edit'),
            ('superadmin', 'users', 'delete'),
            ('admin', 'dashboard', 'view'),
            ('admin', 'users', 'view'),
            ('admin', 'users', 'add'),
            ('admin', 'users', 'edit'),
            ('HR Admin', 'dashboard', 'view'),
            ('HR Admin', 'inquiries', 'view'),
            ('HR Admin', 'inquiries', 'reply'),
            ('Marketing Admin', 'dashboard', 'view'),
            ('Marketing Admin', 'blog', 'view'),
            ('Marketing Admin', 'blog', 'add'),
            ('Marketing Admin', 'blog', 'edit'),
            ('Marketing Admin', 'blog', 'delete')
        ");
    }
} catch (PDOException $e) {
    error_log("Error checking/creating tables: " . $e->getMessage());
}

// Get roles and permissions from database or use mock data if tables not yet set up
try {
    $roles = get_roles($db);
    if (empty($roles)) {
        // Use mock data as fallback
        $roles = [
            [
                'id' => 1,
                'name' => 'superadmin',
                'description' => 'Full access to all system features and settings',
                'users_count' => 1,
                'created_at' => date('Y-m-d'),
                'status' => 'active'
            ],
            [
                'id' => 2,
                'name' => 'admin',
                'description' => 'General admin with access to most features',
                'users_count' => 2,
                'created_at' => date('Y-m-d'),
                'status' => 'active'
            ],
            [
                'id' => 3,
                'name' => 'HR Admin',
                'description' => 'Access to leads, inquiries, and HR-related functions',
                'users_count' => 5,
                'created_at' => '2023-06-20',
                'status' => 'active'
            ],
            [
                'id' => 4,
                'name' => 'Marketing Admin',
                'description' => 'Manages blog, testimonials, FAQ and marketing content',
                'users_count' => 4,
                'created_at' => '2023-07-10',
                'status' => 'active'
            ]
        ];
    }
    
    $permissions = get_permissions_matrix($db);
    if (empty($permissions)) {
        // Use mock data as fallback
        $permissions = [
            'dashboard' => ['superadmin' => ['view'], 'admin' => ['view'], 'HR Admin' => ['view'], 'Marketing Admin' => ['view']],
            'users' => ['superadmin' => ['view', 'add', 'edit', 'delete'], 'admin' => ['view', 'add', 'edit'], 'HR Admin' => [], 'Marketing Admin' => []],
            'roles' => ['superadmin' => ['view', 'add', 'edit', 'delete'], 'admin' => ['view'], 'HR Admin' => [], 'Marketing Admin' => []],
            'services' => ['superadmin' => ['view', 'add', 'edit', 'delete'], 'admin' => ['view', 'add', 'edit', 'delete'], 'HR Admin' => [], 'Marketing Admin' => ['view', 'add', 'edit', 'delete']],
            'blog' => ['superadmin' => ['view', 'add', 'edit', 'delete'], 'admin' => ['view', 'add', 'edit', 'delete'], 'HR Admin' => [], 'Marketing Admin' => ['view', 'add', 'edit', 'delete']],
            'inquiries' => ['superadmin' => ['view', 'reply', 'delete'], 'admin' => ['view', 'reply', 'delete'], 'HR Admin' => ['view', 'reply'], 'Marketing Admin' => ['view', 'reply']],
            'testimonials' => ['superadmin' => ['view', 'add', 'edit', 'delete'], 'admin' => ['view', 'add', 'edit', 'delete'], 'HR Admin' => [], 'Marketing Admin' => ['view', 'add', 'edit', 'delete']],
            'faq' => ['superadmin' => ['view', 'add', 'edit', 'delete'], 'admin' => ['view', 'add', 'edit', 'delete'], 'HR Admin' => [], 'Marketing Admin' => ['view', 'add', 'edit', 'delete']],
            'subscribers' => ['superadmin' => ['view', 'export', 'delete'], 'admin' => ['view', 'export'], 'HR Admin' => [], 'Marketing Admin' => []],
            'seo' => ['superadmin' => ['view', 'edit'], 'admin' => ['view', 'edit'], 'HR Admin' => [], 'Marketing Admin' => []],
            'integrations' => ['superadmin' => ['view', 'edit'], 'admin' => ['view', 'edit'], 'HR Admin' => [], 'Marketing Admin' => []],
            'general' => ['superadmin' => ['view', 'edit'], 'admin' => ['view', 'edit'], 'HR Admin' => [], 'Marketing Admin' => []],
            'appearance' => ['superadmin' => ['view', 'edit'], 'admin' => ['view', 'edit'], 'HR Admin' => [], 'Marketing Admin' => []],
            'backup' => ['superadmin' => ['view', 'create', 'restore'], 'admin' => ['view', 'create'], 'HR Admin' => [], 'Marketing Admin' => []],
            'profile' => ['superadmin' => ['view', 'edit'], 'admin' => ['view', 'edit'], 'HR Admin' => ['view', 'edit'], 'Marketing Admin' => ['view', 'edit']]
        ];
    }
} catch (PDOException $e) {
    error_log("Error setting up roles/permissions: " . $e->getMessage());
    // Use mock data as fallback
    $roles = [
        [
            'id' => 1,
            'name' => 'superadmin',
            'description' => 'Full access to all system features and settings',
            'users_count' => 1,
            'created_at' => date('Y-m-d'),
            'status' => 'active'
        ],
        [
            'id' => 2,
            'name' => 'admin',
            'description' => 'General admin with access to most features',
            'users_count' => 2,
            'created_at' => date('Y-m-d'),
            'status' => 'active'
        ],
        [
            'id' => 3,
            'name' => 'HR Admin',
            'description' => 'Access to leads, inquiries, and HR-related functions',
            'users_count' => 5,
            'created_at' => '2023-06-20',
            'status' => 'active'
        ],
        [
            'id' => 4,
            'name' => 'Marketing Admin',
            'description' => 'Manages blog, testimonials, FAQ and marketing content',
            'users_count' => 4,
            'created_at' => '2023-07-10',
            'status' => 'active'
        ]
    ];
    
    $permissions = [
        'dashboard' => ['superadmin' => ['view'], 'admin' => ['view'], 'HR Admin' => ['view'], 'Marketing Admin' => ['view']],
        'users' => ['superadmin' => ['view', 'add', 'edit', 'delete'], 'admin' => ['view', 'add', 'edit'], 'HR Admin' => [], 'Marketing Admin' => []],
        'roles' => ['superadmin' => ['view', 'add', 'edit', 'delete'], 'admin' => ['view'], 'HR Admin' => [], 'Marketing Admin' => []],
        'services' => ['superadmin' => ['view', 'add', 'edit', 'delete'], 'admin' => ['view', 'add', 'edit', 'delete'], 'HR Admin' => [], 'Marketing Admin' => ['view', 'add', 'edit', 'delete']],
        'blog' => ['superadmin' => ['view', 'add', 'edit', 'delete'], 'admin' => ['view', 'add', 'edit', 'delete'], 'HR Admin' => [], 'Marketing Admin' => ['view', 'add', 'edit', 'delete']],
        'inquiries' => ['superadmin' => ['view', 'reply', 'delete'], 'admin' => ['view', 'reply', 'delete'], 'HR Admin' => ['view', 'reply'], 'Marketing Admin' => ['view', 'reply']],
        'testimonials' => ['superadmin' => ['view', 'add', 'edit', 'delete'], 'admin' => ['view', 'add', 'edit', 'delete'], 'HR Admin' => [], 'Marketing Admin' => ['view', 'add', 'edit', 'delete']],
        'faq' => ['superadmin' => ['view', 'add', 'edit', 'delete'], 'admin' => ['view', 'add', 'edit', 'delete'], 'HR Admin' => [], 'Marketing Admin' => ['view', 'add', 'edit', 'delete']],
        'subscribers' => ['superadmin' => ['view', 'export', 'delete'], 'admin' => ['view', 'export'], 'HR Admin' => [], 'Marketing Admin' => []],
        'seo' => ['superadmin' => ['view', 'edit'], 'admin' => ['view', 'edit'], 'HR Admin' => [], 'Marketing Admin' => []],
        'integrations' => ['superadmin' => ['view', 'edit'], 'admin' => ['view', 'edit'], 'HR Admin' => [], 'Marketing Admin' => []],
        'general' => ['superadmin' => ['view', 'edit'], 'admin' => ['view', 'edit'], 'HR Admin' => [], 'Marketing Admin' => []],
        'appearance' => ['superadmin' => ['view', 'edit'], 'admin' => ['view', 'edit'], 'HR Admin' => [], 'Marketing Admin' => []],
        'backup' => ['superadmin' => ['view', 'create', 'restore'], 'admin' => ['view', 'create'], 'HR Admin' => [], 'Marketing Admin' => []],
        'profile' => ['superadmin' => ['view', 'edit'], 'admin' => ['view', 'edit'], 'HR Admin' => ['view', 'edit'], 'Marketing Admin' => ['view', 'edit']]
    ];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = false;
    $error = "";
    
    // Process role creation
    if (isset($_POST['role_name']) && !empty($_POST['role_name'])) {
        $role_name = trim($_POST['role_name']);
        $role_description = trim($_POST['role_description'] ?? '');
        
        try {
            // Check if role already exists
            $checkStmt = $db->prepare("SELECT id FROM roles WHERE name = ?");
            $checkStmt->execute([$role_name]);
            
            if ($checkStmt->rowCount() > 0) {
                $error = "Role '{$role_name}' already exists.";
            } else {
                // Insert new role
                $insertStmt = $db->prepare("INSERT INTO roles (name, description) VALUES (?, ?)");
                $success = $insertStmt->execute([$role_name, $role_description]);
                
                // Process permissions
                if ($success && isset($_POST['permissions']) && is_array($_POST['permissions'])) {
                    foreach ($_POST['permissions'] as $feature => $perms) {
                        foreach ($perms as $perm) {
                            $permStmt = $db->prepare("INSERT INTO permissions (role, feature, permission) VALUES (?, ?, ?)");
                            $permStmt->execute([$role_name, $feature, $perm]);
                        }
                    }
                }
                
                // Log the action
                if (function_exists('log_admin_action')) {
                    log_admin_action('create', 'role', $db->lastInsertId(), "Created new role: {$role_name}");
                }
            }
        } catch (PDOException $e) {
            error_log("Error creating role: " . $e->getMessage());
            $error = "Database error while creating role.";
        }
    }
    
    // Process permission matrix update
    if (isset($_POST['action']) && $_POST['action'] === 'update_permissions' && isset($_POST['matrix'])) {
        try {
            // First, clear existing permissions
            $db->exec("DELETE FROM permissions");
            
            // Then insert the new ones
            foreach ($_POST['matrix'] as $feature => $roles) {
                foreach ($roles as $role => $perms) {
                    if (is_array($perms)) {
                        foreach ($perms as $perm) {
                            $permStmt = $db->prepare("INSERT INTO permissions (role, feature, permission) VALUES (?, ?, ?)");
                            $permStmt->execute([$role, $feature, $perm]);
                        }
                    }
                }
            }
            
            $success = true;
            
            // Log the action
            if (function_exists('log_admin_action')) {
                log_admin_action('update', 'permissions', 0, "Updated permission matrix");
            }
        } catch (PDOException $e) {
            error_log("Error updating permissions: " . $e->getMessage());
            $error = "Database error while updating permissions.";
        }
    }
    
    // Redirect with success/error message
    if ($success) {
        header('Location: admin-roles.php?success=1');
    } else {
        header('Location: admin-roles.php?error=' . urlencode($error));
    }
    exit;
}

// Get admin user info for the UI
$admin_user = get_admin_user();
$current_page = 'roles';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles Management - Backsure Global Support</title>
    <!-- Include your CSS files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar Navigation -->
        <div class="admin-sidebar">
            <div class="sidebar-header">
                <img src="assets/images/logo.png" alt="Backsure Global Logo" class="sidebar-logo">
                <h3>Backsure Global</h3>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-users"></i> User Management</a>
                        <ul class="submenu">
                            <li><a href="admin-users.php">All Users</a></li>
                            <li><a href="admin-roles.php" class="active">Roles & Permissions</a></li>
                        </ul>
                    </li>
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-briefcase"></i> Services</a>
                        <ul class="submenu">
                            <li><a href="admin-services.php">All Services</a></li>
                        </ul>
                    </li>
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-newspaper"></i> Content</a>
                        <ul class="submenu">
                            <li><a href="admin-blog.php">Blog Posts</a></li>
                            <li><a href="admin-testimonials.php">Testimonials</a></li>
                            <li><a href="admin-faq.php">FAQ</a></li>
                        </ul>
                    </li>
                    <li><a href="admin-enquiries.php"><i class="fas fa-inbox"></i> Inquiries</a></li>
                    <li><a href="admin-clients.php"><i class="fas fa-building"></i> Clients</a></li>
                    <li><a href="admin-subscribers.php"><i class="fas fa-envelope"></i> Subscribers</a></li>
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-cog"></i> Settings</a>
                        <ul class="submenu">
                            <li><a href="admin-seo.php">SEO Settings</a></li>
                            <li><a href="admin-integrations.php">Integrations</a></li>
                            <li><a href="admin-general.php">General Settings</a></li>
                            <li><a href="admin-appearance.php">Appearance</a></li>
                            <li><a href="admin-backup.php">Backup & Restore</a></li>
                        </ul>
                    </li>
                    <li><a href="admin-profile.php"><i class="fas fa-user-circle"></i> My Profile</a></li>
                    <li><a href="admin-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="admin-content">
            <!-- Top Navigation Bar -->
            <div class="admin-topbar">
                <div class="toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="search-box">
                    <input type="text" placeholder="Search...">
                    <button><i class="fas fa-search"></i></button>
                </div>
                <div class="user-info">
                    <div class="notifications">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </div>
                    <div class="user-profile">
                        <img src="assets/images/admin-avatar.jpg" alt="Admin User">
                        <span><?php echo htmlspecialchars($admin_user['username']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="content-wrapper">
                <!-- Page Header -->
                <div class="content-header">
                    <h1>Roles & Permissions Management</h1>
                    <div class="breadcrumb">
                        <a href="admin-dashboard.php">Dashboard</a> / <span>Roles & Permissions</span>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> The role has been updated successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <!-- Main Content -->
                <div class="content-body">
                    <div class="card">
                        <div class="card-header">
                            <h3>Role Management</h3>
                            <div class="card-actions">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                                    <i class="fas fa-plus"></i> Add New Role
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Role Name</th>
                                            <th>Description</th>
                                            <th>Users</th>
                                            <th>Created On</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($roles as $role): ?>
                                        <tr>
                                            <td><?php echo $role['id']; ?></td>
                                            <td><?php echo htmlspecialchars($role['name']); ?></td>
                                            <td><?php echo htmlspecialchars($role['description']); ?></td>
                                            <td><?php echo $role['users_count']; ?></td>
                                            <td><?php echo isset($role['created_at']) ? $role['created_at'] : 'N/A'; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo (isset($role['status']) && $role['status'] == 'active') ? 'success' : 'danger'; ?>">
                                                    <?php echo isset($role['status']) ? ucfirst($role['status']) : 'Active'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info view-permissions" data-role-id="<?php echo $role['id']; ?>" data-role-name="<?php echo htmlspecialchars($role['name']); ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-primary edit-role" data-role-id="<?php echo $role['id']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <?php if($role['name'] !== 'superadmin' && $role['name'] !== 'admin'): ?>
                                                    <button type="button" class="btn btn-danger delete-role" data-role-id="<?php echo $role['id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Permission Matrix Card -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3>Permission Matrix</h3>
                            <div class="card-actions">
                                <button type="button" class="btn btn-primary" id="editPermissionsBtn">
                                    <i class="fas fa-edit"></i> Edit Permissions
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered permission-matrix">
                                    <thead>
                                        <tr>
                                            <th>Feature/Module</th>
                                            <?php 
                                            $role_names = array_unique(array_column($roles, 'name'));
                                            foreach($role_names as $roleName): 
                                            ?>
                                            <th><?php echo htmlspecialchars($roleName); ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($permissions as $feature => $rolePermissions): ?>
                                        <tr>
                                            <td class="text-capitalize"><?php echo str_replace('_', ' ', $feature); ?></td>
                                            <?php foreach($role_names as $roleName): ?>
                                            <td>
                                                <?php 
                                                if(isset($rolePermissions[$roleName]) && !empty($rolePermissions[$roleName])) {
                                                    foreach($rolePermissions[$roleName] as $permission) {
                                                        echo '<span class="badge bg-primary me-1">' . ucfirst($permission) . '</span>';
                                                    }
                                                } else {
                                                    echo '<span class="badge bg-secondary">No Access</span>';
                                                }
                                                ?>
                                            </td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Role Modal -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoleModalLabel">Add New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin-roles.php" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="roleName" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="roleName" name="role_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="roleDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="roleDescription" name="role_description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="permission-checkboxes">
                                <?php foreach(array_keys($permissions) as $feature): ?>
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="text-capitalize mb-0"><?php echo str_replace('_', ' ', $feature); ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[<?php echo $feature; ?>][]" value="view" id="view_<?php echo $feature; ?>">
                                                    <label class="form-check-label" for="view_<?php echo $feature; ?>">
                                                        View
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[<?php echo $feature; ?>][]" value="add" id="add_<?php echo $feature; ?>">
                                                    <label class="form-check-label" for="add_<?php echo $feature; ?>">
                                                        Add
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[<?php echo $feature; ?>][]" value="edit" id="edit_<?php echo $feature; ?>">
                                                    <label class="form-check-label" for="edit_<?php echo $feature; ?>">
                                                        Edit
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[<?php echo $feature; ?>][]" value="delete" id="delete_<?php echo $feature; ?>">
                                                    <label class="form-check-label" for="delete_<?php echo $feature; ?>">
                                                        Delete
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Permissions Modal -->
    <div class="modal fade" id="viewPermissionsModal" tabindex="-1" aria-labelledby="viewPermissionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewPermissionsModalLabel">Permissions for <span id="roleTitleSpan"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Feature/Module</th>
                                    <th>Permissions</th>
                                </tr>
                            </thead>
                            <tbody id="permissionsTableBody">
                                <!-- This will be filled dynamically by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Permissions Modal -->
    <div class="modal fade" id="editPermissionsModal" tabindex="-1" aria-labelledby="editPermissionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPermissionsModalLabel">Edit Permission Matrix</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin-roles.php" method="post">
                    <input type="hidden" name="action" value="update_permissions">
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Feature/Module</th>
                                        <?php foreach($role_names as $roleName): ?>
                                        <th><?php echo htmlspecialchars($roleName); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach(array_keys($permissions) as $feature): ?>
                                    <tr>
                                        <td class="text-capitalize"><?php echo str_replace('_', ' ', $feature); ?></td>
                                        
                                        <?php foreach($role_names as $roleName): ?>
                                        <td>
                                            <div class="d-flex flex-wrap">
                                                <div class="form-check me-2">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][<?php echo $roleName; ?>][]" value="view" 
                                                        <?php echo (isset($permissions[$feature][$roleName]) && in_array('view', $permissions[$feature][$roleName])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">View</label>
                                                </div>
                                                <div class="form-check me-2">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][<?php echo $roleName; ?>][]" value="add" 
                                                        <?php echo (isset($permissions[$feature][$roleName]) && in_array('add', $permissions[$feature][$roleName])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Add</label>
                                                </div>
                                                <div class="form-check me-2">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][<?php echo $roleName; ?>][]" value="edit" 
                                                        <?php echo (isset($permissions[$feature][$roleName]) && in_array('edit', $permissions[$feature][$roleName])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Edit</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][<?php echo $roleName; ?>][]" value="delete" 
                                                        <?php echo (isset($permissions[$feature][$roleName]) && in_array('delete', $permissions[$feature][$roleName])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Delete</label>
                                                </div>
                                            </div>
                                        </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin-dashboard.js"></script>
    <script>
    $(document).ready(function() {
        // Toggle sidebar on mobile
        $('.toggle-sidebar').click(function() {
            $('.admin-sidebar').toggleClass('active');
        });

        // View permissions button click handler
        $('.view-permissions').click(function() {
            const roleId = $(this).data('role-id');
            const roleName = $(this).data('role-name');
            
            // Set the role name in the modal title
            $('#roleTitleSpan').text(roleName);
            
            // Get permissions for this role and populate the table
            let permissionsHtml = '';
            
            // This would normally be an AJAX call to get the permissions
            // For now, we'll use our client-side data
            <?php echo "const permissions = " . json_encode($permissions) . ";"; ?>
            
            // Loop through the permissions and add rows for features this role has access to
            Object.keys(permissions).forEach(feature => {
                if(permissions[feature][roleName] && permissions[feature][roleName].length > 0) {
                    permissionsHtml += `
                        <tr>
                            <td class="text-capitalize">${feature.replace('_', ' ')}</td>
                            <td>
                                ${permissions[feature][roleName].map(perm => 
                                    `<span class="badge bg-primary me-1">${perm.charAt(0).toUpperCase() + perm.slice(1)}</span>`
                                ).join('')}
                            </td>
                        </tr>
                    `;
                }
            });
            
            // If no permissions found
            if(permissionsHtml === '') {
                permissionsHtml = `
                    <tr>
                        <td colspan="2" class="text-center">No permissions assigned to this role.</td>
                    </tr>
                `;
            }
            
            // Update the table body
            $('#permissionsTableBody').html(permissionsHtml);
            
            // Show the modal
            $('#viewPermissionsModal').modal('show');
        });
        
        // Edit role button click handler
        $('.edit-role').click(function() {
            const roleId = $(this).data('role-id');
            // In a real implementation, we'd make an AJAX call to get the role details
            // For now, just show a message
            alert('Edit role ' + roleId + ' functionality would go here. This would be implemented with an AJAX call to fetch role details.');
        });
        
        // Delete role button click handler
        $('.delete-role').click(function() {
            const roleId = $(this).data('role-id');
            if(confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
                // In a real implementation, we'd make an AJAX call to delete the role
                // For now, just show a message
                window.location.href = 'admin-roles.php?action=delete&role_id=' + roleId;
            }
        });
        
        // Edit permissions matrix button click handler
        $('#editPermissionsBtn').click(function() {
            $('#editPermissionsModal').modal('show');
        });
        
        // Handle parent permission logic (View must be checked if any other permission is checked)
        $('.permission-checkbox, .form-check-input').change(function() {
            const feature = $(this).closest('.card').find('.card-header h6').text().trim().toLowerCase().replace(/ /g, '_');
            const permType = $(this).val();
            
            // If any permission other than VIEW is checked, VIEW must also be checked
            if(permType !== 'view' && $(this).prop('checked')) {
                $(`input[name="permissions[${feature}][]"][value="view"]`).prop('checked', true);
            }
            
            // If VIEW is unchecked, uncheck all other permissions
            if(permType === 'view' && !$(this).prop('checked')) {
                $(`input[name="permissions[${feature}][]"]:not([value="view"])`).prop('checked', false);
            }
        });
        
        // Similar logic for the matrix form
        $('.form-check-input').change(function() {
            const name = $(this).attr('name');
            const value = $(this).val();
            
            if (name && name.includes('matrix')) {
                // Extract the feature and role from the name attribute
                const matches = name.match(/matrix\[(.*?)\]\[(.*?)\]/);
                if (matches && matches.length >= 3) {
                    const feature = matches[1];
                    const role = matches[2];
                    
                    // If any permission other than VIEW is checked, VIEW must also be checked
                    if(value !== 'view' && $(this).prop('checked')) {
                        $(`input[name="matrix[${feature}][${role}][]"][value="view"]`).prop('checked', true);
                    }
                    
                    // If VIEW is unchecked, uncheck all other permissions
                    if(value === 'view' && !$(this).prop('checked')) {
                        $(`input[name="matrix[${feature}][${role}][]"]:not([value="view"])`).prop('checked', false);
                    }
                }
            }
        });
    });
    </script>
</body>
</html>
