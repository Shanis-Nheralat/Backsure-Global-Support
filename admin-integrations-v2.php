<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Increase limits
ini_set('memory_limit', '256M');
set_time_limit(60);

// Start session
session_start();

// Basic authentication (simplified for testing)
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['admin_logged_in'] = true; // For testing
}

// Get admin information from session
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin User';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'Administrator';

// Simplified mock data
$integrations = [
    'payment_gateways' => [
        [
            'id' => 'stripe',
            'name' => 'Stripe',
            'description' => 'Accept credit card payments securely with Stripe.',
            'logo' => 'assets/images/integrations/stripe-logo.png',
            'status' => 'active',
            'config' => [
                'mode' => 'test',
                'currency' => 'USD'
            ]
        ],
        [
            'id' => 'paypal',
            'name' => 'PayPal',
            'description' => 'Enable customers to pay via PayPal.',
            'logo' => 'assets/images/integrations/paypal-logo.png',
            'status' => 'inactive',
            'config' => [
                'mode' => 'sandbox',
                'currency' => 'USD'
            ]
        ]
    ],
    'email_marketing' => [
        [
            'id' => 'mailchimp',
            'name' => 'Mailchimp',
            'description' => 'Sync subscribers and send automated emails.',
            'logo' => 'assets/images/integrations/mailchimp-logo.png',
            'status' => 'active',
            'config' => [
                'enable_sync' => true,
                'sync_frequency' => 'daily'
            ]
        ]
    ]
];

$available_integrations = [
    [
        'id' => 'slack',
        'name' => 'Slack',
        'description' => 'Get notifications in your Slack workspace.',
        'logo' => 'assets/images/integrations/slack-logo.png',
        'category' => 'communication'
    ],
    [
        'id' => 'dropbox',
        'name' => 'Dropbox',
        'description' => 'Cloud storage integration with Dropbox.',
        'logo' => 'assets/images/integrations/dropbox-logo.png',
        'category' => 'file_storage'
    ]
];

$connection_logs = [
    [
        'integration' => 'Stripe',
        'action' => 'Test Connection',
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s'),
        'details' => 'Connection successful'
    ],
    [
        'integration' => 'Mailchimp',
        'action' => 'Sync Contacts',
        'status' => 'error',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'details' => 'API key invalid'
    ]
];

// Get active category/tab from URL or set defaults
$active_category = isset($_GET['category']) ? $_GET['category'] : 'payment_gateways';
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'integrations';

// Simplified form handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Redirect with success message
    header('Location: admin-integrations-v2.php?success=test');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Integrations | Backsure Global Support</title>
  <!-- Use CDN for external resources -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <style>
    /* Minimal styling */
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f8f9fc;
    }
    .admin-sidebar {
      width: 250px;
      background-color: #062767;
      color: white;
      position: fixed;
      left: 0;
      top: 0;
      height: 100vh;
    }
    .admin-main {
      margin-left: 250px;
      min-height: 100vh;
    }
    .integration-card {
      border: 1px solid #e3e6f0;
      transition: all 0.3s ease;
    }
    .integration-inactive {
      opacity: 0.7;
    }
    /* Simplified sidebar styles */
    .sidebar-nav ul {
      list-style: none;
      padding: 0;
    }
    .sidebar-nav ul li a {
      color: rgba(255, 255, 255, 0.8);
      display: flex;
      align-items: center;
      padding: 10px 20px;
      text-decoration: none;
    }
    .sidebar-nav ul li a i {
      width: 20px;
      margin-right: 10px;
    }
    .sidebar-nav ul li.active > a {
      background-color: #b19763;
    }
  </style>
</head>
<body>
  <div class="container-fluid p-0">
    <div class="row g-0">
      <!-- Simplified Sidebar -->
      <div class="col-auto">
        <div class="admin-sidebar">
          <div class="p-3 text-center">
            <h4 class="text-white">Admin Panel</h4>
          </div>
          <nav class="sidebar-nav">
            <ul>
              <li><a href="admin-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
              <li><a href="admin-services.php"><i class="fas fa-briefcase"></i> Services</a></li>
              <li class="active"><a href="admin-integrations-v2.php"><i class="fas fa-plug"></i> Integrations</a></li>
            </ul>
          </nav>
        </div>
      </div>
      
      <!-- Main Content -->
      <div class="col">
        <div class="admin-main">
          <div class="p-4">
            <h1>Integrations</h1>
            
            <!-- Success Message -->
            <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">
              <strong>Success!</strong> Operation completed successfully.
            </div>
            <?php endif; ?>
            
            <!-- Tabs -->
            <ul class="nav nav-tabs mt-4" id="integrationTabs" role="tablist">
              <li class="nav-item">
                <button class="nav-link <?php echo ($active_tab == 'integrations') ? 'active' : ''; ?>" 
                        id="integrations-tab" data-bs-toggle="tab" data-bs-target="#integrations">
                  Configured Integrations
                </button>
              </li>
              <li class="nav-item">
                <button class="nav-link <?php echo ($active_tab == 'available') ? 'active' : ''; ?>" 
                        id="available-tab" data-bs-toggle="tab" data-bs-target="#available">
                  Available Integrations
                </button>
              </li>
              <li class="nav-item">
                <button class="nav-link <?php echo ($active_tab == 'logs') ? 'active' : ''; ?>" 
                        id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs">
                  Connection Logs
                </button>
              </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content mt-3">
              <!-- Configured Integrations Tab -->
              <div class="tab-pane fade <?php echo ($active_tab == 'integrations') ? 'show active' : ''; ?>" id="integrations">
                <ul class="nav nav-pills mb-3">
                  <?php foreach (array_keys($integrations) as $category): ?>
                  <li class="nav-item">
                    <a class="nav-link <?php echo ($active_category == $category) ? 'active' : ''; ?>" 
                       href="#<?php echo $category; ?>" data-category="<?php echo $category; ?>">
                      <?php echo ucwords(str_replace('_', ' ', $category)); ?>
                    </a>
                  </li>
                  <?php endforeach; ?>
                </ul>
                
                <?php foreach ($integrations as $category => $category_integrations): ?>
                <div class="category-content <?php echo ($active_category == $category) ? 'd-block' : 'd-none'; ?>" id="<?php echo $category; ?>_content">
                  <div class="row">
                    <?php foreach ($category_integrations as $integration): ?>
                    <div class="col-md-6 mb-4">
                      <div class="card integration-card <?php echo ($integration['status'] == 'inactive') ? 'integration-inactive' : ''; ?>">
                        <div class="card-header d-flex justify-content-between align-items-center">
                          <h5 class="mb-0"><?php echo htmlspecialchars($integration['name']); ?></h5>
                          <span class="badge bg-<?php echo ($integration['status'] == 'active') ? 'success' : 'secondary'; ?>">
                            <?php echo ucfirst($integration['status']); ?>
                          </span>
                        </div>
                        <div class="card-body">
                          <p><?php echo htmlspecialchars($integration['description']); ?></p>
                        </div>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
              
              <!-- Available Integrations Tab -->
              <div class="tab-pane fade <?php echo ($active_tab == 'available') ? 'show active' : ''; ?>" id="available">
                <div class="row">
                  <?php foreach ($available_integrations as $integration): ?>
                  <div class="col-md-4 mb-4">
                    <div class="card h-100">
                      <div class="card-header">
                        <h5 class="mb-0"><?php echo htmlspecialchars($integration['name']); ?></h5>
                      </div>
                      <div class="card-body">
                        <p><?php echo htmlspecialchars($integration['description']); ?></p>
                      </div>
                      <div class="card-footer">
                        <button class="btn btn-primary">Install</button>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
              
              <!-- Logs Tab -->
              <div class="tab-pane fade <?php echo ($active_tab == 'logs') ? 'show active' : ''; ?>" id="logs">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Integration</th>
                        <th>Action</th>
                        <th>Status</th>
                        <th>Timestamp</th>
                        <th>Details</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($connection_logs as $log): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($log['integration']); ?></td>
                        <td><?php echo htmlspecialchars($log['action']); ?></td>
                        <td>
                          <span class="badge bg-<?php echo ($log['status'] == 'success') ? 'success' : 'danger'; ?>">
                            <?php echo ucfirst($log['status']); ?>
                          </span>
                        </td>
                        <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                        <td><?php echo htmlspecialchars($log['details']); ?></td>
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
  </div>
  
  <!-- JavaScript -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function() {
      // Category navigation
      $('.nav-pills .nav-link').click(function(e) {
        e.preventDefault();
        const category = $(this).data('category');
        
        // Update active nav link
        $('.nav-pills .nav-link').removeClass('active');
        $(this).addClass('active');
        
        // Show selected category content
        $('.category-content').removeClass('d-block').addClass('d-none');
        $(`#${category}_content`).removeClass('d-none').addClass('d-block');
      });
    });
  </script>
</body>
</html>