/**
 * admin-login.js
 * Backsure Global Support - Admin Login JavaScript
 * Handles the admin login authentication process
 */

document.addEventListener('DOMContentLoaded', function() {
  const loginForm = document.getElementById('admin-login-form');
  const errorMessage = document.getElementById('error-message');
  
  if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Clear previous error messages
      if (errorMessage) {
        errorMessage.textContent = '';
        errorMessage.style.display = 'none';
      }
      
      // Get form values
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value.trim();
      
      // Basic validation
      if (!username || !password) {
        showError('Please enter both username and password');
        return;
      }
      
      // Show loading state
      const submitBtn = loginForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
      submitBtn.disabled = true;
      
      // In a real implementation, we would send these credentials to a server
      // For demo purposes, we'll use a simple check for predefined credentials
      
      // Simulating a server request with setTimeout
      setTimeout(() => {
        if (authenticateUser(username, password)) {
          // Create a mock token (in a real app, this would come from the server)
          const mockToken = generateMockToken();
          
          // Store auth token and admin status
          localStorage.setItem('authToken', mockToken);
          localStorage.setItem('adminLoggedIn', 'true');
          localStorage.setItem('adminUser', JSON.stringify({
            username: username,
            role: 'admin',
            name: 'Admin User',
            lastLogin: new Date().toISOString()
          }));
          
          // Redirect to dashboard
          window.location.href = 'admin-dashboard.html';
        } else {
          // Show error message
          showError('Invalid username or password');
          
          // Reset button
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        }
      }, 1000); // Simulate server delay
    });
  }
  
  // Check if already logged in
  const isAdminLoggedIn = localStorage.getItem('adminLoggedIn') === 'true';
  const authToken = localStorage.getItem('authToken');
  
  if (isAdminLoggedIn && authToken) {
    // If on login page, redirect to dashboard
    if (window.location.pathname.includes('login.html')) {
      window.location.href = 'admin-dashboard.html';
    }
  }
  
  // Add event listener for forgot password link
  const forgotPasswordLink = document.querySelector('.forgot-password');
  if (forgotPasswordLink) {
    forgotPasswordLink.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Show password reset form or modal
      const resetModal = document.getElementById('password-reset-modal');
      if (resetModal) {
        resetModal.style.display = 'flex';
      } else {
        alert('Password reset functionality will be available soon.');
      }
    });
  }
});

/**
 * Authenticate user against predefined credentials
 * In a real app, this would be a server request
 */
function authenticateUser(username, password) {
  // Demo admin accounts - in a real app, this would be server-side
  const adminAccounts = [
    { username: 'admin', password: 'admin123' },
    { username: 'superadmin', password: 'super123' }
  ];
  
  return adminAccounts.some(account => 
    account.username === username && account.password === password);
}

/**
 * Show error message
 */
function showError(message) {
  const errorMessage = document.getElementById('error-message');
  
  if (errorMessage) {
    errorMessage.textContent = message;
    errorMessage.style.display = 'block';
  } else {
    // If error element doesn't exist, create it
    const loginForm = document.getElementById('admin-login-form');
    const errorDiv = document.createElement('div');
    errorDiv.id = 'error-message';
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    
    // Insert after the password field
    const passwordField = document.getElementById('password');
    if (passwordField && passwordField.parentNode) {
      passwordField.parentNode.insertBefore(errorDiv, passwordField.nextSibling);
    } else if (loginForm) {
      // Or at the top of the form if password field not found
      loginForm.prepend(errorDiv);
    }
  }
}

/**
 * Generate a mock authentication token
 * In a real app, this would come from the server
 */
function generateMockToken() {
  const tokenParts = [];
  const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  
  // Generate 3 parts of the token
  for (let part = 0; part < 3; part++) {
    let segment = '';
    for (let i = 0; i < 16; i++) {
      segment += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    tokenParts.push(segment);
  }
  
  // Join with dots like a JWT
  return tokenParts.join('.');
}

/**
 * Handle password reset request
 */
function handlePasswordReset(email) {
  // In a real app, this would send a request to the server
  console.log('Password reset requested for:', email);
  
  // Show success message
  alert(`If ${email} is associated with an account, a password reset link will be sent.`);
  
  // Close modal if exists
  const resetModal = document.getElementById('password-reset-modal');
  if (resetModal) {
    resetModal.style.display = 'none';
  }
  
  // Reset form if exists
  const resetForm = document.getElementById('password-reset-form');
  if (resetForm) {
    resetForm.reset();
  }
}