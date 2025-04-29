// Admin Panel Diagnostics
function checkElementsExist() {
  const elements = [
    { id: 'traffic-chart', description: 'Traffic Chart' },
    { id: 'traffic-sources-chart', description: 'Traffic Sources Chart' },
    { id: 'theme-select', description: 'Theme Selector' },
    { id: 'auto-dark-mode', description: 'Auto Dark Mode Toggle' },
    { id: 'current-date', description: 'Current Date Display' }
  ];
  
  console.log("=== ELEMENT EXISTENCE CHECK ===");
  let missingElements = 0;
  
  elements.forEach(el => {
    const element = document.getElementById(el.id);
    if (element) {
      console.log(`✓ Found: ${el.description} (${el.id})`);
    } else {
      console.log(`✗ Missing: ${el.description} (${el.id})`);
      missingElements++;
    }
  });
  
  if (missingElements > 0) {
    console.log(`Missing ${missingElements} elements. This could be why some features aren't working.`);
  } else {
    console.log("All required elements are present in the DOM.");
  }
}

function checkScriptsLoaded() {
  console.log("=== JAVASCRIPT DEPENDENCY CHECK ===");
  
  // Check Chart.js availability
  if (typeof Chart !== 'undefined') {
    console.log("✓ Chart.js is loaded");
  } else {
    console.log("✗ Chart.js is missing - charts won't render");
  }
  
  // Check for our theme switcher functionality
  if (typeof setupThemeSwitcher === 'function') {
    console.log("✓ Theme switcher function is defined");
  } else {
    console.log("✗ Theme switcher function is missing - theme switching won't work");
  }
  
  // Check event handlers on theme select
  const themeSelect = document.getElementById('theme-select');
  if (themeSelect) {
    if (themeSelect.onchange) {
      console.log("✓ Theme select has event handler attached");
    } else {
      console.log("✗ Theme select is missing event handler - theme won't change on selection");
    }
  }
}

function checkCSSVariables() {
  console.log("=== CSS VARIABLES CHECK ===");
  
  const rootStyles = getComputedStyle(document.documentElement);
  const variablesToCheck = [
    '--primary-color',
    '--sidebar-bg',
    '--header-bg',
    '--content-bg',
    '--card-bg'
  ];
  
  let missingVariables = 0;
  
  variablesToCheck.forEach(variable => {
    const value = rootStyles.getPropertyValue(variable).trim();
    if (value) {
      console.log(`✓ CSS Variable ${variable} = ${value}`);
    } else {
      console.log(`✗ CSS Variable ${variable} is missing or empty`);
      missingVariables++;
    }
  });
  
  if (missingVariables > 0) {
    console.log(`Missing ${missingVariables} CSS variables. This could affect theming and layout.`);
  }
}

function checkConsoleErrors() {
  console.log("=== CONSOLE ERRORS CHECK ===");
  console.log("Please check your browser's console for any JavaScript errors.");
  console.log("Common errors include:");
  console.log("- Uncaught ReferenceError: Variable is not defined");
  console.log("- Uncaught TypeError: Cannot read property of undefined");
  console.log("- Failed to load resource: 404 errors for missing files");
}

function checkHeaderStructure() {
  console.log("=== HEADER STRUCTURE CHECK ===");
  
  const header = document.querySelector('.admin-header');
  if (!header) {
    console.log("✗ Admin header element not found");
    return;
  }
  
  console.log("✓ Admin header element found");
  
  const themeSwitcher = header.querySelector('.theme-switcher');
  if (themeSwitcher) {
    console.log("✓ Theme switcher found in header");
  } else {
    console.log("✗ Theme switcher not found in header");
  }
  
  const headerRight = header.querySelector('.header-right');
  if (headerRight) {
    console.log("✓ Header right section found");
  } else {
    console.log("✗ Header right section missing - theme switcher may not display correctly");
  }
}

function checkDashboardStructure() {
  console.log("=== DASHBOARD STRUCTURE CHECK ===");
  
  const statsRow = document.querySelector('.admin-content .row:first-child');
  if (statsRow) {
    const statCards = statsRow.querySelectorAll('.card');
    console.log(`Found ${statCards.length} stat cards. Expected 4.`);
    
    if (statCards.length < 4) {
      console.log("✗ Missing statistics cards in dashboard");
    }
  } else {
    console.log("✗ Statistics row not found in dashboard");
  }
  
  const chartRow = document.querySelector('.admin-content .row:nth-child(2)');
  if (chartRow) {
    console.log("✓ Chart row found in dashboard");
    
    const trafficChartContainer = chartRow.querySelector('.chart-area');
    if (trafficChartContainer) {
      console.log("✓ Traffic chart container found");
    } else {
      console.log("✗ Traffic chart container missing");
    }
    
    const sourcesChartContainer = chartRow.querySelector('.chart-pie');
    if (sourcesChartContainer) {
      console.log("✓ Sources chart container found");
    } else {
      console.log("✗ Sources chart container missing");
    }
  } else {
    console.log("✗ Chart row not found in dashboard");
  }
}

function checkFiles() {
  console.log("=== FILE LOADING CHECK ===");
  
  // Get all script tags
  const scripts = document.querySelectorAll('script');
  console.log(`Found ${scripts.length} script tags.`);
  
  // Check for our key script files
  const scriptFiles = Array.from(scripts).map(script => script.src);
  const requiredScripts = [
    'admin-core.js',
    'admin-theme-switcher.js',
    'chart.js'
  ];
  
  requiredScripts.forEach(script => {
    const found = scriptFiles.some(src => src.includes(script));
    if (found) {
      console.log(`✓ ${script} is loaded`);
    } else {
      console.log(`✗ ${script} not found in page`);
    }
  });
  
  // Get all stylesheet tags
  const stylesheets = document.querySelectorAll('link[rel="stylesheet"]');
  console.log(`Found ${stylesheets.length} stylesheet tags.`);
  
  // Check for our key CSS files
  const cssFiles = Array.from(stylesheets).map(link => link.href);
  const requiredCSS = [
    'admin-core.css',
    'admin-themes.css'
  ];
  
  requiredCSS.forEach(css => {
    const found = cssFiles.some(href => href.includes(css));
    if (found) {
      console.log(`✓ ${css} is loaded`);
    } else {
      console.log(`✗ ${css} not found in page`);
    }
  });
}

function runDiagnostics() {
  console.log("======= ADMIN PANEL DIAGNOSTICS =======");
  console.log("Running diagnostics to identify issues...");
  console.log("");
  
  checkElementsExist();
  console.log("");
  
  checkScriptsLoaded();
  console.log("");
  
  checkCSSVariables();
  console.log("");
  
  checkHeaderStructure();
  console.log("");
  
  checkDashboardStructure();
  console.log("");
  
  checkFiles();
  console.log("");
  
  checkConsoleErrors();
  console.log("");
  
  console.log("======= DIAGNOSTICS COMPLETE =======");
  console.log("Check your browser's console for results.");
}

// Run diagnostics when loaded
document.addEventListener('DOMContentLoaded', runDiagnostics);
