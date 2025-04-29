/**
 * Core JavaScript functionality for Admin Panel
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap components
    initBootstrapComponents();
    
    // Setup sidebar functionality
    setupSidebar();
    
    // Setup user dropdown
    setupUserDropdown();
    
    // Setup notifications
    setupNotifications();
    
    // Update current date in dashboard
    updateCurrentDate();
});

/**
 * Initialize Bootstrap components
 */
function initBootstrapComponents() {
    // Initialize tooltips
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Initialize popovers
    if (typeof bootstrap !== 'undefined' && bootstrap.Popover) {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }
    
    // Initialize dropdowns that don't have data-bs-toggle
    if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
        const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle:not([data-bs-toggle])'));
        dropdownElementList.forEach(function(dropdownToggleEl) {
            new bootstrap.Dropdown(dropdownToggleEl);
        });
    }
}

/**
 * Setup sidebar functionality
 */
function setupSidebar() {
    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const adminContainer = document.querySelector('.admin-container');
    const sidebarBackdrop = document.querySelector('.sidebar-backdrop');
    
    if (sidebarToggle && adminContainer) {
        sidebarToggle.addEventListener('click', function() {
            adminContainer.classList.toggle('sidebar-collapsed');
            
            // For mobile
            if (window.innerWidth < 992) {
                adminContainer.classList.toggle('sidebar-expanded');
                if (sidebarBackdrop) {
                    sidebarBackdrop.classList.toggle('show');
                }
            }
        });
    }
    
    // Close sidebar when clicking on backdrop (mobile)
    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', function() {
            adminContainer.classList.remove('sidebar-expanded');
            this.classList.remove('show');
        });
    }
    
    // Fix sidebar dropdown functionality
    fixSidebarDropdowns();
}

/**
 * Fix sidebar dropdown toggle functionality
 */
function fixSidebarDropdowns() {
    // Handle submenu toggles
    const submenuToggles = document.querySelectorAll('.has-submenu > a');
    
    submenuToggles.forEach(function(toggle) {
        // Remove any existing event listeners to prevent duplicates
        toggle.removeEventListener('click', handleSubmenuToggle);
        
        // Add the click event listener
        toggle.addEventListener('click', handleSubmenuToggle);
    });
    
    // Auto-open submenu if it contains active item
    document.querySelectorAll('.sidebar-nav .submenu .active').forEach(function(activeItem) {
        const parentLi = activeItem.closest('.has-submenu');
        if (parentLi) {
            parentLi.classList.add('open');
            
            // Make sure the submenu is visible
            const submenu = parentLi.querySelector('.submenu');
            if (submenu) {
                submenu.style.display = 'block';
            }
        }
    });
}

/**
 * Handle submenu toggle click
 * 
 * @param {Event} e Click event
 */
function handleSubmenuToggle(e) {
    if (this.getAttribute('href') === 'javascript:void(0)' || this.getAttribute('href') === '#') {
        e.preventDefault();
        
        const parentLi = this.parentElement;
        const submenu = parentLi.querySelector('.submenu');
        
        // Toggle open class
        parentLi.classList.toggle('open');
        
        // Toggle submenu visibility
        if (submenu) {
            if (parentLi.classList.contains('open')) {
                submenu.style.display = 'block';
                // Use setTimeout to ensure the transition works
                setTimeout(function() {
                    submenu.style.maxHeight = submenu.scrollHeight + 'px';
                }, 10);
            } else {
                submenu.style.maxHeight = '0px';
                // Hide the submenu after transition
                setTimeout(function() {
                    if (!parentLi.classList.contains('open')) {
                        submenu.style.display = 'none';
                    }
                }, 300); // Transition duration
            }
        }
    }
}

/**
 * Setup user dropdown
 */
function setupUserDropdown() {
    const userDropdownToggle = document.getElementById('user-dropdown-toggle');
    const userDropdown = document.getElementById('user-dropdown');
    
    if (userDropdownToggle && userDropdown) {
        userDropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            userDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdownToggle.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('show');
            }
        });
    }
}

/**
 * Setup notifications
 */
function setupNotifications() {
    // Auto dismiss notifications with timeout
    document.querySelectorAll('.alert[data-timeout]').forEach(function(alert) {
        const timeout = parseInt(alert.getAttribute('data-timeout'));
        if (!isNaN(timeout) && timeout > 0) {
            setTimeout(function() {
                const closeBtn = alert.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.click();
                } else {
                    alert.classList.add('fade');
                    setTimeout(function() { alert.remove(); }, 150);
                }
            }, timeout);
        }
    });
    
    // Mark all notifications as read
    const markAllReadBtn = document.getElementById('mark-all-read');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // AJAX call to mark all notifications as read
            fetch('admin-ajax.php?action=mark_all_notifications_read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove unread class from all notification items
                    document.querySelectorAll('.notification-item.unread').forEach(function(item) {
                        item.classList.remove('unread');
                    });
                    
                    // Remove badge
                    const badge = document.querySelector('.notification-btn .badge');
                    if (badge) {
                        badge.remove();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }
}

/**
 * Update current date in dashboard
 */
function updateCurrentDate() {
    const currentDateElement = document.getElementById('current-date');
    if (currentDateElement) {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        currentDateElement.textContent = now.toLocaleDateString(undefined, options);
    }
}

/**
 * Confirmation dialog for delete actions
 * 
 * @param {string} type Resource type (e.g., 'user', 'post')
 * @param {string|number} id Resource ID
 * @param {string} customMessage Optional custom message
 */
function confirmDelete(type, id, customMessage) {
    const message
