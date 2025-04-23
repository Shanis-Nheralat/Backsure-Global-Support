<?php
// Initialize session if not already started
session_start();

// TODO: Add authentication check here
// if(!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
//     header("Location: admin-login.php");
//     exit;
// }

// TODO: Check if user has permission to access this page
// if($_SESSION['admin_role'] != 'Super Admin') {
//     header("Location: admin-dashboard.php?error=unauthorized");
//     exit;
// }

// Mock data for roles and permissions
$roles = [
    [
        'id' => 1,
        'name' => 'Super Admin',
        'description' => 'Full access to all system features and settings',
        'users_count' => 3,
        'created_at' => '2023-05-15',
        'status' => 'active'
    ],
    [
        'id' => 2,
        'name' => 'HR Admin',
        'description' => 'Access to leads, inquiries, and HR-related functions',
        'users_count' => 5,
        'created_at' => '2023-06-20',
        'status' => 'active'
    ],
    [
        'id' => 3,
        'name' => 'Marketing Admin',
        'description' => 'Manages blog, testimonials, FAQ and marketing content',
        'users_count' => 4,
        'created_at' => '2023-07-10',
        'status' => 'active'
    ]
];

// Mock data for permissions matrix
$permissions = [
    'dashboard' => ['Super Admin' => ['view'], 'HR Admin' => ['view'], 'Marketing Admin' => ['view']],
    'users' => ['Super Admin' => ['view', 'add', 'edit', 'delete'], 'HR Admin' => [], 'Marketing Admin' => []],
    'roles' => ['Super Admin' => ['view', 'add', 'edit', 'delete'], 'HR Admin' => [], 'Marketing Admin' => []],
    'services' => ['Super Admin' => ['view', 'add', 'edit', 'delete'], 'HR Admin' => [], 'Marketing Admin' => ['view', 'add', 'edit', 'delete']],
    'blog' => ['Super Admin' => ['view', 'add', 'edit', 'delete'], 'HR Admin' => [], 'Marketing Admin' => ['view', 'add', 'edit', 'delete']],
    'inquiries' => ['Super Admin' => ['view', 'reply', 'delete'], 'HR Admin' => ['view', 'reply'], 'Marketing Admin' => ['view', 'reply']],
    'testimonials' => ['Super Admin' => ['view', 'add', 'edit', 'delete'], 'HR Admin' => [], 'Marketing Admin' => ['view', 'add', 'edit', 'delete']],
    'faq' => ['Super Admin' => ['view', 'add', 'edit', 'delete'], 'HR Admin' => [], 'Marketing Admin' => ['view', 'add', 'edit', 'delete']],
    'subscribers' => ['Super Admin' => ['view', 'export', 'delete'], 'HR Admin' => [], 'Marketing Admin' => []],
    'seo' => ['Super Admin' => ['view', 'edit'], 'HR Admin' => [], 'Marketing Admin' => []],
    'integrations' => ['Super Admin' => ['view', 'edit'], 'HR Admin' => [], 'Marketing Admin' => []],
    'general' => ['Super Admin' => ['view', 'edit'], 'HR Admin' => [], 'Marketing Admin' => []],
    'appearance' => ['Super Admin' => ['view', 'edit'], 'HR Admin' => [], 'Marketing Admin' => []],
    'backup' => ['Super Admin' => ['view', 'create', 'restore'], 'HR Admin' => [], 'Marketing Admin' => []],
    'profile' => ['Super Admin' => ['view', 'edit'], 'HR Admin' => ['view', 'edit'], 'Marketing Admin' => ['view', 'edit']]
];

// Handle form submissions (for a real implementation)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process role creation/editing logic here
    // For now, we'll just redirect back with a success message
    header('Location: admin-roles.php?success=1');
    exit;
}
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
                        <span>Admin User</span>
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
                    <strong>Error!</strong> There was a problem processing your request.
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
                                            <td><?php echo $role['name']; ?></td>
                                            <td><?php echo $role['description']; ?></td>
                                            <td><?php echo $role['users_count']; ?></td>
                                            <td><?php echo $role['created_at']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo ($role['status'] == 'active') ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($role['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info view-permissions" data-role-id="<?php echo $role['id']; ?>" data-role-name="<?php echo $role['name']; ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-primary edit-role" data-role-id="<?php echo $role['id']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <?php if($role['name'] !== 'Super Admin'): ?>
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
                                            <th>Super Admin</th>
                                            <th>HR Admin</th>
                                            <th>Marketing Admin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($permissions as $feature => $rolePermissions): ?>
                                        <tr>
                                            <td class="text-capitalize"><?php echo str_replace('_', ' ', $feature); ?></td>
                                            <td>
                                                <?php 
                                                if(isset($rolePermissions['Super Admin']) && !empty($rolePermissions['Super Admin'])) {
                                                    foreach($rolePermissions['Super Admin'] as $permission) {
                                                        echo '<span class="badge bg-primary me-1">' . ucfirst($permission) . '</span>';
                                                    }
                                                } else {
                                                    echo '<span class="badge bg-secondary">No Access</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if(isset($rolePermissions['HR Admin']) && !empty($rolePermissions['HR Admin'])) {
                                                    foreach($rolePermissions['HR Admin'] as $permission) {
                                                        echo '<span class="badge bg-primary me-1">' . ucfirst($permission) . '</span>';
                                                    }
                                                } else {
                                                    echo '<span class="badge bg-secondary">No Access</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if(isset($rolePermissions['Marketing Admin']) && !empty($rolePermissions['Marketing Admin'])) {
                                                    foreach($rolePermissions['Marketing Admin'] as $permission) {
                                                        echo '<span class="badge bg-primary me-1">' . ucfirst($permission) . '</span>';
                                                    }
                                                } else {
                                                    echo '<span class="badge bg-secondary">No Access</span>';
                                                }
                                                ?>
                                            </td>
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
                                        <th>Super Admin</th>
                                        <th>HR Admin</th>
                                        <th>Marketing Admin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach(array_keys($permissions) as $feature): ?>
                                    <tr>
                                        <td class="text-capitalize"><?php echo str_replace('_', ' ', $feature); ?></td>
                                        
                                        <!-- Super Admin Permissions -->
                                        <td>
                                            <div class="d-flex flex-wrap">
                                                <div class="form-check me-2">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][Super Admin][]" value="view" 
                                                        <?php echo (isset($permissions[$feature]['Super Admin']) && in_array('view', $permissions[$feature]['Super Admin'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">View</label>
                                                </div>
                                                <div class="form-check me-2">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][Super Admin][]" value="add" 
                                                        <?php echo (isset($permissions[$feature]['Super Admin']) && in_array('add', $permissions[$feature]['Super Admin'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Add</label>
                                                </div>
                                                <div class="form-check me-2">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][Super Admin][]" value="edit" 
                                                        <?php echo (isset($permissions[$feature]['Super Admin']) && in_array('edit', $permissions[$feature]['Super Admin'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Edit</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][Super Admin][]" value="delete" 
                                                        <?php echo (isset($permissions[$feature]['Super Admin']) && in_array('delete', $permissions[$feature]['Super Admin'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Delete</label>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- HR Admin Permissions -->
                                        <td>
                                            <div class="d-flex flex-wrap">
                                                <div class="form-check me-2">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][HR Admin][]" value="view" 
                                                        <?php echo (isset($permissions[$feature]['HR Admin']) && in_array('view', $permissions[$feature]['HR Admin'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">View</label>
                                                </div>
                                                <div class="form-check me-2">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][HR Admin][]" value="add" 
                                                        <?php echo (isset($permissions[$feature]['HR Admin']) && in_array('add', $permissions[$feature]['HR Admin'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Add</label>
                                                </div>
                                                <div class="form-check me-2">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][HR Admin][]" value="edit" 
                                                        <?php echo (isset($permissions[$feature]['HR Admin']) && in_array('edit', $permissions[$feature]['HR Admin'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Edit</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][HR Admin][]" value="delete" 
                                                        <?php echo (isset($permissions[$feature]['HR Admin']) && in_array('delete', $permissions[$feature]['HR Admin'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Delete</label>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Marketing Admin Permissions -->
                                        <td>
                                            <div class="d-flex flex-wrap">
                                                <div class="form-check me-2">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][Marketing Admin][]" value="view" 
                                                        <?php echo (isset($permissions[$feature]['Marketing Admin']) && in_array('view', $permissions[$feature]['Marketing Admin'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">View</label>
                                                </div>
                                                <div class="form-check me-2">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][Marketing Admin][]" value="add" 
                                                        <?php echo (isset($permissions[$feature]['Marketing Admin']) && in_array('add', $permissions[$feature]['Marketing Admin'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Add</label>
                                                </div>
                                                <div class="form-check me-2">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][Marketing Admin][]" value="edit" 
                                                        <?php echo (isset($permissions[$feature]['Marketing Admin']) && in_array('edit', $permissions[$feature]['Marketing Admin'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Edit</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="matrix[<?php echo $feature; ?>][Marketing Admin][]" value="delete" 
                                                        <?php echo (isset($permissions[$feature]['Marketing Admin']) && in_array('delete', $permissions[$feature]['Marketing Admin'])) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Delete</label>
                                                </div>
                                            </div>
                                        </td>
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
            // For now, we'll use our mock data
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
            // This would normally be an AJAX call to get the role details
            // Then populate a modal form
            // For now, just show an alert
            alert('Edit role ' + roleId + ' functionality would go here!');
        });
        
        // Delete role button click handler
        $('.delete-role').click(function() {
            const roleId = $(this).data('role-id');
            if(confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
                // This would normally be an AJAX call to delete the role
                // For now, just show an alert
                alert('Delete role ' + roleId + ' functionality would go here!');
            }
        });
        
        // Edit permissions matrix button click handler
        $('#editPermissionsBtn').click(function() {
            $('#editPermissionsModal').modal('show');
        });
        
        // Select all permissions in a category
        $('.select-all-permissions').click(function() {
            const category = $(this).data('category');
            const isChecked = $(this).prop('checked');
            $(`.permission-checkbox[data-category="${category}"]`).prop('checked', isChecked);
        });
        
        // Handle parent permission logic (View must be checked if any other permission is checked)
        $('.permission-checkbox').change(function() {
            const category = $(this).data('category');
            const permType = $(this).data('perm-type');
            
            // If any permission other than VIEW is checked, VIEW must also be checked
            if(permType !== 'view' && $(this).prop('checked')) {
                $(`.permission-checkbox[data-category="${category}"][data-perm-type="view"]`).prop('checked', true);
            }
            
            // If VIEW is unchecked, uncheck all other permissions
            if(permType === 'view' && !$(this).prop('checked')) {
                $(`.permission-checkbox[data-category="${category}"]:not([data-perm-type="view"])`).prop('checked', false);
            }
        });
    });
    </script>
</body>
</html>
