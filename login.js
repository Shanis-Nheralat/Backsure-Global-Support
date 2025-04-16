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
            
            // Check credentials and redirect accordingly
            if (username.toLowerCase() === 'admin' && password === 'adminPassword123') {
                // Admin login
                localStorage.setItem('adminLoggedIn', 'true');
                window.location.href = 'admin-dashboard.html';
            } else if (validateClientCredentials(username, password)) {
                // Client login
                localStorage.setItem('clientLoggedIn', 'true');
                localStorage.setItem('clientUsername', username);
                window.location.href = 'client-dashboard.html';
            } else {
                // Invalid credentials
                errorMessage.textContent = 'Invalid username or password';
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }
    
    // In a real implementation, this would validate against a database
    function validateClientCredentials(username, password) {
        // Demo client accounts
        const clients = [
            { username: 'client1', password: 'clientpass1' },
            { username: 'client2', password: 'clientpass2' }
        ];
        
        return clients.some(client => 
            client.username === username && client.password === password);
    }
    
    // Check if user is already logged in
    const isAdminLoggedIn = localStorage.getItem('adminLoggedIn') === 'true';
    const isClientLoggedIn = localStorage.getItem('clientLoggedIn') === 'true';
    
    if (isAdminLoggedIn) {
        window.location.href = 'admin-dashboard.html';
    } else if (isClientLoggedIn) {
        window.location.href = 'client-dashboard.html';
    }
});