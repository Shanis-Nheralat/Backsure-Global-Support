<?php
// Initialize session if not already started
session_start();

// TODO: Add authentication check here
// if(!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
//     header("Location: admin-login.php");
//     exit;
// }

// TODO: Check if user has permission to access this page
// if($_SESSION['admin_role'] != 'Super Admin' && $_SESSION['admin_role'] != 'Marketing Admin') {
//     header("Location: admin-dashboard.php?error=unauthorized");
//     exit;
// }

// Mock data for FAQ categories
$categories = [
    [
        'id' => 1,
        'name' => 'General Questions',
        'slug' => 'general',
        'description' => 'Common questions about our services and company',
        'items_count' => 5,
        'display_order' => 1,
        'status' => 'active'
    ],
    [
        'id' => 2,
        'name' => 'Insurance Coverage',
        'slug' => 'insurance-coverage',
        'description' => 'Questions related to insurance coverage and policies',
        'items_count' => 8,
        'display_order' => 2,
        'status' => 'active'
    ],
    [
        'id' => 3,
        'name' => 'Claims Process',
        'slug' => 'claims',
        'description' => 'How to file and track claims',
        'items_count' => 6,
        'display_order' => 3,
        'status' => 'active'
    ],
    [
        'id' => 4,
        'name' => 'Billing & Payments',
        'slug' => 'billing',
        'description' => 'Questions about billing cycles, payment methods and invoices',
        'items_count' => 4,
        'display_order' => 4,
        'status' => 'active'
    ],
    [
        'id' => 5,
        'name' => 'Technical Support',
        'slug' => 'support',
        'description' => 'Help with account access and technical issues',
        'items_count' => 3,
        'display_order' => 5,
        'status' => 'inactive'
    ]
];

// Mock data for FAQ items
$faqs = [
    [
        'id' => 1,
        'question' => 'What services does Backsure Global offer?',
        'answer' => 'Backsure Global offers a comprehensive range of insurance and risk management services including property insurance, liability coverage, business interruption insurance, and specialized industry-specific solutions. Our services are tailored to meet the unique needs of businesses across various sectors.',
        'category_id' => 1,
        'category_name' => 'General Questions',
        'display_order' => 1,
        'status' => 'active',
        'created_at' => '2023-05-15',
        'updated_at' => '2023-10-10'
    ],
    [
        'id' => 2,
        'question' => 'How do I know which insurance coverage is right for my business?',
        'answer' => 'Our expert consultants conduct a thorough risk assessment of your business operations to determine the appropriate coverage. We take into account factors such as your industry, business size, location, and specific risks to recommend tailored insurance solutions. You can schedule a consultation with one of our advisors for personalized recommendations.',
        'category_id' => 2,
        'category_name' => 'Insurance Coverage',
        'display_order' => 1,
        'status' => 'active',
        'created_at' => '2023-05-18',
        'updated_at' => '2023-09-22'
    ],
    [
        'id' => 3,
        'question' => 'What information do I need to file a claim?',
        'answer' => 'To file a claim, you will need your policy number, details of the incident (date, time, location), a description of the loss or damage, any relevant photos or documentation, police report numbers (if applicable), and contact information for any witnesses. Our claims portal allows you to submit this information securely, and our claims team is available to assist you through the process.',
        'category_id' => 3,
        'category_name' => 'Claims Process',
        'display_order' => 1,
        'status' => 'active',
        'created_at' => '2023-06-10',
        'updated_at' => '2023-09-05'
    ],
    [
        'id' => 4,
        'question' => 'What payment methods do you accept?',
        'answer' => 'We accept various payment methods including credit/debit cards (Visa, Mastercard, American Express), bank transfers, checks, and automated clearing house (ACH) payments. For ongoing policies, you can set up automatic recurring payments for your convenience. All payments are processed securely through our encrypted payment system.',
        'category_id' => 4,
        'category_name' => 'Billing & Payments',
        'display_order' => 1,
        'status' => 'active',
        'created_at' => '2023-06-22',
        'updated_at' => '2023-08-15'
    ],
    [
        'id' => 5,
        'question' => 'How quickly are claims processed?',
        'answer' => 'Our commitment is to process claims efficiently and fairly. The timeline for processing depends on the complexity of the claim and the documentation provided. Simple claims may be processed within 3-5 business days, while more complex claims might take longer. Our claims team will keep you updated throughout the process and work to resolve your claim as quickly as possible.',
        'category_id' => 3,
        'category_name' => 'Claims Process',
        'display_order' => 2,
        'status' => 'active',
        'created_at' => '2023-07-05',
        'updated_at' => '2023-10-20'
    ],
    [
        'id' => 6,
        'question' => 'I forgot my account password. How can I reset it?',
        'answer' => 'You can reset your password by clicking on the "Forgot Password" link on the login page. Enter the email address associated with your account, and we will send you a secure link to create a new password. For security reasons, the link expires after 24 hours. If you don\'t receive the email, please check your spam folder or contact our technical support team.',
        'category_id' => 5,
        'category_name' => 'Technical Support',
        'display_order' => 1,
        'status' => 'active',
        'created_at' => '2023-07-12',
        'updated_at' => '2023-08-30'
    ]
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Process FAQ item creation/editing
    if ($action === 'save_faq') {
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-faq.php?success=faq_saved');
        exit;
    }
    
    // Process category creation/editing
    if ($action === 'save_category') {
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-faq.php?success=category_saved');
        exit;
    }
    
    // Process FAQ item deletion
    if ($action === 'delete_faq') {
        // In a real implementation, delete from database
        // For now, just redirect with success message
        header('Location: admin-faq.php?success=faq_deleted');
        exit;
    }
    
    // Process category deletion
    if ($action === 'delete_category') {
        // In a real implementation, delete from database
        // For now, just redirect with success message
        header('Location: admin-faq.php?success=category_deleted');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ Management - Backsure Global Support</title>
    <!-- Include your CSS files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css">
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
                            <li><a href="admin-faq.php" class="active">FAQ</a></li>
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
                    <h1>FAQ Management</h1>
                    <div class="breadcrumb">
                        <a href="admin-dashboard.php">Dashboard</a> / <span>FAQ Management</span>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php if($_GET['success'] == 'faq_saved'): ?>
                    <strong>Success!</strong> FAQ item has been saved successfully.
                    <?php elseif($_GET['success'] == 'category_saved'): ?>
                    <strong>Success!</strong> FAQ category has been saved successfully.
                    <?php elseif($_GET['success'] == 'faq_deleted'): ?>
                    <strong>Success!</strong> FAQ item has been deleted successfully.
                    <?php elseif($_GET['success'] == 'category_deleted'): ?>
                    <strong>Success!</strong> FAQ category has been deleted successfully.
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
                    <!-- Tabs navigation -->
                    <ul class="nav nav-tabs" id="faqTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="faqs-tab" data-bs-toggle="tab" data-bs-target="#faqs" type="button" role="tab" aria-controls="faqs" aria-selected="true">
                                FAQ Items
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories" type="button" role="tab" aria-controls="categories" aria-selected="false">
                                Categories
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">
                                FAQ Settings
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content" id="faqTabsContent">
                        <!-- FAQ Items Tab -->
                        <div class="tab-pane fade show active" id="faqs" role="tabpanel" aria-labelledby="faqs-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Frequently Asked Questions</h3>
                                    <div class="card-actions">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                                            <i class="fas fa-plus"></i> Add New FAQ
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- FAQ Filter Options -->
                                    <div class="row mb-4">
                                        <div class="col-md-3">
                                            <select class="form-select" id="categoryFilter">
                                                <option value="">All Categories</option>
                                                <?php foreach($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-select" id="statusFilter">
                                                <option value="">All Statuses</option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="searchFaq" placeholder="Search FAQs...">
                                                <button class="btn btn-outline-secondary" type="button">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-outline-secondary">
                                                    <i class="fas fa-sort-amount-down"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary">
                                                    <i class="fas fa-filter"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- FAQ Table -->
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="30%">Question</th>
                                                    <th width="20%">Category</th>
                                                    <th width="10%">Order</th>
                                                    <th width="15%">Last Updated</th>
                                                    <th width="10%">Status</th>
                                                    <th width="10%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($faqs as $faq): ?>
                                                <tr>
                                                    <td><?php echo $faq['id']; ?></td>
                                                    <td>
                                                        <a href="#" class="view-faq" data-bs-toggle="modal" data-bs-target="#viewFaqModal" data-faq-id="<?php echo $faq['id']; ?>">
                                                            <?php echo $faq['question']; ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo $faq['category_name']; ?></td>
                                                    <td>
                                                        <div class="order-controls">
                                                            <button class="btn btn-sm btn-light move-up" title="Move Up">
                                                                <i class="fas fa-arrow-up"></i>
                                                            </button>
                                                            <span class="mx-1"><?php echo $faq['display_order']; ?></span>
                                                            <button class="btn btn-sm btn-light move-down" title="Move Down">
                                                                <i class="fas fa-arrow-down"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $faq['updated_at']; ?></td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input status-toggle" type="checkbox" role="switch" id="statusSwitch<?php echo $faq['id']; ?>" <?php echo ($faq['status'] === 'active') ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="statusSwitch<?php echo $faq['id']; ?>">
                                                                <span class="badge bg-<?php echo ($faq['status'] === 'active') ? 'success' : 'secondary'; ?>">
                                                                    <?php echo ucfirst($faq['status']); ?>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-primary edit-faq" data-bs-toggle="modal" data-bs-target="#editFaqModal" data-faq-id="<?php echo $faq['id']; ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger delete-faq" data-faq-id="<?php echo $faq['id']; ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <nav aria-label="FAQ pagination" class="mt-4">
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

                        <!-- Categories Tab -->
                        <div class="tab-pane fade" id="categories" role="tabpanel" aria-labelledby="categories-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h3>FAQ Categories</h3>
                                    <div class="card-actions">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                            <i class="fas fa-plus"></i> Add New Category
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="20%">Name</th>
                                                    <th width="30%">Description</th>
                                                    <th width="10%">Slug</th>
                                                    <th width="10%">Items</th>
                                                    <th width="10%">Order</th>
                                                    <th width="5%">Status</th>
                                                    <th width="10%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($categories as $category): ?>
                                                <tr>
                                                    <td><?php echo $category['id']; ?></td>
                                                    <td><?php echo $category['name']; ?></td>
                                                    <td><?php echo $category['description']; ?></td>
                                                    <td><code><?php echo $category['slug']; ?></code></td>
                                                    <td><?php echo $category['items_count']; ?></td>
                                                    <td>
                                                        <div class="order-controls">
                                                            <button class="btn btn-sm btn-light move-up" title="Move Up">
                                                                <i class="fas fa-arrow-up"></i>
                                                            </button>
                                                            <span class="mx-1"><?php echo $category['display_order']; ?></span>
                                                            <button class="btn btn-sm btn-light move-down" title="Move Down">
                                                                <i class="fas fa-arrow-down"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php echo ($category['status'] === 'active') ? 'success' : 'secondary'; ?>">
                                                            <?php echo ucfirst($category['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-primary edit-category" data-bs-toggle="modal" data-bs-target="#editCategoryModal" data-category-id="<?php echo $category['id']; ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger delete-category" data-category-id="<?php echo $category['id']; ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings Tab -->
                        <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h3>FAQ Display Settings</h3>
                                </div>
                                <div class="card-body">
                                    <form action="admin-faq.php" method="post">
                                        <input type="hidden" name="action" value="save_settings">
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="faqPageTitle" class="form-label">FAQ Page Title</label>
                                                <input type="text" class="form-control" id="faqPageTitle" name="faq_page_title" value="Frequently Asked Questions - Backsure Global Support">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="faqPageSlug" class="form-label">FAQ Page Slug</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">https://backsure.com/</span>
                                                    <input type="text" class="form-control" id="faqPageSlug" name="faq_page_slug" value="faq">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="faqIntroText" class="form-label">FAQ Introduction Text</label>
                                                <textarea class="form-control" id="faqIntroText" name="faq_intro_text" rows="3">Find answers to commonly asked questions about our services, insurance coverage, claims processing, and more. If you can't find what you're looking for, please contact our support team.</textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="faqsPerPage" class="form-label">FAQs Per Page</label>
                                                <input type="number" class="form-control" id="faqsPerPage" name="faqs_per_page" value="10" min="5" max="50">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="defaultCategory" class="form-label">Default Category</label>
                                                <select class="form-select" id="defaultCategory" name="default_category">
                                                    <option value="0">All Categories</option>
                                                    <?php foreach($categories as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == 1) ? 'selected' : ''; ?>>
                                                        <?php echo $category['name']; ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="enableSearch" name="enable_search" checked>
                                                    <label class="form-check-label" for="enableSearch">Enable FAQ Search</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="enableFeedback" name="enable_feedback" checked>
                                                    <label class="form-check-label" for="enableFeedback">Enable "Was this helpful?" Feedback</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="displayStyle" class="form-label">FAQ Display Style</label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="display_style" id="styleAccordion" value="accordion" checked>
                                                        <label class="form-check-label" for="styleAccordion">
                                                            Accordion Style
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="display_style" id="styleTabbed" value="tabbed">
                                                        <label class="form-check-label" for="styleTabbed">
                                                            Tabbed Categories
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="display_style" id="styleExpanded" value="expanded">
                                                        <label class="form-check-label" for="styleExpanded">
                                                            Expanded List
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <label for="seoCategoryHeaders" class="form-label">SEO Heading Level for Categories</label>
                                                <select class="form-select" id="seoCategoryHeaders" name="seo_category_headers">
                                                    <option value="h2" selected>H2 Headers</option>
                                                    <option value="h3">H3 Headers</option>
                                                    <option value="h4">H4 Headers</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="seoQuestionHeaders" class="form-label">SEO Heading Level for Questions</label>
                                                <select class="form-select" id="seoQuestionHeaders" name="seo_question_headers">
                                                    <option value="h3" selected>H3 Headers</option>
                                                    <option value="h4">H4 Headers</option>
                                                    <option value="h5">H5 Headers</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary">Save Settings</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add FAQ Modal -->
    <div class="modal fade" id="addFaqModal" tabindex="-1" aria-labelledby="addFaqModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFaqModalLabel">Add New FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin-faq.php" method="post">
                    <input type="hidden" name="action" value="save_faq">
                    <input type="hidden" name="faq_id" value="0">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="faqQuestion" class="form-label">Question</label>
                            <input type="text" class="form-control" id="faqQuestion" name="question" required>
                        </div>
                        <div class="mb-3">
                            <label for="faqAnswer" class="form-label">Answer</label>
                            <textarea class="form-control summernote" id="faqAnswer" name="answer" rows="5" required></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="faqCategory" class="form-label">Category</label>
                                <select class="form-select" id="faqCategory" name="category_id" required>
                                    <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="faqOrder" class="form-label">Display Order</label>
                                <input type="number" class="form-control" id="faqOrder" name="display_order" value="1" min="1">
                            </div>
                            <div class="col-md-3">
                                <label for="faqStatus" class="form-label">Status</label>
                                <select class="form-select" id="faqStatus" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="faqTags" class="form-label">Tags (comma separated)</label>
                            <input type="text" class="form-control" id="faqTags" name="tags" placeholder="insurance, coverage, policy">
                            <small class="form-text">Tags help with search and categorization</small>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="faqFeatured" name="is_featured">
                            <label class="form-check-label" for="faqFeatured">
                                Feature this FAQ (will appear in featured FAQs section)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save FAQ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit FAQ Modal -->
    <div class="modal fade" id="editFaqModal" tabindex="-1" aria-labelledby="editFaqModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFaqModalLabel">Edit FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin-faq.php" method="post">
                    <input type="hidden" name="action" value="save_faq">
                    <input type="hidden" name="faq_id" id="editFaqId" value="">
                    <div class="modal-body">
                        <!-- Same fields as Add FAQ Modal -->
                        <div class="mb-3">
                            <label for="editFaqQuestion" class="form-label">Question</label>
                            <input type="text" class="form-control" id="editFaqQuestion" name="question" required>
                        </div>
                        <div class="mb-3">
                            <label for="editFaqAnswer" class="form-label">Answer</label>
                            <textarea class="form-control summernote" id="editFaqAnswer" name="answer" rows="5" required></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editFaqCategory" class="form-label">Category</label>
                                <select class="form-select" id="editFaqCategory" name="category_id" required>
                                    <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="editFaqOrder" class="form-label">Display Order</label>
                                <input type="number" class="form-control" id="editFaqOrder" name="display_order" value="1" min="1">
                            </div>
                            <div class="col-md-3">
                                <label for="editFaqStatus" class="form-label">Status</label>
                                <select class="form-select" id="editFaqStatus" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editFaqTags" class="form-label">Tags (comma separated)</label>
                            <input type="text" class="form-control" id="editFaqTags" name="tags" placeholder="insurance, coverage, policy">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="editFaqFeatured" name="is_featured">
                            <label class="form-check-label" for="editFaqFeatured">
                                Feature this FAQ
                            </label>
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

    <!-- View FAQ Modal -->
    <div class="modal fade" id="viewFaqModal" tabindex="-1" aria-labelledby="viewFaqModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewFaqModalLabel">FAQ Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="faq-details">
                        <h4 id="viewFaqQuestion"></h4>
                        <div class="faq-meta mb-3">
                            <span class="badge bg-primary me-2" id="viewFaqCategory"></span>
                            <span class="text-muted">Last updated: <span id="viewFaqUpdated"></span></span>
                        </div>
                        <div class="faq-answer mb-4" id="viewFaqAnswer">
                            <!-- Content will be loaded dynamically -->
                        </div>
                        <div class="faq-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> <span id="viewFaqStatus"></span></p>
                                    <p><strong>Display Order:</strong> <span id="viewFaqOrder"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Created:</strong> <span id="viewFaqCreated"></span></p>
                                    <p><strong>ID:</strong> <span id="viewFaqId"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary edit-from-view">Edit This FAQ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin-faq.php" method="post">
                    <input type="hidden" name="action" value="save_category">
                    <input type="hidden" name="category_id" value="0">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="categoryName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="categorySlug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="categorySlug" name="slug" required>
                            <small class="form-text">Used in URLs. Use lowercase letters, numbers, and hyphens only.</small>
                        </div>
                        <div class="mb-3">
                            <label for="categoryDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="categoryDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="categoryOrder" class="form-label">Display Order</label>
                                <input type="number" class="form-control" id="categoryOrder" name="display_order" value="1" min="1">
                            </div>
                            <div class="col-md-6">
                                <label for="categoryStatus" class="form-label">Status</label>
                                <select class="form-select" id="categoryStatus" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="showOnHomepage" name="show_on_homepage">
                            <label class="form-check-label" for="showOnHomepage">
                                Show on Homepage FAQ Section
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin-faq.php" method="post">
                    <input type="hidden" name="action" value="save_category">
                    <input type="hidden" name="category_id" id="editCategoryId" value="">
                    <div class="modal-body">
                        <!-- Same fields as Add Category Modal -->
                        <div class="mb-3">
                            <label for="editCategoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="editCategoryName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCategorySlug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="editCategorySlug" name="slug" required>
                            <small class="form-text">Used in URLs. Use lowercase letters, numbers, and hyphens only.</small>
                        </div>
                        <div class="mb-3">
                            <label for="editCategoryDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editCategoryDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editCategoryOrder" class="form-label">Display Order</label>
                                <input type="number" class="form-control" id="editCategoryOrder" name="display_order" value="1" min="1">
                            </div>
                            <div class="col-md-6">
                                <label for="editCategoryStatus" class="form-label">Status</label>
                                <select class="form-select" id="editCategoryStatus" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="editShowOnHomepage" name="show_on_homepage">
                            <label class="form-check-label" for="editShowOnHomepage">
                                Show on Homepage FAQ Section
                            </label>
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteConfirmText">Are you sure you want to delete this item? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="admin-faq.php" method="post" id="deleteForm">
                        <input type="hidden" name="action" id="deleteAction" value="">
                        <input type="hidden" name="item_id" id="deleteItemId" value="">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin-dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
    $(document).ready(function() {
        // Toggle sidebar on mobile
        $('.toggle-sidebar').click(function() {
            $('.admin-sidebar').toggleClass('active');
        });
        
        // Initialize Summernote WYSIWYG editor
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
        
        // Auto-generate slug from category name
        $('#categoryName').on('input', function() {
            var slug = $(this).val().toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
            $('#categorySlug').val(slug);
        });
        
        // View FAQ details
        $('.view-faq').click(function() {
            var faqId = $(this).data('faq-id');
            
            // In a real implementation, you would make an AJAX call to get the FAQ details
            // For this example, we'll use our mock data
            <?php echo "const faqs = " . json_encode($faqs) . ";"; ?>
            
            const faq = faqs.find(f => f.id == faqId);
            
            if (faq) {
                $('#viewFaqQuestion').text(faq.question);
                $('#viewFaqAnswer').html(faq.answer);
                $('#viewFaqCategory').text(faq.category_name);
                $('#viewFaqStatus').text(faq.status.charAt(0).toUpperCase() + faq.status.slice(1));
                $('#viewFaqOrder').text(faq.display_order);
                $('#viewFaqCreated').text(faq.created_at);
                $('#viewFaqUpdated').text(faq.updated_at);
                $('#viewFaqId').text(faq.id);
                
                // Set data attribute for the edit button
                $('.edit-from-view').data('faq-id', faq.id);
            }
        });
        
        // Edit from view modal
        $('.edit-from-view').click(function() {
            var faqId = $(this).data('faq-id');
            $('#viewFaqModal').modal('hide');
            
            // Trigger the edit modal with the same FAQ ID
            setTimeout(function() {
                $('.edit-faq[data-faq-id="' + faqId + '"]').trigger('click');
            }, 500);
        });
        
        // Edit FAQ
        $('.edit-faq').click(function() {
            var faqId = $(this).data('faq-id');
            
            // In a real implementation, you would make an AJAX call to get the FAQ details
            // For this example, we'll use our mock data
            <?php echo "const faqs = " . json_encode($faqs) . ";"; ?>
            
            const faq = faqs.find(f => f.id == faqId);
            
            if (faq) {
                $('#editFaqId').val(faq.id);
                $('#editFaqQuestion').val(faq.question);
                
                // Set Summernote content
                $('#editFaqAnswer').summernote('code', faq.answer);
                
                $('#editFaqCategory').val(faq.category_id);
                $('#editFaqOrder').val(faq.display_order);
                $('#editFaqStatus').val(faq.status);
                
                // Normally you would also set tags and featured status
                $('#editFaqTags').val('insurance, policy');  // Example tags, would come from database
                $('#editFaqFeatured').prop('checked', false);  // Example value, would come from database
            }
        });
        
        // Edit Category
        $('.edit-category').click(function() {
            var categoryId = $(this).data('category-id');
            
            // In a real implementation, you would make an AJAX call to get the category details
            // For this example, we'll use our mock data
            <?php echo "const categories = " . json_encode($categories) . ";"; ?>
            
            const category = categories.find(c => c.id == categoryId);
            
            if (category) {
                $('#editCategoryId').val(category.id);
                $('#editCategoryName').val(category.name);
                $('#editCategorySlug').val(category.slug);
                $('#editCategoryDescription').val(category.description);
                $('#editCategoryOrder').val(category.display_order);
                $('#editCategoryStatus').val(category.status);
                
                // Example value for homepage display, would come from database
                $('#editShowOnHomepage').prop('checked', category.id <= 3);
            }
        });
        
        // Delete FAQ confirmation
        $('.delete-faq').click(function() {
            var faqId = $(this).data('faq-id');
            $('#deleteAction').val('delete_faq');
            $('#deleteItemId').val(faqId);
            $('#deleteConfirmText').text('Are you sure you want to delete this FAQ? This action cannot be undone.');
            $('#deleteConfirmModal').modal('show');
        });
        
        // Delete Category confirmation
        $('.delete-category').click(function() {
            var categoryId = $(this).data('category-id');
            $('#deleteAction').val('delete_category');
            $('#deleteItemId').val(categoryId);
            $('#deleteConfirmText').text('Are you sure you want to delete this category? All FAQs in this category will be moved to the default category.');
            $('#deleteConfirmModal').modal('show');
        });
        
        // Status toggle for FAQ items
        $('.status-toggle').change(function() {
            const faqId = $(this).attr('id').replace('statusSwitch', '');
            const newStatus = $(this).prop('checked') ? 'active' : 'inactive';
            const badge = $(this).siblings('label').find('.badge');
            
            // Update the badge
            badge.removeClass('bg-success bg-secondary')
                 .addClass(newStatus === 'active' ? 'bg-success' : 'bg-secondary')
                 .text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
            
            // In a real implementation, you would make an AJAX call to update the status
            console.log(`FAQ ${faqId} status changed to ${newStatus}`);
        });
        
        // Category filter change
        $('#categoryFilter').change(function() {
            // Filter table rows based on category
            // In a real implementation, you might reload the page with a query parameter or use AJAX
            const categoryId = $(this).val();
            console.log(`Filtering by category: ${categoryId}`);
        });
        
        // Status filter change
        $('#statusFilter').change(function() {
            // Filter table rows based on status
            const status = $(this).val();
            console.log(`Filtering by status: ${status}`);
        });
        
        // Search FAQs
        $('#searchFaq').on('input', function() {
            // Search functionality
            const searchTerm = $(this).val().toLowerCase();
            console.log(`Searching for: ${searchTerm}`);
        });
        
        // Order controls
        $('.move-up, .move-down').click(function() {
            const direction = $(this).hasClass('move-up') ? 'up' : 'down';
            const row = $(this).closest('tr');
            
            // In a real implementation, you would make an AJAX call to update the order
            console.log(`Moving item ${direction}`);
            
            // Visually move the row for demonstration
            if (direction === 'up' && row.prev().length) {
                row.insertBefore(row.prev());
            } else if (direction === 'down' && row.next().length) {
                row.insertAfter(row.next());
            }
        });
    });
    </script>
</body>
</html>