/**
 * Theme Switcher JavaScript
 * Save this as admin-theme-switcher.js
 */

document.addEventListener('DOMContentLoaded', function() {
    setupThemeSwitcher();
});

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
                        if (document.getElementById('auto-dark-mode').checked) {
                            const theme = e.matches ? 'dark' : 'default';
                            document.documentElement.setAttribute('data-theme', theme);
                            document.getElementById('theme-select').value = theme;
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
