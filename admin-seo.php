<?php
/**
 * Admin SEO Settings
 * Manages SEO configuration for the website
 */

// Include authentication and common functions
require_once 'admin-auth.php';
require_once 'admin-notifications.php';
require_once 'settings-functions.php';

// Require admin authentication
require_admin_auth();
require_admin_role(['admin']);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['settings'])) {
    $updated = 0;
    
    foreach ($_POST['settings'] as $group => $settings) {
        foreach ($settings as $key => $value) {
            // Get the field type
            $type = get_setting_type($group, $key);
            
            // Handle file uploads
            if ($type == 'image' && isset($_FILES['settings_files']) && 
                isset($_FILES['settings_files']['name'][$group][$key]) && 
                !empty($_FILES['settings_files']['name'][$group][$key])) {
                
                $file = [
                    'name' => $_FILES['settings_files']['name'][$group][$key],
                    'type' => $_FILES['settings_files']['type'][$group][$key],
                    'tmp_name' => $_FILES['settings_files']['tmp_name'][$group][$key],
                    'error' => $_FILES['settings_files']['error'][$group][$key],
                    'size' => $_FILES['settings_files']['size'][$group][$key]
                ];
                
                $value = handle_file_upload($file, get_setting($group, $key));
            }
            
            // Update the setting
            if (set_setting($group, $key, $value, $type)) {
                $updated++;
            }
        }
    }
    
    if ($updated > 0) {
        set_admin_notification('success', 'SEO settings updated successfully.', '#', get_admin_user()['id']);
    } else {
        set_admin_notification('info', 'No changes were made to SEO settings.', '#', get_admin_user()['id']);
    }
    
    // Redirect to prevent form resubmission
    header('Location: admin-seo.php');
    exit;
}

// Get settings for each tab/section
$homepage_seo = [
    'meta_title' => get_setting('seo_homepage', 'meta_title'),
    'meta_description' => get_setting('seo_homepage', 'meta_description'),
    'meta_keywords' => get_setting('seo_homepage', 'meta_keywords'),
    'og_image' => get_setting('seo_homepage', 'og_image')
];

$default_meta = [
    'default_title' => get_setting('seo_default', 'default_title'),
    'default_description' => get_setting('seo_default', 'default_description'),
    'robots_tag' => get_setting('seo_default', 'robots_tag')
];

$social_sharing = [
    'facebook_app_id' => get_setting('social_sharing', 'facebook_app_id'),
    'twitter_card_type' => get_setting('social_sharing', 'twitter_card_type'),
    'og_default_title' => get_setting('social_sharing', 'og_default_title'),
    'og_default_description' => get_setting('social_sharing', 'og_default_description')
];

$sitemap_config = [
    'sitemap_url' => get_setting('sitemap_config', 'sitemap_url', site_url() . '/sitemap.xml'),
    'robots_txt' => get_setting('sitemap_config', 'robots_txt'),
    'ping_engines' => get_setting('sitemap_config', 'ping_engines', true)
];

$google_tools = [
    'ga_id' => get_setting('google_tools', 'ga_id'),
    'gtm_id' => get_setting('google_tools', 'gtm_id'),
    'search_console_code' => get_setting('google_tools', 'search_console_code')
];

$seo_advanced = [
    'canonical_url' => get_setting('seo_advanced', 'canonical_url'),
    'enable_breadcrumb_schema' => get_setting('seo_advanced', 'enable_breadcrumb_schema', true),
    'structured_data_json' => get_setting('seo_advanced', 'structured_data_json')
];

// Set page variables
$page_title = 'SEO Settings';
$current_page = 'seo_settings';
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => 'admin-dashboard.php'],
    ['title' => 'SEO Settings', 'url' => '#']
];

// Include templates
include 'admin-head.php';
include 'admin-sidebar.php';
include 'admin-header.php';
?>

<main class="admin-main">
    <div class="admin-content container-fluid py-4">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <h1><?php echo $page_title; ?></h1>
        </div>
        
        <?php display_notifications(); ?>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <ul class="nav nav-tabs card-header-tabs" id="seoTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="homepage-tab" data-bs-toggle="tab" data-bs-target="#homepage" type="button" role="tab" aria-controls="homepage" aria-selected="true">Homepage SEO</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="default-tab" data-bs-toggle="tab" data-bs-target="#default" type="button" role="tab" aria-controls="default" aria-selected="false">Default Meta Tags</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab" aria-controls="social" aria-selected="false">Social Sharing</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sitemap-tab" data-bs-toggle="tab" data-bs-target="#sitemap" type="button" role="tab" aria-controls="sitemap" aria-selected="false">Sitemap & Robots</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="google-tab" data-bs-toggle="tab" data-bs-target="#google" type="button" role="tab" aria-controls="google" aria-selected="false">Google Tools</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="advanced-tab" data-bs-toggle="tab" data-bs-target="#advanced" type="button" role="tab" aria-controls="advanced" aria-selected="false">Advanced</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <form action="admin-seo.php" method="post" enctype="multipart/form-data"><div class="tab-content" id="seoTabsContent">
                    <!-- Homepage SEO Tab -->
                    <div class="tab-pane fade show active" id="homepage" role="tabpanel" aria-labelledby="homepage-tab">
                        <div class="row">
                            <div class="col-md-8">
                                <?php echo render_setting_field('seo_homepage', 'meta_title', 'Meta Title', 'text', [
                                    'placeholder' => 'Enter homepage title',
                                    'required' => true,
                                    'description' => 'The title that appears in search engine results (recommended: 50-60 characters)'
                                ]); ?>
                                
                                <?php echo render_setting_field('seo_homepage', 'meta_description', 'Meta Description', 'textarea', [
                                    'placeholder' => 'Enter homepage description',
                                    'required' => true,
                                    'rows' => 3,
                                    'description' => 'The description that appears in search engine results (recommended: 150-160 characters)'
                                ]); ?>
                                
                                <?php echo render_setting_field('seo_homepage', 'meta_keywords', 'Meta Keywords', 'textarea', [
                                    'placeholder' => 'Enter keywords separated by commas',
                                    'rows' => 2,
                                    'description' => 'Keywords related to your homepage (less important for modern SEO)'
                                ]); ?>
                                
                                <?php echo render_setting_field('seo_homepage', 'og_image', 'Open Graph Image', 'image', [
                                    'description' => 'Image displayed when sharing the homepage on social media (recommended: 1200×630 pixels)'
                                ]); ?>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="mb-0">SEO Preview</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="seo-preview">
                                            <div class="seo-title" id="previewTitle">
                                                <?php echo !empty($homepage_seo['meta_title']) ? $homepage_seo['meta_title'] : 'Title Example'; ?>
                                            </div>
                                            <div class="seo-url" id="previewUrl">
                                                <?php echo site_url(); ?>
                                            </div>
                                            <div class="seo-description" id="previewDescription">
                                                <?php echo !empty($homepage_seo['meta_description']) ? $homepage_seo['meta_description'] : 'This is an example description that would appear in search results. It should be compelling and relevant to the page content.'; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">SEO Tips</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="seo-tips">
                                            <li>Keep titles under 60 characters</li>
                                            <li>Descriptions should be 150-160 characters</li>
                                            <li>Include your main keyword in the title</li>
                                            <li>Make descriptions compelling to increase click-through rates</li>
                                            <li>Use a high-quality image for social sharing</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Default Meta Tags Tab -->
                    <div class="tab-pane fade" id="default" role="tabpanel" aria-labelledby="default-tab">
                        <div class="row">
                            <div class="col-md-8">
                                <?php echo render_setting_field('seo_default', 'default_title', 'Default Title Pattern', 'text', [
                                    'placeholder' => 'e.g., %page_title% | %site_name%',
                                    'description' => 'Use %page_title% and %site_name% as placeholders'
                                ]); ?>
                                
                                <?php echo render_setting_field('seo_default', 'default_description', 'Default Description', 'textarea', [
                                    'placeholder' => 'Default description for pages without a specific one',
                                    'rows' => 3
                                ]); ?>
                                
                                <?php echo render_setting_field('seo_default', 'robots_tag', 'Default Robots Meta Tag', 'text', [
                                    'placeholder' => 'e.g., index, follow',
                                    'description' => 'Controls how search engines index your site'
                                ]); ?>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Default SEO Info</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>These settings apply to pages without specific SEO information.</p>
                                        <p>For the title pattern, you can use:</p>
                                        <ul>
                                            <li><code>%page_title%</code> - The page's title</li>
                                            <li><code>%site_name%</code> - Your website name</li>
                                            <li><code>%separator%</code> - A separator (|, -, etc.)</li>
                                        </ul>
                                        <p>Example: <code>%page_title% | %site_name%</code></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Social Sharing Tab -->
                    <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                        <div class="row">
                            <div class="col-md-8">
                                <?php echo render_setting_field('social_sharing', 'facebook_app_id', 'Facebook App ID', 'text', [
                                    'placeholder' => 'Enter your Facebook App ID',
                                    'description' => 'Used for Facebook Insights and advanced sharing features'
                                ]); ?>
                                
                                <?php echo render_setting_field('social_sharing', 'twitter_card_type', 'Twitter Card Type', 'text', [
                                    'placeholder' => 'e.g., summary, summary_large_image',
                                    'description' => 'Controls how content appears when shared on Twitter'
                                ]); ?>
                                
                                <?php echo render_setting_field('social_sharing', 'og_default_title', 'Default OpenGraph Title', 'text', [
                                    'placeholder' => 'Default title for social sharing',
                                    'description' => 'Used when a page doesn\'t have a specific OG title'
                                ]); ?>
                                
                                <?php echo render_setting_field('social_sharing', 'og_default_description', 'Default OpenGraph Description', 'textarea', [
                                    'placeholder' => 'Default description for social sharing',
                                    'rows' => 3,
                                    'description' => 'Used when a page doesn\'t have a specific OG description'
                                ]); ?>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Social Sharing Info</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>These settings control how your content appears when shared on social media platforms.</p>
                                        <p><strong>Twitter Card Types:</strong></p>
                                        <ul>
                                            <li><code>summary</code> - Standard card with small image</li>
                                            <li><code>summary_large_image</code> - Card with large image</li>
                                        </ul>
                                        <p>The Facebook App ID allows you to use Facebook Insights to track sharing metrics.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sitemap & Robots Tab -->
                    <div class="tab-pane fade" id="sitemap" role="tabpanel" aria-labelledby="sitemap-tab">
                        <div class="row">
                            <div class="col-md-8">
                                <?php echo render_setting_field('sitemap_config', 'sitemap_url', 'Sitemap URL', 'text', [
                                    'readonly' => true,
                                    'description' => 'The URL of your XML sitemap (generated automatically)'
                                ]); ?>
                                
                                <?php echo render_setting_field('sitemap_config', 'robots_txt', 'Robots.txt Content', 'textarea', [
                                    'rows' => 8,
                                    'description' => 'Content of your robots.txt file (controls search engine crawling)'
                                ]); ?>
                                
                                <?php echo render_setting_field('sitemap_config', 'ping_engines', 'Ping Search Engines', 'boolean', [
                                    'description' => 'Automatically notify search engines when your sitemap is updated'
                                ]); ?>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="mb-0">Sitemap Info</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>The sitemap is automatically generated and contains all public URLs on your site.</p>
                                        <p>You can manually submit your sitemap to:</p>
                                        <ul>
                                            <li><a href="https://search.google.com/search-console" target="_blank">Google Search Console</a></li>
                                            <li><a href="https://www.bing.com/webmaster/home/mysites" target="_blank">Bing Webmaster Tools</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Robots.txt Example</h5>
                                    </div>
                                    <div class="card-body">
                                        <pre>User-agent: *
Allow: /
Disallow: /admin/
Disallow: /private/

Sitemap: <?php echo site_url(); ?>/sitemap.xml</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Google Tools Tab -->
                    <div class="tab-pane fade" id="google" role="tabpanel" aria-labelledby="google-tab">
                        <div class="row">
                            <div class="col-md-8">
                                <?php echo render_setting_field('google_tools', 'ga_id', 'Google Analytics ID', 'text', [
                                    'placeholder' => 'e.g., G-XXXXXXXXXX or UA-XXXXXXXX-X',
                                    'description' => 'Your Google Analytics tracking ID'
                                ]); ?>
                                
                                <?php echo render_setting_field('google_tools', 'gtm_id', 'Google Tag Manager ID', 'text', [
                                    'placeholder' => 'e.g., GTM-XXXXXXX',
                                    'description' => 'Your Google Tag Manager container ID'
                                ]); ?>
                                
                                <?php echo render_setting_field('google_tools', 'search_console_code', 'Search Console Verification', 'text', [
                                    'placeholder' => 'HTML tag verification code',
                                    'description' => 'Google Search Console verification meta tag (without the HTML tags)'
                                ]); ?>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Google Tools Info</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>These integration codes allow you to use Google's tools for tracking and optimizing your website.</p>
                                        <p><strong>You can find these in:</strong></p>
                                        <ul>
                                            <li><a href="https://analytics.google.com/" target="_blank">Google Analytics</a></li>
                                            <li><a href="https://tagmanager.google.com/" target="_blank">Google Tag Manager</a></li>
                                            <li><a href="https://search.google.com/search-console" target="_blank">Google Search Console</a></li>
                                        </ul>
                                        <p>For Search Console verification, only include the content portion of the meta tag.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Advanced Tab -->
                    <div class="tab-pane fade" id="advanced" role="tabpanel" aria-labelledby="advanced-tab">
                        <div class="row">
                            <div class="col-md-8">
                                <?php echo render_setting_field('seo_advanced', 'canonical_url', 'Canonical URL Format', 'text', [
                                    'placeholder' => 'e.g., https://example.com/%page_path%',
                                    'description' => 'Format for canonical URLs (use %page_path% as a placeholder)'
                                ]); ?>
                                
                                <?php echo render_setting_field('seo_advanced', 'enable_breadcrumb_schema', 'Enable Breadcrumb Schema', 'boolean', [
                                    'description' => 'Add structured data for breadcrumbs to improve search results'
                                ]); ?>
                                
                                <?php echo render_setting_field('seo_advanced', 'structured_data_json', 'Additional Structured Data', 'textarea', [
                                    'placeholder' => 'Enter valid JSON-LD markup',
                                    'rows' => 8,
                                    'description' => 'Add custom JSON-LD structured data to the homepage (advanced)'
                                ]); ?>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Advanced SEO Info</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>These settings are for advanced users who understand structured data and canonical URLs.</p>
                                        <p><strong>Resources:</strong></p>
                                        <ul>
                                            <li><a href="https://schema.org/" target="_blank">Schema.org</a></li>
                                            <li><a href="https://search.google.com/test/rich-results" target="_blank">Rich Results Test</a></li>
                                            <li><a href="https://developers.google.com/search/docs/advanced/structured-data/intro-structured-data" target="_blank">Structured Data Guidelines</a></li>
                                        </ul>
                                        <p>Be careful when editing these settings, as incorrect values can negatively impact SEO.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Save SEO Settings
                    </button>
                    <button type="reset" class="btn btn-secondary ms-2">
                        <i class="fas fa-undo me-2"></i> Reset Changes
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Real-time SEO preview updates
    const titleInput = document.getElementById('setting_seo_homepage_meta_title');
    const descriptionInput = document.getElementById('setting_seo_homepage_meta_description');
    const previewTitle = document.getElementById('previewTitle');
    const previewDescription = document.getElementById('previewDescription');
    
    if (titleInput && previewTitle) {
        titleInput.addEventListener('input', function() {
            previewTitle.textContent = this.value || 'Title Example';
        });
    }
    
    if (descriptionInput && previewDescription) {
        descriptionInput.addEventListener('input', function() {
            previewDescription.textContent = this.value || 'This is an example description that would appear in search results. It should be compelling and relevant to the page content.';
        });
    }
    
    // Media library integration
    document.querySelectorAll('.upload-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const fileInput = document.getElementById('file_' + targetId);
            if (fileInput) {
                fileInput.click();
            }
        });
    });
    
    document.querySelectorAll('.media-library-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const type = this.dataset.type;
            window.open('media-library.php?modal=1&target=' + targetId + '&type=' + type, 'mediaLibrary', 'width=800,height=600');
        });
    });
    
    document.querySelectorAll('.hidden-file-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const targetId = this.dataset.target;
            const previewId = 'preview_' + targetId;
            const previewDiv = document.getElementById(previewId);
            
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    let preview = '';
                    
                    if (file.type.startsWith('image/')) {
                        preview = `<img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-height: 150px">`;
                    } else {
                        preview = `<div class="file-preview"><i class="fas fa-file"></i> ${file.name}</div>`;
                    }
                    
                    preview += `<button type="button" class="btn btn-sm btn-danger remove-file" data-target="${targetId}">Remove</button>`;
                    previewDiv.innerHTML = preview;
                }
                
                reader.readAsDataURL(file);
            }
        });
    });
    
    // Remove file functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-file')) {
            const targetId = e.target.dataset.target;
            const inputField = document.getElementById(targetId);
            const previewDiv = document.getElementById('preview_' + targetId);
            
            if (inputField) {
                inputField.value = '';
            }
            
            if (previewDiv) {
                previewDiv.innerHTML = '<div class="no-file">No file selected</div>';
            }
        }
    });
});

// Function to handle media library selection
function setMediaFile(targetId, filePath, fileName) {
    const inputField = document.getElementById(targetId);
    const previewDiv = document.getElementById('preview_' + targetId);
    
    if (inputField) {
        inputField.value = filePath;
    }
    
    if (previewDiv) {
        let preview = '';
        
        // Check if file is an image
        if (/\.(jpg|jpeg|png|gif)$/i.test(fileName)) {
            preview = `<img src="${filePath}?v=${Date.now()}" alt="Preview" class="img-thumbnail" style="max-height: 150px">`;
        } else {
            preview = `<div class="file-preview"><i class="fas fa-file"></i> ${fileName}</div>`;
        }
        
        preview += `<button type="button" class="btn btn-sm btn-danger remove-file" data-target="${targetId}">Remove</button>`;
        previewDiv.innerHTML = preview;
    }
}
</script>

<?php include 'admin-footer.php'; ?>
