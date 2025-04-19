/**
 * Backsure Global Support
 * Client Management JavaScript
 * Version: 1.0
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize DataTables
  initClientsTable();
  
  // Initialize event listeners
  initBulkActions();
  initModals();
  initFilters();
  initRowActions();
  initFormValidation();
  initFlatpickr();
  
  // Initialize any notifications
  checkForNotifications();
});

/**
 * Initialize the clients DataTable
 */
function initClientsTable() {
  const clientsTable = $('#clients-table').DataTable({
    responsive: true,
    language: {
      search: "",
      searchPlaceholder: "Search clients...",
      lengthMenu: "Show _MENU_ clients per page",
      info: "Showing _START_ to _END_ of _TOTAL_ clients",
      infoEmpty: "No clients available",
      infoFiltered: "(filtered from _MAX_ total clients)",
      paginate: {
        first: '<i class="fas fa-angle-double-left"></i>',
        previous: '<i class="fas fa-angle-left"></i>',
        next: '<i class="fas fa-angle-right"></i>',
        last: '<i class="fas fa-angle-double-right"></i>'
      }
    },
    columnDefs: [
      {
        targets: 0, // Checkbox column
        orderable: false,
        className: 'select-checkbox',
        width: '30px'
      },
      {
        targets: 8, // Actions column
        orderable: false,
        width: '120px'
      }
    ],
    order: [[5, 'desc']], // Sort by inquiry date by default
    pageLength: 10,
    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "All"]
    ],
    dom: 'Bfrtip',
    buttons: [
      'copy', 'csv', 'excel', 'pdf', 'print'
    ]
  });
  
  // Add the DataTable search box functionality to our custom search box
  $('#search-clients').on('keyup', function() {
    clientsTable.search(this.value).draw();
  });
  
  // Make select-all checkbox work with DataTables
  $('#select-all').on('click', function() {
    $('.row-checkbox').prop('checked', this.checked);
    updateBulkActionCounter();
  });
  
  // Update select-all when individual checkboxes change
  $('#clients-table tbody').on('change', '.row-checkbox', function() {
    if (!this.checked) {
      $('#select-all').prop('checked', false);
    } else {
      const allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
      $('#select-all').prop('checked', allChecked);
    }
    updateBulkActionCounter();
  });
  
  // Update select-all state when table is redrawn
  clientsTable.on('draw', function() {
    const allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
    $('#select-all').prop('checked', allChecked && $('.row-checkbox').length > 0);
    updateBulkActionCounter();
  });
}

/**
 * Initialize date picker
 */
function initFlatpickr() {
  if (typeof flatpickr !== 'undefined') {
    // Date picker for follow-up scheduling
    flatpickr("#followup-date", {
      enableTime: true,
      dateFormat: "Y-m-d H:i",
      minDate: "today",
      time_24hr: false
    });
    
    // Date picker for client detail follow-up
    flatpickr("#client-followup-date", {
      enableTime: true,
      dateFormat: "Y-m-d H:i",
      minDate: "today",
      time_24hr: false
    });
  }
}

/**
 * Initialize bulk actions functionality
 */
function initBulkActions() {
  // Show/hide bulk actions panel
  $('#bulk-actions-btn').on('click', function() {
    $('#bulk-actions-panel').slideToggle(200);
  });
  
  // Close bulk actions panel
  $('.bulk-close').on('click', function() {
    $('#bulk-actions-panel').slideUp(200);
  });
  
  // Export button
  $('#export-clients-btn').on('click', function() {
    const selectedIds = getSelectedClientIds();
    if (selectedIds.length > 0) {
      exportClients(selectedIds);
    } else {
      // If none selected, export all visible
      exportClients('all');
    }
  });
  
  // Bulk action buttons
  $('.bulk-btn').on('click', function() {
    const action = $(this).data('action');
    const selectedIds = getSelectedClientIds();
    
    if (selectedIds.length === 0) {
      showToast('Please select at least one client', 'warning');
      return;
    }
    
    switch (action) {
      case 'contact':
        showConfirmModal('Are you sure you want to mark the selected clients as contacted?', function() {
          performBulkAction('contact', selectedIds);
        });
        break;
      case 'followup':
        $('#followup-modal').show();
        break;
      case 'assign':
        $('#assign-modal').show();
        break;
      case 'export':
        exportClients(selectedIds);
        break;
      case 'delete':
        showConfirmModal('Are you sure you want to delete the selected clients? This action cannot be undone.', function() {
          performBulkAction('delete', selectedIds);
        });
        break;
    }
  });
  
  // Schedule followup button in modal
  $('#schedule-followup-btn').on('click', function() {
    const selectedIds = getSelectedClientIds();
    const followupDate = $('#followup-date').val();
    const followupNote = $('#followup-note').val();
    
    if (!followupDate) {
      showToast('Please select a followup date', 'warning');
      return;
    }
    
    scheduleFollowup(selectedIds, followupDate, followupNote);
    $('#followup-modal').hide();
  });
  
  // Assign to user button in modal
  $('#assign-to-btn').on('click', function() {
    const selectedIds = getSelectedClientIds();
    const assignedUser = $('#assigned-user').val();
    const assignedNote = $('#assign-note').val();
    
    if (!assignedUser) {
      showToast('Please select a user to assign', 'warning');
      return;
    }
    
    assignToUser(selectedIds, assignedUser, assignedNote);
    $('#assign-modal').hide();
  });
}

/**
 * Initialize modal dialogs
 */
function initModals() {
  // Close modal when clicking the X or Cancel button
  $('.modal-close, .modal-cancel').on('click', function() {
    $(this).closest('.modal').hide();
  });
  
  // Close modal when clicking outside the modal content
  $('.modal').on('click', function(e) {
    if (e.target === this) {
      $(this).hide();
    }
  });
  
  // Add Client button
  $('#add-client-btn').on('click', function() {
    // Reset form
    $('#add-client-form')[0].reset();
    $('#client-id').val('');
    $('#client-form-title').text('Add New Client');
    
    // Show modal
    $('#client-form-modal').show();
  });
  
  // Save Client button
  $('#save-client-btn').on('click', function() {
    const isValid = validateClientForm();
    if (isValid) {
      submitClientForm();
    }
  });
  
  // Client details modal tabs
  $('.client-modal-tab').on('click', function() {
    const tabId = $(this).data('tab');
    
    // Hide all tab contents
    $('.client-tab-content').hide();
    
    // Show the selected tab content
    $(`#${tabId}`).show();
    
    // Update active tab
    $('.client-modal-tab').removeClass('active');
    $(this).addClass('active');
  });
}

/**
 * Initialize filter functionality
 */
function initFilters() {
  // Apply filters button
  $('#apply-filters').on('click', function() {
    applyFilters();
  });
  
  // Reset filters button
  $('#reset-filters').on('click', function() {
    resetFilters();
  });
  
  // Set today's date as default for date-to filter
  const today = new Date();
  const formattedDate = today.toISOString().split('T')[0];
  $('#date-to').val(formattedDate);
  
  // Set date 30 days ago as default for date-from filter
  const thirtyDaysAgo = new Date();
  thirtyDaysAgo.setDate(today.getDate() - 30);
  const formattedThirtyDaysAgo = thirtyDaysAgo.toISOString().split('T')[0];
  $('#date-from').val(formattedThirtyDaysAgo);
}

/**
 * Initialize row action buttons (view, contact, edit, delete)
 */
function initRowActions() {
  // View client details
  $('#clients-table').on('click', '.action-btn.view', function(e) {
    e.preventDefault();
    const clientId = $(this).data('id');
    viewClientDetails(clientId);
  });
  
  // Edit client
  $('#clients-table').on('click', '.action-btn.edit', function(e) {
    e.preventDefault();
    const clientId = $(this).data('id');
    editClient(clientId);
  });
  
  // Contact client
  $('#clients-table').on('click', '.action-btn.contact', function(e) {
    e.preventDefault();
    const clientId = $(this).data('id');
    contactClient(clientId);
  });
  
  // Delete client
  $('#clients-table').on('click', '.action-btn.delete', function() {
    const clientId = $(this).data('id');
    const clientName = $(this).closest('tr').find('.client-name').text();
    
    showConfirmModal(`Are you sure you want to delete "${clientName}"? This action cannot be undone.`, function() {
      deleteClient(clientId);
    });
  });
  
  // Edit button in client details modal
  $('#edit-client-btn').on('click', function() {
    const clientId = $(this).data('client-id');
    $('#client-detail-modal').hide();
    editClient(clientId);
  });
  
  // Send email button in client details modal
  $('#send-email-btn').on('click', function() {
    const clientId = $(this).data('client-id');
    const emailSubject = $('#email-subject').val();
    const emailBody = $('#email-body').val();
    
    if (!emailSubject || !emailBody) {
      showToast('Please fill in both subject and message', 'warning');
      return;
    }
    
    sendEmail(clientId, emailSubject, emailBody);
  });
  
  // Add note button in client details modal
  $('#add-note-btn').on('click', function() {
    const clientId = $(this).data('client-id');
    const noteContent = $('#note-content').val();
    
    if (!noteContent) {
      showToast('Please enter a note', 'warning');
      return;
    }
    
    addClientNote(clientId, noteContent);
  });
  
  // Schedule followup in client details modal
  $('#schedule-client-followup-btn').on('click', function() {
    const clientId = $(this).data('client-id');
    const followupDate = $('#client-followup-date').val();
    const followupNote = $('#client-followup-note').val();
    
    if (!followupDate) {
      showToast('Please select a followup date', 'warning');
      return;
    }
    
    scheduleFollowup([clientId], followupDate, followupNote);
  });
}

/**
 * Initialize form validation
 */
function initFormValidation() {
  // Source field should be required when adding a new client
  $('#client-source').on('change', function() {
    if ($(this).val() === 'referral') {
      // If referral is selected, show referral source field
      if ($('#referral-source-group').length === 0) {
        const referralField = `
          <div class="form-group" id="referral-source-group">
            <label for="referral-source">Referral Source</label>
            <input type="text" id="referral-source" class="form-control" placeholder="Who referred this client?">
          </div>
        `;
        $(this).closest('.form-row').after(referralField);
      }
    } else {
      // Hide referral source field if not needed
      $('#referral-source-group').remove();
    }
  });
}

/**
 * Check for notifications and upcoming followups
 */
function checkForNotifications() {
  // In a real implementation, this would make an API call
  // For now, we'll just simulate with a timeout
  
  setTimeout(() => {
    // Check for today's followups
    const todaysFollowups = 3; // This would come from API
    
    if (todaysFollowups > 0) {
      showToast(`You have ${todaysFollowups} client followups scheduled for today`, 'info', 8000);
    }
    
    // Check for overdue followups
    const overdueFollowups = 2; // This would come from API
    
    if (overdueFollowups > 0) {
      showToast(`You have ${overdueFollowups} overdue client followups`, 'warning', 8000);
    }
  }, 2000);
}

/**
 * Get array of selected client IDs
 */
function getSelectedClientIds() {
  const selectedIds = [];
  $('.row-checkbox:checked').each(function() {
    const clientId = $(this).closest('tr').data('id');
    selectedIds.push(clientId);
  });
  return selectedIds;
}

/**
 * Update the bulk action counter with the number of selected clients
 */
function updateBulkActionCounter() {
  const count = $('.row-checkbox:checked').length;
  $('.selected-count').text(`${count} item${count !== 1 ? 's' : ''} selected`);
}

/**
 * Apply filters to the clients table
 */
function applyFilters() {
  const statusFilter = $('#filter-status').val();
  const serviceFilter = $('#filter-service').val();
  const sourceFilter = $('#filter-source').val();
  const assignedFilter = $('#filter-assigned').val();
  const dateFrom = $('#date-from').val();
  const dateTo = $('#date-to').val();
  
  // Apply filters to DataTable
  const table = $('#clients-table').DataTable();
  
  // Clear existing filters
  table.search('').columns().search('').draw();
  
  // Apply each filter if set
  if (statusFilter) {
    table.column(6).search(statusFilter, true, false).draw();
  }
  
  if (serviceFilter) {
    table.column(4).search(serviceFilter).draw();
  }
  
  if (sourceFilter) {
    // Source filter would be applied to a hidden column in a real implementation
    console.log(`Source filter: ${sourceFilter}`);
  }
  
  if (assignedFilter) {
    table.column(7).search(assignedFilter).draw();
  }
  
  // Date range filtering is more complex and might require custom filtering logic
  // This is a simplified example that doesn't actually filter by date
  if (dateFrom && dateTo) {
    console.log(`Filtering dates from ${dateFrom} to ${dateTo}`);
    // In a real implementation, you would create a custom filtering function
  }
  
  showToast('Filters applied', 'success');
}

/**
 * Reset all filters to default values
 */
function resetFilters() {
  // Reset dropdown filters
  $('#filter-status, #filter-service, #filter-source, #filter-assigned').val('');
  
  // Reset date filters to defaults
  const today = new Date();
  const formattedDate = today.toISOString().split('T')[0];
  $('#date-to').val(formattedDate);
  
  const thirtyDaysAgo = new Date();
  thirtyDaysAgo.setDate(today.getDate() - 30);
  const formattedThirtyDaysAgo = thirtyDaysAgo.toISOString().split('T')[0];
  $('#date-from').val(formattedThirtyDaysAgo);
  
  // Clear DataTable filters
  const table = $('#clients-table').DataTable();
  table.search('').columns().search('').draw();
  
  // Reset search box
  $('#search-clients').val('');
  
  showToast('Filters reset', 'info');
}

/**
 * Validate client form
 */
function validateClientForm() {
  const name = $('#client-name').val().trim();
  const email = $('#client-email').val().trim();
  const phone = $('#client-phone').val().trim();
  const service = $('#client-service').val();
  
  // Simple validation
  if (!name) {
    showToast('Client name is required', 'warning');
    return false;
  }
  
  if (!email && !phone) {
    showToast('Either email or phone is required', 'warning');
    return false;
  }
  
  if (email && !isValidEmail(email)) {
    showToast('Please enter a valid email', 'warning');
    return false;
  }
  
  if (!service) {
    showToast('Please select a service', 'warning');
    return false;
  }
  
  return true;
}

/**
 * Simple email validation
 */
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

/**
 * Submit client form (add or edit)
 */
function submitClientForm() {
  const clientId = $('#client-id').val();
  const isNewClient = !clientId;
  
  // In a real implementation, this would make an API call
  showToast(isNewClient ? 'Adding client...' : 'Updating client...', 'info');
  
  setTimeout(() => {
    const name = $('#client-name').val();
    const service = $('#client-service').val();
    const status = $('#client-status').val();
    const assigned = $('#client-assigned').val();
    
    // If editing an existing client
    if (!isNewClient) {
      const row = $(`tr[data-id="${clientId}"]`);
      
      // Update row data
      row.find('.client-name').text(name);
      row.find('td:eq(4)').text(getServiceName(service));
      row.find('td:eq(6)').html(`<span class="status-badge ${status}">${getStatusName(status)}</span>`);
      row.find('td:eq(7)').text(getAssignedName(assigned));
      
      showToast('Client updated successfully', 'success');
    } else {
      // Add new client - in a real implementation, the server would return the new ID
      const newId = Math.max(...Array.from($('#clients-table tbody tr')).map(row => parseInt($(row).data('id') || 0))) + 1;
      
      // Add to DataTable
      const table = $('#clients-table').DataTable();
      const email = $('#client-email').val();
      const phone = $('#client-phone').val();
      
      const newRow = [
        `<div class="checkbox-wrapper">
          <input type="checkbox" id="select-${newId}" class="row-checkbox">
          <label for="select-${newId}"></label>
        </div>`,
        `<span class="client-name">${name}</span>`,
        email,
        phone,
        getServiceName(service),
        formatDate(new Date()),
        `<span class="status-badge ${status}">${getStatusName(status)}</span>`,
        getAssignedName(assigned),
        `<div class="action-buttons">
          <button class="action-btn view" title="View Details" data-id="${newId}">
            <i class="fas fa-eye"></i>
          </button>
          <button class="action-btn contact" title="Contact" data-id="${newId}">
            <i class="fas fa-envelope"></i>
          </button>
          <button class="action-btn edit" title="Edit" data-id="${newId}">
            <i class="fas fa-edit"></i>
          </button>
          <button class="action-btn delete" title="Delete" data-id="${newId}">
            <i class="fas fa-trash"></i>
          </button>
        </div>`
      ];
      
      table.row.add(newRow).draw();
      const newRowNode = table.row(':last').node();
      $(newRowNode).attr('data-id', newId);
      
      showToast('Client added successfully', 'success');
    }
    
    // Close the modal
    $('#client-form-modal').hide();
  }, 1000);
}

/**
 * Format date for display
 */
function formatDate(date) {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return date.toLocaleDateString('en-US', options);
}

/**
 * Get service name from value
 */
function getServiceName(value) {
  const services = {
    'finance-accounting': 'Finance & Accounting',
    'hr-admin': 'HR & Admin',
    'dedicated-teams': 'Dedicated Teams',
    'insurance': 'Insurance',
    'business-care': 'Business Care Plans',
    'other': 'Other'
  };
  
  return services[value] || value;
}

/**
 * Get status name from value
 */
function getStatusName(value) {
  const statusNames = {
    'new': 'New',
    'contacted': 'Contacted',
    'followup': 'Follow-up',
    'converted': 'Converted',
    'closed': 'Closed'
  };
  
  return statusNames[value] || value;
}

/**
 * Get assigned user name from ID
 */
function getAssignedName(id) {
  if (!id) return 'Unassigned';
  
  const users = {
    '1': 'John Smith',
    '2': 'Sarah Johnson',
    '3': 'Michael Chen'
  };
  
  return users[id] || 'Unknown';
}

/**
 * View client details
 */
function viewClientDetails(clientId) {
  // In a real implementation, this would fetch client details from the API
  console.log(`Viewing client ID: ${clientId}`);
  
  // For demo purposes, we'll just show the modal with static data
  const clientName = $(`tr[data-id="${clientId}"] .client-name`).text();
  const clientEmail = $(`tr[data-id="${clientId}"] td:eq(2)`).text();
  
  // Set client info in the modal
  $('#client-detail-name').text(clientName);
  $('#client-detail-id').text(`ID: ${clientId}`);
  
  // Set data attributes for action buttons
  $('#send-email-btn, #add-note-btn, #schedule-client-followup-btn, #edit-client-btn').data('client-id', clientId);
  
  // Reset active tab
  $('.client-modal-tab:first').click();
  
  // Show the modal
  $('#client-detail-modal').show();
  
  // In a real implementation, you would load notes, emails, and activities here
  loadClientNotes(clientId);
  loadClientEmails(clientId);
  loadClientActivities(clientId);
  loadClientFollowups(clientId);
}

/**
 * Edit client
 */
function editClient(clientId) {
  // In a real implementation, this would fetch client data from the API
  console.log(`Editing client ID: ${clientId}`);
  
  // For demo purposes, we'll prefill the form with data from the table
  const clientRow = $(`tr[data-id="${clientId}"]`);
  
  // Set client ID in hidden field
  $('#client-id').val(clientId);
  
  // Set form title
  $('#client-form-title').text('Edit Client');
  
  // Set form fields
  $('#client-name').val(clientRow.find('.client-name').text());
  $('#client-email').val(clientRow.find('td:eq(2)').text());
  $('#client-phone').val(clientRow.find('td:eq(3)').text());
  
  // Set service (this is a simplified mapping for demo)
  const serviceText = clientRow.find('td:eq(4)').text();
  let serviceValue = '';
  
  if (serviceText.includes('Finance')) {
    serviceValue = 'finance-accounting';
  } else if (serviceText.includes('HR')) {
    serviceValue = 'hr-admin';
  } else if (serviceText.includes('Dedicated')) {
    serviceValue = 'dedicated-teams';
  } else if (serviceText.includes('Insurance')) {
    serviceValue = 'insurance';
  } else if (serviceText.includes('Business Care')) {
    serviceValue = 'business-care';
  } else {
    serviceValue = 'other';
  }
  
  $('#client-service').val(serviceValue);
  
  // Set status
  const statusBadge = clientRow.find('.status-badge');
  let statusValue = '';
  
  if (statusBadge.hasClass('new')) {
    statusValue = 'new';
  } else if (statusBadge.hasClass('contacted')) {
    statusValue = 'contacted';
  } else if (statusBadge.hasClass('followup')) {
    statusValue = 'followup';
  } else if (statusBadge.hasClass('converted')) {
    statusValue = 'converted';
  } else if (statusBadge.hasClass('closed')) {
    statusValue = 'closed';
  }
  
  $('#client-status').val(statusValue);
  
  // Set assigned user (simplified for demo)
  const assignedText = clientRow.find('td:eq(7)').text();
  let assignedValue = '';
  
  if (assignedText.includes('John')) {
    assignedValue = '1';
  } else if (assignedText.includes('Sarah')) {
    assignedValue = '2';
  } else if (assignedText.includes('Michael')) {
    assignedValue = '3';
  }
  
  $('#client-assigned').val(assignedValue);
  
  // Show the modal
  $('#client-form-modal').show();
}

/**
 * Contact client
 */
function contactClient(clientId) {
  // In a real implementation, this would open an email/SMS modal
  // For now, we'll just open the client details modal on the communication tab
  viewClientDetails(clientId);
  
  // Switch to communication tab
  $('.client-modal-tab[data-tab="client-communication"]').click();
}

/**
 * Delete a single client
 */
function deleteClient(clientId) {
  // In a real implementation, this would make an API call
  console.log(`Deleting client ID:`, clientId);
  
  // Simulate API call with timeout
  showToast('Deleting client...', 'info');
  
  setTimeout(() => {
    // Remove the row with animation
    $(`tr[data-id="${clientId}"]`).fadeOut(300, function() {
      // Update DataTable
      const table = $('#clients-table').DataTable();
      table.row($(this)).remove().draw(false);
      
      showToast('Client deleted successfully', 'success');
    });
  }, 1000);
}

/**
 * Perform bulk action on selected clients
 */
function performBulkAction(action, clientIds) {
  // In a real implementation, this would make an API call
  console.log(`Performing bulk action: ${action} on clients:`, clientIds);
  
  // Simulate API call with timeout
  showToast('Processing...', 'info');
  
  setTimeout(() => {
    // Update UI based on action
    switch (action) {
      case 'contact':
        // Update status badges
        clientIds.forEach(id => {
          const statusCell = $(`tr[data-id="${id}"] td:nth-child(7)`);
          statusCell.html('<span class="status-badge contacted">Contacted</span>');
        });
        showToast('Selected clients have been marked as contacted', 'success');
        break;
      case 'delete':
        clientIds.forEach(id => {
          $(`tr[data-id="${id}"]`).fadeOut(300, function() {
            // Update DataTable
            const table = $('#clients-table').DataTable();
            table.row($(this)).remove().draw(false);
          });
        });
        showToast('Selected clients have been deleted', 'success');
        break;
    }
    
    // Reset checkboxes after bulk action
    $('#select-all').prop('checked', false);
    $('.row-checkbox').prop('checked', false);
    updateBulkActionCounter();
    
    // Hide bulk actions panel
    $('#bulk-actions-panel').slideUp(200);
  }, 1000);
}

/**
 * Schedule followup for clients
 */
function scheduleFollowup(clientIds, followupDate, followupNote) {
  // In a real implementation, this would make an API call
  console.log(`Scheduling followup for clients:`, clientIds);
  console.log(`Followup date:`, followupDate);
  console.log(`Followup note:`, followupNote);
  
  // Simulate API call with timeout
  showToast('Scheduling followup...', 'info');
  
  setTimeout(() => {
    // Update UI
    clientIds.forEach(id => {
      const statusCell = $(`tr[data-id="${id}"] td:nth-child(7)`);
      if (statusCell.find('.status-badge').hasClass('new')) {
        statusCell.html('<span class="status-badge followup">Follow-up</span>');
      }
    });
    
    // Reset form
    $('#followup-date').val('');
    $('#followup-note').val('');
    $('#client-followup-date').val('');
    $('#client-followup-note').val('');
    
    showToast('Followup scheduled successfully', 'success');
    
    // If this was triggered from the/**
 * Backsure Global Support
 * Client Management JavaScript
 * Version: 1.0
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize DataTables
  initClientsTable();
  
  // Initialize event listeners
  initBulkActions();
  initModals();
  initFilters();
  initRowActions();
  initFormValidation();
  initFlatpickr();
  
  // Initialize any notifications
  checkForNotifications();
});

/**
 * Initialize the clients DataTable
 */
function initClientsTable() {
  const clientsTable = $('#clients-table').DataTable({
    responsive: true,
    language: {
      search: "",
      searchPlaceholder: "Search clients...",
      lengthMenu: "Show _MENU_ clients per page",
      info: "Showing _START_ to _END_ of _TOTAL_ clients",
      infoEmpty: "No clients available",
      infoFiltered: "(filtered from _MAX_ total clients)",
      paginate: {
        first: '<i class="fas fa-angle-double-left"></i>',
        previous: '<i class="fas fa-angle-left"></i>',
        next: '<i class="fas fa-angle-right"></i>',
        last: '<i class="fas fa-angle-double-right"></i>'
      }
    },
    columnDefs: [
      {
        targets: 0, // Checkbox column
        orderable: false,
        className: 'select-checkbox',
        width: '30px'
      },
      {
        targets: 8, // Actions column
        orderable: false,
        width: '120px'
      }
    ],
    order: [[5, 'desc']], // Sort by inquiry date by default
    pageLength: 10,
    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "All"]
    ],
    dom: 'Bfrtip',
    buttons: [
      'copy', 'csv', 'excel', 'pdf', 'print'
    ]
  });
  
  // Add the DataTable search box functionality to our custom search box
  $('#search-clients').on('keyup', function() {
    clientsTable.search(this.value).draw();
  });
  
  // Make select-all checkbox work with DataTables
  $('#select-all').on('click', function() {
    $('.row-checkbox').prop('checked', this.checked);
    updateBulkActionCounter();
  });
  
  // Update select-all when individual checkboxes change
  $('#clients-table tbody').on('change', '.row-checkbox', function() {
    if (!this.checked) {
      $('#select-all').prop('checked', false);
    } else {
      const allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
      $('#select-all').prop('checked', allChecked);
    }
    updateBulkActionCounter();
  });
  
  // Update select-all state when table is redrawn
  clientsTable.on('draw', function() {
    const allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
    $('#select-all').prop('checked', allChecked && $('.row-checkbox').length > 0);
    updateBulkActionCounter();
  });
}

/**
 * Initialize date picker
 */
function initFlatpickr() {
  if (typeof flatpickr !== 'undefined') {
    // Date picker for follow-up scheduling
    flatpickr("#followup-date", {
      enableTime: true,
      dateFormat: "Y-m-d H:i",
      minDate: "today",
      time_24hr: false
    });
    
    // Date picker for client detail follow-up
    flatpickr("#client-followup-date", {
      enableTime: true,
      dateFormat: "Y-m-d H:i",
      minDate: "today",
      time_24hr: false
    });
  }
}

/**
 * Initialize bulk actions functionality
 */
function initBulkActions() {
  // Show/hide bulk actions panel
  $('#bulk-actions-btn').on('click', function() {
    $('#bulk-actions-panel').slideToggle(200);
  });
  
  // Close bulk actions panel
  $('.bulk-close').on('click', function() {
    $('#bulk-actions-panel').slideUp(200);
  });
  
  // Export button
  $('#export-clients-btn').on('click', function() {
    const selectedIds = getSelectedClientIds();
    if (selectedIds.length > 0) {
      exportClients(selectedIds);
    } else {
      // If none selected, export all visible
      exportClients('all');
    }
  });
  
  // Bulk action buttons
  $('.bulk-btn').on('click', function() {
    const action = $(this).data('action');
    const selectedIds = getSelectedClientIds();
    
    if (selectedIds.length === 0) {
      showToast('Please select at least one client', 'warning');
      return;
    }
    
    switch (action) {
      case 'contact':
        showConfirmModal('Are you sure you want to mark the selected clients as contacted?', function() {
          performBulkAction('contact', selectedIds);
        });
        break;
      case 'followup':
        $('#followup-modal').show();
        break;
      case 'assign':
        $('#assign-modal').show();
        break;
      case 'export':
        exportClients(selectedIds);
        break;
      case 'delete':
        showConfirmModal('Are you sure you want to delete the selected clients? This action cannot be undone.', function() {
          performBulkAction('delete', selectedIds);
        });
        break;
    }
  });
  
  // Schedule followup button in modal
  $('#schedule-followup-btn').on('click', function() {
    const selectedIds = getSelectedClientIds();
    const followupDate = $('#followup-date').val();
    const followupNote = $('#followup-note').val();
    
    if (!followupDate) {
      showToast('Please select a followup date', 'warning');
      return;
    }
    
    scheduleFollowup(selectedIds, followupDate, followupNote);
    $('#followup-modal').hide();
  });
  
  // Assign to user button in modal
  $('#assign-to-btn').on('click', function() {
    const selectedIds = getSelectedClientIds();
    const assignedUser = $('#assigned-user').val();
    const assignedNote = $('#assign-note').val();
    
    if (!assignedUser) {
      showToast('Please select a user to assign', 'warning');
      return;
    }
    
    assignToUser(selectedIds, assignedUser, assignedNote);
    $('#assign-modal').hide();
  });
}

/**
 * Initialize modal dialogs
 */
function initModals() {
  // Close modal when clicking the X or Cancel button
  $('.modal-close, .modal-cancel').on('click', function() {
    $(this).closest('.modal').hide();
  });
  
  // Close modal when clicking outside the modal content
  $('.modal').on('click', function(e) {
    if (e.target === this) {
      $(this).hide();
    }
  });
  
  // Add Client button
  $('#add-client-btn').on('click', function() {
    // Reset form
    $('#add-client-form')[0].reset();
    $('#client-id').val('');
    $('#client-form-title').text('Add New Client');
    
    // Show modal
    $('#client-form-modal').show();
  });
  
  // Save Client button
  $('#save-client-btn').on('click', function() {
    const isValid = validateClientForm();
    if (isValid) {
      submitClientForm();
    }
  });
  
  // Client details modal tabs
  $('.client-modal-tab').on('click', function() {
    const tabId = $(this).data('tab');
    
    // Hide all tab contents
    $('.client-tab-content').hide();
    
    // Show the selected tab content
    $(`#${tabId}`).show();
    
    // Update active tab
    $('.client-modal-tab').removeClass('active');
    $(this).addClass('active');
  });
}

/**
 * Initialize filter functionality
 */
function initFilters() {
  // Apply filters button
  $('#apply-filters').on('click', function() {
    applyFilters();
  });
  
  // Reset filters button
  $('#reset-filters').on('click', function() {
    resetFilters();
  });
  
  // Set today's date as default for date-to filter
  const today = new Date();
  const formattedDate = today.toISOString().split('T')[0];
  $('#date-to').val(formattedDate);
  
  // Set date 30 days ago as default for date-from filter
  const thirtyDaysAgo = new Date();
  thirtyDaysAgo.setDate(today.getDate() - 30);
  const formattedThirtyDaysAgo = thirtyDaysAgo.toISOString().split('T')[0];
  $('#date-from').val(formattedThirtyDaysAgo);
}

/**
 * Initialize row action buttons (view, contact, edit, delete)
 */
function initRowActions() {
  // View client details
  $('#clients-table').on('click', '.action-btn.view', function(e) {
    e.preventDefault();
    const clientId = $(this).data('id');
    viewClientDetails(clientId);
  });
  
  // Edit client
  $('#clients-table').on('click', '.action-btn.edit', function(e) {
    e.preventDefault();
    const clientId = $(this).data('id');
    editClient(clientId);
  });
  
  // Contact client
  $('#clients-table').on('click', '.action-btn.contact', function(e) {
    e.preventDefault();
    const clientId = $(this).data('id');
    contactClient(clientId);
  });
  
  // Delete client
  $('#clients-table').on('click', '.action-btn.delete', function() {
    const clientId = $(this).data('id');
    const clientName = $(this).closest('tr').find('.client-name').text();
    
    showConfirmModal(`Are you sure you want to delete "${clientName}"? This action cannot be undone.`, function() {
      deleteClient(clientId);
    });
  });
  
  // Edit button in client details modal
  $('#edit-client-btn').on('click', function() {
    const clientId = $(this).data('client-id');
    $('#client-detail-modal').hide();
    editClient(clientId);
  });
  
  // Send email button in client details modal
  $('#send-email-btn').on('click', function() {
    const clientId = $(this).data('client-id');
    const emailSubject = $('#email-subject').val();
    const emailBody = $('#email-body').val();
    
    if (!emailSubject || !emailBody) {
      showToast('Please fill in both subject and message', 'warning');
      return;
    }
    
    sendEmail(clientId, emailSubject, emailBody);
  });
  
  // Add note button in client details modal
  $('#add-note-btn').on('click', function() {
    const clientId = $(this).data('client-id');
    const noteContent = $('#note-content').val();
    
    if (!noteContent) {
      showToast('Please enter a note', 'warning');
      return;
    }
    
    addClientNote(clientId, noteContent);
  });
  
  // Schedule followup in client details modal
  $('#schedule-client-followup-btn').on('click', function() {
    const clientId = $(this).data('client-id');
    const followupDate = $('#client-followup-date').val();
    const followupNote = $('#client-followup-note').val();
    
    if (!followupDate) {
      showToast('Please select a followup date', 'warning');
      return;
    }
    
    scheduleFollowup([clientId], followupDate, followupNote);
  });
}

/**
 * Initialize form validation
 */
function initFormValidation() {
  // Source field should be required when adding a new client
  $('#client-source').on('change', function() {
    if ($(this).val() === 'referral') {
      // If referral is selected, show referral source field
      if ($('#referral-source-group').length === 0) {
        const referralField = `
          <div class="form-group" id="referral-source-group">
            <label for="referral-source">Referral Source</label>
            <input type="text" id="referral-source" class="form-control" placeholder="Who referred this client?">
          </div>
        `;
        $(this).closest('.form-row').after(referralField);
      }
    } else {
      // Hide referral source field if not needed
      $('#referral-source-group').remove();
    }
  });
}

/**
 * Check for notifications and upcoming followups
 */
function checkForNotifications() {
  // In a real implementation, this would make an API call
  // For now, we'll just simulate with a timeout
  
  setTimeout(() => {
    // Check for today's followups
    const todaysFollowups = 3; // This would come from API
    
    if (todaysFollowups > 0) {
      showToast(`You have ${todaysFollowups} client followups scheduled for today`, 'info', 8000);
    }
    
    // Check for overdue followups
    const overdueFollowups = 2; // This would come from API
    
    if (overdueFollowups > 0) {
      showToast(`You have ${overdueFollowups} overdue client followups`, 'warning', 8000);
    }
  }, 2000);
}

/**
 * Get array of selected client IDs
 */
function getSelectedClientIds() {
  const selectedIds = [];
  $('.row-checkbox:checked').each(function() {
    const clientId = $(this).closest('tr').data('id');
    selectedIds.push(clientId);
  });
  return selectedIds;
}

/**
 * Update the bulk action counter with the number of selected clients
 */
function updateBulkActionCounter() {
  const count = $('.row-checkbox:checked').length;
  $('.selected-count').text(`${count} item${count !== 1 ? 's' : ''} selected`);
}

/**
 * Apply filters to the clients table
 */
function applyFilters() {
  const statusFilter = $('#filter-status').val();
  const serviceFilter = $('#filter-service').val();
  const sourceFilter = $('#filter-source').val();
  const assignedFilter = $('#filter-assigned').val();
  const dateFrom = $('#date-from').val();
  const dateTo = $('#date-to').val();
  
  // Apply filters to DataTable
  const table = $('#clients-table').DataTable();
  
  // Clear existing filters
  table.search('').columns().search('').draw();
  
  // Apply each filter if set
  if (statusFilter) {
    table.column(6).search(statusFilter, true, false).draw();
  }
  
  if (serviceFilter) {
    table.column(4).search(serviceFilter).draw();
  }
  
  if (sourceFilter) {
    // Source filter would be applied to a hidden column in a real implementation
    console.log(`Source filter: ${sourceFilter}`);
  }
  
  if (assignedFilter) {
    table.column(7).search(assignedFilter).draw();
  }
  
  // Date range filtering is more complex and might require custom filtering logic
  // This is a simplified example that doesn't actually filter by date
  if (dateFrom && dateTo) {
    console.log(`Filtering dates from ${dateFrom} to ${dateTo}`);
    // In a real implementation, you would create a custom filtering function
  }
  
  showToast('Filters applied', 'success');
}

/**
 * Reset all filters to default values
 */
function resetFilters() {
  // Reset dropdown filters
  $('#filter-status, #filter-service, #filter-source, #filter-assigned').val('');
  
  // Reset date filters to defaults
  const today = new Date();
  const formattedDate = today.toISOString().split('T')[0];
  $('#date-to').val(formattedDate);
  
  const thirtyDaysAgo = new Date();
  thirtyDaysAgo.setDate(today.getDate() - 30);
  const formattedThirtyDaysAgo = thirtyDaysAgo.toISOString().split('T')[0];
  $('#date-from').val(formattedThirtyDaysAgo);
  
  // Clear DataTable filters
  const table = $('#clients-table').DataTable();
  table.search('').columns().search('').draw();
  
  // Reset search box
  $('#search-clients').val('');
  
  showToast('Filters reset', 'info');
}

/**
 * Validate client form
 */
function validateClientForm() {
  const name = $('#client-name').val().trim();
  const email = $('#client-email').val().trim();
  const phone = $('#client-phone').val().trim();
  const service = $('#client-service').val();
  
  // Simple validation
  if (!name) {
    showToast('Client name is required', 'warning');
    return false;
  }
  
  if (!email && !phone) {
    showToast('Either email or phone is required', 'warning');
    return false;
  }
  
  if (email && !isValidEmail(email)) {
    showToast('Please enter a valid email', 'warning');
    return false;
  }
  
  if (!service) {
    showToast('Please select a service', 'warning');
    return false;
  }
  
  return true;
}

/**
 * Simple email validation
 */
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

/**
 * Submit client form (add or edit)
 */
function submitClientForm() {
  const clientId = $('#client-id').val();
  const isNewClient = !clientId;
  
  // In a real implementation, this would make an API call
  showToast(isNewClient ? 'Adding client...' : 'Updating client...', 'info');
  
  setTimeout(() => {
    const name = $('#client-name').val();
    const service = $('#client-service').val();
    const status = $('#client-status').val();
    const assigned = $('#client-assigned').val();
    
    // If editing an existing client
    if (!isNewClient) {
      const row = $(`tr[data-id="${clientId}"]`);
      
      // Update row data
      row.find('.client-name').text(name);
      row.find('td:eq(4)').text(getServiceName(service));
      row.find('td:eq(6)').html(`<span class="status-badge ${status}">${getStatusName(status)}</span>`);
      row.find('td:eq(7)').text(getAssignedName(assigned));
      
      showToast('Client updated successfully', 'success');
    } else {
      // Add new client - in a real implementation, the server would return the new ID
      const newId = Math.max(...Array.from($('#clients-table tbody tr')).map(row => parseInt($(row).data('id') || 0))) + 1;
      
      // Add to DataTable
      const table = $('#clients-table').DataTable();
      const email = $('#client-email').val();
      const phone = $('#client-phone').val();
      
      const newRow = [
        `<div class="checkbox-wrapper">
          <input type="checkbox" id="select-${newId}" class="row-checkbox">
          <label for="select-${newId}"></label>
        </div>`,
        `<span class="client-name">${name}</span>`,
        email,
        phone,
        getServiceName(service),
        formatDate(new Date()),
        `<span class="status-badge ${status}">${getStatusName(status)}</span>`,
        getAssignedName(assigned),
        `<div class="action-buttons">
          <button class="action-btn view" title="View Details" data-id="${newId}">
            <i class="fas fa-eye"></i>
          </button>
          <button class="action-btn contact" title="Contact" data-id="${newId}">
            <i class="fas fa-envelope"></i>
          </button>
          <button class="action-btn edit" title="Edit" data-id="${newId}">
            <i class="fas fa-edit"></i>
          </button>
          <button class="action-btn delete" title="Delete" data-id="${newId}">
            <i class="fas fa-trash"></i>
          </button>
        </div>`
      ];
      
      table.row.add(newRow).draw();
      const newRowNode = table.row(':last').node();
      $(newRowNode).attr('data-id', newId);
      
      showToast('Client added successfully', 'success');
    }
    
    // Close the modal
    $('#client-form-modal').hide();
  }, 1000);
}

/**
 * Format date for display
 */
function formatDate(date) {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return date.toLocaleDateString('en-US', options);
}

/**
 * Get service name from value
 */
function getServiceName(value) {
  const services = {
    'finance-accounting': 'Finance & Accounting',
    'hr-admin': 'HR & Admin',
    'dedicated-teams': 'Dedicated Teams',
    'insurance': 'Insurance',
    'business-care': 'Business Care Plans',
    'other': 'Other'
  };
  
  return services[value] || value;
}

/**
 * Get status name from value
 */
function getStatusName(value) {
  const statusNames = {
    'new': 'New',
    'contacted': 'Contacted',
    'followup': 'Follow-up',
    'converted': 'Converted',
    'closed': 'Closed'
  };
  
  return statusNames[value] || value;
}

/**
 * Get assigned user name from ID
 */
function getAssignedName(id) {
  if (!id) return 'Unassigned';
  
  const users = {
    '1': 'John Smith',
    '2': 'Sarah Johnson',
    '3': 'Michael Chen'
  };
  
  return users[id] || 'Unknown';
}

/**
 * View client details
 */
function viewClientDetails(clientId) {
  // In a real implementation, this would fetch client details from the API
  console.log(`Viewing client ID: ${clientId}`);
  
  // For demo purposes, we'll just show the modal with static data
  const clientName = $(`tr[data-id="${clientId}"] .client-name`).text();
  const clientEmail = $(`tr[data-id="${clientId}"] td:eq(2)`).text();
  
  // Set client info in the modal
  $('#client-detail-name').text(clientName);
  $('#client-detail-id').text(`ID: ${clientId}`);
  
  // Set data attributes for action buttons
  $('#send-email-btn, #add-note-btn, #schedule-client-followup-btn, #edit-client-btn').data('client-id', clientId);
  
  // Reset active tab
  $('.client-modal-tab:first').click();
  
  // Show the modal
  $('#client-detail-modal').show();
  
  // In a real implementation, you would load notes, emails, and activities here
  loadClientNotes(clientId);
  loadClientEmails(clientId);
  loadClientActivities(clientId);
  loadClientFollowups(clientId);
}

/**
 * Edit client
 */
function editClient(clientId) {
  // In a real implementation, this would fetch client data from the API
  console.log(`Editing client ID: ${clientId}`);
  
  // For demo purposes, we'll prefill the form with data from the table
  const clientRow = $(`tr[data-id="${clientId}"]`);
  
  // Set client ID in hidden field
  $('#client-id').val(clientId);
  
  // Set form title
  $('#client-form-title').text('Edit Client');
  
  // Set form fields
  $('#client-name').val(clientRow.find('.client-name').text());
  $('#client-email').val(clientRow.find('td:eq(2)').text());
  $('#client-phone').val(clientRow.find('td:eq(3)').text());
  
  // Set service (this is a simplified mapping for demo)
  const serviceText = clientRow.find('td:eq(4)').text();
  let serviceValue = '';
  
  if (serviceText.includes('Finance')) {
    serviceValue = 'finance-accounting';
  } else if (serviceText.includes('HR')) {
    serviceValue = 'hr-admin';
  } else if (serviceText.includes('Dedicated')) {
    serviceValue = 'dedicated-teams';
  } else if (serviceText.includes('Insurance')) {
    serviceValue = 'insurance';
  } else if (serviceText.includes('Business Care')) {
    serviceValue = 'business-care';
  } else {
    serviceValue = 'other';
  }
  
  $('#client-service').val(serviceValue);
  
  // Set status
  const statusBadge = clientRow.find('.status-badge');
  let statusValue = '';
  
  if (statusBadge.hasClass('new')) {
    statusValue = 'new';
  } else if (statusBadge.hasClass('contacted')) {
    statusValue = 'contacted';
  } else if (statusBadge.hasClass('followup')) {
    statusValue = 'followup';
  } else if (statusBadge.hasClass('converted')) {
    statusValue = 'converted';
  } else if (statusBadge.hasClass('closed')) {
    statusValue = 'closed';
  }
  
  $('#client-status').val(statusValue);
  
  // Set assigned user (simplified for demo)
  const assignedText = clientRow.find('td:eq(7)').text();
  let assignedValue = '';
  
  if (assignedText.includes('John')) {
    assignedValue = '1';
  } else if (assignedText.includes('Sarah')) {
    assignedValue = '2';
  } else if (assignedText.includes('Michael')) {
    assignedValue = '3';
  }
  
  $('#client-assigned').val(assignedValue);
  
  // Show the modal
  $('#client-form-modal').show();
}

/**
 * Contact client
 */
function contactClient(clientId) {
  // In a real implementation, this would open an email/SMS modal
  // For now, we'll just open the client details modal on the communication tab
  viewClientDetails(clientId);
  
  // Switch to communication tab
  $('.client-modal-tab[data-tab="client-communication"]').click();
}

/**
 * Delete a single client
 */
function deleteClient(clientId) {
  // In a real implementation, this would make an API call
  console.log(`Deleting client ID:`, clientId);
  
  // Simulate API call with timeout
  showToast('Deleting client...', 'info');
  
  setTimeout(() => {
    // Remove the row with animation
    $(`tr[data-id="${clientId}"]`).fadeOut(300, function() {
      // Update DataTable
      const table = $('#clients-table').DataTable();
      table.row($(this)).remove().draw(false);
      
      showToast('Client deleted successfully', 'success');
    });
  }, 1000);
}

/**
 * Perform bulk action on selected clients
 */
function performBulkAction(action, clientIds) {
  // In a real implementation, this would make an API call
  console.log(`Performing bulk action: ${action} on clients:`, clientIds);
  
  // Simulate API call with timeout
  showToast('Processing...', 'info');
  
  setTimeout(() => {
    // Update UI based on action
    switch (action) {
      case 'contact':
        // Update status badges
        clientIds.forEach(id => {
          const statusCell = $(`tr[data-id="${id}"] td:nth-child(7)`);
          statusCell.html('<span class="status-badge contacted">Contacted</span>');
        });
        showToast('Selected clients have been marked as contacted', 'success');
        break;
      case 'delete':
        clientIds.forEach(id => {
          $(`tr[data-id="${id}"]`).fadeOut(300, function() {
            // Update DataTable
            const table = $('#clients-table').DataTable();
            table.row($(this)).remove().draw(false);
          });
        });
        showToast('Selected clients have been deleted', 'success');
        break;
    }
    
    // Reset checkboxes after bulk action
    $('#select-all').prop('checked', false);
    $('.row-checkbox').prop('checked', false);
    updateBulkActionCounter();
    
    // Hide bulk actions panel
    $('#bulk-actions-panel').slideUp(200);
  }, 1000);
}

/**
 * Schedule followup for clients
 */
function scheduleFollowup(clientIds, followupDate, followupNote) {
  // In a real implementation, this would make an API call
  console.log(`Scheduling followup for clients:`, clientIds);
  console.log(`Followup date:`, followupDate);
  console.log(`Followup note:`, followupNote);
  
  // Simulate API call with timeout
  showToast('Scheduling followup...', 'info');
  
  setTimeout(() => {
    // Update UI
    clientIds.forEach(id => {
      const statusCell = $(`tr[data-id="${id}"] td:nth-child(7)`);
      if (statusCell.find('.status-badge').hasClass('new')) {
        statusCell.html('<span class="status-badge followup">Follow-up</span>');
      }
    });
    
    // Reset form
    $('#followup-date').val('');
    $('#followup-note').val('');
    $('#client-followup-date').val('');
    $('#client-followup-note').val('');
    
    showToast('Followup scheduled successfully', 'success');
    
    // If this was triggered from the client details modal, update the followups tab
    if ($('#client-detail-modal').is(':visible')) {
      const currentClientId = $('#send-email-btn').data('client-id');
      if (clientIds.includes(currentClientId)) {
        loadClientFollowups(currentClientId);
      }
    }
  }, 1000);
}

/**
 * Assign clients to a user
 */
function assignToUser(clientIds, userId, note) {
  // In a real implementation, this would make an API call
  console.log(`Assigning clients to user ID ${userId}:`, clientIds);
  console.log(`Assignment note:`, note);
  
  // Get user name for display
  const userName = $(`#assigned-user option[value="${userId}"]`).text();
  
  // Simulate API call with timeout
  showToast('Assigning clients...', 'info');
  
  setTimeout(() => {
    // Update UI
    clientIds.forEach(id => {
      const assignedCell = $(`tr[data-id="${id}"] td:nth-child(8)`);
      assignedCell.text(userName);
    });
    
    // Reset form
    $('#assigned-user').val('');
    $('#assign-note').val('');
    
    showToast('Clients assigned successfully', 'success');
  }, 1000);
}

/**
 * Export selected clients
 */
function exportClients(clientIds) {
  // In a real implementation, this would generate a CSV/Excel file for download
  console.log(`Exporting clients:`, clientIds);
  
  showToast('Generating export...', 'info');
  
  setTimeout(() => {
    // Simulate file download (in a real implementation, we would trigger a download)
    showToast('Export complete. Your download should begin shortly.', 'success');
    
    // Reset checkboxes after export
    $('#select-all').prop('checked', false);
    $('.row-checkbox').prop('checked', false);
    updateBulkActionCounter();
    
    // Hide bulk actions panel
    $('#bulk-actions-panel').slideUp(200);
  }, 1500);
}

/**
 * Send email to client
 */
function sendEmail(clientId, subject, body) {
  // In a real implementation, this would make an API call
  console.log(`Sending email to client ID ${clientId}`);
  console.log(`Subject: ${subject}`);
  console.log(`Body: ${body}`);
  
  // Simulate API call with timeout
  showToast('Sending email...', 'info');
  
  setTimeout(() => {
    // Reset form
    $('#email-subject').val('');
    $('#email-body').val('');
    
    // Update email history in the modal
    const emailList = $('#client-emails-list');
    const now = new Date();
    const formattedDate = now.toLocaleDateString() + ' ' + now.toLocaleTimeString();
    
    const newEmail = $(`
      <div class="email-item">
        <div class="email-header">
          <span class="email-subject">${subject}</span>
          <span class="email-time">${formattedDate}</span>
        </div>
        <div class="email-body">${body}</div>
      </div>
    `);
    
    emailList.prepend(newEmail);
    
    // Update status if it was "New"
    const statusCell = $(`tr[data-id="${clientId}"] td:nth-child(7)`);
    if (statusCell.find('.status-badge').hasClass('new')) {
      statusCell.html('<span class="status-badge contacted">Contacted</span>');
    }
    
    showToast('Email sent successfully', 'success');
  }, 1500);
}

/**
 * Add note to client
 */
function addClientNote(clientId, noteContent) {
  // In a real implementation, this would make an API call
  console.log(`Adding note to client ID ${clientId}`);
  console.log(`Note: ${noteContent}`);
  
  // Simulate API call with timeout
  showToast('Adding note...', 'info');
  
  setTimeout(() => {
    // Reset form
    $('#note-content').val('');
    
    // Update notes in the modal
    const notesList = $('#client-notes-list');
    const now = new Date();
    const formattedDate = now.toLocaleDateString() + ' ' + now.toLocaleTimeString();
    
    const newNote = $(`
      <div class="note-item">
        <div class="note-header">
          <span class="note-author">You</span>
          <span class="note-time">${formattedDate}</span>
        </div>
        <div class="note-content">${noteContent}</div>
      </div>
    `);
    
    notesList.prepend(newNote);
    
    showToast('Note added successfully', 'success');
  }, 1000);
}

/**
 * Load client notes (would fetch from API in real implementation)
 */
function loadClientNotes(clientId) {
  // Simulate API call
  console.log(`Loading notes for client ID ${clientId}`);
  
  // For demo purposes, let's just show some sample notes
  const notesList = $('#client-notes-list');
  notesList.empty();
  
  const sampleNotes = [
    {
      author: 'Sarah Johnson',
      time: '2 days ago',
      content: 'Client requested information about finance and accounting services.'
    },
    {
      author: 'John Smith',
      time: '1 week ago',
      content: 'Initial inquiry call completed. Client is interested in outsourcing HR functions.'
    }
  ];
  
  if (sampleNotes.length === 0) {
    notesList.html('<p class="no-data">No notes available for this client.</p>');
  } else {
    sampleNotes.forEach(note => {
      const noteItem = $(`
        <div class="note-item">
          <div class="note-header">
            <span class="note-author">${note.author}</span>
            <span class="note-time">${note.time}</span>
          </div>
          <div class="note-content">${note.content}</div>
        </div>
      `);
      
      notesList.append(noteItem);
    });
  }
}

/**
 * Load client emails (would fetch from API in real implementation)
 */
function loadClientEmails(clientId) {
  // Simulate API call
  console.log(`Loading emails for client ID ${clientId}`);
  
  // For demo purposes, let's just show some sample emails
  const emailsList = $('#client-emails-list');
  emailsList.empty();
  
  const sampleEmails = [
    {
      subject: 'Follow-up on our discussion',
      time: '3 days ago',
      body: 'Thank you for your interest in our services. As discussed, I\'m attaching our service brochure for your review.'
    },
    {
      subject: 'Welcome to Backsure Global Support',
      time: '1 week ago',
      body: 'Thank you for reaching out to us. We\'re excited to learn more about your business needs and how we can support you.'
    }
  ];
  
  if (sampleEmails.length === 0) {
    emailsList.html('<p class="no-data">No email history available for this client.</p>');
  } else {
    sampleEmails.forEach(email => {
      const emailItem = $(`
        <div class="email-item">
          <div class="email-header">
            <span class="email-subject">${email.subject}</span>
            <span class="email-time">${email.time}</span>
          </div>
          <div class="email-body">${email.body}</div>
        </div>
      `);
      
      emailsList.append(emailItem);
    });
  }
}

/**
 * Load client activities (would fetch from API in real implementation)
 */
function loadClientActivities(clientId) {
  // Simulate API call
  console.log(`Loading activities for client ID ${clientId}`);
  
  // For demo purposes, let's just show some sample activities
  const activitiesList = $('#client-activities-list');
  activitiesList.empty();
  
  const sampleActivities = [
    {
      type: 'email',
      user: 'System',
      time: '3 days ago',
      description: 'Automatic welcome email sent'
    },
    {
      type: 'note',
      user: 'Sarah Johnson',
      time: '2 days ago',
      description: 'Added a new note'
    },
    {
      type: 'status',
      user: 'John Smith',
      time: '1 day ago',
      description: 'Changed status to "Contacted"'
    }
  ];
  
  if (sampleActivities.length === 0) {
    activitiesList.html('<p class="no-data">No activity history available for this client.</p>');
  } else {
    sampleActivities.forEach(activity => {
      let iconClass = '';
      
      switch (activity.type) {
        case 'email':
          iconClass = 'fa-envelope';
          break;
        case 'note':
          iconClass = 'fa-sticky-note';
          break;
        case 'status':
          iconClass = 'fa-exchange-alt';
          break;
        default:
          iconClass = 'fa-clock';
      }
      
      const activityItem = $(`
        <div class="activity-item">
          <div class="activity-icon">
            <i class="fas ${iconClass}"></i>
          </div>
          <div class="activity-content">
            <div class="activity-header">
              <span class="activity-user">${activity.user}</span>
              <span class="activity-time">${activity.time}</span>
            </div>
            <div class="activity-description">${activity.description}</div>
          </div>
        </div>
      `);
      
      activitiesList.append(activityItem);
    });
  }
}

/**
 * Load client followups (would fetch from API in real implementation)
 */
function loadClientFollowups(clientId) {
  // Simulate API call
  console.log(`Loading followups for client ID ${clientId}`);
  
  // For demo purposes, let's just show some sample followups
  const followupsList = $('#client-followups-list');
  followupsList.empty();
  
  const sampleFollowups = [
    {
      date: 'Apr 25, 2025 10:00 AM',
      note: 'Schedule demo of HR services',
      status: 'pending'
    },
    {
      date: 'Apr 15, 2025 2:30 PM',
      note: 'Follow up on initial proposal',
      status: 'completed'
    },
    {
      date: 'Apr 5, 2025 11:00 AM',
      note: 'Initial consultation call',
      status: 'completed'
    }
  ];
  
  if (sampleFollowups.length === 0) {
    followupsList.html('<p class="no-data">No followups scheduled for this client.</p>');
  } else {
    sampleFollowups.forEach(followup => {
      const followupItem = $(`
        <div class="followup-item">
          <div class="followup-date">${followup.date}</div>
          <div class="followup-note">${followup.note}</div>
          <div class="followup-status ${followup.status}">${followup.status}</div>
        </div>
      `);
      
      followupsList.append(followupItem);
    });
  }
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info', duration = 3000) {
  // Remove existing toasts
  const existingToasts = document.querySelectorAll('.toast');
  existingToasts.forEach(toast => {
    if (toast.classList.contains('hiding')) return;
    
    toast.classList.add('hiding');
    setTimeout(() => {
      toast.remove();
    }, 300);
  });
  
  // Create toast element
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  
  // Create icon based on type
  let icon = '';
  switch (type) {
    case 'success':
      icon = '<i class="fas fa-check-circle"></i>';
      break;
    case 'warning':
      icon = '<i class="fas fa-exclamation-triangle"></i>';
      break;
    case 'error':
      icon = '<i class="fas fa-times-circle"></i>';
      break;
    default:
      icon = '<i class="fas fa-info-circle"></i>';
  }
  
  // Set content
  toast.innerHTML = `
    <div class="toast-icon">${icon}</div>
    <div class="toast-content">${message}</div>
    <button class="toast-close"><i class="fas fa-times"></i></button>
  `;
  
  // Add to document
  document.body.appendChild(toast);
  
  // Show toast with animation
  setTimeout(() => {
    toast.classList.add('show');
  }, 10);
  
  // Add close button functionality
  const closeBtn = toast.querySelector('.toast-close');
  closeBtn.addEventListener('click', () => {
    toast.classList.remove('show');
    toast.classList.add('hiding');
    setTimeout(() => {
      toast.remove();
    }, 300);
  });
  
  // Auto-close after duration
  setTimeout(() => {
    if (document.body.contains(toast)) {
      toast.classList.remove('show');
      toast.classList.add('hiding');
      setTimeout(() => {
        if (document.body.contains(toast)) {
          toast.remove();
        }
      }, 300);
    }
  }, duration);
  
  return toast;
}

/**
 * Show confirmation modal
 */
function showConfirmModal(message, callback) {
  // Create modal if it doesn't exist
  let confirmModal = document.getElementById('confirm-modal');
  
  if (!confirmModal) {
    confirmModal = document.createElement('div');
    confirmModal.id = 'confirm-modal';
    confirmModal.className = 'modal confirm-modal';
    confirmModal.innerHTML = `
      <div class="modal-content">
        <div class="modal-header">
          <h3>Confirm Action</h3>
          <button class="modal-close">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <p id="confirm-message"></p>
        </div>
        <div class="modal-footer">
          <button class="btn-secondary" id="confirm-cancel">Cancel</button>
          <button class="btn-primary" id="confirm-ok">Confirm</button>
        </div>
      </div>
    `;
    
    document.body.appendChild(confirmModal);
    
    // Close modal when clicking the X or Cancel button
    const closeBtn = confirmModal.querySelector('.modal-close');
    const cancelBtn = confirmModal.querySelector('#confirm-cancel');
    
    closeBtn.addEventListener('click', () => {
      confirmModal.style.display = 'none';
    });
    
    cancelBtn.addEventListener('click', () => {
      confirmModal.style.display = 'none';
    });
    
    // Close modal when clicking outside the modal content
    confirmModal.addEventListener('click', function(e) {
      if (e.target === this) {
        confirmModal.style.display = 'none';
      }
    });
  }
  
  // Update message
  const messageEl = confirmModal.querySelector('#confirm-message');
  messageEl.textContent = message;
  
  // Show modal
  confirmModal.style.display = 'flex';
  
  // Handle confirm button
  const confirmBtn = confirmModal.querySelector('#confirm-ok');
  
  // Remove existing event listeners
  const newConfirmBtn = confirmBtn.cloneNode(true);
  confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
  
  // Add new event listener
  newConfirmBtn.addEventListener('click', () => {
    confirmModal.style.display = 'none';
    if (typeof callback === 'function') {
      callback();
    }
  });
}

// Add CSS for toast and confirmation modal if not already in stylesheet
if (!document.getElementById('client-management-styles')) {
  const styles = document.createElement('style');
  styles.id = 'client-management-styles';
  styles.textContent = `
    /* Toast Notifications */
    .toast {
      position: fixed;
      top: 20px;
      right: 20px;
      min-width: 300px;
      max-width: 400px;
      background-color: white;
      color: #333;
      padding: 15px;
      border-radius: 4px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      display: flex;
      align-items: center;
      z-index: 10000;
      transform: translateY(-20px);
      opacity: 0;
      transition: transform 0.3s, opacity 0.3s;
    }
    
    .toast.show {
      transform: translateY(0);
      opacity: 1;
    }
    
    .toast.hiding {
      transform: translateY(-20px);
      opacity: 0;
    }
    
    .toast-icon {
      margin-right: 15px;
      font-size: 1.2rem;
    }
    
    .toast-content {
      flex: 1;
    }
    
    .toast-close {
      background: none;
      border: none;
      color: #999;
      cursor: pointer;
      font-size: 0.8rem;
    }
    
    .toast-success {
      border-left: 4px solid #1cc88a;
    }
    
    .toast-success .toast-icon {
      color: #1cc88a;
    }
    
    .toast-info {
      border-left: 4px solid #36b9cc;
    }
    
    .toast-info .toast-icon {
      color: #36b9cc;
    }
    
    .toast-warning {
      border-left: 4px solid #f6c23e;
    }
    
    .toast-warning .toast-icon {
      color: #f6c23e;
    }
    
    .toast-error {
      border-left: 4px solid #e74a3b;
    }
    
    .toast-error .toast-icon {
      color: #e74a3b;
    }
    
    /* Confirmation Modal */
    .confirm-modal .modal-content {
      max-width: 400px;
    }
    
    .confirm-modal .modal-body {
      padding: 20px;
    }
    
    .confirm-modal .modal-footer {
      justify-content: flex-end;
      gap: 10px;
    }
  `;
  
  document.head.appendChild(styles);
}
