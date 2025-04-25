<?php
/**
 * Admin Dashboard
 * Main dashboard page for the admin panel
 */

// Start session first thing - no whitespace before this
session_start();

// Block unauthorized users
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Get admin information from session
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin User';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'Administrator';

// Set current page for sidebar highlighting
$current_page = 'dashboard';
$page_title = 'Dashboard Overview';

// Sample notification and task counts - in a real app, these would come from the database
$notification_count = 5;
$task_count = 2;
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="robots" content="noindex, nofollow">
  <title>Admin Dashboard | Backsure Global Support</title>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <!-- Chart.js for analytics -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Admin CSS - External stylesheet -->
  <link rel="stylesheet" href="admin-dashboard.css">
</head>
<body class="admin-body">
  <div class="admin-container">
    <!-- Include the sidebar -->
    <?php include 'admin-sidebar.php'; ?>
    
    <!-- Main Content Area -->
    <main class="admin-main">
      <!-- Include the header -->
      <?php include 'admin-header.php'; ?>
      
      <!-- Dashboard Content -->
      <div class="admin-content">
        <div class="page-header">
          <h1>Dashboard Overview</h1>
          <div class="date-display">
            <i class="far fa-calendar-alt"></i>
            <span id="current-date">Loading date...</span>
          </div>
        </div>
        
        <!-- Stats Overview -->
        <div class="stats-overview">
          <div class="stat-card">
            <div class="stat-icon blue">
              <i class="fas fa-eye"></i>
            </div>
            <div class="stat-content">
              <h3>Website Visitors</h3>
              <div class="stat-value">2,458</div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <span>15.3%</span> vs last month
              </div>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon green">
              <i class="fas fa-envelope-open"></i>
            </div>
            <div class="stat-content">
              <h3>New Inquiries</h3>
              <div class="stat-value">37</div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <span>8.2%</span> vs last month
              </div>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon purple">
              <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
              <h3>Blog Articles</h3>
              <div class="stat-value">14</div>
              <div class="stat-change neutral">
                <i class="fas fa-minus"></i>
                <span>0%</span> vs last month
              </div>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon orange">
              <i class="fas fa-user-plus"></i>
            </div>
            <div class="stat-content">
              <h3>New Subscribers</h3>
              <div class="stat-value">28</div>
              <div class="stat-change negative">
                <i class="fas fa-arrow-down"></i>
                <span>5.7%</span> vs last month
              </div>
            </div>
          </div>
        </div>
        
        <!-- Charts & Analytics -->
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
            <div class="widget traffic-sources">
              <h3>Traffic Sources</h3>
              <ul class="source-list">
                <li>
                  <div class="source-info">
                    <span class="source-name">Direct</span>
                    <span class="source-value">45%</span>
                  </div>
                  <div class="progress-bar">
                    <div class="progress" style="width: 45%; background-color: #4e73df;"></div>
                  </div>
                </li>
                <li>
                  <div class="source-info">
                    <span class="source-name">Organic Search</span>
                    <span class="source-value">30%</span>
                  </div>
                  <div class="progress-bar">
                    <div class="progress" style="width: 30%; background-color: #1cc88a;"></div>
                  </div>
                </li>
                <li>
                  <div class="source-info">
                    <span class="source-name">Social Media</span>
                    <span class="source-value">15%</span>
                  </div>
                  <div class="progress-bar">
                    <div class="progress" style="width: 15%; background-color: #36b9cc;"></div>
                  </div>
                </li>
                <li>
                  <div class="source-info">
                    <span class="source-name">Referral</span>
                    <span class="source-value">10%</span>
                  </div>
                  <div class="progress-bar">
                    <div class="progress" style="width: 10%; background-color: #f6c23e;"></div>
                  </div>
                </li>
              </ul>
            </div>
            
            <div class="widget page-performance">
              <h3>Top Pages</h3>
              <ul class="page-list">
                <li>
                  <div class="page-info">
                    <span class="page-name">Home</span>
                    <span class="page-views">1,245 views</span>
                  </div>
                </li>
                <li>
                  <div class="page-info">
                    <span class="page-name">Finance & Accounting</span>
                    <span class="page-views">842 views</span>
                  </div>
                </li>
                <li>
                  <div class="page-info">
                    <span class="page-name">Contact Us</span>
                    <span class="page-views">625 views</span>
                  </div>
                </li>
                <li>
                  <div class="page-info">
                    <span class="page-name">Dedicated Teams</span>
                    <span class="page-views">418 views</span>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        
        <!-- Recent Activities -->
        <div class="activities-section">
          <div class="section-header">
            <h2>Recent Activities</h2>
            <a href="admin-inquiries.php" class="view-all">View All</a>
          </div>
          
          <div class="activity-container">
            <div class="activity-item">
              <div class="activity-icon inquiry">
                <i class="fas fa-envelope"></i>
              </div>
              <div class="activity-content">
                <h4>New Inquiry Received</h4>
                <p>John Smith submitted a contact form inquiry about Dedicated Teams.</p>
                <div class="activity-meta">
                  <span class="activity-time">2 hours ago</span>
                  <a href="admin-inquiries.php" class="activity-action">View Details</a>
                </div>
              </div>
            </div>
            
            <div class="activity-item">
              <div class="activity-icon user">
                <i class="fas fa-user-plus"></i>
              </div>
              <div class="activity-content">
                <h4>New Admin User Added</h4>
                <p>Sarah Johnson was added as a Marketing Admin.</p>
                <div class="activity-meta">
                  <span class="activity-time">Yesterday</span>
                  <a href="admin-users.php" class="activity-action">View User</a>
                </div>
              </div>
            </div>
            
            <div class="activity-item">
              <div class="activity-icon content">
                <i class="fas fa-edit"></i>
              </div>
              <div class="activity-content">
                <h4>Page Content Updated</h4>
                <p>The Home page content was updated by Mark Wilson.</p>
                <div class="activity-meta">
                  <span class="activity-time">2 days ago</span>
                  <a href="index.php" target="_blank" class="activity-action">View Page</a>
                </div>
              </div>
            </div>
            
            <div class="activity-item">
              <div class="activity-icon testimonial">
                <i class="fas fa-star"></i>
              </div>
              <div class="activity-content">
                <h4>New Testimonial Added</h4>
                <p>A new testimonial from ABC Company was published.</p>
                <div class="activity-meta">
                  <span class="activity-time">3 days ago</span>
                  <a href="admin-testimonials.php" class="activity-action">View Testimonial</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Quick Actions & Recent Inquiries -->
        <div class="quick-access-section">
          <div class="quick-actions">
            <div class="section-header">
              <h2>Quick Actions</h2>
            </div>
            <div class="action-buttons">
              <a href="admin-blog.php" class="quick-action-btn">
                <i class="fas fa-plus"></i>
                <span>New Blog Post</span>
              </a>
              <a href="admin-services.php" class="quick-action-btn">
                <i class="fas fa-edit"></i>
                <span>Edit Services</span>
              </a>
              <a href="admin-blog.php" class="quick-action-btn">
                <i class="fas fa-upload"></i>
                <span>Upload Media</span>
              </a>
              <a href="admin-users.php" class="quick-action-btn">
                <i class="fas fa-user-plus"></i>
                <span>Add User</span>
              </a>
              <a href="admin-services.php" class="quick-action-btn">
                <i class="fas fa-briefcase"></i>
                <span>Manage Services</span>
              </a>
              <a href="admin-settings.php" class="quick-action-btn">
                <i class="fas fa-download"></i>
                <span>Backup Data</span>
              </a>
            </div>
          </div>
          
          <div class="recent-inquiries">
            <div class="section-header">
              <h2>Recent Inquiries</h2>
              <a href="admin-inquiries.php" class="view-all">View All</a>
            </div>
            <div class="inquiries-table-container">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>John Smith</td>
                    <td>john.smith@example.com</td>
                    <td>Dedicated Teams Inquiry</td>
                    <td>Apr 17, 2025</td>
                    <td><span class="status-badge new">New</span></td>
                    <td>
                      <div class="table-actions">
                        <a href="admin-inquiries.php" class="view-btn" title="View"><i class="fas fa-eye"></i></a>
                        <a href="admin-inquiries.php" class="reply-btn" title="Reply"><i class="fas fa-reply"></i></a>
                        <a href="admin-inquiries.php" class="delete-btn" title="Delete"><i class="fas fa-trash-alt"></i></a>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>Emma Johnson</td>
                    <td>emma.j@example.com</td>
                    <td>Business Care Plans</td>
                    <td>Apr 16, 2025</td>
                    <td><span class="status-badge new">New</span></td>
                    <td>
                      <div class="table-actions">
                        <a href="admin-inquiries.php" class="view-btn" title="View"><i class="fas fa-eye"></i></a>
                        <a href="admin-inquiries.php" class="reply-btn" title="Reply"><i class="fas fa-reply"></i></a>
                        <a href="admin-inquiries.php" class="delete-btn" title="Delete"><i class="fas fa-trash-alt"></i></a>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>Michael Chen</td>
                    <td>m.chen@example.com</td>
                    <td>Insurance Support</td>
                    <td>Apr 15, 2025</td>
                    <td><span class="status-badge replied">Replied</span></td>
                    <td>
                      <div class="table-actions">
                        <a href="admin-inquiries.php" class="view-btn" title="View"><i class="fas fa-eye"></i></a>
                        <a href="admin-inquiries.php" class="reply-btn" title="Reply"><i class="fas fa-reply"></i></a>
                        <a href="admin-inquiries.php" class="delete-btn" title="Delete"><i class="fas fa-trash-alt"></i></a>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>Sarah Davis</td>
                    <td>sarah.d@example.com</td>
                    <td>Finance & Accounting</td>
                    <td>Apr 12, 2025</td>
                    <td><span class="status-badge closed">Closed</span></td>
                    <td>
                      <div class="table-actions">
                        <a href="admin-inquiries.php" class="view-btn" title="View"><i class="fas fa-eye"></i></a>
                        <a href="admin-inquiries.php" class="reply-btn" title="Reply"><i class="fas fa-reply"></i></a>
                        <a href="admin-inquiries.php" class="delete-btn" title="Delete"><i class="fas fa-trash-alt"></i></a>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Include the footer -->
      <?php include 'admin-footer.php'; ?>
    </main>
  </div>
</body>
</html>
