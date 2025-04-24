<?php
// Initialize system and security
require_once 'includes/admin-init.php';

// Set page-specific variables
$page_title = "Testimonials & Partners";
$active_menu = "content";
$active_submenu = "testimonials";

// Mock data for testimonials
$testimonials = [
    [
        'id' => 1,
        'client_name' => 'Robert Johnson',
        'client_company' => 'Alpine Industries Ltd.',
        'client_position' => 'Chief Risk Officer',
        'client_image' => 'assets/images/testimonials/client1.jpg',
        'content' => 'Backsure Global has been instrumental in optimizing our insurance coverage while reducing our premiums by 15%. Their risk assessment identified several areas we had overlooked, and their responsive team made the transition seamless.',
        'rating' => 5,
        'status' => 'active',
        'featured' => true,
        'created_at' => '2023-04-10',
        'display_order' => 1
    ],
    [
        'id' => 2,
        'client_name' => 'Sarah Thompson',
        'client_company' => 'Riverdale Healthcare',
        'client_position' => 'Operations Director',
        'client_image' => 'assets/images/testimonials/client2.jpg',
        'content' => 'We\'ve worked with several insurance brokers in the past, but none have shown the level of industry-specific knowledge that Backsure Global provides. Their healthcare sector expertise has been invaluable in securing the right coverage for our specialized needs.',
        'rating' => 4,
        'status' => 'active',
        'featured' => true,
        'created_at' => '2023-05-22',
        'display_order' => 2
    ],
    [
        'id' => 3,
        'client_name' => 'Michael Chen',
        'client_company' => 'Techlify Solutions',
        'client_position' => 'CEO',
        'client_image' => 'assets/images/testimonials/client3.jpg',
        'content' => 'As a fast-growing tech company, our insurance needs evolve rapidly. Backsure Global has been proactive in adjusting our coverage as we scale, ensuring we\'re never over or under-insured. Their digital claims process is incredibly efficient.',
        'rating' => 5,
        'status' => 'active',
        'featured' => false,
        'created_at' => '2023-07-15',
        'display_order' => 3
    ],
    [
        'id' => 4,
        'client_name' => 'Olivia Martinez',
        'client_company' => 'Coastal Shipping Co.',
        'client_position' => 'Risk Manager',
        'client_image' => 'assets/images/testimonials/client4.jpg',
        'content' => 'The marine insurance expertise at Backsure Global has been exceptional. They arranged comprehensive coverage for our fleet while providing valuable guidance on international compliance requirements. Highly recommended for logistics companies.',
        'rating' => 5,
        'status' => 'active',
        'featured' => true,
        'created_at' => '2023-08-03',
        'display_order' => 4
    ],
    [
        'id' => 5,
        'client_name' => 'James Wilson',
        'client_company' => 'Constructa Engineering',
        'client_position' => 'Managing Director',
        'client_image' => 'assets/images/testimonials/client5.jpg',
        'content' => 'When our project faced a major setback due to equipment damage, Backsure Global\'s claims team went above and beyond. Their quick response minimized our downtime and the claim was settled within just 10 days. Their construction industry knowledge is a real asset.',
        'rating' => 4,
        'status' => 'inactive',
        'featured' => false,
        'created_at' => '2023-09-17',
        'display_order' => 5
    ]
];

// Mock data for partner logos
$partners = [
    [
        'id' => 1,
        'name' => 'Global Insurance Group',
        'logo' => 'assets/images/partners/partner1.png',
        'website' => 'https://www.globalinsurancegroup.com',
        'category' => 'Insurance Provider',
        'description' => 'Leading international insurance provider specializing in commercial coverage.',
        'status' => 'active',
        'display_order' => 1
    ],
    [
        'id' => 2,
        'name' => 'SecureGuard Partners',
        'logo' => 'assets/images/partners/partner2.png',
        'website' => 'https://www.secureguardpartners.com',
        'category' => 'Risk Assessment',
        'description' => 'Premier risk assessment and management consultancy with global reach.',
        'status' => 'active',
        'display_order' => 2
    ],
    [
        'id' => 3,
        'name' => 'Alliance Underwriters',
        'logo' => 'assets/images/partners/partner3.png',
        'website' => 'https://www.allianceunderwriters.com',
        'category' => 'Underwriting',
        'description' => 'Specialized underwriters focusing on high-risk industries and unique coverage needs.',
        'status' => 'active',
        'display_order' => 3
    ],
    [
        'id' => 4,
        'name' => 'ClaimPro Solutions',
        'logo' => 'assets/images/partners/partner4.png',
        'website' => 'https://www.claimprosolutions.com',
        'category' => 'Claims Processing',
        'description' => 'Digital-first claims processing service with industry-leading turnaround times.',
        'status' => 'active',
        'display_order' => 4
    ],
    [
        'id' => 5,
        'name' => 'RiskTech Innovations',
        'logo' => 'assets/images/partners/partner5.png',
        'website' => 'https://www.risktechinnovations.com',
        'category' => 'InsurTech',
        'description' => 'Technology provider specializing in insurance risk assessment software and tools.',
        'status' => 'inactive',
        'display_order' => 5
    ]
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Process testimonial creation/editing
    if ($action === 'save_testimonial') {
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-testimonials.php?success=testimonial_saved');
        exit;
    }
    
    // Process partner logo creation/editing
    if ($action === 'save_partner') {
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-testimonials.php?success=partner_saved');
        exit;
    }
    
    // Process testimonial deletion
    if ($action === 'delete_testimonial') {
        // In a real implementation, delete from database
        // For now, just redirect with success message
        header('Location: admin-testimonials.php?success=testimonial_deleted');
        exit;
    }
    
    // Process partner deletion
    if ($action === 'delete_partner') {
        // In a real implementation, delete from database
        // For now, just redirect with success message
        header('Location: admin-testimonials.php?success=partner_deleted');
        exit;
    }
    
    // Process display settings
    if ($action === 'save_settings') {
        // In a real implementation, validate and save to database
        // For now, just redirect with success message
        header('Location: admin-testimonials.php?success=settings_saved');
        exit;
    }
}

// Include header (with shared head section and top navigation)
require_once 'includes/admin-header.php';

// Include sidebar
require_once 'includes/admin-sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="content-header">
        <h1><?php echo $page_title; ?></h1>
        <div class="breadcrumb">
            <a href="admin-dashboard.php">Dashboard</a> / <span><?php echo $page_title; ?></span>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php if($_GET['success'] == 'testimonial_saved'): ?>
        <strong>Success!</strong> The testimonial has been saved successfully.
        <?php elseif($_GET['success'] == 'partner_saved'): ?>
        <strong>Success!</strong> The partner logo has been saved successfully.
        <?php elseif($_GET['success'] == 'testimonial_deleted'): ?>
        <strong>Success!</strong> The testimonial has been deleted successfully.
        <?php elseif($_GET['success'] == 'partner_deleted'): ?>
        <strong>Success!</strong> The partner logo has been deleted successfully.
        <?php elseif($_GET['success'] == 'settings_saved'): ?>
        <strong>Success!</strong> Display settings have been updated successfully.
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
        <ul class="nav nav-tabs" id="testimonialTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="testimonials-tab" data-bs-toggle="tab" data-bs-target="#testimonials" type="button" role="tab" aria-controls="testimonials" aria-selected="true">
                    Client Testimonials
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="partners-tab" data-bs-toggle="tab" data-bs-target="#partners" type="button" role="tab" aria-controls="partners" aria-selected="false">
                    Partner Logos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="display-tab" data-bs-toggle="tab" data-bs-target="#display" type="button" role="tab" aria-controls="display" aria-selected="false">
                    Display Settings
                </button>
            </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content" id="testimonialTabsContent">
            <!-- Testimonials Tab -->
            <div class="tab-pane fade show active" id="testimonials" role="tabpanel" aria-labelledby="testimonials-tab">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Client Testimonials</h3>
                        <div class="card-actions">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                                <i class="fas fa-plus"></i> Add New Testimonial
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Controls -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <select class="form-select" id="testimonialStatusFilter">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active Only</option>
                                    <option value="inactive">Inactive Only</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="testimonialFeaturedFilter">
                                    <option value="">All Testimonials</option>
                                    <option value="1">Featured Only</option>
                                    <option value="0">Non-Featured Only</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="testimonialSearch" placeholder="Search...">
                                    <button class="btn btn-outline-secondary" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonials Grid View -->
                        <div class="row testimonials-grid">
                            <?php foreach($testimonials as $testimonial): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 testimonial-card <?php echo ($testimonial['status'] == 'inactive') ? 'inactive-item' : ''; ?>">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="testimonial-image">
                                                <img src="<?php echo $testimonial['client_image']; ?>" alt="<?php echo $testimonial['client_name']; ?>" class="rounded-circle" width="50" height="50">
                                            </div>
                                            <div class="ms-3">
                                                <h5 class="mb-0"><?php echo $testimonial['client_name']; ?></h5>
                                                <p class="mb-0 text-muted small"><?php echo $testimonial['client_position']; ?>, <?php echo $testimonial['client_company']; ?></p>
                                            </div>
                                        </div>
                                        <div class="testimonial-status">
                                            <?php if($testimonial['featured']): ?>
                                            <span class="badge bg-warning">Featured</span>
                                            <?php endif; ?>
                                            <span class="badge bg-<?php echo ($testimonial['status'] == 'active') ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($testimonial['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="testimonial-rating mb-2">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo ($i <= $testimonial['rating']) ? 'text-warning' : 'text-muted'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="testimonial-content"><?php echo $testimonial['content']; ?></p>
                                    </div>
                                    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Added: <?php echo $testimonial['created_at']; ?></small>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-primary edit-testimonial" data-bs-toggle="modal" data-bs-target="#editTestimonialModal" data-testimonial-id="<?php echo $testimonial['id']; ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-testimonial" data-testimonial-id="<?php echo $testimonial['id']; ?>">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Testimonials pagination" class="mt-4">
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

            <!-- Partners Tab -->
            <div class="tab-pane fade" id="partners" role="tabpanel" aria-labelledby="partners-tab">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Partner Logos</h3>
                        <div class="card-actions">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPartnerModal">
                                <i class="fas fa-plus"></i> Add New Partner
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Controls -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <select class="form-select" id="partnerStatusFilter">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active Only</option>
                                    <option value="inactive">Inactive Only</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="partnerCategoryFilter">
                                    <option value="">All Categories</option>
                                    <option value="Insurance Provider">Insurance Providers</option>
                                    <option value="Risk Assessment">Risk Assessment</option>
                                    <option value="Underwriting">Underwriting</option>
                                    <option value="Claims Processing">Claims Processing</option>
                                    <option value="InsurTech">InsurTech</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="partnerSearch" placeholder="Search...">
                                    <button class="btn btn-outline-secondary" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Partners Table -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th width="15%">Logo</th>
                                        <th width="20%">Partner Name</th>
                                        <th width="15%">Category</th>
                                        <th width="15%">Website</th>
                                        <th width="10%">Order</th>
                                        <th width="10%">Status</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($partners as $partner): ?>
                                    <tr class="<?php echo ($partner['status'] == 'inactive') ? 'table-secondary' : ''; ?>">
                                        <td><?php echo $partner['id']; ?></td>
                                        <td>
                                            <img src="<?php echo $partner['logo']; ?>" alt="<?php echo $partner['name']; ?>" class="partner-logo-thumbnail" width="100">
                                        </td>
                                        <td><?php echo $partner['name']; ?></td>
                                        <td><?php echo $partner['category']; ?></td>
                                        <td>
                                            <a href="<?php echo $partner['website']; ?>" target="_blank" rel="noopener noreferrer">
                                                <i class="fas fa-external-link-alt"></i> Visit Site
                                            </a>
                                        </td>
                                        <td>
                                            <div class="order-controls">
                                                <button class="btn btn-sm btn-light move-up" title="Move Up">
                                                    <i class="fas fa-arrow-up"></i>
                                                </button>
                                                <span class="mx-1"><?php echo $partner['display_order']; ?></span>
                                                <button class="btn btn-sm btn-light move-down" title="Move Down">
                                                    <i class="fas fa-arrow-down"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle" type="checkbox" role="switch" id="partnerSwitch<?php echo $partner['id']; ?>" <?php echo ($partner['status'] === 'active') ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="partnerSwitch<?php echo $partner['id']; ?>">
                                                    <span class="badge bg-<?php echo ($partner['status'] === 'active') ? 'success' : 'secondary'; ?>">
                                                        <?php echo ucfirst($partner['status']); ?>
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-primary edit-partner" data-bs-toggle="modal" data-bs-target="#editPartnerModal" data-partner-id="<?php echo $partner['id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger delete-partner" data-partner-id="<?php echo $partner['id']; ?>">
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

                <!-- Partners Grid Preview -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h3>Partners Grid Preview</h3>
                    </div>
                    <div class="card-body">
                        <div class="row partner-logos-preview">
                            <?php 
                            $activePartners = array_filter($partners, function($partner) {
                                return $partner['status'] === 'active';
                            });
                            foreach($activePartners as $partner): 
                            ?>
                            <div class="col-md-3 col-sm-6 mb-4 text-center">
                                <div class="partner-logo-container p-3 border rounded">
                                    <img src="<?php echo $partner['logo']; ?>" alt="<?php echo $partner['name']; ?>" class="img-fluid partner-logo mb-2">
                                    <p class="mb-0"><?php echo $partner['name']; ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Display Settings Tab -->
            <div class="tab-pane fade" id="display" role="tabpanel" aria-labelledby="display-tab">
                <div class="card">
                    <div class="card-header">
                        <h3>Display Settings</h3>
                    </div>
                    <div class="card-body">
                        <form action="admin-testimonials.php" method="post">
                            <input type="hidden" name="action" value="save_settings">
                            
                            <!-- Testimonials Display Settings -->
                            <h4 class="mb-3">Testimonials Settings</h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="testimonialsPerPage" class="form-label">Testimonials Per Page</label>
                                    <input type="number" class="form-control" id="testimonialsPerPage" name="testimonials_per_page" value="4" min="1" max="12">
                                </div>
                                <div class="col-md-6">
                                    <label for="testimonialSortOrder" class="form-label">Default Sort Order</label>
                                    <select class="form-select" id="testimonialSortOrder" name="testimonial_sort_order">
                                        <option value="newest">Newest First</option>
                                        <option value="oldest">Oldest First</option>
                                        <option value="rating">Highest Rating First</option>
                                        <option value="display_order" selected>Custom Order</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="testimonialsDisplayStyle" class="form-label">Display Style</label>
                                    <select class="form-select" id="testimonialsDisplayStyle" name="testimonials_display_style">
                                        <option value="grid" selected>Grid Layout</option>
                                        <option value="slider">Slider/Carousel</option>
                                        <option value="list">List View</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="featuredTestimonialsCount" class="form-label">Featured Testimonials on Homepage</label>
                                    <input type="number" class="form-control" id="featuredTestimonialsCount" name="featured_testimonials_count" value="3" min="0" max="6">
                                    <small class="form-text">Set to 0 to hide testimonials on homepage</small>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="showClientCompany" name="show_client_company" checked>
                                        <label class="form-check-label" for="showClientCompany">
                                            Show Client Company
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="showClientImage" name="show_client_image" checked>
                                        <label class="form-check-label" for="showClientImage">
                                            Show Client Image
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="showClientRating" name="show_client_rating" checked>
                                        <label class="form-check-label" for="showClientRating">
                                            Show Rating Stars
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="enableTestimonialPagination" name="enable_testimonial_pagination" checked>
                                        <label class="form-check-label" for="enableTestimonialPagination">
                                            Enable Pagination
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Partner Logos Display Settings -->
                            <h4 class="mb-3">Partner Logos Settings</h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="partnersPerRow" class="form-label">Partners Per Row</label>
                                    <select class="form-select" id="partnersPerRow" name="partners_per_row">
                                        <option value="3">3 Logos</option>
                                        <option value="4" selected>4 Logos</option>
                                        <option value="5">5 Logos</option>
                                        <option value="6">6 Logos</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="partnerLogoSize" class="form-label">Logo Size</label>
                                    <select class="form-select" id="partnerLogoSize" name="partner_logo_size">
                                        <option value="small">Small</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="large">Large</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="partnerSectionTitle" class="form-label">Partners Section Title</label>
                                    <input type="text" class="form-control" id="partnerSectionTitle" name="partner_section_title" value="Our Trusted Partners">
                                </div>
                                <div class="col-md-6">
                                    <label for="partnerDisplayStyle" class="form-label">Display Style</label>
                                    <select class="form-select" id="partnerDisplayStyle" name="partner_display_style">
                                        <option value="static" selected>Static Grid</option>
                                        <option value="carousel">Carousel/Slider</option>
                                        <option value="marquee">Scrolling Marquee</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="showPartnerNames" name="show_partner_names">
                                        <label class="form-check-label" for="showPartnerNames">
                                            Show Partner Names
                                        </label>
                                   </div>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="enablePartnerLinks" name="enable_partner_links" checked>
                                        <label class="form-check-label" for="enablePartnerLinks">
                                            Enable Partner Website Links
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="grayscaleLogos" name="grayscale_logos">
                                        <label class="form-check-label" for="grayscaleLogos">
                                            Display Logos in Grayscale
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="colorizeOnHover" name="colorize_on_hover" checked>
                                        <label class="form-check-label" for="colorizeOnHover">
                                            Colorize Logos on Hover
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Page Settings -->
                            <h4 class="mb-3">Page Settings</h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="testimonialPageTitle" class="form-label">Testimonials Page Title</label>
                                    <input type="text" class="form-control" id="testimonialPageTitle" name="testimonial_page_title" value="Client Testimonials - Backsure Global Support">
                                </div>
                                <div class="col-md-6">
                                    <label for="testimonialPageSlug" class="form-label">Testimonials Page Slug</label>
                                    <div class="input-group">
                                        <span class="input-group-text">https://backsure.com/</span>
                                        <input type="text" class="form-control" id="testimonialPageSlug" name="testimonial_page_slug" value="testimonials">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label for="testimonialPageIntro" class="form-label">Testimonials Page Introduction</label>
                                    <textarea class="form-control" id="testimonialPageIntro" name="testimonial_page_intro" rows="3">Don't just take our word for it - hear what our satisfied clients have to say about their experience working with Backsure Global Support.</textarea>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Save Display Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Testimonial Modal -->
<div class="modal fade" id="addTestimonialModal" tabindex="-1" aria-labelledby="addTestimonialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTestimonialModalLabel">Add New Testimonial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="admin-testimonials.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_testimonial">
                <input type="hidden" name="testimonial_id" value="0">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="clientName" class="form-label">Client Name</label>
                            <input type="text" class="form-control" id="clientName" name="client_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="clientCompany" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="clientCompany" name="client_company">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="clientPosition" class="form-label">Position/Title</label>
                            <input type="text" class="form-control" id="clientPosition" name="client_position">
                        </div>
                        <div class="col-md-6">
                            <label for="clientRating" class="form-label">Rating</label>
                            <select class="form-select" id="clientRating" name="rating">
                                <option value="5" selected>5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="testimonialContent" class="form-label">Testimonial Content</label>
                        <textarea class="form-control" id="testimonialContent" name="content" rows="5" required></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="clientImage" class="form-label">Client Image</label>
                            <input type="file" class="form-control" id="clientImage" name="client_image" accept="image/*">
                            <small class="form-text">Recommended size: 300x300 pixels (square). Max size: 2MB.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="displayOrder" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="displayOrder" name="display_order" value="1" min="1">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="featured" name="featured" value="1">
                                <label class="form-check-label" for="featured">
                                    Featured Testimonial
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Testimonial</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Testimonial Modal -->
<div class="modal fade" id="editTestimonialModal" tabindex="-1" aria-labelledby="editTestimonialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTestimonialModalLabel">Edit Testimonial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="admin-testimonials.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_testimonial">
                <input type="hidden" name="testimonial_id" id="editTestimonialId" value="">
                <div class="modal-body">
                    <!-- Same fields as Add Testimonial Modal -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editClientName" class="form-label">Client Name</label>
                            <input type="text" class="form-control" id="editClientName" name="client_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editClientCompany" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="editClientCompany" name="client_company">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editClientPosition" class="form-label">Position/Title</label>
                            <input type="text" class="form-control" id="editClientPosition" name="client_position">
                        </div>
                        <div class="col-md-6">
                            <label for="editClientRating" class="form-label">Rating</label>
                            <select class="form-select" id="editClientRating" name="rating">
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editTestimonialContent" class="form-label">Testimonial Content</label>
                        <textarea class="form-control" id="editTestimonialContent" name="content" rows="5" required></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editClientImage" class="form-label">Client Image</label>
                            <div class="input-group mb-2">
                                <img src="" id="currentClientImage" class="img-thumbnail" width="100" height="100">
                            </div>
                            <input type="file" class="form-control" id="editClientImage" name="client_image" accept="image/*">
                            <small class="form-text">Leave empty to keep current image.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="editDisplayOrder" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="editDisplayOrder" name="display_order" value="1" min="1">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="editFeatured" name="featured" value="1">
                                <label class="form-check-label" for="editFeatured">
                                    Featured Testimonial
                                </label>
                            </div>
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

<!-- Add Partner Modal -->
<div class="modal fade" id="addPartnerModal" tabindex="-1" aria-labelledby="addPartnerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPartnerModalLabel">Add New Partner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="admin-testimonials.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_partner">
                <input type="hidden" name="partner_id" value="0">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="partnerName" class="form-label">Partner Name</label>
                        <input type="text" class="form-control" id="partnerName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="partnerLogo" class="form-label">Partner Logo</label>
                        <input type="file" class="form-control" id="partnerLogo" name="logo" accept="image/*" required>
                        <small class="form-text">Recommended size: 200x100 pixels. Transparent PNG preferred.</small>
                    </div>
                    <div class="mb-3">
                        <label for="partnerWebsite" class="form-label">Website URL</label>
                        <input type="url" class="form-control" id="partnerWebsite" name="website" placeholder="https://">
                    </div>
                    <div class="mb-3">
                        <label for="partnerCategory" class="form-label">Category</label>
                        <select class="form-select" id="partnerCategory" name="category">
                            <option value="Insurance Provider">Insurance Provider</option>
                            <option value="Risk Assessment">Risk Assessment</option>
                            <option value="Underwriting">Underwriting</option>
                            <option value="Claims Processing">Claims Processing</option>
                            <option value="InsurTech">InsurTech</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="partnerDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="partnerDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="partnerOrder" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="partnerOrder" name="display_order" value="1" min="1">
                        </div>
                        <div class="col-md-6">
                            <label for="partnerStatus" class="form-label">Status</label>
                            <select class="form-select" id="partnerStatus" name="status">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Partner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Partner Modal -->
<div class="modal fade" id="editPartnerModal" tabindex="-1" aria-labelledby="editPartnerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPartnerModalLabel">Edit Partner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="admin-testimonials.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_partner">
                <input type="hidden" name="partner_id" id="editPartnerId" value="">
                <div class="modal-body">
                    <!-- Same fields as Add Partner Modal -->
                    <div class="mb-3">
                        <label for="editPartnerName" class="form-label">Partner Name</label>
                        <input type="text" class="form-control" id="editPartnerName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPartnerLogo" class="form-label">Partner Logo</label>
                        <div class="input-group mb-2">
                            <img src="" id="currentPartnerLogo" class="img-thumbnail" width="100">
                        </div>
                        <input type="file" class="form-control" id="editPartnerLogo" name="logo" accept="image/*">
                        <small class="form-text">Leave empty to keep current logo.</small>
                    </div>
                    <div class="mb-3">
                        <label for="editPartnerWebsite" class="form-label">Website URL</label>
                        <input type="url" class="form-control" id="editPartnerWebsite" name="website" placeholder="https://">
                    </div>
                    <div class="mb-3">
                        <label for="editPartnerCategory" class="form-label">Category</label>
                        <select class="form-select" id="editPartnerCategory" name="category">
                            <option value="Insurance Provider">Insurance Provider</option>
                            <option value="Risk Assessment">Risk Assessment</option>
                            <option value="Underwriting">Underwriting</option>
                            <option value="Claims Processing">Claims Processing</option>
                            <option value="InsurTech">InsurTech</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editPartnerDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editPartnerDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editPartnerOrder" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="editPartnerOrder" name="display_order" value="1" min="1">
                        </div>
                        <div class="col-md-6">
                            <label for="editPartnerStatus" class="form-label">Status</label>
                            <select class="form-select" id="editPartnerStatus" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
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
                <form action="admin-testimonials.php" method="post" id="deleteForm">
                    <input type="hidden" name="action" id="deleteAction" value="">
                    <input type="hidden" name="item_id" id="deleteItemId" value="">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Add page-specific JavaScript
$page_scripts = "
$(document).ready(function() {
    // Edit testimonial button click handler
    $('.edit-testimonial').click(function() {
        var testimonialId = $(this).data('testimonial-id');
        
        // In a real implementation, you would make an AJAX call to get the testimonial details
        // For this example, we'll use our mock data
        const testimonials = " . json_encode($testimonials) . ";
        
        const testimonial = testimonials.find(t => t.id == testimonialId);
        
        if (testimonial) {
            $('#editTestimonialId').val(testimonial.id);
            $('#editClientName').val(testimonial.client_name);
            $('#editClientCompany').val(testimonial.client_company);
            $('#editClientPosition').val(testimonial.client_position);
            $('#editClientRating').val(testimonial.rating);
            $('#editTestimonialContent').val(testimonial.content);
            $('#currentClientImage').attr('src', testimonial.client_image);
            $('#editDisplayOrder').val(testimonial.display_order);
            $('#editStatus').val(testimonial.status);
            $('#editFeatured').prop('checked', testimonial.featured);
        }
    });
    
    // Edit partner button click handler
    $('.edit-partner').click(function() {
        var partnerId = $(this).data('partner-id');
        
        // In a real implementation, you would make an AJAX call to get the partner details
        // For this example, we'll use our mock data
        const partners = " . json_encode($partners) . ";
        
        const partner = partners.find(p => p.id == partnerId);
        
        if (partner) {
            $('#editPartnerId').val(partner.id);
            $('#editPartnerName').val(partner.name);
            $('#currentPartnerLogo').attr('src', partner.logo);
            $('#editPartnerWebsite').val(partner.website);
            $('#editPartnerCategory').val(partner.category);
            $('#editPartnerDescription').val(partner.description);
            $('#editPartnerOrder').val(partner.display_order);
            $('#editPartnerStatus').val(partner.status);
        }
    });
    
    // Delete testimonial confirmation
    $('.delete-testimonial').click(function() {
        var testimonialId = $(this).data('testimonial-id');
        $('#deleteAction').val('delete_testimonial');
        $('#deleteItemId').val(testimonialId);
        $('#deleteConfirmText').text('Are you sure you want to delete this testimonial? This action cannot be undone.');
        $('#deleteConfirmModal').modal('show');
    });
    
    // Delete partner confirmation
    $('.delete-partner').click(function() {
        var partnerId = $(this).data('partner-id');
        $('#deleteAction').val('delete_partner');
        $('#deleteItemId').val(partnerId);
        $('#deleteConfirmText').text('Are you sure you want to delete this partner? This action cannot be undone.');
        $('#deleteConfirmModal').modal('show');
    });
    
    // Status toggle for partners
    $('.status-toggle').change(function() {
        const partnerId = $(this).attr('id').replace('partnerSwitch', '');
        const newStatus = $(this).prop('checked') ? 'active' : 'inactive';
        const badge = $(this).siblings('label').find('.badge');
        
        // Update the badge
        badge.removeClass('bg-success bg-secondary')
             .addClass(newStatus === 'active' ? 'bg-success' : 'bg-secondary')
             .text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
        
        // In a real implementation, you would make an AJAX call to update the status
        console.log(`Partner ${partnerId} status changed to ${newStatus}`);
        
        // Update the row class based on status
        if (newStatus === 'inactive') {
            $(this).closest('tr').addClass('table-secondary');
        } else {
            $(this).closest('tr').removeClass('table-secondary');
        }
    });
    
    // Partner category filter
    $('#partnerCategoryFilter').change(function() {
        const category = $(this).val();
        if (category) {
            $('tbody tr').hide();
            $('tbody tr').each(function() {
                if ($(this).find('td:nth-child(4)').text() === category) {
                    $(this).show();
                }
            });
        } else {
            $('tbody tr').show();
        }
    });
    
    // Partner status filter
    $('#partnerStatusFilter').change(function() {
        const status = $(this).val();
        if (status) {
            $('tbody tr').hide();
            $('tbody tr').each(function() {
                const rowStatus = $(this).find('.badge').text().toLowerCase();
                if (rowStatus === status) {
                    $(this).show();
                }
            });
        } else {
            $('tbody tr').show();
        }
    });
    
    // Search functionality for partners
    $('#partnerSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        if (searchTerm) {
            $('tbody tr').hide();
            $('tbody tr').each(function() {
                const partnerName = $(this).find('td:nth-child(3)').text().toLowerCase();
                if (partnerName.includes(searchTerm)) {
                    $(this).show();
                }
            });
        } else {
            $('tbody tr').show();
        }
    });
    
    // Testimonial filters
    $('#testimonialStatusFilter, #testimonialFeaturedFilter').change(function() {
        applyTestimonialFilters();
    });
    
    // Search functionality for testimonials
    $('#testimonialSearch').on('input', function() {
        applyTestimonialFilters();
    });
    
    function applyTestimonialFilters() {
        const statusFilter = $('#testimonialStatusFilter').val();
        const featuredFilter = $('#testimonialFeaturedFilter').val();
        const searchTerm = $('#testimonialSearch').val().toLowerCase();
        
        $('.testimonials-grid .testimonial-card').each(function() {
            let showCard = true;
            
            // Check status filter
            if (statusFilter) {
                const cardStatus = $(this).find('.badge:not(.bg-warning)').text().toLowerCase();
                if (cardStatus !== statusFilter) {
                    showCard = false;
                }
            }
            
            // Check featured filter
            if (featuredFilter && showCard) {
                const isFeatured = $(this).find('.badge.bg-warning').length > 0;
                if ((featuredFilter === '1' && !isFeatured) || (featuredFilter === '0' && isFeatured)) {
                    showCard = false;
                }
            }
            
            // Check search term
            if (searchTerm && showCard) {
                const clientName = $(this).find('h5').text().toLowerCase();
                const clientCompany = $(this).find('p.text-muted').text().toLowerCase();
                const testimonialContent = $(this).find('.testimonial-content').text().toLowerCase();
                
                if (!clientName.includes(searchTerm) && !clientCompany.includes(searchTerm) && !testimonialContent.includes(searchTerm)) {
                    showCard = false;
                }
            }
            
            // Show or hide card
            $(this).closest('.col-md-6')[showCard ? 'show' : 'hide']();
        });
    }
    
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
    
    // Display style change preview
    $('#testimonialsDisplayStyle').change(function() {
        const displayStyle = $(this).val();
        // In a real implementation, you would update a preview of the display style
        console.log(`Testimonials display style changed to: ${displayStyle}`);
    });
    
    $('#partnerDisplayStyle').change(function() {
        const displayStyle = $(this).val();
        // In a real implementation, you would update a preview of the display style
        console.log(`Partner display style changed to: ${displayStyle}`);
    });
    
    // Preview image upload for testimonials
    $('#clientImage').change(function() {
        previewImage(this, 'client-image-preview');
    });
    
    $('#editClientImage').change(function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#currentClientImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Preview image upload for partners
    $('#partnerLogo').change(function() {
        previewImage(this, 'partner-logo-preview');
    });
    
    $('#editPartnerLogo').change(function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#currentPartnerLogo').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Create preview if it doesn't exist
                if ($('#' + previewId).length === 0) {
                    $(input).after('<div class=\"mt-2\"><img id=\"' + previewId + '\" src=\"\" class=\"img-thumbnail\" width=\"100\"></div>');
                }
                $('#' + previewId).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // Toggle grayscale preview for logos
    $('#grayscaleLogos').change(function() {
        if ($(this).is(':checked')) {
            $('.partner-logo-container img').addClass('grayscale');
        } else {
            $('.partner-logo-container img').removeClass('grayscale');
        }
    });
    
    // Toggle partner names display
    $('#showPartnerNames').change(function() {
        if ($(this).is(':checked')) {
            $('.partner-logo-container p').show();
        } else {
            $('.partner-logo-container p').hide();
        }
    });
    
    // Update partners per row preview
    $('#partnersPerRow').change(function() {
        const perRow = $(this).val();
        const colClass = `col-md-${12 / perRow}`;
        
        $('.partner-logos-preview > div').removeClass('col-md-2 col-md-3 col-md-4 col-md-6')
                                        .addClass(colClass);
    });
    
    // Update logo size preview
    $('#partnerLogoSize').change(function() {
        const size = $(this).val();
        const padding = size === 'small' ? 'p-2' : (size === 'medium' ? 'p-3' : 'p-4');
        
        $('.partner-logo-container').removeClass('p-2 p-3 p-4').addClass(padding);
    });
});
";

// Include footer (which will close remaining HTML tags)
require_once 'includes/admin-footer.php';
?>
