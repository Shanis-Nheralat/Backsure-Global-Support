<?php
/**
 * Admin Panel Analytics System
 * 
 * This file provides functionality for tracking admin panel usage
 * and displaying insights about admin activity.
 * 
 * File: admin-analytics.php
 */

/**
 * Database table structure (create this in your database)
 * 
 * CREATE TABLE admin_activity_log (
 *     id INT AUTO_INCREMENT PRIMARY KEY,
 *     user_id INT NOT NULL,
 *     username VARCHAR(255) NOT NULL,
 *     action VARCHAR(255) NOT NULL,
 *     page VARCHAR(255) NOT NULL,
 *     details TEXT NULL,
 *     ip_address VARCHAR(45) NOT NULL,
 *     user_agent TEXT NULL,
 *     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 * 
 * CREATE INDEX idx_admin_activity_user ON admin_activity_log(user_id);
 * CREATE INDEX idx_admin_activity_action ON admin_activity_log(action);
 * CREATE INDEX idx_admin_activity_page ON admin_activity_log(page);
 * CREATE INDEX idx_admin_activity_date ON admin_activity_log(created_at);
 */

/**
 * Log an admin activity
 *
 * @param int $user_id Admin user ID
 * @param string $username Admin username
 * @param string $action Action performed (e.g., 'login', 'create', 'update', 'delete')
 * @param string $page Admin page where action occurred
 * @param array|string|null $details Additional details about the action
 * @return bool Success status
 */
function log_admin_activity($user_id, $username, $action, $page, $details = null) {
    global $pdo; // Your database connection
    
    // Convert details to JSON if it's an array
    if (is_array($details)) {
        $details = json_encode($details);
    }
    
    // Get IP address and user agent
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_activity_log 
                (user_id, username, action, page, details, ip_address, user_agent) 
            VALUES 
                (:user_id, :username, :action, :page, :details, :ip_address, :user_agent)
        ");
        
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':action', $action, PDO::PARAM_STR);
        $stmt->bindParam(':page', $page, PDO::PARAM_STR);
        $stmt->bindParam(':details', $details, PDO::PARAM_STR);
        $stmt->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
        $stmt->bindParam(':user_agent', $user_agent, PDO::PARAM_STR);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        // Log the error but don't disrupt the application
        error_log("Failed to log admin activity: " . $e->getMessage());
        return false;
    }
}

/**
 * Shorthand function to log page view
 *
 * @param string $page Page being viewed
 * @return bool Success status
 */
function log_page_view($page) {
    // Get user info from session
    $user_id = $_SESSION['admin_id'] ?? 0;
    $username = $_SESSION['admin_username'] ?? 'Unknown';
    
    return log_admin_activity($user_id, $username, 'view', $page);
}

/**
 * Log a CRUD operation
 *
 * @param string $action CRUD action (create, read, update, delete)
 * @param string $page Admin page
 * @param string $entity_type Type of entity (e.g., 'user', 'post', 'setting')
 * @param mixed $entity_id ID of the affected entity
 * @param array $details Additional details
 * @return bool Success status
 */
function log_crud_operation($action, $page, $entity_type, $entity_id, $details = []) {
    // Get user info from session
    $user_id = $_SESSION['admin_id'] ?? 0;
    $username = $_SESSION['admin_username'] ?? 'Unknown';
    
    // Add entity information to details
    $details['entity_type'] = $entity_type;
    $details['entity_id'] = $entity_id;
    
    return log_admin_activity($user_id, $username, $action, $page, $details);
}

/**
 * Get recent activity logs
 *
 * @param int $limit Maximum number of records to return
 * @param int $offset Offset for pagination
 * @return array Activity records
 */
function get_recent_activity($limit = 50, $offset = 0) {
    global $pdo; // Your database connection
    
    try {
        $stmt = $pdo->prepare("
            SELECT *
            FROM admin_activity_log
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Failed to get recent activity: " . $e->getMessage());
        return [];
    }
}

/**
 * Get activity counts by page
 *
 * @param string $date_from Start date (YYYY-MM-DD)
 * @param string $date_to End date (YYYY-MM-DD)
 * @return array Page view statistics
 */
function get_page_view_stats($date_from = null, $date_to = null) {
    global $pdo; // Your database connection
    
    // Set default date range if not provided (last 30 days)
    if (!$date_from) {
        $date_from = date('Y-m-d', strtotime('-30 days'));
    }
    
    if (!$date_to) {
        $date_to = date('Y-m-d');
    }
    
    // Add time to make the end date inclusive
    $date_to = $date_to . ' 23:59:59';
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                page, 
                COUNT(*) as view_count
            FROM admin_activity_log
            WHERE 
                action = 'view' AND
                created_at BETWEEN :date_from AND :date_to
            GROUP BY page
            ORDER BY view_count DESC
        ");
        
        $stmt->bindParam(':date_from', $date_from, PDO::PARAM_STR);
        $stmt->bindParam(':date_to', $date_to, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Failed to get page view stats: " . $e->getMessage());
        return [];
    }
}

/**
 * Get activity data for a specific user
 *
 * @param int $user_id User ID
 * @param int $limit Maximum number of records to return
 * @return array User activity data
 */
function get_user_activity($user_id, $limit = 50) {
    global $pdo; // Your database connection
    
    try {
        $stmt = $pdo->prepare("
            SELECT *
            FROM admin_activity_log
            WHERE user_id = :user_id
            ORDER BY created_at DESC
            LIMIT :limit
        ");
        
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Failed to get user activity: " . $e->getMessage());
        return [];
    }
}

/**
 * Get count of actions by type over a time period
 *
 * @param string $date_from Start date (YYYY-MM-DD)
 * @param string $date_to End date (YYYY-MM-DD)
 * @return array Action statistics
 */
function get_action_stats($date_from = null, $date_to = null) {
    global $pdo; // Your database connection
    
    // Set default date range if not provided (last 30 days)
    if (!$date_from) {
        $date_from = date('Y-m-d', strtotime('-30 days'));
    }
    
    if (!$date_to) {
        $date_to = date('Y-m-d');
    }
    
    // Add time to make the end date inclusive
    $date_to = $date_to . ' 23:59:59';
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                action, 
                COUNT(*) as count
            FROM admin_activity_log
            WHERE created_at BETWEEN :date_from AND :date_to
            GROUP BY action
            ORDER BY count DESC
        ");
        
        $stmt->bindParam(':date_from', $date_from, PDO::PARAM_STR);
        $stmt->bindParam(':date_to', $date_to, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Failed to get action stats: " . $e->getMessage());
        return [];
    }
}

/**
 * Get daily activity counts for charting
 *
 * @param int $days Number of days to include
 * @return array Daily activity counts
 */
function get_daily_activity_chart_data($days = 30) {
    global $pdo; // Your database connection
    
    $date_from = date('Y-m-d', strtotime("-{$days} days"));
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as count
            FROM admin_activity_log
            WHERE created_at >= :date_from
            GROUP BY DATE(created_at)
            ORDER BY date
        ");
        
        $stmt->bindParam(':date_from', $date_from, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Failed to get daily activity data: " . $e->getMessage());
        return [];
    }
}

/**
 * Create an analytics dashboard
 * Can be included in admin-dashboard.php or as a dedicated analytics page
 *
 * @param string $date_from Start date (YYYY-MM-DD)
 * @param string $date_to End date (YYYY-MM-DD)
 * @return string HTML for analytics dashboard
 */
function render_analytics_dashboard($date_from = null, $date_to = null) {
    // Set default date range if not provided (last 30 days)
    if (!$date_from) {
        $date_from = date('Y-m-d', strtotime('-30 days'));
    }
    
    if (!$date_to) {
        $date_to = date('Y-m-d');
    }
    
    // Get analytics data
    $page_stats = get_page_view_stats($date_from, $date_to);
    $action_stats = get_action_stats($date_from, $date_to);
    $daily_data = get_daily_activity_chart_data(30);
    $recent_activity = get_recent_activity(10);
    
    // Format date labels and data points for chart
    $date_labels = [];
    $activity_counts = [];
    
    foreach ($daily_data as $day) {
        $date_labels[] = date('M d', strtotime($day['date']));
        $activity_counts[] = (int)$day['count'];
    }
    
    // Start building HTML
    $html = '<div class="analytics-dashboard">';
    
    // Date range selector
    $html .= '<div class="card mb-4">';
    $html .= '<div class="card-header">Date Range</div>';
    $html .= '<div class="card-body">';
    $html .= '<form class="form-inline" method="GET">';
    $html .= '<div class="form-group mr-3">';
    $html .= '<label for="date_from" class="mr-2">From:</label>';
    $html .= '<input type="date" class="form-control" id="date_from" name="date_from" value="' . $date_from . '">';
    $html .= '</div>';
    $html .= '<div class="form-group mr-3">';
    $html .= '<label for="date_to" class="mr-2">To:</label>';
    $html .= '<input type="date" class="form-control" id="date_to" name="date_to" value="' . $date_to . '">';
    $html .= '</div>';
    $html .= '<button type="submit" class="btn btn-primary">Apply</button>';
    $html .= '</form>';
    $html .= '</div>'; // End card body
    $html .= '</div>'; // End card
    
    // Charts row
    $html .= '<div class="row">';
    
    // Activity chart
    $html .= '<div class="col-md-8">';
    $html .= '<div class="card mb-4">';
    $html .= '<div class="card-header">Daily Activity</div>';
    $html .= '<div class="card-body">';
    $html .= '<div class="chart-container" style="position: relative; height:300px;">';
    $html .= '<canvas id="activityChart"></canvas>';
    $html .= '</div>';
    $html .= '</div>'; // End card body
    $html .= '</div>'; // End card
    $html .= '</div>'; // End col
    
    // Action stats
    $html .= '<div class="col-md-4">';
    $html .= '<div class="card mb-4">';
    $html .= '<div class="card-header">Actions</div>';
    $html .= '<div class="card-body">';
    $html .= '<div class="chart-container" style="position: relative; height:300px;">';
    $html .= '<canvas id="actionChart"></canvas>';
    $html .= '</div>';
    $html .= '</div>'; // End card body
    $html .= '</div>'; // End card
    $html .= '</div>'; // End col
    
    $html .= '</div>'; // End charts row
    
    // Second row - page stats and recent activity
    $html .= '<div class="row">';
    
    // Popular pages
    $html .= '<div class="col-md-6">';
    $html .= '<div class="card mb-4">';
    $html .= '<div class="card-header">Most Visited Pages</div>';
    $html .= '<div class="card-body">';
    $html .= '<div class="table-responsive">';
    $html .= '<table class="table table-sm">';
    $html .= '<thead><tr><th>Page</th><th>Views</th></tr></thead>';
    $html .= '<tbody>';
    
    foreach ($page_stats as $page) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($page['page']) . '</td>';
        $html .= '<td>' . htmlspecialchars($page['view_count']) . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</div>'; // End table-responsive
    $html .= '</div>'; // End card body
    $html .= '</div>'; // End card
    $html .= '</div>'; // End col
    
    // Recent activity
    $html .= '<div class="col-md-6">';
    $html .= '<div class="card mb-4">';
    $html .= '<div class="card-header">Recent Activity</div>';
    $html .= '<div class="card-body">';
    $html .= '<div class="table-responsive">';
    $html .= '<table class="table table-sm">';
    $html .= '<thead><tr><th>User</th><th>Action</th><th>Page</th><th>Time</th></tr></thead>';
    $html .= '<tbody>';
    
    foreach ($recent_activity as $activity) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($activity['username']) . '</td>';
        $html .= '<td>' . htmlspecialchars($activity['action']) . '</td>';
        $html .= '<td>' . htmlspecialchars($activity['page']) . '</td>';
        $html .= '<td>' . date('M d, H:i', strtotime($activity['created_at'])) . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</div>'; // End table-responsive
    $html .= '</div>'; // End card body
    $html .= '</div>'; // End card
    $html .= '</div>'; // End col
    
    $html .= '</div>'; // End second row
    
    // JavaScript for charts
    $html .= '<script>';
    $html .= 'document.addEventListener("DOMContentLoaded", function() {';
    
    // Daily activity chart
    $html .= 'const activityCtx = document.getElementById("activityChart").getContext("2d");';
    $html .= 'const activityChart = new Chart(activityCtx, {';
    $html .= '    type: "line",';
    $html .= '    data: {';
    $html .= '        labels: ' . json_encode($date_labels) . ',';
    $html .= '        datasets: [{';
    $html .= '            label: "Admin Actions",';
    $html .= '            data: ' . json_encode($activity_counts) . ',';
    $html .= '            borderColor: "rgba(52, 152, 219, 1)",';
    $html .= '            backgroundColor: "rgba(52, 152, 219, 0.1)",';
    $html .= '            borderWidth: 2,';
    $html .= '            tension: 0.3,';
    $html .= '            fill: true';
    $html .= '        }]';
    $html .= '    },';
    $html .= '    options: {';
    $html .= '        responsive: true,';
    $html .= '        maintainAspectRatio: false,';
    $html .= '        scales: {';
    $html .= '            y: {';
    $html .= '                beginAtZero: true,';
    $html .= '                grid: {';
    $html .= '                    color: "rgba(0, 0, 0, 0.05)"';
    $html .= '                }';
    $html .= '            },';
    $html .= '            x: {';
    $html .= '                grid: {';
    $html .= '                    display: false';
    $html .= '                }';
    $html .= '            }';
    $html .= '        }';
    $html .= '    }';
    $html .= '});';
    
    // Action pie chart
    $html .= 'const actionCtx = document.getElementById("actionChart").getContext("2d");';
    
    // Prepare data for action chart
    $action_labels = [];
    $action_counts = [];
    $action_colors = [
        'view' => 'rgba(52, 152, 219, 0.7)',    // Blue for views
        'create' => 'rgba(46, 204, 113, 0.7)',  // Green for create
        'update' => 'rgba(241, 196, 15, 0.7)',  // Yellow for update
        'delete' => 'rgba(231, 76, 60, 0.7)',   // Red for delete
        'login' => 'rgba(155, 89, 182, 0.7)',   // Purple for login
        'logout' => 'rgba(52, 73, 94, 0.7)'     // Dark blue for logout
    ];
    $action_background_colors = [];
    
    foreach ($action_stats as $action) {
        $action_labels[] = ucfirst($action['action']);
        $action_counts[] = (int)$action['count'];
        
        // Get color for this action, or use a default
        $color = isset($action_colors[$action['action']]) 
            ? $action_colors[$action['action']] 
            : 'rgba(149, 165, 166, 0.7)'; // Default gray
            
        $action_background_colors[] = $color;
    }
    
    $html .= 'const actionChart = new Chart(actionCtx, {';
    $html .= '    type: "doughnut",';
    $html .= '    data: {';
    $html .= '        labels: ' . json_encode($action_labels) . ',';
    $html .= '        datasets: [{';
    $html .= '            data: ' . json_encode($action_counts) . ',';
    $html .= '            backgroundColor: ' . json_encode($action_background_colors) . ',';
    $html .= '            borderWidth: 1';
    $html .= '        }]';
    $html .= '    },';
    $html .= '    options: {';
    $html .= '        responsive: true,';
    $html .= '        maintainAspectRatio: false,';
    $html .= '        plugins: {';
    $html .= '            legend: {';
    $html .= '                position: "bottom",';
    $html .= '                labels: {';
    $html .= '                    boxWidth: 12';
    $html .= '                }';
    $html .= '            }';
    $html .= '        }';
    $html .= '    }';
    $html .= '});';
    
    $html .= '});'; // End DOMContentLoaded
    $html .= '</script>';
    
    $html .= '</div>'; // End analytics-dashboard
    
    return $html;
}

/**
 * Implementation for Matomo/Piwik Analytics Integration
 * This is an alternative to self-hosted analytics
 */
function get_matomo_tracking_code($site_id, $matomo_url) {
    return "
    <!-- Matomo -->
    <script>
      var _paq = window._paq = window._paq || [];
      _paq.push(['setCustomDimension', 1, 'admin']); // Track that this is admin panel
      _paq.push(['trackPageView']);
      _paq.push(['enableLinkTracking']);
      (function() {
        var u='" . $matomo_url . "';
        _paq.push(['setTrackerUrl', u+'matomo.php']);
        _paq.push(['setSiteId', '" . $site_id . "']);
        var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
        g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
      })();
    </script>
    <!-- End Matomo Code -->
    ";
}

/**
 * Implementation for Plausible Analytics Integration
 * This is a privacy-friendly alternative
 */
function get_plausible_tracking_code($domain) {
    return "
    <!-- Plausible Analytics -->
    <script defer data-domain='" . $domain . "' src='https://plausible.io/js/script.js'></script>
    <!-- End Plausible Analytics -->
    ";
}

/**
 * Function to track custom events using Plausible
 * Add this to admin-core.js
 */
function get_plausible_events_js() {
    return "
    /**
     * Track custom events with Plausible
     * @param {string} event Event name
     * @param {object} props Event properties
     */
    function trackEvent(event, props = {}) {
        if (window.plausible) {
            window.plausible(event, { props });
        }
    }
    
    // Example usage:
    // Track form submissions
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const formId = this.id || 'unknown';
            trackEvent('FormSubmit', { form: formId });
        });
    });
    
    // Track button clicks
    document.querySelectorAll('button, .btn').forEach(button => {
        button.addEventListener('click', function() {
            const buttonText = this.textContent.trim();
            const buttonId = this.id || buttonText;
            trackEvent('ButtonClick', { button: buttonId });
        });
    });
    
    // Track tab changes
    document.querySelectorAll('.nav-tabs .nav-link').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('href') || this.textContent.trim();
            trackEvent('TabView', { tab: tabId });
        });
    });
    ";
}

/**
 * Usage examples:
 * 
 * // In each admin page after authentication
 * log_page_view('admin-dashboard.php');
 * 
 * // After creating a new user
 * log_crud_operation('create', 'admin-users.php', 'user', $new_user_id, [
 *     'username' => $username,
 *     'email' => $email,
 *     'role' => $role
 * ]);
 * 
 * // After updating a blog post
 * log_crud_operation('update', 'admin-blog.php', 'post', $post_id, [
 *     'title' => $title,
 *     'status' => $status
 * ]);
 *
 * // After deleting something
 * log_crud_operation('delete', 'admin-services.php', 'service', $service_id);
 * 
 * // On login page after successful login
 * log_admin_activity($user_id, $username, 'login', 'admin-login.php');
 * 
 * // On logout
 * log_admin_activity($user_id, $username, 'logout', 'admin-logout.php');
 * 
 * // Add to admin-head.php to include Matomo/Plausible analytics
 * echo get_matomo_tracking_code(1, 'https://analytics.example.com/');
 * // OR
 * echo get_plausible_tracking_code('admin.example.com');
 */
