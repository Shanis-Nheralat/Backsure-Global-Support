<?php
/**
 * Admin Dashboard
 * Overview page with quick actions and activity feed
 */

// Authentication and permissions
require_once 'admin-auth.php';
require_admin_auth();

// Include notifications system
require_once 'admin-notifications.php';

// Track page view for analytics
require_once 'admin-analytics.php';
log_page_view(basename($_SERVER['PHP_SELF']));

// Page variables
$page_title = 'Dashboard';
$current_page = 'dashboard';
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '#']
];

// Get admin info
$admin_user = get_admin_user();
$admin_username = $admin_user['username'];
$admin_role = $admin_user['role'];

// Get analytics data
$traffic_data = get_page_view_stats('weekly', 7);
$chart_data = format_chart_data($traffic_data, 'Website Traffic');
$traffic_sources = get_traffic_sources();
$top_pages = get_top_pages(5);
$recent_activities = get_recent_activities(4);
$unread_inquiries = 3; // Placeholder value, should be fetched from database

// Base URL for assets
$scriptPath = $_SERVER['SCRIPT_NAME'];
$parentDir = dirname($scriptPath);
$baseUrl = rtrim($parentDir, '/') . '/';

// Include templates
include 'admin-head.php';
include 'admin-sidebar.php';
?>

<main class="admin-main">
  <?php include 'admin-header.php'; ?>
  
  <!-- Dashboard Content -->
  <div class="admin-content container-fluid py-4">
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <h1 class="h3 mb-0 text-gray-800">Dashboard Overview</h1>
          <div class="date-display">
            <i class="far fa-calendar-alt"></i>
            <span id="current-date"><?php echo date('l, F j, Y'); ?></span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Stats Overview -->
    <div class="row mb-4">
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Website Visitors</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">2,458</div>
                <div class="mt-2 small">
                  <span class="text-success">
                    <i class="fas fa-arrow-up"></i>
                    15.3%
                  </span>
                  vs last month
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-eye fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">New Inquiries</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">37</div>
                <div class="mt-2 small">
                  <span class="text-success">
                    <i class="fas fa-arrow-up"></i>
                    8.2%
                  </span>
                  vs last month
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-envelope-open fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Blog Articles</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">14</div>
                <div class="mt-2 small">
                  <span class="text-muted">
                    <i class="fas fa-minus"></i>
                    0%
                  </span>
                  vs last month
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-file-alt fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">New Subscribers</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">28</div>
                <div class="mt-2 small">
                  <span class="text-danger">
                    <i class="fas fa-arrow-down"></i>
                    5.7%
                  </span>
                  vs last month
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-user-plus fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Charts & Analytics -->
    <div class="row mb-4">
      <div class="col-lg-8">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Website Traffic</h6>
            <div class="dropdown no-arrow">
              <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-primary active" data-period="weekly">Weekly</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-period="monthly">Monthly</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-period="yearly">Yearly</button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="chart-area">
              <canvas id="traffic-chart"></canvas>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4">
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Traffic Sources</h6>
          </div>
          <div class="card-body">
            <div class="chart-pie mb-4">
              <canvas id="traffic-sources-chart"></canvas>
            </div>
            <div class="mt-4">
              <?php foreach ($traffic_sources as $source): ?>
              <div class="mb-2">
                <div class="d-flex justify-content-between mb-1">
                  <span><?php echo htmlspecialchars($source['name']); ?></span>
                  <span><?php echo $source['value']; ?>%</span>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar" style="width: <?php echo $source['value']; ?>%; background-color: <?php echo $source['color']; ?>;"></div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        
        <div class="card shadow">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Top Pages</h6>
          </div>
          <div class="card-body p-0">
            <div class="list-group list-group-flush">
              <?php foreach ($top_pages as $page): ?>
              <div class="list-group-item px-3 py-2 d-flex justify-content-between align-items-center">
                <span><?php echo htmlspecialchars($page['name']); ?></span>
                <span class="badge bg-primary rounded-pill"><?php echo number_format($page['views']); ?></span>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="row">
      <!-- Recent Activities Section -->
      <div class="col-lg-7 mb-4">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
            <a href="admin-activities.php" class="btn btn-sm btn-primary">View All</a>
          </div>
          <div class="card-body">
            <?php foreach ($recent_activities as $activity): ?>
            <div class="activity-item mb-3 pb-3 border-bottom">
              <div class="d-flex">
                <div class="mr-3">
                  <div class="icon-circle bg-<?php 
                    echo $activity['type'] === 'inquiry' ? 'primary' : 
                        ($activity['type'] === 'user' ? 'success' : 
                        ($activity['type'] === 'content' ? 'info' : 
                        ($activity['type'] === 'testimonial' ? 'warning' : 'secondary'))); 
                  ?>">
                    <i class="fas fa-<?php echo get_notification_icon($activity['type']); ?> text-white"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <div class="small text-gray-500"><?php echo $activity['time']; ?></div>
                  <h5 class="mb-1"><?php echo htmlspecialchars($activity['title']); ?></h5>
                  <p class="mb-1"><?php echo htmlspecialchars($activity['description']); ?></p>
                  <a href="<?php echo $activity['link']; ?>" class="btn btn-sm btn-primary"><?php echo $activity['action_text']; ?></a>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      
      <!-- Quick Actions Section -->
      <div class="col-lg-5 mb-4">
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
          </div>
          <div class="card-body">
            <div class="row">
              <?php if (user_has_permission('manage_blog')): ?>
              <div class="col-md-6 mb-3">
                <a href="admin-blog-add.php" class="btn btn-primary btn-block d-flex align-items-center justify-content-center">
                  <i class="fas fa-plus mr-2"></i> New Blog Post
                </a>
              </div>
              <?php endif; ?>
              
              <?php if (user_has_permission('manage_content')): ?>
              <div class="col-md-6 mb-3">
                <a href="admin-pages.php" class="btn btn-primary btn-block d-flex align-items-center justify-content-center">
                  <i class="fas fa-edit mr-2"></i> Edit Pages
                </a>
              </div>
              <?php endif; ?>
              
              <?php if (user_has_permission('manage_media')): ?>
              <div class="col-md-6 mb-3">
                <a href="admin-media.php" class="btn btn-primary btn-block d-flex align-items-center justify-content-center">
                  <i class="fas fa-upload mr-2"></i> Upload Media
                </a>
              </div>
              <?php endif; ?>
              
              <?php if (user_has_permission('manage_users')): ?>
              <div class="col-md-6 mb-3">
                <a href="admin-users-add.php" class="btn btn-primary btn-block d-flex align-items-center justify-content-center">
                  <i class="fas fa-user-plus mr-2"></i> Add User
                </a>
              </div>
              <?php endif; ?>
              
              <?php if (user_has_permission('manage_services')): ?>
              <div class="col-md-6 mb-3">
                <a href="admin-services.php" class="btn btn-primary btn-block d-flex align-items-center justify-content-center">
                  <i class="fas fa-briefcase mr-2"></i> Manage Services
                </a>
              </div>
              <?php endif; ?>
              
              <?php if (user_has_permission('manage_backup')): ?>
              <div class="col-md-6 mb-3">
                <a href="admin-backup.php" class="btn btn-primary btn-block d-flex align-items-center justify-content-center">
                  <i class="fas fa-download mr-2"></i> Backup Data
                </a>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <!-- Recent Inquiries Section -->
        <div class="card shadow">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Recent Inquiries</h6>
            <a href="admin-inquiries.php" class="btn btn-sm btn-primary">View All</a>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>John Smith</td>
                    <td>Dedicated Teams</td>
                    <td><?php echo date('M d', strtotime('-1 day')); ?></td>
                    <td><span class="badge bg-primary">New</span></td>
                  </tr>
                  <tr>
                    <td>Emma Johnson</td>
                    <td>Business Care Plans</td>
                    <td><?php echo date('M d', strtotime('-2 days')); ?></td>
                    <td><span class="badge bg-primary">New</span></td>
                  </tr>
                  <tr>
                    <td>Michael Chen</td>
                    <td>Insurance Support</td>
                    <td><?php echo date('M d', strtotime('-3 days')); ?></td>
                    <td><span class="badge bg-success">Replied</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <?php include 'admin-footer.php'; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Traffic Chart
  const trafficChartCanvas = document.getElementById('traffic-chart');
  if (trafficChartCanvas) {
    const initialData = <?php echo json_encode($chart_data); ?>;
    const trafficChart = new Chart(trafficChartCanvas, {
      type: 'line',
      data: initialData,
      options: {
        maintainAspectRatio: false,
        layout: {
          padding: {
            left: 10,
            right: 25,
            top: 25,
            bottom: 0
          }
        },
        scales: {
          y: {
            ticks: {
              beginAtZero: true
            },
            grid: {
              color: "rgba(0, 0, 0, 0.05)"
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
    
    // Period buttons
    document.querySelectorAll('[data-period]').forEach(button => {
      button.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('[data-period]').forEach(btn => {
          btn.classList.remove('active');
          btn.classList.remove('btn-primary');
          btn.classList.add('btn-outline-primary');
        });
        
        // Add active class to clicked button
        this.classList.add('active');
        this.classList.add('btn-primary');
        this.classList.remove('btn-outline-primary');
        
        // Get period
        const period = this.getAttribute('data-period');
        
        // Update chart data based on period
        let url = `admin-ajax.php?action=get_traffic_data&period=${period}`;
        
        // For demo, we'll use pre-generated data
        let newData;
        switch(period) {
          case 'weekly':
            newData = <?php echo json_encode(format_chart_data(get_page_view_stats('weekly', 7), 'Weekly Traffic')); ?>;
            break;
          case 'monthly':
            newData = <?php echo json_encode(format_chart_data(get_page_view_stats('monthly', 6), 'Monthly Traffic')); ?>;
            break;
          case 'yearly':
            newData = <?php echo json_encode(format_chart_data(get_page_view_stats('yearly', 12), 'Yearly Traffic')); ?>;
            break;
        }
        
        // Update chart
        trafficChart.data.labels = newData.labels;
        trafficChart.data.datasets[0].data = newData.datasets[0].data;
        trafficChart.data.datasets[0].label = newData.datasets[0].label;
        trafficChart.update();
      });
    });
  }
  
  // Traffic Sources Chart
  const trafficSourcesCanvas = document.getElementById('traffic-sources-chart');
  if (trafficSourcesCanvas) {
    const sourcesData = {
      labels: <?php echo json_encode(array_column($traffic_sources, 'name')); ?>,
      datasets: [{
        data: <?php echo json_encode(array_column($traffic_sources, 'value')); ?>,
        backgroundColor:
