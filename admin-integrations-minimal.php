<?php
// Start session and enable error reporting
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Force login for testing (similar to what's already in your session)
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_username'] = 'Admin User';
$_SESSION['admin_role'] = 'admin';

// Minimal data structure
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
        ]
    ]
];

// Get active category/tab from URL
$active_category = isset($_GET['category']) ? $_GET['category'] : 'payment_gateways';
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'integrations';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Integrations | Minimal Version</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
  <div class="container my-5">
    <h1>Integrations (Minimal Version)</h1>
    <p>This is a minimal version of the integrations page to troubleshoot display issues.</p>
    
    <!-- Basic Tab Navigation -->
    <ul class="nav nav-tabs mt-4">
      <li class="nav-item">
        <button class="nav-link active" id="integrations-tab" data-bs-toggle="tab" data-bs-target="#integrations">
          Configured Integrations
        </button>
      </li>
    </ul>
    
    <!-- Tab Content -->
    <div class="tab-content mt-3">
      <div class="tab-pane fade show active" id="integrations">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <?php foreach ($integrations['payment_gateways'] as $integration): ?>
              <div class="col-md-6 mb-4">
                <div class="card">
                  <div class="card-header">
                    <h5><?php echo $integration['name']; ?></h5>
                  </div>
                  <div class="card-body">
                    <p><?php echo $integration['description']; ?></p>
                    <div>
                      <strong>Status:</strong> 
                      <span class="badge bg-success">Active</span>
                    </div>
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

  <!-- JavaScript Files -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
