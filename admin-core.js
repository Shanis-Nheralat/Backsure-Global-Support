/**
 * Admin Core JavaScript
 * Core functionality for the admin panel
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize all components
  initSidebar();
  initDropdowns();
  initModals();
  
  // Initialize any message auto-hide
  initMessageAutoHide();
});

/**
 * Initialize sidebar functionality
 */
function initSidebar() {
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const adminContainer = document.querySelector('.admin-container');
  
  // Create backdrop element for mobile
  if (!document.querySelector('.sidebar-backdrop')) {
    const backdrop = document.createElement('div');
    backdrop.className = 'sidebar-backdrop';
    document.body.appendChild(backdrop);
    
    // Close sidebar when clicking backdrop
    backdrop.addEventListener('click', function() {
      adminContainer.classList.remove('sidebar-active');
    });
  }
  
  // Toggle sidebar
  if (sidebarToggle && adminContainer) {
    sidebarToggle.addEventListener('click', function() {
      if (window.innerWidth >= 768) {
        // On desktop, toggle collapsed state
        adminContainer.classList.toggle('sidebar-collapsed');
      } else {
        // On mobile, toggle active state
        adminContainer.classList.toggle('sidebar-active');
      }
    });
  }
  
  // Submenu toggle functionality
  const submenuItems = document.querySelectorAll('.has-submenu > a');
  
  submenuItems.forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      const parent = this.parentElement;
      
      // Close other open submenus
      if (!e.ctrlKey) { // Allow opening multiple with CTRL key
        const openItems = document.querySelectorAll('.has-submenu.open');
        openItems.forEach(openItem => {
          if (openItem !== parent) {
            openItem.classList.remove('open');
            const submenu = openItem.querySelector('.submenu');
            if (submenu) {
              submenu.style.maxHeight = null;
            }
          }
        });
      }
      
      // Toggle current submenu
      parent.classList.toggle('open');
      const submenu = parent.querySelector('.submenu');
      
      if (submenu) {
        if (parent.classList.contains('open')) {
          submenu.style.maxHeight = submenu.scrollHeight + 'px';
        } else {
          submenu.style.maxHeight = null;
        }
      }
    });
  });
  
  // Adjust for mobile screens on resize
  window.addEventListener('resize', function() {
    if (window.innerWidth >= 768) {
      // Remove active class on larger screens
      adminContainer.classList.remove('sidebar-active');
    } else {
      // Remove collapsed class on smaller screens
      adminContainer.classList.remove('sidebar-collapsed');
    }
  });
  
  // Initial check
  if (window.innerWidth < 768) {
    adminContainer.classList.remove('sidebar-collapsed');
  }
}

/**
 * Initialize dropdown menus
 */
function initDropdowns() {
  // User profile dropdown
  const userDropdownToggle = document.getElementById('user-dropdown-toggle');
  const userDropdown = document.getElementById('user-dropdown');
  
  if (userDropdownToggle && userDropdown) {
    userDropdownToggle.addEventListener('click', function(e) {
      e.stopPropagation();
      userDropdown.classList.toggle('show');
    });
  }
  
  // Close dropdowns when clicking outside
  document.addEventListener('click', function(e) {
    const dropdowns = document.querySelectorAll('.dropdown-menu.show');
    dropdowns.forEach(dropdown => {
      if (!dropdown.contains(e.target) && !e.target.matches('.dropdown-toggle')) {
        dropdown.classList.remove('show');
      }
    });
  });
  
  // Header action dropdowns (notifications, etc.)
  const actionButtons = document.querySelectorAll('.action-btn');
  
  actionButtons.forEach(button => {
    const dropdown = button.querySelector('.dropdown-menu');
    if (dropdown) {
      button.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
      });
    }
  });
}

/**
 * Initialize modal dialogs
 */
function initModals() {
  // Open modals
  const modalTriggers = document.querySelectorAll('[data-modal]');
  modalTriggers.forEach(trigger => {
    trigger.addEventListener('click', function(e) {
      e.preventDefault();
      const modalId = this.getAttribute('data-modal');
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.style.display = 'block';
      }
    });
  });
  
  // Close modals
  const modalCloseButtons = document.querySelectorAll('.modal-close, [data-dismiss="modal"]');
  modalCloseButtons.forEach(button => {
    button.addEventListener('click', function() {
      const modal = this.closest('.modal');
      if (modal) {
        modal.style.display = 'none';
      }
    });
  });
  
  // Close modal when clicking outside
  window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
      e.target.style.display = 'none';
    }
  });
}

/**
 * Initialize auto-hide for success messages
 */
function initMessageAutoHide() {
  const successMessages = document.querySelectorAll('.message-success, .alert-success');
  successMessages.forEach(message => {
    setTimeout(() => {
      message.style.opacity = '0';
      setTimeout(() => {
        message.style.display = 'none';
      }, 500);
    }, 5000);
  });
}

/**
 * Confirm action with modal or browser confirm
 * @param {string} message - Confirmation message
 * @param {function} callback - Function to call if confirmed
 * @param {string} modalId - Optional modal ID to use instead of browser confirm
 */
function confirmAction(message, callback, modalId = null) {
  if (modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      // Set message
      const messageEl = modal.querySelector('.confirm-message');
      if (messageEl) {
        messageEl.textContent = message;
      }
      
      // Set confirm button action
      const confirmBtn = modal.querySelector('.confirm-action');
      if (confirmBtn) {
        // Remove any existing event listeners
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        // Add new event listener
        newConfirmBtn.addEventListener('click', function() {
          modal.style.display = 'none';
          callback();
        });
      }
      
      // Show modal
      modal.style.display = 'block';
    } else {
      // Fallback to browser confirm
      if (confirm(message)) {
        callback();
      }
    }
  } else {
    // Use browser confirm
    if (confirm(message)) {
      callback();
    }
  }
}

/**
 * Form validation helper
 * @param {HTMLFormElement} form - Form to validate
 * @return {boolean} - True if valid, false otherwise
 */
function validateForm(form) {
  const requiredFields = form.querySelectorAll('[required]');
  let isValid = true;
  
  requiredFields.forEach(field => {
    if (!field.value.trim()) {
      isValid = false;
      field.classList.add('is-invalid');
      
      // Add error message if not exists
      let errorMessage = field.nextElementSibling;
      if (!errorMessage || !errorMessage.classList.contains('error-message')) {
        errorMessage = document.createElement('div');
        errorMessage.className = 'error-message';
        errorMessage.textContent = 'This field is required';
        field.parentNode.insertBefore(errorMessage, field.nextSibling);
      }
    } else {
      field.classList.remove('is-invalid');
      
      // Remove error message if exists
      const errorMessage = field.nextElementSibling;
      if (errorMessage && errorMessage.classList.contains('error-message')) {
        errorMessage.remove();
      }
    }
  });
  
  return isValid;
}

/**
 * Ajax helper function 
 * @param {string} url - URL to send request to
 * @param {Object} options - Request options
 * @param {function} callback - Function to call with response
 */
function ajaxRequest(url, options = {}, callback) {
  // Default options
  const defaultOptions = {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    data: null
  };
  
  // Merge options
  const requestOptions = {...defaultOptions, ...options};
  
  // Create request
  const xhr = new XMLHttpRequest();
  xhr.open(requestOptions.method, url, true);
  
  // Set headers
  Object.keys(requestOptions.headers).forEach(header => {
    xhr.setRequestHeader(header, requestOptions.headers[header]);
  });
  
  // Handle response
  xhr.onload = function() {
    if (xhr.status >= 200 && xhr.status < 300) {
      // Success
      let response;
      try {
        response = JSON.parse(xhr.responseText);
      } catch (e) {
        response = xhr.responseText;
      }
      callback(null, response);
    } else {
      // Error
      callback(xhr.statusText);
    }
  };
  
  // Handle network errors
  xhr.onerror = function() {
    callback('Network Error');
  };
  
  // Send request
  if (requestOptions.data) {
    xhr.send(JSON.stringify(requestOptions.data));
  } else {
    xhr.send();
  }
}
