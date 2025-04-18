/**
 * Backsure Global Support
 * Blog Management JavaScript
 * Version: 1.0
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize DataTables
  initBlogTable();
  
  // Initialize event listeners
  initBulkActions();
  initModals();
  initFilters();
  initRowActions();
});

/**
 * Initialize the blog posts DataTable
 */
function initBlogTable() {
  const blogTable = $('#blog-posts-table').DataTable({
    responsive: true,
    language: {
      search: "",
      searchPlaceholder: "Search posts...",
      lengthMenu: "Show _MENU_ posts per page",
      info: "Showing _START_ to _END_ of _TOTAL_ posts",
      infoEmpty: "No posts available",
      infoFiltered: "(filtered from _MAX_ total posts)",
      paginate: {
        first: '<i class="fas fa-angle-double-left"></i>',
        previous: '<i class="fas fa-angle-left"></i>',
        next: '<i class="fas fa-angle-right"></i>',
        last: '<i class="fas fa-angle-double-right"></i>'
      }
    },
    columnDefs: [
      {
        targets: 0, // Checkbox column
        orderable: false,
        className: 'select-checkbox',
        width: '30px'
      },
      {
        targets: 7, // Actions column
        orderable: false,
        width: '120px'
      }
    ],
    order: [[6, 'desc']], // Sort by published date by default
    pageLength: 10,
    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "All"]
    ],
    dom: 'Bfrtip',
    buttons: [
      'copy', 'csv', 'excel', 'pdf', 'print'
    ]
  });
  
  // Add the DataTable search box functionality to our custom search box
  $('#search-posts').on('keyup', function() {
    blogTable.search(this.value).draw();
  });
  
  // Make select-all checkbox work with DataTables
  $('#select-all').on('click', function() {
    $('.row-checkbox').prop('checked', this.checked);
    updateBulkActionCounter();
  });
  
  // Update select-all when individual checkboxes change
  $('#blog-posts-table tbody').on('change', '.row-checkbox', function() {
    if (!this.checked) {
      $('#select-all').prop('checked', false);
    } else {
      const allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
      $('#select-all').prop('checked', allChecked);
    }
    updateBulkActionCounter();
  });
  
  // Update select-all state when table is redrawn
  blogTable.on('draw', function() {
    const allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
    $('#select-all').prop('checked', allChecked && $('.row-checkbox').length > 0);
    updateBulkActionCounter();
  });
}

/**
 * Initialize bulk actions functionality
 */
function initBulkActions() {
  // Show/hide bulk actions panel
  $('#bulk-actions-btn').on('click', function() {
    $('#bulk-actions-panel').slideToggle(200);
  });
  
  // Close bulk actions panel
  $('.bulk-close').on('click', function() {
    $('#bulk-actions-panel').slideUp(200);
  });
  
  // Bulk action buttons
  $('.bulk-btn').on('click', function() {
    const action = $(this).data('action');
    const selectedIds = getSelectedPostIds();
    
    if (selectedIds.length === 0) {
      showToast('Please select at least one post', 'warning');
      return;
    }
    
    switch (action) {
      case 'publish':
        showConfirmModal('Are you sure you want to publish the selected posts?', function() {
          performBulkAction('publish', selectedIds);
        });
        break;
      case 'draft':
        showConfirmModal('Are you sure you want to move the selected posts to draft?', function() {
          performBulkAction('draft', selectedIds);
        });
        break;
      case 'delete':
        showConfirmModal('Are you sure you want to delete the selected posts? This action cannot be undone.', function() {
          performBulkAction('delete', selectedIds);
        });
        break;
      case 'categories':
        $('#category-modal').show();
        break;
    }
  });
  
  // Update categories button in modal
  $('#update-categories-btn').on('click', function() {
    const selectedIds = getSelectedPostIds();
    const selectedCategories = [];
    
    $('input[name="bulk-categories"]:checked').each(function() {
      selectedCategories.push($(this).val());
    });
    
    if (selectedCategories.length === 0) {
      showToast('Please select at least one category', 'warning');
      return;
    }
    
    const action = $('input[name="category-action"]:checked').val();
    
    performCategoryUpdate(selectedIds, selectedCategories, action);
    $('#category-modal').hide();
  });
}

/**
 * Initialize modal dialogs
 */
function initModals() {
  // Close modal when clicking the X or Cancel button
  $('.modal-close, .modal-cancel').on('click', function() {
    $(this).closest('.modal').hide();
  });
  
  // Close modal when clicking outside the modal content
  $('.modal').on('click', function(e) {
    if (e.target === this) {
      $(this).hide();
    }
  });
}

/**
 * Initialize filter functionality
 */
function initFilters() {
  // Apply filters button
  $('#apply-filters').on('click', function() {
    applyFilters();
  });
  
  // Reset filters button
  $('#reset-filters').on('click', function() {
    resetFilters();
  });
  
  // Set today's date as default for date-to filter
  const today = new Date();
  const formattedDate = today.toISOString().split('T')[0];
  $('#date-to').val(formattedDate);
  
  // Set date 30 days ago as default for date-from filter
  const thirtyDaysAgo = new Date();
  thirtyDaysAgo.setDate(today.getDate() - 30);
  const formattedThirtyDaysAgo = thirtyDaysAgo.toISOString().split('T')[0];
  $('#date-from').val(formattedThirtyDaysAgo);
}

/**
 * Initialize row action buttons (edit, view, duplicate, delete)
 */
function initRowActions() {
  // View button
  $('#blog-posts-table').on('click', '.action-btn.view', function(e) {
    e.preventDefault();
    const url = $(this).data('url');
    window.open(url, '_blank');
  });
  
  // Duplicate button
  $('#blog-posts-table').on('click', '.action-btn.duplicate', function() {
    const postId = $(this).data('id');
    duplicatePost(postId);
  });
  
  // Delete button
  $('#blog-posts-table').on('click', '.action-btn.delete', function() {
    const postId = $(this).data('id');
    const postTitle = $(this).closest('tr').find('.post-title a').text();
    
    showConfirmModal(`Are you sure you want to delete "${postTitle}"? This action cannot be undone.`, function() {
      deletePost(postId);
    });
  });
}

/**
 * Get array of selected post IDs
 */
function getSelectedPostIds() {
  const selectedIds = [];
  $('.row-checkbox:checked').each(function() {
    const postId = $(this).closest('tr').data('id');
    selectedIds.push(postId);
  });
  return selectedIds;
}

/**
 * Update the bulk action counter with the number of selected posts
 */
function updateBulkActionCounter() {
  const count = $('.row-checkbox:checked').length;
  $('.selected-count').text(`${count} item${count !== 1 ? 's' : ''} selected`);
}

/**
 * Apply filters to the blog posts table
 */
function applyFilters() {
  const statusFilter = $('#filter-status').val();
  const categoryFilter = $('#filter-category').val();
  const authorFilter = $('#filter-author').val();
  const dateFrom = $('#date-from').val();
  const dateTo = $('#date-to').val();
  
  // Apply filters to DataTable
  const table = $('#blog-posts-table').DataTable();
  
  // Clear existing filters
  table.search('').columns().search('').draw();
  
  // Apply each filter if set
  if (statusFilter) {
    table.column(4).search(statusFilter, true, false).draw();
  }
  
  if (categoryFilter) {
    table.column(2).search(categoryFilter).draw();
  }
  
  if (authorFilter) {
    table.column(3).search(authorFilter).draw();
  }
  
  // Date range filtering is more complex and might require custom filtering logic
  // This is a simplified example that doesn't actually filter by date
  if (dateFrom && dateTo) {
    // In a real implementation, you would create a custom filtering function
    console.log(`Filtering dates from ${dateFrom} to ${dateTo}`);
  }
  
  showToast('Filters applied', 'success');
}

/**
 * Reset all filters to default values
 */
function resetFilters() {
  // Reset dropdown filters
  $('#filter-status, #filter-category, #filter-author').val('');
  
  // Reset date filters to defaults
  const today = new Date();
  const formattedDate = today.toISOString().split('T')[0];
  $('#date-to').val(formattedDate);
  
  const thirtyDaysAgo = new Date();
  thirtyDaysAgo.setDate(today.getDate() - 30);
  const formattedThirtyDaysAgo = thirtyDaysAgo.toISOString().split('T')[0];
  $('#date-from').val(formattedThirtyDaysAgo);
  
  // Clear DataTable filters
  const table = $('#blog-posts-table').DataTable();
  table.search('').columns().search('').draw();
  
  // Reset search box
  $('#search-posts').val('');
  
  showToast('Filters reset', 'info');
}

/**
 * Perform bulk action on selected posts
 */
function performBulkAction(action, postIds) {
  // In a real implementation, this would make an API call
  console.log(`Performing bulk action: ${action} on posts:`, postIds);
  
  // Simulate API call with timeout
  showToast('Processing...', 'info');
  
  setTimeout(() => {
    // Update UI based on action
    switch (action) {
      case 'publish':
        // Update status badges
        postIds.forEach(id => {
          const statusCell = $(`tr[data-id="${id}"] td:nth-child(5)`);
          statusCell.html('<span class="status-badge published">Published</span>');
        });
        showToast('Selected posts have been published', 'success');
        break;
      case 'draft':
        postIds.forEach(id => {
          const statusCell = $(`tr[data-id="${id}"] td:nth-child(5)`);
          statusCell.html('<span class="status-badge draft">Draft</span>');
        });
        showToast('Selected posts have been moved to draft', 'success');
        break;
      case 'delete':
        postIds.forEach(id => {
          $(`tr[data-id="${id}"]`).fadeOut(300, function() {
            $(this).remove();
            
            // Update DataTable
            const table = $('#blog-posts-table').DataTable();
            table.row($(this)).remove().draw(false);
          });
        });
        showToast('Selected posts have been deleted', 'success');
        break;
    }
    
    // Reset checkboxes after bulk action
    $('#select-all').prop('checked', false);
    $('.row-checkbox').prop('checked', false);
    updateBulkActionCounter();
    
    // Hide bulk actions panel
    $('#bulk-actions-panel').slideUp(200);
  }, 1000);
}

/**
 * Perform category update on selected posts
 */
function performCategoryUpdate(postIds, categories, action) {
  // In a real implementation, this would make an API call
  console.log(`Updating categories for posts:`, postIds);
  console.log(`Categories:`, categories);
  console.log(`Action:`, action);
  
  // Simulate API call with timeout
  showToast('Updating categories...', 'info');
  
  setTimeout(() => {
    // Update UI
    postIds.forEach(id => {
      const categoryCell = $(`tr[data-id="${id}"] td:nth-child(3)`);
      
      // If action is "replace", just set the new categories
      // If action is "add", we would need to merge with existing categories
      // For this demo, we'll just set the first selected category
      if (categories.length > 0) {
        const categoryName = categories[0].replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        categoryCell.text(categoryName);
      }
    });
    
    // Reset checkboxes
    $('input[name="bulk-categories"]').prop('checked', false);
    
    showToast('Categories updated successfully', 'success');
  }, 1000);
}

/**
 * Delete a single post
 */
function deletePost(postId) {
  // In a real implementation, this would make an API call
  console.log(`Deleting post ID:`, postId);
  
  // Simulate API call with timeout
  showToast('Deleting post...', 'info');
  
  setTimeout(() => {
    // Remove the row with animation
    $(`tr[data-id="${postId}"]`).fadeOut(300, function() {
      // Update DataTable
      const table = $('#blog-posts-table').DataTable();
      table.row($(this)).remove().draw(false);
      
      showToast('Post deleted successfully', 'success');
    });
  }, 1000);
}

/**
 * Duplicate a post
 */
function duplicatePost(postId) {
  // In a real implementation, this would make an API call
  console.log(`Duplicating post ID:`, postId);
  
  // Simulate API call with timeout
  showToast('Duplicating post...', 'info');
  
  setTimeout(() => {
    // Get original post data
    const originalRow = $(`tr[data-id="${postId}"]`);
    const title = originalRow.find('.post-title a').text();
    const category = originalRow.find('td:nth-child(3)').text();
    const author = originalRow.find('td:nth-child(4)').text();
    
    // Create a new post ID for the duplicate (would be assigned by the server in real implementation)
    const newPostId = Math.max(...Array.from($('#blog-posts-table tbody tr')).map(row => parseInt($(row).data('id')))) + 1;
    
    // Add a new row to the table
    const table = $('#blog-posts-table').DataTable();
    
    const newRowData = [
      `<div class="checkbox-wrapper">
        <input type="checkbox" id="select-${newPostId}" class="row-checkbox">
        <label for="select-${newPostId}"></label>
      </div>`,
      `<div class="post-title">
        <a href="admin-blog-add.html?id=${newPostId}">${title} (Copy)</a>
        <span class="preview-text">Duplicated from original post...</span>
      </div>`,
      category,
      author,
      '<span class="status-badge draft">Draft</span>',
      '0',
      '-',
      `<div class="action-buttons">
        <a href="admin-blog-add.html?id=${newPostId}" class="action-btn edit" title="Edit">
          <i class="fas fa-edit"></i>
        </a>
        <a href="#" class="action-btn view" title="Preview" data-url="blog/preview?id=${newPostId}">
          <i class="fas fa-eye"></i>
        </a>
        <button class="action-btn duplicate" title="Duplicate" data-id="${newPostId}">
          <i class="fas fa-copy"></i>
        </button>
        <button class="action-btn delete" title="Delete" data-id="${newPostId}">
          <i class="fas fa-trash"></i>
        </button>
      </div>`
    ];
    
    const newRow = table.row.add(newRowData).draw().node();
    $(newRow).attr('data-id', newPostId);
    $(newRow).hide().fadeIn(500);
    
    showToast('Post duplicated successfully', 'success');
  }, 1000);
}

/**
 * Show confirmation modal with custom message and callback
 */
function showConfirmModal(message, callback) {
  $('#confirm-message').text(message);
  
  // Remove any existing click handlers
  $('#confirm-action-btn').off('click');
  
  // Add new click handler
  $('#confirm-action-btn').on('click', function() {
    callback();
    $('#confirm-modal').hide();
  });
  
  $('#confirm-modal').show();
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
  // Remove any existing toasts
  $('.toast-notification').remove();
  
  // Create toast element
  const toast = document.createElement('div');
  toast.className = `toast-notification toast-${type}`;
  
  // Set icon based on type
  let icon;
  switch (type) {
    case 'success':
      icon = 'fa-check-circle';
      break;
    case 'warning':
      icon = 'fa-exclamation-triangle';
      break;
    case 'error':
      icon = 'fa-times-circle';
      break;
    case 'info':
    default:
      icon = 'fa-info-circle';
      break;
  }
  
  toast.innerHTML = `
    <div class="toast-icon">
      <i class="fas ${icon}"></i>
    </div>
    <div class="toast-content">${message}</div>
    <button class="toast-close">
      <i class="fas fa-times"></i>
    </button>
  `;
  
  // Add to document
  document.body.appendChild(toast);
  
  // Animate in
  setTimeout(() => {
    toast.classList.add('show');
  }, 10);
  
  // Add close button functionality
  toast.querySelector('.toast-close').addEventListener('click', () => {
    toast.classList.remove('show');
    setTimeout(() => {
      toast.remove();
    }, 300);
  });
  
  // Auto-dismiss after 5 seconds for success and info messages
  if (type === 'success' || type === 'info') {
    setTimeout(() => {
      if (document.body.contains(toast)) {
        toast.classList.remove('show');
        setTimeout(() => {
          if (document.body.contains(toast)) {
            toast.remove();
          }
        }, 300);
      }
    }, 5000);
  }
}

/**
 * Additional styles for toast notifications
 * Added through JavaScript to avoid adding a separate CSS file
 */
(function addToastStyles() {
  const style = document.createElement('style');
  style.textContent = `
    .toast-notification {
      position: fixed;
      top: 20px;
      right: 20px;
      display: flex;
      align-items: center;
      background-color: white;
      border-radius: 4px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
      padding: 12px 15px;
      width: 300px;
      max-width: calc(100vw - 40px);
      z-index: 9999;
      transform: translateX(400px);
      opacity: 0;
      transition: all 0.3s ease;
    }
    
    .toast-notification.show {
      transform: translateX(0);
      opacity: 1;
    }
    
    .toast-icon {
      margin-right: 12px;
      font-size: 1.2rem;
    }
    
    .toast-content {
      flex: 1;
      font-size: 0.95rem;
    }
    
    .toast-close {
      background: none;
      border: none;
      color: #999;
      cursor: pointer;
      font-size: 0.8rem;
      padding: 4px;
    }
    
    .toast-close:hover {
      color: #666;
    }
    
    .toast-success .toast-icon {
      color: #1cc88a;
    }
    
    .toast-warning .toast-icon {
      color: #f6c23e;
    }
    
    .toast-error .toast-icon {
      color: #e74a3b;
    }
    
    .toast-info .toast-icon {
      color: #36b9cc;
    }
    
    @media (max-width: 576px) {
      .toast-notification {
        top: auto;
        bottom: 20px;
        left: 20px;
        right: 20px;
        width: auto;
      }
    }
  `;
  document.head.appendChild(style);
})();

/**
 * Add additional custom styles for the blog management page
 */
(function addCustomStyles() {
  const style = document.createElement('style');
  style.textContent = `
    /* Page header styles */
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
      background-color: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .page-header-actions {
      display: flex;
      gap: 10px;
    }
    
    /* Button styles */
    .btn-primary, .btn-secondary, .btn-outline, .btn-text {
      padding: 8px 16px;
      border-radius: 4px;
      font-weight: 600;
      font-size: 0.9rem;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s;
      border: none;
    }
    
    .btn-primary {
      background-color: #062767;
      color: white;
    }
    
    .btn-primary:hover {
      background-color: #041c4a;
    }
    
    .btn-secondary {
      background-color: #f8f9fc;
      color: #5a5c69;
      border: 1px solid #d1d3e2;
    }
    
    .btn-secondary:hover {
      background-color: #eaecf4;
    }
    
    .btn-outline {
      background-color: transparent;
      color: #062767;
      border: 1px solid #062767;
    }
    
    .btn-outline:hover {
      background-color: #f8f9fc;
    }
    
    .btn-text {
      background-color: transparent;
      color: #5a5c69;
      padding: 8px;
    }
    
    .btn-text:hover {
      color: #062767;
    }
    
    /* Filter container styles */
    .filter-container {
      background-color: white;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 25px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }
    
    .filter-section {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      flex: 3;
    }
    
    .search-section {
      flex: 1;
      min-width: 240px;
    }
    
    .filter-group {
      min-width: 180px;
    }
    
    .filter-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      color: #5a5c69;
      font-size: 0.9rem;
    }
    
    .filter-select, .date-input {
      width: 100%;
      padding: 8px 12px;
      border: 1px solid #d1d3e2;
      border-radius: 4px;
      font-size: 0.9rem;
      color: #5a5c69;
    }
    
    .date-range-container {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .date-separator {
      color: #5a5c69;
    }
    
    .search-box {
      position: relative;
    }
    
    .search-box input {
      width: 100%;
      padding: 8px 30px 8px 12px;
      border: 1px solid #d1d3e2;
      border-radius: 4px;
      font-size: 0.9rem;
    }
    
    .search-box button {
      position: absolute;
      right: 5px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #5a5c69;
      cursor: pointer;
    }
    
    .filter-actions {
      display: flex;
      align-items: center;
      gap: 10px;
      width: 100%;
      justify-content: flex-end;
      margin-top: 10px;
    }
    
    /* Bulk actions panel */
    .bulk-actions-panel {
      background-color: #f8f9fc;
      border: 1px solid #d1d3e2;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 25px;
      position: relative;
    }
    
    .bulk-actions-container {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 15px;
    }
    
    .bulk-action-title {
      font-weight: 600;
      color: #5a5c69;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .selected-count {
      font-weight: normal;
      color: #5a5c69;
      font-size: 0.9rem;
    }
    
    .bulk-action-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
    
    .bulk-btn {
      padding: 6px 12px;
      background-color: white;
      border: 1px solid #d1d3e2;
      border-radius: 4px;
      color: #5a5c69;
      font-size: 0.9rem;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s;
    }
    
    .bulk-btn:hover {
      background-color: #eaecf4;
    }
    
    .bulk-close {
      position: absolute;
      top: 15px;
      right: 15px;
      background: none;
      border: none;
      color: #5a5c69;
      cursor: pointer;
    }
    
    /* Table styles */
    .table-responsive {
      background-color: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.1);
      overflow-x: auto;
    }
    
    .data-table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .data-table th, .data-table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #eaecf4;
    }
    
    .data-table thead th {
      background-color: #f8f9fc;
      color: #5a5c69;
      font-weight: 600;
      border-bottom: 2px solid #e3e6f0;
    }
    
    .data-table tbody tr:hover {
      background-color: #f8f9fc;
    }
    
    .checkbox-wrapper {
      position: relative;
      display: inline-block;
    }
    
    .checkbox-wrapper input[type="checkbox"] {
      position: absolute;
      opacity: 0;
      cursor: pointer;
      height: 0;
      width: 0;
    }
    
    .checkbox-wrapper label {
      position: relative;
      cursor: pointer;
      padding-left: 25px;
      user-select: none;
    }
    
    .checkbox-wrapper label:before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 18px;
      height: 18px;
      border: 1px solid #d1d3e2;
      border-radius: 3px;
      background-color: white;
    }
    
    .checkbox-wrapper input[type="checkbox"]:checked + label:before {
      background-color: #062767;
      border-color: #062767;
    }
    
    .checkbox-wrapper input[type="checkbox"]:checked + label:after {
      content: '\\f00c';
      font-family: 'Font Awesome 5 Free';
      font-weight: 900;
      position: absolute;
      left: 3px;
      top: 50%;
      transform: translateY(-50%);
      color: white;
      font-size: 12px;
    }
    
    .post-title {
      display: flex;
      flex-direction: column;
    }
    
    .post-title a {
      color: #062767;
      font-weight: 600;
      text-decoration: none;
      margin-bottom: 4px;
    }
    
    .post-title a:hover {
      text-decoration: underline;
    }
    
    .preview-text {
      font-size: 0.85rem;
      color: #6e707e;
    }
    
    .status-badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    
    .status-badge.published {
      background-color: rgba(28, 200, 138, 0.15);
      color: #1cc88a;
    }
    
    .status-badge.draft {
      background-color: rgba(133, 135, 150, 0.15);
      color: #858796;
    }
    
    .status-badge.pending {
      background-color: rgba(246, 194, 62, 0.15);
      color: #f6c23e;
    }
    
    .status-badge.scheduled {
      background-color: rgba(54, 185, 204, 0.15);
      color: #36b9cc;
    }
    
    .action-buttons {
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .action-btn {
      width: 28px;
      height: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 4px;
      background: none;
      border: none;
      cursor: pointer;
      color: #5a5c69;
      transition: all 0.2s;
    }
    
    .action-btn:hover {
      background-color: #f8f9fc;
    }
    
    .action-btn.edit:hover {
      color: #4e73df;
    }
    
    .action-btn.view:hover {
      color: #36b9cc;
    }
    
    .action-btn.duplicate:hover {
      color: #f6c23e;
    }
    
    .action-btn.delete:hover {
      color: #e74a3b;
    }
    
    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }
    
    .modal.show {
      display: flex;
    }
    
    .modal-content {
      background-color: white;
      border-radius: 8px;
      width: 90%;
      max-width: 500px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      animation: modalFadeIn 0.3s;
    }
    
    @keyframes modalFadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .modal-header {
      padding: 15px 20px;
      border-bottom: 1px solid #eaecf4;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .modal-header h3 {
      margin: 0;
      color: #5a5c69;
      font-size: 1.2rem;
    }
    
    .modal-close {
      background: none;
      border: none;
      color: #5a5c69;
      cursor: pointer;
      font-size: 1.1rem;
    }
    
    .modal-body {
      padding: 20px;
    }
    
    .modal-footer {
      padding: 15px 20px;
      border-top: 1px solid #eaecf4;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }
    
    .category-options {
      margin: 15px 0;
    }
    
    .checkbox-group, .radio-label {
      margin-bottom: 10px;
    }
    
    .checkbox-label, .radio-label {
      display: flex;
      align-items: center;
      cursor: pointer;
    }
    
    .checkbox-label input, .radio-label input {
      margin-right: 8px;
    }
    
    .modal-options {
      margin-top: 15px;
      border-top: 1px solid #eaecf4;
      padding-top: 15px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
      .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }
      
      .page-header-actions {
        width: 100%;
        justify-content: flex-start;
      }
      
      .filter-section, .search-section {
        width: 100%;
        flex: auto;
      }
    }
  `;
  document.head.appendChild(style);
})();