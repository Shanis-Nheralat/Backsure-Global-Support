/**
 * Backsure Global Support - Admin Dashboard JavaScript
 * This file handles all the functionality for the admin dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize sidebar functionality
  initializeSidebar();
  
  // Initialize dropdown menus
  initializeDropdowns();
  
  // Handle modals
  initializeModals();
  
  // Set current date in dashboard
  setCurrentDate();
  
  // Initialize data tables if they exist
  initializeDataTables();

  // Check if logged in, redirect if not
  checkAuth();
});

/**
 * Check if the user is authenticated
 */
function checkAuth() {
  const token = localStorage.getItem('authToken');
  const currentPage = window.location.pathname;
  
  // Skip check for login page
  if (currentPage.includes('login.html')) {
    return;
  }
  
  // If not logged in and trying to access admin page, redirect to login
  if (!token && currentPage.includes('admin-')) {
    window.location.href = 'admin-login.html';
  }
}

/**
 * Initialize sidebar functionality
 */
function initializeSidebar() {
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const adminContainer = document.querySelector('.admin-container');
  
  if (sidebarToggle && adminContainer) {
    sidebarToggle.addEventListener('click', function() {
      adminContainer.classList.toggle('sidebar-collapsed');
      
      // Save state to localStorage
      const isCollapsed = adminContainer.classList.contains('sidebar-collapsed');
      localStorage.setItem('sidebar-collapsed', isCollapsed);
    });
    
    // Check saved state
    const savedState = localStorage.getItem('sidebar-collapsed');
    if (savedState === 'true') {
      adminContainer.classList.add('sidebar-collapsed');
    }
  }
  
  // Mobile sidebar functionality
  if (window.innerWidth <= 992) {
    // Create overlay if it doesn't exist
    let overlay = document.querySelector('.sidebar-overlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.className = 'sidebar-overlay';
      document.body.appendChild(overlay);
    }
    
    // Add click event to overlay
    overlay.addEventListener('click', function() {
      const sidebar = document.querySelector('.admin-sidebar');
      sidebar.classList.remove('active');
      overlay.classList.remove('active');
    });
    
    // Mobile toggle functionality
    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', function() {
        const sidebar = document.querySelector('.admin-sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
      });
    }
  }
  
  // Highlight active menu item
  highlightActiveMenuItem();
}

/**
 * Highlight the active menu item based on current URL
 */
function highlightActiveMenuItem() {
  const currentUrl = window.location.href;
  const menuItems = document.querySelectorAll('.sidebar-nav a');
  
  menuItems.forEach(item => {
    if (currentUrl.includes(item.getAttribute('href'))) {
      // Remove active class from all items
      menuItems.forEach(menuItem => {
        menuItem.parentElement.classList.remove('active');
      });
      
      // Add active class to current item
      item.parentElement.classList.add('active');
      
      // If item is in submenu, open parent menu
      const parentMenu = item.closest('.has-submenu');
      if (parentMenu) {
        parentMenu.classList.add('open');
        const submenu = parentMenu.querySelector('.submenu');
        if (submenu) {
          submenu.style.maxHeight = submenu.scrollHeight + 'px';
        }
      }
    }
  });
}

/**
 * Initialize submenu functionality
 */
function initializeSubmenuToggles() {
  const submenuItems = document.querySelectorAll('.has-submenu > a');
  
  submenuItems.forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      const parent = this.parentElement;
      
      // Close other open submenus
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
      
      // Toggle current submenu
      parent.classList.toggle('open');
      const submenu = parent.querySelector('.submenu');
      
      if (parent.classList.contains('open')) {
        submenu.style.maxHeight = submenu.scrollHeight + 'px';
      } else {
        submenu.style.maxHeight = null;
      }
    });
  });
}

/**
 * Initialize dropdown menus
 */
function initializeDropdowns() {
  const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
  
  dropdownToggles.forEach(toggle => {
    toggle.addEventListener('click', function(e) {
      e.stopPropagation();
      
      const dropdown = this.nextElementSibling;
      dropdown.classList.toggle('show');
      
      // Close other dropdowns
      dropdownToggles.forEach(otherToggle => {
        if (otherToggle !== toggle) {
          const otherDropdown = ot/**
 * Backsure Global Support - Admin Dashboard JavaScript
 * This file handles all the functionality for the admin dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize sidebar functionality
  initializeSidebar();
  
  // Initialize dropdown menus
  initializeDropdowns();
  
  // Handle modals
  initializeModals();
  
  // Set current date in dashboard
  setCurrentDate();
  
  // Initialize data tables if they exist
  initializeDataTables();

  // Check if logged in, redirect if not
  checkAuth();
  
  // Initialize submenu toggles
  initializeSubmenuToggles();
  
  // Initialize any charts on the dashboard
  initializeCharts();
  
  // Initialize form validations
  initializeFormValidations();
});

/**
 * Check if the user is authenticated
 */
function checkAuth() {
  const token = localStorage.getItem('authToken');
  const currentPage = window.location.pathname;
  
  // Skip check for login page
  if (currentPage.includes('login.html')) {
    return;
  }
  
  // If not logged in and trying to access admin page, redirect to login
  if (!token && currentPage.includes('admin-')) {
    window.location.href = 'admin-login.html';
  }
}

/**
 * Initialize sidebar functionality
 */
function initializeSidebar() {
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const adminContainer = document.querySelector('.admin-container');
  
  if (sidebarToggle && adminContainer) {
    sidebarToggle.addEventListener('click', function() {
      adminContainer.classList.toggle('sidebar-collapsed');
      
      // Save state to localStorage
      const isCollapsed = adminContainer.classList.contains('sidebar-collapsed');
      localStorage.setItem('sidebar-collapsed', isCollapsed);
    });
    
    // Check saved state
    const savedState = localStorage.getItem('sidebar-collapsed');
    if (savedState === 'true') {
      adminContainer.classList.add('sidebar-collapsed');
    }
  }
  
  // Mobile sidebar functionality
  if (window.innerWidth <= 992) {
    // Create overlay if it doesn't exist
    let overlay = document.querySelector('.sidebar-overlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.className = 'sidebar-overlay';
      document.body.appendChild(overlay);
    }
    
    // Add click event to overlay
    overlay.addEventListener('click', function() {
      const sidebar = document.querySelector('.admin-sidebar');
      sidebar.classList.remove('active');
      overlay.classList.remove('active');
    });
    
    // Mobile toggle functionality
    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', function() {
        const sidebar = document.querySelector('.admin-sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
      });
    }
  }
  
  // Highlight active menu item
  highlightActiveMenuItem();
}

/**
 * Highlight the active menu item based on current URL
 */
function highlightActiveMenuItem() {
  const currentUrl = window.location.href;
  const menuItems = document.querySelectorAll('.sidebar-nav a');
  
  menuItems.forEach(item => {
    if (currentUrl.includes(item.getAttribute('href'))) {
      // Remove active class from all items
      menuItems.forEach(menuItem => {
        menuItem.parentElement.classList.remove('active');
      });
      
      // Add active class to current item
      item.parentElement.classList.add('active');
      
      // If item is in submenu, open parent menu
      const parentMenu = item.closest('.has-submenu');
      if (parentMenu) {
        parentMenu.classList.add('open');
        const submenu = parentMenu.querySelector('.submenu');
        if (submenu) {
          submenu.style.maxHeight = submenu.scrollHeight + 'px';
        }
      }
    }
  });
}

/**
 * Initialize submenu functionality
 */
function initializeSubmenuToggles() {
  const submenuItems = document.querySelectorAll('.has-submenu > a');
  
  submenuItems.forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      const parent = this.parentElement;
      
      // Close other open submenus
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
      
      // Toggle current submenu
      parent.classList.toggle('open');
      const submenu = parent.querySelector('.submenu');
      
      if (parent.classList.contains('open')) {
        submenu.style.maxHeight = submenu.scrollHeight + 'px';
      } else {
        submenu.style.maxHeight = null;
      }
    });
  });
}

/**
 * Initialize dropdown menus
 */
function initializeDropdowns() {
  const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
  
  dropdownToggles.forEach(toggle => {
    toggle.addEventListener('click', function(e) {
      e.stopPropagation();
      
      const dropdown = this.nextElementSibling;
      dropdown.classList.toggle('show');
      
      // Close other dropdowns
      dropdownToggles.forEach(otherToggle => {
        if (otherToggle !== toggle) {
          const otherDropdown = otherToggle.nextElementSibling;
          if (otherDropdown && otherDropdown.classList.contains('show')) {
            otherDropdown.classList.remove('show');
          }
        }
      });
    });
  });
  
  // Close dropdowns when clicking outside
  document.addEventListener('click', function() {
    dropdownToggles.forEach(toggle => {
      const dropdown = toggle.nextElementSibling;
      if (dropdown && dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
      }
    });
  });
}

/**
 * Initialize modal functionality
 */
function initializeModals() {
  const modalTriggers = document.querySelectorAll('[data-modal]');
  const closeButtons = document.querySelectorAll('.close-modal');
  
  modalTriggers.forEach(trigger => {
    trigger.addEventListener('click', function(e) {
      e.preventDefault();
      
      const modalId = this.getAttribute('data-modal');
      const modal = document.getElementById(modalId);
      
      if (modal) {
        modal.style.display = 'flex';
      }
    });
  });
  
  closeButtons.forEach(button => {
    button.addEventListener('click', function() {
      const modal = this.closest('.modal');
      modal.style.display = 'none';
    });
  });
  
  // Close modal when clicking outside content
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
      e.target.style.display = 'none';
    }
  });
}

/**
 * Set current date in dashboard header
 */
function setCurrentDate() {
  const dateDisplay = document.querySelector('.date-display');
  if (dateDisplay) {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    dateDisplay.querySelector('span').textContent = now.toLocaleDateString('en-US', options);
  }
}

/**
 * Initialize data tables with sorting and pagination
 */
function initializeDataTables() {
  const tables = document.querySelectorAll('.admin-table');
  
  tables.forEach(table => {
    if (!table.classList.contains('no-sort')) {
      const headers = table.querySelectorAll('th');
      
      headers.forEach(header => {
        if (!header.classList.contains('no-sort')) {
          header.addEventListener('click', function() {
            const index = Array.from(headers).indexOf(this);
            sortTable(table, index);
          });
          
          // Add sort indicator
          header.style.position = 'relative';
          header.style.cursor = 'pointer';
          const sortIndicator = document.createElement('span');
          sortIndicator.className = 'sort-indicator';
          sortIndicator.innerHTML = ' ↕';
          header.appendChild(sortIndicator);
        }
      });
    }
  });
}

/**
 * Sort table by column
 */
function sortTable(table, columnIndex) {
  const tbody = table.querySelector('tbody');
  const rows = Array.from(tbody.querySelectorAll('tr'));
  const headers = table.querySelectorAll('th');
  const header = headers[columnIndex];
  
  // Determine sort direction
  const currentDir = header.getAttribute('data-sort') || 'asc';
  const newDir = currentDir === 'asc' ? 'desc' : 'asc';
  
  // Reset all headers
  headers.forEach(h => {
    h.setAttribute('data-sort', '');
    h.querySelector('.sort-indicator').innerHTML = ' ↕';
  });
  
  // Set new sort direction
  header.setAttribute('data-sort', newDir);
  header.querySelector('.sort-indicator').innerHTML = newDir === 'asc' ? ' ↑' : ' ↓';
  
  // Sort rows
  rows.sort((a, b) => {
    const cellA = a.querySelectorAll('td')[columnIndex].textContent.trim();
    const cellB = b.querySelectorAll('td')[columnIndex].textContent.trim();
    
    if (isNaN(cellA) || isNaN(cellB)) {
      // Sort as strings
      return newDir === 'asc' 
        ? cellA.localeCompare(cellB) 
        : cellB.localeCompare(cellA);
    } else {
      // Sort as numbers
      return newDir === 'asc' 
        ? Number(cellA) - Number(cellB) 
        : Number(cellB) - Number(cellA);
    }
  });
  
  // Re-append sorted rows
  rows.forEach(row => tbody.appendChild(row));
}

/**
 * Initialize charts on dashboard
 */
function initializeCharts() {
  // Check if Chart.js is available
  if (typeof Chart === 'undefined') {
    return;
  }
  
  // Traffic chart
  const trafficChartEl = document.getElementById('traffic-chart');
  if (trafficChartEl) {
    const ctx = trafficChartEl.getContext('2d');
    
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
          label: 'Website Traffic',
          data: [1500, 1800, 2200, 1900, 2400, 2800, 2600, 2950, 3200, 3500, 3800, 4100],
          backgroundColor: 'rgba(6, 39, 103, 0.1)',
          borderColor: '#062767',
          borderWidth: 2,
          pointBackgroundColor: '#062767',
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
  }
  
  // Inquiries chart
  const inquiriesChartEl = document.getElementById('inquiries-chart');
  if (inquiriesChartEl) {
    const ctx = inquiriesChartEl.getContext('2d');
    
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Business', 'Insurance', 'Health', 'Finance', 'Other'],
        datasets: [{
          label: 'Inquiries by Category',
          data: [45, 32, 18, 27, 10],
          backgroundColor: [
            '#1e3a8a',
            '#3a5ca2',
            '#b19763',
            '#1cc88a',
            '#858796'
          ],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        }
      }
    });
  }
}

/**
 * Initialize form validations
 */
function initializeFormValidations() {
  const forms = document.querySelectorAll('form:not(.no-validate)');
  
  forms.forEach(form => {
    form.addEventListener('submit', function(e) {
      if (!validateForm(this)) {
        e.preventDefault();
      }
    });
    
    // Add blur event to validate fields as user leaves them
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
      input.addEventListener('blur', function() {
        validateField(this);
      });
    });
  });
}

/**
 * Validate a form
 */
function validateForm(form) {
  let isValid = true;
  
  // Validate all required fields
  const requiredInputs = form.querySelectorAll('input[required], select[required], textarea[required]');
  requiredInputs.forEach(input => {
    if (!validateField(input)) {
      isValid = false;
    }
  });
  
  // Validate emails
  const emailInputs = form.querySelectorAll('input[type="email"]');
  emailInputs.forEach(input => {
    if (input.value.trim() !== '' && !validateEmail(input.value)) {
      showError(input, 'Please enter a valid email address');
      isValid = false;
    }
  });
  
  // Validate password match if applicable
  const password = form.querySelector('input[name="password"]');
  const confirmPassword = form.querySelector('input[name="confirm_password"]');
  
  if (password && confirmPassword && password.value !== confirmPassword.value) {
    showError(confirmPassword, 'Passwords do not match');
    isValid = false;
  }
  
  return isValid;
}

/**
 * Validate a single field
 */
function validateField(field) {
  removeError(field);
  
  // Check if field is required and empty
  if (field.hasAttribute('required') && field.value.trim() === '') {
    showError(field, 'This field is required');
    return false;
  }
  
  // Check if email is valid
  if (field.type === 'email' && field.value.trim() !== '' && !validateEmail(field.value)) {
    showError(field, 'Please enter a valid email address');
    return false;
  }
  
  return true;
}

/**
 * Show error message for a field
 */
function showError(field, message) {
  removeError(field);
  
  // Add error class to field
  field.classList.add('input-error');
  
  // Create and insert error message
  const errorElement = document.createElement('div');
  errorElement.className = 'error-message';
  errorElement.textContent = message;
  
  // Insert after the field
  field.parentNode.insertBefore(errorElement, field.nextSibling);
}

/**
 * Remove error message for a field
 */
function removeError(field) {
  field.classList.remove('input-error');
  
  // Find and remove error message
  const parent = field.parentNode;
  const errorElement = parent.querySelector('.error-message');
  
  if (errorElement) {
    parent.removeChild(errorElement);
  }
}

/**
 * Validate email format
 */
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

/**
 * Handle AJAX form submissions
 */
function handleAjaxForm(formId, successCallback, errorCallback) {
  const form = document.getElementById(formId);
  
  if (!form) return;
  
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!validateForm(form)) {
      return;
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    submitBtn.disabled = true;
    
    // Get form data
    const formData = new FormData(form);
    
    // Convert to JSON for API
    const data = {};
    formData.forEach((value, key) => {
      data[key] = value;
    });
    
    // Send data to server
    fetch(form.action, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + localStorage.getItem('authToken')
      },
      body: JSON.stringify(data)
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      // Reset form
      form.reset();
      
      // Reset button
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
      
      // Call success callback
      if (typeof successCallback === 'function') {
        successCallback(data);
      }
    })
    .catch(error => {
      // Reset button
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
      
      // Call error callback
      if (typeof errorCallback === 'function') {
        errorCallback(error);
      } else {
        console.error('Error:', error);
        
        // Show generic error message
        const formError = document.createElement('div');
        formError.className = 'form-error-message';
        formError.textContent = 'An error occurred. Please try again.';
        
        // Remove existing error
        const existingError = form.querySelector('.form-error-message');
        if (existingError) {
          form.removeChild(existingError);
        }
        
        form.prepend(formError);
      }
    });
  });
}

/**
 * Handle file uploads with preview
 */
function handleFileUpload(inputId, previewId) {
  const fileInput = document.getElementById(inputId);
  const preview = document.getElementById(previewId);
  
  if (!fileInput || !preview) return;
  
  fileInput.addEventListener('change', function() {
    const file = this.files[0];
    
    if (file) {
      const reader = new FileReader();
      
      reader.addEventListener('load', function() {
        // Check if preview is an img element
        if (preview.tagName === 'IMG') {
          preview.src = reader.result;
          preview.style.display = 'block';
        } else {
          // If it's a container
          const imgPreview = preview.querySelector('img');
          
          if (imgPreview) {
            imgPreview.src = reader.result;
            imgPreview.style.display = 'block';
          } else {
            const img = document.createElement('img');
            img.src = reader.result;
            img.style.width = '100%';
            img.style.height = 'auto';
            preview.innerHTML = '';
            preview.appendChild(img);
          }
          
          // Hide placeholder if exists
          const placeholder = preview.querySelector('.upload-placeholder');
          if (placeholder) {
            placeholder.style.display = 'none';
          }
        }
      });
      
      reader.readAsDataURL(file);
    }
  });
}

/**
 * Create dynamic slug from title input
 */
function createSlugFromTitle(titleInputId, slugInputId) {
  const titleInput = document.getElementById(titleInputId);
  const slugInput = document.getElementById(slugInputId);
  
  if (!titleInput || !slugInput) return;
  
  titleInput.addEventListener('input', function() {
    const slug = this.value
      .toLowerCase()
      .replace(/[^\w\s]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim();
    
    slugInput.value = slug;
    
    // If the slug is editable content
    if (slugInput.getAttribute('contenteditable') === 'true') {
      slugInput.textContent = slug;
    }
  });
}

/**
 * Handle logout functionality
 */
function handleLogout() {
  const logoutButtons = document.querySelectorAll('.logout-btn');
  
  logoutButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Clear auth token and other stored data
      localStorage.removeItem('authToken');
      localStorage.removeItem('adminLoggedIn');
      localStorage.removeItem('clientLoggedIn');
      localStorage.removeItem('clientUsername');
      
      // Redirect to login page
      window.location.href = 'login.html';
    });
  });
}

// Call logout handler on page load
handleLogout();