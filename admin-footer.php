<!-- Page Content Ends Here -->
            </main>
        </div>
    </div>
    
    <script>
        // Toggle sidebar
        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('sidebar-collapsed');
            document.querySelector('.content-wrapper').classList.toggle('content-wrapper-expanded');
        });
        
        // Admin dropdown menu
        document.querySelector('.admin-dropdown-toggle').addEventListener('click', function(e) {
            e.stopPropagation();
            document.querySelector('.admin-dropdown-menu').classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            document.querySelector('.admin-dropdown-menu').classList.remove('show');
        });
        
        // Prevent closing when clicking inside dropdown
        document.querySelector('.admin-dropdown-menu').addEventListener('click', function(e) {
            e.stopPropagation();
        });
    </script>
</body>
</html>