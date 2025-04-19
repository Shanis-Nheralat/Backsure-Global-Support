<?php
/**
 * Admin Login
 * 
 * Handles authentication for admin users
 */

// Start session
session_start();

// Include database configuration
require_once 'db_config.php';

// Initialize error message
$error = '';

// Check if user is already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Redirect to admin dashboard
    header('Location: admin-dashboard.php');
    exit;
}

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Basic validation
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            // Connect to database
            $db = get_db_connection();
            
            // Check if admin table exists
            $table_exists = false;
            try {
                $stmt = $db->query("SHOW TABLES LIKE 'admins'");
                $table_exists = $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                // Table doesn't exist
            }
            
            if (!$table_exists) {
                $error = 'Admin system not set up. Please run the installation script first.';
            } else {
                // Check user credentials
                $stmt = $db->prepare("SELECT id, username, password, role FROM admins WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && password_verify($password, $user['password'])) {
                    // Login successful
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_role'] = $user['role'];
                    
                    // Update last login timestamp
                    $update_stmt = $db->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
                    $update_stmt->execute([$user['id']]);
                    
                    // Redirect to admin dashboard
                    header('Location: admin-dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid username or password.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Database error. Please try again later.';
            error_log('Login error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Backsure Global Support</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        body {
            font-family: 'Open Sans', Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            background-color: #062767;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .login-header h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        .login-body {
            padding: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #062767;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: inherit;
        }
        .form-group input:focus {
            border-color: #B19763;
            outline: none;
            box-shadow: 0 0 0 2px rgba(177, 151, 99, 0.2);
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .login-button {
            background-color: #062767;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 12px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            font-family: inherit;
            font-size: 1rem;
            transition: background-color 0.2s;
        }
        .login-button:hover {
            background-color: #051d4d;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #062767;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .form-icon {
            position: relative;
        }
        .form-icon i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 10px;
            color: #6c757d;
        }
        .form-icon input {
            padding-left: 35px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>BSG Support Admin</h1>
        </div>
        <div class="login-body">
            <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="form-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" required autofocus>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="form-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>
                
                <button type="submit" class="login-button">Sign In</button>
            </form>
            
            <div class="back-link">
                <a href="index.html">&larr; Back to Website</a>
            </div>
        </div>
    </div>
</body>
</html>