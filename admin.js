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
  
  // Initialize submenu toggles
  initializeSubmenuToggles();
  
  // Initialize any charts on the dashboard
  initializeCharts();
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
    // For demo purposes, we'll just log instead of redirecting
    console.log('Authentication check: User would be redirected to login page');
    // window.location.href = 'admin-login.html';
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
  const dateDisplay = document.getElementById('current-date');
  if (dateDisplay) {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    dateDisplay.textContent = now.toLocaleDateString('en-US', options);
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
    const indicator = h.querySelector('.sort-indicator');
    if (indicator) indicator.innerHTML = ' ↕';
  });
  
  // Set new sort direction
  header.setAttribute('data-sort', newDir);
  const indicator = header.querySelector('.sort-indicator');
  if (indicator) indicator.innerHTML = newDir === 'asc' ? ' ↑' : ' ↓';
  
  // Sort rows
  rows.sort((a, b) => {
    const cellA = a.querySelectorAll('td')[columnIndex].textContent.trim();
    const cellB = b.querySelectorAll('td')[columnIndex].textContent.trim();
    
    if (!isNaN(parseFloat(cellA)) && !isNaN(parseFloat(cellB))) {
      // Sort as numbers
      return newDir === 'asc' 
        ? parseFloat(cellA) - parseFloat(cellB) 
        : parseFloat(cellB) - parseFloat(cellA);
    } else {
      // Sort as strings
      return newDir === 'asc' 
        ? cellA.localeCompare(cellB) 
        : cellB.localeCompare(cellA);
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
    console.warn('Chart.js not loaded. Charts will not be displayed.');
    return;
  }
  
  // Traffic chart
  const trafficChartEl = document.getElementById('traffic-chart');
  if (trafficChartEl) {
    const ctx = trafficChartEl.getContext('2d');
    
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Visitors',
          data: [320, 420, 395, 450, 380, 285, 310],
          backgroundColor: 'rgba(6, 39, 103, 0.1)',
          borderColor: '#062767',
          borderWidth: 2,
          pointBackgroundColor: '#062767',
          pointBorderColor: '#fff',
          pointHoverBackgroundColor: '#fff',
          pointHoverBorderColor: '#062767',
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
  
  // Other charts initialization can be added here
}

/**
 * Handle logout functionality
 */
function handleLogout() {
  const logoutLinks = document.querySelectorAll('a[href*="logout"]');
  
  logoutLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Clear auth token and other stored data
      localStorage.removeItem('authToken');
      localStorage.removeItem('adminLoggedIn');
      
      // Redirect to login page
      window.location.href = 'admin-login.html';
    });
  });
}

// Initialize logout handler
handleLogout();
