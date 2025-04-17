/**
 * users.js
 * Backsure Global Support - User Management JavaScript
 * Handles functionality for admin user management
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize user filters
  initializeUserFilters();
  
  // Initialize user actions (edit, view, delete)
  initializeUserActions();
  
  // Initialize user form validations
  initializeUserFormValidations();
  
  // Initialize role permissions
  initializeRolePermissions();
});

/**
 * Initialize user filters
 */
function initializeUserFilters() {
  const filterSelects = document.querySelectorAll('.user-filter select');
  const filterSearchInput = document.querySelector('.user-filter input[type="search"]');
  
  if (filterSelects.length) {
    filterSelects.forEach(select => {
      select.addEventListener('change', function() {
        applyUserFilters();
      });
    });
  }
  
  if (filterSearchInput) {
    filterSearchInput.addEventListener('input', function() {
      // Debounce search to avoid excessive filtering
      clearTimeout(this.searchTimer);
      
      this.searchTimer = setTimeout(() => {
        applyUserFilters();
      }, 300);
    });
  }
}

/**
 * Apply filters to user list
 */
function applyUserFilters() {
  const roleFilter = document.getElementById('role-filter');
  const statusFilter = document.getElementById('status-filter');
  const searchInput = document.querySelector('.user-filter input[type="search"]');
  
  const roleValue = roleFilter ? roleFilter.value : 'all';
  const statusValue = statusFilter ? statusFilter.value : 'all';
  const searchValue = searchInput ? searchInput.value.trim().toLowerCase() : '';
  
  const userRows = document.querySelectorAll('.users-table tbody tr');
  
  // Show all users initially
  userRows.forEach(row => row.style.display = 'table-row');
  
  // Filter by role
  if (roleValue !== 'all') {
    userRows.forEach(row => {
      const userRole = row.querySelector('.user-role').textContent.toLowerCase();
      if (userRole !== roleValue) {
        row.style.display = 'none';
      }
    });
  }
  
  // Filter by status
  if (statusValue !== 'all') {
    userRows.forEach(row => {
      const activeStatus = row.querySelector('.status-badge').classList.contains('active');
      if ((statusValue === 'active' && !activeStatus) || (statusValue === 'inactive' && activeStatus)) {
        row.style.display = 'none';
      }
    });
  }
  
  // Filter by search term
  if (searchValue) {
    userRows.forEach(row => {
      if (row.style.display === 'none') return;
      
      const userName = row.querySelector('.user-name').textContent.toLowerCase();
      const userEmail = row.querySelector('.user-email').textContent.toLowerCase();
      
      if (!userName.includes(searchValue) && !userEmail.includes(searchValue)) {
        row.style.display = 'none';
      }
    });
  }
  
  // Update results count
  updateUserResultsCount();
}

/**
 * Update user results count
 */
function updateUserResultsCount() {
  const visibleRows = document.querySelectorAll('.users-table tbody tr[style="display: table-row"]');
  const totalRows = document.querySelectorAll('.users-table tbody tr');
  
  const resultsInfo = document.querySelector('.results-info');
  
  if (resultsInfo) {
    resultsInfo.textContent = `Showing ${visibleRows.length} of ${totalRows.length} users`;
  }
}

/**
 * Initialize user actions (edit, view, delete)
 */
function initializeUserActions() {
  // Edit user buttons
  const editButtons = document.querySelectorAll('.edit-user-btn');
  
  editButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      
      const userId = this.getAttribute('data-id');
      
      // Open edit modal or redirect to edit page
      const editModal = document.getElementById('edit-user-modal');
      
      if (editModal) {
        editModal.style.display = 'flex';
        loadUserData(userId);
      } else {
        window.location.href = `admin-users-edit.html?id=${userId}`;
      }
    });
  });
  
  // Delete user buttons
  const deleteButtons = document.querySelectorAll('.delete-user-btn');
  
  deleteButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      
      const userId = this.getAttribute('data-id');
      const row = this.closest('tr');
      const userName = row.querySelector('.user-name').textContent;
      
      if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
        // In a real app, send delete request to server
        // For demo purposes, simulate server action
        row.classList.add('deleting');
        
        setTimeout(() => {
          row.remove();
          updateUserResultsCount();
          showNotification(`User "${userName}" has been deleted.`, 'success');
        }, 500);
      }
    });
  });
  
  // Add new user button
  const addUserBtn = document.getElementById('add-user-btn');
  
  if (addUserBtn) {
    addUserBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Open add user modal or redirect to add user page
      const addModal = document.getElementById('add-user-modal');
      
      if (addModal) {
        addModal.style.display = 'flex';
        resetUserForm();
      } else {
        window.location.href = 'admin-users-add.html';
      }
    });
  }
  
  // Toggle user status
  const statusToggles = document.querySelectorAll('.status-toggle');
  
  statusToggles.forEach(toggle => {
    toggle.addEventListener('change', function() {
      const userId = this.getAttribute('data-id');
      const row = this.closest('tr');
      const statusBadge = row.querySelector('.status-badge');
      const userName = row.querySelector('.user-name').textContent;
      
      // Update UI
      if (this.checked) {
        statusBadge.textContent = 'Active';
        statusBadge.classList.remove('inactive');
        statusBadge.classList.add('active');
        showNotification(`User "${userName}" has been activated.`, 'success');
      } else {
        statusBadge.textContent = 'Inactive';
        statusBadge.classList.remove('active');
        statusBadge.classList.add('inactive');
        showNotification(`User "${userName}" has been deactivated.`, 'success');
      }
      
      // In a real app, send status update to server
      console.log(`User ID ${userId} status changed to ${this.checked ? 'active' : 'inactive'}`);
    });
  });
}

/**
 * Load user data for editing
 * In a real app, this would fetch data from the server
 */
function loadUserData(userId) {
  const editForm = document.getElementById('edit-user-form');
  const modalTitle = document.querySelector('#edit-user-modal .modal-title');
  
  if (!editForm || !modalTitle) return;
  
  // For demo purposes, get data from the table row
  const userRow = document.querySelector(`.users-table tr[data-id="${userId}"]`);
  
  if (!userRow) return;
  
  const userName = userRow.querySelector('.user-name').textContent;
  const userEmail = userRow.querySelector('.user-email').textContent;
  const userRole = userRow.querySelector('.user-role').textContent;
  const isActive = userRow.querySelector('.status-badge').classList.contains('active');
  
  // Update modal title
  modalTitle.textContent = `Edit User: ${userName}`;
  
  // Set form values
  editForm.querySelector('#edit-user-id').value = userId;
  editForm.querySelector('#edit-name').value = userName;
  editForm.querySelector('#edit-email').value = userEmail;
  editForm.querySelector('#edit-role').value = userRole.toLowerCase();
  editForm.querySelector('#edit-status').checked = isActive;
  
  // Clear password fields
  editForm.querySelector('#edit-password').value = '';
  editForm.querySelector('#edit-confirm-password').value = '';
}

/**
 * Reset user form for adding new user
 */
function resetUserForm() {
  const addForm = document.getElementById('add-user-form');
  
  if (!addForm) return;
  
  // Clear all fields
  addForm.reset();
  
  // Set default role and status
  addForm.querySelector('#add-role').value = 'editor';
  addForm.querySelector('#add-status').checked = true;
  
  // Remove any error messages
  const errorMessages = addForm.querySelectorAll('.error-message');
  
  errorMessages.forEach(error => {
    error.remove();
  });
  
  // Remove error classes
  const inputFields = addForm.querySelectorAll('.input-error');
  
  inputFields.forEach(field => {
    field.classList.remove('input-error');
  });
}

/**
 * Initialize user form validations
 */
function initializeUserFormValidations() {
  const addForm = document.getElementById('add-user-form');
  const editForm = document.getElementById('edit-user-form');
  
  if (addForm) {
    addForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      if (validateUserForm(this, 'add')) {
        // In a real app, send data to server
        // For demo purposes, simulate server response
        const formData = new FormData(this);
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding User...';
        
        setTimeout(() => {
          // Reset button
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
          
          // Close modal
          const modal = this.closest('.modal');
          if (modal) {
            modal.style.display = 'none';
          }
          
          // Show success message
          showNotification('User added successfully!', 'success');
          
          // Reload page to show new user
          // In a real app, you would add the new user to the table dynamically
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        }, 1500);
      }
    });
  }
  
  if (editForm) {
    editForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      if (validateUserForm(this, 'edit')) {
        // In a real app, send data to server
        // For demo purposes, simulate server response
        const formData = new FormData(this);
        const userId = formData.get('user-id');
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating User...';
        
        setTimeout(() => {
          // Reset button
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
          
          // Close modal
          const modal = this.closest('.modal');
          if (modal) {
            modal.style.display = 'none';
          }
          
          // Update user row in table
          updateUserRow(formData);
          
          // Show success message
          showNotification('User updated successfully!', 'success');
        }, 1500);
      }
    });
  }
}

/**
 * Validate user form
 */
function validateUserForm(form, formType) {
  let isValid = true;
  
  // Get form fields
  const nameField = form.querySelector(`#${formType}-name`);
  const emailField = form.querySelector(`#${formType}-email`);
  const passwordField = form.querySelector(`#${formType}-password`);
  const confirmPasswordField = form.querySelector(`#${formType}-confirm-password`);
  
  // Clear previous errors
  const errorMessages = form.querySelectorAll('.error-message');
  errorMessages.forEach(error => error.remove());
  
  const errorFields = form.querySelectorAll('.input-error');
  errorFields.forEach(field => field.classList.remove('input-error'));
  
  // Validate name
  if (!nameField.value.trim()) {
    showFormError(nameField, 'Name is required');
    isValid = false;
  }
  
  // Validate email
  if (!emailField.value.trim()) {
    showFormError(emailField, 'Email is required');
    isValid = false;
  } else if (!isValidEmail(emailField.value)) {
    showFormError(emailField, 'Please enter a valid email address');
    isValid = false;
  }
  
  // Validate password
  // For add form, password is required
  if (formType === 'add' && !passwordField.value) {
    showFormError(passwordField, 'Password is required');
    isValid = false;
  }
  
  // For edit form, password is optional, but if provided must be valid
  if (passwordField.value) {
    // Check password strength
    if (passwordField.value.length < 8) {
      showFormError(passwordField, 'Password must be at least 8 characters long');
      isValid = false;
    }
    
    // Check password confirmation
    if (passwordField.value !== confirmPasswordField.value) {
      showFormError(confirmPasswordField, 'Passwords do not match');
      isValid = false;
    }
  }
  
  return isValid;
}

/**
 * Show form error message
 */
function showFormError(field, message) {
  // Add error class to field
  field.classList.add('input-error');
  
  // Create error message
  const errorMessage = document.createElement('div');
  errorMessage.className = 'error-message';
  errorMessage.textContent = message;
  
  // Insert after field
  field.parentNode.insertBefore(errorMessage, field.nextSibling);
}

/**
 * Check if email is valid
 */
function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

/**
 * Update user row in table with form data
 */
function updateUserRow(formData) {
  const userId = formData.get('user-id');
  const name = formData.get('name');
  const email = formData.get('email');
  const role = formData.get('role');
  const isActive = formData.get('status') === 'on';
  
  const userRow = document.querySelector(`.users-table tr[data-id="${userId}"]`);
  
  if (!userRow) return;
  
  // Update row data
  userRow.querySelector('.user-name').textContent = name;
  userRow.querySelector('.user-email').textContent = email;
  userRow.querySelector('.user-role').textContent = role.charAt(0).toUpperCase() + role.slice(1);
  
  const statusBadge = userRow.querySelector('.status-badge');
  const statusToggle = userRow.querySelector('.status-toggle');
  
  if (isActive) {
    statusBadge.textContent = 'Active';
    statusBadge.classList.remove('inactive');
    statusBadge.classList.add('active');
    statusToggle.checked = true;
  } else {
    statusBadge.textContent = 'Inactive';
    statusBadge.classList.remove('active');
    statusBadge.classList.add('inactive');
    statusToggle.checked = false;
  }
}

/**
 * Initialize role permissions management
 */
function initializeRolePermissions() {
  const roleSelect = document.getElementById('role-select');
  const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
  
  if (!roleSelect || !permissionCheckboxes.length) return;
  
  // Role change event
  roleSelect.addEventListener('change', function() {
    const selectedRole = this.value;
    
    // In a real app, fetch permissions for the selected role
    // For demo purposes, use predefined permissions
    setRolePermissions(selectedRole);
  });
  
  // Initialize with default role
  setRolePermissions(roleSelect.value);
  
  // Save permissions button
  const savePermissionsBtn = document.getElementById('save-permissions');
  
  if (savePermissionsBtn) {
    savePermissionsBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Get selected role
      const selectedRole = roleSelect.value;
      
      // Get selected permissions
      const selectedPermissions = Array.from(permissionCheckboxes)
        .filter(checkbox => checkbox.checked)
        .map(checkbox => checkbox.name);
      
      // In a real app, send permissions to server
      // For demo purposes, show success message
      showNotification(`Permissions updated for ${selectedRole} role.`, 'success');
      
      // Save to localStorage for demo persistence
      localStorage.setItem(`permissions_${selectedRole}`, JSON.stringify(selectedPermissions));
    });
  }
}

/**
 * Set permissions checkboxes based on role
 */
function setRolePermissions(role) {
  const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
  
  if (!permissionCheckboxes.length) return;
  
  // Try to get saved permissions from localStorage
  const savedPermissions = localStorage.getItem(`permissions_${role}`);
  
  if (savedPermissions) {
    const permissions = JSON.parse(savedPermissions);
    
    // Set checkboxes based on saved permissions
    permissionCheckboxes.forEach(checkbox => {
      checkbox.checked = permissions.includes(checkbox.name);
    });
    
    return;
  }
  
  // Default permissions by role
  const defaultPermissions = {
    admin: ['view_dashboard', 'manage_users', 'manage_roles', 'manage_content', 'manage_media', 'manage_settings', 'view_reports', 'manage_comments', 'manage_inquiries'],
    editor: ['view_dashboard', 'manage_content', 'manage_media', 'view_reports', 'manage_comments'],
    author: ['view_dashboard', 'manage_content', 'manage_media'],
    contributor: ['view_dashboard', 'manage_content']
  };
  
  // Set checkboxes based on default permissions
  const permissions = defaultPermissions[role] || [];
  
  permissionCheckboxes.forEach(checkbox => {
    checkbox.checked = permissions.includes(checkbox.name);
  });
}

/**
 * Show notification message
 */
function showNotification(message, type = 'info') {
  // Check if notification container exists
  let notificationContainer = document.querySelector('.notification-container');
  
  if (!notificationContainer) {
    // Create container
    notificationContainer = document.createElement('div');
    notificationContainer.className = 'notification-container';
    document.body.appendChild(notificationContainer);
  }
  
  // Create notification
  const notification = document.createElement('div');
  notification.className = `notification ${type}`;
  notification.innerHTML = `
    <div class="notification-content">
      <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
      <span>${message}</span>
    </div>
    <button class="notification-close"><i class="fas fa-times"></i></button>
  `;
  
  // Add to container
  notificationContainer.appendChild(notification);
  
  // Add close button functionality
  const closeButton = notification.querySelector('.notification-close');
  
  if (closeButton) {
    closeButton.addEventListener('click', function() {
      notification.classList.add('closing');
      
      setTimeout(() => {
        if (notificationContainer.contains(notification)) {
          notificationContainer.removeChild(notification);
        }
      }, 300);
    });
  }
  
  // Auto-remove after 5 seconds
  setTimeout(() => {
    notification.classList.add('closing');
    
    setTimeout(() => {
      if (notificationContainer.contains(notification)) {
        notificationContainer.removeChild(notification);
      }
    }, 300);
  }, 5000);
}