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

// Mock data for subscribers
$subscribers = [
    [
        'id' => 1,
        'email' => 'john.smith@example.com',
        'name' => 'John Smith',
        'status' => 'active',
        'source' => 'Homepage Form',
        'signup_date' => '2023-05-10 09:23:45',
        'last_activity' => '2023-10-15 14:32:18',
        'lists' => ['Newsletter', 'Product Updates'],
        'opens' => 12,
        'clicks' => 5
    ],
    [
        'id' => 2,
        'email' => 'sarah.johnson@company.org',
        'name' => 'Sarah Johnson',
        'status' => 'active',
        'source' => 'Blog Sidebar',
        'signup_date' => '2023-06-22 15:47:32',
        'last_activity' => '2023-11-02 08:15:39',
        'lists' => ['Newsletter'],
        'opens' => 8,
        'clicks' => 3
    ],
    [
        'id' => 3,
        'email' => 'michael.williams@enterprise.net',
        'name' => 'Michael Williams',
        'status' => 'inactive',
        'source' => 'Footer Form',
        'signup_date' => '2023-07-14 11:09:27',
        'last_activity' => '2023-08-05 16:42:51',
        'lists' => ['Product Updates', 'Industry Insights'],
        'opens' => 2,
        'clicks' => 0
    ],
    [
        'id' => 4,
        'email' => 'emily.brown@example.com',
        'name' => 'Emily Brown',
        'status' => 'active',
        'source' => 'Whitepaper Download',
        'signup_date' => '2023-08-30 16:53:11',
        'last_activity' => '2023-11-10 10:28:43',
        'lists' => ['Newsletter', 'Industry Insights', 'Special Offers'],
        'opens' => 15,
        'clicks' => 9
    ],
    [
        'id' => 5,
        'email' => 'robert.jones@company.co',
        'name' => 'Robert Jones',
        'status' => 'unsubscribed',
        'source' => 'Event Registration',
        'signup_date' => '2023-04-05 14:22:38',
        'last_activity' => '2023-09-18 11:05:29',
        'lists' => ['Newsletter'],
        'opens' => 4,
        'clicks' => 1
    ],
    [
        'id' => 6,
        'email' => 'jennifer.miller@organization.edu',
        'name' => 'Jennifer Miller',
        'status' => 'active',
        'source' => 'Contact Form',
        'signup_date' => '2023-09-12 09:37:15',
        'last_activity' => '2023-11-08 15:47:22',
        'lists' => ['Newsletter', 'Special Offers'],
        'opens' => 7,
        'clicks' => 4
    ],
    [
        'id' => 7,
        'email' => 'david.davis@example.org',
        'name' => 'David Davis',
        'status' => 'bounced',
        'source' => 'Homepage Form',
        'signup_date' => '2023-05-28 13:45:01',
        'last_activity' => '2023-07-17 08:22:36',
        'lists' => ['Newsletter', 'Product Updates'],
        'opens' => 3,
        'clicks' => 0
    ],
    [
        'id' => 8,
        'email' => 'lisa.wilson@company.com',
        'name' => 'Lisa Wilson',
        'status' => 'active',
        'source' => 'Blog Sidebar',
        'signup_date' => '2023-10-03 10:19:53',
        'last_activity' => '2023-11-12 14:33:47',
        'lists' => ['Newsletter', 'Industry Insights'],
        'opens' => 6,
        'clicks' => 2
    ],
    [
        'id' => 9,
        'email' => 'james.taylor@enterprise.com',
        'name' => 'James Taylor',
        'status' => 'active',
        'source' => 'Whitepaper Download',
        'signup_date' => '2023-08-15 16:08:27',
        'last_activity' => '2023-11-05 09:17:31',
        'lists' => ['Newsletter', 'Product Updates', 'Industry Insights'],
        'opens' => 10,
        'clicks' => 6
    ],
    [
        'id' => 10,
        'email' => 'patricia.anderson@example.net',
        'name' => 'Patricia Anderson',
        'status' => 'pending',
        'source' => 'Footer Form',
        'signup_date' => '2023-11-01 14:52:19',
        'last_activity' => null,
        'lists' => ['Newsletter'],
        'opens' => 0,
        'clicks' => 0
    ],
    [
        'id' => 11,
        'email' => 'thomas.moore@organization.net',
        'name' => 'Thomas Moore',
        'status' => 'active',
        'source' => 'Event Registration',
        'signup_date' => '2023-07-25 08:41:32',
        'last_activity' => '2023-10-29 11:28:56',
        'lists' => ['Newsletter', 'Special Offers'],
        'opens' => 9,
        'clicks' => 4
    ],
    [
        'id' => 12,
        'email' => 'elizabeth.martin@company.org',
        'name' => 'Elizabeth Martin',
        'status' => 'inactive',
        'source' => 'Contact Form',
        'signup_date' => '2023-06-17 12:33:48',
        'last_activity' => '2023-08-22 15:09:22',
        'lists' => ['Newsletter', 'Industry Insights'],
        'opens' => 5,
        'clicks' => 1
    ]
];

// Mock data for newsletter lists
$lists = [
    [
        'id' => 1,
        'name' => 'Newsletter',
        'description' => 'Regular company news and updates',
        'subscribers_count' => 12,
        'status' => 'active',
        'created_at' => '2023-01-15'
    ],
    [
        'id' => 2,
        'name' => 'Product Updates',
        'description' => 'New product releases and feature announcements',
        'subscribers_count' => 7,
        'status' => 'active',
        'created_at' => '2023-02-22'
    ],
    [
        'id' => 3,
        'name' => 'Industry Insights',
        'description' => 'Industry news, analysis, and research reports',
        'subscribers_count' => 6,
        'status' => 'active',
        'created_at' => '2023-03-10'
    ],
    [
        'id' => 4,
        'name' => 'Special Offers',
        'description' => 'Exclusive deals, promotions, and discounts',
        'subscribers_count' => 5,
        'status' => 'active',
        'created_at' => '2023-04-05'
    ],
    [
        'id' => 5,
        'name' => 'Events & Webinars',
        'description' => 'Upcoming events, webinars, and conference information',
        'subscribers_count' => 0,
        'status' => 'inactive',
        'created_at' => '2023-05-20'
    ]
];

// Mock data for campaign summary
$campaigns = [
    [
        'id' => 1,
        'name' => 'October Newsletter',
        'sent_date' => '2023-10-10 09:00:00',
        'recipients' => 10,
        'opens' => 85,
        'clicks' => 35,
        'unsubscribes' => 1
    ],
    [
        'id' => 2,
        'name' => 'New Service Announcement',
        'sent_date' => '2023-09-15 10:30:00',
        'recipients' => 12,
        'opens' => 92,
        'clicks' => 45,
        'unsubscribes' => 0
    ],
    [
        'id' => 3,
        'name' => 'Industry Report 2023',
        'sent_date' => '2023-08-22 14:00:00',
        'recipients' => 8,
        'opens' => 75,
        'clicks' => 40,
        'unsubscribes' => 1
    ],
    [
        'id' => 4,
        'name' => 'Summer Special Offer',
        'sent_date' => '2023-07-05 11:15:00',
        'recipients' => 9,
        'opens' => 80,
        'clicks' => 55,
        'unsubscribes' => 0
    ]
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Process subscriber deletion
    if ($action === 'delete_subscriber') {
        // In a real implementation, validate and delete from database
        // For now, just redirect with success message
        header('Location: admin-subscribers.php?success=deleted');
        exit;
    }
    
    // Process bulk actions
    if ($action === 'bulk_action') {
        $bulk_action = isset($_POST['bulk_action']) ? $_POST['bulk_action'] : '';
        $selected_ids = isset($_POST['selected_subscribers']) ? $_POST['selected_subscribers'] : [];
        
        if (!empty($bulk_action) && !empty($selected_ids)) {
            // Process different bulk actions
            switch ($bulk_action) {
                case 'activate':
                    // Activate selected subscribers
                    header('Location: admin-subscribers.php?success=bulk_activated');
                    break;
                case 'deactivate':
                    // Deactivate selected subscribers
                    header('Location: admin-subscribers.php?success=bulk_deactivated');
                    break;
                case 'delete':
                    // Delete selected subscribers
                    header('Location: admin-subscribers.php?success=bulk_deleted');
                    break;
                case 'export':
                    // Export selected subscribers
                    // This would trigger a CSV download in a real implementation
                    header('Location: admin-subscribers.php?success=exported');
                    break;
                case 'add_to_list':
                    // Add selected subscribers to a list
                    header('Location: admin-subscribers.php?success=added_to_list');
                    break;
            }
            exit;
        }
    }
    
    // Process adding a new subscriber
    if ($action === 'add_subscriber') {
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-subscribers.php?success=added');
        exit;
    }
    
    // Process adding a new list
    if ($action === 'add_list') {
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-subscribers.php?success=list_added');
        exit;
    }
}

// Generate status badge HTML
function getStatusBadge($status) {
    $badge_class = '';
    switch ($status) {
        case 'active':
            $badge_class = 'bg-success';
            break;
        case 'inactive':
            $badge_class = 'bg-secondary';
            break;
        case 'pending':
            $badge_class = 'bg-warning';
            break;
        case 'unsubscribed':
            $badge_class = 'bg-danger';
            break;
        case 'bounced':
            $badge_class = 'bg-dark';
            break;
        default:
            $badge_class = 'bg-info';
    }
    
    return '<span class="badge ' . $badge_class . '">' . ucfirst($status) . '</span>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribers - Backsure Global Support</title>
    <!-- Include your CSS files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
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
                    <li><a href="admin-subscribers.php" class="active"><i class="fas fa-envelope"></i> Subscribers</a></li>
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
                    <h1>Subscribers Management</h1>
                    <div class="breadcrumb">
                        <a href="admin-dashboard.php">Dashboard</a> / <span>Subscribers</span>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php if($_GET['success'] == 'deleted'): ?>
                    <strong>Success!</strong> Subscriber has been deleted successfully.
                    <?php elseif($_GET['success'] == 'bulk_activated'): ?>
                    <strong>Success!</strong> Selected subscribers have been activated.
                    <?php elseif($_GET['success'] == 'bulk_deactivated'): ?>
                    <strong>Success!</strong> Selected subscribers have been deactivated.
                    <?php elseif($_GET['success'] == 'bulk_deleted'): ?>
                    <strong>Success!</strong> Selected subscribers have been deleted.
                    <?php elseif($_GET['success'] == 'exported'): ?>
                    <strong>Success!</strong> Subscriber data has been exported successfully.
                    <?php elseif($_GET['success'] == 'added'): ?>
                    <strong>Success!</strong> New subscriber has been added successfully.
                    <?php elseif($_GET['success'] == 'added_to_list'): ?>
                    <strong>Success!</strong> Subscribers have been added to the selected list.
                    <?php elseif($_GET['success'] == 'list_added'): ?>
                    <strong>Success!</strong> New subscriber list has been created.
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

                <!-- Dashboard Summary Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Subscribers</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($subscribers); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Subscribers</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php 
                                            $activeCount = count(array_filter($subscribers, function($s) {
                                                return $s['status'] === 'active';
                                            }));
                                            echo $activeCount;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Lists</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($lists); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-list fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Engagement Rate</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">84%</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="content-body">
                    <!-- Tabs navigation -->
                    <ul class="nav nav-tabs" id="subscriberTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="subscribers-tab" data-bs-toggle="tab" data-bs-target="#subscribers" type="button" role="tab" aria-controls="subscribers" aria-selected="true">
                                Subscribers List
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="lists-tab" data-bs-toggle="tab" data-bs-target="#lists" type="button" role="tab" aria-controls="lists" aria-selected="false">
                                Subscription Lists
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab" aria-controls="analytics" aria-selected="false">
                                Analytics & Reports
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content" id="subscriberTabsContent">
                        <!-- Subscribers Tab -->
                        <div class="tab-pane fade show active" id="subscribers" role="tabpanel" aria-labelledby="subscribers-tab">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3>Email Subscribers</h3>
                                    <div class="card-actions">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubscriberModal">
                                            <i class="fas fa-plus"></i> Add Subscriber
                                        </button>
                                        <div class="btn-group ms-2">
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-file-export"></i> Export
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#" id="exportAllCSV">Export All as CSV</a></li>
                                                <li><a class="dropdown-item" href="#" id="exportActiveCSV">Export Active Subscribers</a></li>
                                                <li><a class="dropdown-item" href="#" id="exportSelectedCSV">Export Selected</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="#" id="exportExcel">Export as Excel</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Filter Controls -->
                                    <div class="row mb-4">
                                        <div class="col-md-3 mb-2">
                                            <select class="form-select" id="statusFilter">
                                                <option value="">All Statuses</option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="pending">Pending</option>
                                                <option value="unsubscribed">Unsubscribed</option>
                                                <option value="bounced">Bounced</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <select class="form-select" id="listFilter">
                                                <option value="">All Lists</option>
                                                <?php foreach($lists as $list): ?>
                                                <option value="<?php echo $list['name']; ?>"><?php echo $list['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="dateRangeFilter" placeholder="Date Range">
                                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="searchSubscriber" placeholder="Search...">
                                                <button class="btn btn-outline-secondary" type="button">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bulk Actions -->
                                    <form action="admin-subscribers.php" method="post" id="subscribersForm">
                                        <input type="hidden" name="action" value="bulk_action">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="btn-group">
                                                    <select class="form-select" name="bulk_action" id="bulkAction">
                                                        <option value="">Bulk Actions</option>
                                                        <option value="activate">Activate</option>
                                                        <option value="deactivate">Deactivate</option>
                                                        <option value="add_to_list">Add to List</option>
                                                        <option value="export">Export Selected</option>
                                                        <option value="delete">Delete</option>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-secondary" id="applyBulkAction">Apply</button>
                                                </div>
                                                <div class="list-select-dropdown d-none mt-2" id="listSelectDropdown">
                                                    <select class="form-select" name="list_id">
                                                        <?php foreach($lists as $list): ?>
                                                        <option value="<?php echo $list['id']; ?>"><?php echo $list['name']; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-outline-secondary" id="selectAll">Select All</button>
                                                    <button type="button" class="btn btn-outline-secondary" id="deselectAll">Deselect All</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Subscribers Table -->
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th width="3%"><input type="checkbox" id="checkAll"></th>
                                                        <th width="5%">ID</th>
                                                        <th width="20%">Email</th>
                                                        <th width="15%">Name</th>
                                                        <th width="10%">Status</th>
                                                        <th width="12%">Source</th>
                                                        <th width="15%">Signup Date</th>
                                                        <th width="10%">Lists</th>
                                                        <th width="10%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($subscribers as $subscriber): ?>
                                                    <tr data-status="<?php echo $subscriber['status']; ?>" data-lists="<?php echo implode(',', $subscriber['lists']); ?>">
                                                        <td><input type="checkbox" name="selected_subscribers[]" value="<?php echo $subscriber['id']; ?>" class="subscriber-checkbox"></td>
                                                        <td><?php echo $subscriber['id']; ?></td>
                                                        <td><?php echo $subscriber['email']; ?></td>
                                                        <td><?php echo $subscriber['name']; ?></td>
                                                        <td><?php echo getStatusBadge($subscriber['status']); ?></td>
                                                        <td><?php echo $subscriber['source']; ?></td>
                                                        <td><?php echo $subscriber['signup_date']; ?></td>
                                                        <td>
                                                            <?php foreach($subscriber['lists'] as $list): ?>
                                                            <span class="badge bg-info"><?php echo $list; ?></span>
                                                            <?php endforeach; ?>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <button type="button" class="btn btn-primary view-subscriber" data-bs-toggle="modal" data-bs-target="#viewSubscriberModal" data-subscriber-id="<?php echo $subscriber['id']; ?>">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-success edit-subscriber" data-bs-toggle="modal" data-bs-target="#editSubscriberModal" data-subscriber-id="<?php echo $subscriber['id']; ?>">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-danger delete-subscriber" data-subscriber-id="<?php echo $subscriber['id']; ?>">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>

                                    <!-- Pagination -->
                                    <nav aria-label="Subscribers pagination" class="mt-4">
                                        <ul class="pagination justify-content-center">
                                            <li class="page-item disabled">
                                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                                            </li>
                                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                                            <li class="page-item">
                                                <a class="page-link" href="#">Next</a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>

                        <!-- Lists Tab -->
                        <div class="tab-pane fade" id="lists" role="tabpanel" aria-labelledby="lists-tab">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3>Subscription Lists</h3>
                                    <div class="card-actions">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addListModal">
                                            <i class="fas fa-plus"></i> Add New List
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach($lists as $list): ?>
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 <?php echo ($list['status'] == 'inactive') ? 'bg-light' : ''; ?>">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="card-title mb-0"><?php echo $list['name']; ?></h5>
                                                        <span class="badge bg-<?php echo ($list['status'] == 'active') ? 'success' : 'secondary'; ?>">
                                                            <?php echo ucfirst($list['status']); ?>
                                                        </span>
                                                    </div>
                                                    <p class="card-text"><?php echo $list['description']; ?></p>
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <div>
                                                            <span class="badge bg-primary"><?php echo $list['subscribers_count']; ?> Subscribers</span>
                                                            <small class="text-muted ms-2">Created: <?php echo $list['created_at']; ?></small>
                                                        </div>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary view-list-subscribers" data-list-id="<?php echo $list['id']; ?>" data-list-name="<?php echo $list['name']; ?>">
                                                                <i class="fas fa-users"></i> View Subscribers
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-secondary edit-list" data-list-id="<?php echo $list['id']; ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- List Management Tips Card -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h3>List Management Tips</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="tips-card">
                                                <i class="fas fa-users-cog fa-3x mb-3 text-primary"></i>
                                                <h5>Segment Your Subscribers</h5>
                                                <p>Create targeted lists based on subscriber interests, behavior, or demographics to improve engagement.</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="tips-card">
                                                <i class="fas fa-broom fa-3x mb-3 text-warning"></i>
                                                <h5>Regular List Cleaning</h5>
                                                <p>Remove inactive subscribers periodically to maintain high deliverability rates and accurate engagement metrics.</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="tips-card">
                                                <i class="fas fa-chart-pie fa-3x mb-3 text-success"></i>
                                                <h5>Analyze Performance</h5>
                                                <p>Track open rates, click rates, and engagement metrics for each list to optimize your email marketing strategy.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics Tab -->
                        <div class="tab-pane fade" id="analytics" role="tabpanel" aria-labelledby="analytics-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Subscriber Growth</h3>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="growthChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3>Subscription Sources</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-container">
                                                <canvas id="sourceChart" height="200"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3>Subscriber Status</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-container">
                                                <canvas id="statusChart" height="200"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Campaigns Performance -->
                            <div class="card mt-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3>Recent Campaigns Performance</h3>
                                    <a href="#" class="btn btn-sm btn-outline-primary">View All Campaigns</a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Campaign Name</th>
                                                    <th>Sent Date</th>
                                                    <th>Recipients</th>
                                                    <th>Open Rate</th>
                                                    <th>Click Rate</th>
                                                    <th>Unsubscribes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($campaigns as $campaign): ?>
                                                <tr>
                                                    <td><?php echo $campaign['name']; ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($campaign['sent_date'])); ?></td>
                                                    <td><?php echo $campaign['recipients']; ?></td>
                                                    <td>
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $campaign['opens']; ?>%;" aria-valuenow="<?php echo $campaign['opens']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small><?php echo $campaign['opens']; ?>%</small>
                                                    </td>
                                                    <td>
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $campaign['clicks']; ?>%;" aria-valuenow="<?php echo $campaign['clicks']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small><?php echo $campaign['clicks']; ?>%</small>
                                                    </td>
                                                    <td><?php echo $campaign['unsubscribes']; ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Export Reports Card -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h3>Export Reports</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                                                    <h5>Subscriber Report</h5>
                                                    <p>Complete list of subscribers with status, sources, and list memberships.</p>
                                                    <button class="btn btn-sm btn-outline-primary">Export</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-chart-line fa-3x mb-3 text-success"></i>
                                                    <h5>Growth Report</h5>
                                                    <p>Monthly subscription growth and churn rates over the past year.</p>
                                                    <button class="btn btn-sm btn-outline-success">Export</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-chart-pie fa-3x mb-3 text-warning"></i>
                                                    <h5>Engagement Report</h5>
                                                    <p>Open rates, click rates, and engagement metrics across campaigns.</p>
                                                    <button class="btn btn-sm btn-outline-warning">Export</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Subscriber Modal -->
    <div class="modal fade" id="addSubscriberModal" tabindex="-1" aria-labelledby="addSubscriberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSubscriberModalLabel">Add New Subscriber</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin-subscribers.php" method="post">
                    <input type="hidden" name="action" value="add_subscriber">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="subscriberEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="subscriberEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="subscriberName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="subscriberName" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="subscriberSource" class="form-label">Source</label>
                            <select class="form-select" id="subscriberSource" name="source">
                                <option value="Manual Entry">Manual Entry</option>
                                <option value="Homepage Form">Homepage Form</option>
                                <option value="Blog Sidebar">Blog Sidebar</option>
                                <option value="Footer Form">Footer Form</option>
                                <option value="Contact Form">Contact Form</option>
                                <option value="Whitepaper Download">Whitepaper Download</option>
                                <option value="Event Registration">Event Registration</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="subscriberStatus" class="form-label">Status</label>
                            <select class="form-select" id="subscriberStatus" name="status">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Add to Lists</label>
                            <div class="list-checkboxes">
                                <?php foreach($lists as $list): ?>
                                <?php if($list['status'] === 'active'): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="list<?php echo $list['id']; ?>" name="lists[]" value="<?php echo $list['id']; ?>" <?php echo ($list['name'] === 'Newsletter') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="list<?php echo $list['id']; ?>">
                                        <?php echo $list['name']; ?>
                                    </label>
                                </div>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="sendWelcome" name="send_welcome" value="1" checked>
                            <label class="form-check-label" for="sendWelcome">
                                Send welcome email to new subscriber
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Subscriber</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Subscriber Modal -->
    <div class="modal fade" id="editSubscriberModal" tabindex="-1" aria-labelledby="editSubscriberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSubscriberModalLabel">Edit Subscriber</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin-subscribers.php" method="post">
                    <input type="hidden" name="action" value="update_subscriber">
                    <input type="hidden" name="subscriber_id" id="editSubscriberId" value="">
                    <div class="modal-body">
                        <!-- Same fields as Add Subscriber Modal -->
                        <div class="mb-3">
                            <label for="editSubscriberEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="editSubscriberEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editSubscriberName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editSubscriberName" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="editSubscriberSource" class="form-label">Source</label>
                            <select class="form-select" id="editSubscriberSource" name="source">
                                <option value="Manual Entry">Manual Entry</option>
                                <option value="Homepage Form">Homepage Form</option>
                                <option value="Blog Sidebar">Blog Sidebar</option>
                                <option value="Footer Form">Footer Form</option>
                                <option value="Contact Form">Contact Form</option>
                                <option value="Whitepaper Download">Whitepaper Download</option>
                                <option value="Event Registration">Event Registration</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editSubscriberStatus" class="form-label">Status</label>
                            <select class="form-select" id="editSubscriberStatus" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending</option>
                                <option value="unsubscribed">Unsubscribed</option>
                                <option value="bounced">Bounced</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subscription Lists</label>
                            <div class="edit-list-checkboxes">
                                <?php foreach($lists as $list): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="editList<?php echo $list['id']; ?>" name="lists[]" value="<?php echo $list['id']; ?>">
                                    <label class="form-check-label" for="editList<?php echo $list['id']; ?>">
                                        <?php echo $list['name']; ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
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

    <!-- View Subscriber Modal -->
    <div class="modal fade" id="viewSubscriberModal" tabindex="-1" aria-labelledby="viewSubscriberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewSubscriberModalLabel">Subscriber Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="subscriber-profile mb-4">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="subscriber-avatar mb-3">
                                    <img src="assets/images/avatar-placeholder.jpg" alt="Subscriber" class="rounded-circle" width="100" height="100">
                                </div>
                                <h5 id="viewSubscriberName"></h5>
                                <p id="viewSubscriberEmail" class="text-muted"></p>
                                <div id="viewSubscriberStatus"></div>
                            </div>
                            <div class="col-md-8">
                                <h6>Subscription Details</h6>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Source:</div>
                                    <div class="col-md-8" id="viewSubscriberSource"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Signup Date:</div>
                                    <div class="col-md-8" id="viewSubscriberSignupDate"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Last Activity:</div>
                                    <div class="col-md-8" id="viewSubscriberLastActivity"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Lists:</div>
                                    <div class="col-md-8" id="viewSubscriberLists"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <ul class="nav nav-tabs" id="subscriberDetailTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab" aria-controls="activity" aria-selected="true">
                                Activity
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="campaigns-tab" data-bs-toggle="tab" data-bs-target="#campaigns" type="button" role="tab" aria-controls="campaigns" aria-selected="false">
                                Campaigns
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="engagement-tab" data-bs-toggle="tab" data-bs-target="#engagement" type="button" role="tab" aria-controls="engagement" aria-selected="false">
                                Engagement
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="subscriberDetailTabsContent">
                        <div class="tab-pane fade show active" id="activity" role="tabpanel" aria-labelledby="activity-tab">
                            <div class="p-3">
                                <h6>Recent Activity</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <span class="badge bg-info">OPEN</span>
                                        <span class="ms-2">Opened "October Newsletter" - Nov 10, 2023</span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge bg-primary">CLICK</span>
                                        <span class="ms-2">Clicked "View Report" in "Industry Report 2023" - Nov 5, 2023</span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge bg-info">OPEN</span>
                                        <span class="ms-2">Opened "Industry Report 2023" - Nov 5, 2023</span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge bg-success">JOIN</span>
                                        <span class="ms-2">Added to "Industry Insights" list - Oct 15, 2023</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="campaigns" role="tabpanel" aria-labelledby="campaigns-tab">
                            <div class="p-3">
                                <h6>Campaign History</h6>
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Campaign</th>
                                            <th>Sent Date</th>
                                            <th>Opened</th>
                                            <th>Clicked</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>October Newsletter</td>
                                            <td>Nov 10, 2023</td>
                                            <td><i class="fas fa-check text-success"></i></td>
                                            <td><i class="fas fa-times text-danger"></i></td>
                                        </tr>
                                        <tr>
                                            <td>Industry Report 2023</td>
                                            <td>Nov 5, 2023</td>
                                            <td><i class="fas fa-check text-success"></i></td>
                                            <td><i class="fas fa-check text-success"></i></td>
                                        </tr>
                                        <tr>
                                            <td>New Service Announcement</td>
                                            <td>Sep 15, 2023</td>
                                            <td><i class="fas fa-check text-success"></i></td>
                                            <td><i class="fas fa-check text-success"></i></td>
                                        </tr>
                                        <tr>
                                            <td>Summer Special Offer</td>
                                            <td>Jul 5, 2023</td>
                                            <td><i class="fas fa-times text-danger"></i></td>
                                            <td><i class="fas fa-times text-danger"></i></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="engagement" role="tabpanel" aria-labelledby="engagement-tab">
                            <div class="p-3">
                                <h6>Engagement Metrics</h6>
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <h2 id="viewSubscriberOpens">0</h2>
                                        <p>Total Opens</p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h2 id="viewSubscriberClicks">0</h2>
                                        <p>Total Clicks</p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <h2 id="viewSubscriberRate">0%</h2>
                                        <p>Click Rate</p>
                                    </div>
                                </div>
                                <div class="engagement-chart mt-3">
                                    <canvas id="subscriberEngagementChart" height="150"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary edit-from-view">Edit Subscriber</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add List Modal -->
    <div class="modal fade" id="addListModal" tabindex="-1" aria-labelledby="addListModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addListModalLabel">Add New List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin-subscribers.php" method="post">
                    <input type="hidden" name="action" value="add_list">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="listName" class="form-label">List Name</label>
                            <input type="text" class="form-control" id="listName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="listDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="listDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="listStatus" class="form-label">Status</label>
                            <select class="form-select" id="listStatus" name="status">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="addAllSubscribers" name="add_all_subscribers" value="1">
                            <label class="form-check-label" for="addAllSubscribers">
                                Add all active subscribers to this list
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create List</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View List Subscribers Modal -->
    <div class="modal fade" id="viewListSubscribersModal" tabindex="-1" aria-labelledby="viewListSubscribersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewListSubscribersModalLabel">Subscribers in <span id="listNameDisplay"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="listSubscribersTable">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Signup Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary export-list-subscribers">Export List Subscribers</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this subscriber? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="admin-subscribers.php" method="post">
                        <input type="hidden" name="action" value="delete_subscriber">
                        <input type="hidden" name="subscriber_id" id="deleteSubscriberId" value="">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Action Confirmation Modal -->
    <div class="modal fade" id="bulkActionConfirmModal" tabindex="-1" aria-labelledby="bulkActionConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkActionConfirmModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="bulkActionConfirmText">Are you sure you want to perform this action on the selected subscribers?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmBulkAction">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin-dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
    $(document).ready(function() {
        // Toggle sidebar on mobile
        $('.toggle-sidebar').click(function() {
            $('.admin-sidebar').toggleClass('active');
        });
        
        // Check/Uncheck all checkboxes
        $('#checkAll').change(function() {
            $('.subscriber-checkbox').prop('checked', $(this).prop('checked'));
        });
        
        // Select All button
        $('#selectAll').click(function() {
            $('.subscriber-checkbox').prop('checked', true);
            $('#checkAll').prop('checked', true);
        });
        
        // Deselect All button
        $('#deselectAll').click(function() {
            $('.subscriber-checkbox').prop('checked', false);
            $('#checkAll').prop('checked', false);
        });
        
        // Subscriber checkbox change
        $('.subscriber-checkbox').change(function() {
            if ($('.subscriber-checkbox:checked').length === $('.subscriber-checkbox').length) {
                $('#checkAll').prop('checked', true);
            } else {
                $('#checkAll').prop('checked', false);
            }
        });
        
        // Date range picker initialization
        $('#dateRangeFilter').daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });
        
        $('#dateRangeFilter').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            filterSubscribers();
        });
        
        $('#dateRangeFilter').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            filterSubscribers();
        });
        
        // Status filter change
        $('#statusFilter').change(function() {
            filterSubscribers();
        });
        
        // List filter change
        $('#listFilter').change(function() {
            filterSubscribers();
        });
        
        // Search functionality
        $('#searchSubscriber').on('input', function() {
            filterSubscribers();
        });
        
        // Combined filter function
        function filterSubscribers() {
            const statusFilter = $('#statusFilter').val();
            const listFilter = $('#listFilter').val();
            const searchTerm = $('#searchSubscriber').val().toLowerCase();
            const dateRange = $('#dateRangeFilter').val();
            
            $('tbody tr').each(function() {
                let showRow = true;
                
                // Check status filter
                if (statusFilter && $(this).data('status') !== statusFilter) {
                    showRow = false;
                }
                
                // Check list filter
                if (listFilter && showRow) {
                    const lists = $(this).data('lists').split(',');
                    if (!lists.includes(listFilter)) {
                        showRow = false;
                    }
                }
                
                // Check search term
                if (searchTerm && showRow) {
                    const rowText = $(this).text().toLowerCase();
                    if (!rowText.includes(searchTerm)) {
                        showRow = false;
                    }
                }
                
                // Check date range (this would need more complex implementation in real usage)
                if (dateRange && showRow) {
                    // In a real implementation, you would parse the dates and check if within range
                    // For this demo, we'll just show all rows when date range is set
                    showRow = true;
                }
                
                $(this)[showRow ? 'show' : 'hide']();
            });
        }
        
        // Show/hide list dropdown based on bulk action selection
        $('#bulkAction').change(function() {
            if ($(this).val() === 'add_to_list') {
                $('#listSelectDropdown').removeClass('d-none');
            } else {
                $('#listSelectDropdown').addClass('d-none');
            }
        });
        
        // Apply bulk action button
        $('#applyBulkAction').click(function() {
            const selectedAction = $('#bulkAction').val();
            const selectedCount = $('.subscriber-checkbox:checked').length;
            
            if (!selectedAction) {
                alert('Please select an action to perform.');
                return;
            }
            
            if (selectedCount === 0) {
                alert('Please select at least one subscriber.');
                return;
            }
            
            // Set confirmation message based on action
            let confirmText = 'Are you sure you want to ';
            switch (selectedAction) {
                case 'activate':
                    confirmText += 'activate';
                    break;
                case 'deactivate':
                    confirmText += 'deactivate';
                    break;
                case 'delete':
                    confirmText += 'delete';
                    break;
                case 'add_to_list':
                    const listName = $('#listSelectDropdown select option:selected').text();
                    confirmText += 'add to the "' + listName + '" list';
                    break;
                case 'export':
                    confirmText += 'export';
                    break;
            }
            confirmText += ' the selected ' + selectedCount + ' subscribers?';
            
            // Show confirmation modal
            $('#bulkActionConfirmText').text(confirmText);
            $('#bulkActionConfirmModal').modal('show');
        });
        
        // Confirm bulk action button
        $('#confirmBulkAction').click(function() {
            $('#bulkActionConfirmModal').modal('hide');
            // Submit the form
            $('#subscribersForm').submit();
        });
        
        // Delete subscriber button
        $('.delete-subscriber').click(function() {
            const subscriberId = $(this).data('subscriber-id');
            $('#deleteSubscriberId').val(subscriberId);
            $('#deleteConfirmModal').modal('show');
        });
        
        // View subscriber details
        $('.view-subscriber').click(function() {
            const subscriberId = $(this).data('subscriber-id');
            
            // In a real implementation, you would make an AJAX call to get the subscriber details
            // For this example, we'll use our mock data
            <?php echo "const subscribers = " . json_encode($subscribers) . ";"; ?>
            
            const subscriber = subscribers.find(s => s.id == subscriberId);
            
            if (subscriber) {
                $('#viewSubscriberName').text(subscriber.name);
                $('#viewSubscriberEmail').text(subscriber.email);
                $('#viewSubscriberStatus').html(getStatusBadgeHTML(subscriber.status));
                $('#viewSubscriberSource').text(subscriber.source);
                $('#viewSubscriberSignupDate').text(subscriber.signup_date);
                $('#viewSubscriberLastActivity').text(subscriber.last_activity || 'No activity recorded');
                
                // Set lists
                let listsHTML = '';
                subscriber.lists.forEach(list => {
                    listsHTML += '<span class="badge bg-info me-1">' + list + '</span>';
                });
                $('#viewSubscriberLists').html(listsHTML);
                
                // Set engagement metrics
                $('#viewSubscriberOpens').text(subscriber.opens);
                $('#viewSubscriberClicks').text(subscriber.clicks);
                
                // Calculate click rate
                const clickRate = subscriber.opens > 0 ? Math.round((subscriber.clicks / subscriber.opens) * 100) : 0;
                $('#viewSubscriberRate').text(clickRate + '%');
                
                // Initialize engagement chart
                initEngagementChart(subscriber);
            }
        });
        
        // Helper function to generate status badge HTML
        function getStatusBadgeHTML(status) {
            let badgeClass = '';
            switch (status) {
                case 'active':
                    badgeClass = 'bg-success';
                    break;
                case 'inactive':
                    badgeClass = 'bg-secondary';
                    break;
                case 'pending':
                    badgeClass = 'bg-warning';
                    break;
                case 'unsubscribed':
                    badgeClass = 'bg-danger';
                    break;
                case 'bounced':
                    badgeClass = 'bg-dark';
                    break;
                default:
                    badgeClass = 'bg-info';
            }
            
            return '<span class="badge ' + badgeClass + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
        }
        
        // Edit from view button
        $('.edit-from-view').click(function() {
            // Close the view modal
            $('#viewSubscriberModal').modal('hide');
            
            // Get the subscriber ID from the view modal data
            const subscriberId = $('#viewSubscriberModal').find('.view-subscriber').data('subscriber-id');
            
            // Open the edit modal after a short delay
            setTimeout(function() {
                $('.edit-subscriber[data-subscriber-id="' + subscriberId + '"]').trigger('click');
            }, 500);
        });
        
        // Edit subscriber button
        $('.edit-subscriber').click(function() {
            const subscriberId = $(this).data('subscriber-id');
            
            // In a real implementation, you would make an AJAX call to get the subscriber details
            // For this example, we'll use our mock data
            <?php echo "const subscribers = " . json_encode($subscribers) . ";"; ?>
            
            const subscriber = subscribers.find(s => s.id == subscriberId);
            
            if (subscriber) {
                $('#editSubscriberId').val(subscriber.id);
                $('#editSubscriberEmail').val(subscriber.email);
                $('#editSubscriberName').val(subscriber.name);
                $('#editSubscriberSource').val(subscriber.source);
                $('#editSubscriberStatus').val(subscriber.status);
                
                // Set list checkboxes
                $('.edit-list-checkboxes input[type="checkbox"]').prop('checked', false);
                subscriber.lists.forEach(list => {
                    $('.edit-list-checkboxes input[type="checkbox"]').each(function() {
                        if ($(this).siblings('label').text().trim() === list) {
                            $(this).prop('checked', true);
                        }
                    });
                });
            }
        });
        
        // View list subscribers button
        $('.view-list-subscribers').click(function() {
            const listId = $(this).data('list-id');
            const listName = $(this).data('list-name');
            
            // Set the list name in the modal title
            $('#listNameDisplay').text(listName);
            
            // In a real implementation, you would make an AJAX call to get the subscribers of this list
            // For this example, we'll use our mock data and filter by the list
            <?php echo "const subscribers = " . json_encode($subscribers) . ";"; ?>
            
            const listSubscribers = subscribers.filter(s => s.lists.includes(listName));
            
            // Populate the table
            let tableHTML = '';
            listSubscribers.forEach(s => {
                tableHTML += `
                    <tr>
                        <td>${s.email}</td>
                        <td>${s.name}</td>
                        <td>${getStatusBadgeHTML(s.status)}</td>
                        <td>${s.signup_date}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-primary view-subscriber-from-list" data-subscriber-id="${s.id}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-danger remove-from-list" data-subscriber-id="${s.id}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            if (listSubscribers.length === 0) {
                tableHTML = '<tr><td colspan="5" class="text-center">No subscribers in this list</td></tr>';
            }
            
            $('#listSubscribersTable tbody').html(tableHTML);
            
            // Show the modal
            $('#viewListSubscribersModal').modal('show');
        });
        
        // Export buttons
        $('#exportAllCSV, #exportActiveCSV, #exportSelectedCSV, #exportExcel').click(function(e) {
            e.preventDefault();
            // In a real implementation, this would trigger an AJAX call to generate and download the export file
            alert('Export functionality would be implemented here.');
        });
        
        // Initialize charts
        initGrowthChart();
        initSourceChart();
        initStatusChart();
        
        // Subscriber Growth Chart
        function initGrowthChart() {
            const ctx = document.getElementById('growthChart').getContext('2d');
            const growthChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'New Subscribers',
                        data: [5, 8, 12, 15, 10, 18, 20, 25, 17, 15, 22, 30],
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: '#fff',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
        
        // Subscription Sources Chart
        function initSourceChart() {
            const ctx = document.getElementById('sourceChart').getContext('2d');
            const sourceChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Homepage Form', 'Blog Sidebar', 'Footer Form', 'Contact Form', 'Whitepaper Download', 'Event Registration'],
                    datasets: [{
                        data: [30, 22, 18, 15, 10, 5],
                        backgroundColor: [
                            'rgba(78, 115, 223, 0.8)',
                            'rgba(28, 200, 138, 0.8)',
                            'rgba(54, 185, 204, 0.8)',
                            'rgba(246, 194, 62, 0.8)',
                            'rgba(231, 74, 59, 0.8)',
                            'rgba(133, 135, 150, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        }
        
        // Subscriber Status Chart
        function initStatusChart() {
            const ctx = document.getElementById('statusChart').getContext('2d');
            const statusChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Active', 'Inactive', 'Pending', 'Unsubscribed', 'Bounced'],
                    datasets: [{
                        label: 'Subscribers by Status',
                        data: [65, 15, 8, 7, 5],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(108, 117, 125, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(220, 53, 69, 0.8)',
                            'rgba(52, 58, 64, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // Individual Subscriber Engagement Chart
        function initEngagementChart(subscriber) {
            const ctx = document.getElementById('subscriberEngagementChart').getContext('2d');
            
            // If there's an existing chart, destroy it
            if (window.engagementChart) {
                window.engagementChart.destroy();
            }
            
            // Create a new chart
            window.engagementChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        {
                            label: 'Opens',
                            data: [2, 1, 0, 3, 4, 2],
                            borderColor: 'rgba(40, 167, 69, 1)',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            fill: true
                        },
                        {
                            label: 'Clicks',
                            data: [1, 0, 0, 2, 1, 1],
                            borderColor: 'rgba(0, 123, 255, 1)',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            fill: true
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    });
    </script>
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

// Mock data for subscribers
$subscribers = [
    [
        'id' => 1,
        'email' => 'john.smith@example.com',
        'name' => 'John Smith',
        'status' => 'active',
        'source' => 'Homepage Form',
        'signup_date' => '2023-05-10 09:23:45',
        'last_activity' => '2023-10-15 14:32:18',
        'lists' => ['Newsletter', 'Product Updates'],
        'opens' => 12,
        'clicks' => 5
    ],
    [
        'id' => 2,
        'email' => 'sarah.johnson@company.org',
        'name' => 'Sarah Johnson',
        'status' => 'active',
        'source' => 'Blog Sidebar',
        'signup_date' => '2023-06-22 15:47:32',
        'last_activity' => '2023-11-02 08:15:39',
        'lists' => ['Newsletter'],
        'opens' => 8,
        'clicks' => 3
    ],
    [
        'id' => 3,
        'email' => 'michael.williams@enterprise.net',
        'name' => 'Michael Williams',
        'status' => 'inactive',
        'source' => 'Footer Form',
        'signup_date' => '2023-07-14 11:09:27',
        'last_activity' => '2023-08-05 16:42:51',
        'lists' => ['Product Updates', 'Industry Insights'],
        'opens' => 2,
        'clicks' => 0
    ],
    [
        'id' => 4,
        'email' => 'emily.brown@example.com',
        'name' => 'Emily Brown',
        'status' => 'active',
        'source' => 'Whitepaper Download',
        'signup_date' => '2023-08-30 16:53:11',
        'last_activity' => '2023-11-10 10:28:43',
        'lists' => ['Newsletter', 'Industry Insights', 'Special Offers'],
        'opens' => 15,
        'clicks' => 9
    ],
    [
        'id' => 5,
        'email' => 'robert.jones@company.co',
        'name' => 'Robert Jones',
        'status' => 'unsubscribed',
        'source' => 'Event Registration',
        'signup_date' => '2023-04-05 14:22:38',
        'last_activity' => '2023-09-18 11:05:29',
        'lists' => ['Newsletter'],
        'opens' => 4,
        'clicks' => 1
    ],
    [
        'id' => 6,
        'email' => 'jennifer.miller@organization.edu',
        'name' => 'Jennifer Miller',
        'status' => 'active',
        'source' => 'Contact Form',
        'signup_date' => '2023-09-12 09:37:15',
        'last_activity' => '2023-11-08 15:47:22',
        'lists' => ['Newsletter', 'Special Offers'],
        'opens' => 7,
        'clicks' => 4
    ],
    [
        'id' => 7,
        'email' => 'david.davis@example.org',
        'name' => 'David Davis',
        'status' => 'bounced',
        'source' => 'Homepage Form',
        'signup_date' => '2023-05-28 13:45:01',
        'last_activity' => '2023-07-17 08:22:36',
        'lists' => ['Newsletter', 'Product Updates'],
        'opens' => 3,
        'clicks' => 0
    ],
    [
        'id' => 8,
        'email' => 'lisa.wilson@company.com',
        'name' => 'Lisa Wilson',
        'status' => 'active',
        'source' => 'Blog Sidebar',
        'signup_date' => '2023-10-03 10:19:53',
        'last_activity' => '2023-11-12 14:33:47',
        'lists' => ['Newsletter', 'Industry Insights'],
        'opens' => 6,
        'clicks' => 2
    ],
    [
        'id' => 9,
        'email' => 'james.taylor@enterprise.com',
        'name' => 'James Taylor',
        'status' => 'active',
        'source' => 'Whitepaper Download',
        'signup_date' => '2023-08-15 16:08:27',
        'last_activity' => '2023-11-05 09:17:31',
        'lists' => ['Newsletter', 'Product Updates', 'Industry Insights'],
        'opens' => 10,
        'clicks' => 6
    ],
    [
        'id' => 10,
        'email' => 'patricia.anderson@example.net',
        'name' => 'Patricia Anderson',
        'status' => 'pending',
        'source' => 'Footer Form',
        'signup_date' => '2023-11-01 14:52:19',
        'last_activity' => null,
        'lists' => ['Newsletter'],
        'opens' => 0,
        'clicks' => 0
    ],
    [
        'id' => 11,
        'email' => 'thomas.moore@organization.net',
        'name' => 'Thomas Moore',
        'status' => 'active',
        'source' => 'Event Registration',
        'signup_date' => '2023-07-25 08:41:32',
        'last_activity' => '2023-10-29 11:28:56',
        'lists' => ['Newsletter', 'Special Offers'],
        'opens' => 9,
        'clicks' => 4
    ],
    [
        'id' => 12,
        'email' => 'elizabeth.martin@company.org',
        'name' => 'Elizabeth Martin',
        'status' => 'inactive',
        'source' => 'Contact Form',
        'signup_date' => '2023-06-17 12:33:48',
        'last_activity' => '2023-08-22 15:09:22',
        'lists' => ['Newsletter', 'Industry Insights'],
        'opens' => 5,
        'clicks' => 1
    ]
];

// Mock data for newsletter lists
$lists = [
    [
        'id' => 1,
        'name' => 'Newsletter',
        'description' => 'Regular company news and updates',
        'subscribers_count' => 12,
        'status' => 'active',
        'created_at' => '2023-01-15'
    ],
    [
        'id' => 2,
        'name' => 'Product Updates',
        'description' => 'New product releases and feature announcements',
        'subscribers_count' => 7,
        'status' => 'active',
        'created_at' => '2023-02-22'
    ],
    [
        'id' => 3,
        'name' => 'Industry Insights',
        'description' => 'Industry news, analysis, and research reports',
        'subscribers_count' => 6,
        'status' => 'active',
        'created_at' => '2023-03-10'
    ],
    [
        'id' => 4,
        'name' => 'Special Offers',
        'description' => 'Exclusive deals, promotions, and discounts',
        'subscribers_count' => 5,
        'status' => 'active',
        'created_at' => '2023-04-05'
    ],
    [
        'id' => 5,
        'name' => 'Events & Webinars',
        'description' => 'Upcoming events, webinars, and conference information',
        'subscribers_count' => 0,
        'status' => 'inactive',
        'created_at' => '2023-05-20'
    ]
];

// Mock data for campaign summary
$campaigns = [
    [
        'id' => 1,
        'name' => 'October Newsletter',
        'sent_date' => '2023-10-10 09:00:00',
        'recipients' => 10,
        'opens' => 85,
        'clicks' => 35,
        'unsubscribes' => 1
    ],
    [
        'id' => 2,
        'name' => 'New Service Announcement',
        'sent_date' => '2023-09-15 10:30:00',
        'recipients' => 12,
        'opens' => 92,
        'clicks' => 45,
        'unsubscribes' => 0
    ],
    [
        'id' => 3,
        'name' => 'Industry Report 2023',
        'sent_date' => '2023-08-22 14:00:00',
        'recipients' => 8,
        'opens' => 75,
        'clicks' => 40,
        'unsubscribes' => 1
    ],
    [
        'id' => 4,
        'name' => 'Summer Special Offer',
        'sent_date' => '2023-07-05 11:15:00',
        'recipients' => 9,
        'opens' => 80,
        'clicks' => 55,
        'unsubscribes' => 0
    ]
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Process subscriber deletion
    if ($action === 'delete_subscriber') {
        // In a real implementation, validate and delete from database
        // For now, just redirect with success message
        header('Location: admin-subscribers.php?success=deleted');
        exit;
    }
    
    // Process bulk actions
    if ($action === 'bulk_action') {
        $bulk_action = isset($_POST['bulk_action']) ? $_POST['bulk_action'] : '';
        $selected_ids = isset($_POST['selected_subscribers']) ? $_POST['selected_subscribers'] : [];
        
        if (!empty($bulk_action) && !empty($selected_ids)) {
            // Process different bulk actions
            switch ($bulk_action) {
                case 'activate':
                    // Activate selected subscribers
                    header('Location: admin-subscribers.php?success=bulk_activated');
                    break;
                case 'deactivate':
                    // Deactivate selected subscribers
                    header('Location: admin-subscribers.php?success=bulk_deactivated');
                    break;
                case 'delete':
                    // Delete selected subscribers
                    header('Location: admin-subscribers.php?success=bulk_deleted');
                    break;
                case 'export':
                    // Export selected subscribers
                    // This would trigger a CSV download in a real implementation
                    header('Location: admin-subscribers.php?success=exported');
                    break;
                case 'add_to_list':
                    // Add selected subscribers to a list
                    header('Location: admin-subscribers.php?success=added_to_list');
                    break;
            }
            exit;
        }
    }
    
    // Process adding a new subscriber
    if ($action === 'add_subscriber') {
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-subscribers.php?success=added');
        exit;
    }
    
    // Process adding a new list
    if ($action === 'add_list') {
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-subscribers.php?success=list_added');
        exit;
    }
}

// Generate status badge HTML
function getStatusBadge($status) {
    $badge_class = '';
    switch ($status) {
        case 'active':
            $badge_class = 'bg-success';
            break;
        case 'inactive':
            $badge_class = 'bg-secondary';
            break;
        case 'pending':
            $badge_class = 'bg-warning';
            break;
        case 'unsubscribed':
            $badge_class = 'bg-danger';
            break;
        case 'bounced':
            $badge_class = 'bg-dark';
            break;
        default:
            $badge_class = 'bg-info';
    }
    
    return '<span class="badge ' . $badge_class . '">' . ucfirst($status) . '</span>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribers - Backsure Global Support</title>
    <!-- Include your CSS files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
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
                    <li><a href="admin-subscribers.php" class="active"><i class="fas fa-envelope"></i> Subscribers</a></li>
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
                    <h1>Subscribers Management</h1>
                    <div class="breadcrumb">
                        <a href="admin-dashboard.php">Dashboard</a> / <span>Subscribers</span>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php if($_GET['success'] == 'deleted'): ?>
                    <strong>Success!</strong> Subscriber has been deleted successfully.
                    <?php elseif($_GET['success'] == 'bulk_activated'): ?>
                    <strong>Success!</strong> Selected subscribers have been activated.
                    <?php elseif($_GET['success'] == 'bulk_deactivated'): ?>
                    <strong>Success!</strong> Selected subscribers have been deactivated.
                    <?php elseif($_GET['success'] == 'bulk_deleted'): ?>
                    <strong>Success!</strong> Selected subscribers have been deleted.
                    <?php elseif($_GET['success'] == 'exported'): ?>
                    <strong>Success!</strong> Subscriber data has been exported successfully.
                    <?php elseif($_GET['success'] == 'added'): ?>
                    <strong>Success!</strong> New subscriber has been added successfully.
                    <?php elseif($_GET['success'] == 'added_to_list'): ?>
                    <strong>Success!</strong> Subscribers have been added to the selected list.
                    <?php elseif($_GET['success'] == 'list_added'): ?>
                    <strong>Success!</strong> New subscriber list has been created.
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

                <!-- Dashboard Summary Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Subscribers</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($subscribers); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Subscribers</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php 
                                            $activeCount = count(array_filter($subscribers, function($s) {
                                                return $s['status'] === 'active';
                                            }));
                                            echo $activeCount;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Lists</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($lists); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-list fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Engagement Rate</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">84%</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="content-body">
                    <!-- Tabs navigation -->
                    <ul class="nav nav-tabs" id="subscriberTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="subscribers-tab" data-bs-toggle="tab" data-bs-target="#subscribers" type="button" role="tab" aria-controls="subscribers" aria-selected="true">
                                Subscribers List
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="lists-tab" data-bs-toggle="tab" data-bs-target="#lists" type="button" role="tab" aria-controls="lists" aria-selected="false">
                                Subscription Lists
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab" aria-controls="analytics" aria-selected="false">
                                Analytics & Reports
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content" id="subscriberTabsContent">
                        <!-- Subscribers Tab -->
                        <div class="tab-pane fade show active" id="subscribers" role="tabpanel" aria-labelledby="subscribers-tab">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3>Email Subscribers</h3>
                                    <div class="card-actions">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubscriberModal">
                                            <i class="fas fa-plus"></i> Add Subscriber
                                        </button>
                                        <div class="btn-group ms-2">
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-file-export"></i> Export
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#" id="exportAllCSV">Export All as CSV</a></li>
                                                <li><a class="dropdown-item" href="#" id="exportActiveCSV">Export Active Subscribers</a></li>
                                                <li><a class="dropdown-item" href="#" id="exportSelectedCSV">Export Selected</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="#" id="exportExcel">Export as Excel</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Filter Controls -->
                                    <div class="row mb-4">
                                        <div class="col-md-3 mb-2">
                                            <select class="form-select" id="statusFilter">
                                                <option value="">All Statuses</option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="pending">Pending</option>
                                                <option value="unsubscribed">Unsubscribed</option>
                                                <option value="bounced">Bounced</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <select class="form-select" id="listFilter">
                                                <option value="">All Lists</option>
                                                <?php foreach($lists as $list): ?>
                                                <option value="<?php echo $list['name']; ?>"><?php echo $list['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="dateRangeFilter" placeholder="Date Range">
                                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="searchSubscriber" placeholder="Search...">
                                                <button class="btn btn-outline-secondary" type="button">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bulk Actions -->
                                    <form action="admin-subscribers.php" method="post" id="subscribersForm">
                                        <input type="hidden" name="action" value="bulk_action">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="btn-group">
                                                    <select class="form-select" name="bulk_action" id="bulkAction">
                                                        <option value="">Bulk Actions</option>
                                                        <option value="activate">Activate</option>
                                                        <option value="deactivate">Deactivate</option>
                                                        <option value="add_to_list">Add to List</option>
                                                        <option value="export">Export Selected</option>
                                                        <option value="delete">Delete</option>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-secondary" id="applyBulkAction">Apply</button>
                                                </div>
                                                <div class="list-select-dropdown d-none mt-2" id="listSelectDropdown">
                                                    <select class="form-select" name="list_id">
                                                        <?php foreach($lists as $list): ?>
                                                        <option value="<?php echo $list['id']; ?>"><?php echo $list['name']; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-outline-secondary" id="selectAll">Select All</button>
                                                    <button type="button" class="btn btn-outline-secondary" id="deselectAll">Deselect All</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Subscribers Table -->
                                        <div class="table-responsive">
                                            <table class="table table-stripe