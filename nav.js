/**
 * Mobile Navigation Functionality
 * Handles the mobile menu toggle and dropdown behavior
 */

document.addEventListener('DOMContentLoaded', function() {
  // Create menu overlay
  const overlay = document.createElement('div');
  overlay.className = 'menu-overlay';
  document.body.appendChild(overlay);
  
  // Mobile menu toggle
  const navToggle = document.getElementById('nav-toggle');
  const mainNav = document.querySelector('.main-nav');
  
  if (navToggle && mainNav) {
    navToggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      mainNav.classList.toggle('active');
      overlay.classList.toggle('active');
      document.body.classList.toggle('menu-open');
    });
    
    // Close menu when clicking overlay
    overlay.addEventListener('click', function() {
      mainNav.classList.remove('active');
      overlay.classList.remove('active');
      document.body.classList.remove('menu-open');
    });
  }
  
  // Handle dropdown menus on mobile
  const dropdowns = document.querySelectorAll('.dropdown');
  
  dropdowns.forEach(dropdown => {
    const toggle = dropdown.querySelector('a');
    
    toggle.addEventListener('click', function(e) {
      // Only handle dropdown on mobile view
      if (window.innerWidth <= 768) {
        e.preventDefault();
        e.stopPropagation();
        
        // Close other dropdowns
        dropdowns.forEach(item => {
          if (item !== dropdown && item.classList.contains('active')) {
            item.classList.remove('active');
          }
        });
        
        // Toggle current dropdown
        dropdown.classList.toggle('active');
      }
    });
  });
  
  // Close mobile menu on window resize (if switching to desktop)
  window.addEventListener('resize', function() {
    if (window.innerWidth > 768 && mainNav.classList.contains('active')) {
      mainNav.classList.remove('active');
      overlay.classList.remove('active');
      document.body.classList.remove('menu-open');
      
      // Close all dropdowns
      dropdowns.forEach(dropdown => {
        dropdown.classList.remove('active');
      });
    }
  });
  
  // Close mobile menu when clicking outside
  document.addEventListener('click', function(e) {
    if (window.innerWidth <= 768 && 
        mainNav.classList.contains('active') && 
        !e.target.closest('.main-nav') && 
        !e.target.closest('#nav-toggle')) {
      mainNav.classList.remove('active');
      overlay.classList.remove('active');
      document.body.classList.remove('menu-open');
    }
  });
});