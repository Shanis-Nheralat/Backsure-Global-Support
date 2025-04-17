/**
 * admin-login.js
 * Handles login, signup, and password recovery functionality
 */

document.addEventListener('DOMContentLoaded', function() {
  // Get form containers
  const loginContainer = document.getElementById('login-form-container');
  const forgotContainer = document.getElementById('forgot-password-container');
  const signupContainer = document.getElementById('signup-container');
  
  // Get forms
  const loginForm = document.getElementById('admin-login-form');
  const forgotForm = document.getElementById('forgot-password-form');
  const signupForm = document.getElementById('signup-form');
  
  // Get navigation links
  const forgotPasswordLink = document.getElementById('forgot-password-link');
  const signupLink = document.getElementById('signup-link');
  const loginLink = document.getElementById('login-link');
  const backToLoginLink = document.getElementById('back-to-login-link');
  
  // Function to show a specific form container
  function showFormContainer(container) {
    console.log("Showing container:", container.id);
    // Hide all containers
    document.querySelectorAll('.login-form-container').forEach(function(c) {
      c.classList.remove('active');
    });
    
    // Show selected container
    container.classList.add('active');
    
    // Clear all forms and error messages
    document.querySelectorAll('form').forEach(form => form.reset());
    clearErrorMessages();
    
    // If signup form is shown, reset password strength meter
    if (container.id === 'signup-container') {
      updatePasswordStrength('');
    }
  }
  
  // Switch between forms
  if (forgotPasswordLink) {
    forgotPasswordLink.addEventListener('click', function(e) {
      e.preventDefault();
      showFormContainer(forgotContainer);
    });
  }
  
  if (signupLink) {
    signupLink.addEventListener('click', function(e) {
      e.preventDefault();
      console.log("Signup link clicked");
      showFormContainer(signupContainer);
    });
  }
  
  if (loginLink) {
    loginLink.addEventListener('click', function(e) {
      e.preventDefault();
      showFormContainer(loginContainer);
    });
  }
  
  if (backToLoginLink) {
    backToLoginLink.addEventListener('click', function(e) {
      e.preventDefault();
      showFormContainer(loginContainer);
    });
  }
  
  // Login form submission
  if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Clear previous error messages
      clearErrorMessages();
      
      // Get form values
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value.trim();
      const rememberMe = document.getElementById('remember') ? document.getElementById('remember').checked : false;
      
      // Validate form
      if (!username || !password) {
        showErrorMessage('error-message', 'Please enter both username and password');
        return;
      }
      
      // Show loading state
      const submitBtn = loginForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
      submitBtn.disabled = true;
      
      // In a real app, this would be an AJAX request to the server
      // For demo purposes, we'll simulate authentication
      setTimeout(() => {
        // Check credentials
        if (authenticateUser(username, password)) {
          // Create mock token and store login state
          const mockToken = generateMockToken();
          localStorage.setItem('authToken', mockToken);
          localStorage.setItem('adminLoggedIn', 'true');
          localStorage.setItem('adminUser', JSON.stringify({
            username: username,
            name: 'Admin User',
            role: 'admin',
            lastLogin: new Date().toISOString()
          }));
          
          // Save remember me setting
          if (rememberMe) {
            localStorage.setItem('rememberMe', 'true');
          } else {
            localStorage.removeItem('rememberMe');
          }
          
          // Redirect to dashboard
          window.location.href = 'admin-dashboard.html';
        } else {
          // Show error message
          showErrorMessage('error-message', 'Invalid username or password');
          
          // Reset button
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        }
      }, 1000);
    });
  }
  
  // Forgot password form submission
  if (forgotForm) {
    forgotForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Clear previous messages
      clearErrorMessages();
      
      // Get email
      const email = document.getElementById('reset-email').value.trim();
      
      // Validate email
      if (!email) {
        showErrorMessage('forgot-error-message', 'Please enter your email address');
        return;
      }
      
      if (!isValidEmail(email)) {
        showErrorMessage('forgot-error-message', 'Please enter a valid email address');
        return;
      }
      
      // Show loading state
      const submitBtn = forgotForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
      submitBtn.disabled = true;
      
      // In a real app, this would be an AJAX request to the server
      // For demo purposes, we'll simulate sending a reset email
      setTimeout(() => {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        // Show success message
        showSuccessMessage('forgot-success-message', 'Password reset instructions have been sent to your email address. Please check your inbox.');
        
        // Clear the form
        forgotForm.reset();
      }, 1500);
    });
  }
  
  // Signup form submission
  if (signupForm) {
    signupForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Clear previous messages
      clearErrorMessages();
      
      // Get form values
      const fullname = document.getElementById('fullname').value.trim();
      const email = document.getElementById('signup-email').value.trim();
      const username = document.getElementById('signup-username').value.trim();
      const password = document.getElementById('signup-password').value.trim();
      const confirmPassword = document.getElementById('confirm-password').value.trim();
      const termsAccepted = document.getElementById('terms').checked;
      
      // Validate form
      if (!fullname || !email || !username || !password || !confirmPassword) {
        showErrorMessage('signup-error-message', 'Please fill in all fields');
        return;
      }
      
      if (!isValidEmail(email)) {
        showErrorMessage('signup-error-message', 'Please enter a valid email address');
        return;
      }
      
      if (password.length < 8) {
        showErrorMessage('signup-error-message', 'Password must be at least 8 characters long');
        return;
      }
      
      if (password !== confirmPassword) {
        showErrorMessage('signup-error-message', 'Passwords do not match');
        return;
      }
      
      if (!termsAccepted) {
        showErrorMessage('signup-error-message', 'You must agree to the Terms & Conditions');
        return;
      }
      
      // Calculate password strength
      const strength = calculatePasswordStrength(password);
      if (strength < 2) {
        showErrorMessage('signup-error-message', 'Please choose a stronger password');
        return;
      }
      
      // Show loading state
      const submitBtn = signupForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
      submitBtn.disabled = true;
      
      // In a real app, this would be an AJAX request to the server
      // For demo purposes, we'll simulate account creation
      setTimeout(() => {
        // Check if username or email already exists
        if (username === 'admin' || email === 'admin@example.com') {
          showErrorMessage('signup-error-message', 'This username or email is already registered');
          
          // Reset button
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
          
          return;
        }
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        // Show success message
        const successMessage = document.createElement('div');
        successMessage.className = 'success-message show';
        successMessage.textContent = 'Account created successfully! A confirmation email has been sent to your email address.';
        
        const errorMessage = document.getElementById('signup-error-message');
        errorMessage.parentNode.insertBefore(successMessage, errorMessage);
        
        // Clear the form
        signupForm.reset();
        
        // Update password strength indicator
        updatePasswordStrength('');
        
        // Redirect to login form after 3 seconds
        setTimeout(() => {
          showFormContainer(loginContainer);
        }, 3000);
      }, 2000);
    });
  }
  
  // Password strength meter
  const passwordInput = document.getElementById('signup-password');
  if (passwordInput) {
    passwordInput.addEventListener('input', function() {
      const password = this.value;
      updatePasswordStrength(password);
    });
  }
  
  // Check for remembered login
  if (localStorage.getItem('rememberMe') === 'true' && localStorage.getItem('authToken')) {
    // Auto-fill username if available
    const adminUser = JSON.parse(localStorage.getItem('adminUser') || '{}');
    
    if (adminUser.username && document.getElementById('username')) {
      document.getElementById('username').value = adminUser.username;
      
      // Check remember me checkbox
      if (document.getElementById('remember')) {
        document.getElementById('remember').checked = true;
      }
    }
  }
});

/**
 * Clear all error and success messages
 */
function clearErrorMessages() {
  const errorMessages = document.querySelectorAll('.error-message');
  const successMessages = document.querySelectorAll('.success-message');
  
  errorMessages.forEach(msg => {
    msg.textContent = '';
    msg.classList.remove('show');
  });
  
  successMessages.forEach(msg => {
    if (msg.classList) {
      msg.textContent = '';
      msg.classList.remove('show');
    }
  });
}

/**
 * Show error message
 */
function showErrorMessage(elementId, message) {
  const element = document.getElementById(elementId);
  
  if (element) {
    element.textContent = message;
    element.classList.add('show');
  }
}

/**
 * Show success message
 */
function showSuccessMessage(elementId, message) {
  const element = document.getElementById(elementId);
  
  if (element) {
    element.textContent = message;
    element.classList.add('show');
  }
}

/**
 * Validate email format
 */
function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

/**
 * Calculate password strength
 * Returns a score from 0-4
 * 0: Too weak, 1: Weak, 2: Medium, 3: Strong, 4: Very strong
 */
function calculatePasswordStrength(password) {
  let score = 0;
  
  // Length check
  if (password.length >= 8) score++;
  if (password.length >= 12) score++;
  
  // Complexity checks
  if (/[0-9]/.test(password)) score++;
  if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
  if (/[^a-zA-Z0-9]/.test(password)) score++;
  
  // Cap score at 4
  return Math.min(4, score);
}

/**
 * Update password strength meter
 */
function updatePasswordStrength(password) {
  const strengthMeter = document.querySelector('.strength-meter-fill');
  const strengthText = document.querySelector('.strength-text span');
  
  if (!strengthMeter || !strengthText) return;
  
  if (!password) {
    strengthMeter.setAttribute('data-strength', '0');
    strengthText.textContent = 'Too weak';
    return;
  }
  
  const score = calculatePasswordStrength(password);
  
  // Update strength meter
  strengthMeter.setAttribute('data-strength', score.toString());
  
  // Update strength text
  switch (score) {
    case 0:
      strengthText.textContent = 'Too weak';
      break;
    case 1:
      strengthText.textContent = 'Weak';
      break;
    case 2:
      strengthText.textContent = 'Medium';
      break;
    case 3:
      strengthText.textContent = 'Strong';
      break;
    case 4:
      strengthText.textContent = 'Very strong';
      break;
  }
}

/**
 * Authenticate user against predefined credentials
 * In a real app, this would be done server-side
 */
function authenticateUser(username, password) {
  // Demo admin accounts
  const adminAccounts = [
    { username: 'admin', password: 'admin123' },
    { username: 'admin@example.com', password: 'admin123' },
    { username: 'superadmin', password: 'super123' }
  ];
  
  return adminAccounts.some(account => 
    (account.username === username || account.username === username) && 
    account.password === password);
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
