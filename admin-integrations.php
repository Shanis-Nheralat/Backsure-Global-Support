<?php
// Start session first thing - no whitespace before this
session_start();

// Block unauthorized users
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Get admin information from session
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin User';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'Administrator';

// Check if user has permission to access this page
// Super Admin and Admin roles can access this page
if (!in_array($_SESSION['admin_role'], ['admin', 'superadmin'])) {
    header("Location: admin-dashboard.php?error=unauthorized");
    exit();
}

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="robots" content="noindex, nofollow">
  <title>Integrations | Backsure Global Support</title>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <!-- Include Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Root variables and common styles - matching dashboard style */
    :root {
      --primary-color: #062767;
      --primary-light: #3a5ca2;
      --primary-dark: #041c4a;
      --accent-color: #b19763;
      --accent-light: #cdb48e;
      --accent-dark: #97814c;
      --success-color: #1cc88a;
      --info-color: #36b9cc;
      --warning-color: #f6c23e;
      --danger-color: #e74a3b;
      --dark-color: #333333;
      --light-color: #f8f9fc;
      --gray-100: #f8f9fc;
      --gray-200: #eaecf4;
      --gray-300: #dddfeb;
      --gray-400: #d1d3e2;
      --gray-500: #b7b9cc;
      --gray-600: #858796;
      --gray-700: #6e707e;
      --gray-800: #5a5c69;
      --gray-900: #3a3b45;
      
      --sidebar-width: 250px;
      --sidebar-collapsed-width: 80px;
      --header-height: 60px;
      --transition-speed: 0.3s;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Open Sans', sans-serif;
      font-size: 14px;
      line-height: 1.6;
      color: var(--gray-800);
      background-color: var(--gray-100);
    }

    .admin-body {
      min-height: 100vh;
    }

    .admin-container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar Styles */
    .admin-sidebar {
      width: var(--sidebar-width);
      background-color: var(--primary-color);
      color: white;
      position: fixed;
      left: 0;
      top: 0;
      height: 100vh;
      overflow-y: auto;
      transition: width var(--transition-speed);
      z-index: 100;
      display: flex;
      flex-direction: column;
    }

    .admin-main {
      flex: 1;
      margin-left: var(--sidebar-width);
      transition: margin-left var(--transition-speed);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .admin-container.sidebar-collapsed .admin-sidebar {
      width: var(--sidebar-collapsed-width);
    }

    .admin-container.sidebar-collapsed .admin-main {
      margin-left: var(--sidebar-collapsed-width);
    }

    .admin-container.sidebar-collapsed .sidebar-header h2,
    .admin-container.sidebar-collapsed .admin-user .user-info,
    .admin-container.sidebar-collapsed .admin-user .dropdown-toggle,
    .admin-container.sidebar-collapsed .sidebar-nav ul li a span,
    .admin-container.sidebar-collapsed .sidebar-nav ul li a .submenu-icon,
    .admin-container.sidebar-collapsed .sidebar-footer a span {
      display: none;
    }

    .sidebar-header {
      padding: 15px 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
    }

    .admin-logo {
      height: 40px;
      margin-bottom: 10px;
    }

    .sidebar-header h2 {
      color: white;
      font-size: 1.2rem;
      text-align: center;
      margin: 0;
    }

    .admin-user {
      padding: 15px 20px;
      display: flex;
      align-items: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      position: relative;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      overflow: hidden;
      margin-right: 10px;
    }

    .user-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .user-info {
      flex: 1;
    }

    .user-info h3 {
      margin: 0;
      font-size: 0.9rem;
      color: white;
    }

    .user-role {
      font-size: 0.8rem;
      color: rgba(255, 255, 255, 0.7);
    }

    .dropdown-toggle {
      background: none;
      border: none;
      color: white;
      cursor: pointer;
      padding: 5px;
    }

    .dropdown-menu {
      display: none;
      position: absolute;
      top: 100%;
      right: 10px;
      background-color: white;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      border-radius: 5px;
      min-width: 180px;
      z-index: 101;
      overflow: hidden;
    }

    .dropdown-menu.show {
      display: block;
    }

    .dropdown-menu li {
      border-bottom: 1px solid var(--gray-200);
    }

    .dropdown-menu li:last-child {
      border-bottom: none;
    }

    .dropdown-menu li a {
      color: var(--gray-700);
      display: flex;
      align-items: center;
      padding: 10px 15px;
      text-decoration: none;
      transition: background-color 0.2s;
    }

    .dropdown-menu li a i {
      margin-right: 10px;
      width: 16px;
      text-align: center;
    }

    .dropdown-menu li a:hover {
      background-color: var(--gray-100);
    }

    .sidebar-nav {
      flex: 1;
      padding: 15px 0;
    }

    .sidebar-nav ul {
      list-style: none;
    }

    .sidebar-nav ul li {
      margin-bottom: 2px;
    }

    .sidebar-nav ul li a {
      color: rgba(255, 255, 255, 0.8);
      display: flex;
      align-items: center;
      padding: 10px 20px;
      transition: all 0.3s;
      text-decoration: none;
    }

    .sidebar-nav ul li a i {
      width: 20px;
      margin-right: 10px;
      text-align: center;
    }

    .sidebar-nav ul li a .submenu-icon {
      margin-left: auto;
      transition: transform 0.3s;
    }

    .sidebar-nav ul li.open > a .submenu-icon {
      transform: rotate(90deg);
    }

    .sidebar-nav ul li a:hover {
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
    }

    .sidebar-nav ul li.active > a {
      background-color: var(--accent-color);
      color: white;
    }

    .sidebar-nav ul li .submenu {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease-out;
    }

    .sidebar-nav ul li.open .submenu {
      max-height: 1000px;
    }

    .sidebar-nav ul li .submenu li a {
      padding-left: 50px;
    }

    .badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background-color: var(--danger-color);
      color: white;
      border-radius: 10px;
      font-size: 0.7rem;
      padding: 2px 6px;
      margin-left: 8px;
    }

    .sidebar-footer {
      padding: 15px 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-footer a {
      color: rgba(255, 255, 255, 0.7);
      display: flex;
      align-items: center;
      text-decoration: none;
      font-size: 0.9rem;
    }

    .sidebar-footer a i {
      margin-right: 10px;
    }

    .sidebar-footer a:hover {
      color: white;
    }

    /* Header Styles */
    .admin-header {
      height: var(--header-height);
      background-color: white;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      position: sticky;
      top: 0;
      z-index: 99;
    }

    .header-left {
      display: flex;
      align-items: center;
    }

    .sidebar-toggle {
      background: none;
      border: none;
      color: var(--gray-600);
      font-size: 1.2rem;
      cursor: pointer;
      margin-right: 15px;
    }

    .breadcrumbs {
      display: flex;
      align-items: center;
    }

    .breadcrumbs a {
      color: var(--gray-600);
      text-decoration: none;
    }

    .header-right {
      display: flex;
      align-items: center;
    }

    .admin-search {
      position: relative;
      margin-right: 20px;
    }

    .admin-search input {
      background-color: var(--gray-100);
      border: none;
      border-radius: 4px;
      padding: 8px 30px 8px 10px;
      width: 200px;
      font-family: inherit;
    }

    .admin-search button {
      background: none;
      border: none;
      color: var(--gray-600);
      position: absolute;
      right: 5px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
    }

    .header-actions {
      display: flex;
      align-items: center;
    }

    .action-btn {
      background: none;
      border: none;
      color: var(--gray-600);
      font-size: 1rem;
      margin-left: 15px;
      position: relative;
      cursor: pointer;
    }

    .action-btn .badge {
      position: absolute;
      top: -5px;
      right: -5px;
      width: 18px;
      height: 18px;
      padding: 0;
    }

    /* Admin Content */
    .admin-content {
      padding: 20px;
      flex: 1;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .page-header h1 {
      margin: 0;
      color: var(--primary-color);
      font-size: 1.8rem;
    }

    /* Footer */
    .admin-footer {
      background-color: white;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-top: 1px solid var(--gray-200);
      font-size: 0.9rem;
      color: var(--gray-600);
    }

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
        object-fit: contain;
    }
    
    /* Integration Category Nav Styles */
    .integration-category-nav {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 10px;
    }
    
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

        .admin-main {
          margin-left: 0;
        }
        
        .admin-sidebar {
          left: -250px;
        }
        
        .admin-container.sidebar-collapsed .admin-sidebar {
          left: 0;
        }
    }
  </style>
</head>
<body class="admin-body">
  <div class="admin-container">
    <!-- Sidebar Navigation -->
    <aside class="admin-sidebar">
      <div class="sidebar-header">
        <img src="Logo.png" alt="BSG Support Logo" class="admin-logo">
        <h2>Admin Panel</h2>
      </div>
      
      <div class="admin-user">
        <div class="user-avatar">
          <img src="avatar.webp" alt="Admin User">
        </div>
        <div class="user-info">
          <h3><?php echo htmlspecialchars($admin_username); ?></h3>
          <span class="user-role"><?php echo htmlspecialchars($admin_role); ?></span>
        </div>
        <button id="user-dropdown-toggle" class="dropdown-toggle">
          <i class="fas fa-chevron-down"></i>
        </button>
        <ul id="user-dropdown" class="dropdown-menu">
          <li><a href="admin-profile.php"><i class="fas fa-user"></i> My Profile</a></li>
          <li><a href="admin-general.php"><i class="fas fa-cog"></i> Settings</a></li>
          <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
      </div>
      
      <nav class="sidebar-nav">
        <ul>
          <!-- Dashboard (Priority 1) -->
          <li>
            <a href="admin-dashboard.php">
              <i class="fas fa-tachometer-alt"></i>
              <span>Dashboard</span>
            </a>
          </li>
          
          <!-- Content Management (Priority 2) -->
          <li class="has-submenu">
            <a href="javascript:void(0)">
              <i class="fas fa-edit"></i>
              <span>Content Management</span>
              <i class="fas fa-chevron-right submenu-icon"></i>
            </a>
            <ul class="submenu">
              <li><a href="admin-blog.php"><i class="fas fa-blog"></i> Blog Management</a></li>
              <li><a href="admin-services.php"><i class="fas fa-briefcase"></i> Services Editor</a></li>
              <li><a href="admin-testimonials.php"><i class="fas fa-star"></i> Testimonials & Logos</a></li>
              <li><a href="admin-faq.php"><i class="fas fa-question-circle"></i> FAQ Management</a></li>
              <li><a href="admin-solutions.php"><i class="fas fa-file-alt"></i> Solutions</a></li>
            </ul>
          </li>
          
          <!-- CRM / Clients (Priority 3) -->
          <li class="has-submenu">
            <a href="javascript:void(0)">
              <i class="fas fa-envelope"></i>
              <span>CRM</span>
              <i class="fas fa-chevron-right submenu-icon"></i>
            </a>
            <ul class="submenu">
              <li><a href="admin-enquiries.php"><i class="fas fa-envelope-open-text"></i> Client Inquiries</a></li>
              <li><a href="admin-subscribers.php"><i class="fas fa-envelope-open"></i> Subscribers</a></li>
              <li><a href="admin-clients.php"><i class="fas fa-users"></i> Clients</a></li>
            </ul>
          </li>
          
          <!-- HR Tools (Priority 4) -->
          <li class="has-submenu">
            <a href="javascript:void(0)">
              <i class="fas fa-user-tie"></i>
              <span>HR Tools</span>
              <i class="fas fa-chevron-right submenu-icon"></i>
            </a>
            <ul class="submenu">
              <li><a href="admin-candidate.php"><i class="fas fa-user-graduate"></i> Candidates</a></li>
              <li><a href="admin-candidate-notes.php"><i class="fas fa-sticky-note"></i> Candidate Notes</a></li>
            </ul>
          </li>
          
          <!-- Users & Roles (Priority 5) -->
          <li class="has-submenu">
            <a href="javascript:void(0)">
              <i class="fas fa-users-cog"></i>
              <span>User Management</span>
              <i class="fas fa-chevron-right submenu-icon"></i>
            </a>
            <ul class="submenu">
              <li><a href="admin-users.php"><i class="fas fa-user-friends"></i> All Users</a></li>
              <li><a href="admin-roles.php"><i class="fas fa-user-tag"></i> Roles & Permissions</a></li>
              <li><a href="admin-profile.php"><i class="fas fa-id-card"></i> My Profile</a></li>
            </ul>
          </li>
          
          <!-- Settings (Priority 6) -->
          <li class="has-submenu open">
            <a href="javascript:void(0)">
              <i class="fas fa-cogs"></i>
              <span>Site Settings</span>
              <i class="fas fa-chevron-right submenu-icon"></i>
            </a>
            <ul class="submenu">
              <li><a href="admin-seo.php"><i class="fas fa-search"></i> SEO Settings</a></li>
              <li class="active"><a href="admin-integrations.php"><i class="fas fa-plug"></i> Integrations</a></li>
              <li><a href="admin-general.php"><i class="fas fa-sliders-h"></i> General Settings</a></li>
            </ul>
          </li>
        </ul>
      </nav>
      
      <div class="sidebar-footer">
        <a href="index.html" target="_blank">
          <i class="fas fa-external-link-alt"></i>
          <span>View Website</span>
        </a>
      </div>
    </aside>
    
    <!-- Main Content Area -->
    <main class="admin-main">
      <!-- Top Navigation Bar -->
      <header class="admin-header">
        <div class="header-left">
          <button id="sidebar-toggle" class="sidebar-toggle">
            <i class="fas fa-bars"></i>
          </button>
          <div class="breadcrumbs">
            <a href="admin-dashboard.php">Dashboard</a> / 
            <a href="admin-integrations.php">Integrations</a>
          </div>
        </div>
        
        <div class="header-right">
          <div class="admin-search">
            <input type="text" placeholder="Search integrations...">
            <button type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
          
          <div class="header-actions">
            <button class="action-btn notification-btn">
              <i class="fas fa-bell"></i>
              <span class="badge">5</span>
            </button>
            
            <button class="action-btn help-btn">
              <i class="fas fa-question-circle"></i>
            </button>
          </div>
        </div>
      </header>
      
      <!-- Dashboard Content -->
      <div class="admin-content">
        <div class="page-header">
          <h1>Integrations</h1>
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
                            <h3 class="mb-0">Available Integrations</h3>
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
                            <h3 class="mb-0">Connection Logs</h3>
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
                                <i class="fas fa-info-circle"></i> Showing most recent logs. Logs are automatically purged after 30 days.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      
      <!-- Admin Footer -->
      <footer class="admin-footer">
        <div class="footer-left">
          <p>&copy; <?php echo date('Y'); ?> Backsure Global Support. All rights reserved.</p>
        </div>
        <div class="footer-right">
          <span>Admin Panel v1.0</span>
        </div>
      </footer>
    </main>
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function() {
        // Sidebar toggle
        $('#sidebar-toggle').click(function() {
            $('.admin-container').toggleClass('sidebar-collapsed');
        });
        
        // User dropdown
        $('#user-dropdown-toggle').click(function(e) {
            e.stopPropagation();
            $('#user-dropdown').toggleClass('show');
        });
        
        // Close dropdown when clicking outside
        $(document).click(function(e) {
            if (!$('#user-dropdown-toggle').is(e.target) && !$('#user-dropdown').is(e.target) && $('#user-dropdown').has(e.target).length === 0) {
                $('#user-dropdown').removeClass('show');
            }
        });
        
        // Submenu toggle
        $('.has-submenu > a').click(function(e) {
            e.preventDefault();
            const parent = $(this).parent();
            
            // Close other open submenus
            const openItems = $('.has-submenu.open');
            openItems.each(function() {
                if ($(this)[0] !== parent[0]) {
                    $(this).removeClass('open');
                    const submenu = $(this).find('.submenu');
                    if (submenu.length) {
                        submenu.css('max-height', null);
                    }
                }
            });
            
            // Toggle current submenu
            parent.toggleClass('open');
            const submenu = parent.find('.submenu');
            
            if (submenu.length) {
                if (parent.hasClass('open')) {
                    submenu.css('max-height', submenu[0].scrollHeight + 'px');
                } else {
                    submenu.css('max-height', null);
                }
            }
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
            
            const modal = $('#configurationModal');
            
            // Find the integration name
            const integrationName = $(this).closest('.card-header').find('h5').text();
            modal.find('.modal-title').text(`Configure ${integrationName}`);
            
            // In a real implementation, you would dynamically load the form via AJAX
            $('#configuration-content').html('<div class="alert alert-info">Configuration options would be loaded here for ' + integrationName + '.</div>');
            
            modal.modal('show');
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
        
        // Documentation links
        $(document).on('click', '.documentation-link', function(e) {
            e.preventDefault();
            const integration = $(this).data('integration');
            // In a real implementation, this would open documentation
            alert(`Documentation for ${integration} would open here.`);
        });
    });
  </script>
</body>
</html><?php
// Start session first thing - no whitespace before this
session_start();

// Block unauthorized users
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Get admin information from session
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin User';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'Administrator';

// Check if user has permission to access this page
// Super Admin and Admin roles can access this page
if (!in_array($_SESSION['admin_role'], ['admin', 'superadmin'])) {
    header("Location: admin-dashboard.php?error=unauthorized");
    exit();
}

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="robots" content="noindex, nofollow">
  <title>Integrations | Backsure Global Support</title>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <!-- Include Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Root variables and common styles - matching dashboard style */
    :root {
      --primary-color: #062767;
      --primary-light: #3a5ca2;
      --primary-dark: #041c4a;
      --accent-color: #b19763;
      --accent-light: #cdb48e;
      --accent-dark: #97814c;
      --success-color: #1cc88a;
      --info-color: #36b9cc;
      --warning-color: #f6c23e;
      --danger-color: #e74a3b;
      --dark-color: #333333;
      --light-color: #f8f9fc;
      --gray-100: #f8f9fc;
      --gray-200: #eaecf4;
      --gray-300: #dddfeb;
      --gray-400: #d1d3e2;
      --gray-500: #b7b9cc;
      --gray-600: #858796;
      --gray-700: #6e707e;
      --gray-800: #5a5c69;
      --gray-900: #3a3b45;
      
      --sidebar-width: 250px;
      --sidebar-collapsed-width: 80px;
      --header-height: 60px;
      --transition-speed: 0.3s;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Open Sans', sans-serif;
      font-size: 14px;
      line-height: 1.6;
      color: var(--gray-800);
      background-color: var(--gray-100);
    }

    .admin-body {
      min-height: 100vh;
    }

    .admin-container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar Styles */
    .admin-sidebar {
      width: var(--sidebar-width);
      background-color: var(--primary-color);
      color: white;
      position: fixed;
      left: 0;
      top: 0;
      height: 100vh;
      overflow-y: auto;
      transition: width var(--transition-speed);
      z-index: 100;
      display: flex;
      flex-direction: column;
    }

    .admin-main {
      flex: 1;
      margin-left: var(--sidebar-width);
      transition: margin-left var(--transition-speed);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .admin-container.sidebar-collapsed .admin-sidebar {
      width: var(--sidebar-collapsed-width);
    }

    .admin-container.sidebar-collapsed .admin-main {
      margin-left: var(--sidebar-collapsed-width);
    }

    .admin-container.sidebar-collapsed .sidebar-header h2,
    .admin-container.sidebar-collapsed .admin-user .user-info,
    .admin-container.sidebar-collapsed .admin-user .dropdown-toggle,
    .admin-container.sidebar-collapsed .sidebar-nav ul li a span,
    .admin-container.sidebar-collapsed .sidebar-nav ul li a .submenu-icon,
    .admin-container.sidebar-collapsed .sidebar-footer a span {
      display: none;
    }

    .sidebar-header {
      padding: 15px 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
    }

    .admin-logo {
      height: 40px;
      margin-bottom: 10px;
    }

    .sidebar-header h2 {
      color: white;
      font-size: 1.2rem;
      text-align: center;
      margin: 0;
    }

    .admin-user {
      padding: 15px 20px;
      display: flex;
      align-items: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      position: relative;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      overflow: hidden;
      margin-right: 10px;
    }

    .user-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .user-info {
      flex: 1;
    }

    .user-info h3 {
      margin: 0;
      font-size: 0.9rem;
      color: white;
    }

    .user-role {
      font-size: 0.8rem;
      color: rgba(255, 255, 255, 0.7);
    }

    .dropdown-toggle {
      background: none;
      border: none;
      color: white;
      cursor: pointer;
      padding: 5px;
    }

    .dropdown-menu {
      display: none;
      position: absolute;
      top: 100%;
      right: 10px;
      background-color: white;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      border-radius: 5px;
      min-width: 180px;
      z-index: 101;
      overflow: hidden;
    }

    .dropdown-menu.show {
      display: block;
    }

    .dropdown-menu li {
      border-bottom: 1px solid var(--gray-200);
    }

    .dropdown-menu li:last-child {
      border-bottom: none;
    }

    .dropdown-menu li a {
      color: var(--gray-700);
      display: flex;
      align-items: center;
      padding: 10px 15px;
      text-decoration: none;
      transition: background-color 0.2s;
    }

    .dropdown-menu li a i {
      margin-right: 10px;
      width: 16px;
      text-align: center;
    }

    .dropdown-menu li a:hover {
      background-color: var(--gray-100);
    }

    .sidebar-nav {
      flex: 1;
      padding: 15px 0;
    }

    .sidebar-nav ul {
      list-style: none;
    }

    .sidebar-nav ul li {
      margin-bottom: 2px;
    }

    .sidebar-nav ul li a {
      color: rgba(255, 255, 255, 0.8);
      display: flex;
      align-items: center;
      padding: 10px 20px;
      transition: all 0.3s;
      text-decoration: none;
    }

    .sidebar-nav ul li a i {
      width: 20px;
      margin-right: 10px;
      text-align: center;
    }

    .sidebar-nav ul li a .submenu-icon {
      margin-left: auto;
      transition: transform 0.3s;
    }

    .sidebar-nav ul li.open > a .submenu-icon {
      transform: rotate(90deg);
    }

    .sidebar-nav ul li a:hover {
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
    }

    .sidebar-nav ul li.active > a {
      background-color: var(--accent-color);
      color: white;
    }

    .sidebar-nav ul li .submenu {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease-out;
    }

    .sidebar-nav ul li.open .submenu {
      max-height: 1000px;
    }

    .sidebar-nav ul li .submenu li a {
      padding-left: 50px;
    }

    .badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background-color: var(--danger-color);
      color: white;
      border-radius: 10px;
      font-size: 0.7rem;
      padding: 2px 6px;
      margin-left: 8px;
    }

    .sidebar-footer {
      padding: 15px 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-footer a {
      color: rgba(255, 255, 255, 0.7);
      display: flex;
      align-items: center;
      text-decoration: none;
      font-size: 0.9rem;
    }

    .sidebar-footer a i {
      margin-right: 10px;
    }

    .sidebar-footer a:hover {
      color: white;
    }

    /* Header Styles */
    .admin-header {
      height: var(--header-height);
      background-color: white;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      position: sticky;
      top: 0;
      z-index: 99;
    }

    .header-left {
      display: flex;
      align-items: center;
    }

    .sidebar-toggle {
      background: none;
      border: none;
      color: var(--gray-600);
      font-size: 1.2rem;
      cursor: pointer;
      margin-right: 15px;
    }

    .breadcrumbs {
      display: flex;
      align-items: center;
    }

    .breadcrumbs a {
      color: var(--gray-600);
      text-decoration: none;
    }

    .header-right {
      display: flex;
      align-items: center;
    }

    .admin-search {
      position: relative;
      margin-right: 20px;
    }

    .admin-search input {
      background-color: var(--gray-100);
      border: none;
      border-radius: 4px;
      padding: 8px 30px 8px 10px;
      width: 200px;
      font-family: inherit;
    }

    .admin-search button {
      background: none;
      border: none;
      color: var(--gray-600);
      position: absolute;
      right: 5px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
    }

    .header-actions {
      display: flex;
      align-items: center;
    }

    .action-btn {
      background: none;
      border: none;
      color: var(--gray-600);
      font-size: 1rem;
      margin-left: 15px;
      position: relative;
      cursor: pointer;
    }

    .action-btn .badge {
      position: absolute;
      top: -5px;
      right: -5px;
      width: 18px;
      height: 18px;
      padding: 0;
    }

    /* Admin Content */
    .admin-content {
      padding: 20px;
      flex: 1;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .page-header h1 {
      margin: 0;
      color: var(--primary-color);
      font-size: 1.8rem;
    }

    /* Footer */
    .admin-footer {
      background-color: white;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-top: 1px solid var(--gray-200);
      font-size: 0.9rem;
      color: var(--gray-600);
    }

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
        object-fit: contain;
    }
    
    /* Integration Category Nav Styles */
    .integration-category-nav {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 10px;
    }
    
    .integration-category-nav .nav-
