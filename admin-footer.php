<?php
/**
 * Admin Footer Component
 * This file contains the footer for the admin panel
 * It should be included in all admin pages
 */
?>
<!-- Admin Footer -->
<footer class="admin-footer">
  <div class="footer-left">
    <p>&copy; <?php echo date('Y'); ?> Backsure Global Support. All rights reserved.</p>
  </div>
  <div class="footer-right">
    <span>Admin Panel v1.0</span>
  </div>
</footer>

<!-- JavaScript for Admin Dashboard -->
<script>
  // Initialize dashboard functionality
  document.addEventListener('DOMContentLoaded', function() {
    // Set current date if element exists
    const dateElement = document.getElementById('current-date');
    if (dateElement) {
      const now = new Date();
      dateElement.innerText = now.toLocaleDateString('en-US', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
      });
    }
    
    // Initialize traffic chart if element exists
    const trafficChartElement = document.getElementById('traffic-chart');
    if (trafficChartElement) {
      const trafficCtx = trafficChartElement.getContext('2d');
      const trafficChart = new Chart(trafficCtx, {
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
      
      // Chart controls
      const chartControls = document.querySelectorAll('.chart-controls button');
      chartControls.forEach(button => {
        button.addEventListener('click', function() {
          chartControls.forEach(btn => btn.classList.remove('active'));
          this.classList.add('active');
          // Here you would update the chart data based on the selected time period
        });
      });
    }
    
    // Sidebar toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const adminContainer = document.querySelector('.admin-container');
    
    if (sidebarToggle && adminContainer) {
      sidebarToggle.addEventListener('click', function() {
        adminContainer.classList.toggle('sidebar-collapsed');
      });
    }
    
    // User dropdown
    const userDropdownToggle = document.getElementById('user-dropdown-toggle');
    const userDropdown = document.getElementById('user-dropdown');
    
    if (userDropdownToggle && userDropdown) {
      userDropdownToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        userDropdown.classList.toggle('show');
      });
      
      // Close dropdown when clicking outside
      document.addEventListener('click', function(e) {
        if (!userDropdownToggle.contains(e.target) && !userDropdown.contains(e.target)) {
          userDropdown.classList.remove('show');
        }
      });
    }
    
    // Submenu toggle
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
  });
</script>
