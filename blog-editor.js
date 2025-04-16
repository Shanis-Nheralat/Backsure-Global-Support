/**
 * blog-editor.js
 * Backsure Global Support - Blog Post Editor JavaScript
 * Handles functionality for adding and editing blog posts
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize rich text editor
  initializeEditor();
  
  // Initialize publish options
  initializePublishOptions();
  
  // Initialize featured image uploader
  initializeFeaturedImage();
  
  // Initialize slug generation
  initializeSlugGenerator();
  
  // Initialize SEO character counters
  initializeCharacterCounters();
  
  // Initialize tag suggestions
  initializeTagSuggestions();
  
  // Initialize media library
  initializeMediaLibrary();
  
  // Handle form submission
  initializeFormSubmission();
});

/**
 * Initialize rich text editor (Quill)
 */
function initializeEditor() {
  // Check if Quill is available
  if (typeof Quill === 'undefined') {
    console.warn('Quill editor not loaded. Basic textarea will be used instead.');
    return;
  }
  
  // Initialize Quill editor
  const quill = new Quill('#editor-content', {
    theme: 'snow',
    modules: {
      toolbar: '#editor-toolbar'
    },
    placeholder: 'Write your post content here...'
  });
  
  // Handle image uploads
  const toolbar = quill.getModule('toolbar');
  
  toolbar.addHandler('image', function() {
    // Open media modal
    const mediaModal = document.getElementById('media-modal');
    
    if (mediaModal) {
      mediaModal.style.display = 'flex';
      
      // Set a flag to indicate we're selecting for the editor
      mediaModal.setAttribute('data-selection-for', 'editor');
    }
  });
}

/**
 * Initialize publish options
 */
function initializePublishOptions() {
  const statusSelect = document.getElementById('post-status');
  const scheduledOptions = document.querySelector('.scheduled-options');
  const visibilitySelect = document.getElementById('post-visibility');
  const passwordField = document.querySelector('.password-field');
  
  // Status change handler
  if (statusSelect && scheduledOptions) {
    statusSelect.addEventListener('change', function() {
      if (this.value === 'scheduled') {
        scheduledOptions.style.display = 'block';
      } else {
        scheduledOptions.style.display = 'none';
      }
    });
  }
  
  // Visibility change handler
  if (visibilitySelect && passwordField) {
    visibilitySelect.addEventListener('change', function() {
      if (this.value === 'password') {
        passwordField.style.display = 'block';
      } else {
        passwordField.style.display = 'none';
      }
    });
  }
  
  // Initialize date picker for scheduled posts
  const publishDate = document.getElementById('publish-date');
  
  if (publishDate && typeof flatpickr !== 'undefined') {
    flatpickr(publishDate, {
      enableTime: true,
      dateFormat: "Y-m-d H:i",
      minDate: "today"
    });
  }
}

/**
 * Initialize featured image uploader
 */
function initializeFeaturedImage() {
  const uploadBtn = document.getElementById('upload-image-btn');
  const removeBtn = document.getElementById('remove-image-btn');
  const imagePlaceholder = document.getElementById('image-placeholder');
  const imagePreview = document.getElementById('featured-image-preview');
  
  if (uploadBtn && removeBtn && imagePlaceholder && imagePreview) {
    // Upload button click
    uploadBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Open media modal
      const mediaModal = document.getElementById('media-modal');
      
      if (mediaModal) {
        mediaModal.style.display = 'flex';
        
        // Set a flag to indicate we're selecting for featured image
        mediaModal.setAttribute('data-selection-for', 'featured');
      }
    });
    
    // Remove button click
    removeBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Hide image preview and show placeholder
      imagePreview.style.display = 'none';
      imagePlaceholder.style.display = 'flex';
      
      // Hide remove button
      removeBtn.style.display = 'none';
    });
  }
}

/**
 * Initialize slug generator
 */
function initializeSlugGenerator() {
  const titleInput = document.getElementById('post-title');
  const slugField = document.getElementById('post-slug');
  
  if (titleInput && slugField) {
    titleInput.addEventListener('input', function() {
      // Only generate slug if it's empty or the default value
      if (slugField.textContent === 'post-title' || slugField.textContent === '') {
        slugField.textContent = generateSlug(this.value);
      }
    });
    
    // Make slug editable on click
    const editBtn = document.querySelector('.edit-btn');
    
    if (editBtn) {
      editBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Focus on slug and select all text
        slugField.focus();
        
        // Create selection range
        const range = document.createRange();
        range.selectNodeContents(slugField);
        
        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(range);
      });
    }
    
    // Update slug on blur
    slugField.addEventListener('blur', function() {
      // Ensure slug is valid
      this.textContent = generateSlug(this.textContent);
      
      // If empty, use title
      if (this.textContent === '') {
        this.textContent = generateSlug(titleInput.value) || 'post-title';
      }
    });
    
    // Prevent Enter key in slug field
    slugField.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        this.blur();
      }
    });
  }
}

/**
 * Generate slug from text
 */
function generateSlug(text) {
  return text
    .toLowerCase()
    .replace(/[^\w\s-]/g, '') // Remove special characters
    .replace(/\s+/g, '-')     // Replace spaces with hyphens
    .replace(/-+/g, '-')      // Replace multiple hyphens with a single one
    .trim();                  // Trim leading/trailing whitespace
}

/**
 * Initialize SEO character counters
 */
function initializeCharacterCounters() {
  // Excerpt counter
  const postExcerpt = document.getElementById('post-excerpt');
  const excerptCharacters = document.getElementById('excerpt-characters');
  
  if (postExcerpt && excerptCharacters) {
    postExcerpt.addEventListener('input', function() {
      excerptCharacters.textContent = this.value.length;
      
      // Add warning class if over limit
      if (this.value.length > 160) {
        excerptCharacters.classList.add('over-limit');
      } else {
        excerptCharacters.classList.remove('over-limit');
      }
    });
    
    // Initial count
    excerptCharacters.textContent = postExcerpt.value.length;
  }
  
  // Meta title counter
  const metaTitle = document.getElementById('meta-title');
  const titleCharacters = document.getElementById('title-characters');
  
  if (metaTitle && titleCharacters) {
    metaTitle.addEventListener('input', function() {
      titleCharacters.textContent = this.value.length;
      
      // Add warning class if over limit
      if (this.value.length > 60) {
        titleCharacters.classList.add('over-limit');
      } else {
        titleCharacters.classList.remove('over-limit');
      }
    });
    
    // Initial count
    titleCharacters.textContent = metaTitle.value.length;
  }
  
  // Meta description counter
  const metaDescription = document.getElementById('meta-description');
  const descriptionCharacters = document.getElementById('description-characters');
  
  if (metaDescription && descriptionCharacters) {
    metaDescription.addEventListener('input', function() {
      descriptionCharacters.textContent = this.value.length;
      
      // Add warning class if over limit
      if (this.value.length > 160) {
        descriptionCharacters.classList.add('over-limit');
      } else {
        descriptionCharacters.classList.remove('over-limit');
      }
    });
    
    // Initial count
    descriptionCharacters.textContent = metaDescription.value.length;
  }
}

/**
 * Initialize tag suggestions
 */
function initializeTagSuggestions() {
  const tagsInput = document.getElementById('post-tags');
  const tagSuggestions = document.querySelectorAll('.tag-suggestion');
  
  if (tagsInput && tagSuggestions.length) {
    tagSuggestions.forEach(tag => {
      tag.addEventListener('click', function() {
        const tagText = this.textContent;
        
        // Get current tags
        const currentTags = tagsInput.value.split(',').map(tag => tag.trim()).filter(tag => tag);
        
        // Add tag if not already present
        if (!currentTags.includes(tagText)) {
          currentTags.push(tagText);
          
          // Update input value
          tagsInput.value = currentTags.join(', ');
        }
      });
    });
  }
}

/**
 * Initialize media library
 */
function initializeMediaLibrary() {
  // Media tabs
  const mediaTabs = document.querySelectorAll('.media-tabs .tab-btn');
  const mediaTabContents = document.querySelectorAll('.modal .tab-content');
  
  if (mediaTabs.length && mediaTabContents.length) {
    mediaTabs.forEach(tab => {
      tab.addEventListener('click', function() {
        // Remove active class from all tabs and contents
        mediaTabs.forEach(t => t.classList.remove('active'));
        mediaTabContents.forEach(c => c.classList.remove('active'));
        
        // Add active class to clicked tab and corresponding content
        this.classList.add('active');
        const tabId = this.getAttribute('data-tab');
        document.getElementById(tabId).classList.add('active');
      });
    });
  }
  
  // Media selection
  const mediaItems = document.querySelectorAll('.media-item');
  const selectMediaBtn = document.getElementById('select-media');
  
  if (mediaItems.length && selectMediaBtn) {
    // Select media item on click
    let selectedMediaItem = null;
    
    mediaItems.forEach(item => {
      item.addEventListener('click', function() {
        // Remove selection from all items
        mediaItems.forEach(i => i.classList.remove('selected'));
        
        // Add selection to clicked item
        this.classList.add('selected');
        selectedMediaItem = this;
      });
    });
    
    // Select media button click
    selectMediaBtn.addEventListener('click', function() {
      if (!selectedMediaItem) {
        alert('Please select an image');
        return;
      }
      
      const mediaModal = document.getElementById('media-modal');
      
      if (!mediaModal) return;
      
      // Check what we're selecting for
      const selectionFor = mediaModal.getAttribute('data-selection-for');
      
      if (selectionFor === 'featured') {
        // Set featured image
        const imgSrc = selectedMediaItem.querySelector('img').src;
        const featuredImagePreview = document.getElementById('featured-image-preview');
        const imagePlaceholder = document.getElementById('image-placeholder');
        const removeImageBtn = document.getElementById('remove-image-btn');
        
        if (featuredImagePreview && imagePlaceholder && removeImageBtn) {
          featuredImagePreview.src = imgSrc;
          featuredImagePreview.style.display = 'block';
          imagePlaceholder.style.display = 'none';
          removeImageBtn.style.display = 'inline-block';
        }
      } else if (selectionFor === 'editor') {
        // Insert image into editor
        const imgSrc = selectedMediaItem.querySelector('img').src;
        const quill = Quill.find(document.getElementById('editor-content'));
        
        if (quill) {
          const range = quill.getSelection(true);
          quill.insertEmbed(range.index, 'image', imgSrc);
          quill.setSelection(range.index + 1);
        }
      }
      
      // Close modal
      mediaModal.style.display = 'none';
    });
  }
  
  // File upload functionality
  const uploadDropzone = document.getElementById('upload-dropzone');
  const fileInput = document.getElementById('file-input');
  const selectFilesBtn = document.getElementById('select-files');
  
  if (uploadDropzone && fileInput && selectFilesBtn) {
    // Select files button click
    selectFilesBtn.addEventListener('click', function() {
      fileInput.click();
    });
    
    // File input change
    fileInput.addEventListener('change', function() {
      if (this.files.length > 0) {
        showUploadProgress(this.files);
      }
    });
    
    // Drag and drop functionality
    uploadDropzone.addEventListener('dragover', function(e) {
      e.preventDefault();
      this.classList.add('dragover');
    });
    
    uploadDropzone.addEventListener('dragleave', function() {
      this.classList.remove('dragover');
    });
    
    uploadDropzone.addEventListener('drop', function(e) {
      e.preventDefault();
      this.classList.remove('dragover');
      
      if (e.dataTransfer.files.length > 0) {
        showUploadProgress(e.dataTransfer.files);
      }
    });
  }
}

/**
 * Show upload progress for files
 */
function showUploadProgress(files) {
  const uploadProgress = document.querySelector('.upload-progress');
  const filesList = document.getElementById('upload-files-list');
  
  if (!uploadProgress || !filesList) return;
  
  // Show upload progress area
  uploadProgress.style.display = 'block';
  filesList.innerHTML = '';
  
  // Create file list items
  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    const fileItem = document.createElement('div');
    fileItem.className = 'file-item';
    
    const fileIcon = getFileIcon(file.type);
    
    fileItem.innerHTML = `
      <div class="file-info">
        <div class="file-icon">${fileIcon}</div>
        <div class="file-details">
          <div class="file-name">${file.name}</div>
          <div class="file-size">${formatFileSize(file.size)}</div>
        </div>
      </div>
      <div class="file-progress">
        <div class="progress-bar">
          <div class="progress" style="width: 0%"></div>
        </div>
      </div>
    `;
    
    filesList.appendChild(fileItem);
  }
  
  // Add click event to Upload All button
  const uploadAllBtn = document.getElementById('upload-all');
  
  if (uploadAllBtn) {
    uploadAllBtn.addEventListener('click', function() {
      // In a real app, this would upload the files to the server
      // For demo purposes, simulate upload progress
      simulateFileUpload(files);
    });
  }
}

/**
 * Get appropriate icon for file type
 */
function getFileIcon(fileType) {
  if (fileType.startsWith('image/')) {
    return '<i class="fas fa-file-image"></i>';
  } else if (fileType.startsWith('video/')) {
    return '<i class="fas fa-file-video"></i>';
  } else if (fileType.startsWith('audio/')) {
    return '<i class="fas fa-file-audio"></i>';
  } else if (fileType === 'application/pdf') {
    return '<i class="fas fa-file-pdf"></i>';
  } else if (fileType.includes('word') || fileType === 'application/msword') {
    return '<i class="fas fa-file-word"></i>';
  } else if (fileType.includes('excel') || fileType === 'application/vnd.ms-excel') {
    return '<i class="fas fa-file-excel"></i>';
  } else if (fileType.includes('powerpoint') || fileType === 'application/vnd.ms-powerpoint') {
    return '<i class="fas fa-file-powerpoint"></i>';
  } else {
    return '<i class="fas fa-file"></i>';
  }
}

/**
 * Format file size in human-readable format
 */
function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes';
  
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Simulate file upload progress
 */
function simulateFileUpload(files) {
  const progressBars = document.querySelectorAll('.file-progress .progress');
  
  if (!progressBars.length) return;
  
  // Disable upload button
  const uploadAllBtn = document.getElementById('upload-all');
  if (uploadAllBtn) {
    uploadAllBtn.disabled = true;
    uploadAllBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
  }
  
  // Simulate progress for each file
  progressBars.forEach((progressBar, index) => {
    let width = 0;
    const maxWidth = 100;
    const increment = Math.random() * 2 + 1; // Random increment between 1-3
    const interval = Math.random() * 50 + 50; // Random interval between 50-100ms
    
    const timer = setInterval(() => {
      if (width >= maxWidth) {
        clearInterval(timer);
        
        // Check if all files are done
        const allDone = Array.from(progressBars).every(bar => 
          parseInt(bar.style.width, 10) >= 100);
        
        if (allDone && uploadAllBtn) {
          // Re-enable upload button and change text
          uploadAllBtn.disabled = false;
          uploadAllBtn.innerHTML = 'Upload Complete!';
          
          // After 3 seconds, change text back
          setTimeout(() => {
            uploadAllBtn.innerHTML = 'Upload All';
            
            // Add the files to the media library (in a real app)
            updateMediaLibrary(files);
          }, 3000);
        }
      } else {
        width += increment;
        width = Math.min(width, maxWidth);
        progressBar.style.width = width + '%';
      }
    }, interval);
  });
}

/**
 * Update media library with new files
 * In a real app, this would come from the server response
 */
function updateMediaLibrary(files) {
  // Switch to media library tab
  const libraryTab = document.querySelector('.tab-btn[data-tab="media-library"]');
  
  if (libraryTab) {
    libraryTab.click();
  }
  
  // In a real app, this would refresh the media library items
  alert('In a production app, the new files would now appear in your media library');
}

/**
 * Initialize form submission
 */
function initializeFormSubmission() {
  const saveButtons = document.querySelectorAll('#save-draft, #save-draft-sidebar');
  const publishButtons = document.querySelectorAll('#publish-post, #publish-post-sidebar');
  const previewButton = document.getElementById('preview-post');
  
  // Save as draft
  saveButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      
      if (!validateForm('draft')) return;
      
      // Show loading state
      this.disabled = true;
      this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
      
      // Simulate server delay
      setTimeout(() => {
        // Reset button
        this.disabled = false;
        this.innerHTML = '<i class="fas fa-save"></i> Save Draft';
        
        // Show success message
        showNotification('Draft saved successfully!', 'success');
      }, 1500);
    });
  });
  
  // Publish post
  publishButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      
      if (!validateForm('publish')) return;
      
      // Confirm publication
      if (confirm('Are you sure you want to publish this post? It will be visible to the public immediately.')) {
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publishing...';
        
        // Simulate server delay
        setTimeout(() => {
          // Reset button
          this.disabled = false;
          this.innerHTML = '<i class="fas fa-paper-plane"></i> Publish';
          
          // Show success message
          showNotification('Post published successfully!', 'success');
          
          // Redirect to blog management page
          setTimeout(() => {
            window.location.href = 'admin-blog.html';
          }, 1500);
        }, 2000);
      }
    });
  });
  
  // Preview post
  if (previewButton) {
    previewButton.addEventListener('click', function(e) {
      e.preventDefault();
      
      // In a real app, this would save the post as a draft and open a preview URL
      alert('In a production app, this would open a preview of your post in a new tab');
    });
  }
}

/**
 * Validate form before submission
 */
function validateForm(type) {
  let isValid = true;
  const title = document.getElementById('post-title');
  
  // Title is required
  if (!title || !title.value.trim()) {
    showError(title, 'Post title is required');
    isValid = false;
  }
  
  // For publishing, check for featured image (optional)
  if (type === 'publish') {
    const featuredImage = document.getElementById('featured-image-preview');
    
    if (featuredImage && featuredImage.style.display === 'none') {
      // Not an error, but show a confirmation
      if (!confirm('Your post doesn\'t have a featured image. Do you want to continue?')) {
        return false;
      }
    }
    
    // For publishing, check content length
    const quill = Quill.find(document.getElementById('editor-content'));
    
    if (quill && quill.getText().trim().length < 100) {
      if (!confirm('Your post content is very short. Do you want to continue?')) {
        return false;
      }
    }
  }
  
  return isValid;
}

/**
 * Show error message for a field
 */
function showError(field, message) {
  // Add error class to field
  field.classList.add('error');
  
  // Check if error message already exists
  let errorMessage = field.nextElementSibling;
  
  if (!errorMessage || !errorMessage.classList.contains('error-message')) {
    errorMessage = document.createElement('div');
    errorMessage.className = 'error-message';
    field.parentNode.insertBefore(errorMessage, field.nextSibling);
  }
  
  errorMessage.textContent = message;
  
  // Focus on field
  field.focus();
  
  // Remove error when field is modified
  field.addEventListener('input', function() {
    this.classList.remove('error');
    if (errorMessage) {
      errorMessage.textContent = '';
    }
  }, { once: true });
}

/**
 * Show notification message
 */
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `notification ${type}`;
  notification.innerHTML = `
    <div class="notification-content">
      <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
      <span>${message}</span>
    </div>
    <button class="notification-close"><i class="fas fa-times"></i></button>
  `;
  
  // Add to document
  document.body.appendChild(notification);
  
  // Add close button functionality
  const closeButton = notification.querySelector('.notification-close');
  
  if (closeButton) {
    closeButton.addEventListener('click', function() {
      document.body.removeChild(notification);
    });
  }
  
  // Auto-remove after 5 seconds
  setTimeout(() => {
    if (document.body.contains(notification)) {
      document.body.removeChild(notification);
    }
  }, 5000);
}