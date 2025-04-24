<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Increase memory limit if needed
ini_set('memory_limit', '256M');

// Start session
session_start();

// Basic authentication (simplified for testing)
$_SESSION['admin_logged_in'] = true;
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin User';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'Administrator';

// Create simple mock data (reduced size)
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
                'test_publishable_key' => 'pk_test_sample',
                'currency' => 'USD'
            ]
        ]
    ]
];

$available_integrations = [
    [
        'id' => 'paypal',
        'name' => 'PayPal',
        'description' => 'Enable payments via PayPal.',
        'logo' => 'assets/images/integrations/paypal-logo.png',
        'category' => 'payment_gateways'
    ]
];

$connection_logs = [
    [
        'integration' => 'Stripe',
        'action' => 'Test',
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s'),
        'details' => 'Test log entry'
    ]
];

// Set active states
$active_category = 'payment_gateways';
$active_tab = 'integrations';

// Check for form submissions - simplified version
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    echo "<p>Action submitted: $action</p>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Integrations Debug</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
  <div class="container mt-4">
    <h1>Integrations Management (Debug Version)</h1>
    <p>Logged in as: <?php echo htmlspecialchars($admin_username); ?></p>
    
    <!-- Simplified Tabs -->
    <ul class="nav nav-tabs" id="integrationTabs" role="tablist">
      <li class="nav-item">
        <button class="nav-link active" id="integrations-tab" data-bs-toggle="tab" data-bs-target="#integrations">
          Configured Integrations
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link" id="available-tab" data-bs-toggle="tab" data-bs-target="#available">
          Available Integrations
        </button>
      </li>
    </ul>
    
    <!-- Tab Content -->
    <div class="tab-content mt-3">
      <div class="tab-pane fade show active" id="integrations">
        <div class="row">
          <?php foreach ($integrations['payment_gateways'] as $integration): ?>
          <div class="col-md-6">
            <div class="card mb-4">
              <div class="card-header">
                <h5><?php echo htmlspecialchars($integration['name']); ?></h5>
              </div>
              <div class="card-body">
                <p><?php echo htmlspecialchars($integration['description']); ?></p>
                <?php if($integration['status'] == 'active'): ?>
                <div>
                  <strong>Status:</strong> Active
                </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      
      <div class="tab-pane fade" id="available">
        <div class="row">
          <?php foreach ($available_integrations as $integration): ?>
          <div class="col-md-6">
            <div class="card mb-4">
              <div class="card-header">
                <h5><?php echo htmlspecialchars($integration['name']); ?></h5>
              </div>
              <div class="card-body">
                <p><?php echo htmlspecialchars($integration['description']); ?></p>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>