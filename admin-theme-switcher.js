/**
 * JavaScript for theme selection
 * Handles theme switching and persistence using Bootstrap framework
 */

document.addEventListener('DOMContentLoaded', function() {
    // Theme selector
    const themeSelector = document.getElementById('theme-selector');
    const autoDarkMode = document.getElementById('auto-dark-mode');
    
    // Load saved theme
    const savedTheme = localStorage.getItem('admin_theme');
    const autoDarkEnabled = localStorage.getItem('admin_auto_dark') === 'true';
    
    // Set initial theme
    if (autoDarkEnabled) {
        if (autoDarkMode) {
            autoDarkMode.checked = true;
        }
        applyAutoDarkMode();
    } else if (savedTheme) {
        setTheme(savedTheme);
        if (themeSelector) {
            themeSelector.value = savedTheme;
        }
    }
    
    // Theme selector change event
    if (themeSelector) {
        themeSelector.addEventListener('change', function() {
            const selectedTheme = this.value;
            setTheme(selectedTheme);
            localStorage.setItem('admin_theme', selectedTheme);
            
            // Disable auto dark mode when manually changing theme
            if (autoDarkMode && autoDarkMode.checked) {
                autoDarkMode.checked = false;
                localStorage.setItem('admin_auto_dark', 'false');
            }
        });
    }
    
    // Auto dark mode toggle
    if (autoDarkMode) {
        autoDarkMode.addEventListener('change', function() {
            if (this.checked) {
                localStorage.setItem('admin_auto_dark', 'true');
                applyAutoDarkMode();
            } else {
                localStorage.setItem('admin_auto_dark', 'false');
                
                // Restore saved theme or default
                const savedTheme = localStorage.getItem('admin_theme') || 'default';
                setTheme(savedTheme);
                if (themeSelector) {
                    themeSelector.value = savedTheme;
                }
            }
        });
        
        // Listen for system preference changes
        if (window.matchMedia) {
            const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            
            // Add change listener
            try {
                // Chrome & Firefox
                darkModeMediaQuery.addEventListener('change', function(e) {
                    if (autoDarkMode && autoDarkMode.checked) {
                        applyAutoDarkMode();
                    }
                });
            } catch (error) {
                try {
                    // Safari
                    darkModeMediaQuery.addListener(function(e) {
                        if (autoDarkMode && autoDarkMode.checked) {
                            applyAutoDarkMode();
                        }
                    });
                } catch (error2) {
                    console.error('Error setting up dark mode listener:', error2);
                }
            }
        }
    }
    
    // Handle sidebar toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const adminContainer = document.querySelector('.admin-container');
    const sidebarBackdrop = document.querySelector('.sidebar-backdrop');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            if (adminContainer) {
                adminContainer.classList.toggle('sidebar-collapsed');
            }
        });
    }
    
    // Close sidebar when clicking on backdrop (mobile)
    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', function() {
            if (adminContainer) {
                adminContainer.classList.remove('sidebar-expanded');
            }
        });
    }
    
    // Handle submenu toggles
    const submenuToggles = document.querySelectorAll('.has-submenu > a');
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            if (this.getAttribute('href') === 'javascript:void(0)') {
                e.preventDefault();
                const parentLi = this.parentElement;
                const submenu = parentLi.querySelector('.submenu');
                
                // Close other open submenus
                document.querySelectorAll('.has-submenu.open').forEach(item => {
                    if (item !== parentLi) {
                        item.classList.remove('open');
                        const otherSubmenu = item.querySelector('.submenu');
                        if (otherSubmenu) {
                            otherSubmenu.style.maxHeight = null;
                        }
                    }
                });
                
                // Toggle current submenu
                parentLi.classList.toggle('open');
                
                // Animate submenu height
                if (submenu) {
                    if (parentLi.classList.contains('open')) {
                        submenu.style.maxHeight = submenu.scrollHeight + 'px';
                    } else {
                        submenu.style.maxHeight = null;
                    }
                }
            }
        });
    });
    
    // Initialize Bootstrap tooltips
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Initialize user dropdown
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
});

/**
 * Apply theme to document
 * 
 * @param {string} theme Theme name
 */
function setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
}

/**
 * Apply theme based on system preference
 */
function applyAutoDarkMode() {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        setTheme('dark');
        const themeSelector = document.getElementById('theme-selector');
        if (themeSelector) {
            themeSelector.value = 'dark';
        }
    } else {
        setTheme('default');
        const themeSelector = document.getElementById('theme-selector');
        if (themeSelector) {
            themeSelector.value = 'default';
        }
    }
}
