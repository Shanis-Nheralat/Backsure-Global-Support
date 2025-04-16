document.addEventListener('DOMContentLoaded', function() {
    // Check authentication
    const token = localStorage.getItem('authToken');
    if (!token) {
        window.location.href = 'admin-login.html';
        return;
    }
    
    // Set current date in dashboard
    const currentDateElement = document.getElementById('current-date');
    if (currentDateElement) {
        const now = new Date();
        currentDateElement.textContent = now.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
    
    // Sidebar navigation
    const navLinks = document.querySelectorAll('.sidebar-nav a');
    const contentSections = document.querySelectorAll('.content-section');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.getAttribute('id') === 'logout-btn') {
                return; // Handle logout separately
            }
            
            e.preventDefault();
            
            // Remove active class from all links and sections
            navLinks.forEach(navLink => navLink.parentElement.classList.remove('active'));
            contentSections.forEach(section => section.classList.remove('active'));
            
            // Add active class to clicked link and corresponding section
            this.parentElement.classList.add('active');
            const sectionId = this.getAttribute('data-section');
            document.getElementById(sectionId).classList.add('active');
        });
    });
    
    // Toggle sidebar on mobile
    const toggleSidebar = document.querySelector('.toggle-sidebar');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (toggleSidebar) {
        toggleSidebar.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });
    }
    
    // Logout functionality
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Clear authentication token
            localStorage.removeItem('authToken');
            
            // Redirect to login page
            window.location.href = 'admin-login.html';
        });
    }
    
    // Modal functionality
    const modal = document.getElementById('detail-modal');
    const closeButtons = document.querySelectorAll('.close-modal, .close-btn');
    
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    });
    
    // Close modal when clicking outside of modal content
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Fetch and display dashboard data
    fetchDashboardData();
    
    // Fetch and display inquiry lists
    fetchInquiries('general-inquiries-list', 'general_inquiry');
    fetchInquiries('meetings-list', 'meeting_request');
    fetchInquiries('services-list', 'service_intake');
    
    // ===== Helper Functions =====
    
    // Function to fetch dashboard data
    function fetchDashboardData() {
        // Show loading state
        document.getElementById('general-count').textContent = 'Loading...';
        document.getElementById('meeting-count').textContent = 'Loading...';
        document.getElementById('service-count').textContent = 'Loading...';
        document.getElementById('today-count').textContent = 'Loading...';
        
        fetch('/api/admin/dashboard-stats', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch dashboard data');
            }
            return response.json();
        })
        .then(data => {
            // Update counters
            document.getElementById('general-count').textContent = data.generalCount;
            document.getElementById('meeting-count').textContent = data.meetingCount;
            document.getElementById('service-count').textContent = data.serviceCount;
            document.getElementById('today-count').textContent = data.todayCount;
            
            // Update recent inquiries table
            const recentInquiriesTable = document.getElementById('recent-inquiries');
            recentInquiriesTable.innerHTML = '';
            
            if (data.recentInquiries && data.recentInquiries.length > 0) {
                data.recentInquiries.forEach(inquiry => {
                    const row = document.createElement('tr');
                    
                    // Format the date
                    const date = new Date(inquiry.submission_date);
                    const formattedDate = date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                    
                    // Format the type for display
                    let displayType = 'Unknown';
                    if (inquiry.form_type === 'general_inquiry') displayType = 'General Inquiry';
                    if (inquiry.form_type === 'meeting_request') displayType = 'Meeting Request';
                    if (inquiry.form_type === 'service_intake') displayType = 'Service Request';
                    
                    row.innerHTML = `
                        <td>${formattedDate}</td>
                        <td>${displayType}</td>
                        <td>${inquiry.name}</td>
                        <td>${inquiry.email}</td>
                        <td>
                            <button class="action-btn view-btn" data-id="${inquiry._id}">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    `;
                    
                    recentInquiriesTable.appendChild(row);
                });
                
                // Add event listeners to view buttons
                addViewButtonListeners(recentInquiriesTable);
            } else {
                recentInquiriesTable.innerHTML = '<tr><td colspan="5" class="no-data">No recent inquiries found</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error fetching dashboard data:', error);
            document.getElementById('general-count').textContent = 'Error';
            document.getElementById('meeting-count').textContent = 'Error';
            document.getElementById('service-count').textContent = 'Error';
            document.getElementById('today-count').textContent = 'Error';
            document.getElementById('recent-inquiries').innerHTML = '<tr><td colspan="5" class="error-message">Failed to load recent inquiries</td></tr>';
        });
    }
    
    // Function to fetch inquiries by type
    function fetchInquiries(tableId, formType, page = 1, filters = {}) {
        const tableElement = document.getElementById(tableId);
        
        if (!tableElement) return;
        
        // Show loading state
        tableElement.innerHTML = '<tr><td colspan="7" class="loading-message">Loading inquiries...</td></tr>';
        
        // Build query parameters
        let queryParams = new URLSearchParams();
        queryParams.append('type', formType);
        queryParams.append('page', page);
        
        // Add any filters
        Object.keys(filters).forEach(key => {
            if (filters[key]) {
                queryParams.append(key, filters[key]);
            }
        });
        
        fetch(`/api/admin/inquiries?${queryParams.toString()}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch inquiries');
            }
            return response.json();
        })
        .then(data => {
            // Clear table
            tableElement.innerHTML = '';
            
            if (data.inquiries && data.inquiries.length > 0) {
                data.inquiries.forEach(inquiry => {
                    const row = document.createElement('tr');
                    
                    // Format the date
                    const date = new Date(inquiry.submission_date);
                    const formattedDate = date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                    
                    // Different table structures based on inquiry type
                    if (formType === 'general_inquiry') {
                        row.innerHTML = `
                            <td>${formattedDate}</td>
                            <td>${inquiry.name}</td>
                            <td>${inquiry.email}</td>
                            <td>${inquiry.phone || 'N/A'}</td>
                            <td>
                                <button class="action-btn view-btn" data-id="${inquiry._id}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        `;
                    } else if (formType === 'meeting_request') {
                        // Format meeting date
                        const meetingDate = inquiry.date ? new Date(inquiry.date).toLocaleDateString() : 'N/A';
                        
                        row.innerHTML = `
                            <td>${formattedDate}</td>
                            <td>${meetingDate}</td>
                            <td>${inquiry.name}</td>
                            <td>${inquiry.email}</td>
                            <td>${inquiry.time || 'N/A'}</td>
                            <td>${inquiry.indian_time || 'N/A'}</td>
                            <td>
                                <button class="action-btn view-btn" data-id="${inquiry._id}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        `;
                    } else if (formType === 'service_intake') {
                        row.innerHTML = `
                            <td>${formattedDate}</td>
                            <td>${inquiry.name}</td>
                            <td>${inquiry.email}</td>
                            <td>${inquiry.service_type || 'N/A'}</td>
                            <td>${inquiry.timeline || 'N/A'}</td>
                            <td>
                                <button class="action-btn view-btn" data-id="${inquiry._id}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        `;
                    }
                    
                    tableElement.appendChild(row);
                });
                
                // Add event listeners to view buttons
                addViewButtonListeners(tableElement);
                
                // Update pagination
                updatePagination(formType, data.totalPages, page);
            } else {
                const columnCount = formType === 'meeting_request' ? 7 : (formType === 'service_intake' ? 6 : 5);
                tableElement.innerHTML = `<tr><td colspan="${columnCount}" class="no-data">No inquiries found</td></tr>`;
            }
        })
        .catch(error => {
            console.error(`Error fetching ${formType} inquiries:`, error);
            const columnCount = formType === 'meeting_request' ? 7 : (formType === 'service_intake' ? 6 : 5);
            tableElement.innerHTML = `<tr><td colspan="${columnCount}" class="error-message">Failed to load inquiries</td></tr>`;
        });
    }
    
    // Function to update pagination
    function updatePagination(formType, totalPages, currentPage) {
        let paginationId;
        
        if (formType === 'general_inquiry') {
            paginationId = 'general-pagination';
        } else if (formType === 'meeting_request') {
            paginationId = 'meetings-pagination';
        } else if (formType === 'service_intake') {
            paginationId = 'services-pagination';
        }
        
        const paginationElement = document.getElementById(paginationId);
        
        if (!paginationElement) return;
        
        // Clear previous pagination
        paginationElement.innerHTML = '';
        
        if (totalPages <= 1) return;
        
        // Previous button
        const prevButton = document.createElement('button');
        prevButton.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prevButton.disabled = currentPage === 1;
        prevButton.addEventListener('click', () => {
            if (currentPage > 1) {
                fetchInquiries(formType === 'general_inquiry' ? 'general-inquiries-list' : 
                              (formType === 'meeting_request' ? 'meetings-list' : 'services-list'), 
                              formType, currentPage - 1, getFilters(formType));
            }
        });
        paginationElement.appendChild(prevButton);
        
        // Page buttons
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const pageButton = document.createElement('button');
            pageButton.textContent = i;
            pageButton.classList.toggle('active', i === currentPage);
            
            pageButton.addEventListener('click', () => {
                if (i !== currentPage) {
                    fetchInquiries(formType === 'general_inquiry' ? 'general-inquiries-list' : 
                                  (formType === 'meeting_request' ? 'meetings-list' : 'services-list'), 
                                  formType, i, getFilters(formType));
                }
            });
            
            paginationElement.appendChild(pageButton);
        }
        
        // Next button
        const nextButton = document.createElement('button');
        nextButton.innerHTML = '<i class="fas fa-chevron-right"></i>';
        nextButton.disabled = currentPage === totalPages;
        nextButton.addEventListener('click', () => {
            if (currentPage < totalPages) {
                fetchInquiries(formType === 'general_inquiry' ? 'general-inquiries-list' : 
                              (formType === 'meeting_request' ? 'meetings-list' : 'services-list'), 
                              formType, currentPage + 1, getFilters(formType));
            }
        });
        paginationElement.appendChild(nextButton);
    }
    
    // Function to get current filters for a form type
    function getFilters(formType) {
        const filters = {};
        
        if (formType === 'general_inquiry') {
            const searchValue = document.getElementById('general-search').value.trim();
            const dateFrom = document.getElementById('general-date-from').value;
            const dateTo = document.getElementById('general-date-to').value;
            
            if (searchValue) filters.search = searchValue;
            if (dateFrom) filters.dateFrom = dateFrom;
            if (dateTo) filters.dateTo = dateTo;
        } else if (formType === 'meeting_request') {
            const searchValue = document.getElementById('meeting-search').value.trim();
            const dateFrom = document.getElementById('meeting-date-from').value;
            const dateTo = document.getElementById('meeting-date-to').value;
            
            if (searchValue) filters.search = searchValue;
            if (dateFrom) filters.dateFrom = dateFrom;
            if (dateTo) filters.dateTo = dateTo;
        } else if (formType === 'service_intake') {
            const searchValue = document.getElementById('service-search').value.trim();
            const dateFrom = document.getElementById('service-date-from').value;
            const dateTo = document.getElementById('service-date-to').value;
            
            if (searchValue) filters.search = searchValue;
            if (dateFrom) filters.dateFrom = dateFrom;
            if (dateTo) filters.dateTo = dateTo;
        }
        
        return filters;
    }
    
    // Function to add event listeners to view buttons
    function addViewButtonListeners(tableElement) {
        const viewButtons = tableElement.querySelectorAll('.view-btn');
        
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const inquiryId = this.getAttribute('data-id');
                viewInquiryDetails(inquiryId);
            });
        });
    }
    
    // Function to view inquiry details
    function viewInquiryDetails(inquiryId) {
        // Show loading state in modal
        const modalBody = document.getElementById('modal-body');
        modalBody.innerHTML = '<div class="loading">Loading inquiry details...</div>';
        
        // Show the modal
        document.getElementById('detail-modal').style.display = 'flex';
        
        // Fetch inquiry details
        fetch(`/api/admin/inquiries/${inquiryId}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch inquiry details');
            }
            return response.json();
        })
        .then(inquiry => {
            // Format the date
            const date = new Date(inquiry.submission_date);
            const formattedDate = date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Format the type for display
            let displayType = 'Unknown';
            if (inquiry.form_type === 'general_inquiry') displayType = 'General Inquiry';
            if (inquiry.form_type === 'meeting_request') displayType = 'Meeting Request';
            if (inquiry.form_type ===