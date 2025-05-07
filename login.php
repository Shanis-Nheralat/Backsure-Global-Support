<?php
/**
 * Admin Login Page
 * Handles authentication for the admin panel
 */

// Include required files
require_once 'db_config.php';
require_once 'admin-auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect to dashboard
if (is_admin_logged_in()) {
    header("Location: admin-dashboard.php");
    exit();
}

// Initialize variables
$error = '';
$username = '';

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        try {
            // Get database connection
            $db = get_db_connection();
            
            // Get user from database
            $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            // Verify user and password
            if ($user && password_verify($password, $user['password'])) {
                // Check if user is active
                if ($user['status'] != 0) {
                    $error = "Your account is currently inactive. Please contact the administrator.";
                } else {
                    // Set session variables
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_role'] = $user['role'] ? $user['role'] : 'admin'; // Default to admin if role is empty
                    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                    
                    // Update last login time
                    $updateStmt = $db->prepare("UPDATE admins SET last_login = NOW(), login_attempts = 0 WHERE id = ?");
                    $updateStmt->execute([$user['id']]);
                    
                    // Redirect to dashboard or original destination
                    $redirect = $_SESSION['redirect_after_login'] ?? 'admin-dashboard.php';
                    unset($_SESSION['redirect_after_login']);
                    header("Location: $redirect");
                    exit();
                }
            } else {
                // Failed login attempt
                if ($user) {
                    // Increment login attempts
                    $attempts = $user['login_attempts'] + 1;
                    $updateStmt = $db->prepare("UPDATE admins SET login_attempts = ?, last_attempt_time = NOW() WHERE id = ?");
                    $updateStmt->execute([$attempts, $user['id']]);
                }
                
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $error = "Database error. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Backsure Global Support</title>
    <!-- Include your CSS files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-logo">
                <img src="assets/images/logo.png" alt="Backsure Global Logo">
                <h2>Backsure Global Support</h2>
                <h4>Admin Panel</h4>
            </div>
            
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="login-form">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember Me</label>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </div>
                <div class="login-footer mt-3 text-center">
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- JavaScript Files -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
