/**
 * Backsure Global Support Admin Dashboard
 * Enhanced JavaScript functionality
 * Version: 1.0
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize all dashboard components
  initSidebar();
  initDropdowns();
  initDateDisplay();
  initCharts();
  initTableActions();
  initTooltips();
  initResponsiveHandling();
  
  // Add animation classes after initial load
  setTimeout(function() {
    document.querySelectorAll('.stat-card').forEach((card, index) => {
      setTimeout(() => {
        card.classList.add('animated');
      }, index * 100);
    });
  }, 300);
});

/**
 * Sidebar functionality
 */
function initSidebar() {
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const adminContainer = document.querySelector('.admin-container');
  const sidebar = document.querySelector('.admin-sidebar');
  
  // Create backdrop element for mobile
  const backdrop = document.createElement('div');
  backdrop.className = 'sidebar-backdrop';
  document.body.appendChild(backdrop);
  
  // Toggle sidebar
  sidebarToggle.addEventListener('click', function() {
    adminContainer.classList.toggle('sidebar-collapsed');
    adminContainer.classList.toggle('sidebar-active');
  });
  
  // Close sidebar when clicking backdrop
  backdrop.addEventListener('click', function() {
    adminContainer.classList.remove('sidebar-active');
  });
  
  // Submenu toggle functionality
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
  
  // Add active class to current page link
  const currentPage = window.location.pathname.split('/').pop();
  const navLinks = document.querySelectorAll('.sidebar-nav a');
  
  navLinks.forEach(link => {
    const href = link.getAttribute('href');
    if (href === currentPage || (currentPage === '' && href === 'admin-dashboard.html')) {
      link.parentElement.classList.add('active');
      
      // Open parent submenu if in submenu
      const parentSubmenu = link.closest('.submenu');
      if (parentSubmenu) {
        const parentItem = parentSubmenu.parentElement;
        parentItem.classList.add('open');
        parentSubmenu.style.maxHeight = parentSubmenu.scrollHeight + 'px';
      }
    }
  });
}

/**
 * Dropdown menu functionality
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
  
  // Header action dropdowns (notifications, tasks)
  const actionButtons = document.querySelectorAll('.action-btn');
  
  actionButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.stopPropagation();
      const dropdown = this.querySelector('.dropdown-menu');
      if (dropdown) {
        dropdown.classList.toggle('show');
      }
    });
  });
}

/**
 * Current date display
 */
function initDateDisplay() {
  const dateElement = document.getElementById('current-date');
  if (dateElement) {
    const now = new Date();
    dateElement.textContent = now.toLocaleDateString('en-US', { 
      weekday: 'long', 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
  }
}

/**
 * Charts initialization
 */
function initCharts() {
  // Traffic chart
  const trafficChartElement = document.getElementById('traffic-chart');
  if (trafficChartElement) {
    const ctx = trafficChartElement.getContext('2d');
    
    // Create gradient for chart background
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(78, 115, 223, 0.1)');
    gradient.addColorStop(1, 'rgba(78, 115, 223, 0)');
    
    const trafficChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Visitors',
          data: [320, 420, 395, 450, 380, 285, 310],
          backgroundColor: gradient,
          borderColor: 'rgba(78, 115, 223, 1)',
          pointBackgroundColor: 'rgba(78, 115, 223, 1)',
          pointBorderColor: '#fff',
          pointHoverBackgroundColor: '#fff',
          pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
          borderWidth: 3,
          tension: 0.3,
          fill: true
        }]
      },
      options: {
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            titleColor: '#333',
            bodyColor: '#666',
            borderColor: 'rgba(0, 0, 0, 0.1)',
            borderWidth: 1,
            padding: 10,
            displayColors: false,
            callbacks: {
              label: function(context) {
                return `Visitors: ${context.raw}`;
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            },
            ticks: {
              precision: 0
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        },
        interaction: {
          mode: 'nearest',
          axis: 'x',
          intersect: false
        },
        elements: {
          point: {
            radius: 4,
            hoverRadius: 6
          }
        }
      }
    });
    
    // Add chart period controls functionality
    const chartControls = document.querySelectorAll('.chart-controls button');
    if (chartControls.length > 0) {
      chartControls.forEach(button => {
        button.addEventListener('click', function() {
          // Remove active class from all buttons
          chartControls.forEach(btn => btn.classList.remove('active'));
          
          // Add active class to clicked button
          this.classList.add('active');
          
          // Update chart data based on selected period
          let newData;
          const period = this.textContent.trim().toLowerCase();
          
          if (period === 'weekly') {
            newData = [320, 420, 395, 450, 380, 285, 310];
          } else if (period === 'monthly') {
            newData = [1250, 1380, 1520, 1350, 1410, 1280, 1390, 1450, 1600, 1580, 1620, 1750];
          } else if (period === 'yearly') {
            newData = [12500, 15800, 18200, 21500, 24300];
          }
          
          // Update chart labels based on period
          if (period === 'weekly') {
            trafficChart.data.labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
          } else if (period === 'monthly') {
            trafficChart.data.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
          } else if (period === 'yearly') {
            trafficChart.data.labels = ['2021', '2022', '2023', '2024', '2025'];
          }
          
          // Update chart data
          trafficChart.data.datasets[0].data = newData;
          trafficChart.update();
        });
      });
    }
  }
}

/**
 * Table actions initialization
 */
function initTableActions() {
  // Table row actions
  const tableActions = document.querySelectorAll('.table-actions a');
  
  tableActions.forEach(action => {
    action.addEventListener('click', function(e) {
      if (this.classList.contains('delete-btn')) {
        e.preventDefault();
        
        if (confirm('Are you sure you want to delete this item?')) {
          const row = this.closest('tr');
          row.style.backgroundColor = '#ffeeee';
          
          setTimeout(() => {
            row.style.opacity = '0';
            row.style.height = '0';
            row.style.overflow = 'hidden';
            
            setTimeout(() => {
              row.remove();
            }, 300);
          }, 300);
        }
      }
    });
  });
}

/**
 * Tooltips functionality
 */
function initTooltips() {
  const tooltipElements = document.querySelectorAll('[title]');
  
  tooltipElements.forEach(element => {
    element.addEventListener('mouseenter', function() {
      const title = this.getAttribute('title');
      this.setAttribute('data-title', title);
      this.removeAttribute('title');
      
      const tooltip = document.createElement('div');
      tooltip.className = 'tooltip';
      tooltip.textContent = title;
      
      document.body.appendChild(tooltip);
      
      const rect = this.getBoundingClientRect();
      const tooltipRect = tooltip.getBoundingClientRect();
      
      tooltip.style.top = `${rect.top - tooltipRect.height - 10 + window.scrollY}px`;
      tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltipRect.width / 2)}px`;
      tooltip.style.opacity = '1';
      
      this.addEventListener('mouseleave', function once() {
        tooltip.remove();
        this.setAttribute('title', this.getAttribute('data-title'));
        this.removeAttribute('data-title');
        this.removeEventListener('mouseleave', once);
      });
    });
  });
}

/**
 * Responsive handling
 */
function initResponsiveHandling() {
  // Adjust for mobile screens
  const handleResize = () => {
    const adminContainer = document.querySelector('.admin-container');
    
    if (window.innerWidth < 992) {
      adminContainer.classList.remove('sidebar-collapsed');
    }
  };
  
  // Initial call and event listener
  handleResize();
  window.addEventListener('resize', handleResize);
  
  // Add double-tap protection for mobile devices
  const navigationLinks = document.querySelectorAll('.sidebar-nav a');
  
  navigationLinks.forEach(link => {
    if (link.nextElementSibling && link.nextElementSibling.classList.contains('submenu')) {
      link.addEventListener('touchend', function(e) {
        const parent = this.parentElement;
        
        if (!parent.classList.contains('mobile-tapped')) {
          e.preventDefault();
          
          // Remove class from other items
          document.querySelectorAll('.has-submenu').forEach(item => {
            if (item !== parent) {
              item.classList.remove('mobile-tapped');
            }
          });
          
          parent.classList.add('mobile-tapped');
          
          // Remove class after delay
          setTimeout(() => {
            parent.classList.remove('mobile-tapped');
          }, 3000);
        }
      });
    }
  });
}

/**
 * Add smooth transition animations for dashboard elements
 */
function animateDashboardElements() {
  // Add animation classes to elements with delay
  const animateElement = (selector, delay = 0, duration = 500) => {
    const elements = document.querySelectorAll(selector);
    elements.forEach((element, index) => {
      element.style.opacity = '0';
      element.style.transform = 'translateY(20px)';
      element.style.transition = `opacity ${duration}ms ease, transform ${duration}ms ease`;
      
      setTimeout(() => {
        element.style.opacity = '1';
        element.style.transform = 'translateY(0)';
      }, delay + (index * 100));
    });
  };
  
  // Animate various dashboard components
  animateElement('.stat-card', 100);
  animateElement('.chart-container', 300);
  animateElement('.widget', 400);
  animateElement('.activity-item', 500);
  animateElement('.quick-action-btn', 600);
  animateElement('.admin-table', 700);
}

/**
 * Data refreshing simulation
 */
function initDataRefreshing() {
  // Simulate real-time updates for dashboard data
  setInterval(() => {
    // Update visitor count with small random changes
    document.querySelectorAll('.stat-value').forEach(value => {
      const currentValue = parseInt(value.textContent.replace(/,/g, ''));
      const change = Math.floor(Math.random() * 5) - 2; // Random change between -2 and +2
      
      if (!isNaN(currentValue)) {
        value.textContent = (currentValue + change).toLocaleString();
      }
    });
  }, 30000); // Update every 30 seconds
}

// Call after DOM loaded
window.addEventListener('load', function() {
  animateDashboardElements();
  initDataRefreshing();
});
