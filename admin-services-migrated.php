<?php
/**
 * Admin Services
 * Services management for the admin panel
 */

// Include authentication component
require_once 'admin-auth.php';

// Check role permissions - only Super Admin and Marketing Admin can access this page
require_admin_role(['admin', 'superadmin', 'marketing']);

// Include database configuration
require_once 'db_config.php';

// Set page variables
$page_title = 'Services Management';
$current_page = 'services';
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => 'admin-dashboard.php'],
    ['title' => 'Services Management', 'url' => 'admin-services.php']
];

// Get admin info
$admin_user = get_admin_user();
$admin_username = $admin_user['username'];
$admin_role = $admin_user['role'];

// Super Admins can edit and delete, Marketing Admins can only edit
$canDelete = has_admin_role(['admin', 'superadmin']);

// Initialize variables
$services = [];
$message = '';
$messageType = '';
$editingService = null;
$categories = ['Accounting', 'HR', 'IT', 'Marketing', 'Customer Support', 'Administration'];

// Database connection
try {
    $pdo = get_db_connection();
    
    // Create services table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        icon VARCHAR(50),
        image VARCHAR(255),
        category VARCHAR(100),
        tags TEXT,
        display_order INT DEFAULT 0,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Process form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Add/Edit Service
        if (isset($_POST['action']) && ($_POST['action'] === 'add' || $_POST['action'] === 'edit')) {
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $icon = trim($_POST['icon']);
            $category = $_POST['category'];
            $tags = trim($_POST['tags']);
            $display_order = (int)$_POST['display_order'];
            $status = isset($_POST['status']) ? $_POST['status'] : 'active';
            
            // Handle image upload
            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/services/';
                
                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = basename($_FILES['image']['name']);
                $targetFile = $uploadDir . time() . '_' . $fileName;
                
                // Check if image file is an actual image
                $check = getimagesize($_FILES['image']['tmp_name']);
                if ($check !== false) {
                    // Upload file
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                        $image = $targetFile;
                    }
                }
            } elseif (isset($_POST['existing_image']) && !empty($_POST['existing_image'])) {
                $image = $_POST['existing_image'];
            }
            
            // Validate inputs
            if (empty($title)) {
                $message = "Service title is required.";
                $messageType = "error";
            } else {
                if ($_POST['action'] === 'add') {
                    // Insert new service
                    $stmt = $pdo->prepare("INSERT INTO services (title, description, icon, image, category, tags, display_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute([$title, $description, $icon, $image, $category, $tags, $display_order, $status])) {
                        $message = "Service added successfully.";
                        $messageType = "success";
                    } else {
                        $message = "Error adding service.";
                        $messageType = "error";
                    }
                } else { // Edit
                    $serviceId = $_POST['service_id'];
                    
                    // Check if user has permission to edit
                    if ($_SESSION['admin_role'] === 'marketing' && !$canDelete) {
                        // Marketing admins can only update content, not status
                        $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, icon = ?, image = ?, category = ?, tags = ?, display_order = ? WHERE id = ?");
                        if ($stmt->execute([$title, $description, $icon, $image, $category, $tags, $display_order, $serviceId])) {
                            $message = "Service updated successfully.";
                            $messageType = "success";
                        } else {
                            $message = "Error updating service.";
                            $messageType = "error";
                        }
                    } else {
                        // Super admins can update everything
                        $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, icon = ?, image = ?, category = ?, tags = ?, display_order = ?, status = ? WHERE id = ?");
                        if ($stmt->execute([$title, $description, $icon, $image, $category, $tags, $display_order, $status, $serviceId])) {
                            $message = "Service updated successfully.";
                            $messageType = "success";
                        } else {
                            $message = "Error updating service.";
                            $messageType = "error";
                        }
                    }
                }
            }
        }
        
        // Delete Service
        if (isset($_POST['action']) && $_POST['action'] === 'delete' && $canDelete) {
            $serviceId = $_POST['service_id'];
            
            // Get service image before deleting
            $stmt = $pdo->prepare("SELECT image FROM services WHERE id = ?");
            $stmt->execute([$serviceId]);
            $serviceImage = $stmt->fetchColumn();
            
            // Delete service
            $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
            if ($stmt->execute([$serviceId])) {
                // Delete service image if exists
                if (!empty($serviceImage) && file_exists($serviceImage)) {
                    unlink($serviceImage);
                }
                
                $message = "Service deleted successfully.";
                $messageType = "success";
            } else {
                $message = "Error deleting service.";
                $messageType = "error";
            }
        }
    }
    
    // Handle edit request from GET
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $serviceId = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$serviceId]);
        $editingService = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get all services for display
    $stmt = $pdo->query("SELECT * FROM services ORDER BY display_order, title");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $message = "Database error: " . $e->getMessage();
    $messageType = "error";
}

// Include header template
include 'admin-head.php';
include 'admin-sidebar.php';
?>

<!-- Main Content Area -->
<main class="admin-main">
  <?php include 'admin-header.php'; ?>
  
  <!-- Page Content -->
  <div class="admin-content">
    <div class="page-header">
      <h1>Services Management</h1>
      <div class="page-header-actions">
        <button id="add-service-btn" class="btn btn-primary">
          <i class="fas fa-plus"></i> Add New Service
        </button>
      </div>
    </div>
    
    <?php if (!empty($message)): ?>
    <div class="message message-<?php echo $messageType; ?>">
      <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
      <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>
    
    <!-- Services Grid -->
    <div class="services-grid">
      <?php foreach ($services as $service): ?>
      <div class="service-card">
        <?php if (!empty($service['image']) && file_exists($service['image'])): ?>
        <div class="service-image" style="background-image: url('<?php echo htmlspecialchars($service['image']); ?>');">
        <?php else: ?>
        <div class="service-image no-image">
          <i class="<?php echo !empty($service['icon']) ? htmlspecialchars($service['icon']) : 'fas fa-briefcase'; ?>"></i>
        <?php endif; ?>
          <div class="service-status <?php echo htmlspecialchars($service['status']); ?>">
            <?php echo htmlspecialchars(ucfirst($service['status'])); ?>
          </div>
        </div>
        <div class="service-content">
          <h3 class="service-title">
            <i class="service-icon <?php echo !empty($service['icon']) ? htmlspecialchars($service['icon']) : 'fas fa-briefcase'; ?>"></i>
            <?php echo htmlspecialchars($service['title']); ?>
          </h3>
          <div class="service-category">
            <?php echo htmlspecialchars($service['category']); ?>
          </div>
          <div class="service-description">
            <?php echo htmlspecialchars($service['description']); ?>
          </div>
          <?php if (!empty($service['tags'])): ?>
          <div class="service-tags">
            <?php 
            $tags = explode(',', $service['tags']);
            foreach ($tags as $tag): 
              $tag = trim($tag);
              if (!empty($tag)):
            ?>
            <span class="service-tag"><?php echo htmlspecialchars($tag); ?></span>
            <?php 
              endif;
            endforeach; 
            ?>
          </div>
          <?php endif; ?>
          <div class="service-actions">
            <a href="admin-services.php?action=edit&id=<?php echo $service['id']; ?>" class="btn btn-info btn-sm"><i class="fas fa-edit"></i> Edit</a>
            
            <?php if ($canDelete): ?>
            <button class="btn btn-danger btn-sm delete-service-btn" data-id="<?php echo $service['id']; ?>" data-title="<?php echo htmlspecialchars($service['title']); ?>">
              <i class="fas fa-trash"></i> Delete
            </button>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      
      <?php if (empty($services)): ?>
      <div class="card" style="grid-column: 1 / -1;">
        <div class="card-body" style="text-align: center; padding: 50px 20px;">
          <i class="fas fa-briefcase" style="font-size: 48px; color: var(--gray-300); margin-bottom: 20px;"></i>
          <h3>No services found</h3>
          <p>Start by adding your first service using the button above.</p>
        </div>
      </div>
      <?php endif; ?>
    </div>
    
    <!-- Add/Edit Service Modal -->
    <div id="service-modal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h3><?php echo $editingService ? 'Edit Service' : 'Add New Service'; ?></h3>
          <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
          <form id="service-form" method="post" action="admin-services.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?php echo $editingService ? 'edit' : 'add'; ?>">
            <?php if ($editingService): ?>
            <input type="hidden" name="service_id" value="<?php echo $editingService['id']; ?>">
            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($editingService['image']); ?>">
            <?php endif; ?>
            
            <div class="form-row">
              <div class="form-group">
                <label for="title" class="form-label required">Service Title</label>
                <input type="text" id="title" name="title" class="form-control" value="<?php echo $editingService ? htmlspecialchars($editingService['title']) : ''; ?>" required>
              </div>
              
              <div class="form-group">
                <label for="category" class="form-label required">Category</label>
                <select id="category" name="category" class="form-control" required>
                  <option value="">Select Category</option>
                  <?php foreach ($categories as $category): ?>
                  <option value="<?php echo htmlspecialchars($category); ?>" <?php echo ($editingService && $editingService['category'] === $category) ? 'selected' : ''; ?>><?php echo htmlspecialchars($category); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            
            <div class="form-group">
              <label for="description" class="form-label">Description</label>
              <textarea id="description" name="description" class="form-control"><?php echo $editingService ? htmlspecialchars($editingService['description']) : ''; ?></textarea>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label for="icon" class="form-label">Icon (FontAwesome)</label>
                <input type="text" id="icon" name="icon" class="form-control" value="<?php echo $editingService ? htmlspecialchars($editingService['icon']) : 'fas fa-briefcase'; ?>" placeholder="fas fa-briefcase">
                <div class="form-text">Choose from the icon picker below or enter a FontAwesome class.</div>
                
                <div class="icon-picker">
                  <?php 
                  $icons = ['fas fa-briefcase', 'fas fa-chart-line', 'fas fa-users', 'fas fa-cogs', 'fas fa-laptop', 'fas fa-file-invoice-dollar', 'fas fa-headset', 'fas fa-shield-alt', 'fas fa-globe', 'fas fa-server', 'fas fa-chart-bar', 'fas fa-desktop', 'fas fa-mobile-alt', 'fas fa-envelope', 'fas fa-search', 'fas fa-database'];
                  foreach ($icons as $icon): 
                  ?>
                  <div class="icon-option <?php echo ($editingService && $editingService['icon'] === $icon) ? 'selected' : ''; ?>" data-icon="<?php echo $icon; ?>">
                    <i class="<?php echo $icon; ?>"></i>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
              
              <div class="form-group">
                <label for="image" class="form-label">Image</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                <div class="form-text">Recommended size: 600x400px. Leave empty to keep current image.</div>
                
                <?php if ($editingService && !empty($editingService['image']) && file_exists($editingService['image'])): ?>
                <div class="image-preview" style="background-image: url('<?php echo htmlspecialchars($editingService['image']); ?>');"></div>
                <?php else: ?>
                <div class="image-preview">No image selected</div>
                <?php endif; ?>
              </div>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label for="tags" class="form-label">Tags</label>
                <input type="text" id="tags" name="tags" class="form-control" value="<?php echo $editingService ? htmlspecialchars($editingService['tags']) : ''; ?>" placeholder="Tag1, Tag2, Tag3">
                <div class="form-text">Separate tags with commas.</div>
              </div>
              
              <div class="form-group">
                <label for="display_order" class="form-label">Display Order</label>
                <input type="number" id="display_order" name="display_order" class="form-control" value="<?php echo $editingService ? (int)$editingService['display_order'] : 0; ?>" min="0">
                <div class="form-text">Lower numbers will be displayed first.</div>
              </div>
            </div>
            
            <div class="form-group">
              <label for="status" class="form-label">Status</label>
              <select id="status" name="status" class="form-control" <?php echo ($_SESSION['admin_role'] === 'marketing' && !$canDelete) ? 'disabled' : ''; ?>>
                <option value="active" <?php echo ($editingService && $editingService['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo ($editingService && $editingService['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
              </select>
              <?php if ($_SESSION['admin_role'] === 'marketing' && !$canDelete): ?>
              <div class="form-text">Marketing admins cannot change service status.</div>
              <?php endif; ?>
            </div>
          
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
              <button type="submit" class="btn btn-primary"><?php echo $editingService ? 'Update Service' : 'Add Service'; ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <!-- Delete Service Modal -->
    <div id="delete-service-modal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Delete Service</h3>
          <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete the service <strong id="delete-service-title"></strong>?</p>
          <p>This action cannot be undone.</p>
          
          <form id="delete-service-form" method="post" action="admin-services.php">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="service_id" id="delete-service-id">
            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary modal-close-btn">Cancel</button>
              <button type="submit" class="btn btn-danger">Delete Service</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Page-specific JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize service editor
      initServiceEditor();
    });
    
    /**
     * Initialize service editor functionality
     */
    function initServiceEditor() {
      // Show add service modal when button is clicked
      const addServiceBtn = document.getElementById('add-service-btn');
      if (addServiceBtn) {
        addServiceBtn.addEventListener('click', function() {
          document.getElementById('service-modal').style.display = 'block';
        });
      }
      
      // Service edit modal was triggered from GET parameter
      <?php if ($editingService): ?>
      document.getElementById('service-modal').style.display = 'block';
      <?php endif; ?>
      
      // Icon picker functionality
      const iconPicker = document.querySelectorAll('.icon-option');
      const iconInput = document.getElementById('icon');
      
      iconPicker.forEach(icon => {
        icon.addEventListener('click', function() {
          // Remove selected class from all icons
          iconPicker.forEach(i => i.classList.remove('selected'));
          
          // Add selected class to clicked icon
          this.classList.add('selected');
          
          // Update input value
          iconInput.value = this.getAttribute('data-icon');
        });
      });
      
      // Image preview functionality
      const imageInput = document.getElementById('image');
      const imagePreview = document.querySelector('.image-preview');
      
      if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
          if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
              imagePreview.style.backgroundImage = 'url(' + e.target.result + ')';
              imagePreview.textContent = '';
            };
            
            reader.readAsDataURL(this.files[0]);
          } else {
            imagePreview.style.backgroundImage = 'none';
            imagePreview.textContent = 'No image selected';
          }
        });
      }
      
      // Delete service functionality
      const deleteButtons = document.querySelectorAll('.delete-service-btn');
      
      deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
          const serviceId = this.getAttribute('data-id');
          const serviceTitle = this.getAttribute('data-title');
          
          document.getElementById('delete-service-id').value = serviceId;
          document.getElementById('delete-service-title').textContent = serviceTitle;
          
          document.getElementById('delete-service-modal').style.display = 'block';
        });
      });
    }
  </script>
  
  <?php include 'admin-footer.php'; ?>
</main>
</div>
</body>
</html>
