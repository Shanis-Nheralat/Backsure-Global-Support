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

// Mock data for pages with SEO settings
$pages = [
    // Static Pages
    [
        'id' => 1,
        'type' => 'static',
        'name' => 'Homepage',
        'slug' => 'index',
        'meta_title' => 'Backsure Global Support | Insurance & Risk Management Solutions',
        'meta_description' => 'Backsure Global provides comprehensive insurance and risk management solutions for businesses across multiple industries. Expert advice, customized coverage.',
        'meta_keywords' => 'insurance, risk management, business insurance, global insurance solutions, corporate risk',
        'og_title' => 'Insurance & Risk Management Solutions | Backsure Global',
        'og_description' => 'Protect your business with tailored insurance and risk management solutions from Backsure Global.',
        'og_image' => 'assets/images/og-homepage.jpg',
        'canonical_url' => 'https://backsure.com/',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"Organization","name":"Backsure Global Support","url":"https://backsure.com","logo":"https://backsure.com/assets/images/logo.png"}',
        'last_updated' => '2023-09-15',
        'last_updated_by' => 'Admin User'
    ],
    [
        'id' => 2,
        'type' => 'static',
        'name' => 'About Us',
        'slug' => 'about',
        'meta_title' => 'About Backsure Global | Our History & Vision',
        'meta_description' => 'Learn about Backsure Global\'s history, our expert team, and our vision for transforming insurance and risk management for businesses worldwide.',
        'meta_keywords' => 'about backsure, insurance history, risk management company, insurance experts, global insurance vision',
        'og_title' => 'About Backsure Global | Expert Insurance Solutions',
        'og_description' => 'Discover how Backsure Global is revolutionizing business insurance and risk management.',
        'og_image' => 'assets/images/og-about.jpg',
        'canonical_url' => 'https://backsure.com/about',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"AboutPage","name":"About Backsure Global","url":"https://backsure.com/about"}',
        'last_updated' => '2023-08-20',
        'last_updated_by' => 'Admin User'
    ],
    [
        'id' => 3,
        'type' => 'static',
        'name' => 'Contact Us',
        'slug' => 'contact',
        'meta_title' => 'Contact Backsure Global | Get Expert Insurance Advice',
        'meta_description' => 'Reach out to Backsure Global for expert advice on business insurance and risk management solutions. Contact our team today.',
        'meta_keywords' => 'contact insurance company, business insurance contact, risk management advice, insurance consultation',
        'og_title' => 'Contact Our Insurance Experts | Backsure Global',
        'og_description' => 'Get in touch with Backsure Global for dedicated support and expert advice on all your insurance needs.',
        'og_image' => 'assets/images/og-contact.jpg',
        'canonical_url' => 'https://backsure.com/contact',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"ContactPage","name":"Contact Backsure Global","url":"https://backsure.com/contact"}',
        'last_updated' => '2023-10-05',
        'last_updated_by' => 'Admin User'
    ],
    [
        'id' => 4,
        'type' => 'static',
        'name' => 'Services Overview',
        'slug' => 'services',
        'meta_title' => 'Our Insurance & Risk Management Services | Backsure Global',
        'meta_description' => 'Explore Backsure Global\'s comprehensive range of insurance and risk management services designed for businesses of all sizes and industries.',
        'meta_keywords' => 'insurance services, risk management services, business insurance, corporate risk solutions',
        'og_title' => 'Business Insurance & Risk Management Services',
        'og_description' => 'Comprehensive insurance and risk management services tailored to your business needs.',
        'og_image' => 'assets/images/og-services.jpg',
        'canonical_url' => 'https://backsure.com/services',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"Service","name":"Backsure Global Services","url":"https://backsure.com/services"}',
        'last_updated' => '2023-09-28',
        'last_updated_by' => 'Admin User'
    ],
    
    // Service Pages
    [
        'id' => 5,
        'type' => 'service',
        'name' => 'Property Insurance',
        'slug' => 'services/property-insurance',
        'meta_title' => 'Property Insurance Solutions | Backsure Global',
        'meta_description' => 'Protect your business property with comprehensive insurance solutions from Backsure Global. Coverage for buildings, equipment, and assets.',
        'meta_keywords' => 'property insurance, commercial property, business assets protection, building insurance',
        'og_title' => 'Property Insurance for Businesses | Backsure Global',
        'og_description' => 'Comprehensive property insurance solutions to protect your business assets and premises.',
        'og_image' => 'assets/images/og-property.jpg',
        'canonical_url' => 'https://backsure.com/services/property-insurance',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"Service","name":"Property Insurance","url":"https://backsure.com/services/property-insurance"}',
        'last_updated' => '2023-08-15',
        'last_updated_by' => 'Admin User'
    ],
    [
        'id' => 6,
        'type' => 'service',
        'name' => 'Liability Coverage',
        'slug' => 'services/liability-coverage',
        'meta_title' => 'Business Liability Insurance | Backsure Global Support',
        'meta_description' => 'Protect your business from liability claims with our comprehensive coverage solutions. General, professional, and product liability insurance.',
        'meta_keywords' => 'liability insurance, business liability, professional liability, product liability, general liability',
        'og_title' => 'Business Liability Protection | Backsure Global',
        'og_description' => 'Comprehensive liability insurance solutions to protect your business from various liability risks.',
        'og_image' => 'assets/images/og-liability.jpg',
        'canonical_url' => 'https://backsure.com/services/liability-coverage',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"Service","name":"Liability Coverage","url":"https://backsure.com/services/liability-coverage"}',
        'last_updated' => '2023-09-10',
        'last_updated_by' => 'Admin User'
    ],
    
    // Blog Posts
    [
        'id' => 7,
        'type' => 'blog',
        'name' => 'Understanding Business Interruption Insurance',
        'slug' => 'blog/understanding-business-interruption-insurance',
        'meta_title' => 'Understanding Business Interruption Insurance | Backsure Global',
        'meta_description' => 'Learn what business interruption insurance covers, why it\'s essential for business continuity, and how to choose the right policy for your needs.',
        'meta_keywords' => 'business interruption insurance, revenue protection, business continuity, income protection insurance',
        'og_title' => 'Business Interruption Insurance Explained | Backsure Global',
        'og_description' => 'Everything you need to know about business interruption insurance and how it can protect your company\'s financial stability.',
        'og_image' => 'assets/images/og-business-interruption.jpg',
        'canonical_url' => 'https://backsure.com/blog/understanding-business-interruption-insurance',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"BlogPosting","headline":"Understanding Business Interruption Insurance","datePublished":"2023-10-12T09:00:00+00:00"}',
        'last_updated' => '2023-10-12',
        'last_updated_by' => 'Admin User'
    ],
    [
        'id' => 8,
        'type' => 'blog',
        'name' => 'Risk Assessment for Small Businesses',
        'slug' => 'blog/risk-assessment-small-businesses',
        'meta_title' => 'Essential Risk Assessment Guide for Small Businesses | Backsure Global',
        'meta_description' => 'Learn how to conduct a comprehensive risk assessment for your small business. Identify, analyze, and mitigate potential risks effectively.',
        'meta_keywords' => 'risk assessment, small business risks, risk management, risk mitigation, business risk analysis',
        'og_title' => 'Risk Assessment Guide for Small Businesses',
        'og_description' => 'Step-by-step guide to identifying and managing risks to protect your small business.',
        'og_image' => 'assets/images/og-risk-assessment.jpg',
        'canonical_url' => 'https://backsure.com/blog/risk-assessment-small-businesses',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"BlogPosting","headline":"Essential Risk Assessment Guide for Small Businesses","datePublished":"2023-09-28T10:30:00+00:00"}',
        'last_updated' => '2023-09-28',
        'last_updated_by' => 'Admin User'
    ]
];

// Mock data for global SEO settings
$global_seo = [
    'site_name' => 'Backsure Global Support',
    'site_description' => 'Comprehensive insurance and risk management solutions for businesses worldwide',
    'default_og_image' => 'assets/images/og-default.jpg',
    'google_analytics_id' => 'UA-123456789-1',
    'google_tag_manager_id' => 'GTM-ABCDEF',
    'bing_webmaster_verification' => '1A2B3C4D5E6F7G8H9I0J',
    'google_search_console_verification' => 'google1a2b3c4d5e6f7g8h',
    'robots_txt_content' => 'User-agent: *\nDisallow: /admin/\nDisallow: /private/\nSitemap: https://backsure.com/sitemap.xml',
    'enable_auto_meta_descriptions' => true,
    'auto_meta_length' => 160,
    'default_article_schema' => '{"@context":"https://schema.org","@type":"Article","publisher":{"@type":"Organization","name":"Backsure Global Support","logo":"https://backsure.com/assets/images/logo.png"}}',
    'last_sitemap_generated' => '2023-10-15 08:30:45'
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Process page SEO update
    if ($action === 'update_page_seo') {
        $page_id = isset($_POST['page_id']) ? $_POST['page_id'] : 0;
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-seo.php?success=page_updated&page_id=' . $page_id);
        exit;
    }
    
    // Process global SEO settings update
    if ($action === 'update_global_seo') {
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-seo.php?success=global_updated');
        exit;
    }
    
    // Process robots.txt update
    if ($action === 'update_robots') {
        // In a real implementation, validate and save to database or file
        // For now, just redirect with success message
        header('Location: admin-seo.php?success=robots_updated');
        exit;
    }
    
    // Process sitemap generation
    if ($action === 'generate_sitemap') {
        // In a real implementation, generate and save sitemap.xml
        // For now, just redirect with success message
        header('Location: admin-seo.php?success=sitemap_generated');
        exit;
    }
    
    // Process SEO data export
    if ($action === 'export_seo') {
        // In a real implementation, generate CSV file and trigger download
        // For now, just redirect with success message
        header('Location: admin-seo.php?success=seo_exported');
        exit;
    }
}

// Get page types for filtering
$page_types = array_unique(array_column($pages, 'type'));

// Get page by ID
function getPageById($id, $pages) {
    foreach ($pages as $page) {
        if ($page['id'] == $id) {
            return $page;
        }
    }
    return null;
}

// Selected page ID from GET parameter
$selected_page_id = isset($_GET['page_id']) ? intval($_GET['page_id']) : 1;
$selected_page = getPageById($selected_page_id, $pages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Settings - Backsure Global Support</title>
    <!-- Include your CSS files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5.65.5/lib/codemirror.css">
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
                            <li><a href="admin-seo.php" class="active"><i class="fas fa-search"></i> SEO Settings</a></li>
                            <li><a href="admin-integrations.php"><i class="fas fa-plug"></i> Integrations</a></li>
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
                    <h1>SEO Settings</h1>
                    <div class="breadcrumb">
                        <a href="admin-dashboard.php">Dashboard</a> / <span>SEO Settings</span>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php if($_GET['success'] == 'page_updated'): ?>
                    <strong>Success!</strong> Page SEO settings have been updated.
                    <?php elseif($_GET['success'] == 'global_updated'): ?>
                    <strong>Success!</strong> Global SEO settings have been updated.
                    <?php elseif($_GET['success'] == 'robots_updated'): ?>
                    <strong>Success!</strong> Robots.txt file has been updated.
                    <?php elseif($_GET['success'] == 'sitemap_generated'): ?>
                    <strong>Success!</strong> Sitemap has been generated successfully.
                    <?php elseif($_GET['success'] == 'seo_exported'): ?>
                    <strong>Success!</strong> SEO data has been exported successfully.
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
                    <ul class="nav nav-tabs" id="seoTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="page-seo-tab" data-bs-toggle="tab" data-bs-target="#pageSeo" type="button" role="tab" aria-controls="pageSeo" aria-selected="true">
                                Page SEO
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="global-seo-tab" data-bs-toggle="tab" data-bs-target="#globalSeo" type="button" role="tab" aria-controls="globalSeo" aria-selected="false">
                                Global Settings
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="technical-seo-tab" data-bs-toggle="tab" data-bs-target="#technicalSeo" type="button" role="tab" aria-controls="technicalSeo" aria-selected="false">
                                Technical SEO
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="bulk-tools-tab" data-bs-toggle="tab" data-bs-target="#bulkTools" type="button" role="tab" aria-controls="bulkTools" aria-selected="false">
                                Bulk Tools
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content" id="seoTabsContent">
                        <!-- Page SEO Tab -->
                        <div class="tab-pane fade show active" id="pageSeo" role="tabpanel" aria-labelledby="page-seo-tab">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3>Page SEO Settings</h3>
                                    <div class="card-actions">
                                        <button type="button" class="btn btn-outline-primary" id="previewSeoBtn">
                                            <i class="fas fa-eye"></i> Preview
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Page Selection -->
                                    <div class="row mb-4">
                                        <div class="col-md-8">
                                            <label for="pageSelect" class="form-label">Select Page</label>
                                            <select class="form-select" id="pageSelect" onchange="window.location.href='admin-seo.php?page_id='+this.value">
                                                <optgroup label="Static Pages">
                                                    <?php foreach($pages as $page): ?>
                                                    <?php if($page['type'] === 'static'): ?>
                                                    <option value="<?php echo $page['id']; ?>" <?php echo ($selected_page_id == $page['id']) ? 'selected' : ''; ?>>
                                                        <?php echo $page['name']; ?> (<?php echo $page['slug']; ?>)
                                                    </option>
                                                    <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                                <optgroup label="Service Pages">
                                                    <?php foreach($pages as $page): ?>
                                                    <?php if($page['type'] === 'service'): ?>
                                                    <option value="<?php echo $page['id']; ?>" <?php echo ($selected_page_id == $page['id']) ? 'selected' : ''; ?>>
                                                        <?php echo $page['name']; ?> (<?php echo $page['slug']; ?>)
                                                    </option>
                                                    <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                                <optgroup label="Blog Posts">
                                                    <?php foreach($pages as $page): ?>
                                                    <?php if($page['type'] === 'blog'): ?>
                                                    <option value="<?php echo $page['id']; ?>" <?php echo ($selected_page_id == $page['id']) ? 'selected' : ''; ?>>
                                                        <?php echo $page['name']; ?> (<?php echo $page['slug']; ?>)
                                                    </option>
                                                    <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="pageType" class="form-label">Page Type</label>
                                            <input type="text" class="form-control" id="pageType" value="<?php echo ucfirst($selected_page['type']); ?>" readonly>
                                        </div>
                                    </div>

                                    <!-- Page SEO Form -->
                                    <?php if($selected_page): ?>
                                    <form action="admin-seo.php" method="post">
                                        <input type="hidden" name="action" value="update_page_seo">
                                        <input type="hidden" name="page_id" value="<?php echo $selected_page['id']; ?>">
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="metaTitle" class="form-label">Meta Title</label>
                                                <input type="text" class="form-control" id="metaTitle" name="meta_title" value="<?php echo $selected_page['meta_title']; ?>" maxlength="70">
                                                <div class="form-text">
                                                    <span id="metaTitleCount">0</span>/70 characters recommended
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="metaDescription" class="form-label">Meta Description</label>
                                                <textarea class="form-control" id="metaDescription" name="meta_description" rows="3" maxlength="160"><?php echo $selected_page['meta_description']; ?></textarea>
                                                <div class="form-text">
                                                    <span id="metaDescriptionCount">0</span>/160 characters recommended
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="metaKeywords" class="form-label">Meta Keywords</label>
                                                <input type="text" class="form-control" id="metaKeywords" name="meta_keywords" value="<?php echo $selected_page['meta_keywords']; ?>">
                                                <div class="form-text">Separate keywords with commas</div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="canonicalUrl" class="form-label">Canonical URL</label>
                                                <input type="text" class="form-control" id="canonicalUrl" name="canonical_url" value="<?php echo $selected_page['canonical_url']; ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="robots" class="form-label">Robots</label>
                                                <select class="form-select" id="robots" name="robots">
                                                    <option value="index, follow" <?php echo ($selected_page['robots'] === 'index, follow') ? 'selected' : ''; ?>>index, follow</option>
                                                    <option value="index, nofollow" <?php echo ($selected_page['robots'] === 'index, nofollow') ? 'selected' : ''; ?>>index, nofollow</option>
                                                    <option value="noindex, follow" <?php echo ($selected_page['robots'] === 'noindex, follow') ? 'selected' : ''; ?>>noindex, follow</option>
                                                    <option value="noindex, nofollow" <?php echo ($selected_page['robots'] === 'noindex, nofollow') ? 'selected' : ''; ?>>noindex, nofollow</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Open Graph Settings -->
                                        <h5 class="mt-4 mb-3">Open Graph Settings</h5>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="ogTitle" class="form-label">OG Title</label>
                                                <input type="text" class="form-control" id="ogTitle" name="og_title" value="<?php echo $selected_page['og_title']; ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="ogImage" class="form-label">OG Image</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="ogImage" name="og_image" value="<?php echo $selected_page['og_image']; ?>">
                                                    <button class="btn btn-outline-secondary" type="button" id="uploadOgImage">
                                                        <i class="fas fa-upload"></i>
                                                    </button>
                                                </div>
                                                <div class="form-text">Recommended size: 1200 x 630 pixels</div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="ogDescription" class="form-label">OG Description</label>
                                                <textarea class="form-control" id="ogDescription" name="og_description" rows="2"><?php echo $selected_page['og_description']; ?></textarea>
                                            </div>
                                        </div>
                                        
                                        <!-- Schema Markup -->
                                        <h5 class="mt-4 mb-3">Schema Markup</h5>
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="schemaMarkup" class="form-label">JSON-LD Schema</label>
                                                <textarea class="form-control code-editor" id="schemaMarkup" name="schema_markup" rows="6"><?php echo $selected_page['schema_markup']; ?></textarea>
                                                <div class="form-text">
                                                    <a href="https://technicalseo.com/tools/schema-markup-generator/" target="_blank">
                                                        <i class="fas fa-external-link-alt"></i> Use Schema Markup Generator
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-4">
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="mb-0">Preview</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="seo-preview">
                                                            <div class="search-preview mb-4">
                                                                <h6 class="text-muted">Google Search Preview</h6>
                                                                <div class="search-result-item">
                                                                    <h4 class="search-title" id="previewTitle"><?php echo $selected_page['meta_title']; ?></h4>
                                                                    <div class="search-url" id="previewUrl"><?php echo $selected_page['canonical_url']; ?></div>
                                                                    <div class="search-description" id="previewDescription"><?php echo $selected_page['meta_description']; ?></div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="social-preview">
                                                                <h6 class="text-muted">Social Media Preview</h6>
                                                                <div class="social-card">
                                                                    <div class="social-image">
                                                                        <img src="<?php echo $selected_page['og_image']; ?>" id="previewSocialImage" alt="Social preview image">
                                                                    </div>
                                                                    <div class="social-content">
                                                                        <div class="social-url" id="previewSocialUrl"><?php echo str_replace('https://', '', $selected_page['canonical_url']); ?></div>
                                                                        <h5 class="social-title" id="previewSocialTitle"><?php echo $selected_page['og_title']; ?></h5>
                                                                        <div class="social-description" id="previewSocialDescription"><?php echo $selected_page['og_description']; ?></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-text text-muted mt-3">
                                            Last updated: <?php echo $selected_page['last_updated']; ?> by <?php echo $selected_page['last_updated_by']; ?>
                                        </div>
                                        
                                        <div class="row mt-4">
                                            <div class="col-md-12 text-end">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Save Changes
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <?php else: ?>
                                    <div class="alert alert-warning">
                                        No page selected or page not found.
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Global SEO Tab -->
                        <div class="tab-pane fade" id="globalSeo" role="tabpanel" aria-labelledby="global-seo-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Global SEO Settings</h3>
                                </div>
                                <div class="card-body">
                                    <form action="admin-seo.php" method="post">
                                        <input type="hidden" name="action" value="update_global_seo">
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="siteName" class="form-label">Site Name</label>
                                                <input type="text" class="form-control" id="siteName" name="site_name" value="<?php echo $global_seo['site_name']; ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="defaultOgImage" class="form-label">Default Social Image</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="defaultOgImage" name="default_og_image" value="<?php echo $global_seo['default_og_image']; ?>">
                                                    <button class="btn btn-outline-secondary" type="button">
                                                        <i class="fas fa-upload"></i>
                                                    </button>
                                                </div>
                                                <div class="form-text">Used when no page-specific image is set</div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="siteDescription" class="form-label">Site Description</label>
                                                <textarea class="form-control" id="siteDescription" name="site_description" rows="2"><?php echo $global_seo['site_description']; ?></textarea>
                                                <div class="form-text">Used as fallback when no page-specific description is set</div>
                                            </div>
                                        </div>
                                        
                                        <h5 class="mt-4 mb-3">Analytics & Tracking</h5>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="googleAnalyticsId" class="form-label">Google Analytics ID</label>
                                                <input type="text" class="form-control" id="googleAnalyticsId" name="google_analytics_id" value="<?php echo $global_seo['google_analytics_id']; ?>">
                                                <div class="form-text">Format: UA-XXXXXXXXX-X or G-XXXXXXXXXX</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="googleTagManagerId" class="form-label">Google Tag Manager ID</label>
                                                <input type="text" class="form-control" id="googleTagManagerId" name="google_tag_manager_id" value="<?php echo $global_seo['google_tag_manager_id']; ?>">
                                                <div class="form-text">Format: GTM-XXXXXX</div>
                                            </div>
                                        </div>
                                        
                                        <h5 class="mt-4 mb-3">Site Verification</h5>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="googleSearchConsole" class="form-label">Google Search Console</label>
                                                <input type="text" class="form-control" id="googleSearchConsole" name="google_search_console_verification" value="<?php echo $global_seo['google_search_console_verification']; ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="bingWebmaster" class="form-label">Bing Webmaster Tools</label>
                                                <input type="text" class="form-control" id="bingWebmaster" name="bing_webmaster_verification" value="<?php echo $global_seo['bing_webmaster_verification']; ?>">
                                            </div>
                                        </div>
                                        
                                        <h5 class="mt-4 mb-3">Auto-Generated Content</h5>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="enableAutoMeta" name="enable_auto_meta_descriptions" value="1" <?php echo $global_seo['enable_auto_meta_descriptions'] ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="enableAutoMeta">
                                                        Auto-generate meta descriptions from content
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="autoMetaLength" class="form-label">Auto Meta Description Length</label>
                                                <input type="number" class="form-control" id="autoMetaLength" name="auto_meta_length" value="<?php echo $global_seo['auto_meta_length']; ?>" min="50" max="300">
                                            </div>
                                        </div>
                                        
                                        <h5 class="mt-4 mb-3">Default Schema</h5>
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="defaultArticleSchema" class="form-label">Default Article Schema</label>
                                                <textarea class="form-control code-editor" id="defaultArticleSchema" name="default_article_schema" rows="6"><?php echo $global_seo['default_article_schema']; ?></textarea>
                                                <div class="form-text">Applied to all blog posts by default if no custom schema is defined</div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-4">
                                            <div class="col-md-12 text-end">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Save Global Settings
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Technical SEO Tab -->
                        <div class="tab-pane fade" id="technicalSeo" role="tabpanel" aria-labelledby="technical-seo-tab">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h3>Robots.txt</h3>
                                </div>
                                <div class="card-body">
                                    <form action="admin-seo.php" method="post">
                                        <input type="hidden" name="action" value="update_robots">
                                        <div class="mb-3">
                                            <label for="robotsTxt" class="form-label">Robots.txt Content</label>
                                            <textarea class="form-control code-editor" id="robotsTxt" name="robots_txt_content" rows="10"><?php echo $global_seo['robots_txt_content']; ?></textarea>
                                        </div>
                                        <div class="form-text mb-3">
                                            <a href="https://developers.google.com/search/docs/advanced/robots/create-robots-txt" target="_blank">
                                                <i class="fas fa-external-link-alt"></i> Learn more about Robots.txt
                                            </a>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 text-end">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Update Robots.txt
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3>XML Sitemap</h3>
                                    <div class="last-generated">
                                        Last Generated: <strong><?php echo $global_seo['last_sitemap_generated']; ?></strong>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form action="admin-seo.php" method="post">
                                        <input type="hidden" name="action" value="generate_sitemap">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <h5>Included in Sitemap</h5>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="includeStaticPages" name="include_static_pages" value="1" checked>
                                                    <label class="form-check-label" for="includeStaticPages">
                                                        Static Pages
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="includeServices" name="include_services" value="1" checked>
                                                    <label class="form-check-label" for="includeServices">
                                                        Service Pages
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="includeBlogPosts" name="include_blog_posts" value="1" checked>
                                                    <label class="form-check-label" for="includeBlogPosts">
                                                        Blog Posts
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="includeFAQs" name="include_faqs" value="1" checked>
                                                    <label class="form-check-label" for="includeFAQs">
                                                        FAQs
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h5>Priority & Frequency</h5>
                                                <div class="mb-2">
                                                    <label for="homePriority" class="form-label">Homepage Priority</label>
                                                    <select class="form-select" id="homePriority" name="home_priority">
                                                        <option value="1.0" selected>1.0</option>
                                                        <option value="0.9">0.9</option>
                                                        <option value="0.8">0.8</option>
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label for="staticPagesPriority" class="form-label">Static Pages Priority</label>
                                                    <select class="form-select" id="staticPagesPriority" name="static_pages_priority">
                                                        <option value="0.8" selected>0.8</option>
                                                        <option value="0.7">0.7</option>
                                                        <option value="0.6">0.6</option>
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label for="blogPostsPriority" class="form-label">Blog Posts Priority</label>
                                                    <select class="form-select" id="blogPostsPriority" name="blog_posts_priority">
                                                        <option value="0.7">0.7</option>
                                                        <option value="0.6" selected>0.6</option>
                                                        <option value="0.5">0.5</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12 text-end">
                                                <a href="https://backsure.com/sitemap.xml" target="_blank" class="btn btn-outline-secondary me-2">
                                                    <i class="fas fa-external-link-alt"></i> View Current Sitemap
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-sync-alt"></i> Generate New Sitemap
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Bulk Tools Tab -->
                        <div class="tab-pane fade" id="bulkTools" role="tabpanel" aria-labelledby="bulk-tools-tab">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h3>Bulk SEO Editor</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <label for="bulkPageType" class="form-label">Filter by Page Type</label>
                                            <select class="form-select" id="bulkPageType">
                                                <option value="">All Pages</option>
                                                <?php foreach($page_types as $type): ?>
                                                <option value="<?php echo $type; ?>"><?php echo ucfirst($type); ?> Pages</option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="bulkSeoField" class="form-label">SEO Field</label>
                                            <select class="form-select" id="bulkSeoField">
                                                <option value="meta_title">Meta Title</option>
                                                <option value="meta_description">Meta Description</option>
                                                <option value="meta_keywords">Meta Keywords</option>
                                                <option value="robots">Robots</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="bulkSearchFilter" class="form-label">Search</label>
                                            <input type="text" class="form-control" id="bulkSearchFilter" placeholder="Filter by page name...">
                                        </div>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="bulkSeoTable">
                                            <thead>
                                                <tr>
                                                    <th>Page Name</th>
                                                    <th>Type</th>
                                                    <th>URL</th>
                                                    <th>SEO Field Value</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($pages as $page): ?>
                                                <tr data-page-type="<?php echo $page['type']; ?>">
                                                    <td><?php echo $page['name']; ?></td>
                                                    <td><?php echo ucfirst($page['type']); ?></td>
                                                    <td><?php echo $page['slug']; ?></td>
                                                    <td>
                                                        <div class="field-value meta_title"><?php echo $page['meta_title']; ?></div>
                                                        <div class="field-value meta_description" style="display: none;"><?php echo $page['meta_description']; ?></div>
                                                        <div class="field-value meta_keywords" style="display: none;"><?php echo $page['meta_keywords']; ?></div>
                                                        <div class="field-value robots" style="display: none;"><?php echo $page['robots']; ?></div>
                                                    </td>
                                                    <td>
                                                        <a href="admin-seo.php?page_id=<?php echo $page['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">
                                    <h3>Export SEO Data</h3>
                                </div>
                                <div class="card-body">
                                    <form action="admin-seo.php" method="post">
                                        <input type="hidden" name="action" value="export_seo">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="exportPageType" class="form-label">Page Type</label>
                                                <select class="form-select" id="exportPageType" name="export_page_type">
                                                    <option value="">All Pages</option>
                                                    <?php foreach($page_types as $type): ?>
                                                    <option value="<?php echo $type; ?>"><?php echo ucfirst($type); ?> Pages</option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label">Include Fields</label>
                                                <div class="export-field-checkboxes">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" id="exportMetaTitle" name="export_fields[]" value="meta_title" checked>
                                                        <label class="form-check-label" for="exportMetaTitle">
                                                            Meta Title
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" id="exportMetaDescription" name="export_fields[]" value="meta_description" checked>
                                                        <label class="form-check-label" for="exportMetaDescription">
                                                            Meta Description
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" id="exportMetaKeywords" name="export_fields[]" value="meta_keywords" checked>
                                                        <label class="form-check-label" for="exportMetaKeywords">
                                                            Meta Keywords
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" id="exportOgData" name="export_fields[]" value="og_data" checked>
                                                        <label class="form-check-label" for="exportOgData">
                                                            Open Graph Data
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" id="exportCanonical" name="export_fields[]" value="canonical_url" checked>
                                                        <label class="form-check-label" for="exportCanonical">
                                                            Canonical URL
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" id="exportRobots" name="export_fields[]" value="robots" checked>
                                                        <label class="form-check-label" for="exportRobots">
                                                            Robots
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-3">
                                            <div class="col-md-12 text-end">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-file-export"></i> Export as CSV
                                                </button>
                                            </div>
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

    <!-- JavaScript Files -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin-dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.5/lib/codemirror.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.5/mode/javascript/javascript.js"></script>
    <script>
    $(document).ready(function() {
        // Toggle sidebar on mobile
        $('.toggle-sidebar').click(function() {
            $('.admin-sidebar').toggleClass('active');
        });
        
        // Code editor for JSON fields
        document.querySelectorAll('.code-editor').forEach(function(element) {
            CodeMirror.fromTextArea(element, {
                lineNumbers: true,
                mode: 'application/json',
                theme: 'default',
                lineWrapping: true,
                matchBrackets: true
            });
        });
        
        // Character count for SEO fields
        $('#metaTitle').on('input', function() {
            const count = $(this).val().length;
            $('#metaTitleCount').text(count);
            
            if (count > 60) {
                $('#metaTitleCount').addClass('text-danger');
            } else {
                $('#metaTitleCount').removeClass('text-danger');
            }
            
            // Update preview
            $('#previewTitle').text($(this).val());
        });
        
        $('#metaDescription').on('input', function() {
            const count = $(this).val().length;
            $('#metaDescriptionCount').text(count);
            
            if (count > 155) {
                $('#metaDescriptionCount').addClass('text-danger');
            } else {
                $('#metaDescriptionCount').removeClass('text-danger');
            }
            
            // Update preview
            $('#previewDescription').text($(this).val());
        });
        
        // Trigger character count on page load
        $('#metaTitle').trigger('input');
        $('#metaDescription').trigger('input');
        
        // Open Graph preview updates
        $('#ogTitle').on('input', function() {
            $('#previewSocialTitle').text($(this).val());
        });
        
        $('#ogDescription').on('input', function() {
            $('#previewSocialDescription').text($(this).val());
        });
        
        $('#ogImage').on('input', function() {
            $('#previewSocialImage').attr('src', $(this).val());
        });
        
        $('#canonicalUrl').on('input', function() {
            $('#previewUrl').text($(this).val());
            $('#previewSocialUrl').text($(this).val().replace('https://', ''));
        });
        
        // Bulk SEO editor functionality
        $('#bulkPageType').change(function() {
            filterBulkTable();
        });
        
        $('#bulkSeoField').change(function() {
            const field = $(this).val();
            $('.field-value').hide();
            $('.field-value.' + field).show();
        });
        
        $('#bulkSearchFilter').on('input', function() {
            filterBulkTable();
        });
        
        function filterBulkTable() {
            const pageType = $('#bulkPageType').val();
            const searchTerm = $('#bulkSearchFilter').val().toLowerCase();
            
            $('#bulkSeoTable tbody tr').each(function() {
                let showRow = true;
                
                // Filter by page type
                if (pageType && $(this).data('page-type') !== pageType) {
                    showRow = false;
                }
                
                // Filter by search term
                if (searchTerm && showRow) {
                    const rowText = $(this).text().toLowerCase();
                    if (!rowText.includes(searchTerm)) {
                        showRow = false;
                    }
                }
                
                $(this)[showRow ? 'show' : 'hide']();
            });
        }
        
        // Preview SEO button
        $('#previewSeoBtn').click(function() {
            // In a real implementation, this might open a modal with a rendered preview
            // or open a new tab with a preview URL
            alert('SEO Preview functionality would be implemented here.');
        });
        
        // Select all in export
        $('#selectAllExportFields').change(function() {
            $('.export-field-checkboxes input[type="checkbox"]').prop('checked', $(this).prop('checked'));
        });
    });
    </script>
    
    <style>
    /* SEO Preview Styles */
    .seo-preview {
        font-family: Arial, sans-serif;
    }
    
    .search-result-item {
        max-width: 600px;
        margin-bottom: 20px;
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 4px;
    }
    
    .search-title {
        color: #1a0dab;
        font-size: 18px;
        margin: 0 0 5px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .search-url {
        color: #006621;
        font-size: 14px;
        margin-bottom: 3px;
    }
    
    .search-description {
        color: #545454;
        font-size: 14px;
        line-height: 1.4;
    }
    
    .social-card {
        max-width: 500px;
        border: 1px solid #ddd;
        border-radius: 6px;
        overflow: hidden;
    }
    
    .social-image {
        width: 100%;
        height: 260px;
        background-color: #f8f9fa;
        overflow: hidden;
    }
    
    .social-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .social-content {
        padding: 10px;
    }
    
    .social-url {
        color: #606770;
        font-size: 12px;
        margin-bottom: 5px;
        text-transform: uppercase;
    }
    
    .social-title {
        font-size: 16px;
        margin: 0 0 5px;
        color: #1c1e21;
    }
    
    .social-description {
        font-size: 14px;
        color: #606770;
        line-height: 1.4;
    }
    
    /* Bulk Tools Styles */
    .field-value {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /* CodeMirror Custom Styles */
    .CodeMirror {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        height: auto;
    }
    
    /* Card styles */
    .tips-card {
        text-align: center;
        padding: 15px;
        height: 100%;
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

// Mock data for pages with SEO settings
$pages = [
    // Static Pages
    [
        'id' => 1,
        'type' => 'static',
        'name' => 'Homepage',
        'slug' => 'index',
        'meta_title' => 'Backsure Global Support | Insurance & Risk Management Solutions',
        'meta_description' => 'Backsure Global provides comprehensive insurance and risk management solutions for businesses across multiple industries. Expert advice, customized coverage.',
        'meta_keywords' => 'insurance, risk management, business insurance, global insurance solutions, corporate risk',
        'og_title' => 'Insurance & Risk Management Solutions | Backsure Global',
        'og_description' => 'Protect your business with tailored insurance and risk management solutions from Backsure Global.',
        'og_image' => 'assets/images/og-homepage.jpg',
        'canonical_url' => 'https://backsure.com/',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"Organization","name":"Backsure Global Support","url":"https://backsure.com","logo":"https://backsure.com/assets/images/logo.png"}',
        'last_updated' => '2023-09-15',
        'last_updated_by' => 'Admin User'
    ],
    [
        'id' => 2,
        'type' => 'static',
        'name' => 'About Us',
        'slug' => 'about',
        'meta_title' => 'About Backsure Global | Our History & Vision',
        'meta_description' => 'Learn about Backsure Global\'s history, our expert team, and our vision for transforming insurance and risk management for businesses worldwide.',
        'meta_keywords' => 'about backsure, insurance history, risk management company, insurance experts, global insurance vision',
        'og_title' => 'About Backsure Global | Expert Insurance Solutions',
        'og_description' => 'Discover how Backsure Global is revolutionizing business insurance and risk management.',
        'og_image' => 'assets/images/og-about.jpg',
        'canonical_url' => 'https://backsure.com/about',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"AboutPage","name":"About Backsure Global","url":"https://backsure.com/about"}',
        'last_updated' => '2023-08-20',
        'last_updated_by' => 'Admin User'
    ],
    [
        'id' => 3,
        'type' => 'static',
        'name' => 'Contact Us',
        'slug' => 'contact',
        'meta_title' => 'Contact Backsure Global | Get Expert Insurance Advice',
        'meta_description' => 'Reach out to Backsure Global for expert advice on business insurance and risk management solutions. Contact our team today.',
        'meta_keywords' => 'contact insurance company, business insurance contact, risk management advice, insurance consultation',
        'og_title' => 'Contact Our Insurance Experts | Backsure Global',
        'og_description' => 'Get in touch with Backsure Global for dedicated support and expert advice on all your insurance needs.',
        'og_image' => 'assets/images/og-contact.jpg',
        'canonical_url' => 'https://backsure.com/contact',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"ContactPage","name":"Contact Backsure Global","url":"https://backsure.com/contact"}',
        'last_updated' => '2023-10-05',
        'last_updated_by' => 'Admin User'
    ],
    [
        'id' => 4,
        'type' => 'static',
        'name' => 'Services Overview',
        'slug' => 'services',
        'meta_title' => 'Our Insurance & Risk Management Services | Backsure Global',
        'meta_description' => 'Explore Backsure Global\'s comprehensive range of insurance and risk management services designed for businesses of all sizes and industries.',
        'meta_keywords' => 'insurance services, risk management services, business insurance, corporate risk solutions',
        'og_title' => 'Business Insurance & Risk Management Services',
        'og_description' => 'Comprehensive insurance and risk management services tailored to your business needs.',
        'og_image' => 'assets/images/og-services.jpg',
        'canonical_url' => 'https://backsure.com/services',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"Service","name":"Backsure Global Services","url":"https://backsure.com/services"}',
        'last_updated' => '2023-09-28',
        'last_updated_by' => 'Admin User'
    ],
    
    // Service Pages
    [
        'id' => 5,
        'type' => 'service',
        'name' => 'Property Insurance',
        'slug' => 'services/property-insurance',
        'meta_title' => 'Property Insurance Solutions | Backsure Global',
        'meta_description' => 'Protect your business property with comprehensive insurance solutions from Backsure Global. Coverage for buildings, equipment, and assets.',
        'meta_keywords' => 'property insurance, commercial property, business assets protection, building insurance',
        'og_title' => 'Property Insurance for Businesses | Backsure Global',
        'og_description' => 'Comprehensive property insurance solutions to protect your business assets and premises.',
        'og_image' => 'assets/images/og-property.jpg',
        'canonical_url' => 'https://backsure.com/services/property-insurance',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"Service","name":"Property Insurance","url":"https://backsure.com/services/property-insurance"}',
        'last_updated' => '2023-08-15',
        'last_updated_by' => 'Admin User'
    ],
    [
        'id' => 6,
        'type' => 'service',
        'name' => 'Liability Coverage',
        'slug' => 'services/liability-coverage',
        'meta_title' => 'Business Liability Insurance | Backsure Global Support',
        'meta_description' => 'Protect your business from liability claims with our comprehensive coverage solutions. General, professional, and product liability insurance.',
        'meta_keywords' => 'liability insurance, business liability, professional liability, product liability, general liability',
        'og_title' => 'Business Liability Protection | Backsure Global',
        'og_description' => 'Comprehensive liability insurance solutions to protect your business from various liability risks.',
        'og_image' => 'assets/images/og-liability.jpg',
        'canonical_url' => 'https://backsure.com/services/liability-coverage',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"Service","name":"Liability Coverage","url":"https://backsure.com/services/liability-coverage"}',
        'last_updated' => '2023-09-10',
        'last_updated_by' => 'Admin User'
    ],
    
    // Blog Posts
    [
        'id' => 7,
        'type' => 'blog',
        'name' => 'Understanding Business Interruption Insurance',
        'slug' => 'blog/understanding-business-interruption-insurance',
        'meta_title' => 'Understanding Business Interruption Insurance | Backsure Global',
        'meta_description' => 'Learn what business interruption insurance covers, why it\'s essential for business continuity, and how to choose the right policy for your needs.',
        'meta_keywords' => 'business interruption insurance, revenue protection, business continuity, income protection insurance',
        'og_title' => 'Business Interruption Insurance Explained | Backsure Global',
        'og_description' => 'Everything you need to know about business interruption insurance and how it can protect your company\'s financial stability.',
        'og_image' => 'assets/images/og-business-interruption.jpg',
        'canonical_url' => 'https://backsure.com/blog/understanding-business-interruption-insurance',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"BlogPosting","headline":"Understanding Business Interruption Insurance","datePublished":"2023-10-12T09:00:00+00:00"}',
        'last_updated' => '2023-10-12',
        'last_updated_by' => 'Admin User'
    ],
    [
        'id' => 8,
        'type' => 'blog',
        'name' => 'Risk Assessment for Small Businesses',
        'slug' => 'blog/risk-assessment-small-businesses',
        'meta_title' => 'Essential Risk Assessment Guide for Small Businesses | Backsure Global',
        'meta_description' => 'Learn how to conduct a comprehensive risk assessment for your small business. Identify, analyze, and mitigate potential risks effectively.',
        'meta_keywords' => 'risk assessment, small business risks, risk management, risk mitigation, business risk analysis',
        'og_title' => 'Risk Assessment Guide for Small Businesses',
        'og_description' => 'Step-by-step guide to identifying and managing risks to protect your small business.',
        'og_image' => 'assets/images/og-risk-assessment.jpg',
        'canonical_url' => 'https://backsure.com/blog/risk-assessment-small-businesses',
        'robots' => 'index, follow',
        'schema_markup' => '{"@context":"https://schema.org","@type":"BlogPosting","headline":"Essential Risk Assessment Guide for Small Businesses","datePublished":"2023-09-28T10:30:00+00:00"}',
        'last_updated' => '2023-09-28',
        'last_updated_by' => 'Admin User'
    ]
];

// Mock data for global SEO settings
$global_seo = [
    'site_name' => 'Backsure Global Support',
    'site_description' => 'Comprehensive insurance and risk management solutions for businesses worldwide',
    'default_og_image' => 'assets/images/og-default.jpg',
    'google_analytics_id' => 'UA-123456789-1',
    'google_tag_manager_id' => 'GTM-ABCDEF',
    'bing_webmaster_verification' => '1A2B3C4D5E6F7G8H9I0J',
    'google_search_console_verification' => 'google1a2b3c4d5e6f7g8h',
    'robots_txt_content' => 'User-agent: *\nDisallow: /admin/\nDisallow: /private/\nSitemap: https://backsure.com/sitemap.xml',
    'enable_auto_meta_descriptions' => true,
    'auto_meta_length' => 160,
    'default_article_schema' => '{"@context":"https://schema.org","@type":"Article","publisher":{"@type":"Organization","name":"Backsure Global Support","logo":"https://backsure.com/assets/images/logo.png"}}',
    'last_sitemap_generated' => '2023-10-15 08:30:45'
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Process page SEO update
    if ($action === 'update_page_seo') {
        $page_id = isset($_POST['page_id']) ? $_POST['page_id'] : 0;
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-seo.php?success=page_updated&page_id=' . $page_id);
        exit;
    }
    
    // Process global SEO settings update
    if ($action === 'update_global_seo') {
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-seo.php?success=global_updated');
        exit;
    }
    
    // Process robots.txt update
    if ($action === 'update_robots') {
        // In a real implementation, validate and save to database or file
        // For now, just redirect with success message
        header('Location: admin-seo.php?success=robots_updated');
        exit;
    }
    
    // Process sitemap generation
    if ($action === 'generate_sitemap') {
        // In a real implementation, generate and save sitemap.xml
        // For now, just redirect with success message
        header('Location: admin-seo.php?success=sitemap_generated');
        exit;
    }
    
    // Process SEO data export
    if ($action === 'export_seo') {
        // In a real implementation, generate CSV file and trigger download
        // For now, just redirect with success message
        header('Location: admin-seo.php?success=seo_exported');
        exit;
    }
}

// Get page types for filtering
$page_types = array_unique(array_column($pages, 'type'));

// Get page by ID
function getPageById($id, $pages) {
    foreach ($pages as $page) {
        if ($page['id'] == $id) {
            return $page;
        }
    }
    return null;
}

// Selected page ID from GET parameter
$selected_page_id = isset($_GET['page_id']) ? intval($_GET['page_id']) : 1;
$selected_page = getPageById($selected_page_id, $pages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Settings - Backsure Global Support</title>
    <!-- Include your CSS files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5.65.5/lib/codemirror.css">
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
                            <li><a href="admin-seo.php" class="active"><i class="fas fa-search"></i> SEO Settings</a></li>
                            <li><a href="admin-integrations.php"><i class="fas fa-plug"></i> Integrations</a></li>
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
                    <h1>SEO Settings</h1>
                    <div class="breadcrumb">
                        <a href="admin-dashboard.php">Dashboard</a> / <span>SEO Settings</span>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php if($_GET['success'] == 'page_updated'): ?>
                    <strong>Success!</strong> Page SEO settings have been updated.
                    <?php elseif($_GET['success'] == 'global_updated'): ?>
                    <strong>Success!</strong> Global SEO settings have been updated.
                    <?php elseif($_GET['success'] == 'robots_updated'): ?>
                    <strong>Success!</strong> Robots.txt file has been updated.
                    <?php elseif($_GET['success'] == 'sitemap_generated'): ?>
                    <strong>Success!</strong> Sitemap has been generated successfully.
                    <?php elseif($_GET['success'] == 'seo_exported'): ?>
                    <strong>Success!</strong> SEO data has been exported successfully.
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
                    <ul class="nav nav-tabs" id="seoTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="page-seo-tab" data-bs-toggle="tab" data-bs-target="#pageSeo" type="button" role="tab" aria-controls="pageSeo" aria-selected="true">
                                Page SEO
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="global-seo-tab" data-bs-toggle="tab" data-bs-target="#globalSeo" type="button" role="tab" aria-controls="globalSeo" aria-selected="false">
                                Global Settings
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="technical-seo-tab" data-bs-toggle="tab" data-bs-target="#technicalSeo" type="button" role="tab" aria-controls="technicalSeo" aria-selected="false">
                                Technical SEO
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="bulk-tools-tab" data-bs-toggle="tab" data-bs-target="#bulkTools" type="button" role="tab" aria-controls="bulkTools" aria-selected="false">
                                Bulk Tools
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content" id="seoTabsContent">
                        <!-- Page SEO Tab -->
                        <div class="tab-pane fade show active" id="pageSeo" role="tabpanel" aria-labelledby="page-seo-tab">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3>Page SEO Settings</h3>
                                    <div class="card-actions">
                                        <button type="button" class="btn btn-outline-primary" id="previewSeoBtn">
                                            <i class="fas fa-eye"></i> Preview
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Page Selection -->
                                    <div class="row mb-4">
                                        <div class="col-md-8">
                                            <label for="pageSelect" class="form-label">Select Page</label>
                                            <select class="form-select" id="pageSelect" onchange="window.location.href='admin-seo.php?page_id='+this.value">
                                                <optgroup label="Static Pages">
                                                    <?php foreach($pages as $page): ?>
                                                    <?php if($page['type'] === 'static'): ?>
                                                    <option value="<?php echo $page['id']; ?>" <?php echo ($selected_page_id == $page['id']) ? 'selected' : ''; ?>>
                                                        <?php echo $page['name']; ?> (<?php echo $page['slug']; ?>)
                                                    </option>
                                                    <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                                <optgroup label="Service Pages">
                                                    <?php foreach($pages as $page): ?>
                                                    <?php if($page['type'] === 'service'): ?>
                                                    <option value="<?php echo $page['id']; ?>" <?php echo ($selected_page_id == $page['id']) ? 'selected' : ''; ?>>
                                                        <?php echo $page['name']; ?> (<?php echo $page['slug']; ?>)
                                                    </option>
                                                    <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                                <optgroup label="Blog Posts">
                                                    <?php foreach($pages as $page): ?>
                                                    <?php if($page['type'] === 'blog'): ?>
                                                    <option value="<?php echo $page['id']; ?>" <?php echo ($selected_page_id == $page['id']) ? 'selected' : ''; ?>>
                                                        <?php echo $page['name']; ?> (<?php echo $page['slug']; ?>)
                                                    </option>
                                                    <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="pageType" class="form-label">Page Type</label>
                                            <input type="text" class="form-control" id="pageType" value="<?php echo ucfirst($selected_page['type']); ?>" readonly>
                                        </div>
                                    </div>

                                    <!-- Page SEO Form -->
                                    <?php if($selected_page): ?>
                                    <form action="admin-seo.php" method="post">
                                        <input type="hidden" name="action" value="update_page_seo">
                                        <input type="hidden" name="page_id" value="<?php echo $selected_page['id']; ?>">
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="metaTitle" class="form-label">Meta Title</label>
                                                <input type="text" class="form-control" id="metaTitle" name="meta_title" value="<?php echo $selected_page['meta_title']; ?>" maxlength="70">
                                                <div class="form-text">
                                                    <span id="metaTitleCount">0</span>/70 characters recommended
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="metaDescription" class="form-label">Meta Description</label>
                                                <textarea class="form-control" id="metaDescription" name="meta_description" rows="3" maxlength="160"><?php echo $selected_page['meta_description']; ?></textarea>
                                                <div class="form-text">
                                                    <span id="metaDescriptionCount">0</span>/160 characters recommended
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="metaKeywords" class="form-label">Meta Keywords</label>
                                                <input type="text" class="form-control" id="metaKeywords" name="meta_keywords" value="<?php echo $selected_page['meta_keywords']; ?>">
                                                <div class="form-text">Separate keywords with commas</div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="canonicalUrl" class="form-label">Canonical URL</label>
                                                <input type="text" class="form-control" id="canonicalUrl" name="canonical_url" value="<?php echo $selected_page['canonical_url']; ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="robots" class="form-label">Robots</label>
                                                <select class="form-select" id="robots" name="robots">
                                                    <option value="index, follow" <?php echo ($selected_page['robots'] === 'index, follow') ? 'selected' : ''; ?>>index, follow</option>
                                                    <option value="index, nofollow" <?php echo ($selected_page['robots'] === 'index, nofollow') ? 'selected' : ''; ?>>index, nofollow</option>
                                                    <option value="noindex, follow" <?php echo ($selected_page['robots'] === 'noindex, follow') ? 'selected' : ''; ?>>noindex, follow</option>
                                                    <option value="noindex, nofollow" <?php echo ($selected_page['robots'] === 'noindex, nofollow') ? 'selected' : ''; ?>>noindex, nofollow</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Open Graph Settings -->
                                        <h5 class="mt-4 mb-3">Open Graph Settings</h5>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="ogTitle" class="form-label">OG Title</label>
                                                <input type="text" class="form-control" id="ogTitle" name="og_title" value="<?php echo $selected_page['og_title']; ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="ogImage" class="form-label">OG Image</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="ogImage" name="og_image" value="<?php echo $selected_page['og_image']; ?>">
                                                    <button class="btn btn-outline-secondary" type="button" id="uploadOgImage">
                                                        <i class="fas fa-upload"></i>
                                                    </button>
                                                </div>
                                                <div class="form-text">Recommended size: 1200