/**
 * Admin Panel Lazy Loading System
 * 
 * This script implements lazy loading for the admin panel to improve performance
 * by only loading resources when they're needed.
 */

// Add this to admin-core.js or create a new admin-lazy-load.js file
document.addEventListener('DOMContentLoaded', function() {
  initLazyLoading();
});

/**
 * Initialize lazy loading functionality
 */
function initLazyLoading() {
  lazyLoadImages();
  lazyLoadTabs();
  lazyLoadCharts();
  setupIntersectionObserver();
}

/**
 * Lazy load images using Intersection Observer
 */
function lazyLoadImages() {
  // Select all images that should be lazy loaded
  const lazyImages = document.querySelectorAll('img[data-src]');
  
  if (!lazyImages.length) return;
  
  // Create observer for images
  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        
        // Remove data-src to mark as loaded
        img.removeAttribute('data-src');
        observer.unobserve(img);
      }
    });
  });
  
  // Observe each image
  lazyImages.forEach(img => {
    imageObserver.observe(img);
  });
}

/**
 * Lazy load tab content
 */
function lazyLoadTabs() {
  const tabLinks = document.querySelectorAll('.tab-link');
  
  if (!tabLinks.length) return;
  
  tabLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      
      const tabId = this.getAttribute('data-tab');
      const tabContent = document.getElementById(tabId);
      
      // Hide all tab content
      const allTabContents = document.querySelectorAll('.tab-content');
      allTabContents.forEach(content => {
        content.classList.remove('active');
      });
      
      // Show selected tab content
      tabContent.classList.add('active');
      
      // Deactivate all tab links
      tabLinks.forEach(link => {
        link.classList.remove('active');
      });
      
      // Activate current tab link
      this.classList.add('active');
      
      // If content needs to be loaded
      if (tabContent.getAttribute('data-loaded') !== 'true') {
        loadTabContent(tabId);
      }
    });
  });
  
  // Load first tab by default
  if (tabLinks.length > 0) {
    tabLinks[0].click();
  }
}

/**
 * Load tab content via AJAX
 */
function loadTabContent(tabId) {
  const tabContent = document.getElementById(tabId);
  const endpoint = tabContent.getAttribute('data-endpoint');
  
  if (!endpoint) return;
  
  // Show loading indicator
  tabContent.innerHTML = '<div class="loading-spinner"></div>';
  
  // Fetch content
  fetch(endpoint)
    .then(response => response.text())
    .then(html => {
      tabContent.innerHTML = html;
      tabContent.setAttribute('data-loaded', 'true');
      
      // Initialize any scripts in the loaded content
      initializeTabScripts(tabContent);
    })
    .catch(error => {
      tabContent.innerHTML = '<div class="error-message">Failed to load content</div>';
      console.error('Error loading tab content:', error);
    });
}

/**
 * Initialize any scripts in dynamically loaded content
 */
function initializeTabScripts(container) {
  // Find and execute any inline scripts
  const scripts = container.querySelectorAll('script');
  scripts.forEach(script => {
    const newScript = document.createElement('script');
    
    if (script.src) {
      newScript.src = script.src;
    } else {
      newScript.textContent = script.textContent;
    }
    
    // Replace the original script with the new one
    script.parentNode.replaceChild(newScript, script);
  });
  
  // Re-init any components that might be in the loaded content
  if (typeof initComponents === 'function') {
    initComponents();
  }
}

/**
 * Lazy load charts when they become visible
 */
function lazyLoadCharts() {
  const chartContainers = document.querySelectorAll('[data-chart]');
  
  if (!chartContainers.length) return;
  
  // Create observer for charts
  const chartObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const container = entry.target;
        const chartType = container.getAttribute('data-chart');
        const chartData = JSON.parse(container.getAttribute('data-chart-data') || '{}');
        
        loadChart(container, chartType, chartData);
        observer.unobserve(container);
      }
    });
  });
  
  // Observe each chart container
  chartContainers.forEach(container => {
    chartObserver.observe(container);
  });
}

/**
 * Load a chart using appropriate library
 */
function loadChart(container, chartType, chartData) {
  // Show loading indicator
  container.innerHTML = '<div class="loading-spinner"></div>';
  
  // Load chart library if not already loaded
  if (typeof Chart === 'undefined') {
    const script = document.createElement('script');
    script.src = 'assets/lib/chart.min.js';
    script.onload = () => createChart(container, chartType, chartData);
    document.head.appendChild(script);
  } else {
    createChart(container, chartType, chartData);
  }
}

/**
 * Create chart with the loaded library
 */
function createChart(container, chartType, chartData) {
  container.innerHTML = ''; // Clear loading spinner
  
  const canvas = document.createElement('canvas');
  container.appendChild(canvas);
  
  // Create chart based on type
  new Chart(canvas, {
    type: chartType,
    data: chartData,
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  });
}

/**
 * Setup Intersection Observer for any elements that should load when visible
 */
function setupIntersectionObserver() {
  const lazyElements = document.querySelectorAll('[data-lazy]');
  
  if (!lazyElements.length) return;
  
  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const element = entry.target;
        const lazyType = element.getAttribute('data-lazy');
        
        switch (lazyType) {
          case 'iframe':
            loadIframe(element);
            break;
          case 'component':
            loadComponent(element);
            break;
          case 'html':
            loadHtml(element);
            break;
        }
        
        observer.unobserve(element);
      }
    });
  });
  
  lazyElements.forEach(element => {
    observer.observe(element);
  });
}

/**
 * Load iframe content when visible
 */
function loadIframe(element) {
  const src = element.getAttribute('data-src');
  if (!src) return;
  
  const iframe = document.createElement('iframe');
  iframe.src = src;
  iframe.width = element.getAttribute('data-width') || '100%';
  iframe.height = element.getAttribute('data-height') || '400';
  iframe.frameBorder = '0';
  
  element.appendChild(iframe);
}

/**
 * Load a component via AJAX
 */
function loadComponent(element) {
  const endpoint = element.getAttribute('data-src');
  if (!endpoint) return;
  
  // Show loading indicator
  element.innerHTML = '<div class="loading-spinner"></div>';
  
  // Fetch component
  fetch(endpoint)
    .then(response => response.text())
    .then(html => {
      element.innerHTML = html;
      
      // Initialize any scripts in the loaded content
      initializeTabScripts(element);
    })
    .catch(error => {
      element.innerHTML = '<div class="error-message">Failed to load component</div>';
      console.error('Error loading component:', error);
    });
}

/**
 * Load HTML content
 */
function loadHtml(element) {
  const html = element.getAttribute('data-content');
  if (html) {
    element.innerHTML = html;
  }
}

/**
 * Usage in your HTML:
 * 
 * For images:
 * <img data-src="path/to/image.jpg" alt="Lazy loaded image">
 * 
 * For tabs:
 * <div class="tabs">
 *   <a href="#" class="tab-link" data-tab="tab1">Tab 1</a>
 *   <a href="#" class="tab-link" data-tab="tab2">Tab 2</a>
 * </div>
 * <div id="tab1" class="tab-content" data-endpoint="admin-ajax.php?action=get_tab_content&tab=1"></div>
 * <div id="tab2" class="tab-content" data-endpoint="admin-ajax.php?action=get_tab_content&tab=2"></div>
 * 
 * For charts:
 * <div data-chart="bar" data-chart-data='{"labels":["Jan","Feb"],"datasets":[{"label":"Sales","data":[30,70]}]}'></div>
 * 
 * For other elements:
 * <div data-lazy="iframe" data-src="https://example.com/embed"></div>
 * <div data-lazy="component" data-src="admin-ajax.php?action=get_component&name=stats"></div>
 * <div data-lazy="html" data-content="<strong>This content loads when visible</strong>"></div>
 */
