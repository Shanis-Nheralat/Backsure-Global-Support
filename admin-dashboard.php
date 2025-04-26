<?php
/**
 * Admin Dashboard
 * Main dashboard for the admin panel
 */

// Include authentication component
require_once 'admin-auth.php';

// Require authentication
require_admin_auth();

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
    'assets/js/chart.min.js',
    'assets/js/admin-dashboard.js'
];

// Get admin info
$admin_user = get_admin_user();
$admin_username = $admin_user['username'];
$admin_role = $admin_user['role'];

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
    
    <!-- Analytics Section -->
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
  
  <?php include 'admin-footer.php'; ?>
</main>
</div>
</body>
</html>
