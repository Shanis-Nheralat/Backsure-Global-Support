<?php
session_start();

// 1. Database Connection – UPDATE if your host gives different values
$host = 'localhost';
$db = 'backsure_admin'; // Suggested DB name – change if needed
$user = 'shanis@backsureglobalsupport.com';
$pass = 'lBzymn$l2h1$wpYoo9RV';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// 2. Process login request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        header("Location: admin-login.html?error=empty");
        exit();
    }

    // 3. Fetch admin from database
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = :email AND status = 'active'");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 4. Verify password
    if ($user && password_verify($password, $user['password'])) {
        // 5. Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        // 6. Redirect to dashboard (all roles for now)
        header("Location: admin-dashboard.html");
        exit();
    } else {
        // 7. Invalid login
        header("Location: admin-login.html?error=invalid");
        exit();
    }
} else {
    header("Location: admin-login.html");
    exit();
}
?>
