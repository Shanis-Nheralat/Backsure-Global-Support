<?php
// Initialize session if not already started
session_start();

// TODO: Add authentication check here
// if(!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
//     header("Location: admin-login.php");
//     exit;
// }

// Mock data for the logged-in admin user
$admin = [
    'id' => 1,
    'name' => 'John Doe',
    'email' => 'john.doe@backsure.com',
    'role' => 'Super Admin',
    'avatar' => 'assets/images/admin-avatar.jpg',
    'phone' => '+1 (555) 123-4567',
    'created_at' => '2023-01-15',
    'last_login' => '2023-11-10 14:30:22',
    'status' => 'active',
    'department' => 'Management',
    'bio' => 'Experienced administrator with over 10 years in the industry. Specializes in system optimization and team management.'
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Process profile update
    if ($action === 'update_profile') {
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-profile.php?success=profile_updated');
        exit;
    }
    
    // Process password change
    if ($action === 'change_password') {
        // In a real implementation, validate passwords and update
        // For now, just redirect with success message
        header('Location: admin-profile.php?success=password_changed');
        exit;
    }
    
    // Process avatar upload
    if ($action === 'upload_avatar') {
        // In a real implementation, handle file upload
        // For now, just redirect with success message
        header('Location: admin-profile.php?success=avatar_updated');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Backsure Global Support</title>
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
                            <li><a href="admin-roles.php">Roles & Permissions</a></li>
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
                    <li><a href="admin-profile.php" class="active"><i class="fas fa-user-circle"></i> My Profile</a></li>
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
                        <img src="<?php echo $admin['avatar']; ?>" alt="<?php echo $admin['name']; ?>">
                        <span><?php echo $admin['name']; ?></span>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="content-wrapper">
                <!-- Page Header -->
                <div class="content-header">
                    <h1>My Profile</h1>
                    <div class="breadcrumb">
                        <a href="admin-dashboard.php">Dashboard</a> / <span>My Profile</span>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php if($_GET['success'] == 'profile_updated'): ?>
                    <strong>Success!</strong> Your profile information has been updated.
                    <?php elseif($_GET['success'] == 'password_changed'): ?>
                    <strong>Success!</strong> Your password has been changed.
                    <?php elseif($_GET['success'] == 'avatar_updated'): ?>
                    <strong>Success!</strong> Your profile picture has been updated.
                    <?php endif; ?>
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
                    <div class="row">
                        <!-- Profile Overview Card -->
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="profile-avatar-container mb-3">
                                        <img src="<?php echo $admin['avatar']; ?>" class="profile-avatar" alt="<?php echo $admin['name']; ?>">
                                        <button class="avatar-edit-btn" data-bs-toggle="modal" data-bs-target="#avatarModal">
                                            <i class="fas fa-camera"></i>
                                        </button>
                                    </div>
                                    <h4><?php echo $admin['name']; ?></h4>
                                    <p class="badge bg-primary"><?php echo $admin['role']; ?></p>
                                    <p class="text-muted"><?php echo $admin['email']; ?></p>
                                    <p class="text-muted"><?php echo $admin['phone']; ?></p>
                                    <hr>
                                    <div class="profile-stats">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <h6>Department</h6>
                                                <p><?php echo $admin['department']; ?></p>
                                            </div>
                                            <div class="col-4">
                                                <h6>Status</h6>
                                                <p>
                                                    <span class="badge bg-<?php echo ($admin['status'] == 'active') ? 'success' : 'danger'; ?>">
                                                        <?php echo ucfirst($admin['status']); ?>
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-4">
                                                <h6>Joined</h6>
                                                <p><?php echo date('M Y', strtotime($admin['created_at'])); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="profile-bio">
                                        <h6>Bio</h6>
                                        <p class="text-muted"><?php echo $admin['bio']; ?></p>
                                    </div>
                                    <div class="mt-3">
                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#passwordModal">
                                            <i class="fas fa-key"></i> Change Password
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm ms-2">
                                            <i class="fas fa-shield-alt"></i> 2FA Settings
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Activity Log Card -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5>Recent Activity</h5>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <small class="text-muted">Just now</small>
                                            </div>
                                            <p class="mb-1">Logged in to the admin dashboard</p>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <small class="text-muted">Yesterday</small>
                                            </div>
                                            <p class="mb-1">Updated blog post #1245</p>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <small class="text-muted">3 days ago</small>
                                            </div>
                                            <p class="mb-1">Changed password</p>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <small class="text-muted">1 week ago</small>
                                            </div>
                                            <p class="mb-1">Added new service #456</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-footer">
                                    <a href="#" class="btn btn-sm btn-outline-secondary w-100">View All Activity</a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Profile Edit Card -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Edit Profile Information</h5>
                                </div>
                                <div class="card-body">
                                    <form action="admin-profile.php" method="post">
                                        <input type="hidden" name="action" value="update_profile">
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="fullName" class="form-label">Full Name</label>
                                                <input type="text" class="form-control" id="fullName" name="full_name" value="<?php echo $admin['name']; ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email" class="form-label">Email Address</label>
                                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $admin['email']; ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="phone" class="form-label">Phone Number</label>
                                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $admin['phone']; ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="department" class="form-label">Department</label>
                                                <select class="form-select" id="department" name="department">
                                                    <option value="Management" <?php echo ($admin['department'] == 'Management') ? 'selected' : ''; ?>>Management</option>
                                                    <option value="HR" <?php echo ($admin['department'] == 'HR') ? 'selected' : ''; ?>>HR</option>
                                                    <option value="Marketing" <?php echo ($admin['department'] == 'Marketing') ? 'selected' : ''; ?>>Marketing</option>
                                                    <option value="IT" <?php echo ($admin['department'] == 'IT') ? 'selected' : ''; ?>>IT</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="bio" class="form-label">Bio</label>
                                            <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo $admin['bio']; ?></textarea>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="timezone" class="form-label">Timezone</label>
                                                <select class="form-select" id="timezone" name="timezone">
                                                    <option value="UTC-8">Pacific Time (UTC-8)</option>
                                                    <option value="UTC-7">Mountain Time (UTC-7)</option>
                                                    <option value="UTC-6">Central Time (UTC-6)</option>
                                                    <option value="UTC-5" selected>Eastern Time (UTC-5)</option>
                                                    <option value="UTC+0">UTC</option>
                                                    <option value="UTC+1">Central European Time (UTC+1)</option>
                                                    <option value="UTC+2">Eastern European Time (UTC+2)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="language" class="form-label">Language</label>
                                                <select class="form-select" id="language" name="language">
                                                    <option value="en" selected>English</option>
                                                    <option value="es">Spanish</option>
                                                    <option value="fr">French</option>
                                                    <option value="de">German</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Notification Preferences</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="emailNotif" name="notifications[]" value="email" checked>
                                                <label class="form-check-label" for="emailNotif">
                                                    Email Notifications
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="smsNotif" name="notifications[]" value="sms">
                                                <label class="form-check-label" for="smsNotif">
                                                    SMS Notifications
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="browserNotif" name="notifications[]" value="browser" checked>
                                                <label class="form-check-label" for="browserNotif">
                                                    Browser Notifications
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Save Changes
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Security Settings Card -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5>Account Security</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <h6>Two-Factor Authentication</h6>
                                        <p class="text-muted">Add an extra layer of security to your account</p>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="twoFactorAuth">
                                            <label class="form-check-label" for="twoFactorAuth">Enable 2FA</label>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h6>Login Session</h6>
                                        <p class="text-muted">Last login: <?php echo $admin['last_login']; ?></p>
                                        <button class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-power-off"></i> End All Other Sessions
                                        </button>
                                    </div>
                                    
                                    <div>
                                        <h6>Account Activity Log</h6>
                                        <p class="text-muted">View a detailed log of all account activities</p>
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-history"></i> View Activity Log
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Avatar Modal -->
    <div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="avatarModalLabel">Change Profile Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin-profile.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload_avatar">
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <img src="<?php echo $admin['avatar']; ?>" class="avatar-preview" alt="Current avatar">
                        </div>
                        <div class="mb-3">
                            <label for="avatarFile" class="form-label">Upload New Image</label>
                            <input class="form-control" type="file" id="avatarFile" name="avatar_file" accept="image/*" required>
                            <div class="form-text">Recommended size: 300x300 pixels. Max file size: 2MB.</div>
                        </div>
                        <div class="avatar-options mb-3">
                            <label class="form-label">Or Choose From Defaults</label>
                            <div class="default-avatars">
                                <div class="row">
                                    <div class="col-3">
                                        <img src="assets/images/default-avatar-1.jpg" class="default-avatar" alt="Default avatar 1">
                                    </div>
                                    <div class="col-3">
                                        <img src="assets/images/default-avatar-2.jpg" class="default-avatar" alt="Default avatar 2">
                                    </div>
                                    <div class="col-3">
                                        <img src="assets/images/default-avatar-3.jpg" class="default-avatar" alt="Default avatar 3">
                                    </div>
                                    <div class="col-3">
                                        <img src="assets/images/default-avatar-4.jpg" class="default-avatar" alt="Default avatar 4">
                                    </div>
                                </div>
                            </div>
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

    <!-- Change Password Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin-profile.php" method="post">
                    <input type="hidden" name="action" value="change_password">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" required>
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="form-text password-strength-text">Password strength: Too weak</div>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                        </div>
                        <div class="password-requirements">
                            <p class="mb-2">Password must contain:</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-times-circle text-danger"></i> At least 8 characters</li>
                                <li><i class="fas fa-times-circle text-danger"></i> At least one uppercase letter</li>
                                <li><i class="fas fa-times-circle text-danger"></i> At least one number</li>
                                <li><i class="fas fa-times-circle text-danger"></i> At least one special character</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="changePasswordBtn" disabled>Change Password</button>
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
        
        // Password strength meter
        $('#newPassword').on('input', function() {
            const password = $(this).val();
            let strength = 0;
            
            // Check password length
            if (password.length >= 8) {
                strength += 25;
                $('.password-requirements li:nth-child(1) i').removeClass('fa-times-circle text-danger').addClass('fa-check-circle text-success');
            } else {
                $('.password-requirements li:nth-child(1) i').removeClass('fa-check-circle text-success').addClass('fa-times-circle text-danger');
            }
            
            // Check for uppercase letter
            if (/[A-Z]/.test(password)) {
                strength += 25;
                $('.password-requirements li:nth-child(2) i').removeClass('fa-times-circle text-danger').addClass('fa-check-circle text-success');
            } else {
                $('.password-requirements li:nth-child(2) i').removeClass('fa-check-circle text-success').addClass('fa-times-circle text-danger');
            }
            
            // Check for number
            if (/[0-9]/.test(password)) {
                strength += 25;
                $('.password-requirements li:nth-child(3) i').removeClass('fa-times-circle text-danger').addClass('fa-check-circle text-success');
            } else {
                $('.password-requirements li:nth-child(3) i').removeClass('fa-check-circle text-success').addClass('fa-times-circle text-danger');
            }
            
            // Check for special character
            if (/[^A-Za-z0-9]/.test(password)) {
                strength += 25;
                $('.password-requirements li:nth-child(4) i').removeClass('fa-times-circle text-danger').addClass('fa-check-circle text-success');
            } else {
                $('.password-requirements li:nth-child(4) i').removeClass('fa-check-circle text-success').addClass('fa-times-circle text-danger');
            }
            
            // Update progress bar
            $('.progress-bar').css('width', strength + '%');
            
            // Set color based on strength
            if (strength <= 25) {
                $('.progress-bar').removeClass('bg-warning bg-info bg-success').addClass('bg-danger');
                $('.password-strength-text').text('Password strength: Too weak');
            } else if (strength <= 50) {
                $('.progress-bar').removeClass('bg-danger bg-info bg-success').addClass('bg-warning');
                $('.password-strength-text').text('Password strength: Weak');
            } else if (strength <= 75) {
                $('.progress-bar').removeClass('bg-danger bg-warning bg-success').addClass('bg-info');
                $('.password-strength-text').text('Password strength: Medium');
            } else {
                $('.progress-bar').removeClass('bg-danger bg-warning bg-info').addClass('bg-success');
                $('.password-strength-text').text('Password strength: Strong');
            }
            
            // Enable/disable submit button based on password match
            checkPasswordMatch();
        });
        
        // Check password confirmation
        $('#confirmPassword').on('input', function() {
            checkPasswordMatch();
        });
        
        function checkPasswordMatch() {
            const password = $('#newPassword').val();
            const confirmPassword = $('#confirmPassword').val();
            
            if (password && confirmPassword && password === confirmPassword && password.length >= 8) {
                $('#changePasswordBtn').prop('disabled', false);
            } else {
                $('#changePasswordBtn').prop('disabled', true);
            }
        }
        
        // Default avatar selection
        $('.default-avatar').click(function() {
            $('.default-avatar').removeClass('selected');
            $(this).addClass('selected');
            $('.avatar-preview').attr('src', $(this).attr('src'));
            // In a real implementation, you would set a hidden input value
        });
        
        // Preview uploaded avatar
        $('#avatarFile').change(function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('.avatar-preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    </script>
</body>
</html>