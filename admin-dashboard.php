<?php
/**
 * Admin Dashboard
 * Overview page showing statistics, activities and quick actions
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
$page_specific_js = 'admin-dashboard.js';

// Get admin info
$admin_user = get_admin_user();
$admin_username = $admin_user['username'];
$admin_role = $admin_user['role'];

// Get analytics data
$traffic_data = get_page_view_stats('weekly', 7);
$chart_data = format_chart_data($traffic_data, 'Website Traffic');
$traffic_sources = get_traffic_sources();
$top_pages = get_top_pages(4);
$recent_activities = get_recent_activities(4);
$dashboard_stats = get_dashboard_stats();

// Base URL for assets
$scriptPath = $_SERVER['SCRIPT_NAME'];
$parentDir = dirname($scriptPath);
$baseUrl = rtrim($parentDir, '/') . '/';

// Include head template
include 'admin-head.php';
?>

<?php 
// Include sidebar
include 'admin-sidebar.php'; 
?>

<main class="admin-main">
  <?php include 'admin-header.php'; ?>
  
  <!-- Dashboard Content -->
  <div class="admin-content container-fluid py-4">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
      <h1>Dashboard Overview</h1>
      <div class="date-display">
        <i class="far fa-calendar-alt"></i>
        <span id="current-date"><?php echo date('l, F j, Y'); ?></span>
      </div>
    </div>
    
    <!-- Stats Overview -->
    <div class="row stats-overview">
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100 border-left-primary">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Website Visitors</div>
                <div class="h5 mb-0 font-weight-bold"><?php echo number_format($dashboard_stats['visitors']['count']); ?></div>
                <div class="mt-2 small">
                  <?php 
                  $change_class = 'text-success';
                  $change_icon = 'fa-arrow-up';
                  
                  if ($dashboard_stats['visitors']['change_type'] === 'negative') {
                    $change_class = 'text-danger';
                    $change_icon = 'fa-arrow-down';
                  } elseif ($dashboard_stats['visitors']['change_type'] === 'neutral') {
                    $change_class = 'text-muted';
                    $change_icon = 'fa-minus';
                  }
                  ?>
                  <span class="<?php echo $change_class; ?>">
                    <i class="fas <?php echo $change_icon; ?>"></i>
                    <?php echo abs($dashboard_stats['visitors']['change']); ?>%
                  </span>
                  vs last month
                </div>
              </div>
              <div class="col-auto">
                <div class="stat-icon blue">
                  <i class="fas fa-eye fa-2x"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100 border-left-success">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">New Inquiries</div>
                <div class="h5 mb-0 font-weight-bold"><?php echo number_format($dashboard_stats['inquiries']['count']); ?></div>
                <div class="mt-2 small">
                  <?php 
                  $change_class = 'text-success';
                  $change_icon = 'fa-arrow-up';
                  
                  if ($dashboard_stats['inquiries']['change_type'] === 'negative') {
                    $change_class = 'text-danger';
                    $change_icon = 'fa-arrow-down';
                  } elseif ($dashboard_stats['inquiries']['change_type'] === 'neutral') {
                    $change_class = 'text-muted';
                    $change_icon = 'fa-minus';
                  }
                  ?>
                  <span class="<?php echo $change_class; ?>">
                    <i class="fas <?php echo $change_icon; ?>"></i>
                    <?php echo abs($dashboard_stats['inquiries']['change']); ?>%
                  </span>
                  vs last month
                </div>
              </div>
              <div class="col-auto">
                <div class="stat-icon green">
                  <i class="fas fa-envelope-open fa-2x"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100 border-left-info">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Blog Articles</div>
                <div class="h5 mb-0 font-weight-bold"><?php echo number_format($dashboard_stats['blog_posts']['count']); ?></div>
                <div class="mt-2 small">
                  <?php 
                  $change_class = 'text-success';
                  $change_icon = 'fa-arrow-up';
                  
                  if ($dashboard_stats['blog_posts']['change_type'] === 'negative') {
                    $change_class = 'text-danger';
                    $change_icon = 'fa-arrow-down';
                  } elseif ($dashboard_stats['blog_posts']['change_type'] === 'neutral') {
                    $change_class = 'text-muted';
                    $change_icon = 'fa-minus';
                  }
                  ?>
                  <span class="<?php echo $change_class; ?>">
                    <i class="fas <?php echo $change_icon; ?>"></i>
                    <?php echo abs($dashboard_stats['blog_posts']['change']); ?>%
                  </span>
                  vs last month
                </div>
              </div>
              <div class="col-auto">
                <div class="stat-icon purple">
                  <i class="fas fa-file-alt fa-2x"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100 border-left-warning">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">New Subscribers</div>
                <div class="h5 mb-0 font-weight-bold"><?php echo number_format($dashboard_stats['subscribers']['count']); ?></div>
                <div class="mt-2 small">
                  <?php 
                  $change_class = 'text-success';
                  $change_icon = 'fa-arrow-up';
                  
                  if ($dashboard_stats['subscribers']['change_type'] === 'negative') {
                    $change_class = 'text-danger';
                    $change_icon = 'fa-arrow-down';
                  } elseif ($dashboard_stats['subscribers']['change_type'] === 'neutral') {
                    $change_class = 'text-muted';
                    $change_icon = 'fa-minus';
                  }
                  ?>
                  <span class="<?php echo $change_class; ?>">
                    <i class="fas <?php echo $change_icon; ?>"></i>
                    <?php echo abs($dashboard_stats['subscribers']['change']); ?>%
                  </span>
                  vs last month
                </div>
              </div>
              <div class="col-auto">
                <div class="stat-icon orange">
                  <i class="fas fa-user-plus fa-2x"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Charts & Analytics -->
    <div class="row mb-4">
      <div class="col-lg-8">
        <div class="card shadow h-100">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold">Website Traffic</h6>
            <div class="chart-controls">
              <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-primary active" data-period="weekly">Weekly</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-period="monthly">Monthly</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-period="yearly">Yearly</button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="chart-area">
              <canvas id="traffic-chart" height="300"></canvas>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4">
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Traffic Sources</h6>
          </div>
          <div class="card-body">
            <div class="chart-pie mb-4">
              <canvas id="traffic-sources-chart" height="200"></canvas>
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
            <h6 class="m-0 font-weight-bold">Top Pages</h6>
          </div>
          <div class="card-body">
            <div class="list-group list-group-flush">
              <?php foreach ($top_pages as $page): ?>
              <div class="list-group-item px-0 d-flex justify-content-between align-items-center border-0">
                <span><?php echo htmlspecialchars($page['name']); ?></span>
                <span class="badge bg-primary rounded-pill"><?php echo number_format($page['views']); ?></span>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Recent Activities & Quick Actions -->
    <div class="row">
      <div class="col-lg-7">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold">Recent Activities</h6>
            <a href="admin-activities.php" class="btn btn-sm btn-primary">View All</a>
          </div>
          <div class="card-body">
            <div class="activity-container">
              <?php foreach ($recent_activities as $activity): ?>
              <div class="activity-item mb-3 pb-3 border-bottom">
                <div class="d-flex">
                  <div class="activity-icon <?php echo $activity['type']; ?> me-3">
                    <i class="fas fa-<?php echo get_notification_icon($activity['type']); ?>"></i>
                  </div>
                  <div class="activity-content">
                    <h5 class="mb-1"><?php echo htmlspecialchars($activity['title']); ?></h5>
                    <p class="mb-1"><?php echo htmlspecialchars($activity['description']); ?></p>
                    <div class="activity-meta d-flex justify-content-between">
                      <span class="text-muted"><i class="far fa-clock me-1"></i> <?php echo $activity['time']; ?></span>
                      <a href="<?php echo $activity['link']; ?>" class="btn btn-sm btn-outline-primary"><?php echo $activity['action_text']; ?></a>
                    </div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-5">
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Quick Actions</h6>
          </div>
          <div class="card-body">
            <div class="row">
              <?php if (user_has_permission('manage_blog')): ?>
              <div class="col-md-6 mb-2">
                <a href="admin-blog-add.php" class="btn btn-primary btn-block text-start">
                  <i class="fas fa-plus me-2"></i> New Blog Post
                </a>
              </div>
              <?php endif; ?>
              
              <?php if (user_has_permission('manage_content')): ?>
              <div class="col-md-6 mb-2">
                <a href="admin-pages.php" class="btn btn-primary btn-block text-start">
                  <i class="fas fa-edit me-2"></i> Edit Pages
                </a>
              </div>
              <?php endif; ?>
              
              <?php if (user_has_permission('manage_media')): ?>
              <div class="col-md-6 mb-2">
                <a href="admin-media.php" class="btn btn-primary btn-block text-start">
                  <i class="fas fa-upload me-2"></i> Upload Media
                </a>
              </div>
              <?php endif; ?>
              
              <?php if (user_has_permission('manage_users')): ?>
              <div class="col-md-6 mb-2">
                <a href="admin-users-add.php" class="btn btn-primary btn-block text-start">
                  <i class="fas fa-user-plus me-2"></i> Add User
                </a>
              </div>
              <?php endif; ?>
              
              <?php if (user_has_permission('manage_services')): ?>
              <div class="col-md-6 mb-2">
                <a href="admin-services.php" class="btn btn-primary btn-block text-start">
                  <i class="fas fa-briefcase me-2"></i> Manage Services
                </a>
              </div>
              <?php endif; ?>
              
              <?php if (user_has_permission('manage_backup')): ?>
              <div class="col-md-6 mb-2">
                <a href="admin-backup.php" class="btn btn-primary btn-block text-start">
                  <i class="fas fa-download me-2"></i> Backup Data
                </a>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <?php if (user_has_permission('manage_inquiries') && !empty($recent_inquiries)): ?>
        <div class="card shadow">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold">Recent Inquiries</h6>
            <a href="admin-inquiries.php" class="btn btn-sm btn-primary">View All</a>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped table-hover mb-0">
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
        <?php endif; ?>
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
        });
        
        // Add active class to clicked button
        this.classList.add('active');
        
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
        backgroundColor: <?php echo json_encode(array_column($traffic_sources, 'color')); ?>,
        hoverBackgroundColor: <?php echo json_encode(array_column($traffic_sources, 'color')); ?>,
        hoverBorderColor: "rgba(234, 236, 244, 1)",
      }]
    };
    
    const sourcesChart = new Chart(trafficSourcesCanvas, {
      type: 'doughnut',
      data: sourcesData,
      options: {
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: "rgb(255,255,255)",
            bodyColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            caretPadding: 10,
            displayColors: false,
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
});
</script>
