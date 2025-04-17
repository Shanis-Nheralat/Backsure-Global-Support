/**
 * Backsure Global Support - Admin Dashboard JavaScript
 * This file handles all the functionality for the admin dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize sidebar functionality
  initializeSidebar();
  
  // Initialize dropdown menus
  initializeDropdowns();
  
  // Set current date in dashboard
  setCurrentDate();
  
  // Initialize any charts on the dashboard
  initializeCharts();
  
  // Check if we're on the login page
  const isLoginPage = window.location.href.includes('admin-login.html');
  
  // Only check authentication on non-login pages
  if (!isLoginPage) {
    checkAuthentication();
  }
});

/**
 * Initialize sidebar functionality
 */
function initializeSidebar() {
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const adminContainer = document.querySelector('.admin-container');
  const adminSidebar = document.querySelector('.admin-sidebar');
  
  if (sidebarToggle && adminContainer) {
    sidebarToggle.addEventListener('click', function() {
      // For mobile
      if (window.innerWidth <= 992) {
        adminSidebar.classList.toggle('active');
        
        // Create backdrop for mobile if it doesn't exist
        let backdrop = document.querySelector('.sidebar-backdrop');
        if (!backdrop) {
          backdrop = document.createElement('div');
          backdrop.className = 'sidebar-backdrop';
          document.body.appendChild(backdrop);
          
          backdrop.addEventListener('click', function() {
            adminSidebar.classList.remove('active');
            this.style.display = 'none';
          });
        }
        
        backdrop.style.display = adminSidebar.classList.contains('active') ? 'block' : 'none';
      } else {
        // For desktop
        adminContainer.classList.toggle('sidebar-collapsed');
        
        // Save state to localStorage
        const isCollapsed = adminContainer.classList.contains('sidebar-collapsed');
        localStorage.setItem('sidebar-collapsed', isCollapsed);
      }
    });
    
    // Check saved state
    const savedState = localStorage.getItem('sidebar-collapsed');
    if (savedState === 'true' && window.innerWidth > 992) {
      adminContainer.classList.add('sidebar-collapsed');
    }
  }
  
  // Initialize submenu toggles
  initializeSubmenuToggles();
  
  // Highlight the current page in the navigation
  highlightCurrentPage();
}

/**
 * Initialize submenu toggle functionality
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
      
      if (submenu) {
        if (parent.classList.contains('open')) {
          submenu.style.maxHeight = submenu.scrollHeight + 'px';
        } else {
          submenu.style.maxHeight = null;
        }
      }
    });
  });
}

/**
 * Highlight the current page in the navigation
 */
function highlightCurrentPage() {
  const currentPath = window.location.pathname;
  const currentPage = currentPath.substring(currentPath.lastIndexOf('/') + 1);
  
  if (currentPage) {
    const menuLinks = document.querySelectorAll('.sidebar-nav a');
    
    menuLinks.forEach(link => {
      const href = link.getAttribute('href');
      
      if (href === currentPage) {
        // Remove 'active' class from all items
        menuLinks.forEach(item => {
          if (item.parentElement.classList.contains('active')) {
            item.parentElement.classList.remove('active');
          }
        });
        
        // Add 'active' class to current item
        link.parentElement.classList.add('active');
        
        // If in submenu, open the parent menu
        const parentMenu = link.closest('.submenu');
        if (parentMenu) {
          const parentLi = parentMenu.parentElement;
          parentLi.classList.add('open');
          parentMenu.style.maxHeight = parentMenu.scrollHeight + 'px';
        }
      }
    });
  }
}

/**
 * Initialize dropdown menus
 */
function initializeDropdowns() {
  const userDropdownToggle = document.getElementById('user-dropdown-toggle');
  const userDropdown = document.getElementById('user-dropdown');
  
  if (userDropdownToggle && userDropdown) {
    userDropdownToggle.addEventListener('click', function(e) {
      e.stopPropagation();
      userDropdown.classList.toggle('show');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (userDropdown.classList.contains('show') && 
          !userDropdownToggle.contains(e.target) && 
          !userDropdown.contains(e.target)) {
        userDropdown.classList.remove('show');
      }
    });
  }
}

/**
 * Set current date in dashboard
 */
function setCurrentDate() {
  const dateElement = document.getElementById('current-date');
  
  if (dateElement) {
    const now = new Date();
    const options = { 
      weekday: 'long', 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    };
    
    dateElement.textContent = now.toLocaleDateString('en-US', options);
  }
}

/**
 * Initialize charts on dashboard
 */
function initializeCharts() {
  const trafficChartElement = document.getElementById('traffic-chart');
  
  if (trafficChartElement && window.Chart) {
    const ctx = trafficChartElement.getContext('2d');
    
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Visitors',
          data: [320, 420, 395, 450, 380, 285, 310],
          backgroundColor: 'rgba(78, 115, 223, 0.05)',
          borderColor: 'rgba(78, 115, 223, 1)',
          pointBackgroundColor: 'rgba(78, 115, 223, 1)',
          pointBorderColor: '#fff',
          pointHoverBackgroundColor: '#fff',
          pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
          borderWidth: 2,
          tension: 0.3,
          fill: true
        }]
      },
      options: {
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
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
    
    // Set up chart control buttons
    const chartControls = document.querySelectorAll('.chart-controls button');
    
    if (chartControls.length) {
      chartControls.forEach(button => {
        button.addEventListener('click', function() {
          // Remove active class from all buttons
          chartControls.forEach(btn => btn.classList.remove('active'));
          // Add active class to clicked button
          this.classList.add('active');
          
          // In a real application, this would fetch new data based on the selected time period
          // For this demo, we'll just log the action
          console.log('Chart period changed to:', this.textContent.trim());
        });
      });
    }
  }
}

/**
 * Check if the user is authenticated
 */
function checkAuthentication() {
  // In a real app, this would check for a valid auth token
  // For this demo, we'll simulate authenticated state
  const isAuthenticated = localStorage.getItem('authenticated') === 'true';
  
  // Skip authentication for this demo, but in a real app:
  // if (!isAuthenticated) {
  //   window.location.href = 'admin-login.html';
  // }
}

/**
 * Initialize table sorting functionality
 */
function initializeTableSorting() {
  const tables = document.querySelectorAll('.admin-table.sortable');
  
  tables.forEach(table => {
    const headers = table.querySelectorAll('th.sortable');
    
    headers.forEach(header => {
      header.addEventListener('click', function() {
        const index = Array.from(header.parentElement.children).indexOf(header);
        sortTable(table, index);
      });
    });
  });
}

/**
 * Sort a table by a specific column
 */
function sortTable(table, columnIndex) {
  const tbody = table.querySelector('tbody');
  const rows = Array.from(tbody.querySelectorAll('tr'));
  const header = table.querySelector(`th:nth-child(${columnIndex + 1})`);
  
  // Determine sort direction
  const currentDir = header.getAttribute('data-sort') || 'asc';
  const newDir = currentDir === 'asc' ? 'desc' : 'asc';
  
  // Update header attributes
  const headers = table.querySelectorAll('th');
  headers.forEach(h => h.removeAttribute('data-sort'));
  header.setAttribute('data-sort', newDir);
  
  // Sort the rows
  rows.sort((a, b) => {
    const aValue = a.children[columnIndex].textContent.trim();
    const bValue = b.children[columnIndex].textContent.trim();
    
    // If numeric values
    if (!isNaN(aValue) && !isNaN(bValue)) {
      return newDir === 'asc' 
        ? Number(aValue) - Number(bValue) 
        : Number(bValue) - Number(aValue);
    }
    
    // For dates
    const aDate = new Date(aValue);
    const bDate = new Date(bValue);
    if (!isNaN(aDate.getTime()) && !isNaN(bDate.getTime())) {
      return newDir === 'asc' 
        ? aDate - bDate 
        : bDate - aDate;
    }
    
    // For text
    return newDir === 'asc' 
      ? aValue.localeCompare(bValue) 
      : bValue.localeCompare(aValue);
  });
  
  // Reappend the sorted rows
  rows.forEach(row => tbody.appendChild(row));
}

/**
 * Add a notification message
 */
function showNotification(message, type = 'info') {
  // Create notification element
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
  
  // Initialize close button
  const closeButton = notification.querySelector('.notification-close');
  closeButton.addEventListener('click', function() {
    document.body.removeChild(notification);
  });
  
  // Auto remove after 5 seconds
  setTimeout(() => {
    if (document.body.contains(notification)) {
      document.body.removeChild(notification);
    }
  }, 5000);
}

/**
 * Handle form validation
 */
function validateForm(form) {
  const requiredFields = form.querySelectorAll('[required]');
  let valid = true;
  
  // Remove all existing error messages
  form.querySelectorAll('.error-message').forEach(el => el.remove());
  
  // Check each required field
  requiredFields.forEach(field => {
    if (!field.value.trim()) {
      valid = false;
      showFieldError(field, 'This field is required');
    }
  });
  
  // Check email fields
  const emailFields = form.querySelectorAll('input[type="email"]');
  emailFields.forEach(field => {
    if (field.value.trim() && !isValidEmail(field.value)) {
      valid = false;
      showFieldError(field, 'Please enter a valid email address');
    }
  });
  
  return valid;
}

/**
 * Show error message for a form field
 */
function showFieldError(field, message) {
  // Add error class to field
  field.classList.add('error');
  
  // Create error message element
  const errorMessage = document.createElement('div');
  errorMessage.className = 'error-message';
  errorMessage.textContent = message;
  
  // Insert after the field
  field.parentNode.insertBefore(errorMessage, field.nextSibling);
  
  // Remove error class when field is focused
  field.addEventListener('focus', function() {
    this.classList.remove('error');
    if (errorMessage.parentNode) {
      errorMessage.parentNode.removeChild(errorMessage);
    }
  }, { once: true });
}

/**
 * Validate email format
 */
function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
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
        if (preview.tagName === 'IMG') {
          preview.src = reader.result;
          preview.style.display = 'block';
        } else {
          // For a container
          const img = preview.querySelector('img') || document.createElement('img');
          img.src = reader.result;
          img.style.width = '100%';
          img.style.height = 'auto';
          
          if (!img.parentNode) {
            preview.innerHTML = '';
            preview.appendChild(img);
          }
        }
      });
      
      reader.readAsDataURL(file);
    }
  });
}

/**
 * Simulate login functionality
 */
function simulateLogin(username, password, rememberMe) {
  // In a real app, this would send credentials to a server
  // For this demo, we'll accept any username/password
  
  if (username && password) {
    // Save authentication state
    localStorage.setItem('authenticated', 'true');
    localStorage.setItem('admin_username', username);
    
    if (rememberMe) {
      localStorage.setItem('remember_admin', 'true');
    } else {
      localStorage.removeItem('remember_admin');
    }
    
    return true;
  }
  
  return false;
}

/**
 * Simulate logout functionality
 */
function logout() {
  // Clear authentication state
  localStorage.removeItem('authenticated');
  localStorage.removeItem('admin_username');
  
  // Keep remember preference
  
  // Redirect to login page
  window.location.href = 'admin-login.html';
}

// Expose some functions globally for use in HTML event handlers
window.adminDashboard = {
  validateForm,
  simulateLogin,
  logout,
  showNotification
};
