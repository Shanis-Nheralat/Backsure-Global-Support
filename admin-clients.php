<?php
/**
 * Admin Clients Management
 * Secure interface for managing client accounts
 */

// Start session and check authentication
require_once 'session-check.php';

// Check if admin is logged in and has proper permissions
if (!check_user_access(['super_admin', 'admin', 'hr_manager'], 'admin-login.html')) {
    exit;
}

// Check specific permission for managing clients
if (!userHasPermission($_SESSION['user_id'], 'manage_users', true)) {
    header("Location: admin-dashboard.html?error=permission_denied");
    exit;
}

// Create CSRF token for forms and AJAX requests
$csrf_token = createCSRFToken();

// Get clients from database
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Query to get all clients with basic info
    $query = "
        SELECT u.id, u.name, u.email, u.username, u.status, u.created_at, u.last_login, 
               um.meta_value as company
        FROM users u
        LEFT JOIN user_meta um ON u.id = um.user_id AND um.meta_key = 'company_name'
        ORDER BY u.created_at DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $clients = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log('Error loading clients: ' . $e->getMessage());
    $error_message = 'Database error occurred. Please try again later.';
    $clients = [];
}

// Get counts for dashboard stats
try {
    $totalClients = count($clients);
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE status = 'active'");
    $stmt->execute();
    $activeClients = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE status = 'pending'");
    $stmt->execute();
    $pendingClients = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE status = 'blocked'");
    $stmt->execute();
    $blockedClients = $stmt->fetchColumn();
    
    // Get recent registrations (last 30 days)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stmt->execute();
    $recentRegistrations = $stmt->fetchColumn();
    
} catch (PDOException $e) {
    error_log('Error loading client stats: ' . $e->getMessage());
    $activeClients = $pendingClients = $blockedClients = $recentRegistrations = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clients | Backsure Global Support</title>
    <meta name="csrf-token" content="<?php echo $csrf_token; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin-styles.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="dashboard-container">
        <!-- Include sidebar navigation -->
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <?php include 'includes/admin-topnav.php'; ?>
            
            <!-- Main Dashboard Content -->
            <div class="dashboard-content">
                <div class="content-header">
                    <h1 class="content-title">Client Management</h1>
                    <div class="header-actions">
                        <button class="btn btn-primary" id="add-client-btn">
                            <i class="fas fa-plus"></i> Add New Client
                        </button>
                    </div>
                </div>
                
                <!-- Status Cards -->
                <div class="card-container">
                    <div class="card card-primary">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="card-title">Total Clients</h3>
                        <div class="card-value"><?php echo $totalClients; ?></div>
                        <div class="card-change">
                            <i class="fas fa-arrow-up"></i>
                            <span class="text-up"><?php echo $recentRegistrations; ?> new in 30 days</span>
                        </div>
                    </div>
                    
                    <div class="card card-success">
                        <div class="card-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <h3 class="card-title">Active Clients</h3>
                        <div class="card-value"><?php echo $activeClients; ?></div>
                        <div class="card-change">
                            <span><?php echo round(($activeClients / max(1, $totalClients)) * 100); ?>% of total</span>
                        </div>
                    </div>
                    
                    <div class="card card-warning">
                        <div class="card-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <h3 class="card-title">Pending Verification</h3>
                        <div class="card-value"><?php echo $pendingClients; ?></div>
                        <div class="card-change">
                            <span>Require email verification</span>
                        </div>
                    </div>
                    
                    <div class="card card-danger">
                        <div class="card-icon">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <h3 class="card-title">Blocked Clients</h3>
                        <div class="card-value"><?php echo $blockedClients; ?></div>
                        <div class="card-change">
                            <span><?php echo round(($blockedClients / max(1, $totalClients)) * 100); ?>% of total</span>
                        </div>
                    </div>
                </div>
                
                <!-- Client List -->
                <div class="dashboard-content">
                    <div class="content-header">
                        <h2 class="content-title">Client List</h2>
                        
                        <!-- Filters -->
                        <div class="client-filter">
                            <select id="status-filter" class="form-control">
                                <option value="all">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="blocked">Blocked</option>
                            </select>
                            <div class="search-bar">
                                <i class="fas fa-search"></i>
                                <input type="search" placeholder="Search clients...">
                            </div>
                        </div>
                    </div>
                    
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="clients-table data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Company</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($clients)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No clients found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($clients as $client): ?>
                                        <tr data-id="<?php echo $client['id']; ?>">
                                            <td class="client-name"><?php echo htmlspecialchars($client['name']); ?></td>
                                            <td class="client-email"><?php echo htmlspecialchars($client['email']); ?></td>
                                            <td class="client-company"><?php echo htmlspecialchars($client['company'] ?? ''); ?></td>
                                            <td>
                                                <span class="status-badge <?php echo $client['status']; ?>">
                                                    <?php echo ucfirst($client['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($client['created_at'])); ?></td>
                                            <td><?php echo $client['last_login'] ? date('M j, Y g:i A', strtotime($client['last_login'])) : 'Never'; ?></td>
                                            <td class="actions">
                                                <button class="btn btn-sm btn-info view-client-btn" data-id="<?php echo $client['id']; ?>" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <?php if ($client['status'] === 'pending'): ?>
                                                    <button class="btn btn-sm btn-warning send-verification-btn" 
                                                            data-id="<?php echo $client['id']; ?>" 
                                                            data-email="<?php echo htmlspecialchars($client['email']); ?>"
                                                            title="Resend Verification">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <div class="toggle-switch">
                                                    <input type="checkbox" id="status-<?php echo $client['id']; ?>" 
                                                           class="status-toggle" data-id="<?php echo $client['id']; ?>"
                                                           <?php echo $client['status'] === 'active' ? 'checked' : ''; ?>>
                                                    <label for="status-<?php echo $client['id']; ?>">
                                                        <span class="toggle-label"><?php echo $client['status'] === 'active' ? 'Active' : 'Blocked'; ?></span>
                                                    </label>
                                                </div>
                                                
                                                <?php if (userHasPermission($_SESSION['user_id'], 'manage_users', true)): ?>
                                                    <button class="btn btn-sm btn-danger delete-client-btn" data-id="<?php echo $client['id']; ?>" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="results-info">
                        Showing <?php echo count($clients); ?> of <?php echo count($clients); ?> clients
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <?php include 'includes/admin-footer.php'; ?>
        </div>
    </div>
    
    <!-- View Client Modal -->
    <div class="modal" id="view-client-modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-title">Client Details</h2>
            <div class="modal-body">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
    
    <!-- Add Client Modal -->
    <div class="modal" id="add-client-modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-title">Add New Client</h2>
            <div class="modal-body">
                <form id="add-client-form" action="ajax/add-client.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="company">Company Name</label>
                        <input type="text" id="company" name="company" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        <div class="password-strength">
                            <div class="password-strength-bar"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm-password">Confirm Password</label>
                        <input type="password" id="confirm-password" name="confirm-password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="pending">Pending Verification</option>
                            <option value="blocked">Blocked</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="send_welcome" checked>
                            Send welcome email
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary cancel-btn">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Client</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/admin-clients.js"></script>
    
    <script>
        // Initialize modals
        document.addEventListener('DOMContentLoaded', function() {
            // Add Client button
            document.getElementById('add-client-btn').addEventListener('click', function() {
                document.getElementById('add-client-modal').style.display = 'block';
            });
            
            // Close modals
            document.querySelectorAll('.modal .close, .modal .cancel-btn').forEach(el => {
                el.addEventListener('click', function() {
                    this.closest('.modal').style.display = 'none';
                });
            });
            
            // Close when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target.classList.contains('modal')) {
                    event.target.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>