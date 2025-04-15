document.addEventListener("DOMContentLoaded", function () {
  // Load header and footer components
  loadComponent("header-placeholder", "header.html", function() {
    // Set active menu item after header loads
    setActiveMenuItem();
  });
  loadComponent("footer-placeholder", "footer.html");

  // Function to load HTML components
  function loadComponent(elementId, url, callback) {
    const element = document.getElementById(elementId);
    if (element) {
      fetch(url)
        .then(response => {
          if (!response.ok) {
            throw new Error(`Failed to load ${url}: ${response.status} ${response.statusText}`);
          }
          return response.text();
        })
        .then(data => {
          element.innerHTML = data;
          if (typeof callback === 'function') {
            callback();
          }
        })
        .catch(error => {
          console.error("Error loading component:", error);
          element.innerHTML = `<p>Error loading component. Please refresh the page.</p>`;
        });
    }
  }

  // Set active menu item based on current page
  function setActiveMenuItem() {
    const currentPath = window.location.pathname;
    const filename = currentPath.substring(currentPath.lastIndexOf('/') + 1);
    
    // Remove any existing active classes
    const navLinks = document.querySelectorAll('.navbar a');
    navLinks.forEach(link => link.classList.remove('active'));
    
    // Set active class based on current page
    if (filename === '' || filename === 'index.html') {
      document.getElementById('nav-home')?.classList.add('active');
    } else if (filename.includes('service-model') || filename === 'services.html') {
      document.getElementById('nav-services')?.classList.add('active');
    } else if (filename.includes('solution') || filename === 'solutions.html') {
      document.getElementById('nav-solutions')?.classList.add('active');
    } else if (filename.includes('about') || filename.includes('team') || 
              filename.includes('careers') || filename.includes('testimonials')) {
      document.getElementById('nav-about')?.classList.add('active');
    } else if (filename.includes('contact')) {
      document.getElementById('nav-contact')?.classList.add('active');
    }
    
    // Set active class for dropdown menu items if needed
    if (filename.includes('-')) {
      const specificLinks = document.querySelectorAll(`.navbar a[href="${filename}"]`);
      specificLinks.forEach(link => link.classList.add('active'));
    }
  }

  // Mobile navigation menu toggle
  document.addEventListener('click', function (e) {
    // Toggle menu on mobile when hamburger icon is clicked
    if (e.target.matches('#nav-toggle') || e.target.closest('#nav-toggle')) {
      e.preventDefault();
      e.stopPropagation();
      const menu = document.querySelector(".navbar");
      if (menu) {
        menu.classList.toggle("active");
      }
    }
    
    // Close the menu when clicking outside of the header
    const isClickInsideHeader = e.target.closest("header");
    const menu = document.querySelector(".navbar");
    if (!isClickInsideHeader && menu && menu.classList.contains("active")) {
      menu.classList.remove("active");
    }
  });

  // Close on ESC key press
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      const menu = document.querySelector(".navbar");
      if (menu && menu.classList.contains("active")) {
        menu.classList.remove("active");
      }
    }
  });

  // Handle smooth scrolling for anchor links
  document.addEventListener('click', function(e) {
    const link = e.target.closest('a[href^="#"]');
    if (link) {
      e.preventDefault();
      const targetId = link.getAttribute('href').substring(1);
      const targetElement = document.getElementById(targetId);
      
      if (targetElement) {
        window.scrollTo({
          top: targetElement.offsetTop - 100,
          behavior: 'smooth'
        });
      }
    }
  });

  // Add scroll animation for elements
  const animateOnScroll = function() {
    const elements = document.querySelectorAll('.animate-on-scroll');
    
    elements.forEach(element => {
      const elementPosition = element.getBoundingClientRect().top;
      const windowHeight = window.innerHeight;
      
      if (elementPosition < windowHeight - 100) {
        element.classList.add('animate-in');
      }
    });
  };

  // Run animation on page load and scroll
  window.addEventListener('scroll', animateOnScroll);
  animateOnScroll(); // Run once on page load

  // Sticky header on scroll
  const header = document.querySelector('header');
  if (header) {
    window.addEventListener('scroll', function() {
      if (window.scrollY > 100) {
        header.classList.add('sticky');
      } else {
        header.classList.remove('sticky');
      }
    });
  }

  // Initialize any sliders or carousels
  initializeCarousels();

  function initializeCarousels() {
    // Simple testimonial slider/rotator
    const testimonials = document.querySelectorAll('.testimonial-card');
    let currentTestimonial = 0;
    
    // Only setup if there are multiple testimonials
    if (testimonials.length > 1) {
      // Hide all except the first one
      testimonials.forEach((testimonial, index) => {
        if (index !== 0) {
          testimonial.style.display = 'none';
        }
      });
      
      // Auto-rotate testimonials every 5 seconds
      setInterval(() => {
        testimonials[currentTestimonial].style.display = 'none';
        currentTestimonial = (currentTestimonial + 1) % testimonials.length;
        testimonials[currentTestimonial].style.display = 'block';
      }, 5000);
    }
  }

  // Contact form handling if needed
  setupContactForms();

  function setupContactForms() {
    const forms = document.querySelectorAll('.contact-form');
    
    forms.forEach(form => {
      form.addEventListener('submit', function(event) {
        // For demo purposes - prevent actual submission
        event.preventDefault();
        
        // Basic form validation
        if (form.checkValidity()) {
          // Show success message
          const formName = form.closest('.tab-content')?.id || 'form';
          
          // Create success message
          const successMessage = document.createElement('div');
          successMessage.className = 'form-success-message';
          successMessage.innerHTML = `
            <div class="success-icon">
              <i class="fas fa-check-circle"></i>
            </div>
            <h3>Thank you!</h3>
            <p>Your message has been submitted successfully. We'll get back to you shortly.</p>
          `;
          
          // Replace form with success message
          form.style.display = 'none';
          form.insertAdjacentElement('afterend', successMessage);
          
          // Optional: Reset form for future use
          form.reset();
        }
      });
    });
  }
});
.animate-on-scroll {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.6s, transform 0.6s;
}

.animate-in {
  opacity: 1;
  transform: translateY(0);
}
header.sticky {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  animation: slideDown 0.3s forwards;
}

@keyframes slideDown {
  from {
    transform: translateY(-100%);
  }
  to {
    transform: translateY(0);
  }
}
