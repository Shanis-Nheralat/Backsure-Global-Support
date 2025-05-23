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
          <h3>Admin Name</h3>
          <span class="user-role">Super Admin</span>
        </div>
        <button id="user-dropdown-toggle" class="dropdown-toggle">
          <i class="fas fa-chevron-down"></i>
        </button>
        <ul id="user-dropdown" class="dropdown-menu">
          <li><a href="admin-profile.html"><i class="fas fa-user"></i> My Profile</a></li>
          <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
          <li><a href="index.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
      </div>
      
      <nav class="sidebar-nav">
        <ul>
          <li>
            <a href="admin-dashboard.html">
              <i class="fas fa-tachometer-alt"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li class="has-submenu open">
            <a href="javascript:void(0)">
              <i class="fas fa-edit"></i>
              <span>Content Management</span>
              <i class="fas fa-chevron-right submenu-icon"></i>
            </a>
            <ul class="submenu">
              <li><a href="admin-pages.html"><i class="fas fa-file-alt"></i> Pages Editor</a></li>
              <li class="active"><a href="admin-solutions.html"><i class="fas fa-lightbulb"></i> Solutions Editor</a></li>
              <li><a href="admin-blog.html"><i class="fas fa-blog"></i> Blog Management</a></li>
              <li><a href="admin-services.html"><i class="fas fa-briefcase"></i> Services Editor</a></li>
              <li><a href="admin-media.html"><i class="fas fa-images"></i> Media Library</a></li>
            </ul>
          </li>
          <!-- Other sidebar navigation items... -->
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
            <a href="admin-dashboard.html">Dashboard</a> / 
            <a href="admin-solutions.html">Solutions Editor</a>
          </div>
        </div>
        
        <div class="header-right">
          <div class="admin-search">
            <input type="text" placeholder="Search...">
            <button type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
          
          <div class="header-actions">
            <!-- Header action buttons... -->
          </div>
        </div>
      </header>
      
      <!-- Dashboard Content -->
      <div class="admin-content">
        <div class="page-header">
          <h1>Solution Pages Editor</h1>
          <div class="page-actions">
            <button id="previewSolution" class="btn btn-secondary">
              <i class="fas fa-eye"></i> Preview
            </button>
            <button id="saveSolution" class="btn btn-primary">
              <i class="fas fa-save"></i> Save Changes
            </button>
          </div>
        </div>
        
        <!-- Alerts -->
        <div id="alertSuccess" class="alert alert-success">
          <i class="fas fa-check-circle"></i> Solution page saved successfully!
        </div>
        <div id="alertError" class="alert alert-danger">
          <i class="fas fa-exclamation-circle"></i> <span id="errorMessage">Error saving solution page.</span>
        </div>
        
        <!-- Solution Selector -->
        <div class="solution-selector">
          <div class="form-group">
            <label for="solutionSelect">Select Solution to Edit</label>
            <div style="display: flex; align-items: center;">
              <select id="solutionSelect" class="form-control">
                <option value="solution-a">Solution A</option>
                <option value="solution-b">Solution B</option>
                <option value="solution-c">Solution C</option>
                <option value="solution-d">Solution D</option>
              </select>
              <a id="viewSolutionLink" href="#" target="_blank" class="preview-link">
                <i class="fas fa-external-link-alt"></i> View Current Page
              </a>
            </div>
          </div>
        </div>
        
        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="loading-indicator">
          <i class="fas fa-spinner fa-spin"></i>
          <p>Loading solution data...</p>
        </div>
        
        <!-- Solution Editor Form -->
        <form id="solutionForm" class="solution-editor">
          <!-- HERO SECTION -->
          <div class="form-section">
            <h3><i class="fas fa-image"></i> Hero Section</h3>
            <div class="form-group">
              <label for="heroTitle">Title</label>
              <input type="text" id="heroTitle" name="hero_title" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="heroDescription">Description</label>
              <textarea id="heroDescription" name="hero_description" class="form-control" required></textarea>
            </div>
            <div class="form-group">
              <label for="heroImage">Background Image URL</label>
              <input type="text" id="heroImage" name="hero_image_url" class="form-control" required>
              <small class="form-text">Enter the full URL or relative path to the image</small>
              <img id="heroImagePreview" class="image-preview">
            </div>
          </div>
          
          <!-- INTRO SECTION -->
          <div class="form-section">
            <h3><i class="fas fa-paragraph"></i> Intro Section</h3>
            <div class="form-group">
              <label for="introText">Intro Text</label>
              <textarea id="introText" name="intro_text" class="form-control" required></textarea>
            </div>
          </div>
          
          <!-- FEATURE BLOCK 1 -->
          <div class="form-section">
            <h3><i class="fas fa-cube"></i> Feature Block 1 (Text & Image)</h3>
            <div class="form-group">
              <label for="feature1Title">Title</label>
              <input type="text" id="feature1Title" name="feature1_title" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="feature1Description">Description</label>
              <textarea id="feature1Description" name="feature1_description" class="form-control" required></textarea>
            </div>
            <div class="form-group">
              <label for="feature1Image">Image URL</label>
              <input type="text" id="feature1Image" name="feature1_image_url" class="form-control" required>
              <img id="feature1ImagePreview" class="image-preview">
            </div>
          </div>
          
          <!-- FEATURE BLOCK 2 -->
          <div class="form-section">
            <h3><i class="fas fa-list"></i> Feature Block 2 (List & Image)</h3>
            <div class="form-group">
              <label for="feature2Title">Section Title</label>
              <input type="text" id="feature2Title" name="feature2_title" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="feature2Image">Image URL</label>
              <input type="text" id="feature2Image" name="feature2_image_url" class="form-control" required>
              <img id="feature2ImagePreview" class="image-preview">
            </div>
            
            <div class="feature-list-toggle">
              <i class="fas fa-chevron-down"></i> <strong>Feature List Items</strong>
            </div>
            
            <div id="feature2ListContainer" class="feature-list-container">
              <!-- Feature list items will be added here dynamically -->
            </div>
            
            <button type="button" id="addFeature2Item" class="add-feature">
              <i class="fas fa-plus"></i> Add Feature
            </button>
          </div>
          
          <!-- FEATURE BLOCK 3 -->
          <div class="form-section">
            <h3><i class="fas fa-list"></i> Feature Block 3 (List & Image)</h3>
            <div class="form-group">
              <label for="feature3Title">Section Title</label>
              <input type="text" id="feature3Title" name="feature3_title" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="feature3Image">Image URL</label>
              <input type="text" id="feature3Image" name="feature3_image_url" class="form-control" required>
              <img id="feature3ImagePreview" class="image-preview">
            </div>
            
            <div class="feature-list-toggle">
              <i class="fas fa-chevron-down"></i> <strong>Feature List Items</strong>
            </div>
            
            <div id="feature3ListContainer" class="feature-list-container">
              <!-- Feature list items will be added here dynamically -->
            </div>
            
            <button type="button" id="addFeature3Item" class="add-feature">
              <i class="fas fa-plus"></i> Add Feature
            </button>
          </div>
          
          <!-- SUMMARY SECTION -->
          <div class="form-section">
            <h3><i class="fas fa-align-left"></i> Summary Section</h3>
            <div class="form-group">
              <label for="summaryTitle">Title</label>
              <input type="text" id="summaryTitle" name="summary_title" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="summaryText">Description</label>
              <textarea id="summaryText" name="summary_text" class="form-control" required></textarea>
            </div>
          </div>
          
          <!-- CTA SECTION -->
          <div class="form-section">
            <h3><i class="fas fa-bullhorn"></i> Call to Action Section</h3>
            <div class="form-group">
              <label for="ctaTitle">Title</label>
              <input type="text" id="ctaTitle" name="cta_title" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="ctaText">Description</label>
              <textarea id="ctaText" name="cta_text" class="form-control" required></textarea>
            </div>
            
            <div class="row" style="display: flex; gap: 20px;">
              <div style="flex: 1;">
                <h4>Button 1 (Primary)</h4>
                <div class="form-group">
                  <label for="ctaButton1Text">Button 1 Text</label>
                  <input type="text" id="ctaButton1Text" name="cta_button1_text" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="ctaButton1Link">Button 1 Link</label>
                  <input type="text" id="ctaButton1Link" name="cta_button1_link" class="form-control" required>
                </div>
              </div>
              
              <div style="flex: 1;">
                <h4>Button 2 (Secondary)</h4>
                <div class="form-group">
                  <label for="ctaButton2Text">Button 2 Text</label>
                  <input type="text" id="ctaButton2Text" name="cta_button2_text" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="ctaButton2Link">Button 2 Link</label>
                  <input type="text" id="ctaButton2Link" name="cta_button2_link" class="form-control" required>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Form Actions -->
          <div class="editor-actions">
            <button type="button" id="resetForm" class="btn btn-default">
              <i class="fas fa-undo"></i> Reset Changes
            </button>
            <button type="submit" id="submitForm" class="btn btn-primary">
              <i class="fas fa-save"></i> Save Solution
            </button>
          </div>
        </form>
        
      </div>
      
      <!-- Admin Footer -->
      <footer class="admin-footer">
        <div class="footer-left">
          <p>&copy; 2025 Backsure Global Support. All rights reserved.</p>
        </div>
        <div class="footer-right">
          <span>Admin Panel v1.0</span>
        </div>
      </footer>
    </main>
  </div>
  
  <!-- Templates for dynamic content -->
  <template id="featureItemTemplate">
    <div class="feature-item">
      <div class="feature-header">
        <span class="feature-title">Feature Item</span>
        <button type="button" class="remove-feature">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="form-group">
        <label>Title</label>
        <input type="text" name="feature_title" class="form-control feature-title-input" required>
      </div>
      <div class="form-group">
        <label>Description</label>
        <input type="text" name="feature_description" class="form-control feature-description-input" required>
      </div>
    </div>
  </template>
  
  <!-- JavaScript for Solutions Editor -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize sidebar functionality
      initSidebar();
      
      // Initialize solution editor
      initSolutionEditor();
    });
    
    /**
     * Initialize sidebar functionality
     */
    function initSidebar() {
      const sidebarToggle = document.getElementById('sidebar-toggle');
      const adminContainer = document.querySelector('.admin-container');
      
      if (sidebarToggle && adminContainer) {
        sidebarToggle.addEventListener('click', function() {
          adminContainer.classList.toggle('sidebar-collapsed');
        });
      }
      
      // User dropdown toggle
      const userDropdownToggle = document.getElementById('user-dropdown-toggle');
      const userDropdown = document.getElementById('user-dropdown');
      
      if (userDropdownToggle && userDropdown) {
        userDropdownToggle.addEventListener('click', function(e) {
          e.stopPropagation();
          userDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
          if (!userDropdownToggle.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.classList.remove('show');
          }
        });
      }
    }
    
    /**
     * Initialize solution editor functionality
     */
    function initSolutionEditor() {
      // Elements
      const solutionSelect = document.getElementById('solutionSelect');
      const viewSolutionLink = document.getElementById('viewSolutionLink');
      const loadingIndicator = document.getElementById('loadingIndicator');
      const solutionForm = document.getElementById('solutionForm');
      const resetFormBtn = document.getElementById('resetForm');
      const saveSolutionBtn = document.getElementById('saveSolution');
      const previewSolutionBtn = document.getElementById('previewSolution');
      const alertSuccess = document.getElementById('alertSuccess');
      const alertError = document.getElementById('alertError');
      const errorMessage = document.getElementById('errorMessage');
      
      // Feature list containers
      const feature2ListContainer = document.getElementById('feature2ListContainer');
      const feature3ListContainer = document.getElementById('feature3ListContainer');
      
      // Add feature buttons
      const addFeature2ItemBtn = document.getElementById('addFeature2Item');
      const addFeature3ItemBtn = document.getElementById('addFeature3Item');
      
      // Feature list template
      const featureItemTemplate = document.getElementById('featureItemTemplate');
      
      // Image preview elements
      const heroImage = document.getElementById('heroImage');
      const heroImagePreview = document.getElementById('heroImagePreview');
      const feature1Image = document.getElementById('feature1Image');
      const feature1ImagePreview = document.getElementById('feature1ImagePreview');
      const feature2Image = document.getElementById('feature2Image');
      const feature2ImagePreview = document.getElementById('feature2ImagePreview');
      const feature3Image = document.getElementById('feature3Image');
      const feature3ImagePreview = document.getElementById('feature3ImagePreview');
      
      // Initialize feature list toggles
      const featureListToggles = document.querySelectorAll('.feature-list-toggle');
      featureListToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
          this.classList.toggle('collapsed');
          const listContainer = this.nextElementSibling;
          listContainer.classList.toggle('collapsed');
        });
      });
      
      // Initialize image previews
      initImagePreview(heroImage, heroImagePreview);
      initImagePreview(feature1Image, feature1ImagePreview);
      initImagePreview(feature2Image, feature2ImagePreview);
      initImagePreview(feature3Image, feature3ImagePreview);
      
      // Load solution data when solution select changes
      solutionSelect.addEventListener('change', function() {
        const solutionId = this.value;
        loadSolution(solutionId);
        updateViewLink(solutionId);
      });
      
      // Add feature item handlers
      addFeature2ItemBtn.addEventListener('click', function() {
        addFeatureItem(feature2ListContainer, 'feature2');
      });
      
      addFeature3ItemBtn.addEventListener('click', function() {
        addFeatureItem(feature3ListContainer, 'feature3');
      });
      
      // Form reset handler
      resetFormBtn.addEventListener('click', function() {
        const confirmReset = confirm('Are you sure you want to reset all changes?');
        if (confirmReset) {
          loadSolution(solutionSelect.value);
        }
      });
      
      // Form submit handler
      solutionForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveSolution();
      });
      
      // Save button handler
      saveSolutionBtn.addEventListener('click', function() {
        solutionForm.dispatchEvent(new Event('submit'));
      });
      
      // Preview button handler
      previewSolutionBtn.addEventListener('click', function() {
        const solutionId = solutionSelect.value;
        window.open(solutionId + '.php', '_blank');
      });
      
      // Initialize with first solution
      loadSolution(solutionSelect.value);
      updateViewLink(solutionSelect.value);
      
      /**
       * Load solution data from the server
       */
      function loadSolution(solutionId) {
        // Show loading indicator
        loadingIndicator.style.display = 'block';
        solutionForm.style.display = 'none';
        
        // Hide alerts
        alertSuccess.style.display = 'none';
        alertError.style.display = 'none';
        
        // Fetch solution data
        fetch('ajax-get-solution.php?id=' + solutionId)
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Fill form with solution data
              fillSolutionForm(data.data);
            } else {
              // Show error
              alertError.style.display = 'block';
              errorMessage.textContent = data.message || 'Error loading solution data';
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alertError.style.display = 'block';
            errorMessage.textContent = 'Network error loading solution data';
          })
          .finally(() => {
            // Hide loading indicator
            loadingIndicator.style.display = 'none';
            solutionForm.style.display = 'block';
          });
      }
      
      /**
       * Fill form with solution data
       */
      function fillSolutionForm(solution) {
        // Set hidden field for solution ID
        const solutionIdInput = document.createElement('input');
        solutionIdInput.type = 'hidden';
        solutionIdInput.name = 'solution_id';
        solutionIdInput.value = solution.solution_id;
        solutionForm.appendChild(solutionIdInput);
        
        // Basic fields
        document.getElementById('heroTitle').value = solution.hero_title || '';
        document.getElementById('heroDescription').value = solution.hero_description || '';
        document.getElementById('heroImage').value = solution.hero_image_url || '';
        document.getElementById('introText').value = solution.intro_text || '';
        document.getElementById('feature1Title').value = solution.feature1_title || '';
        document.getElementById('feature1Description').value = solution.feature1_description || '';
        document.getElementById('feature1Image').value = solution.feature1_image_url || '';
        document.getElementById('feature2Title').value = solution.feature2_title || '';
        document.getElementById('feature2Image').value = solution.feature2_image_url || '';
        document.getElementById('feature3Title').value = solution.feature3_title || '';
        document.getElementById('feature3Image').value = solution.feature3_image_url || '';
        document.getElementById('summaryTitle').value = solution.summary_title || '';
        document.getElementById('summaryText').value = solution.summary_text || '';
        document.getElementById('ctaTitle').value = solution.cta_title || '';
        document.getElementById('ctaText').value = solution.cta_text || '';
        document.getElementById('ctaButton1Text').value = solution.cta_button1_text || '';
        document.getElementById('ctaButton1Link').value = solution.cta_button1_link || '';
        document.getElementById('ctaButton2Text').value = solution.cta_button2_text || '';
        document.getElementById('ctaButton2Link').value = solution.cta_button2_link || '';
        
        // Update image previews
        updateImagePreview(heroImagePreview, solution.hero_image_url);
        updateImagePreview(feature1ImagePreview, solution.feature1_image_url);
        updateImagePreview(feature2ImagePreview, solution.feature2_image_url);
        updateImagePreview(feature3ImagePreview, solution.feature3_image_url);
        
        // Clear feature lists
        feature2ListContainer.innerHTML = '';
        feature3ListContainer.innerHTML = '';
        
        // Add feature2 items
        if (solution.feature2_features && Array.isArray(solution.feature2_features)) {
          solution.feature2_features.forEach(feature => {
            addFeatureItem(feature2ListContainer, 'feature2', feature);
          });
        }
        
        // Add feature3 items
        if (solution.feature3_features && Array.isArray(solution.feature3_features)) {
          solution.feature3_features.forEach(feature => {
            addFeatureItem(feature3ListContainer, 'feature3', feature);
          });
        }
      }
      
      /**
       * Add a feature item to a feature list
       */
      function addFeatureItem(container, featureType, data = null) {
        // Clone template
        const templateContent = featureItemTemplate.content.cloneNode(true);
        const featureItem = templateContent.querySelector('.feature-item');
        
        // Set input names properly for form submission
        const titleInput = featureItem.querySelector('.feature-title-input');
        const descriptionInput = featureItem.querySelector('.feature-description-input');
        
        titleInput.name = `${featureType}_features[${container.children.length}][title]`;
        descriptionInput.name = `${featureType}_features[${container.children.length}][description]`;
        
        // Fill with data if provided
        if (data) {
          titleInput.value = data.title || '';
          descriptionInput.value = data.description || '';
        }
        
        // Add remove handler
        const removeButton = featureItem.querySelector('.remove-feature');
        removeButton.addEventListener('click', function() {
          container.removeChild(featureItem);
          // Update indices of remaining items
          updateFeatureIndices(container, featureType);
        });
        
        // Add to container
        container.appendChild(featureItem);
      }
      
      /**
       * Update feature indices after removal
       */
      function updateFeatureIndices(container, featureType) {
        const items = container.querySelectorAll('.feature-item');
        items.forEach((item, index) => {
          const titleInput = item.querySelector('.feature-title-input');
          const descriptionInput = item.querySelector('.feature-description-input');
          
          titleInput.name = `${featureType}_features[${index}][title]`;
          descriptionInput.name = `${featureType}_features[${index}][description]`;
        });
      }
      
      /**
       * Initialize image preview functionality
       */
      function initImagePreview(input, previewElement) {
        input.addEventListener('input', function() {
          updateImagePreview(previewElement, this.value);
        });
      }
      
      /**
       * Update image preview with URL
       */
      function updateImagePreview(previewElement, url) {
        if (url && url.trim() !== '') {
          previewElement.src = url;
          previewElement.style.display = 'block';
        } else {
          previewElement.style.display = 'none';
        }
      }
      
      /**
       * Update view link when solution changes
       */
      function updateViewLink(solutionId) {
        viewSolutionLink.href = solutionId + '.php';
      }
      
      /**
       * Save solution data
       */
      function saveSolution() {
        // Show loading indicator
        loadingIndicator.style.display = 'block';
        
        // Hide alerts
        alertSuccess.style.display = 'none';
        alertError.style.display = 'none';
        
        // Prepare form data
        const formData = new FormData(solutionForm);
        
        // Send data to server
        fetch('ajax-save-solution.php', {
          method: 'POST',
          body: formData
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Show success message
              alertSuccess.style.display = 'block';
              // Scroll to top to show message
              window.scrollTo({ top: 0, behavior: 'smooth' });
              // Update view link
              updateViewLink(data.solution_id);
            } else {
              // Show error
              alertError.style.display = 'block';
              errorMessage.textContent = data.message || 'Error saving solution data';
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alertError.style.display = 'block';
            errorMessage.textContent = 'Network error saving solution data';
          })
          .finally(() => {
            // Hide loading indicator
            loadingIndicator.style.display = 'none';
          });
      }
    }
  </script>
</body>
</html>
