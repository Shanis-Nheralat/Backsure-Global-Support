<?php
/**
 * Admin Footer Component
 * This file contains the footer for the admin panel
 */

// Set current year
$current_year = date('Y');

// Set company name
$company_name = "Backsure Global Support";

// Set version
$admin_version = "1.0";
?>
<!-- Admin Footer -->
<footer class="admin-footer">
  <div class="footer-left">
    <p>&copy; <?php echo $current_year; ?> <?php echo htmlspecialchars($company_name); ?>. All rights reserved.</p>
  </div>
  <div class="footer-right">
    <span>Admin Panel v<?php echo htmlspecialchars($admin_version); ?></span>
  </div>
</footer>

<!-- Common JavaScript Files -->
<script src="assets/js/admin-core.js"></script>
