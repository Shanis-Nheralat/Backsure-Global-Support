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
    
    // Setup theme switcher
    setupThemeSwitcher();
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
 * This function is improved to ensure dropdowns work properly
 */
function fixSidebarDropdowns() {
    // Handle submenu toggles
    const submenuToggles = document.querySelectorAll('.has-submenu > a');
    
    submenuToggles.forEach(function(toggle) {
        // First remove existing event listeners to prevent duplicates
        const clonedToggle = toggle.cloneNode(true);
        toggle.parentNode.replaceChild(clonedToggle, toggle);
        
        // Add event listener to the new element
        clonedToggle.addEventListener('click', function(e) {
            if (this.getAttribute('href') === 'javascript:void(0)' || this.getAttribute('href') === '#') {
                e.preventDefault();
                e.stopPropagation();
                
                const parentLi = this.parentElement;
                const submenu = parentLi.querySelector('.submenu');
                
                // Toggle open class
                parentLi.classList.toggle('open');
                
                // Toggle submenu visibility
                if (submenu) {
                    if (parentLi.classList.contains('open')) {
                        submenu.style.display = 'block';
                        submenu.style.maxHeight = submenu.scrollHeight + 'px';
                    } else {
                        submenu.style.maxHeight = '0px';
                        // Use setTimeout to allow the transition to complete
                        setTimeout(function() {
                            if (!parentLi.classList.contains('open')) {
                                submenu.style.display = 'none';
                            }
                        }, 300);
                    }
                }
            }
        });
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
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
            }
        }
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
            e.stopPropagation();
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
 * Setup theme switcher functionality
 */
function setupThemeSwitcher() {
    const themeSelect = document.getElementById('theme-select');
    const autoDarkMode = document.getElementById('auto-dark-mode');
    
    if (themeSelect) {
        // Load saved theme
        const savedTheme = localStorage.getItem('admin-theme') || 'default';
        const prefersDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        const autoDarkEnabled = localStorage.getItem('auto-dark-mode') === 'true';
        
        // Set the select value
        themeSelect.value = savedTheme;
        
        // Set auto dark mode checkbox
        if (autoDarkMode) {
            autoDarkMode.checked = autoDarkEnabled;
        }
        
        // Apply theme
        if (autoDarkEnabled && prefersDarkMode) {
            document.documentElement.setAttribute('data-theme', 'dark');
            themeSelect.value = 'dark';
        } else {
            document.documentElement.setAttribute('data-theme', savedTheme);
        }
        
        // Handle theme selection
        themeSelect.addEventListener('change', function() {
            const theme = this.value;
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('admin-theme', theme);
            
            // If auto dark mode is enabled and user manually selects a theme, disable auto dark mode
            if (autoDarkMode && autoDarkMode.checked) {
                autoDarkMode.checked = false;
                localStorage.setItem('auto-dark-mode', 'false');
            }
        });
        
        // Handle auto dark mode toggle
        if (autoDarkMode) {
            autoDarkMode.addEventListener('change', function() {
                localStorage.setItem('auto-dark-mode', this.checked);
                
                if (this.checked) {
                    const prefersDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (prefersDarkMode) {
                        document.documentElement.setAttribute('data-theme', 'dark');
                        themeSelect.value = 'dark';
                    }
                    
                    // Add listener for system theme changes
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                        if (autoDarkMode.checked) {
                            const theme = e.matches ? 'dark' : 'default';
                            document.documentElement.setAttribute('data-theme', theme);
                            themeSelect.value = theme;
                        }
                    });
                } else {
                    // Apply the selected theme
                    const theme = themeSelect.value;
                    document.documentElement.setAttribute('data-theme', theme);
                }
            });
        }
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

/**
 * Format number with commas for thousands
 * 
 * @param {number} number Number to format
 * @return {string} Formatted number
 */
function formatNumber(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

/**
 * Format date to friendly format
 * 
 * @param {Date|string} date Date object or date string
 * @param {string} format Format string (full, short, time, relative)
 * @return {string} Formatted date
 */
function formatDate(date, format = 'full') {
    if (typeof date === 'string') {
        date = new Date(date);
    }
    
    if (!(date instanceof Date) || isNaN(date)) {
        return 'Invalid date';
    }
    
    switch (format) {
        case 'full':
            return date.toLocaleDateString(undefined, {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
        case 'short':
            return date.toLocaleDateString(undefined, {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            
        case 'time':
            return date.toLocaleTimeString(undefined, {
                hour: '2-digit',
                minute: '2-digit'
            });
            
        case 'datetime':
            return date.toLocaleDateString(undefined, {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            }) + ' ' + date.toLocaleTimeString(undefined, {
                hour: '2-digit',
                minute: '2-digit'
            });
            
        case 'relative':
            const now = new Date();
            const diffMs = now - date;
            const diffSec = Math.floor(diffMs / 1000);
            const diffMin = Math.floor(diffSec / 60);
            const diffHour = Math.floor(diffMin / 60);
            const diffDay = Math.floor(diffHour / 24);
            
            if (diffSec < 60) {
                return 'Just now';
            } else if (diffMin < 60) {
                return `${diffMin} minute${diffMin !== 1 ? 's' : ''} ago`;
            } else if (diffHour < 24) {
                return `${diffHour} hour${diffHour !== 1 ? 's' : ''} ago`;
            } else if (diffDay < 7) {
                return `${diffDay} day${diffDay !== 1 ? 's' : ''} ago`;
            } else {
                return date.toLocaleDateString(undefined, {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }
            
        default:
            return date.toLocaleDateString();
    }
}

// Add CSS for sidebar dropdown functionality
(() => {
    const style = document.createElement('style');
    style.textContent = `
        .sidebar-nav .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .sidebar-nav .has-submenu.open > .submenu {
            display: block;
            max-height: 1000px;
        }
        
        .sidebar-nav .has-submenu > a {
            position: relative;
        }
        
        .sidebar-nav .has-submenu > a .submenu-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            transition: transform 0.3s ease;
        }
        
        .sidebar-nav .has-submenu.open > a .submenu-icon {
            transform: translateY(-50%) rotate(90deg);
        }
    `;
    document.head.appendChild(style);
})();
