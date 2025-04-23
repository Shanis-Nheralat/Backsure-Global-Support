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

// Mock data for integrations
$integrations = [
    // Payment Gateways
    'payment_gateways' => [
        [
            'id' => 'stripe',
            'name' => 'Stripe',
            'description' => 'Accept credit card payments securely with Stripe.',
            'logo' => 'assets/images/integrations/stripe-logo.png',
            'status' => 'active',
            'config' => [
                'mode' => 'test', // 'test' or 'live'
                'test_publishable_key' => 'pk_test_51Abcde...',
                'test_secret_key' => 'sk_test_51Abcde...',
                'live_publishable_key' => '',
                'live_secret_key' => '',
                'webhook_secret' => 'whsec_...',
                'currency' => 'USD',
                'payment_methods' => ['card', 'apple_pay', 'google_pay']
            ]
        ],
        [
            'id' => 'paypal',
            'name' => 'PayPal',
            'description' => 'Enable customers to pay via PayPal.',
            'logo' => 'assets/images/integrations/paypal-logo.png',
            'status' => 'inactive',
            'config' => [
                'mode' => 'sandbox', // 'sandbox' or 'live'
                'sandbox_client_id' => '',
                'sandbox_client_secret' => '',
                'live_client_id' => '',
                'live_client_secret' => '',
                'currency' => 'USD',
                'webhook_id' => ''
            ]
        ]
    ],
    
    // Email Marketing
    'email_marketing' => [
        [
            'id' => 'mailchimp',
            'name' => 'Mailchimp',
            'description' => 'Sync subscribers and send automated emails.',
            'logo' => 'assets/images/integrations/mailchimp-logo.png',
            'status' => 'active',
            'config' => [
                'api_key' => 'abc123def456-us1',
                'server_prefix' => 'us1',
                'default_list_id' => 'a1b2c3d4e5',
                'enable_sync' => true,
                'sync_frequency' => 'daily', // 'realtime', 'hourly', 'daily'
                'double_opt_in' => true
            ]
        ],
        [
            'id' => 'sendgrid',
            'name' => 'SendGrid',
            'description' => 'Email delivery and marketing campaigns.',
            'logo' => 'assets/images/integrations/sendgrid-logo.png',
            'status' => 'inactive',
            'config' => [
                'api_key' => '',
                'from_email' => '',
                'from_name' => '',
                'contact_list_id' => '',
                'enable_tracking' => true
            ]
        ]
    ],
    
    // CRM Systems
    'crm' => [
        [
            'id' => 'hubspot',
            'name' => 'HubSpot',
            'description' => 'Sync contacts and leads with HubSpot CRM.',
            'logo' => 'assets/images/integrations/hubspot-logo.png',
            'status' => 'inactive',
            'config' => [
                'api_key' => '',
                'portal_id' => '',
                'sync_contacts' => true,
                'sync_deals' => true,
                'deal_pipeline_id' => '',
                'lead_status_property' => 'lifecyclestage',
                'sync_frequency' => 'daily' // 'realtime', 'hourly', 'daily'
            ]
        ],
        [
            'id' => 'salesforce',
            'name' => 'Salesforce',
            'description' => 'Connect with Salesforce for contact and opportunity management.',
            'logo' => 'assets/images/integrations/salesforce-logo.png',
            'status' => 'inactive',
            'config' => [
                'client_id' => '',
                'client_secret' => '',
                'username' => '',
                'password' => '',
                'security_token' => '',
                'instance_url' => '',
                'sync_contacts' => true,
                'sync_opportunities' => true,
                'sync_frequency' => 'daily' // 'realtime', 'hourly', 'daily'
            ]
        ]
    ],
    
    // Analytics
    'analytics' => [
        [
            'id' => 'google_analytics',
            'name' => 'Google Analytics',
            'description' => 'Track website traffic and user behavior.',
            'logo' => 'assets/images/integrations/google-analytics-logo.png',
            'status' => 'active',
            'config' => [
                'tracking_id' => 'UA-123456789-1',
                'enable_ip_anonymization' => true,
                'enable_demographics' => true,
                'enable_enhanced_link_attribution' => true,
                'exclude_admin_users' => true
            ]
        ],
        [
            'id' => 'facebook_pixel',
            'name' => 'Facebook Pixel',
            'description' => 'Track conversions and optimize Facebook ads.',
            'logo' => 'assets/images/integrations/facebook-pixel-logo.png',
            'status' => 'inactive',
            'config' => [
                'pixel_id' => '',
                'enable_advanced_matching' => false,
                'track_pageviews' => true,
                'track_form_submissions' => true
            ]
        ]
    ],
    
    // Chat & Support
    'chat_support' => [
        [
            'id' => 'zendesk',
            'name' => 'Zendesk',
            'description' => 'Customer support and ticketing system.',
            'logo' => 'assets/images/integrations/zendesk-logo.png',
            'status' => 'inactive',
            'config' => [
                'subdomain' => '',
                'email' => '',
                'api_token' => '',
                'widget_key' => '',
                'enable_chat' => true,
                'enable_tickets' => true
            ]
        ],
        [
            'id' => 'intercom',
            'name' => 'Intercom',
            'description' => 'Live chat and customer messaging platform.',
            'logo' => 'assets/images/integrations/intercom-logo.png',
            'status' => 'inactive',
            'config' => [
                'app_id' => '',
                'api_key' => '',
                'access_token' => '',
                'identity_verification_secret' => '',
                'enable_identity_verification' => true
            ]
        ]
    ],
    
    // Social Media
    'social_media' => [
        [
            'id' => 'facebook',
            'name' => 'Facebook',
            'description' => 'Facebook page integration for social sharing.',
            'logo' => 'assets/images/integrations/facebook-logo.png',
            'status' => 'inactive',
            'config' => [
                'app_id' => '',
                'app_secret' => '',
                'page_id' => '',
                'enable_sharing' => true,
                'enable_comments' => false
            ]
        ],
        [
            'id' => 'twitter',
            'name' => 'Twitter',
            'description' => 'Twitter integration for social sharing.',
            'logo' => 'assets/images/integrations/twitter-logo.png',
            'status' => 'inactive',
            'config' => [
                'api_key' => '',
                'api_secret' => '',
                'access_token' => '',
                'access_token_secret' => '',
                'enable_sharing' => true
            ]
        ]
    ],
    
    // File Storage
    'file_storage' => [
        [
            'id' => 'aws_s3',
            'name' => 'Amazon S3',
            'description' => 'Cloud storage for files and media.',
            'logo' => 'assets/images/integrations/aws-s3-logo.png',
            'status' => 'inactive',
            'config' => [
                'access_key' => '',
                'secret_key' => '',
                'region' => 'us-east-1',
                'bucket' => '',
                'use_path_style_endpoint' => false,
                'use_for_media' => true,
                'use_for_backups' => true
            ]
        ],
        [
            'id' => 'google_drive',
            'name' => 'Google Drive',
            'description' => 'Cloud storage integration with Google Drive.',
            'logo' => 'assets/images/integrations/google-drive-logo.png',
            'status' => 'inactive',
            'config' => [
                'client_id' => '',
                'client_secret' => '',
                'refresh_token' => '',
                'folder_id' => '',
                'use_for_media' => false,
                'use_for_backups' => true
            ]
        ]
    ]
];

// Mock data for available integrations (not yet installed)
$available_integrations = [
    [
        'id' => 'slack',
        'name' => 'Slack',
        'description' => 'Get notifications and updates in your Slack workspace.',
        'logo' => 'assets/images/integrations/slack-logo.png',
        'category' => 'communication'
    ],
    [
        'id' => 'dropbox',
        'name' => 'Dropbox',
        'description' => 'Cloud storage integration with Dropbox.',
        'logo' => 'assets/images/integrations/dropbox-logo.png',
        'category' => 'file_storage'
    ],
    [
        'id' => 'zapier',
        'name' => 'Zapier',
        'description' => 'Connect with 3,000+ apps through Zapier automation.',
        'logo' => 'assets/images/integrations/zapier-logo.png',
        'category' => 'automation'
    ],
    [
        'id' => 'square',
        'name' => 'Square',
        'description' => 'Process payments with Square.',
        'logo' => 'assets/images/integrations/square-logo.png',
        'category' => 'payment_gateways'
    ],
    [
        'id' => 'zoho_crm',
        'name' => 'Zoho CRM',
        'description' => 'Sync contacts and leads with Zoho CRM.',
        'logo' => 'assets/images/integrations/zoho-crm-logo.png',
        'category' => 'crm'
    ],
    [
        'id' => 'constant_contact',
        'name' => 'Constant Contact',
        'description' => 'Email marketing integration with Constant Contact.',
        'logo' => 'assets/images/integrations/constant-contact-logo.png',
        'category' => 'email_marketing'
    ]
];

// Mock connection logs
$connection_logs = [
    [
        'integration' => 'Stripe',
        'action' => 'API Request: Create Payment Intent',
        'status' => 'success',
        'timestamp' => '2023-11-10 14:23:45',
        'details' => 'Successfully created payment intent for $125.00'
    ],
    [
        'integration' => 'Mailchimp',
        'action' => 'Contact Sync',
        'status' => 'success',
        'timestamp' => '2023-11-10 13:00:02',
        'details' => 'Successfully synchronized 23 contacts'
    ],
    [
        'integration' => 'Mailchimp',
        'action' => 'Add New Subscriber',
        'status' => 'success',
        'timestamp' => '2023-11-10 11:45:18',
        'details' => 'Added john.doe@example.com to list "Newsletter Subscribers"'
    ],
    [
        'integration' => 'Google Analytics',
        'action' => 'Event Tracking',
        'status' => 'success',
        'timestamp' => '2023-11-10 10:30:45',
        'details' => 'Tracked 156 pageview events'
    ],
    [
        'integration' => 'HubSpot',
        'action' => 'API Connection Test',
        'status' => 'error',
        'timestamp' => '2023-11-09 16:15:30',
        'details' => 'API key validation failed: Invalid API key'
    ],
    [
        'integration' => 'Stripe',
        'action' => 'Webhook Received',
        'status' => 'success',
        'timestamp' => '2023-11-09 15:22:17',
        'details' => 'Processed payment.succeeded event for invoice #INV-2023-1045'
    ]
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Process integration update
    if ($action === 'update_integration') {
        $integration_id = isset($_POST['integration_id']) ? $_POST['integration_id'] : '';
        $integration_category = isset($_POST['integration_category']) ? $_POST['integration_category'] : '';
        
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-integrations.php?success=updated&category=' . $integration_category);
        exit;
    }
    
    // Process integration activation/deactivation
    if ($action === 'toggle_integration') {
        $integration_id = isset($_POST['integration_id']) ? $_POST['integration_id'] : '';
        $integration_category = isset($_POST['integration_category']) ? $_POST['integration_category'] : '';
        $new_status = isset($_POST['status']) ? $_POST['status'] : '';
        
        // In a real implementation, update the integration status in the database
        // For now, just redirect with success message
        if ($new_status === 'active') {
            header('Location: admin-integrations.php?success=activated&category=' . $integration_category);
        } else {
            header('Location: admin-integrations.php?success=deactivated&category=' . $integration_category);
        }
        exit;
    }
    
    // Process new integration installation
    if ($action === 'install_integration') {
        $integration_id = isset($_POST['integration_id']) ? $_POST['integration_id'] : '';
        
        // In a real implementation, set up the new integration in the database
        // For now, just redirect with success message
        header('Location: admin-integrations.php?success=installed');
        exit;
    }
    
    // Process connection test
    if ($action === 'test_connection') {
        $integration_id = isset($_POST['integration_id']) ? $_POST['integration_id'] : '';
        $integration_category = isset($_POST['integration_category']) ? $_POST['integration_category'] : '';
        
        // In a real implementation, test the API connection
        // For now, just redirect with success message
        header('Location: admin-integrations.php?success=connection_tested&category=' . $integration_category);
        exit;
    }
    
    // Process clearing connection logs
    if ($action === 'clear_logs') {
        // In a real implementation, clear logs from the database
        // For now, just redirect with success message
        header('Location: admin-integrations.php?success=logs_cleared&tab=logs');
        exit;
    }
}

// Get active category/tab from URL
$active_category = isset($_GET['category']) ? $_GET['category'] : 'payment_gateways';
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'integrations';

// Function to get integration by ID and category
function getIntegrationByIdAndCategory($id, $category, $integrations) {
    if (isset($integrations[$category])) {
        foreach ($integrations[$category] as $integration) {
            if ($integration['id'] === $id) {
                return $integration;
            }
        }
    }
    return null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integrations - Backsure Global Support</title>
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
                            <li><a href="admin-seo.php"><i class="fas fa-search"></i> SEO Settings</a></li>
                            <li><a href="admin-integrations.php" class="active"><i class="fas fa-plug"></i> Integrations</a></li>
                            <li><a href="admin-general.php"><i class="fas fa-sliders-h"></i> General Settings</a></li>
                            <li><a href="admin-appearance.php"><i class="fas fa-palette"></i> Appearance</a></li>
                            <li><a href="admin-backup.php"><i class="fas fa-database"></i> Backup & Restore</a></li>
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
                    <h1>Integrations</h1>
                    <div class="breadcrumb">
                        <a href="admin-dashboard.php">Dashboard</a> / <span>Integrations</span>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php if($_GET['success'] == 'updated'): ?>
                    <strong>Success!</strong> Integration settings have been updated.
                    <?php elseif($_GET['success'] == 'activated'): ?>
                    <strong>Success!</strong> Integration has been activated.
                    <?php elseif($_GET['success'] == 'deactivated'): ?>
                    <strong>Success!</strong> Integration has been deactivated.
                    <?php elseif($_GET['success'] == 'installed'): ?>
                    <strong>Success!</strong> New integration has been installed.
                    <?php elseif($_GET['success'] == 'connection_tested'): ?>
                    <strong>Success!</strong> Connection test was successful.
                    <?php elseif($_GET['success'] == 'logs_cleared'): ?>
                    <strong>Success!</strong> Connection logs have been cleared.
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php if($_GET['error'] == 'connection_failed'): ?>
                    <strong>Error!</strong> Connection test failed. Please check your credentials.
                    <?php else: ?>
                    <strong>Error!</strong> There was a problem processing your request.
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <!-- Main Content -->
                <div class="content-body">
                    <!-- Tabs navigation -->
                    <ul class="nav nav-tabs" id="integrationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo ($active_tab == 'integrations') ? 'active' : ''; ?>" id="integrations-tab" data-bs-toggle="tab" data-bs-target="#integrations" type="button" role="tab" aria-controls="integrations" aria-selected="<?php echo ($active_tab == 'integrations') ? 'true' : 'false'; ?>">
                                Configured Integrations
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo ($active_tab == 'available') ? 'active' : ''; ?>" id="available-tab" data-bs-toggle="tab" data-bs-target="#available" type="button" role="tab" aria-controls="available" aria-selected="<?php echo ($active_tab == 'available') ? 'true' : 'false'; ?>">
                                Available Integrations
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo ($active_tab == 'logs') ? 'active' : ''; ?>" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs" type="button" role="tab" aria-controls="logs" aria-selected="<?php echo ($active_tab == 'logs') ? 'true' : 'false'; ?>">
                                Connection Logs
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content" id="integrationTabsContent">
                        <!-- Configured Integrations Tab -->
                        <div class="tab-pane fade <?php echo ($active_tab == 'integrations') ? 'show active' : ''; ?>" id="integrations" role="tabpanel" aria-labelledby="integrations-tab">
                            <div class="card">
                                <div class="card-header">
                                    <ul class="nav nav-pills integration-category-nav">
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($active_category == 'payment_gateways') ? 'active' : ''; ?>" href="#payment_gateways" data-category="payment_gateways">
                                                <i class="fas fa-credit-card"></i> Payment Gateways
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($active_category == 'email_marketing') ? 'active' : ''; ?>" href="#email_marketing" data-category="email_marketing">
                                                <i class="fas fa-envelope"></i> Email Marketing
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($active_category == 'crm') ? 'active' : ''; ?>" href="#crm" data-category="crm">
                                                <i class="fas fa-users"></i> CRM Systems
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($active_category == 'analytics') ? 'active' : ''; ?>" href="#analytics" data-category="analytics">
                                                <i class="fas fa-chart-bar"></i> Analytics
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($active_category == 'chat_support') ? 'active' : ''; ?>" href="#chat_support" data-category="chat_support">
                                                <i class="fas fa-comments"></i> Chat & Support
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($active_category == 'social_media') ? 'active' : ''; ?>" href="#social_media" data-category="social_media">
                                                <i class="fas fa-share-alt"></i> Social Media
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($active_category == 'file_storage') ? 'active' : ''; ?>" href="#file_storage" data-category="file_storage">
                                                <i class="fas fa-cloud-upload-alt"></i> File Storage
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <!-- Integration Category Content -->
                                    <?php foreach ($integrations as $category => $category_integrations): ?>
                                    <div class="integration-category-content <?php echo ($active_category == $category) ? 'd-block' : 'd-none'; ?>" id="<?php echo $category; ?>_content">
                                        <div class="row">
                                            <?php foreach ($category_integrations as $integration): ?>
                                            <div class="col-md-6 mb-4">
                                                <div class="card h-100 integration-card <?php echo ($integration['status'] == 'inactive') ? 'integration-inactive' : ''; ?>">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <div class="integration-logo me-3">
                                                                <img src="<?php echo $integration['logo']; ?>" alt="<?php echo $integration['name']; ?> Logo">
                                                            </div>
                                                            <h5 class="mb-0"><?php echo $integration['name']; ?></h5>
                                                        </div>
                                                        <div class="integration-status">
                                                            <form action="admin-integrations.php" method="post" class="d-inline">
                                                                <input type="hidden" name="action" value="toggle_integration">
                                                                <input type="hidden" name="integration_id" value="<?php echo $integration['id']; ?>">
                                                                <input type="hidden" name="integration_category" value="<?php echo $category; ?>">
                                                                <input type="hidden" name="status" value="<?php echo ($integration['status'] == 'active') ? 'inactive' : 'active'; ?>">
                                                                <button type="submit" class="btn btn-sm <?php echo ($integration['status'] == 'active') ? 'btn-success active' : 'btn-secondary'; ?> status-toggle">
                                                                    <?php echo ($integration['status'] == 'active') ? 'Active' : 'Inactive'; ?>
                                                                </button>
                                                            </form>
                                                            <button type="button" class="btn btn-sm btn-outline-primary ms-2 configure-integration" data-integration-id="<?php echo $integration['id']; ?>" data-category="<?php echo $category; ?>">
                                                                <i class="fas fa-cog"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <p class="card-text"><?php echo $integration['description']; ?></p>
                                                        
                                                        <?php if($integration['status'] == 'active'): ?>
                                                        <div class="integration-details">
                                                            <div class="mb-2">
                                                                <strong>Status:</strong> 
                                                                <span class="badge bg-success">Connected</span>
                                                            </div>
                                                            
                                                            <?php if($category == 'payment_gateways'): ?>
                                                            <div class="mb-2">
                                                                <strong>Mode:</strong> 
                                                                <span class="badge bg-<?php echo ($integration['config']['mode'] == 'test' || $integration['config']['mode'] == 'sandbox') ? 'warning' : 'info'; ?>">
                                                                    <?php echo ucfirst($integration['config']['mode']); ?>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <strong>Currency:</strong> <?php echo $integration['config']['currency']; ?>
                                                            </div>
                                                            <?php endif; ?>
                                                            
                                                            <?php if($category == 'email_marketing'): ?>
                                                            <div class="mb-2">
                                                                <strong>Sync:</strong> 
                                                                <?php if(isset($integration['config']['enable_sync']) && $integration['config']['enable_sync']): ?>
                                                                <span class="badge bg-success">Enabled</span>
                                                                (<?php echo ucfirst($integration['config']['sync_frequency']); ?>)
                                                                <?php else: ?>
                                                                <span class="badge bg-secondary">Disabled</span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php endif; ?>
                                                            
                                                            <?php if($category == 'analytics'): ?>
                                                            <div class="mb-2">
                                                                <strong>Tracking ID:</strong> <?php echo $integration['config']['tracking_id']; ?>
                                                            </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="card-footer bg-transparent">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <form action="admin-integrations.php" method="post" class="d-inline">
                                                                <input type="hidden" name="action" value="test_connection">
                                                                <input type="hidden" name="integration_id" value="<?php echo $integration['id']; ?>">
                                                                <input type="hidden" name="integration_category" value="<?php echo $category; ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-info" <?php echo ($integration['status'] == 'inactive') ? 'disabled' : ''; ?>>
                                                                    <i class="fas fa-sync-alt"></i> Test Connection
                                                                </button>
                                                            </form>
                                                            <a href="#" class="text-primary documentation-link" data-integration="<?php echo $integration['name']; ?>">
                                                                <i class="fas fa-book"></i> Documentation
                                                            </a>
                                                        </div>
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
                        <div class="tab-pane fade <?php echo ($active_tab == 'available') ? 'show active' : ''; ?>" id="available" role="tabpanel" aria-labelledby="available-tab">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3>Available Integrations</h3>
                                    <div class="input-group" style="max-width: 300px;">
                                        <input type="text" class="form-control" id="searchIntegrations" placeholder="Search integrations...">
                                        <button class="btn btn-outline-secondary" type="button">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach($available_integrations as $integration): ?>
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100 integration-card available-integration" data-category="<?php echo $integration['category']; ?>">
                                                <div class="card-header d-flex align-items-center">
                                                    <div class="integration-logo me-3">
                                                        <img src="<?php echo $integration['logo']; ?>" alt="<?php echo $integration['name']; ?> Logo">
                                                    </div>
                                                    <h5 class="mb-0"><?php echo $integration['name']; ?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text"><?php echo $integration['description']; ?></p>
                                                </div>
                                                <div class="card-footer bg-transparent text-center">
                                                    <form action="admin-integrations.php" method="post">
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

                        <!-- Connection Logs Tab -->
                        <div class="tab-pane fade <?php echo ($active_tab == 'logs') ? 'show active' : ''; ?>" id="logs" role="tabpanel" aria-labelledby="logs-tab">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3>Connection Logs</h3>
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary me-2" id="refreshLogs">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                        <form action="admin-integrations.php" method="post" class="d-inline">
                                            <input type="hidden" name="action" value="clear_logs">
                                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to clear all logs? This cannot be undone.')">
                                                <i class="fas fa-trash"></i> Clear All Logs
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="card-body">
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
                                                <?php foreach($connection_logs as $log): ?>
                                                <tr>
                                                    <td><?php echo $log['integration']; ?></td>
                                                    <td><?php echo $log['action']; ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo ($log['status'] == 'success') ? 'success' : 'danger'; ?>">
                                                            <?php echo ucfirst($log['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo $log['timestamp']; ?></td>
                                                    <td><?php echo $log['details']; ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-info-circle"></i> Showing most recent 100 log entries. Logs are automatically purged after 30 days.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Integration Configuration Modal -->
    <div class="modal fade" id="configurationModal" tabindex="-1" aria-labelledby="configurationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="configurationModalLabel">Configure Integration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Configuration form will be loaded dynamically -->
                    <div id="configuration-content">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading configuration...</p>
                        </div>
                    </div>
                </div>
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
        
        // Integration category navigation
        $('.integration-category-nav .nav-link').click(function(e) {
            e.preventDefault();
            const category = $(this).data('category');
            
            // Update active nav link
            $('.integration-category-nav .nav-link').removeClass('active');
            $(this).addClass('active');
            
            // Show selected category content
            $('.integration-category-content').removeClass('d-block').addClass('d-none');
            $(`#${category}_content`).removeClass('d-none').addClass('d-block');
            
            // Update URL to maintain state on page refresh
            const url = new URL(window.location.href);
            url.searchParams.set('category', category);
            url.searchParams.set('tab', 'integrations');
            window.history.replaceState({}, '', url);
        });
        
        // Configure integration button click
        $('.configure-integration').click(function() {
            const integrationId = $(this).data('integration-id');
            const category = $(this).data('category');
            
            // In a real implementation, you would load the configuration form via AJAX
            // For this example, we'll dynamically generate the form based on our mock data
            const modal = $('#configurationModal');
            
            modal.find('.modal-title').text(`Configure ${getIntegrationName(integrationId, category)}`);
            
            // Load the appropriate configuration form
            loadConfigurationForm(integrationId, category);
            
            modal.modal('show');
        });
        
        // Helper function to get integration name
        function getIntegrationName(id, category) {
            <?php
            // Output JS object of integrations for client-side use
            echo "const integrations = " . json_encode($integrations) . ";\n";
            ?>
            
            const categoryIntegrations = integrations[category] || [];
            const integration = categoryIntegrations.find(item => item.id === id);
            return integration ? integration.name : 'Integration';
        }
        
        // Helper function to load configuration form
        function loadConfigurationForm(id, category) {
            const configContent = $('#configuration-content');
            
            // In a real implementation, this would be loaded via AJAX
            // For this example, we'll generate the form based on the category
            
            let formHtml = `
                <form action="admin-integrations.php" method="post" id="integration-config-form">
                    <input type="hidden" name="action" value="update_integration">
                    <input type="hidden" name="integration_id" value="${id}">
                    <input type="hidden" name="integration_category" value="${category}">
            `;
            
            // Add category-specific fields
            switch(category) {
                case 'payment_gateways':
                    formHtml += getPaymentGatewayForm(id);
                    break;
                case 'email_marketing':
                    formHtml += getEmailMarketingForm(id);
                    break;
                case 'analytics':
                    formHtml += getAnalyticsForm(id);
                    break;
                case 'crm':
                    formHtml += getCrmForm(id);
                    break;
                case 'chat_support':
                    formHtml += getChatSupportForm(id);
                    break;
                case 'social_media':
                    formHtml += getSocialMediaForm(id);
                    break;
                case 'file_storage':
                    formHtml += getFileStorageForm(id);
                    break;
                default:
                    formHtml += `<p>No configuration options available for this integration.</p>`;
            }
            
            formHtml += `
                <div class="text-end mt-4">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
                </form>
            `;
            
            configContent.html(formHtml);
        }
        
        // Helper function to get payment gateway configuration form
        function getPaymentGatewayForm(id) {
            // Find the integration data
            const integration = integrations.payment_gateways.find(item => item.id === id) || { config: {} };
            const config = integration.config;
            
            return `
                <div class="mb-3">
                    <label class="form-label">Mode</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="mode" id="modeTest" value="test" ${config.mode === 'test' || config.mode === 'sandbox' ? 'checked' : ''}>
                        <label class="form-check-label" for="modeTest">
                            Test Mode
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="mode" id="modeLive" value="live" ${config.mode === 'live' ? 'checked' : ''}>
                        <label class="form-check-label" for="modeLive">
                            Live Mode
                        </label>
                    </div>
                </div>
                
                <h5 class="mb-3">Test Credentials</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="testPublishableKey" class="form-label">Test Publishable Key</label>
                        <input type="text" class="form-control" id="testPublishableKey" name="test_publishable_key" value="${config.test_publishable_key || ''}">
                    </div>
                    <div class="col-md-6">
                        <label for="testSecretKey" class="form-label">Test Secret Key</label>
                        <input type="password" class="form-control" id="testSecretKey" name="test_secret_key" value="${config.test_secret_key || ''}">
                    </div>
                </div>
                
                <h5 class="mb-3">Live Credentials</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="livePublishableKey" class="form-label">Live Publishable Key</label>
                        <input type="text" class="form-control" id="livePublishableKey" name="live_publishable_key" value="${config.live_publishable_key || ''}">
                    </div>
                    <div class="col-md-6">
                        <label for="liveSecretKey" class="form-label">Live Secret Key</label>
                        <input type="password" class="form-control" id="liveSecretKey" name="live_secret_key" value="${config.live_secret_key || ''}">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="webhookSecret" class="form-label">Webhook Secret</label>
                    <input type="password" class="form-control" id="webhookSecret" name="webhook_secret" value="${config.webhook_secret || ''}">
                </div>
                
                <div class="mb-3">
                    <label for="currency" class="form-label">Default Currency</label>
                    <select class="form-select" id="currency" name="currency">
                        <option value="USD" ${config.currency === 'USD' ? 'selected' : ''}>USD - US Dollar</option>
                        <option value="EUR" ${config.currency === 'EUR' ? 'selected' : ''}>EUR - Euro</option>
                        <option value="GBP" ${config.currency === 'GBP' ? 'selected' : ''}>GBP - British Pound</option>
                        <option value="CAD" ${config.currency === 'CAD' ? 'selected' : ''}>CAD - Canadian Dollar</option>
                        <option value="AUD" ${config.currency === 'AUD' ? 'selected' : ''}>AUD - Australian Dollar</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Payment Methods</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="methodCard" name="payment_methods[]" value="card" ${config.payment_methods && config.payment_methods.includes('card') ? 'checked' : ''}>
                        <label class="form-check-label" for="methodCard">
                            Credit/Debit Cards
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="methodApplePay" name="payment_methods[]" value="apple_pay" ${config.payment_methods && config.payment_methods.includes('apple_pay') ? 'checked' : ''}>
                        <label class="form-check-label" for="methodApplePay">
                            Apple Pay
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="methodGooglePay" name="payment_methods[]" value="google_pay" ${config.payment_methods && config.payment_methods.includes('google_pay') ? 'checked' : ''}>
                        <label class="form-check-label" for="methodGooglePay">
                            Google Pay
                        </label>
                    </div>
                </div>
            `;
        }
        
        // Helper function to get email marketing configuration form
        function getEmailMarketingForm(id) {
            // Find the integration data
            const integration = integrations.email_marketing.find(item => item.id === id) || { config: {} };
            const config = integration.config;
            
            return `
                <div class="mb-3">
                    <label for="apiKey" class="form-label">API Key</label>
                    <input type="password" class="form-control" id="apiKey" name="api_key" value="${config.api_key || ''}">
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="serverPrefix" class="form-label">Server Prefix</label>
                        <input type="text" class="form-control" id="serverPrefix" name="server_prefix" value="${config.server_prefix || ''}">
                        <div class="form-text">For some providers like Mailchimp</div>
                    </div>
                    <div class="col-md-6">
                        <label for="defaultListId" class="form-label">Default List/Audience ID</label>
                        <input type="text" class="form-control" id="defaultListId" name="default_list_id" value="${config.default_list_id || ''}">
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="enableSync" name="enable_sync" value="1" ${config.enable_sync ? 'checked' : ''}>
                        <label class="form-check-label" for="enableSync">
                            Enable Subscriber Synchronization
                        </label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="syncFrequency" class="form-label">Sync Frequency</label>
                    <select class="form-select" id="syncFrequency" name="sync_frequency">
                        <option value="realtime" ${config.sync_frequency === 'realtime' ? 'selected' : ''}>Real-time</option>
                        <option value="hourly" ${config.sync_frequency === 'hourly' ? 'selected' : ''}>Hourly</option>
                        <option value="daily" ${config.sync_frequency === 'daily' ? 'selected' : ''}>Daily</option>
                        <option value="weekly" ${config.sync_frequency === 'weekly' ? 'selected' : ''}>Weekly</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="doubleOptIn" name="double_opt_in" value="1" ${config.double_opt_in ? 'checked' : ''}>
                        <label class="form-check-label" for="doubleOptIn">
                            Enable Double Opt-in
                        </label>
                    </div>
                </div>
            `;
        }
        
        // Helper function to get analytics configuration form
        function getAnalyticsForm(id) {
            // Find the integration data
            const integration = integrations.analytics.find(item => item.id === id) || { config: {} };
            const config = integration.config;
            
            return `
                <div class="mb-3">
                    <label for="trackingId" class="form-label">Tracking ID</label>
                    <input type="text" class="form-control" id="trackingId" name="tracking_id" value="${config.tracking_id || ''}">
                    <div class="form-text">For Google Analytics, format: UA-XXXXXXXXX-X or G-XXXXXXXXXX</div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="enableIpAnonymization" name="enable_ip_anonymization" value="1" ${config.enable_ip_anonymization ? 'checked' : ''}>
                        <label class="form-check-label" for="enableIpAnonymization">
                            Enable IP Anonymization
                        </label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="enableDemographics" name="enable_demographics" value="1" ${config.enable_demographics ? 'checked' : ''}>
                        <label class="form-check-label" for="enableDemographics">
                            Enable Demographics
                        </label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="enableEnhancedLinkAttribution" name="enable_enhanced_link_attribution" value="1" ${config.enable_enhanced_link_attribution ? 'checked' : ''}>
                        <label class="form-check-label" for="enableEnhancedLinkAttribution">
                            Enable Enhanced Link Attribution
                        </label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="excludeAdminUsers" name="exclude_admin_users" value="1" ${config.exclude_admin_users ? 'checked' : ''}>
                        <label class="form-check-label" for="excludeAdminUsers">
                            Exclude Admin Users from Analytics
                        </label>
                    </div>
                </div>
            `;
        }
        
        // Helper functions for other integration types
        function getCrmForm(id) {
            // Find the integration data
            const integration = integrations.crm.find(item => item.id === id) || { config: {} };
            const config = integration.config;
            
            // Generate form based on CRM type
            if (id === 'hubspot') {
                return `
                    <div class="mb-3">
                        <label for="apiKey" class="form-label">API Key</label>
                        <input type="password" class="form-control" id="apiKey" name="api_key" value="${config.api_key || ''}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="portalId" class="form-label">Portal ID</label>
                        <input type="text" class="form-control" id="portalId" name="portal_id" value="${config.portal_id || ''}">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="syncContacts" name="sync_contacts" value="1" ${config.sync_contacts ? 'checked' : ''}>
                            <label class="form-check-label" for="syncContacts">
                                Sync Contacts
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="syncDeals" name="sync_deals" value="1" ${config.sync_deals ? 'checked' : ''}>
                            <label class="form-check-label" for="syncDeals">
                                Sync Deals
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="dealPipelineId" class="form-label">Deal Pipeline ID</label>
                        <input type="text" class="form-control" id="dealPipelineId" name="deal_pipeline_id" value="${config.deal_pipeline_id || ''}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="syncFrequency" class="form-label">Sync Frequency</label>
                        <select class="form-select" id="syncFrequency" name="sync_frequency">
                            <option value="realtime" ${config.sync_frequency === 'realtime' ? 'selected' : ''}>Real-time</option>
                            <option value="hourly" ${config.sync_frequency === 'hourly' ? 'selected' : ''}>Hourly</option>
                            <option value="daily" ${config.sync_frequency === 'daily' ? 'selected' : ''}>Daily</option>
                            <option value="weekly" ${config.sync_frequency === 'weekly' ? 'selected' : ''}>Weekly</option>
                        </select>
                    </div>
                `;
            } else if (id === 'salesforce') {
                return `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="clientId" class="form-label">Client ID</label>
                            <input type="text" class="form-control" id="clientId" name="client_id" value="${config.client_id || ''}">
                        </div>
                        <div class="col-md-6">
                            <label for="clientSecret" class="form-label">Client Secret</label>
                            <input type="password" class="form-control" id="clientSecret" name="client_secret" value="${config.client_secret || ''}">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="${config.username || ''}">
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" value="${config.password || ''}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="securityToken" class="form-label">Security Token</label>
                        <input type="password" class="form-control" id="securityToken" name="security_token" value="${config.security_token || ''}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="instanceUrl" class="form-label">Instance URL</label>
                        <input type="text" class="form-control" id="instanceUrl" name="instance_url" value="${config.instance_url || ''}">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="syncContacts" name="sync_contacts" value="1" ${config.sync_contacts ? 'checked' : ''}>
                            <label class="form-check-label" for="syncContacts">
                                Sync Contacts
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="syncOpportunities" name="sync_opportunities" value="1" ${config.sync_opportunities ? 'checked' : ''}>
                            <label class="form-check-label" for="syncOpportunities">
                                Sync Opportunities
                            </label>
                        </div>
                    </div>
                `;
            } else {
                return `<p>No configuration options available for this CRM integration.</p>`;
            }
        }
        
        function getChatSupportForm(id) {
            // Find the integration data
            const integration = integrations.chat_support.find(item => item.id === id) || { config: {} };
            const config = integration.config;
            
            if (id === 'zendesk') {
                return `
                    <div class="mb-3">
                        <label for="subdomain" class="form-label">Subdomain</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="subdomain" name="subdomain" value="${config.subdomain || ''}">
                            <span class="input-group-text">.zendesk.com</span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Admin Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="${config.email || ''}">
                        </div>
                        <div class="col-md-6">
                            <label for="apiToken" class="form-label">API Token</label>
                            <input type="password" class="form-control" id="apiToken" name="api_token" value="${config.api_token || ''}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="widgetKey" class="form-label">Widget Key</label>
                        <input type="text" class="form-control" id="widgetKey" name="widget_key" value="${config.widget_key || ''}">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enableChat" name="enable_chat" value="1" ${config.enable_chat ? 'checked' : ''}>
                            <label class="form-check-label" for="enableChat">
                                Enable Live Chat
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enableTickets" name="enable_tickets" value="1" ${config.enable_tickets ? 'checked' : ''}>
                            <label class="form-check-label" for="enableTickets">
                                Enable Ticket System
                            </label>
                        </div>
                    </div>
                `;
            } else if (id === 'intercom') {
                return `
                    <div class="mb-3">
                        <label for="appId" class="form-label">App ID</label>
                        <input type="text" class="form-control" id="appId" name="app_id" value="${config.app_id || ''}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="apiKey" class="form-label">API Key</label>
                        <input type="password" class="form-control" id="apiKey" name="api_key" value="${config.api_key || ''}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="accessToken" class="form-label">Access Token</label>
                        <input type="password" class="form-control" id="accessToken" name="access_token" value="${config.access_token || ''}">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enableIdentityVerification" name="enable_identity_verification" value="1" ${config.enable_identity_verification ? 'checked' : ''}>
                            <label class="form-check-label" for="enableIdentityVerification">
                                Enable Identity Verification
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="identityVerificationSecret" class="form-label">Identity Verification Secret</label>
                        <input type="password" class="form-control" id="identityVerificationSecret" name="identity_verification_secret" value="${config.identity_verification_secret || ''}">
                    </div>
                `;
            } else {
                return `<p>No configuration options available for this chat support integration.</p>`;
            }
        }
        
        function getSocialMediaForm(id) {
            // Find the integration data
            const integration = integrations.social_media.find(item => item.id === id) || { config: {} };
            const config = integration.config;
            
            if (id === 'facebook') {
                return `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="appId" class="form-label">App ID</label>
                            <input type="text" class="form-control" id="appId" name="app_id" value="${config.app_id || ''}">
                        </div>
                        <div class="col-md-6">
                            <label for="appSecret" class="form-label">App Secret</label>
                            <input type="password" class="form-control" id="appSecret" name="app_secret" value="${config.app_secret || ''}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pageId" class="form-label">Page ID</label>
                        <input type="text" class="form-control" id="pageId" name="page_id" value="${config.page_id || ''}">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enableSharing" name="enable_sharing" value="1" ${config.enable_sharing ? 'checked' : ''}>
                            <label class="form-check-label" for="enableSharing">
                                Enable Social Sharing
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enableComments" name="enable_comments" value="1" ${config.enable_comments ? 'checked' : ''}>
                            <label class="form-check-label" for="enableComments">
                                Enable Facebook Comments on Blog Posts
                            </label>
                        </div>
                    </div>
                `;
            } else if (id === 'twitter') {
                return `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="apiKey" class="form-label">API Key</label>
                            <input type="text" class="form-control" id="apiKey" name="api_key" value="${config.api_key || ''}">
                        </div>
                        <div class="col-md-6">
                            <label for="apiSecret" class="form-label">API Secret</label>
                            <input type="password" class="form-control" id="apiSecret" name="api_secret" value="${config.api_secret || ''}">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="accessToken" class="form-label">Access Token</label>
                            <input type="text" class="form-control" id="accessToken" name="access_token" value="${config.access_token || ''}">
                        </div>
                        <div class="col-md-6">
                            <label for="accessTokenSecret" class="form-label">Access Token Secret</label>
                            <input type="password" class="form-control" id="accessTokenSecret" name="access_token_secret" value="${config.access_token_secret || ''}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enableSharing" name="enable_sharing" value="1" ${config.enable_sharing ? 'checked' : ''}>
                            <label class="form-check-label" for="enableSharing">
                                Enable Twitter Sharing
                            </label>
                        </div>
                    </div>
                `;
            } else {
                return `<p>No configuration options available for this social media integration.</p>`;
            }
        }
        
        function getFileStorageForm(id) {
            // Find the integration data
            const integration = integrations.file_storage.find(item => item.id === id) || { config: {} };
            const config = integration.config;
            
            if (id === 'aws_s3') {
                return `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="accessKey" class="form-label">Access Key</label>
                            <input type="text" class="form-control" id="accessKey" name="access_key" value="${config.access_key || ''}">
                        </div>
                        <div class="col-md-6">
                            <label for="secretKey" class="form-label">Secret Key</label>
                            <input type="password" class="form-control" id="secretKey" name="secret_key" value="${config.secret_key || ''}">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="region" class="form-label">Region</label>
                            <select class="form-select" id="region" name="region">
                                <option value="us-east-1" ${config.region === 'us-east-1' ? 'selected' : ''}>US East (N. Virginia)</option>
                                <option value="us-east-2" ${config.region === 'us-east-2' ? 'selected' : ''}>US East (Ohio)</option>
                                <option value="us-west-1" ${config.region === 'us-west-1' ? 'selected' : ''}>US West (N. California)</option>
                                <option value="us-west-2" ${config.region === 'us-west-2' ? 'selected' : ''}>US West (Oregon)</option>
                                <option value="eu-west-1" ${config.region === 'eu-west-1' ? 'selected' : ''}>EU (Ireland)</option>
                                <option value="eu-central-1" ${config.region === 'eu-central-1' ? 'selected' : ''}>EU (Frankfurt)</option>
                                <option value="ap-northeast-1" ${config.region === 'ap-northeast-1' ? 'selected' : ''}>Asia Pacific (Tokyo)</option>
                                <option value="ap-southeast-1" ${config.region === 'ap-southeast-1' ? 'selected' : ''}>Asia Pacific (Singapore)</option>
                                <option value="ap-southeast-2" ${config.region === 'ap-southeast-2' ? 'selected' : ''}>Asia Pacific (Sydney)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="bucket" class="form-label">Bucket Name</label>
                            <input type="text" class="form-control" id="bucket" name="bucket" value="${config.bucket || ''}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="usePathStyleEndpoint" name="use_path_style_endpoint" value="1" ${config.use_path_style_endpoint ? 'checked' : ''}>
                            <label class="form-check-label" for="usePathStyleEndpoint">
                                Use Path Style Endpoint
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="useForMedia" name="use_for_media" value="1" ${config.use_for_media ? 'checked' : ''}>
                            <label class="form-check-label" for="useForMedia">
                                Use for Media Storage
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="useForBackups" name="use_for_backups" value="1" ${config.use_for_backups ? 'checked' : ''}>
                            <label class="form-check-label" for="useForBackups">
                                Use for Backup Storage
                            </label>
                        </div>
                    </div>
                `;
            } else if (id === 'google_drive') {
                return `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="clientId" class="form-label">Client ID</label>
                            <input type="text" class="form-control" id="clientId" name="client_id" value="${config.client_id || ''}">
                        </div>
                        <div class="col-md-6">
                            <label for="clientSecret" class="form-label">Client Secret</label>
                            <input type="password" class="form-control" id="clientSecret" name="client_secret" value="${config.client_secret || ''}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="refreshToken" class="form-label">Refresh Token</label>
                        <input type="text" class="form-control" id="refreshToken" name="refresh_token" value="${config.refresh_token || ''}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="folderId" class="form-label">Folder ID</label>
                        <input type="text" class="form-control" id="folderId" name="folder_id" value="${config.folder_id || ''}">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="useForMedia" name="use_for_media" value="1" ${config.use_for_media ? 'checked' : ''}>
                            <label class="form-check-label" for="useForMedia">
                                Use for Media Storage
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="useForBackups" name="use_for_backups" value="1" ${config.use_for_backups ? 'checked' : ''}>
                            <label class="form-check-label" for="useForBackups">
                                Use for Backup Storage
                            </label>
                        </div>
                    </div>
                `;
            } else {
                return `<p>No configuration options available for this file storage integration.</p>`;
            }
        }
        
        // Documentation links
        $('.documentation-link').click(function(e) {
            e.preventDefault();
            const integration = $(this).data('integration');
            // In a real implementation, this might open a modal with documentation or redirect to an external documentation site
            alert(`Documentation for ${integration} would be shown here.`);
        });
        
        // Search for available integrations
        $('#searchIntegrations').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $('.available-integration').each(function() {
                const integrationText = $(this).text().toLowerCase();
                if (integrationText.includes(searchTerm)) {
                    $(this).closest('.col-md-4').show();
                } else {
                    $(this).closest('.col-md-4').hide();
                }
            });
        });
        
        // Refresh logs button
        $('#refreshLogs').click(function() {
            $(this).html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');
            
            // Simulate a delay for the refresh
            setTimeout(function() {
                $('#refreshLogs').html('<i class="fas fa-sync-alt"></i> Refresh');
                // In a real implementation, you would make an AJAX call to get the latest logs
            }, 1000);
        });
    });
    </script>
    
    <style>
    /* Integration Card Styles */
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
    }
    
    /* Integration Category Nav Styles */
    .integration-category-nav {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 10px;
    }
    
    .integration-category-nav .nav-link {
        white-space: nowrap;
        margin-right: 5px;
    }
    
    /* Status Toggle Button */
    .status-toggle {
        min-width: 80px;
    }
    
    /* Available Integration Card */
    .available-integration {
        height: 100%;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .integration-category-nav {
            padding-bottom: 15px;
        }
        
        .integration-category-nav .nav-link {
            font-size: 0.85rem;
            padding: 0.5rem;
        }
    }
    </style>
</body>
</html><?php
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

// Mock data for integrations
$integrations = [
    // Payment Gateways
    'payment_gateways' => [
        [
            'id' => 'stripe',
            'name' => 'Stripe',
            'description' => 'Accept credit card payments securely with Stripe.',
            'logo' => 'assets/images/integrations/stripe-logo.png',
            'status' => 'active',
            'config' => [
                'mode' => 'test', // 'test' or 'live'
                'test_publishable_key' => 'pk_test_51Abcde...',
                'test_secret_key' => 'sk_test_51Abcde...',
                'live_publishable_key' => '',
                'live_secret_key' => '',
                'webhook_secret' => 'whsec_...',
                'currency' => 'USD',
                'payment_methods' => ['card', 'apple_pay', 'google_pay']
            ]
        ],
        [
            'id' => 'paypal',
            'name' => 'PayPal',
            'description' => 'Enable customers to pay via PayPal.',
            'logo' => 'assets/images/integrations/paypal-logo.png',
            'status' => 'inactive',
            'config' => [
                'mode' => 'sandbox', // 'sandbox' or 'live'
                'sandbox_client_id' => '',
                'sandbox_client_secret' => '',
                'live_client_id' => '',
                'live_client_secret' => '',
                'currency' => 'USD',
                'webhook_id' => ''
            ]
        ]
    ],
    
    // Email Marketing
    'email_marketing' => [
        [
            'id' => 'mailchimp',
            'name' => 'Mailchimp',
            'description' => 'Sync subscribers and send automated emails.',
            'logo' => 'assets/images/integrations/mailchimp-logo.png',
            'status' => 'active',
            'config' => [
                'api_key' => 'abc123def456-us1',
                'server_prefix' => 'us1',
                'default_list_id' => 'a1b2c3d4e5',
                'enable_sync' => true,
                'sync_frequency' => 'daily', // 'realtime', 'hourly', 'daily'
                'double_opt_in' => true
            ]
        ],
        [
            'id' => 'sendgrid',
            'name' => 'SendGrid',
            'description' => 'Email delivery and marketing campaigns.',
            'logo' => 'assets/images/integrations/sendgrid-logo.png',
            'status' => 'inactive',
            'config' => [
                'api_key' => '',
                'from_email' => '',
                'from_name' => '',
                'contact_list_id' => '',
                'enable_tracking' => true
            ]
        ]
    ],
    
    // CRM Systems
    'crm' => [
        [
            'id' => 'hubspot',
            'name' => 'HubSpot',
            'description' => 'Sync contacts and leads with HubSpot CRM.',
            'logo' => 'assets/images/integrations/hubspot-logo.png',
            'status' => 'inactive',
            'config' => [
                'api_key' => '',
                'portal_id' => '',
                'sync_contacts' => true,
                'sync_deals' => true,
                'deal_pipeline_id' => '',
                'lead_status_property' => 'lifecyclestage',
                'sync_frequency' => 'daily' // 'realtime', 'hourly', 'daily'
            ]
        ],
        [
            'id' => 'salesforce',
            'name' => 'Salesforce',
            'description' => 'Connect with Salesforce for contact and opportunity management.',
            'logo' => 'assets/images/integrations/salesforce-logo.png',
            'status' => 'inactive',
            'config' => [
                'client_id' => '',
                'client_secret' => '',
                'username' => '',
                'password' => '',
                'security_token' => '',
                'instance_url' => '',
                'sync_contacts' => true,
                'sync_opportunities' => true,
                'sync_frequency' => 'daily' // 'realtime', 'hourly', 'daily'
            ]
        ]
    ],
    
    // Analytics
    'analytics' => [
        [
            'id' => 'google_analytics',
            'name' => 'Google Analytics',
            'description' => 'Track website traffic and user behavior.',
            'logo' => 'assets/images/integrations/google-analytics-logo.png',
            'status' => 'active',
            'config' => [
                'tracking_id' => 'UA-123456789-1',
                'enable_ip_anonymization' => true,
                'enable_demographics' => true,
                'enable_enhanced_link_attribution' => true,
                'exclude_admin_users' => true
            ]
        ],
        [
            'id' => 'facebook_pixel',
            'name' => 'Facebook Pixel',
            'description' => 'Track conversions and optimize Facebook ads.',
            'logo' => 'assets/images/integrations/facebook-pixel-logo.png',
            'status' => 'inactive',
            'config' => [
                'pixel_id' => '',
                'enable_advanced_matching' => false,
                'track_pageviews' => true,
                'track_form_submissions' => true
            ]
        ]
    ],
    
    // Chat & Support
    'chat_support' => [
        [
            'id' => 'zendesk',
            'name' => 'Zendesk',
            'description' => 'Customer support and ticketing system.',
            'logo' => 'assets/images/integrations/zendesk-logo.png',
            'status' => 'inactive',
            'config' => [
                'subdomain' => '',
                'email' => '',
                'api_token' => '',
                'widget_key' => '',
                'enable_chat' => true,
                'enable_tickets' => true
            ]
        ],
        [
            'id' => 'intercom',
            'name' => 'Intercom',
            'description' => 'Live chat and customer messaging platform.',
            'logo' => 'assets/images/integrations/intercom-logo.png',
            'status' => 'inactive',
            'config' => [
                'app_id' => '',
                'api_key' => '',
                'access_token' => '',
                'identity_verification_secret' => '',
                'enable_identity_verification' => true
            ]
        ]
    ],
    
    // Social Media
    'social_media' => [
        [
            'id' => 'facebook',
            'name' => 'Facebook',
            'description' => 'Facebook page integration for social sharing.',
            'logo' => 'assets/images/integrations/facebook-logo.png',
            'status' => 'inactive',
            'config' => [
                'app_id' => '',
                'app_secret' => '',
                'page_id' => '',
                'enable_sharing' => true,
                'enable_comments' => false
            ]
        ],
        [
            'id' => 'twitter',
            'name' => 'Twitter',
            'description' => 'Twitter integration for social sharing.',
            'logo' => 'assets/images/integrations/twitter-logo.png',
            'status' => 'inactive',
            'config' => [
                'api_key' => '',
                'api_secret' => '',
                'access_token' => '',
                'access_token_secret' => '',
                'enable_sharing' => true
            ]
        ]
    ],
    
    // File Storage
    'file_storage' => [
        [
            'id' => 'aws_s3',
            'name' => 'Amazon S3',
            'description' => 'Cloud storage for files and media.',
            'logo' => 'assets/images/integrations/aws-s3-logo.png',
            'status' => 'inactive',
            'config' => [
                'access_key' => '',
                'secret_key' => '',
                'region' => 'us-east-1',
                'bucket' => '',
                'use_path_style_endpoint' => false,
                'use_for_media' => true,
                'use_for_backups' => true
            ]
        ],
        [
            'id' => 'google_drive',
            'name' => 'Google Drive',
            'description' => 'Cloud storage integration with Google Drive.',
            'logo' => 'assets/images/integrations/google-drive-logo.png',
            'status' => 'inactive',
            'config' => [
                'client_id' => '',
                'client_secret' => '',
                'refresh_token' => '',
                'folder_id' => '',
                'use_for_media' => false,
                'use_for_backups' => true
            ]
        ]
    ]
];

// Mock data for available integrations (not yet installed)
$available_integrations = [
    [
        'id' => 'slack',
        'name' => 'Slack',
        'description' => 'Get notifications and updates in your Slack workspace.',
        'logo' => 'assets/images/integrations/slack-logo.png',
        'category' => 'communication'
    ],
    [
        'id' => 'dropbox',
        'name' => 'Dropbox',
        'description' => 'Cloud storage integration with Dropbox.',
        'logo' => 'assets/images/integrations/dropbox-logo.png',
        'category' => 'file_storage'
    ],
    [
        'id' => 'zapier',
        'name' => 'Zapier',
        'description' => 'Connect with 3,000+ apps through Zapier automation.',
        'logo' => 'assets/images/integrations/zapier-logo.png',
        'category' => 'automation'
    ],
    [
        'id' => 'square',
        'name' => 'Square',
        'description' => 'Process payments with Square.',
        'logo' => 'assets/images/integrations/square-logo.png',
        'category' => 'payment_gateways'
    ],
    [
        'id' => 'zoho_crm',
        'name' => 'Zoho CRM',
        'description' => 'Sync contacts and leads with Zoho CRM.',
        'logo' => 'assets/images/integrations/zoho-crm-logo.png',
        'category' => 'crm'
    ],
    [
        'id' => 'constant_contact',
        'name' => 'Constant Contact',
        'description' => 'Email marketing integration with Constant Contact.',
        'logo' => 'assets/images/integrations/constant-contact-logo.png',
        'category' => 'email_marketing'
    ]
];

// Mock connection logs
$connection_logs = [
    [
        'integration' => 'Stripe',
        'action' => 'API Request: Create Payment Intent',
        'status' => 'success',
        'timestamp' => '2023-11-10 14:23:45',
        'details' => 'Successfully created payment intent for $125.00'
    ],
    [
        'integration' => 'Mailchimp',
        'action' => 'Contact Sync',
        'status' => 'success',
        'timestamp' => '2023-11-10 13:00:02',
        'details' => 'Successfully synchronized 23 contacts'
    ],
    [
        'integration' => 'Mailchimp',
        'action' => 'Add New Subscriber',
        'status' => 'success',
        'timestamp' => '2023-11-10 11:45:18',
        'details' => 'Added john.doe@example.com to list "Newsletter Subscribers"'
    ],
    [
        'integration' => 'Google Analytics',
        'action' => 'Event Tracking',
        'status' => 'success',
        'timestamp' => '2023-11-10 10:30:45',
        'details' => 'Tracked 156 pageview events'
    ],
    [
        'integration' => 'HubSpot',
        'action' => 'API Connection Test',
        'status' => 'error',
        'timestamp' => '2023-11-09 16:15:30',
        'details' => 'API key validation failed: Invalid API key'
    ],
    [
        'integration' => 'Stripe',
        'action' => 'Webhook Received',
        'status' => 'success',
        'timestamp' => '2023-11-09 15:22:17',
        'details' => 'Processed payment.succeeded event for invoice #INV-2023-1045'
    ]
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Process integration update
    if ($action === 'update_integration') {
        $integration_id = isset($_POST['integration_id']) ? $_POST['integration_id'] : '';
        $integration_category = isset($_POST['integration_category']) ? $_POST['integration_category'] : '';
        
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-integrations.php?success=updated&category=' . $integration_category);
        exit;
    }
    
    // Process integration activation/deactivation
    if ($action === 'toggle_integration') {
        $integration_id = isset($_POST['integration_id']) ? $_POST['integration_id'] : '';
        $integration_category = isset($_POST['integration_category']) ? $_POST['integration_category'] : '';
        $new_status = isset($_POST['status']) ? $_POST['status'] : '';
        
        // In a real implementation, update the integration status in the database
        // For now, just redirect with success message
        if ($new_status === 'active') {
            header('Location: admin-integrations.php?success=activated&category=' . $integration_category);
        } else {
            header('Location: admin-integrations.php?success=deactivated&category=' . $integration_category);
        }
        exit;
    }
    
    // Process new integration installation
    if ($action === 'install_integration') {
        $integration_id = isset($_POST['integration_id']) ? $_POST['integration_id'] : '';
        
        // In a real implementation, set up the new integration in the database
        // For now, just redirect with success message
        header('Location: admin-integrations.php?success=installed');
        exit;
    }
    
    // Process connection test
    if ($action === 'test_connection') {
        $integration_id = isset($_POST['integration_id']) ? $_POST['integration_id'] : '';
        $integration_category = isset($_POST['integration_category']) ? $_POST['integration_category'] : '';
        
        // In a real implementation, test the API connection
        // For now, just redirect with success message
        header('Location: admin-integrations.php?success=connection_tested&category=' . $integration_category);
        exit;
    }
    
    // Process clearing connection logs
    if ($action === 'clear_logs') {
        // In a real implementation, clear logs from the database
        // For now, just redirect with success message
        header('Location: admin-integrations.php?success=logs_cleared&tab=logs');
        exit;
    }
}

// Get active category/tab from URL
$active_category = isset($_GET['category']) ? $_GET['category'] : 'payment_gateways';
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'integrations';

// Function to get integration by ID and category
function getIntegrationByIdAndCategory($id, $category, $integrations) {
    if (isset($integrations[$category])) {
        foreach ($integrations[$category] as $integration) {
            if ($integration['id'] === $id) {
                return $integration;
            }
        }
    }
    return null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integrations - Backsure Global Support</title>
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
                            <li><a href="admin-seo.php"><i class="fas fa-search"></i> SEO Settings</a></li>
                            <li><a href="admin-integrations.php" class="active"><i class="fas fa-plug"></i> Integrations</a></li>
                            <li><a href="admin-general.php"><i class="fas fa-sliders-h"></i> General Settings</a></li>
                            <li><a href="admin-appearance.php"><i class="fas fa-palette"></i> Appearance</a></li>
                            <li><a href="admin-backup.php"><i class="fas fa-database"></i> Backup & Restore</a></li>
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
                    <h1>Integrations</h1>
                    <div class="breadcrumb">
                        <a href="admin-dashboard.php">Dashboard</a> / <span>Integrations</span>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php if($_GET['success'] == 'updated'): ?>
                    <strong>Success!</strong> Integration settings have been updated.
                    <?php elseif($_GET['success'] == 'activated'): ?>
                    <strong>Success!</strong> Integration has been activated.
                    <?php elseif($_GET['success'] == 'deactivated'): ?>
                    <strong>Success!</strong> Integration has been deactivated.
                    <?php elseif($_GET['success'] == 'installed'): ?>
                    <strong>Success!</strong> New integration has been installed.
                    <?php elseif($_GET['success'] == 'connection_tested'): ?>
                    <strong>Success!</strong> Connection test was successful.
                    <?php elseif($_GET['success'] == 'logs_cleared'): ?>
                    <strong>Success!</strong> Connection logs have been cleared.
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php if($_GET['error'] == 'connection_failed'): ?>
                    <strong>Error!</strong> Connection test failed. Please check your credentials.
                    <?php else: ?>
                    <strong>Error!</strong> There was a problem processing your request.
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <!-- Main Content -->
                <div class="content-body">
                    <!-- Tabs navigation -->
                    <ul class="nav nav-tabs" id="integrationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo ($active_tab == 'integrations') ? 'active' : ''; ?>" id="integrations-tab" data-bs-toggle="tab" data-bs-target="#integrations" type="button" role="tab" aria-controls="integrations" aria-selected="<?php echo ($active_tab == 'integrations') ? 'true' : 'false'; ?>">
                                Configured Integrations
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo ($active_tab == 'available') ? 'active' : ''; ?>" id="available-tab" data-bs-toggle="tab" data-bs-target="#available" type="button" role="tab" aria-controls="available" aria-selected="<?php echo ($active_tab == 'available') ? 'true' : 'false'; ?>">
                                Available Integrations
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo ($active_tab == 'logs') ? 'active' : ''; ?>" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs" type="button" role="tab" aria-controls="logs" aria-selected="<?php echo ($active_tab == 'logs') ? 'true' : 'false'; ?>">
                                Connection Logs
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content" id="integrationTabsContent">
                        <!-- Configured Integrations Tab -->
                        <div class="tab-pane fade <?php echo ($active_tab == 'integrations') ? 'show active' : ''; ?>" id="integrations" role="tabpanel" aria-labelledby="integrations-tab">
                            <div class="card">
                                <div class="card-header">
                                    <ul class="nav nav-pills integration-category-nav">
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($active_category == 'payment_gateways') ? 'active' : ''; ?>" href="#payment_gateways" data-category="payment_gateways">
                                                <i class="fas fa-credit-card"></i> Payment Gateways
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($active_category == 'email_marketing') ? 'active' : ''; ?>" href="#email_marketing" data-category="email_marketing">
                                                <i class="fas fa-envelope"></i> Email Marketing
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($active_category == 'crm') ? 'active' : ''; ?>" href="#crm" data-category="crm">
                                                <i class="fas fa-users"></i> CRM Systems
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($active_category == 'analytics') ? 'active' : ''; ?>" href="#analytics" data-category="analytics">
                                                <i class="fas fa-chart-bar"></i> Analytics
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($active_category == 'chat_support') ? 'active' : ''; ?>" href="#chat_support" data-category="chat_support">
                                                <i class="fas fa-comments"></i> Chat & Support
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($active_category == 'social_media') ? 'active' : ''; ?>" href="#social_media" data-category="social_media">
                                                <i class="fas fa-share-alt"></i> Social Media
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($active_category == 'file_storage') ? 'active' : ''; ?>" href="#file_storage" data-category="file_storage">
                                                <i class="fas fa-cloud-upload-alt"></i> File Storage
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <!-- Integration Category Content -->
                                    <?php foreach ($integrations as $category => $category_integrations): ?>
                                    <div class="integration-category-content <?php echo ($active_category == $category) ? 'd-block' : 'd-none'; ?>" id="<?php echo $category; ?>_content">
                                        <div class="row">
                                            <?php foreach ($category_integrations as $integration): ?>
                                            <div class="col-md-6 mb-4">
                                                <div class="card h-100 integration-card <?php echo ($integration['status'] == 'inactive') ? 'integration-inactive' : ''; ?>">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <div class="integration-logo me-3">
                                                                <img src="<?php echo $integration['logo']; ?>" alt="<?php echo $integration['name']; ?> Logo">
                                                            </div>
                                                            <h5 class="mb-0"><?php echo $integration['name']; ?></h5>
                                                        </div>
                                                        <div class="integration-status">
                                                            <form action="admin-integrations.php" method="post" class="d-inline">
                                                                <input type="hidden" name="action" value="toggle_integration">
                                                                <input type="hidden" name="integration_id" value="<?php echo $integration['id']; ?>">
                                                                <input type="hidden" name="integration_category" value="<?php echo $category; ?>">
                                                                <input type="hidden" name="status" value="<?php echo ($integration['status'] == 'active') ? 'inactive' : 'active'; ?>">
                                                                <button type="submit" class="btn btn-sm <?php echo ($integration['status'] == 'active') ? 'btn-success active' : 'btn-secondary'; ?> status-toggle">
                                                                    <?php echo ($integration['status'] == 'active') ? 'Active' : 'Inactive'; ?>
                                                                </button>
                                                            </form>
                                                            <button type="button" class="btn btn-sm btn-outline-primary ms-2 configure-integration" data-integration-id="<?php echo $integration['id']; ?>" data-category="<?php echo $category; ?>">
                                                                <i class="fas fa-cog"></i>