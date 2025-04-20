/**
 * admin-clients.js
 * Backsure Global Support - Client Management JavaScript
 * Handles functionality for admin client management with enhanced security
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize client filters
  initializeClientFilters();
  
  // Initialize client actions (view, block/unblock, delete)
  initializeClientActions();
  
  // Initialize client form validations
  initializeClientFormValidations();
  
  // Initialize datatables if available
  initializeDataTables();
});

/**
 * Initialize client filters
 */
function initializeClientFilters() {
  const filterSelects = document.querySelectorAll('.client-filter select');
  const filterSearchInput = document.querySelector('.client-filter input[type="search"]');
  
  if (filterSelects.length) {
    filterSelects.forEach(select => {
      select.addEventListener('change', function() {
        applyClientFilters();
      });
    });
  }
  
  if (filterSearchInput) {
    filterSearchInput.addEventListener('input', function() {
      // Debounce search to avoid excessive filtering
      clearTimeout(this.searchTimer);
      
      this.searchTimer = setTimeout(() => {
        applyClientFilters();
      }, 300);
    });
  }
}

/**
 * Apply filters to client list
 */
function applyClientFilters() {
  const statusFilter = document.getElementById('status-filter');
  const searchInput = document.querySelector('.client-filter input[type="search"]');
  
  const statusValue = statusFilter ? statusFilter.value : 'all';
  const searchValue = searchInput ? searchInput.value.trim().toLowerCase() : '';
  
  const clientRows = document.querySelectorAll('.clients-table tbody tr');
  
  // Show all clients initially
  clientRows.forEach(row => row.style.display = 'table-row');
  
  // Filter by status
  if (statusValue !== 'all') {
    clientRows.forEach(row => {
      const statusBadge = row.querySelector('.status-badge');
      if (statusBadge && !statusBadge.classList.contains(statusValue)) {
        row.style.display = 'none';
      }
    });
  }
  
  // Filter by search term
  if (searchValue) {
    clientRows.forEach(row => {
      if (row.style.display === 'none') return;
      
      const clientName = row.querySelector('.client-name').textContent.toLowerCase();
      const clientEmail = row.querySelector('.client-email').textContent.toLowerCase();
      const clientCompany = row.querySelector('.client-company') ? 
                           row.querySelector('.client-company').textContent.toLowerCase() : '';
      
      if (!clientName.includes(searchValue) && 
          !clientEmail.includes(searchValue) &&
          !clientCompany.includes(searchValue)) {
        row.style.display = 'none';
      }
    });
  }
  
  // Update results count
  updateClientResultsCount();
}

/**
 * Update client results count
 */
function updateClientResultsCount() {
  const visibleRows = document.querySelectorAll('.clients-table tbody tr[style="display: table-row"]');
  const totalRows = document.querySelectorAll('.clients-table tbody tr');
  
  const resultsInfo = document.querySelector('.results-info');
  
  if (resultsInfo) {
    resultsInfo.textContent = `Showing ${visibleRows.length} of ${totalRows.length} clients`;
  }
}

/**
 * Initialize DataTables if the library is available
 */
function initializeDataTables() {
  if (typeof $.fn.DataTable !== 'undefined') {
    $('.clients-table').DataTable({
      responsive: true,
      dom: 'Bfrtip',
      buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
      ],
      order: [[5, 'desc']] // Sort by registration date by default
    });
  }
}

/**
 * Initialize client actions (view, block/unblock, delete)
 */
function initializeClientActions() {
  // View client details
  document.querySelectorAll('.view-client-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      
      const clientId = this.getAttribute('data-id');
      const viewModal = document.getElementById('view-client-modal');
      
      if (viewModal) {
        fetchClientDetails(clientId);
      }
    });
  });
  
  // Block/unblock client toggle
  document.querySelectorAll('.status-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
      const clientId = this.getAttribute('data-id');
      const newStatus = this.checked ? 'active' : 'blocked';
      
      updateClientStatus(clientId, newStatus, this);
    });
  });
  
  // Delete client buttons
  document.querySelectorAll('.delete-client-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      
      const clientId = this.getAttribute('data-id');
      const row = this.closest('tr');
      const clientName = row.querySelector('.client-name').textContent;
      
      confirmDeleteClient(clientId, clientName, row);
    });
  });
  
  // Send verification email buttons
  document.querySelectorAll('.send-verification-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      
      const clientId = this.getAttribute('data-id');
      const clientEmail = this.getAttribute('data-email');
      
      sendVerificationEmail(clientId, clientEmail);
    });
  });
}

/**
 * Fetch client details for viewing
 */
function fetchClientDetails(clientId) {
  const viewModal = document.getElementById('view-client-modal');
  const modalBody = viewModal.querySelector('.modal-body');
  const spinner = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading client details...</p></div>';
  
  // Get CSRF token
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
  // Show loading state
  modalBody.innerHTML = spinner;
  viewModal.style.display = 'block';
  
  // Fetch client details via AJAX
  fetch('ajax/get-client-details.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-Token': csrfToken
    },
    body: JSON.stringify({ client_id: clientId })
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      // Populate modal with client details
      populateClientModal(data.client, modalBody);
    } else {
      modalBody.innerHTML = `<div class="alert alert-danger">${data.message || 'Error loading client details'}</div>`;
    }
  })
  .catch(error => {
    console.error('Error fetching client details:', error);
    modalBody.innerHTML = `<div class="alert alert-danger">Failed to load client details. Please try again.</div>`;
  });
}

/**
 * Populate client modal with data
 */
function populateClientModal(client, modalBody) {
  const statusClasses = {
    active: 'success',
    pending: 'warning',
    blocked: 'danger',
    suspended: 'secondary'
  };
  
  const statusClass = statusClasses[client.status] || 'secondary';
  
  let html = `
    <div class="client-profile">
      <div class="client-header">
        <h3>${client.name}</h3>
        <span class="badge badge-${statusClass}">${client.status}</span>
      </div>
      
      <div class="client-info">
        <div class="info-row">
          <div class="info-label">Email:</div>
          <div class="info-value">${client.email}</div>
        </div>`;
  
  if (client.company) {
    html += `
        <div class="info-row">
          <div class="info-label">Company:</div>
          <div class="info-value">${client.company}</div>
        </div>`;
  }
  
  html += `
        <div class="info-row">
          <div class="info-label">Registered:</div>
          <div class="info-value">${client.created_at}</div>
        </div>
        <div class="info-row">
          <div class="info-label">Last Login:</div>
          <div class="info-value">${client.last_login || 'Never'}</div>
        </div>
      </div>
      
      <div class="client-activity">
        <h4>Recent Activity</h4>
        <div class="activity-list">`;
  
  if (client.activities && client.activities.length) {
    client.activities.forEach(activity => {
      html += `
          <div class="activity-item">
            <div class="activity-time">${activity.created_at}</div>
            <div class="activity-action">${activity.action}</div>
            <div class="activity-details">${activity.details || ''}</div>
          </div>`;
    });
  } else {
    html += `<p>No recent activity</p>`;
  }
  
  html += `
        </div>
      </div>
    </div>`;
  
  modalBody.innerHTML = html;
}

/**
 * Update client status (block/unblock)
 */
function updateClientStatus(clientId, status, toggleElement) {
  const row = toggleElement.closest('tr');
  const statusBadge = row.querySelector('.status-badge');
  const clientName = row.querySelector('.client-name').textContent;
  
  // Get CSRF token
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
  // Show spinner on toggle
  const originalLabel = toggleElement.parentNode.querySelector('span.toggle-label');
  let originalLabelText = '';
  
  if (originalLabel) {
    originalLabelText = originalLabel.textContent;
    originalLabel.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  }
  
  // Disable toggle during request
  toggleElement.disabled = true;
  
  // Send AJAX request to update status
  fetch('ajax/update-client-status.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-Token': csrfToken
    },
    body: JSON.stringify({
      client_id: clientId,
      status: status
    })
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      // Update UI to reflect new status
      updateClientStatusUI(statusBadge, status);
      
      // Show success notification
      showNotification(
        `Client "${clientName}" has been ${status === 'active' ? 'activated' : 'blocked'}.`, 
        'success'
      );
      
      // Log the action
      console.log(`Client ID ${clientId} status changed to ${status}`);
    } else {
      // Revert toggle state
      toggleElement.checked = status !== 'active';
      
      // Show error notification
      showNotification(data.message || 'Failed to update client status', 'error');
    }
  })
  .catch(error => {
    console.error('Error updating client status:', error);
    
    // Revert toggle state
    toggleElement.checked = status !== 'active';
    
    // Show error notification
    showNotification('Failed to update client status. Please try again.', 'error');
  })
  .finally(() => {
    // Re-enable toggle
    toggleElement.disabled = false;
    
    // Restore original label
    if (originalLabel) {
      originalLabel.textContent = originalLabelText;
    }
  });
}

/**
 * Update client status UI
 */
function updateClientStatusUI(statusBadge, status) {
  if (!statusBadge) return;
  
  // Remove all existing status classes
  statusBadge.classList.remove('active', 'pending', 'blocked', 'suspended');
  
  // Add new status class
  statusBadge.classList.add(status);
  
  // Update text
  statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
}

/**
 * Confirm and delete client
 */
function confirmDeleteClient(clientId, clientName, row) {
  // Create confirmation modal if not exists
  let confirmModal = document.getElementById('confirm-delete-modal');
  
  if (!confirmModal) {
    confirmModal = document.createElement('div');
    confirmModal.id = 'confirm-delete-modal';
    confirmModal.className = 'modal';
    confirmModal.innerHTML = `
      <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Confirm Deletion</h3>
        <p id="confirm-message"></p>
        <div class="modal-actions">
          <button id="cancel-delete" class="btn btn-secondary">Cancel</button>
          <button id="confirm-delete" class="btn btn-danger">Delete</button>
        </div>
      </div>
    `;
    document.body.appendChild(confirmModal);
    
    // Add close button functionality
    confirmModal.querySelector('.close').addEventListener('click', function() {
      confirmModal.style.display = 'none';
    });
    
    // Add cancel button functionality
    confirmModal.querySelector('#cancel-delete').addEventListener('click', function() {
      confirmModal.style.display = 'none';
    });
    
    // Close when clicking outside
    window.addEventListener('click', function(event) {
      if (event.target === confirmModal) {
        confirmModal.style.display = 'none';
      }
    });
  }
  
  // Set confirmation message
  confirmModal.querySelector('#confirm-message').textContent = 
    `Are you sure you want to delete client "${clientName}"? This action cannot be undone.`;
  
  // Set up confirm button
  const confirmButton = confirmModal.querySelector('#confirm-delete');
  
  // Remove any existing event listeners
  const newConfirmButton = confirmButton.cloneNode(true);
  confirmButton.parentNode.replaceChild(newConfirmButton, confirmButton);
  
  // Add delete action
  newConfirmButton.addEventListener('click', function() {
    deleteClient(clientId, clientName, row, confirmModal);
  });
  
  // Show the modal
  confirmModal.style.display = 'block';
}

/**
 * Delete client via AJAX
 */
function deleteClient(clientId, clientName, row, modal) {
  // Get CSRF token
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
  // Update modal to show loading
  const confirmButton = modal.querySelector('#confirm-delete');
  const originalText = confirmButton.textContent;
  confirmButton.disabled = true;
  confirmButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
  
  // Send AJAX request to delete client
  fetch('ajax/delete-client.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-Token': csrfToken
    },
    body: JSON.stringify({ client_id: clientId })
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      // Hide modal
      modal.style.display = 'none';
      
      // Remove row with animation
      row.classList.add('deleting');
      
      setTimeout(() => {
        row.remove();
        updateClientResultsCount();
        
        // Show success notification
        showNotification(`Client "${clientName}" has been deleted.`, 'success');
      }, 500);
    } else {
      // Show error in modal
      modal.querySelector('#confirm-message').innerHTML = `
        <div class="alert alert-danger">
          ${data.message || 'Failed to delete client. Please try again.'}
        </div>
      `;
      
      // Reset button
      confirmButton.disabled = false;
      confirmButton.textContent = originalText;
    }
  })
  .catch(error => {
    console.error('Error deleting client:', error);
    
    // Show error in modal
    modal.querySelector('#confirm-message').innerHTML = `
      <div class="alert alert-danger">
        An error occurred. Please try again.
      </div>
    `;
    
    // Reset button
    confirmButton.disabled = false;
    confirmButton.textContent = originalText;
  });
}

/**
 * Send verification email to client
 */
function sendVerificationEmail(clientId, clientEmail) {
  // Get CSRF token
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
  // Show loading state
  const btn = document.querySelector(`.send-verification-btn[data-id="${clientId}"]`);
  const originalText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  
  // Send AJAX request
  fetch('ajax/send-verification-email.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-Token': csrfToken
    },
    body: JSON.stringify({
      client_id: clientId,
      email: clientEmail
    })
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      showNotification(`Verification email sent to ${clientEmail}`, 'success');
    } else {
      showNotification(data.message || 'Failed to send verification email', 'error');
    }
  })
  .catch(error => {
    console.error('Error sending verification email:', error);
    showNotification('Failed to send verification email. Please try again.', 'error');
  })
  .finally(() => {
    // Reset button
    setTimeout(() => {
      btn.disabled = false;
      btn.innerHTML = originalText;
    }, 1000);
  });
}

/**
 * Initialize client form validations
 */
function initializeClientFormValidations() {
  const addForm = document.getElementById('add-client-form');
  
  if (addForm) {
    addForm.addEventListener('submit', function(e) {
      if (!validateClientForm(this)) {
        e.preventDefault();
      }
    });
  }
}

/**
 * Validate client form
 */
function validateClientForm(form) {
  let isValid = true;
  
  // Get form fields
  const nameField = form.querySelector('#name');
  const emailField = form.querySelector('#email');
  const passwordField = form.querySelector('#password');
  const confirmPasswordField = form.querySelector('#confirm-password');
  
  // Clear previous errors
  form.querySelectorAll('.error-message').forEach(error => error.remove());
  form.querySelectorAll('.input-error').forEach(field => field.classList.remove('input-error'));
  
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
  if (!passwordField.value) {
    showFormError(passwordField, 'Password is required');
    isValid = false;
  } else if (passwordField.value.length < 8) {
    showFormError(passwordField, 'Password must be at least 8 characters long');
    isValid = false;
  }
  
  // Check password confirmation
  if (passwordField.value !== confirmPasswordField.value) {
    showFormError(confirmPasswordField, 'Passwords do not match');
    isValid = false;
  }
  
  // Check for CSRF token
  const csrfToken = form.querySelector('input[name="csrf_token"]');
  if (!csrfToken || !csrfToken.value) {
    showFormError(form.querySelector('button[type="submit"]'), 'Security token is missing. Please refresh the page.');
    isValid = false;
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

/**
 * Add CSRF token to all AJAX requests
 */
document.addEventListener('DOMContentLoaded', function() {
  // Get the CSRF token from meta tag
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  
  // Add token to all fetch requests
  const originalFetch = window.fetch;
  window.fetch = function(url, options = {}) {
    // Only add token to same-origin requests
    if (typeof url === 'string' && (url.startsWith('/') || url.startsWith(window.location.origin))) {
      options = options || {};
      options.headers = options.headers || {};
      
      // Don't override if already set
      if (!options.headers['X-CSRF-Token'] && csrfToken) {
        options.headers['X-CSRF-Token'] = csrfToken;
      }
    }
    
    return originalFetch(url, options);
  };
  
  // Add token to all XMLHttpRequest
  const originalOpen = XMLHttpRequest.prototype.open;
  XMLHttpRequest.prototype.open = function() {
    const result = originalOpen.apply(this, arguments);
    
    // Only add token to same-origin requests
    const url = arguments[1];
    if (typeof url === 'string' && (url.startsWith('/') || url.startsWith(window.location.origin))) {
      this.setRequestHeader('X-CSRF-Token', csrfToken);
    }
    
    return result;
  };
  
  // Add token to jQuery AJAX if jQuery exists
  if (typeof $ !== 'undefined' && $.ajax) {
    $(document).ajaxSend(function(e, xhr, options) {
      xhr.setRequestHeader('X-CSRF-Token', csrfToken);
    });
  }
});

/**
 * Session timeout handling
 */
document.addEventListener('DOMContentLoaded', function() {
  // Check for session timeout every minute
  setInterval(checkSessionStatus, 60000);
  
  // Also check on user activity
  let activityTimeout;
  
  function resetActivityTimeout() {
    clearTimeout(activityTimeout);
    activityTimeout = setTimeout(checkSessionStatus, 5 * 60000); // Check after 5 minutes of inactivity
  }
  
  // Monitor user activity
  ['mousemove', 'mousedown', 'keypress', 'scroll', 'touchstart'].forEach(event => {
    document.addEventListener(event, resetActivityTimeout, true);
  });
  
  // Initial timeout
  resetActivityTimeout();
});

/**
 * Check if session is still valid
 */
function checkSessionStatus() {
  fetch('ajax/check-session.php', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (!data.valid) {
      // Session expired, show message and redirect
      showSessionExpiredModal();
    }
  })
  .catch(error => {
    console.error('Error checking session status:', error);
  });
}

/**
 * Show session expired modal
 */
function showSessionExpiredModal() {
  // Create session expired modal if not exists
  let sessionModal = document.getElementById('session-expired-modal');
  
  if (!sessionModal) {
    sessionModal = document.createElement('div');
    sessionModal.id = 'session-expired-modal';
    sessionModal.className = 'modal';
    sessionModal.innerHTML = `
      <div class="modal-content">
        <h3>Session Expired</h3>
        <p>Your session has expired. Please log in again to continue.</p>
        <div class="modal-actions">
          <button id="session-login-btn" class="btn btn-primary">Log In</button>
        </div>
      </div>
    `;
    document.body.appendChild(sessionModal);
    
    // Add login button functionality
    sessionModal.querySelector('#session-login-btn').addEventListener('click', function() {
      window.location.href = 'admin-login.html';
    });
  }
  
  // Show the modal
  sessionModal.style.display = 'block';
  
  // Redirect after 3 seconds
  setTimeout(() => {
    window.location.href = 'admin-login.html?session=expired';
  }, 3000);
}