document.addEventListener('DOMContentLoaded', function() {
  // Initialize dashboard tabs
  initDashboardTabs();
  
  // Initialize charts if Chart.js is loaded
  if (typeof Chart !== 'undefined') {
    initActivityChart();
    initContentChart();
    initUserActionsChart();
  } else {
    console.error('Chart.js is not loaded. Charts will not be displayed.');
  }
  
  // Log dashboard view
  logPageView('admin-dashboard.php');
});

// Dashboard tabs functionality
function initDashboardTabs() {
  const tabItems = document.querySelectorAll('.tab-item');
  const tabContents = document.querySelectorAll('.tab-content');
  
  tabItems.forEach(tab => {
    tab.addEventListener('click', () => {
      const target = tab.getAttribute('data-tab');
      
      // Update active tab
      tabItems.forEach(item => item.classList.remove('active'));
      tab.classList.add('active');
      
      // Update active content
      tabContents.forEach(content => content.classList.remove('active'));
      document.querySelector(`.tab-content[data-tab="${target}"]`).classList.add('active');
    });
  });
}

// Activity overview chart
function initActivityChart() {
  const ctx = document.getElementById('activityChart');
  if (!ctx) return;
  
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
      datasets: [
        {
          label: 'Page Views',
          data: [3200, 4100, 3800, 5200, 4800, 6000, 6700],
          borderColor: getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim(),
          backgroundColor: 'rgba(0, 123, 255, 0.1)',
          tension: 0.3,
          fill: true
        },
        {
          label: 'Unique Visitors',
          data: [2100, 2900, 2600, 3900, 3200, 4600, 5100],
          borderColor: getComputedStyle(document.documentElement).getPropertyValue('--secondary-color').trim(),
          backgroundColor: 'rgba(23, 162, 184, 0.1)',
          tension: 0.3,
          fill: true
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
        tooltip: {
          mode: 'index',
          intersect: false,
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
}

// Content distribution chart
function initContentChart() {
  const ctx = document.getElementById('contentChart');
  if (!ctx) return;
  
  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Blog Posts', 'Services', 'Testimonials', 'FAQ Items', 'Solutions'],
      datasets: [{
        data: [35, 20, 15, 10, 20],
        backgroundColor: [
          '#3b82f6', // blue
          '#10b981', // green
          '#f59e0b', // yellow
          '#ef4444', // red
          '#8b5cf6'  // purple
        ],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'right',
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              let label = context.label || '';
              if (label) {
                label += ': ';
              }
              label += context.parsed + '%';
              return label;
            }
          }
        }
      }
    }
  });
}

// User actions chart
function initUserActionsChart() {
  const ctx = document.getElementById('userActionsChart');
  if (!ctx) return;
  
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Inquiries', 'Subscriptions', 'Comments', 'Contact Form', 'Downloads'],
      datasets: [{
        label: 'Last Month',
        data: [65, 82, 45, 90, 38],
        backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim(),
      },
      {
        label: 'Current Month',
        data: [85, 75, 56, 102, 42],
        backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--secondary-color').trim(),
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
}

// Log page view for analytics
function logPageView(page) {
  // This would typically make an AJAX call to track the page view
  console.log('Page view logged:', page);
  // In a real implementation, you'd send this data to your server
  // Example: fetch('admin-analytics.php', { method: 'POST', body: JSON.stringify({ page }) });
}
