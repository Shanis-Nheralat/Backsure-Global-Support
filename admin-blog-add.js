/**
 * Backsure Global Support
 * Blog Post Editor JavaScript
 * Version: 1.0
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize Quill Editor
  initQuillEditor();
  
  // Initialize date picker
  initFlatpickr();
  
  // Initialize event listeners
  initEventListeners();
  
  // Initialize Media Library
  initMediaLibrary();
  
  // Load post data if editing existing post
  loadPostData();
  
  // Auto-save functionality
  initAutoSave();
});

/**
 * Initialize Quill rich text editor
 */
function initQuillEditor() {
  // Check if Quill is loaded
  if (typeof Quill === 'undefined') {
    console.error('Quill editor not loaded');
    return;
  }
  
  // Initialize Quill with toolbar options
  const quill = new Quill('#editor-content', {
    modules: {
      toolbar: '#editor-toolbar'
    },
    placeholder: 'Write your blog post content here...',
    theme: 'snow'
  });
  
  // Set global reference to access it elsewhere
  window.quillEditor = quill;
  
  // Add image upload handler
  const toolbar = quill.getModule('toolbar');
  toolbar.addHandler('image', function() {
    // Show media modal when image button is clicked
    document.getElementById('media-modal').style.display = 'block';
    // Activate media library tab
    activateTab('media-library');
  });
}

/**
 * Initialize Flatpickr date picker
 */
function initFlatpickr() {
  // Check if Flatpickr is loaded
  if (typeof flatpickr === 'undefined') {
    console.error('Flatpickr not loaded');
    return;
  }
  
  // Initialize publish date picker
  flatpickr("#publish-date", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    minDate: "today",
    defaultDate: new Date(new Date().getTime() + 24 * 60 * 60 * 1000),
    time_24hr: false
  });
}

/**
 * Initialize all event listeners
 */
function initEventListeners() {
  // Post title and slug handling
  initTitleSlugHandling();
  
  // Excerpt character counter
  initExcerptCounter();
  
  // Meta title and description counters
  initMetaCounters();
  
  // Publish options
  initPublishOptions();
  
  // Tag suggestions
  initTagSuggestions();
  
  // Category handling
  initCategoryHandlers();
  
  // Button actions
  initButtonActions();
  
  // Tab handling in modals
  initTabHandling();
}

/**
 * Initialize title and slug handling
 */
function initTitleSlugHandling() {
  const titleInput = document.getElementById('post-title');
  const slugField = document.getElementById('post-slug');
  const editSlugBtn = document.querySelector('.post-url-preview .edit-btn');
  
  // Generate slug from title
  titleInput.addEventListener('blur', function() {
    // Only update slug if it hasn't been manually edited
    if (!slugField.dataset.edited) {
      const title = titleInput.value.trim();
      if (title) {
        const slug = createSlug(title);
        slugField.textContent = slug;
      }
    }
  });
  
  // Make slug editable
  editSlugBtn.addEventListener('click', function() {
    slugField.focus();
  });
  
  // Mark slug as manually edited
  slugField.addEventListener('input', function() {
    slugField.dataset.edited = 'true';
  });
  
  // Format slug on blur
  slugField.addEventListener('blur', function() {
    const slug = createSlug(slugField.textContent);
    slugField.textContent = slug;
  });
}

/**
 * Create a URL-friendly slug from text
 */
function createSlug(text) {
  return text
    .toLowerCase()
    .replace(/[^\w\s-]/g, '') // Remove special characters
    .replace(/\s+/g, '-')     // Replace spaces with hyphens
    .replace(/-+/g, '-')      // Replace multiple hyphens with single hyphen
    .trim();
}

/**
 * Initialize excerpt character counter
 */
function initExcerptCounter() {
  const excerptField = document.getElementById('post-excerpt');
  const counter = document.getElementById('excerpt-characters');
  
  function updateCount() {
    const count = excerptField.value.length;
    counter.textContent = count;
    
    if (count > 160) {
      counter.style.color = '#e74a3b'; // Red for over limit
    } else {
      counter.style.color = ''; // Default color
    }
  }
  
  excerptField.addEventListener('input', updateCount);
  updateCount(); // Initial count
}

/**
 * Initialize meta title and description counters
 */
function initMetaCounters() {
  const metaTitle = document.getElementById('meta-title');
  const titleCounter = document.getElementById('title-characters');
  const metaDescription = document.getElementById('meta-description');
  const descriptionCounter = document.getElementById('description-characters');
  
  function updateTitleCount() {
    const count = metaTitle.value.length;
    titleCounter.textContent = count;
    
    if (count > 60) {
      titleCounter.style.color = '#e74a3b'; // Red for over limit
    } else {
      titleCounter.style.color = ''; // Default color
    }
  }
  
  function updateDescriptionCount() {
    const count = metaDescription.value.length;
    descriptionCounter.textContent = count;
    
    if (count > 160) {
      descriptionCounter.style.color = '#e74a3b'; // Red for over limit
    } else {
      descriptionCounter.style.color = ''; // Default color
    }
  }
  
  metaTitle.addEventListener('input', updateTitleCount);
  metaDescription.addEventListener('input', updateDescriptionCount);
  
  updateTitleCount(); // Initial counts
  updateDescriptionCount();
}

/**
 * Initialize publish options
 */
function initPublishOptions() {
  const statusSelect = document.getElementById('post-status');
  const scheduledOptions = document.querySelector('.scheduled-options');
  const visibilitySelect = document.getElementById('post-visibility');
  const passwordField = document.querySelector('.password-field');
  
  // Status change handler
  statusSelect.addEventListener('change', function() {
    if (this.value === 'scheduled') {
      scheduledOptions.style.display = 'block';
    } else {
      scheduledOptions.style.display = 'none';
    }
  });
  
  // Visibility change handler
  visibilitySelect.addEventListener('change', function() {
    if (this.value === 'password') {
      passwordField.style.display = 'block';
    } else {
      passwordField.style.display = 'none';
    }
  });
}

/**
 * Initialize tag suggestions
 */
function initTagSuggestions() {
  const tagInput = document.getElementById('post-tags');
  const tagSuggestions = document.querySelectorAll('.tag-suggestion');
  
  tagSuggestions.forEach(tag => {
    tag.addEventListener('click', function() {
      const tagText = this.textContent.trim();
      const currentTags = tagInput.value.split(',').map(t => t.trim()).filter(t => t);
      
      // Only add if not already in the list
      if (!currentTags.includes(tagText)) {
        const newTags = currentTags.length > 0 
          ? currentTags.join(', ') + ', ' + tagText
          : tagText;
        
        tagInput.value = newTags;
      }
    });
  });
}

/**
 * Initialize category handlers
 */
function initCategoryHandlers() {
  const addCategoryBtn = document.getElementById('add-category-btn');
  const categoryModal = document.getElementById('category-modal');
  const cancelCategoryBtn = document.getElementById('cancel-category');
  const addCategorySubmitBtn = document.getElementById('add-category');
  
  // Show category modal
  addCategoryBtn.addEventListener('click', function() {
    categoryModal.style.display = 'block';
  });
  
  // Hide category modal
  cancelCategoryBtn.addEventListener('click', function() {
    categoryModal.style.display = 'none';
  });
  
  // Handle category submission
  addCategorySubmitBtn.addEventListener('click', function() {
    const categoryName = document.getElementById('category-name').value.trim();
    const categorySlug = document.getElementById('category-slug').value.trim();
    
    if (!categoryName) {
      alert('Category name is required');
      return;
    }
    
    // In a real implementation, this would make an API call to add the category
    console.log('Adding category:', categoryName, categorySlug);
    
    // Add new category to the list
    const categoryList = document.querySelector('.category-list');
    const newId = 'cat-' + (categorySlug || createSlug(categoryName));
    const newValue = categorySlug || createSlug(categoryName);
    
    const newCategory = document.createElement('div');
    newCategory.className = 'checkbox-group';
    newCategory.innerHTML = `
      <input id="${newId}" name="category[]" type="checkbox" value="${newValue}" checked>
      <label for="${newId}">${categoryName}</label>
    `;
    
    categoryList.appendChild(newCategory);
    
    // Reset and close modal
    document.getElementById('category-name').value = '';
    document.getElementById('category-slug').value = '';
    categoryModal.style.display = 'none';
  });
  
  // Auto-generate slug for new category
  const categoryNameInput = document.getElementById('category-name');
  const categorySlugInput = document.getElementById('category-slug');
  
  categoryNameInput.addEventListener('blur', function() {
    if (!categorySlugInput.value.trim()) {
      categorySlugInput.value = createSlug(categoryNameInput.value);
    }
  });
}

/**
 * Initialize button actions
 */
function initButtonActions() {
  // Save Draft button
  const saveDraftBtn = document.getElementById('save-draft');
  const saveDraftSidebarBtn = document.getElementById('save-draft-sidebar');
  
  saveDraftBtn.addEventListener('click', function() {
    savePost('draft');
  });
  
  saveDraftSidebarBtn.addEventListener('click', function() {
    savePost('draft');
  });
  
  // Preview button
  const previewBtn = document.getElementById('preview-post');
  
  previewBtn.addEventListener('click', function() {
    previewPost();
  });
  
  // Publish button
  const publishBtn = document.getElementById('publish-post');
  const publishSidebarBtn = document.getElementById('publish-post-sidebar');
  
  publishBtn.addEventListener('click', function() {
    const status = document.getElementById('post-status').value;
    savePost(status);
  });
  
  publishSidebarBtn.addEventListener('click', function() {
    const status = document.getElementById('post-status').value;
    savePost(status);
  });
  
  // Featured image buttons
  const uploadImageBtn = document.getElementById('upload-image-btn');
  const removeImageBtn = document.getElementById('remove-image-btn');
  const imagePlaceholder = document.getElementById('image-placeholder');
  const imagePreview = document.getElementById('featured-image-preview');
  
  uploadImageBtn.addEventListener('click', function() {
    document.getElementById('media-modal').style.display = 'block';
    activateTab('media-library');
  });
  
  removeImageBtn.addEventListener('click', function() {
    // Clear featured image
    imagePreview.style.display = 'none';
    imagePlaceholder.style.display = 'flex';
    removeImageBtn.style.display = 'none';
    
    // Clear hidden input for featured image if there is one
    const featuredImageInput = document.getElementById('featured-image-id');
    if (featuredImageInput) {
      featuredImageInput.value = '';
    }
  });
}

/**
 * Initialize tab handling in modals
 */
function initTabHandling() {
  const tabButtons = document.querySelectorAll('.tab-btn');
  
  tabButtons.forEach(button => {
    button.addEventListener('click', function() {
      const tabId = this.getAttribute('data-tab');
      activateTab(tabId);
    });
  });
}

/**
 * Activate a specific tab
 */
function activateTab(tabId) {
  // Update tab buttons
  const tabButtons = document.querySelectorAll('.tab-btn');
  tabButtons.forEach(btn => {
    if (btn.getAttribute('data-tab') === tabId) {
      btn.classList.add('active');
    } else {
      btn.classList.remove('active');
    }
  });
  
  // Update tab content
  const tabContents = document.querySelectorAll('.tab-content');
  tabContents.forEach(content => {
    if (content.id === tabId) {
      content.classList.add('active');
    } else {
      content.classList.remove('active');
    }
  });
}

/**
 * Initialize Media Library
 */
function initMediaLibrary() {
  const mediaItems = document.querySelectorAll('.media-item');
  const selectMediaBtn = document.getElementById('select-media');
  const mediaModal = document.getElementById('media-modal');
  const closeModalBtns = mediaModal.querySelectorAll('.close-modal');
  
  // Close modal
  closeModalBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      mediaModal.style.display = 'none';
    });
  });
  
  // Click outside modal to close
  mediaModal.addEventListener('click', function(event) {
    if (event.target === mediaModal) {
      mediaModal.style.display = 'none';
    }
  });
  
  // Media item selection
  mediaItems.forEach(item => {
    item.addEventListener('click', function() {
      // Toggle selected state
      mediaItems.forEach(i => i.classList.remove('selected'));
      this.classList.add('selected');
    });
  });
  
  // Set selected media as featured image
  selectMediaBtn.addEventListener('click', function() {
    const selectedItem = document.querySelector('.media-item.selected');
    
    if (selectedItem) {
      const imageUrl = selectedItem.querySelector('img').src;
      const imagePreview = document.getElementById('featured-image-preview');
      const imagePlaceholder = document.getElementById('image-placeholder');
      const removeImageBtn = document.getElementById('remove-image-btn');
      
      // Update featured image
      imagePreview.src = imageUrl;
      imagePreview.style.display = 'block';
      imagePlaceholder.style.display = 'none';
      removeImageBtn.style.display = 'inline-block';
      
      // Store media ID if there's a hidden input
      const mediaId = selectedItem.getAttribute('data-id');
      const featuredImageInput = document.getElementById('featured-image-id');
      if (featuredImageInput && mediaId) {
        featuredImageInput.value = mediaId;
      }
      
      // Close modal
      mediaModal.style.display = 'none';
    }
  });
  
  // Initialize file upload
  initFileUpload();
}

/**
 * Initialize file upload functionality
 */
function initFileUpload() {
  const dropzone = document.getElementById('upload-dropzone');
  const fileInput = document.getElementById('file-input');
  const uploadBtn = document.getElementById('select-files');
  const filesList = document.getElementById('upload-files-list');
  const uploadAllBtn = document.getElementById('upload-all');
  const uploadProgress = document.querySelector('.upload-progress');
  
  // Trigger file input when button is clicked
  uploadBtn.addEventListener('click', function() {
    fileInput.click();
  });
  
  // Handle selected files
  fileInput.addEventListener('change', function() {
    if (this.files.length > 0) {
      displaySelectedFiles(this.files);
    }
  });
  
  // Drag and drop functionality
  dropzone.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('dragover');
  });
  
  dropzone.addEventListener('dragleave', function() {
    this.classList.remove('dragover');
  });
  
  dropzone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
    
    if (e.dataTransfer.files.length > 0) {
      displaySelectedFiles(e.dataTransfer.files);
    }
  });
  
  // Upload all files button
  uploadAllBtn.addEventListener('click', function() {
    // In a real implementation, this would upload the files
    // For demo, just simulate upload
    simulateFileUpload();
  });
  
  /**
   * Display selected files in the list
   */
  function displaySelectedFiles(files) {
    filesList.innerHTML = '';
    
    Array.from(files).forEach((file, index) => {
      const fileItem = document.createElement('div');
      fileItem.className = 'file-item';
      
      // Determine icon based on file type
      let iconClass = 'fa-file';
      if (file.type.startsWith('image/')) {
        iconClass = 'fa-file-image';
      } else if (file.type.startsWith('video/')) {
        iconClass = 'fa-file-video';
      } else if (file.type.startsWith('audio/')) {
        iconClass = 'fa-file-audio';
      } else if (file.type.includes('pdf')) {
        iconClass = 'fa-file-pdf';
      } else if (file.type.includes('word')) {
        iconClass = 'fa-file-word';
      } else if (file.type.includes('excel') || file.type.includes('sheet')) {
        iconClass = 'fa-file-excel';
      }
      
      // Format file size
      const fileSize = formatFileSize(file.size);
      
      fileItem.innerHTML = `
        <div class="file-info">
          <div class="file-icon">
            <i class="fas ${iconClass}"></i>
          </div>
          <div>
            <div class="file-name">${file.name}</div>
            <div class="file-size">${fileSize}</div>
          </div>
        </div>
        <div class="file-progress">
          <div class="progress-bar" data-index="${index}">
            <div class="progress-fill" style="width: 0%"></div>
          </div>
        </div>
      `;
      
      filesList.appendChild(fileItem);
    });
    
    // Show upload progress container
    uploadProgress.style.display = 'block';
  }
  
  /**
   * Format file size in KB, MB, etc.
   */
  function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }
  
  /**
   * Simulate file upload (for demo purposes)
   */
  function simulateFileUpload() {
    const progressBars = document.querySelectorAll('.progress-fill');
    const totalFiles = progressBars.length;
    let completedFiles = 0;
    
    progressBars.forEach((bar, index) => {
      let progress = 0;
      const interval = setInterval(() => {
        progress += Math.random() * 10;
        
        if (progress >= 100) {
          progress = 100;
          clearInterval(interval);
          completedFiles++;
          
          if (completedFiles === totalFiles) {
            // All files uploaded
            setTimeout(() => {
              alert('All files uploaded successfully!');
              
              // In a real implementation, you would refresh the media library here
              // For now, just close the upload tab and show the library
              activateTab('media-library');
              uploadProgress.style.display = 'none';
            }, 500);
          }
        }
        
        bar.style.width = progress + '%';
      }, 300 + (index * 50)); // Stagger the uploads
    });
  }
}

/**
 * Load post data if editing existing post
 */
function loadPostData() {
  // Check if we're editing an existing post
  const urlParams = new URLSearchParams(window.location.search);
  const postId = urlParams.get('id');
  
  if (postId) {
    // In a real implementation, this would fetch post data from the API
    // For demo, just simulate loading
    console.log('Loading post ID:', postId);
    
    // Set page title to indicate editing mode
    document.title = 'Edit Blog Post | Backsure Global Support';
    
    // Mock data for demo purposes
    if (postId === '1') {
      // Fill in form with sample data
      document.getElementById('post-title').value = 'Introduction to Backsure Global Support Services';
      document.getElementById('post-slug').textContent = 'introduction-to-backsure';
      document.getElementById('post-slug').dataset.edited = 'true';
      
      // Set editor content
      if (window.quillEditor) {
        window.quillEditor.root.innerHTML = `
          <h2>Welcome to Backsure Global Support</h2>
          <p>Backsure Global Support provides comprehensive business support services to help companies grow and succeed in today's competitive global market.</p>
          <h3>Our Services</h3>
          <p>We offer a wide range of services designed to help businesses of all sizes:</p>
          <ul>
            <li>Finance & Accounting</li>
            <li>HR & Administration</li>
            <li>Dedicated Teams</li>
            <li>Insurance Solutions</li>
            <li>Business Care Plans</li>
          </ul>
          <h3>Why Choose Us</h3>
          <p>With years of experience and a dedicated team of professionals, we provide tailored solutions that meet your unique business needs.</p>
        `;
      }
      
      // Set excerpt
      document.getElementById('post-excerpt').value = 'Learn about our comprehensive services designed to help your business thrive in today\'s competitive market.';
      
      // Set categories
      document.getElementById('cat-business').checked = true;
      
      // Set tags
      document.getElementById('post-tags').value = 'services, support, overview';
      
      // Set meta info
      document.getElementById('meta-title').value = 'Introduction to Backsure Global Support Services | Business Support';
      document.getElementById('meta-description').value = 'Discover how Backsure Global Support can help your business with comprehensive services including finance, HR, dedicated teams, and more.';
      
      // Set status
      document.getElementById('post-status').value = 'published';
      
      // Update character counters
      updateAllCounters();
      
      // Set featured image (mock)
      const imagePreview = document.getElementById('featured-image-preview');
      const imagePlaceholder = document.getElementById('image-placeholder');
      const removeImageBtn = document.getElementById('remove-image-btn');
      
      imagePreview.src = 'images/blog/placeholder-1.jpg';
      imagePreview.style.display = 'block';
      imagePlaceholder.style.display = 'none';
      removeImageBtn.style.display = 'inline-block';
    }
  }
}

/**
 * Update all character counters
 */
function updateAllCounters() {
  // Excerpt counter
  const excerptField = document.getElementById('post-excerpt');
  const excerptCounter = document.getElementById('excerpt-characters');
  excerptCounter.textContent = excerptField.value.length;
  
  // Meta title counter
  const metaTitleField = document.getElementById('meta-title');
  const titleCounter = document.getElementById('title-characters');
  titleCounter.textContent = metaTitleField.value.length;
  
  // Meta description counter
  const metaDescField = document.getElementById('meta-description');
  const descCounter = document.getElementById('description-characters');
  descCounter.textContent = metaDescField.value.length;
}

/**
 * Initialize auto-save functionality
 */
function initAutoSave() {
  // In a real implementation, this would periodically save the post
  // For demo, just log that it would save
  let saveInterval = null;
  const SAVE_DELAY = 60000; // 60 seconds
  
  function startAutoSave() {
    if (saveInterval) {
      clearInterval(saveInterval);
    }
    
    saveInterval = setInterval(() => {
      if (hasPostChanged()) {
        console.log('Auto-saving post...');
        // In a real implementation, this would call an API
        showSaveNotification();
      }
    }, SAVE_DELAY);
  }
  
  // Simple check if anything has changed
  function hasPostChanged() {
    return true; // Always save in demo
  }
  
  // Show a notification that content was saved
  function showSaveNotification() {
    const notification = document.createElement('div');
    notification.className = 'save-notification';
    notification.innerHTML = '<i class="fas fa-save"></i> Draft saved';
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
      notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
      notification.classList.remove('show');
      setTimeout(() => {
        notification.remove();
      }, 300);
    }, 3000);
  }
  
  // Add save notification styles
  const style = document.createElement('style');
  style.textContent = `
    .save-notification {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: rgba(0, 0, 0, 0.7);
      color: white;
      padding: 10px 15px;
      border-radius: 4px;
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.9rem;
      transform: translateY(20px);
      opacity: 0;
      transition: all 0.3s;
      z-index: 9999;
    }
    
    .save-notification.show {
      transform: translateY(0);
      opacity: 1;
    }
  `;
  document.head.appendChild(style);
  
  // Start auto-save
  startAutoSave();
}

/**
 * Save post with specified status
 */
function savePost(status) {
  // Get form data
  const title = document.getElementById('post-title').value.trim();
  
  if (!title) {
    alert('Post title is required');
    return;
  }
  
  // Get content from Quill
  const content = window.quillEditor ? window.quillEditor.root.innerHTML : '';
  
  if (!content || content === '<p><br></p>') {
    alert('Post content is required');
    return;
  }
  
  // In a real implementation, this would submit form data to the API
  console.log('Saving post with status:', status);
  console.log('Title:', title);
  console.log('Content length:', content.length);
  
  // Simulate saving
  const savingMsg = status === 'published' ? 'Publishing...' : 'Saving...';
  showSavingIndicator(savingMsg);
  
  setTimeout(() => {
    const successMsg = status === 'published' ? 'Post published successfully!' : 'Draft saved successfully!';
    hideSavingIndicator();
    alert(successMsg);
    
    // Redirect to blog list in a real implementation
    if (status === 'published') {
      window.location.href = 'admin-blog.html';
    }
  }, 1500);
}

/**
 * Preview the post in a new tab
 */
function previewPost() {
  // In a real implementation, this would create a temporary preview
  // For demo, just show an alert
  alert('In a real implementation, this would open a preview in a new tab.');
}

/**
 * Show saving indicator
 */
function showSavingIndicator(message) {
  // Remove any existing indicator
  hideSavingIndicator();
  
  // Create indicator
  const indicator = document.createElement('div');
  indicator.id = 'saving-indicator';
  indicator.innerHTML = `
    <div class="spinner"></div>
    <span>${message}</span>
  `;
  
  document.body.appendChild(indicator);
  
  // Add styles
  const style = document.createElement('style');
  style.textContent = `
    #saving-indicator {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 10000;
      color: white;
      font-size: 1.2rem;
    }
    
    #saving-indicator .spinner {
      width: 40px;
      height: 40px;
      border: 4px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: white;
      animation: spin 1s linear infinite;
      margin-bottom: 10px;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  `;
  document.head.appendChild(style);
}

/**
 * Hide saving indicator
 */
function hideSavingIndicator() {
  const indicator = document.getElementById('saving-indicator');
  if (indicator) {
    indicator.remove();
  }
}