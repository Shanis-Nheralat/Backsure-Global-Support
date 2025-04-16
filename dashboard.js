/**
 * dashboard.js
 * Backsure Global Support - Dashboard Specific JavaScript
 * Handles functionality specific to the admin dashboard overview
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize dashboard components
  initializeDashboardStats();
  initializeDashboardCharts();
  initializeRecentActivities();
  initializeQuickActions();
  
  // Set user info in header
  setUserInfo();
  
  // Set date range controls
  initializeDateRangeFilter();
  
  // Initialize inquiry table
  initializeInquiryTable();
});

/**
 * Set user information in the header
 */
function setUserInfo() {
  const userNameElement = document.querySelector('.user-name');
  const userRoleElement = document.querySelector('.user-role');
  const userAvatarElement = document.querySelector('.user-avatar');
  
  // Get user info from localStorage
  const userInfo = JSON.parse(localStorage.getItem('adminUser') || '{}');
  
  if (userNameElement && userInfo.name) {
    userNameElement.textContent = userInfo.name;
  }
  
  if (userRoleElement && userInfo.role) {
    userRoleElement.textContent = capitalizeFirstLetter(userInfo.role);
  }
  
  if (userAvatarElement && userInfo.name) {
    // Create initials from name
    const initials = userInfo.name
      .split(' ')
      .map(n => n[0])
      .join('')
      .toUpperCase();
    
    userAvatarElement.textContent = initials;
  }
}

/**
 * Capitalize first letter of a string
 */
function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

/**
 * Initialize dashboard statistics
 * In a real app, this would fetch data from the server
 */
function initializeDashboardStats() {
  // For demo purposes, we'll use static data
  // In a real app, this would be fetched from an API
  
  // Update visitor count with animation
  animateCounter('visitor-count', 1243);
  
  // Update inquiry count with animation
  animateCounter('inquiry-count', 28);
  
  // Update blog count with animation
  animateCounter('blog-count', 12);
  
  // Update services count with animation
  animateCounter('service-count', 8);
}

/**
 * Animate counter from 0 to target value
 */
function animateCounter(elementId, targetValue) {
  const element = document.getElementById(elementId);
  
  if (!element) return;
  
  let currentValue = 0;
  const duration = 1500; // ms
  const stepTime = 30; // ms
  const totalSteps = duration / stepTime;
  const stepValue = targetValue / totalSteps;
  
  const timer = setInterval(() => {
    currentValue += stepValue;
    
    if (currentValue >= targetValue) {
      element.textContent = targetValue;
      clearInterval(timer);
    } else {
      element.textContent = Math.floor(currentValue);
    }
  }, stepTime);
}

/**
 * Initialize dashboard charts
 */
function initializeDashboardCharts() {
  // Check if Chart.js is available
  if (typeof Chart === 'undefined') {
    console.warn('Chart.js is not loaded. Charts will not be displayed.');
    return;
  }
  
  // Initialize traffic chart
  initializeTrafficChart();
  
  // Initialize inquiry distribution chart
  initializeInquiryChart();
  
  // Initialize traffic sources chart
  initializeSourcesChart();
}

/**
 * Initialize website traffic chart
 */
function initializeTrafficChart() {
  const ctx = document.getElementById('traffic-chart');
  
  if (!ctx) return;
  
  // Sample data for the last 12 months
  const trafficData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [
      {
        label: 'Website Visits',
        data: [1500, 1800, 2200, 1900, 2400, 2800, 2600, 2950, 3200, 3500, 3800, 4100],
        borderColor: '#062767',
        backgroundColor: 'rgba(6, 39, 103, 0.1)',
        borderWidth: 2,
        fill: true,
        tension: 0.4
      }
    ]
  };
  
  const trafficChart = new Chart(ctx, {
    type: 'line',
    data: trafficData,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return value.toLocaleString();
            }
          }
        }
      },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return 'Visits: ' + context.raw.toLocaleString();
            }
          }
        }
      }
    }
  });
  
  // Handle chart filter buttons
  const chartFilterBtns = document.querySelectorAll('.chart-filter');
  
  if (chartFilterBtns.length) {
    chartFilterBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        // Remove active class from all buttons
        chartFilterBtns.forEach(b => b.classList.remove('active'));
        
        // Add active class to clicked button
        this.classList.add('active');
        
        // Get time period from data attribute
        const period = this.getAttribute('data-period');
        
        // Update chart data based on selected period
        updateTrafficChartData(trafficChart, period);
      });
    });
  }
}

/**
 * Update traffic chart data based on selected time period
 */
function updateTrafficChartData(chart, period) {
  // In a real app, this would fetch data from an API
  // For demo purposes, we'll generate different sets of data
  
  let labels, data;
  
  switch (period) {
    case 'week':
      labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
      data = [120, 145, 135, 170, 190, 110, 80];
      break;
    case 'month':
      labels = Array.from({ length: 30 }, (_, i) => i + 1);
      data = Array.from({ length: 30 }, () => Math.floor(Math.random() * 100) + 50);
      break;
    case 'quarter':
      labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
      data = [1500, 1800, 2200, 1900, 2400, 2800];
      break;
    case 'year':
    default:
      labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      data = [1500, 1800, 2200, 1900, 2400, 2800, 2600, 2950, 3200, 3500, 3800, 4100];
      break;
  }
  
  // Update chart data
  chart.data.labels = labels;
  chart.data.datasets[0].data = data;
  chart.update();
}

/**
 * Initialize inquiry distribution chart
 */
function initializeInquiryChart() {
  const ctx = document.getElementById('inquiries-chart');
  
  if (!ctx) return;
  
  const inquiryData = {
    labels: ['Business', 'Insurance', 'HR Services', 'Finance', 'Compliance', 'Other'],
    datasets: [
      {
        data: [35, 25, 15, 12, 8, 5],
        backgroundColor: [
          '#1e3a8a',  // Primary
          '#3a5ca2',  // Primary light
          '#b19763',  // Accent
          '#1cc88a',  // Success
          '#36b9cc',  // Info
          '#858796'   // Gray
        ],
        borderWidth: 0
      }
    ]
  };
  
  new Chart(ctx, {
    type: 'doughnut',
    data: inquiryData,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '70%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            padding: 20,
            boxWidth: 12
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return context.label + ': ' + context.raw + '%';
            }
          }
        }
      }
    }
  });
}

/**
 * Initialize traffic sources chart
 */
function initializeSourcesChart() {
  const ctx = document.getElementById('sources-chart');
  
  if (!ctx) return;
  
  const sourcesData = {
    labels: ['Direct', 'Search', 'Referral', 'Social', 'Email'],
    datasets: [
      {
        label: 'Traffic Sources',
        data: [45, 30, 15, 8, 2],
        backgroundColor: '#062767',
        borderWidth: 0
      }
    ]
  };
  
  new Chart(ctx, {
    type: 'bar',
    data: sourcesData,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      indexAxis: 'y',
      scales: {
        x: {
          beginAtZero: true,
          max: 50,
          ticks: {
            callback: function(value) {
              return value + '%';
            }
          }
        }
      },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return context.raw + '%';
            }
          }
        }
      }
    }
  });
}

/**
 * Initialize recent activities section
 * In a real app, this would fetch data from the server
 */
function initializeRecentActivities() {
  const activitiesContainer = document.querySelector('.activity-container');
  
  if (!activitiesContainer) return;
  
  // In a real app, these would be fetched from an API
  // For demo purposes, we'll use static data
  const recentActivities = [
    {
      type: 'inquiry',
      title: 'New inquiry received',
      description: 'John Smith submitted a new inquiry about Business Insurance',
      time: '10 minutes ago',
      action: 'View Inquiry'
    },
    {
      type: 'user',
      title: 'New user registered',
      description: 'Sarah Johnson created a new client account',
      time: '2 hours ago',
      action: 'View Profile'
    },
    {
      type: 'content',
      title: 'Blog post published',
      description: 'New article "5 Ways to Streamline Your Business Operations" is now live',
      time: 'Yesterday',
      action: 'View Post'
    },
    {
      type: 'testimonial',
      title: 'New testimonial added',
      description: 'Michael Lee from TechCorp submitted a positive testimonial',
      time: '2 days ago',
      action: 'Review'
    }
  ];
  
  // Clear any existing content
  activitiesContainer.innerHTML = '';
  
  // Add recent activities to container
  recentActivities.forEach(activity => {
    const activityItem = document.createElement('div');
    activityItem.className = 'activity-item';
    
    activityItem.innerHTML = `
      <div class="activity-icon ${activity.type}">
        <i class="fas fa-${getActivityIcon(activity.type)}"></i>
      </div>
      <div class="activity-content">
        <h4>${activity.title}</h4>
        <p>${activity.description}</p>
        <div class="activity-meta">
          <span class="activity-time">
            <i class="far fa-clock"></i> ${activity.time}
          </span>
          <a href="#" class="activity-action">${activity.action}</a>
        </div>
      </div>
    `;
    
    activitiesContainer.appendChild(activityItem);
  });
  
  // Add click event to "View All" button
  const viewAllBtn = document.querySelector('.activities-section .view-all');
  
  if (viewAllBtn) {
    viewAllBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // In a real app, this would navigate to the activities page
      alert('This would navigate to the full activities page.');
    });
  }
}

/**
 * Get icon class for activity type
 */
function getActivityIcon(type) {
  switch (type) {
    case 'inquiry':
      return 'envelope';
    case 'user':
      return 'user';
    case 'content':
      return 'file-alt';
    case 'testimonial':
      return 'comment';
    default:
      return 'bell';
  }
}

/**
 * Initialize quick actions section
 */
function initializeQuickActions() {
  const quickActionBtns = document.querySelectorAll('.quick-action-btn');
  
  if (!quickActionBtns.length) return;
  
  quickActionBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      const action = this.getAttribute('data-action');
      
      // In a real app, these would navigate to different sections
      switch (action) {
        case 'add-post':
          window.location.href = 'admin-blog-add.html';
          break;
        case 'add-service':
          window.location.href = 'admin-services-add.html';
          break;
        case 'add-user':
          window.location.href = 'admin-users-add.html';
          break;
        case 'settings':
          window.location.href = 'admin-settings.html';
          break;
        default:
          alert('This action is not yet implemented.');
      }
    });
  });
}

/**
 * Initialize date range filter
 */
function initializeDateRangeFilter() {
  const dateFilter = document.getElementById('date-filter');
  
  if (!dateFilter) return;
  
  dateFilter.addEventListener('change', function() {
    const selectedPeriod = this.value;
    
    if (selectedPeriod === 'custom') {
      // Show custom date range picker
      // In a real app, this would open a date range picker
      alert('In a production app, this would open a date range picker.');
    } else {
      // Update dashboard data based on selected period
      updateDashboardData(selectedPeriod);
    }
  });
}

/**
 * Update dashboard data based on selected time period
 * In a real app, this would fetch data from the server
 */
function updateDashboardData(period) {
  console.log('Updating dashboard data for period:', period);
  
  // In a real app, this would fetch data from an API
  // and update all dashboard components
  
  // For demo purposes, just show a message
  const notification = document.createElement('div');
  notification.className = 'notification';
  notification.textContent = `Dashboard data updated for ${period}`;
  
  document.body.appendChild(notification);
  
  // Remove notification after 3 seconds
  setTimeout(() => {
    document.body.removeChild(notification);
  }, 3000);
}

/**
 * Initialize inquiries table
 */
function initializeInquiryTable() {
  const table = document.querySelector('.inquiries-table');
  
  if (!table) return;
  
  // Add click event to view buttons
  const viewButtons = table.querySelectorAll('.view-btn');
  
  viewButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      
      const inquiryId = this.getAttribute('data-id');
      
      // In a real app, this would navigate to the inquiry details page
      alert(`This would view inquiry #${inquiryId || 'unknown'}`);
    });
  });
  
  // Add click event to reply buttons
  const replyButtons = table.querySelectorAll('.reply-btn');
  
  replyButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      
      const inquiryId = this.getAttribute('data-id');
      
      // In a real app, this would open a reply modal or navigate to a reply page
      alert(`This would open a reply form for inquiry #${inquiryId || 'unknown'}`);
    });
  });
}