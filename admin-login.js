document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const errorMessage = document.getElementById('error-message');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous error messages
            errorMessage.textContent = '';
            
            // Get form values
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            // Basic validation
            if (!username || !password) {
                errorMessage.textContent = 'Please enter both username and password';
                return;
            }
            
            // Show loading state
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
            submitBtn.disabled = true;
            
            // For demo purposes, using a simple direct check
            // In production, this should be a secure server-side authentication
            if (username === 'admin' && password === 'adminPassword123') {
                // Store authentication state
                localStorage.setItem('adminLoggedIn', 'true');
                
                // Redirect to admin dashboard
                window.location.href = 'admin-dashboard.html';
            } else {
                // Show error message
                errorMessage.textContent = 'Invalid username or password';
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
            
            // Note: In a real implementation, you would use something like this:
            /*
            fetch('/api/admin/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Invalid credentials');
                }
                return response.json();
            })
            .then(data => {
                // Store token in localStorage
                localStorage.setItem('authToken', data.token);
                
                // Redirect to admin dashboard
                window.location.href = 'admin-dashboard.html';
            })
            .catch(error => {
                errorMessage.textContent = error.message || 'Login failed. Please try again.';
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
            */
        });
    }
    
    // Check if user is already logged in
    const isLoggedIn = localStorage.getItem('adminLoggedIn') === 'true';
    if (isLoggedIn && window.location.pathname.includes('admin-login.html')) {
        // Redirect to dashboard if already logged in
        window.location.href = 'admin-dashboard.html';
    }
});