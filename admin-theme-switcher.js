/**
 * Admin Panel Theme Switcher
 * Allows switching between different themes and persists the selection
 * File: admin-theme-switcher.js
 */

document.addEventListener('DOMContentLoaded', function() {
  initThemeSwitcher();
});

/**
 * Initialize the theme switcher functionality
 */
function initThemeSwitcher() {
  // Set the initial theme from local storage or default
  const currentTheme = localStorage.getItem('admin_theme') || 'default';
  setTheme(currentTheme);
  
  // Find theme switcher dropdown (add this in admin-header.php)
  const themeSwitcher = document.getElementById('theme-switcher');
  if (themeSwitcher) {
    // Set the dropdown to the current theme
    themeSwitcher.value = currentTheme;
    
    // Add change listener
    themeSwitcher.addEventListener('change', function() {
      setTheme(this.value);
      localStorage.setItem('admin_theme', this.value);
    });
  }
  
  // Check if auto dark mode is enabled
  const autoDarkMode = localStorage.getItem('auto_dark_mode') === 'true';
  if (autoDarkMode) {
    const darkModeToggle = document.getElementById('auto-dark-mode');
    if (darkModeToggle) {
      darkModeToggle.checked = true;
      setupAutoDarkMode();
    }
  }
  
  // Auto dark mode toggle
  const darkModeToggle = document.getElementById('auto-dark-mode');
  if (darkModeToggle) {
    darkModeToggle.addEventListener('change', function() {
      if (this.checked) {
        localStorage.setItem('auto_dark_mode', 'true');
        setupAutoDarkMode();
      } else {
        localStorage.setItem('auto_dark_mode', 'false');
        // Remove the media query listener
        window.matchMedia('(prefers-color-scheme: dark)').removeEventListener('change', handleColorSchemeChange);
        // Restore the previously selected theme
        setTheme(localStorage.getItem('admin_theme') || 'default');
      }
    });
  }
}

/**
 * Set the active theme
 * @param {string} theme The theme name
 */
function setTheme(theme) {
  // Remove any existing theme
  document.documentElement.removeAttribute('data-theme');
  
  // If theme isn't default, set the data-theme attribute
  if (theme !== 'default') {
    document.documentElement.setAttribute('data-theme', theme);
  }
  
  // Update the theme switcher dropdown if it exists
  const themeSwitcher = document.getElementById('theme-switcher');
  if (themeSwitcher) {
    themeSwitcher.value = theme;
  }
  
  // Dispatch an event so other scripts can react to theme changes
  document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
}

/**
 * Setup automatic dark mode based on system preference
 */
function setupAutoDarkMode() {
  // Store the user's manually selected theme as a backup
  const userTheme = localStorage.getItem('admin_theme') || 'default';
  localStorage.setItem('user_selected_theme', userTheme);
  
  // Check if the user prefers dark mode
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  
  // Set the theme based on system preference
  setTheme(prefersDark ? 'dark' : 'default');
  
  // Listen for changes in color scheme preference
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', handleColorSchemeChange);
}

/**
 * Handle changes to the system color scheme
 * @param {MediaQueryListEvent} e Media query change event
 */
function handleColorSchemeChange(e) {
  setTheme(e.matches ? 'dark' : 'default');
}

/**
 * Add the following HTML to admin-header.php:
 * 
 * <div class="theme-settings">
 *   <select id="theme-switcher" class="form-control form-control-sm">
 *     <option value="default">Default Theme</option>
 *     <option value="dark">Dark Theme</option>
 *     <option value="blue">Blue Theme</option>
 *     <option value="green">Green Theme</option>
 *     <option value="purple">Purple Theme</option>
 *     <option value="high-contrast">High Contrast</option>
 *   </select>
 *   
 *   <div class="custom-control custom-switch ml-3">
 *     <input type="checkbox" class="custom-control-input" id="auto-dark-mode">
 *     <label class="custom-control-label" for="auto-dark-mode">Auto Dark Mode</label>
 *   </div>
 * </div>
 */
