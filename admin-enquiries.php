<?php
// Start session first thing - no whitespace before this
session_start();

// Block unauthorized users
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// All admin levels can access inquiries, but with different permissions
$canDelete = (isset($_SESSION['admin_role']) && ($_SESSION['admin_role'] === 'admin' || $_SESSION['admin_role'] === 'superadmin'));

// Include database configuration
require_once 'db_config.php';

// Get admin information from session
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin User';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'Administrator';

// Initialize variables
$inquiries = [];
$message = '';
$messageType = '';
$viewingInquiry = null;
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';
$sortField = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Valid sort fields and orders to prevent SQL injection
$validSortFields = ['id', 'name', 'email', 'subject', 'status', 'created_at'];
$validSortOrders = ['ASC', 'DESC'];

if (!in_array($sortField, $validSortFields)) {
    $sortField = 'created_at';
}
if (!in_array($sortOrder, $validSortOrders)) {
    $sortOrder = 'DESC';
}

// Database connection
try {
    $pdo = get_db_connection();
    
    // Create inquiries table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS inquiries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50),
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        response TEXT,
        admin_notes TEXT,
        status ENUM('new', 'replied', 'closed') DEFAULT 'new',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Process form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update Inquiry
        if (isset($_POST['action']) && $_POST['action'] === 'update') {
            $inquiryId = $_POST['inquiry_id'];
            $status = $_POST['status'];
            $response = trim($_POST['response']);
            $adminNotes = trim($_POST['admin_notes']);
            
            // Update inquiry
            $stmt = $pdo->prepare("UPDATE inquiries SET status = ?, response = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?");
            if ($stmt->execute([$status, $response, $adminNotes, $inquiryId])) {
                // Send email if response is provided and status is changed to 'replied'
                if (!empty($response) && $status === 'replied') {
                    // Get inquiry details for email
                    $stmt = $pdo->prepare("SELECT name, email, subject FROM inquiries WHERE id = ?");
                    $stmt->execute([$inquiryId]);
                    $inquiry = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Send email logic would go here
                    // For now, just log it
                    error_log("Email would be sent to: {$inquiry['name']} <{$inquiry['email']}> regarding: {$inquiry['subject']}");
                }
                
                $message = "Inquiry updated successfully.";
                $messageType = "success";
            } else {
                $message = "Error updating inquiry.";
                $messageType = "error";
            }
        }
        
        // Delete Inquiry
        if (isset($_POST['action']) && $_POST['action'] === 'delete' && $canDelete) {
            $inquiryId = $_POST['inquiry_id'];
            
            // Delete inquiry
            $stmt = $pdo->prepare("DELETE FROM inquiries WHERE id = ?");
            if ($stmt->execute([$inquiryId])) {
                $message = "Inquiry deleted successfully.";
                $messageType = "success";
            } else {
                $message = "Error deleting inquiry.";
                $messageType = "error";
            }
        }
        
        // Bulk Actions
        if (isset($_POST['action']) && $_POST['action'] === 'bulk' && isset($_POST['bulk_action']) && isset($_POST['selected_ids'])) {
            $bulkAction = $_POST['bulk_action'];
            $selectedIds = $_POST['selected_ids'];
            
            if (!empty($selectedIds)) {
                $ids = explode(',', $selectedIds);
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                
                if ($bulkAction === 'mark_replied' || $bulkAction === 'mark_closed') {
                    $newStatus = ($bulkAction === 'mark_replied') ? 'replied' : 'closed';
                    
                    $stmt = $pdo->prepare("UPDATE inquiries SET status = ?, updated_at = NOW() WHERE id IN ($placeholders)");
                    $params = array_merge([$newStatus], $ids);
                    
                    if ($stmt->execute($params)) {
                        $message = count($ids) . " inquiries marked as " . $newStatus . ".";
                        $messageType = "success";
                    } else {
                        $message = "Error updating inquiries.";
                        $messageType = "error";
                    }
                } elseif ($bulkAction === 'delete' && $canDelete) {
                    $stmt = $pdo->prepare("DELETE FROM inquiries WHERE id IN ($placeholders)");
                    
                    if ($stmt->execute($ids)) {
                        $message = count($ids) . " inquiries deleted.";
                        $messageType = "success";
                    } else {
                        $message = "Error deleting inquiries.";
                        $messageType = "error";
                    }
                }
            }
        }
    }
    
    // Handle view request from GET
    if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
        $inquiryId = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM inquiries WHERE id = ?");
        $stmt->execute([$inquiryId]);
        $viewingInquiry = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get all inquiries for display with filters and sorting
    $query = "SELECT * FROM inquiries";
    $params = [];
    
    // Apply status filter if set
    if (!empty($filterStatus)) {
        $query .= " WHERE status = ?";
        $params[] = $filterStatus;
    }
    
    // Apply sorting
    $query .= " ORDER BY $sortField $sortOrder";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Count inquiries by status
    $countNew = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'new'")->fetchColumn();
    $countReplied = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'replied'")->fetchColumn();
    $countClosed = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'closed'")->fetchColumn();
    $countTotal = $pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
    
} catch (PDOException $e) {
    $message = "Database error: " . $e->getMessage();
    $messageType = "error";
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="robots" content="noindex, nofollow">
  <title>Lead Management | Backsure Global Support</title>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <style>
    /* Reuse dashboard styles */
    :root {
      --primary-color: #062767;
      --primary-light: #3a5ca2;
      --primary-dark: #041c4a;
      --accent-color: #b19763;
      --accent-light: #cdb48e;
      --accent-dark: #97814c;
      --success-color: #1cc88a;
      --info-color: #36b9cc;
      --warning-color: #f6c23e;
      --danger-color: #e74a3b;
      --dark-color: #333333;
      --light-color: #f8f9fc;
      --gray-100: #f8f9fc;
      --gray-200: #eaecf4;
      --gray-300: #dddfeb;
      --gray-400: #d1d3e2;
      --gray-500: #b7b9cc;
      --gray-600: #858796;
      --gray-700: #6e707e;
      --gray-800: #5a5c69;
      --gray-900: #3a3b45;
      
      --sidebar-width: 250px;
      --sidebar-collapsed-width: 80px;
      --header-height: 60px;
      --transition-speed: 0.3s;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Open Sans', sans-serif;
      font-size: 14px;
      line-height: 1.6;
      color: var(--gray-800);
      background-color: var(--gray-100);
    }

    .admin-body {
      min-height: 100vh;
    }

    .admin-container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar Styles */
    .admin-sidebar {
      width: var(--sidebar-width);
      background-color: var(--primary-color);
      color: white;
      position: fixed;
      left: 0;
      top: 0;
      height: 100vh;