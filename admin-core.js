/**
 * Core JavaScript functionality for Admin Panel
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    initTooltips();
    
    // Initialize Bootstrap popovers
    initPopovers();
    
    // Initialize Bootstrap dropdowns
    initDropdowns();
    
    // Initialize Bootstrap modals
    initModals();
    
    // Setup sidebar functionality
    setupSidebar();
    
    // Setup user dropdown
    setupUserDropdown();
    
    // Setup notifications
    setupNotifications();
    
    // Setup data tables
    setupDataTables();
    
    // Setup form validation
    setupFormValidation();
    
    // Setup confirmation dialogs
    setupConfirmationDialogs();
    
    // Update current date in dashboard
    updateCurrentDate();
});

/**
 * Initialize Bootstrap tooltips
 */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialize Bootstrap popovers
 */
function initPopovers() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.forEach(function(popoverTriggerEl) {
        new bootstrap.Popover(popoverTriggerEl);
    });
}

/**
 * Initialize Bootstrap dropdowns
 */
function initDropdowns() {
    const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    dropdownElementList.forEach(function(dropdownToggleEl) {
        if (!dropdownToggleEl.hasAttribute('data-bs-toggle')) {
            new bootstrap.Dropdown(dropdownToggleEl);
        }
    });
}

/**
 * Initialize Bootstrap modals
 */
function initModals() {
    // Auto-show modals with data-auto-show attribute
    const autoShowModals = document.querySelectorAll('.modal[data-auto-show]');
    autoShowModals.forEach(function(modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    });
    
    // Handle modal confirm buttons
    document.querySelectorAll('[data-confirm-modal]').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-confirm-modal');
            const modal = new bootstrap.Modal(document.getElementById(modalId));
            
            // Set hidden input values if provided
            if (this.hasAttribute('data-id')) {
                const modalForm = document.querySelector(`#${modalId} form`);
                if (modalForm && modalForm.querySelector('input[name="id"]')) {
                    modalForm.querySelector('input[name="id"]').value = this.getAttribute('data-id');
                }
            }
            
            modal.show();
        });
    });
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
    
    // Toggle submenu
    const submenuToggles = document.querySelectorAll('.has-submenu > a');
    submenuToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            if (this.getAttribute('href') === 'javascript:void(0)') {
                e.preventDefault();
                
                const parentLi = this.parentElement;
                const submenu = parentLi.querySelector('.submenu');
                
                if (parentLi.classList.contains('open')) {
                    // Close submenu
                    parentLi.classList.remove('open');
                    if (submenu) {
                        submenu.style.maxHeight = '0px';
                    }
                } else {
                    // Open submenu
                    parentLi.classList.add('open');
                    if (submenu) {
                        submenu.style.maxHeight = submenu.scrollHeight + 'px';
                    }
                }
            }
        });
    });
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
 * Setup data tables
 */
function setupDataTables() {
    // Initialize DataTables if available
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.datatable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "Show _MENU_ entries per page",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)"
            }
        });
    }
}

/**
 * Setup form validation
 */
function setupFormValidation() {
    // Bootstrap 5 validation styles
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
}

/**
 * Setup confirmation dialogs
 */
function setupConfirmationDialogs() {
    // Handle delete confirmations
    document.querySelectorAll('[data-confirm]').forEach(function(element) {
        element.addEventListener('click', function(e) {
            if (!confirm(this.getAttribute('data-confirm') || 'Are you sure you want to perform this action?')) {
                e.preventDefault();
            }
        });
    });
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
    const message = customMessage || `Are you sure you want to delete this ${type}?`;
    
    if (confirm(message)) {
        window.location.href = `admin-${type}-delete.php?id=${id}`;
    }
}

/**
 * Show loading spinner
 * 
 * @param {HTMLElement} element Element to show spinner in
 * @param {string} size Size of spinner (sm, md, lg)
 * @param {string} color Color of spinner (primary, secondary, etc.)
 */
function showSpinner(element, size = 'md', color = 'primary') {
    const spinner = document.createElement('div');
    spinner.className = `spinner-border spinner-border-${size} text-${color}`;
    spinner.setAttribute('role', 'status');
    
    const span = document.createElement('span');
    span.className = 'visually-hidden';
    span.textContent = 'Loading...';
    
    spinner.appendChild(span);
    
    // Store original content
    const originalContent = element.innerHTML;
    element.setAttribute('data-original-content', originalContent);
    
    // Clear and add spinner
    element.innerHTML = '';
    element.appendChild(spinner);
    
    // Disable element if it's a button
    if (element.tagName === 'BUTTON' || element.tagName === 'A') {
        element.disabled = true;
    }
}

/**
 * Hide loading spinner and restore original content
 * 
 * @param {HTMLElement} element Element with spinner
 */
function hideSpinner(element) {
    const originalContent = element.getAttribute('data-original-content');
    if (originalContent) {
        element.innerHTML = originalContent;
        element.removeAttribute('data-original-content');
        
        // Re-enable element if it's a button
        if (element.tagName === 'BUTTON' || element.tagName === 'A') {
            element.disabled = false;
        }
    }
}
