/**
 * blog.js
 * Backsure Global Support - Blog Management JavaScript
 * Handles functionality for blog listing and management
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize blog view toggles
  initializeBlogViewToggles();
  
  // Initialize blog filters
  initializeBlogFilters();
  
  // Initialize bulk actions
  initializeBulkActions();
  
  // Initialize post actions (edit, view, delete)
  initializePostActions();
  
  // Initialize search functionality
  initializeSearchFilter();
});

/**
 * Initialize blog view toggles (grid vs list view)
 */
function initializeBlogViewToggles() {
  const viewButtons = document.querySelectorAll('.view-btn');
  const gridView = document.querySelector('.grid-view');
  const listView = document.querySelector('.list-view');
  
  if (!viewButtons.length || !gridView || !listView) return;
  
  viewButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Remove active class from all buttons
      viewButtons.forEach(btn => btn.classList.remove('active'));
      
      // Add active class to clicked button
      this.classList.add('active');
      
      // Get view type from button
      const viewType = this.getAttribute('data-view');
      
      // Toggle view based on selected type
      if (viewType === 'grid') {
        gridView.classList.add('active');
        listView.classList.remove('active');
        
        // Save preference to localStorage
        localStorage.setItem('blog-view-preference', 'grid');
      } else {
        gridView.classList.remove('active');
        listView.classList.add('active');
        
        // Save preference to localStorage
        localStorage.setItem('blog-view-preference', 'list');
      }
    });
  });
  
  // Check for saved preference
  const savedPreference = localStorage.getItem('blog-view-preference');
  
  if (savedPreference) {
    // Find button with matching data-view
    const matchingButton = document.querySelector(`.view-btn[data-view="${savedPreference}"]`);
    
    if (matchingButton) {
      // Trigger click event to set the view
      matchingButton.click();
    }
  }
}

/**
 * Initialize blog filters (category, status, date)
 */
function initializeBlogFilters() {
  const filterSelects = document.querySelectorAll('#category-filter, #status-filter, #date-filter');
  
  if (!filterSelects.length) return;
  
  filterSelects.forEach(select => {
    select.addEventListener('change', function() {
      // Apply filters to posts
      applyFilters();
    });
  });
}

/**
 * Apply filters to posts
 * In a real app, this might involve an API call or client-side filtering
 */
function applyFilters() {
  const categoryFilter = document.getElementById('category-filter');
  const statusFilter = document.getElementById('status-filter');
  const dateFilter = document.getElementById('date-filter');
  const searchInput = document.getElementById('post-search');
  
  // Get filter values
  const categoryValue = categoryFilter ? categoryFilter.value : 'all';
  const statusValue = statusFilter ? statusFilter.value : 'all';
  const dateValue = dateFilter ? dateFilter.value : 'all';
  const searchValue = searchInput ? searchInput.value.trim().toLowerCase() : '';
  
  // Get all post items in both grid and list view
  const gridPosts = document.querySelectorAll('.post-card');
  const listRows = document.querySelectorAll('.posts-table tbody tr');
  
  // Show all posts initially
  gridPosts.forEach(post => post.style.display = 'block');
  listRows.forEach(row => row.style.display = 'table-row');
  
  // Filter by category
  if (categoryValue !== 'all') {
    gridPosts.forEach(post => {
      const postCategory = post.querySelector('.post-category').textContent.toLowerCase();
      if (!postCategory.includes(categoryValue)) {
        post.style.display = 'none';
      }
    });
    
    listRows.forEach(row => {
      const postCategory = row.cells[3].textContent.toLowerCase();
      if (!postCategory.includes(categoryValue)) {
        row.style.display = 'none';
      }
    });
  }
  
  // Filter by status
  if (statusValue !== 'all') {
    gridPosts.forEach(post => {
      const postStatus = post.querySelector('.post-status').classList.contains(statusValue);
      if (!postStatus) {
        post.style.display = 'none';
      }
    });
    
    listRows.forEach(row => {
      const postStatus = row.querySelector('.status-badge').classList.contains(statusValue);
      if (!postStatus) {
        row.style.display = 'none';
      }
    });
  }
  
  // Filter by date (in a real app, this would be more sophisticated)
  if (dateValue !== 'all') {
    // For demo purposes, just show a message
    console.log('Date filtering would be implemented in a production app');
  }
  
  // Filter by search term
  if (searchValue !== '') {
    gridPosts.forEach(post => {
      const postTitle = post.querySelector('.post-title').textContent.toLowerCase();
      const postExcerpt = post.querySelector('.post-excerpt').textContent.toLowerCase();
      
      if (!postTitle.includes(searchValue) && !postExcerpt.includes(searchValue)) {
        post.style.display = 'none';
      }
    });
    
    listRows.forEach(row => {
      const postTitle = row.querySelector('.post-title-link').textContent.toLowerCase();
      const postExcerpt = row.querySelector('.post-excerpt').textContent.toLowerCase();
      
      if (!postTitle.includes(searchValue) && !postExcerpt.includes(searchValue)) {
        row.style.display = 'none';
      }
    });
  }
  
  // Update UI to show filter results
  updateFilterResults();
}

/**
 * Update UI to show filter results
 */
function updateFilterResults() {
  const gridPosts = document.querySelectorAll('.post-card[style="display: block"]');
  const listRows = document.querySelectorAll('.posts-table tbody tr[style="display: table-row"]');
  
  // Count visible posts
  const visibleCount = gridPosts.length || listRows.length;
  const totalCount = document.querySelectorAll('.post-card').length;
  
  // Update pagination info
  const paginationInfo = document.querySelector('.pagination-info');
  
  if (paginationInfo) {
    paginationInfo.textContent = `Showing ${visibleCount} of ${totalCount} posts`;
  }
  
  // Show "no results" message if no posts are visible
  if (visibleCount === 0) {
    // Check if message already exists
    let noResultsMsg = document.querySelector('.no-results-message');
    
    if (!noResultsMsg) {
      // Create message
      noResultsMsg = document.createElement('div');
      noResultsMsg.className = 'no-results-message';
      noResultsMsg.innerHTML = `
        <div class="empty-state">
          <i class="fas fa-search"></i>
          <h3>No posts found</h3>
          <p>Try adjusting your filters or search term</p>
          <button id="reset-filters" class="btn-secondary">Reset Filters</button>
        </div>
      `;
      
      // Insert before pagination
      const pagination = document.querySelector('.pagination');
      
      if (pagination && pagination.parentNode) {
        pagination.parentNode.insertBefore(noResultsMsg, pagination);
      }
      
      // Add event listener to reset button
      const resetBtn = noResultsMsg.querySelector('#reset-filters');
      
      if (resetBtn) {
        resetBtn.addEventListener('click', resetFilters);
      }
    }
  } else {
    // Remove "no results" message if it exists
    const noResultsMsg = document.querySelector('.no-results-message');
    
    if (noResultsMsg) {
      noResultsMsg.parentNode.removeChild(noResultsMsg);
    }
  }
}

/**
 * Reset all filters to default values
 */
function resetFilters() {
  const filterSelects = document.querySelectorAll('#category-filter, #status-filter, #date-filter');
  const searchInput = document.getElementById('post-search');
  
  // Reset select elements to first option
  filterSelects.forEach(select => {
    select.selectedIndex = 0;
  });
  
  // Clear search input
  if (searchInput) {
    searchInput.value = '';
  }
  
  // Apply filters (will show all posts with default values)
  applyFilters();
}

/**
 * Initialize bulk actions
 */
function initializeBulkActions() {
  const bulkActionSelect = document.getElementById('bulk-action');
  const applyBtn = document.querySelector('.apply-btn');
  const selectAllCheckbox = document.getElementById('select-all');
  const postCheckboxes = document.querySelectorAll('.post-select');
  
  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
      postCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
    });
  }
  
  if (bulkActionSelect && applyBtn) {
    applyBtn.addEventListener('click', function() {
      const selectedAction = bulkActionSelect.value;
      
      if (!selectedAction) {
        alert('Please select an action');
        return;
      }
      
      // Get selected posts
      const selectedPosts = Array.from(postCheckboxes)
        .filter(checkbox => checkbox.checked)
        .map(checkbox => {
          // Get post ID or other identifier
          // In a real app, this would come from a data attribute
          const row = checkbox.closest('tr');
          const postTitle = row ? row.querySelector('.post-title-link').textContent : 'Unknown';
          
          return postTitle;
        });
      
      if (selectedPosts.length === 0) {
        alert('Please select at least one post');
        return;
      }
      
      // Confirm action
      const confirmMessage = `Are you sure you want to ${getActionVerb(selectedAction)} ${selectedPosts.length} post(s)?`;
      
      if (confirm(confirmMessage)) {
        // In a real app, this would send a request to the server
        // For demo purposes, just show a message
        alert(`Action "${selectedAction}" would be applied to ${selectedPosts.length} posts in a production environment`);
      }
    });
  }
}

/**
 * Get appropriate verb for action
 */
function getActionVerb(action) {
  switch (action) {
    case 'publish':
      return 'publish';
    case 'draft':
      return 'move to draft';
    case 'delete':
      return 'delete';
    default:
      return 'apply action to';
  }
}

/**
 * Initialize post actions (edit, view, delete)
 */
function initializePostActions() {
  // Edit buttons
  const editButtons = document.querySelectorAll('.edit-btn');
  
  editButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // In a real app, get post ID from data attribute
      const postId = this.getAttribute('data-id') || '1';
      
      // Redirect to edit page
      window.location.href = `admin-blog-edit.html?id=${postId}`;
    });
  });
  
  // View buttons
  const viewButtons = document.querySelectorAll('.view-btn:not(.table-actions .view-btn)');
  
  viewButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // In a real app, get post slug from data attribute
      const postSlug = this.getAttribute('data-slug') || 'sample-post';
      
      // Open in new tab
      window.open(`../blog/${postSlug}.html`, '_blank');
    });
  });
  
  // Delete buttons
  const deleteButtons = document.querySelectorAll('.delete-btn');
  
  deleteButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // In a real app, get post title from closest element
      let postTitle = 'this post';
      
      // Try to get title from grid view
      const card = this.closest('.post-card');
      if (card) {
        postTitle = card.querySelector('.post-title').textContent;
      }
      
      // Try to get title from list view
      const row = this.closest('tr');
      if (row) {
        postTitle = row.querySelector('.post-title-link').textContent;
      }
      
      // Confirm deletion
      if (confirm(`Are you sure you want to delete "${postTitle}"?`)) {
        // In a real app, this would send a delete request to the server
        // For demo purposes, just show a message
        alert(`The post would be deleted in a production environment`);
      }
    });
  });
}

/**
 * Initialize search filter
 */
function initializeSearchFilter() {
  const searchInput = document.getElementById('post-search');
  const searchBtn = document.querySelector('.search-btn');
  
  if (searchInput && searchBtn) {
    // Search on button click
    searchBtn.addEventListener('click', function() {
      applyFilters();
    });
    
    // Search on enter key
    searchInput.addEventListener('keyup', function(e) {
      if (e.key === 'Enter') {
        applyFilters();
      }
    });
    
    // Search after typing delay (for better performance)
    let typingTimer;
    const doneTypingInterval = 500; // ms
    
    searchInput.addEventListener('keyup', function() {
      clearTimeout(typingTimer);
      
      if (this.value) {
        typingTimer = setTimeout(applyFilters, doneTypingInterval);
      }
    });
  }
}

/**
 * Initialize pagination
 * In a real app, this would handle server-side pagination
 */
function initializePagination() {
  const paginationLinks = document.querySelectorAll('.pagination-link');
  
  if (!paginationLinks.length) return;
  
  paginationLinks.forEach(link => {
    if (link.classList.contains('disabled')) return;
    
    link.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Remove active class from all links
      paginationLinks.forEach(l => l.classList.remove('active'));
      
      // Add active class to clicked link
      this.classList.add('active');
      
      // In a real app, this would fetch the next page of results
      // For demo purposes, just show a message
      const page = this.textContent;
      
      alert(`In a production app, this would navigate to page ${page}`);
    });
  });
}