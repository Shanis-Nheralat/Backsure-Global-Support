/**
 * settings.js
 * Backsure Global Support - Settings Page JavaScript
 * Handles functionality for the site settings administration
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize settings tabs
  initializeSettingsTabs();
  
  // Initialize form submissions
  initializeFormSubmissions();
  
  // Initialize media uploads
  initializeMediaUploads();
  
  // Initialize character counters
  initializeCharacterCounters();
  
  // Initialize maintenance mode toggle
  initializeMaintenanceMode();
  
  // Initialize backup functionality
  initializeBackupFunctions();
  
  // Initialize "Save All" functionality
  initializeSaveAllButton();
  
  // Initialize sitemap regeneration
  initializeSitemapRegeneration();
  
  // Initialize cache clearing
  initializeCacheClear();
  
  // Initialize test email sending
  initializeTestEmail();
});

/**
 * Initialize settings tabs navigation
 */
function initializeSettingsTabs() {
  const tabLinks = document.querySelectorAll('.settings-tabs li');
  const tabPanels = document.querySelectorAll('.settings-panel');
  
  if (!tabLinks.length || !tabPanels.length) return;
  
  tabLinks.forEach(tab => {
    tab.addEventListener('click', function() {
      // Remove active class from all tabs and panels
      tabLinks.forEach(t => t.classList.remove('active'));
      tabPanels.forEach(p => p.classList.remove('active'));
      
      // Add active class to clicked tab
      this.classList.add('active');
      
      // Activate corresponding panel
      const tabId = this.getAttribute('data-tab');
      const panel = document.getElementById(`${tabId}-settings`);
      
      if (panel) {
        panel.classList.add('active');
      }
      
      // Save active tab to localStorage
      localStorage.setItem('active-settings-tab', tabId);
    });
  });
  
  // Check for saved active tab
  const savedTab = localStorage.getItem('active-settings-tab');
  
  if (savedTab) {
    const tab = document.querySelector(`.settings-tabs li[data-tab="${savedTab}"]`);
    
    if (tab) {
      tab.click();
    }
  }
}

/**
 * Initialize form submissions
 */
function initializeFormSubmissions() {
  const settingsForms = document.querySelectorAll('.settings-panel form');
  
  settingsForms.forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Get form ID to identify which settings are being saved
      const formId = this.id;
      
      // Gather form data
      const formData = new FormData(this);
      const formObject = {};
      
      formData.forEach((value, key) => {
        // Handle checkbox arrays (multiple values for one key)
        if (key.endsWith('[]')) {
          const baseKey = key.slice(0, -2);
          
          if (!formObject[baseKey]) {
            formObject[baseKey] = [];
          }
          
          formObject[baseKey].push(value);
        } else {
          formObject[key] = value;
        }
      });
      
      // Show loading state
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
      
      // In a real app, send data to server
      // For demo purposes, simulate server delay
      setTimeout(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        // Show success message
        const settingType = getSettingTypeFromFormId(formId);
        showNotification(`${settingType} settings saved successfully!`, 'success');
        
        // Save to localStorage for demo persistence
        localStorage.setItem(`settings_${formId}`, JSON.stringify(formObject));
        
        // If special handling is needed for certain settings
        handleSpecialSettings(formId, formObject);
      }, 1000);
    });
    
    // Load saved settings from localStorage
    loadSavedSettings(form);
  });
}

/**
 * Get friendly setting type name from form ID
 */
function getSettingTypeFromFormId(formId) {
  switch (formId) {
    case 'general-settings-form':
      return 'General';
    case 'branding-settings-form':
      return 'Branding';
    case 'contact-settings-form':
      return 'Contact Information';
    case 'social-settings-form':
      return 'Social Media';
    case 'seo-settings-form':
      return 'SEO';
    case 'notifications-settings-form':
      return 'Notification';
    case 'performance-settings-form':
      return 'Performance';
    case 'security-settings-form':
      return 'Security';
    case 'api-settings-form':
      return 'API Integrations';
    default:
      return '';
  }
}

/**
 * Load saved settings from localStorage
 */
function loadSavedSettings(form) {
  const formId = form.id;
  const savedSettings = localStorage.getItem(`settings_${formId}`);
  
  if (!savedSettings) return;
  
  try {
    const settings = JSON.parse(savedSettings);
    
    // Set form values
    for (const key in settings) {
      const value = settings[key];
      const elements = form.querySelectorAll(`[name="${key}"], [name="${key}[]"]`);
      
      elements.forEach(element => {
        if (element.type === 'checkbox') {
          // Handle checkbox arrays
          if (Array.isArray(value)) {
            element.checked = value.includes(element.value);
          } else {
            element.checked = value === 'on' || value === true;
          }
        } else if (element.type === 'radio') {
          element.checked = element.value === value;
        } else {
          element.value = value;
        }
      });
    }
    
    // Trigger any dependent toggles
    if (formId === 'general-settings-form') {
      const maintenanceMode = form.querySelector('#maintenance-mode');
      if (maintenanceMode) {
        toggleMaintenanceMessage(maintenanceMode.checked);
      }
    }
  } catch (error) {
    console.error('Error loading saved settings:', error);
  }
}

/**
 * Handle special settings that need additional processing
 */
function handleSpecialSettings(formId, formData) {
  // Custom CSS processing
  if (formId === 'branding-settings-form' && formData.custom_css) {
    console.log('Updating custom CSS...');
    // In a real app, this would update a custom CSS file
  }
  
  // Robots.txt processing
  if (formId === 'seo-settings-form' && formData.robots_txt) {
    console.log('Updating robots.txt...');
    // In a real app, this would write to robots.txt file
  }
  
  // Performance settings
  if (formId === 'performance-settings-form') {
    if (formData.enable_caching === 'on') {
      console.log('Enabling page caching...');
      // In a real app, enable caching system
    } else {
      console.log('Disabling page caching...');
      // In a real app, disable caching system
    }
  }
}

/**
 * Initialize media upload functionality
 */
function initializeMediaUploads() {
  const uploadButtons = document.querySelectorAll('.media-upload-btn');
  const removeButtons = document.querySelectorAll('.media-remove-btn');
  const mediaModal = document.getElementById('media-modal');
  const selectMediaBtn = document.getElementById('select-media');
  const mediaItems = document.querySelectorAll('.media-item');
  const closeModalButtons = document.querySelectorAll('.close-modal');
  
  // Media upload buttons
  if (uploadButtons.length) {
    uploadButtons.forEach(button => {
      button.addEventListener('click', function() {
        if (!mediaModal) return;
        
        // Set current target for selection
        const target = this.getAttribute('data-target');
        mediaModal.setAttribute('data-target', target);
        
        // Show modal
        mediaModal.style.display = 'flex';
      });
    });
  }
  
  // Media remove buttons
  if (removeButtons.length) {
    removeButtons.forEach(button => {
      button.addEventListener('click', function() {
        const target = this.getAttribute('data-target');
        const previewContainer = document.getElementById(target);
        
        if (previewContainer) {
          const img = previewContainer.querySelector('img');
          
          if (img) {
            // Replace with placeholder
            img.src = 'images/placeholder.png';
          }
        }
      });
    });
  }
  
  // Media selection
  if (mediaItems.length && selectMediaBtn) {
    // Select media item on click
    let selectedMediaItem = null;
    
    mediaItems.forEach(item => {
      item.addEventListener('click', function() {
        // Remove selection from all items
        mediaItems.forEach(i => i.classList.remove('selected'));
        
        // Add selection to clicked item
        this.classList.add('selected');
        selectedMediaItem = this;
      });
    });
    
    // Select media button
    selectMediaBtn.addEventListener('click', function() {
      if (!selectedMediaItem || !mediaModal) return;
      
      // Get target container id
      const targetId = mediaModal.getAttribute('data-target');
      const targetContainer = document.getElementById(targetId);
      
      if (targetContainer) {
        const imgSrc = selectedMediaItem.querySelector('img').src;
        const img = targetContainer.querySelector('img');
        
        if (img) {
          img.src = imgSrc;
        }
      }
      
      // Close modal
      mediaModal.style.display = 'none';
    });
  }
  
  // Close modal buttons
  if (closeModalButtons.length) {
    closeModalButtons.forEach(button => {
      button.addEventListener('click', function() {
        const modal = this.closest('.modal');
        
        if (modal) {
          modal.style.display = 'none';
        }
      });
    });
  }
  
  // Close modal when clicking outside
  window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
      e.target.style.display = 'none';
    }
  });
  
  // File upload handling
  initializeFileUpload();
}

/**
 * Initialize file upload functionality
 */
function initializeFileUpload() {
  const dropzone = document.getElementById('upload-dropzone');
  const fileInput = document.getElementById('file-input');
  const selectFilesBtn = document.getElementById('select-files');
  
  if (!dropzone || !fileInput || !selectFilesBtn) return;
  
  // Select files button
  selectFilesBtn.addEventListener('click', function() {
    fileInput.click();
  });
  
  // File input change
  fileInput.addEventListener('change', function() {
    if (this.files.length > 0) {
      // In a real app, handle file uploads
      console.log('Files selected:', this.files);
      
      // Show preview
      showFilePreview(this.files);
    }
  });
  
  // Drag and drop functionality
  dropzone.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('dragover');
  });
  
  dropzone.addEventListener('dragleave', function() {
    this.classList.remove('dragover');
  });
  
  dropzone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
    
    if (e.dataTransfer.files.length > 0) {
      // In a real app, handle file uploads
      console.log('Files dropped:', e.dataTransfer.files);
      
      // Show preview
      showFilePreview(e.dataTransfer.files);
    }
  });
}

/**
 * Show file preview for uploaded files
 */
function showFilePreview(files) {
  // For demo purposes, just show success message
  const fileCount = files.length;
  const fileText = fileCount === 1 ? 'file' : 'files';
  
  showNotification(`${fileCount} ${fileText} would be processed in a production environment.`, 'info');
}

/**
 * Initialize character counters for SEO fields
 */
function initializeCharacterCounters() {
  const metaTitle = document.getElementById('meta-title');
  const titleCount = document.getElementById('title-characters');
  const metaDescription = document.getElementById('meta-description');
  const descriptionCount = document.getElementById('description-characters');
  
  if (metaTitle && titleCount) {
    metaTitle.addEventListener('input', function() {
      titleCount.textContent = this.value.length;
      
      if (this.value.length > 60) {
        titleCount.classList.add('over-limit');
      } else {
        titleCount.classList.remove('over-limit');
      }
    });
  }
  
  if (metaDescription && descriptionCount) {
    metaDescription.addEventListener('input', function() {
      descriptionCount.textContent = this.value.length;
      
      if (this.value.length > 160) {
        descriptionCount.classList.add('over-limit');
      } else {
        descriptionCount.classList.remove('over-limit');
      }
    });
  }
}

/**
 * Initialize maintenance mode toggle
 */
function initializeMaintenanceMode() {
  const maintenanceToggle = document.getElementById('maintenance-mode');
  const messageGroup = document.querySelector('.maintenance-message-group');
  
  if (maintenanceToggle && messageGroup) {
    maintenanceToggle.addEventListener('change', function() {
      toggleMaintenanceMessage(this.checked);
    });
    
    // Initialize on load
    toggleMaintenanceMessage(maintenanceToggle.checked);
  }
}

/**
 * Toggle maintenance message visibility
 */
function toggleMaintenanceMessage(isVisible) {
  const messageGroup = document.querySelector('.maintenance-message-group');
  
  if (messageGroup) {
    messageGroup.style.display = isVisible ? 'block' : 'none';
  }
}

/**
 * Initialize backup functionality
 */
function initializeBackupFunctions() {
  const createBackupBtn = document.getElementById('create-backup');
  const saveBackupSettingsBtn = document.getElementById('save-backup-settings');
  const backupFile = document.getElementById('backup-file');
  const restoreBackupBtn = document.getElementById('restore-backup');
  const downloadButtons = document.querySelectorAll('.download-btn');
  const restoreButtons = document.querySelectorAll('.restore-btn');
  const deleteButtons = document.querySelectorAll('.delete-btn');
  
  // Create backup button
  if (createBackupBtn) {
    createBackupBtn.addEventListener('click', function() {
      // Get selected backup options
      const options = [];
      const checkboxes = document.querySelectorAll('input[name="backup_options[]"]:checked');
      
      checkboxes.forEach(checkbox => {
        options.push(checkbox.value);
      });
      
      if (options.length === 0) {
        showNotification('Please select at least one backup option.', 'error');
        return;
      }
      
      // Show loading state
      this.disabled = true;
      this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Backup...';
      
      // In a real app, send request to create backup
      // For demo purposes, simulate server delay
      setTimeout(() => {
        // Reset button
        this.disabled = false;
        this.innerHTML = '<i class="fas fa-download"></i> Create Backup';
        
        // Show success message
        showNotification('Backup created successfully!', 'success');
      }, 2000);
    });
  }
  
  // Save backup settings button
  if (saveBackupSettingsBtn) {
    saveBackupSettingsBtn.addEventListener('click', function() {
      // Get backup settings
      const autoBackup = document.getElementById('auto-backup').checked;
      const frequency = document.getElementById('backup-frequency').value;
      const retention = document.getElementById('backup-retention').value;
      
      // Show loading state
      this.disabled = true;
      this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
      
      // In a real app, send settings to server
      // For demo purposes, simulate server delay
      setTimeout(() => {
        // Reset button
        this.disabled = false;
        this.innerHTML = '<i class="fas fa-save"></i> Save Backup Settings';
        
        // Show success message
        showNotification('Backup settings saved successfully!', 'success');
        
        // Save to localStorage for demo persistence
        localStorage.setItem('backup_settings', JSON.stringify({
          auto_backup: autoBackup,
          frequency: frequency,
          retention: retention
        }));
      }, 1000);
    });
  }
  
  // Backup file input
  if (backupFile && restoreBackupBtn) {
    backupFile.addEventListener('change', function() {
      if (this.files.length > 0) {
        // Enable restore button
        restoreBackupBtn.disabled = false;
      } else {
        // Disable restore button
        restoreBackupBtn.disabled = true;
      }
    });
  }
  
  // Restore backup button
  if (restoreBackupBtn) {
    restoreBackupBtn.addEventListener('click', function() {
      if (!backupFile || backupFile.files.length === 0) {
        showNotification('Please select a backup file to restore.', 'error');
        return;
      }
      
      // Confirm restore
      if (!confirm('Warning: Restoring a backup will overwrite your current website data. Are you sure you want to continue?')) {
        return;
      }
      
      // Show loading state
      this.disabled = true;
      this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Restoring...';
      
      // In a real app, send file to server for restore
      // For demo purposes, simulate server delay
      setTimeout(() => {
        // Reset button
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-undo"></i> Restore Backup';
        
        // Show success message
        showNotification('Backup restored successfully! Reloading page...', 'success');
        
        // Reload page after restoration
        setTimeout(() => {
          window.location.reload();
        }, 2000);
      }, 3000);
    });
  }
  
  // Download backup buttons
  if (downloadButtons.length) {
    downloadButtons.forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        
        // In a real app, initiate file download
        // For demo purposes, show message
        showNotification('Backup download started.', 'info');
      });
    });
  }
  
  // Restore from backup list buttons
  if (restoreButtons.length) {
    restoreButtons.forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Get backup date from row
        const row = this.closest('tr');
        const date = row.cells[0].textContent;
        
        // Confirm restore
        if (!confirm(`Warning: You are about to restore the backup from "${date}". This will overwrite your current website data. Are you sure you want to continue?`)) {
          return;
        }
        
        // Show loading on the row
        row.classList.add('restoring');
        
        // In a real app, send restore request to server
        // For demo purposes, simulate server delay
        setTimeout(() => {
          // Remove restoring class
          row.classList.remove('restoring');
          
          // Show success message
          showNotification('Backup restored successfully! Reloading page...', 'success');
          
          // Reload page after restoration
          setTimeout(() => {
            window.location.reload();
          }, 2000);
        }, 3000);
      });
    });
  }
  
  // Delete backup buttons
  if (deleteButtons.length) {
    deleteButtons.forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Get backup date from row
        const row = this.closest('tr');
        const date = row.cells[0].textContent;
        
        // Confirm delete
        if (!confirm(`Are you sure you want to delete the backup from "${date}"? This action cannot be undone.`)) {
          return;
        }
        
        // Show deleting animation
        row.classList.add('deleting');
        
        // In a real app, send delete request to server
        // For demo purposes, simulate server delay
        setTimeout(() => {
          // Remove row
          row.remove();
          
          // Show success message
          showNotification('Backup deleted successfully.', 'success');
        }, 500);
      });
    });
  }
}

/**
 * Initialize "Save All" button functionality
 */
function initializeSaveAllButton() {
  const saveAllBtn = document.getElementById('save-all-settings');
  
  if (!saveAllBtn) return;
  
  saveAllBtn.addEventListener('click', function() {
    // Find all visible forms in the active tab panel
    const activePanel = document.querySelector('.settings-panel.active');
    
    if (!activePanel) return;
    
    const forms = activePanel.querySelectorAll('form');
    
    if (forms.length === 0) return;
    
    // Show loading state
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving All...';
    
    // Submit each form
    let formsProcessed = 0;
    
    forms.forEach(form => {
      // Create form submission event
      const submitEvent = new Event('submit', {
        bubbles: true,
        cancelable: true
      });
      
      // Submit form
      form.dispatchEvent(submitEvent);
      
      // Count processed forms
      formsProcessed++;
    });
    
    // Reset button after all forms are processed
    setTimeout(() => {
      this.disabled = false;
      this.innerHTML = '<i class="fas fa-save"></i> Save All Changes';
      
      // Show success message
      if (formsProcessed > 0) {
        showNotification('All settings saved successfully!', 'success');
      }
    }, 1500);
  });
}

/**
 * Initialize sitemap regeneration functionality
 */
function initializeSitemapRegeneration() {
  const regenerateBtn = document.getElementById('regenerate-sitemap');
  
  if (!regenerateBtn) return;
  
  regenerateBtn.addEventListener('click', function() {
    // Show loading state
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Regenerating...';
    
    // In a real app, send request to regenerate sitemap
    // For demo purposes, simulate server delay
    setTimeout(() => {
      // Reset button
      this.disabled = false;
      this.innerHTML = '<i class="fas fa-sync"></i> Regenerate Sitemap Now';
      
      // Show success message
      showNotification('Sitemap regenerated successfully!', 'success');
    }, 2000);
  });
}

/**
 * Initialize cache clearing functionality
 */
function initializeCacheClear() {
  const clearCacheBtn = document.getElementById('clear-cache');
  
  if (!clearCacheBtn) return;
  
  clearCacheBtn.addEventListener('click', function() {
    // Show loading state
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Clearing...';
    
    // In a real app, send request to clear cache
    // For demo purposes, simulate server delay
    setTimeout(() => {
      // Reset button
      this.disabled = false;
      this.innerHTML = '<i class="fas fa-broom"></i> Clear Cache';
      
      // Show success message
      showNotification('Cache cleared successfully!', 'success');
    }, 1500);
  });
}

/**
 * Initialize test email functionality
 */
function initializeTestEmail() {
  const testEmailBtn = document.getElementById('test-email');
  
  if (!testEmailBtn) return;
  
  testEmailBtn.addEventListener('click', function() {
    // Get email settings
    const host = document.getElementById('smtp-host').value;
    const port = document.getElementById('smtp-port').value;
    const username = document.getElementById('smtp-username').value;
    const password = document.getElementById('smtp-password').value;
    
    // Basic validation
    if (!host || !port || !username || !password) {
      showNotification('Please fill in all SMTP settings before sending a test email.', 'error');
      return;
    }
    
    // Show loading state
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    
    // In a real app, send test email request
    // For demo purposes, simulate server delay
    setTimeout(() => {
      // Random success or failure for demo purposes
      const success = Math.random() > 0.3; // 70% success rate
      
      // Reset button
      this.disabled = false;
      this.innerHTML = '<i class="fas fa-paper-plane"></i> Send Test Email';
      
      // Show message
      if (success) {
        showNotification('Test email sent successfully!', 'success');
      } else {
        showNotification('Failed to send test email. Please check your SMTP settings.', 'error');
      }
    }, 2000);
  });
}

/**
 * Show notification message
 */
function showNotification(message, type = 'info') {
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `notification ${type}`;
  notification.innerHTML = `
    <div class="notification-content">
      <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
      <span>${message}</span>
    </div>
    <button class="notification-close"><i class="fas fa-times"></i></button>
  `;
  
  // Add to document
  document.body.appendChild(notification);
  
  // Add close button functionality
  const closeButton = notification.querySelector('.notification-close');
  
  if (closeButton) {
    closeButton.addEventListener('click', function() {
      document.body.removeChild(notification);
    });
  }
  
  // Auto-remove after 5 seconds
  setTimeout(() => {
    if (document.body.contains(notification)) {
      notification.classList.add('fade-out');
      
      setTimeout(() => {
        if (document.body.contains(notification)) {
          document.body.removeChild(notification);
        }
      }, 300);
    }
  }, 5000);
}

/**
 * Initialize color scheme preview 
 */
function initializeColorSchemePreview() {
  const colorOptions = document.querySelectorAll('.color-option input[type="radio"]');
  const primaryColorInput = document.getElementById('primary-color');
  const secondaryColorInput = document.getElementById('secondary-color');
  const accentColorInput = document.getElementById('accent-color');
  
  if (colorOptions.length) {
    colorOptions.forEach(option => {
      option.addEventListener('change', function() {
        if (this.checked) {
          const scheme = this.value;
          
          // Apply color scheme preview
          applyColorScheme(scheme);
        }
      });
    });
  }
  
  // Custom color inputs
  if (primaryColorInput && secondaryColorInput && accentColorInput) {
    [primaryColorInput, secondaryColorInput, accentColorInput].forEach(input => {
      input.addEventListener('change', function() {
        // Select custom scheme
        const customOption = document.getElementById('color-custom');
        
        if (customOption) {
          customOption.checked = true;
        }
        
        // Apply custom colors
        applyCustomColors(
          primaryColorInput.value,
          secondaryColorInput.value,
          accentColorInput.value
        );
      });
    });
  }
}

/**
 * Apply color scheme
 */
function applyColorScheme(scheme) {
  // In a real app, this would apply CSS variables or update a stylesheet
  console.log(`Applying color scheme: ${scheme}`);
  
  // For demo purposes, show message
  showNotification(`Color scheme "${scheme}" would be applied in a production environment.`, 'info');
  
  // Predefined color sets
  const colorSets = {
    default: {
      primary: '#062767',
      secondary: '#b19763',
      accent: '#1cc88a'
    },
    blue: {
      primary: '#1a56db',
      secondary: '#3f83f8',
      accent: '#0694a2'
    },
    green: {
      primary: '#046c4e',
      secondary: '#0e9f6e',
      accent: '#e3a008'
    },
    purple: {
      primary: '#5521b5',
      secondary: '#8b5cf6',
      accent: '#f05252'
    },
    dark: {
      primary: '#111827',
      secondary: '#374151',
      accent: '#f59e0b'
    }
  };
  
  // Update color inputs
  if (scheme in colorSets) {
    const colors = colorSets[scheme];
    
    const primaryColorInput = document.getElementById('primary-color');
    const secondaryColorInput = document.getElementById('secondary-color');
    const accentColorInput = document.getElementById('accent-color');
    
    if (primaryColorInput) primaryColorInput.value = colors.primary;
    if (secondaryColorInput) secondaryColorInput.value = colors.secondary;
    if (accentColorInput) accentColorInput.value = colors.accent;
  }
}

/**
 * Apply custom colors
 */
function applyCustomColors(primary, secondary, accent) {
  // In a real app, this would apply CSS variables or update a stylesheet
  console.log(`Applying custom colors - Primary: ${primary}, Secondary: ${secondary}, Accent: ${accent}`);
  
  // For demo purposes, show message
  showNotification('Custom colors would be applied in a production environment.', 'info');
}

// Call color scheme preview initialization
initializeColorSchemePreview();
