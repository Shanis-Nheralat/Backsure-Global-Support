/**
 * JavaScript for Admin Dashboard
 * Handles chart initialization and data updates
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts if they exist
    initTrafficChart();
    initSourcesChart();
    initDeviceChart();
    
    // Handle chart period toggles
    setupChartPeriodToggles();
    
    // Update current date
    updateCurrentDate();
});

/**
 * Initialize traffic chart
 */
function initTrafficChart() {
    const trafficChartCanvas = document.getElementById('traffic-chart');
    if (!trafficChartCanvas) return;
    
    // Traffic chart configuration is handled in the admin-dashboard.php file
    // This function is a placeholder for additional traffic chart customization
}

/**
 * Initialize sources chart
 */
function initSourcesChart() {
    const sourcesChartCanvas = document.getElementById('traffic-sources-chart');
    if (!sourcesChartCanvas) return;
    
    // Sources chart configuration is handled in the admin-dashboard.php file
    // This function is a placeholder for additional sources chart customization
}

/**
 * Initialize device chart
 */
function initDeviceChart() {
    const deviceChartCanvas = document.getElementById('device-chart');
    if (!deviceChartCanvas) return;
    
    // Get data via AJAX
    // This is a placeholder for actual AJAX implementation
    fetch('admin-ajax.php?action=get_device_stats')
        .then(response => response.json())
        .then(data => {
            const deviceChart = new Chart(deviceChartCanvas, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.data,
                        backgroundColor: data.colors,
                        hoverBackgroundColor: data.colors,
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error loading device stats:', error));
}

/**
 * Setup chart period toggles
 */
function setupChartPeriodToggles() {
    const periodButtons = document.querySelectorAll('[data-period]');
    if (!periodButtons.length) return;
    
    // Period toggles are handled in the admin-dashboard.php file
    // This function is a placeholder for additional period toggle customization
}

/**
 * Update current date display
 */
function updateCurrentDate() {
    const currentDateElement = document.getElementById('current-date');
    if (!currentDateElement) return;
    
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    currentDateElement.textContent = now.toLocaleDateString(undefined, options);
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
