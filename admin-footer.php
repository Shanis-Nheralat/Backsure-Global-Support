<!-- Admin Footer -->
        <footer class="admin-footer">
            <div class="footer-left">
                <p>&copy; <?php echo date('Y'); ?> Backsure Global Support. All rights reserved.</p>
            </div>
            <div class="footer-right">
                <span>Admin Panel v1.0</span>
            </div>
        </footer>
    </main><!-- End .admin-main -->
</div><!-- End .admin-container -->

<!-- JavaScript Files -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Initialize dashboard functionality
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const adminContainer = document.querySelector('.admin-container');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            adminContainer.classList.toggle('sidebar-collapsed');
        });
    }
    
    // User dropdown
    const userDropdownToggle = document.getElementById('user-dropdown-toggle');
    const userDropdown = document.getElementById('user-dropdown');
    
    if (userDropdownToggle && userDropdown) {
        userDropdownToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (userDropdown.classList.contains('show') && !userDropdownToggle.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('show');
            }
        });
    }
    
    // Submenu toggle
    const submenuItems = document.querySelectorAll('.has-submenu > a');
    
    submenuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.parentElement;
            
            // Toggle current submenu
            parent.classList.toggle('open');
            const submenu = parent.querySelector('.submenu');
            
            if (submenu) {
                if (parent.classList.contains('open')) {
                    submenu.style.maxHeight = submenu.scrollHeight + 'px';
                } else {
                    submenu.style.maxHeight = null;
                }
            }
        });
    });
    
    // Make sure active submenus are visible
    const activeMenuItems = document.querySelectorAll('.sidebar-nav .active');
    activeMenuItems.forEach(item => {
        // Find parent submenu if exists
        const parentSubmenu = item.closest('.submenu');
        if (parentSubmenu) {
            // Find parent menu item
            const parentMenuItem = parentSubmenu.closest('.has-submenu');
            if (parentMenuItem) {
                // Open the submenu
                parentMenuItem.classList.add('open');
                parentSubmenu.style.maxHeight = parentSubmenu.scrollHeight + 'px';
            }
        }
    });
});

<?php if (isset($page_scripts)): ?>
// Page-specific scripts
<?php echo $page_scripts; ?>
<?php endif; ?>
</script>
</body>
</html>
