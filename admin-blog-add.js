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
  // Check if Quill is loaded and if editor container exists
  if (typeof Quill === 'undefined') {
    console.error('Quill editor not loaded');
    return;
  }
  
  const editorContainer = document.getElementById('editor-content');
  if (!editorContainer) {
    console.error('Editor container not found');
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
    const mediaModal = document.getElementById('media-modal');
    if (mediaModal) {
      mediaModal.style.display = 'block';
      // Activate media library tab
      activateTab('media-library');
    }
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
  
  const dateField = document.getElementById('publish-date');
  if (!dateField) {
    console.error('Date field not found');
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
  
  if (!titleInput || !slugField) {
    console.error('Title or slug field not found');
    return;
  }
  
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
  if (editSlugBtn) {
    editSlugBtn.addEventListener('click', function() {
      slugField.focus();
    });
  }
  
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
  
  if (!excerptField || !counter) {
    console.error('Excerpt field or counter not found');
    return;
  }
  
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
  
  if (!metaTitle || !titleCounter || !metaDescription || !descriptionCounter) {
    console.error('Meta fields or counters not found');
    return;
  }
  
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
  
  if (!statusSelect || !scheduledOptions || !visibilitySelect || !passwordField) {
    console.error('Publish options elements not found');
    return;
  }
  
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
  
  if (!tagInput) {
    console.error('Tag input not found');
    return;
  }
  
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
  
  if (!addCategoryBtn || !categoryModal || !cancelCategoryBtn || !addCategorySubmitBtn) {
    console.error('Category modal elements not found');
    return;
  }
  
  // Show category modal
  addCategoryBtn.addEventListener('click', function() {
    categoryModal.style.display = 'block';
  });
  
  // Hide category modal
  cancelCategoryBtn.addEventListener('click', function() {
    categoryModal.style.display = 'none';
  });
  
  // Handle closing modal with X button
  const closeButtons = categoryModal.querySelectorAll('.close-modal');
  closeButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      categoryModal.style.display = 'none';
    });
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
    if (categoryList) {
      const newId = 'cat-' + (categorySlug || createSlug(categoryName));
      const newValue = categorySlug || createSlug(categoryName);
      
      const newCategory = document.createElement('div');
      newCategory.className = 'checkbox-group';
      newCategory.innerHTML = `
        <input id="${newId}" name="category[]" type="checkbox" value="${newValue}"
