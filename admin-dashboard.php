<?php
/**
 * Admin Dashboard
 * Main dashboard for the admin panel
 */

// Include authentication component
require_once 'admin-auth.php';

// Require authentication
require_admin_auth();

// Include notifications system
require_once 'admin-notifications.php';

// Include analytics
require_once 'admin-analytics.php';

// Set page variables
$page_title = 'Dashboard';
$current_page = 'dashboard';
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => 'admin-dashboard.php']
];

// Extra CSS/JS files needed for this page
$extra_css = [
    'assets/css/admin-dashboard.css'
];
$extra_js = [
    'https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js',
    'assets/js/admin-dashboard.js'
];

// Get admin info
$admin_user = get_admin_user();
$admin_username = $admin_user['username'];
$admin_role = $admin_user['role'];

// Track page view
log_page_view(basename($_SERVER['PHP_SELF']));

// Function to get recent activity (in a real app, this would come from a database)
function get_recent_activity() {
    return [
        [
            'type' => 'inquiry',
            'title' => 'New Inquiry',
            'description' => 'John Smith submitted a new inquiry about business services.',
            'time' => '2 hours ago',
            'url' => 'admin-inquiries.php?action=view&id=123'
        ],
        [
            'type' => 'user',
            'title' => 'New User Registered',
            'description' => 'Sarah Johnson created a new account.',
            'time' => '5 hours ago',
            'url' => 'admin-users.php?action=view&id=456'
        ],
        [
            'type' => 'content',
            'title' => 'Blog Post Published',
            'description' => 'The article "5 Ways to Improve Your Business" was published.',
            'time' => '1 day ago',
            'url' => 'admin-blog.php?action=view&id=789'
        ],
        [
            'type' => 'testimonial',
            'title' => 'New Testimonial',
            'description' => 'Global Services Inc. submitted a new testimonial.',
            'time' => '2 days ago',
            'url' => 'admin-testimonials.php?action=view&id=101'
        ],
    ];
}

// Get stats (in a real app, these would come from a database)
$stats = [
    'visitors' => [
        'value' => 2,845,
        'change' => 12.5,
        'icon' => 'users',
        'color' => 'blue'
    ],
    'inquiries' => [
        'value' => 42,
        'change' => 5.8,
        'icon' => 'envelope',
        'color' => 'green'
    ],
    'blog_views' => [
        'value' => 1,258,
        'change' => -2.3,
        'icon' => 'newspaper',
        'color' => 'purple'
    ],
    'conversions' => [
        'value' => 18,
        'change' => 9.2,
        'icon' => 'chart-line',
        'color' => 'orange'
    ],
];

// Get recent inquiries (in a real app, these would come from a database)
$recent_inquiries = [
    [
        'id' => 123,
        'name' => 'John Smith',
        'subject' => 'Business Insurance Quote',
        'date' => '2025-04-23',
        'status' => 'new'
    ],
    [
        'id' => 122,
        'name' => 'Emily Johnson',
        'subject' => 'HR Services Inquiry',
        'date' => '2025-04-22',
        'status' => 'replied'
    ],
    [
        'id' => 121,
        'name' => 'Michael Brown',
        'subject' => 'Accounting Support',
        'date' => '2025-04-22',
        'status' => 'new'
    ],
    [
        'id' => 120,
        'name' => 'Sarah Davis',
        'subject' => 'Tax Consultation Request',
        'date' => '2025-04-21',
        'status' => 'closed'
    ],
];

// Dashboard chart data function
function get_dashboard_chart_data() {
    // In a real implementation, these would be database queries
    // Example: SELECT COUNT(*) as count, DATE(created_at) as date FROM page_views GROUP BY DATE(created_at) ORDER BY date LIMIT 7
    
    // Sample data for demonstration
    $activity_data = [
        'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        'pageViews' => [1200, 1800, 1400, 2000, 2400, 2200, 2600],
        'sessions' => [400, 500, 450, 600, 700, 650, 750],
        'newUsers' => [200, 250, 220, 280, 320, 300, 350]
    ];
    
    $content_distribution = [
        ['name' => 'Blog Posts', 'value' => 45, 'color' => '#3498db'],
        ['name' => 'Services', 'value' => 25, 'color' => '#2ecc71'],
        ['name' => 'Testimonials', 'value' => 15, 'color' => '#9b59b6'],
        ['name' => 'FAQ', 'value' => 10, 'color' => '#f39c12'],
        ['name' => 'Other', 'value' => 5, 'color' => '#e74c3c']
    ];
    
    $user_actions = [
        ['name' => 'View', 'count' => 820],
        ['name' => 'Create', 'count' => 330],
        ['name' => 'Update', 'count' => 450],
        ['name' => 'Delete', 'count' => 140]
    ];
    
    return [
        'activity' => $activity_data,
        'content' => $content_distribution,
        'actions' => $user_actions
    ];
}

// Include header template
include 'admin-head.php';
include 'admin-sidebar.php';
?>

<!-- Main Content Area -->
<main class="admin-main">
  <?php include 'admin-header.php'; ?>
  
  <!-- Dashboard Content -->
  <div class="admin-content">
    <div class="page-header">
      <h1>Dashboard</h1>
      <div class="date-display">
        <i class="fas fa-calendar"></i>
        <span id="current-date">April 25, 2025</span>
      </div>
    </div>
    
    <!-- Stats Overview -->
    <div class="stats-overview">
      <?php foreach ($stats as $key => $stat): ?>
        <div class="stat-card">
          <div class="stat-icon <?php echo $stat['color']; ?>">
            <i class="fas fa-<?php echo $stat['icon']; ?>"></i>
          </div>
          <div class="stat-content">
            <h3><?php echo ucwords(str_replace('_', ' ', $key)); ?></h3>
            <div class="stat-value"><?php echo number_format($stat['value']); ?></div>
            <div class="stat-change <?php echo $stat['change'] >= 0 ? 'positive' : 'negative'; ?>">
              <i class="fas fa-<?php echo $stat['change'] >= 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
              <?php echo abs($stat['change']); ?>% since last month
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    
    <!-- New Enhanced Analytics Dashboard -->
    <div class="dashboard-charts bg-white p-6 rounded-lg shadow mb-6">
      <h2 class="text-2xl font-bold mb-6">Admin Panel Analytics</h2>
      
      <!-- Tabs -->
      <div class="chart-tabs flex border-b mb-6">
        <button class="chart-tab active py-2 px-4 border-b-2 border-blue-500 text-blue-500" data-tab="overview">Overview</button>
        <button class="chart-tab py-2 px-4 text-gray-500" data-tab="content">Content</button>
        <button class="chart-tab py-2 px-4 text-gray-500" data-tab="users">User Actions</button>
      </div>
      
      <!-- Tab content containers -->
      <div class="chart-content">
        
        <!-- Overview Tab (shown by default) -->
        <div class="chart-pane active" id="overview-pane">
          <h3 class="text-lg font-semibold mb-4">Activity Overview</h3>
          <div class="chart-container" style="position: relative; height: 300px;">
            <canvas id="activityChart"></canvas>
          </div>
          
          <!-- Stats cards -->
          <div class="stats-cards grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="stat-card bg-blue-50 p-4 rounded">
              <h4 class="text-lg text-blue-700">Total Page Views</h4>
              <p class="text-3xl font-bold">12,600</p>
              <p class="text-sm text-green-600">↑ 18.5% from last month</p>
            </div>
            
            <div class="stat-card bg-green-50 p-4 rounded">
              <h4 class="text-lg text-green-700">Active Users</h4>
              <p class="text-3xl font-bold">4,050</p>
              <p class="text-sm text-green-600">↑ 12.3% from last month</p>
            </div>
            
            <div class="stat-card bg-purple-50 p-4 rounded">
              <h4 class="text-lg text-purple-700">Avg. Session Duration</h4>
              <p class="text-3xl font-bold">2m 45s</p>
              <p class="text-sm text-red-600">↓ 3.1% from last month</p>
            </div>
          </div>
        </div>
        
        <!-- Content Tab -->
        <div class="chart-pane" id="content-pane" style="display: none;">
          <h3 class="text-lg font-semibold mb-4">Content Distribution</h3>
          <div class="flex flex-col md:flex-row">
            <div class="w-full md:w-1/2">
              <div class="chart-container" style="position: relative; height: 300px;">
                <canvas id="contentDistributionChart"></canvas>
              </div>
            </div>
            
            <div class="w-full md:w-1/2 mt-6 md:mt-0">
              <div class="content-bars grid grid-cols-1 gap-4">
                <?php 
                $chart_data = get_dashboard_chart_data();
                foreach ($chart_data['content'] as $item): ?>
                  <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full mr-2" style="background-color: <?php echo $item['color']; ?>"></div>
                    <div class="flex-1">
                      <div class="flex justify-between">
                        <span><?php echo $item['name']; ?></span>
                        <span class="font-bold"><?php echo $item['value']; ?>%</span>
                      </div>
                      <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
                        <div class="h-2.5 rounded-full" style="width: <?php echo $item['value']; ?>%; background-color: <?php echo $item['color']; ?>"></div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          
          <!-- Content performance table -->
          <div class="mt-6">
            <h4 class="text-lg font-semibold mb-2">Content Performance</h4>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Engagement</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap">Blog Posts</td>
                    <td class="px-6 py-4 whitespace-nowrap">4,526</td>
                    <td class="px-6 py-4 whitespace-nowrap">3m 12s</td>
                    <td class="px-6 py-4 whitespace-nowrap">High</td>
                  </tr>
                  
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap">Services</td>
                    <td class="px-6 py-4 whitespace-nowrap">2,845</td>
                    <td class="px-6 py-4 whitespace-nowrap">2m 05s</td>
                    <td class="px-6 py-4 whitespace-nowrap">Medium</td>
                  </tr>
                  
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap">Testimonials</td>
                    <td class="px-6 py-4 whitespace-nowrap">1,724</td>
                    <td class="px-6 py-4 whitespace-nowrap">1m 45s</td>
                    <td class="px-6 py-4 whitespace-nowrap">Medium</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
        <!-- Users Tab -->
        <div class="chart-pane" id="users-pane" style="display: none;">
          <h3 class="text-lg font-semibold mb-4">User Actions</h3>
          <div class="chart-container" style="position: relative; height: 300px;">
            <canvas id="userActionsChart"></canvas>
          </div>
          
          <!-- Active users table -->
          <div class="mt-6">
            <h4 class="text-lg font-semibold mb-2">Most Active Users</h4>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white">JS</div>
                        <div class="ml-3">John Smith</div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">Admin</td>
                    <td class="px-6 py-4 whitespace-nowrap">214</td>
                    <td class="px-6 py-4 whitespace-nowrap">Today at 10:45 AM</td>
                  </tr>
                  
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center text-white">AD</div>
                        <div class="ml-3">Alice Davis</div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">Editor</td>
                    <td class="px-6 py-4 whitespace-nowrap">187</td>
                    <td class="px-6 py-4 whitespace-nowrap">Today at 9:12 AM</td>
                  </tr>
                  
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center text-white">RJ</div>
                        <div class="ml-3">Robert Johnson</div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">Author</td>
                    <td class="px-6 py-4 whitespace-nowrap">156</td>
                    <td class="px-6 py-4 whitespace-nowrap">Yesterday at 2:30 PM</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Original Analytics Section -->
    <div class="analytics-section">
      <div class="chart-container">
        <div class="chart-header">
          <h2>Website Traffic</h2>
          <div class="chart-controls">
            <button class="active">Weekly</button>
            <button>Monthly</button>
            <button>Yearly</button>
          </div>
        </div>
        <div class="chart-body">
          <canvas id="traffic-chart"></canvas>
        </div>
      </div>
      
      <div class="analytics-sidebar">
        <div class="widget">
          <h3>Traffic Sources</h3>
          <ul class="source-list">
            <li>
              <div class="source-info">
                <span class="source-name">Google</span>
                <span class="source-value">45%</span>
              </div>
              <div class="progress-bar">
                <div class="progress" style="width: 45%; background-color: #4e73df;"></div>
              </div>
            </li>
            <li>
              <div class="source-info">
                <span class="source-name">Direct</span>
                <span class="source-value">30%</span>
              </div>
              <div class="progress-bar">
                <div class="progress" style="width: 30%; background-color: #1cc88a;"></div>
              </div>
            </li>
            <li>
              <div class="source-info">
                <span class="source-name">Referral</span>
                <span class="source-value">15%</span>
              </div>
              <div class="progress-bar">
                <div class="progress" style="width: 15%; background-color: #36b9cc;"></div>
              </div>
            </li>
            <li>
              <div class="source-info">
                <span class="source-name">Social</span>
                <span class="source-value">10%</span>
              </div>
              <div class="progress-bar">
                <div class="progress" style="width: 10%; background-color: #f6c23e;"></div>
              </div>
            </li>
          </ul>
        </div>
        
        <div class="widget">
          <h3>Top Pages</h3>
          <ul class="page-list">
            <li>
              <div class="page-info">
                <span class="page-name">Home Page</span>
                <span class="page-views">1,245</span>
              </div>
              <div class="progress-bar">
                <div class="progress" style="width: 65%; background-color: #4e73df;"></div>
              </div>
            </li>
            <li>
              <div class="page-info">
                <span class="page-name">Services</span>
                <span class="page-views">987</span>
              </div>
              <div class="progress-bar">
                <div class="progress" style="width: 52%; background-color: #1cc88a;"></div>
              </div>
            </li>
            <li>
              <div class="page-info">
                <span class="page-name">About Us</span>
                <span class="page-views">743</span>
              </div>
              <div class="progress-bar">
                <div class="progress" style="width: 39%; background-color: #36b9cc;"></div>
              </div>
            </li>
            <li>
              <div class="page-info">
                <span class="page-name">Contact</span>
                <span class="page-views">521</span>
              </div>
              <div class="progress-bar">
                <div class="progress" style="width: 27%; background-color: #f6c23e;"></div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    
    <!-- Quick Access Section -->
    <div class="quick-access-section">
      <div class="quick-actions">
        <div class="section-header">
          <h2>Quick Actions</h2>
        </div>
        
        <div class="action-buttons">
          <a href="admin-inquiries.php" class="quick-action-btn">
            <i class="fas fa-envelope"></i>
            Inquiries
          </a>
          <a href="admin-users.php" class="quick-action-btn">
            <i class="fas fa-users"></i>
            Users
          </a>
          <a href="admin-blog.php" class="quick-action-btn">
            <i class="fas fa-blog"></i>
            Blog
          </a>
          <a href="admin-services.php" class="quick-action-btn">
            <i class="fas fa-briefcase"></i>
            Services
          </a>
          <a href="admin-settings.php" class="quick-action-btn">
            <i class="fas fa-cog"></i>
            Settings
          </a>
          <a href="admin-profile.php" class="quick-action-btn">
            <i class="fas fa-user"></i>
            Profile
          </a>
        </div>
      </div>
      
      <div class="recent-inquiries">
        <div class="section-header">
          <h2>Recent Inquiries</h2>
          <a href="admin-inquiries.php" class="view-all">View All</a>
        </div>
        
        <table class="admin-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Subject</th>
              <th>Date</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_inquiries as $inquiry): ?>
              <tr>
                <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                <td><?php echo htmlspecialchars($inquiry['subject']); ?></td>
                <td><?php echo date('M d, Y', strtotime($inquiry['date'])); ?></td>
                <td>
                  <span class="status-badge <?php echo $inquiry['status']; ?>">
                    <?php echo ucfirst($inquiry['status']); ?>
                  </span>
                </td>
                <td>
                  <div class="table-actions">
                    <a href="admin-inquiries.php?action=view&id=<?php echo $inquiry['id']; ?>" title="View">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="admin-inquiries.php?action=edit&id=<?php echo $inquiry['id']; ?>" title="Reply">
                      <i class="fas fa-reply"></i>
                    </a>
                    <?php if (has_admin_permission('inquiries_delete')): ?>
                      <a href="#" class="delete-btn" data-id="<?php echo $inquiry['id']; ?>" title="Delete">
                        <i class="fas fa-trash"></i>
                      </a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Activities Section -->
    <div class="activities-section">
      <div class="section-header">
        <h2>Recent Activity</h2>
        <a href="#" class="view-all">View All</a>
      </div>
      
      <div class="activity-container">
        <?php $recent_activities = get_recent_activity(); ?>
        <?php foreach ($recent_activities as $activity): ?>
          <div class="activity-item">
            <div class="activity-icon <?php echo $activity['type']; ?>">
              <i class="fas fa-<?php echo get_activity_icon($activity['type']); ?>"></i>
            </div>
            <div class="activity-content">
              <h4><?php echo htmlspecialchars($activity['title']); ?></h4>
              <p><?php echo htmlspecialchars($activity['description']); ?></p>
              <div class="activity-meta">
                <span class="activity-time"><?php echo $activity['time']; ?></span>
                <a href="<?php echo $activity['url']; ?>" class="activity-action">View Details</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  
  <?php
  // Helper function to get icon for activity type
  function get_activity_icon($type) {
    switch ($type) {
      case 'inquiry':
        return 'envelope';
      case 'user':
        return 'user';
      case 'content':
        return 'file-alt';
      case 'testimonial':
        return 'star';
      default:
        return 'bell';
    }
  }
  ?>
  
  <script>
    // Additional JavaScript for the enhanced dashboard charts
    document.addEventListener("DOMContentLoaded", function() {
      // Chart data
      const activityData = <?php echo json_encode($chart_data['activity']); ?>;
      const contentData = <?php echo json_encode($chart_data['content']); ?>;
      const actionsData = <?php echo json_encode($chart_data['actions']); ?>;
      
      // Tab switching functionality
      const tabs = document.querySelectorAll(".chart-tab");
      const panes = document.querySelectorAll(".chart-pane");
      
      tabs.forEach(tab => {
        tab.addEventListener("click", function() {
          // Remove active class from all tabs/panes
          tabs.forEach(t => t.classList.remove("active", "border-blue-500", "text-blue-500"));
          tabs.forEach(t => t.classList.add("text-gray-500"));
          panes.forEach(p => p.style.display = "none");
          
          // Add active class to current tab
          this.classList.add("active", "border-blue-500", "text-blue-500");
          this.classList.remove("text-gray-500");
          
          // Show the corresponding pane
          const paneId = this.getAttribute("data-tab") + "-pane";
          document.getElementById(paneId).style.display = "block";
        });
      });
      
      // Activity Chart
      const activityCtx = document.getElementById("activityChart").getContext("2d");
      new Chart(activityCtx, {
        type: "line",
        data: {
          labels: activityData.labels,
          datasets: [
            {
              label: "Page Views",
              data: activityData.pageViews,
              borderColor: "#3498db",
              backgroundColor: "rgba(52, 152, 219, 0.1)",
              borderWidth: 2,
              tension: 0.3,
              fill: true
            },
            {
              label: "Sessions",
              data: activityData.sessions,
              borderColor: "#2ecc71",
              backgroundColor: "rgba(46, 204, 113, 0.1)",
              borderWidth: 2,
              tension: 0.3,
              fill: true
            },
            {
              label: "New Users",
              data: activityData.newUsers,
              borderColor: "#9b59b6",
              backgroundColor: "rgba(155, 89, 182, 0.1)",
              borderWidth: 2,
              tension: 0.3,
              fill: true
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: "rgba(0, 0, 0, 0.05)"
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });// Content Distribution Chart
      const contentDistributionCtx = document.getElementById("contentDistributionChart").getContext("2d");
      const contentLabels = [];
      const contentValues = [];
      const contentColors = [];
      
      contentData.forEach(item => {
        contentLabels.push(item.name);
        contentValues.push(item.value);
        contentColors.push(item.color);
      });
      
      new Chart(contentDistributionCtx, {
        type: "doughnut",
        data: {
          labels: contentLabels,
          datasets: [
            {
              data: contentValues,
              backgroundColor: contentColors,
              borderWidth: 1
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: "bottom",
              labels: {
                boxWidth: 12
              }
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  return context.label + ": " + context.raw + "%";
                }
              }
            }
          }
        }
      });
      
      // User Actions Chart
      const userActionsCtx = document.getElementById("userActionsChart").getContext("2d");
      const actionLabels = [];
      const actionCounts = [];
      
      actionsData.forEach(item => {
        actionLabels.push(item.name);
        actionCounts.push(item.count);
      });
      
      new Chart(userActionsCtx, {
        type: "bar",
        data: {
          labels: actionLabels,
          datasets: [
            {
              label: "Number of Actions",
              data: actionCounts,
              backgroundColor: "#3498db",
              borderWidth: 0
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: "rgba(0, 0, 0, 0.05)"
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
    });
  </script>
  
  <?php include 'admin-footer.php'; ?>
</main>
</div>
</body>
</html>
