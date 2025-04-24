<?php
// Start session and enable error reporting
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '256M'); // Increase memory limit

// Force login for testing (similar to what's already in your session)
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_username'] = 'Admin User';
$_SESSION['admin_role'] = 'admin';

// More complete but still limited data structure
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
                'api_key' => 'abc123def456-us1',
                'enable_sync' => true,
                'sync_frequency' => 'daily'
            ]
        ]
    ]
];

// Available integrations (limited set)
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

// Basic form handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Just redirect with a success message for testing
    header('Location: admin-integrations-intermediate.php?success=action_performed');
    exit;
}

// Get active category/tab from URL
$active_category = isset($_GET['category']) ? $_GET['category'] : 'payment_gateways';
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'integrations';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Integrations | Intermediate Version</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <style>
    /* Basic styling */
    .integration-card {
        border: 1px solid #e3e6f0;
        transition: all 0.3s ease;
    }
    
    .integration-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .integration-inactive {
        opacity: 0.7;
    }
    
    .integration-logo {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .integration-logo img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
  </style>
</head>
<body>
  <div class="container my-5">
    <h1>Integrations (Intermediate Version)</h1>
    <p>This version has more functionality but is still simplified for troubleshooting.</p>
    
    <!-- Success Message -->
    <?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> Your action was completed successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mt-4" id="integrationTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo ($active_tab == 'integrations') ? 'active' : ''; ?>" id="integrations-tab" data-bs-toggle="tab" data-bs-target="#integrations" type="button" role="tab">
                Configured Integrations
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo ($active_tab == 'available') ? 'active' : ''; ?>" id="available-tab" data-bs-toggle="tab" data-bs-target="#available" type="button" role="tab">
                Available Integrations
            </button>
        </li>
    </ul>
    
    <!-- Tab Content -->
    <div class="tab-content mt-3">
        <!-- Configured Integrations Tab -->
        <div class="tab-pane fade <?php echo ($active_tab == 'integrations') ? 'show active' : ''; ?>" id="integrations" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($active_category == 'payment_gateways') ? 'active' : ''; ?>" href="?category=payment_gateways">
                                <i class="fas fa-credit-card"></i> Payment Gateways
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($active_category == 'email_marketing') ? 'active' : ''; ?>" href="?category=email_marketing">
                                <i class="fas fa-envelope"></i> Email Marketing
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <!-- Integration Category Content -->
                    <?php foreach ($integrations as $category => $category_integrations): ?>
                    <div class="<?php echo ($active_category == $category) ? 'd-block' : 'd-none'; ?>" id="<?php echo $category; ?>_content">
                        <div class="row">
                            <?php foreach ($category_integrations as $integration): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card integration-card <?php echo ($integration['status'] == 'inactive') ? 'integration-inactive' : ''; ?>">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="integration-logo me-3">
                                                <img src="<?php echo $integration['logo']; ?>" alt="<?php echo $integration['name']; ?> Logo" onerror="this.src='https://via.placeholder.com/40'">
                                            </div>
                                            <h5 class="mb-0"><?php echo $integration['name']; ?></h5>
                                        </div>
                                        <div>
                                            <form action="admin-integrations-intermediate.php" method="post" class="d-inline">
                                                <input type="hidden" name="action" value="toggle_integration">
                                                <input type="hidden" name="integration_id" value="<?php echo $integration['id']; ?>">
                                                <input type="hidden" name="integration_category" value="<?php echo $category; ?>">
                                                <input type="hidden" name="status" value="<?php echo ($integration['status'] == 'active') ? 'inactive' : 'active'; ?>">
                                                <button type="submit" class="btn btn-sm <?php echo ($integration['status'] == 'active') ? 'btn-success' : 'btn-secondary'; ?>">
                                                    <?php echo ($integration['status'] == 'active') ? 'Active' : 'Inactive'; ?>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text"><?php echo $integration['description']; ?></p>
                                        
                                        <?php if($integration['status'] == 'active'): ?>
                                        <div class="mb-2">
                                            <strong>Status:</strong> 
                                            <span class="badge bg-success">Connected</span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer">
                                        <button type="button" class="btn btn-sm btn-primary configure-btn" data-id="<?php echo $integration['id']; ?>">
                                            <i class="fas fa-cog"></i> Configure
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Available Integrations Tab -->
        <div class="tab-pane fade <?php echo ($active_tab == 'available') ? 'show active' : ''; ?>" id="available" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Available Integrations</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach($available_integrations as $integration): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex align-items-center">
                                    <div class="integration-logo me-3">
                                        <img src="<?php echo $integration['logo']; ?>" alt="<?php echo $integration['name']; ?> Logo" onerror="this.src='https://via.placeholder.com/40'">
                                    </div>
                                    <h5 class="mb-0"><?php echo $integration['name']; ?></h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?php echo $integration['description']; ?></p>
                                </div>
                                <div class="card-footer text-center">
                                    <form action="admin-integrations-intermediate.php" method="post">
                                        <input type="hidden" name="action" value="install_integration">
                                        <input type="hidden" name="integration_id" value="<?php echo $integration['id']; ?>">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Install
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  <!-- Configuration Modal -->
  <div class="modal fade" id="configModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Configure Integration</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Configuration options would appear here.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function() {
        // Handle configure button click
        $('.configure-btn').click(function() {
            var modal = new bootstrap.Modal(document.getElementById('configModal'));
            modal.show();
        });
    });
  </script>
</body>
</html>
