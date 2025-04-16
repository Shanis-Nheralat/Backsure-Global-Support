/**
 * BSG Support - Global Include Script
 * This script loads common header and footer components across all pages
 * and handles additional site functionality.
 */

document.addEventListener("DOMContentLoaded", function () {

  // ====== Dynamic Loading of Header and Footer ======
  // Include the repository name in path for GitHub Pages
  const repoPath = '/Backsure-Global-Support';
  
  // Determine if we're on GitHub Pages or a different hosting environment
  const isGitHubPages = window.location.hostname.includes('github.io') || 
                        window.location.hostname === 'shanis-nheralat.github.io';
                        
  // Determine the base path to handle different directory levels
  const currentPath = window.location.pathname;
  const pathSegments = currentPath.split('/').filter(Boolean);
  
  // Calculate path to root based on environment
  let basePath = '';
  
  if (isGitHubPages) {
    // For GitHub Pages, use the repo path directly
    basePath = repoPath + '/';
  } else {
    // For other hosting, calculate relative path
    if (pathSegments.length > 0) {
      // Check if the last segment is an HTML file (like about.html)
      const hasHTMLFile = pathSegments[pathSegments.length - 1].includes('.html');
      const depth = hasHTMLFile ? pathSegments.length - 1 : pathSegments.length;
      
      if (depth > 0) {
          basePath = Array(depth).fill('..').join('/') + '/';
      }
    }
  }

  // Load components with environment-aware paths
  if (document.getElementById('header-placeholder')) {
    const headerPath = isGitHubPages ? `${repoPath}/header.html` : 'header.html';
    loadComponent("header-placeholder", headerPath, setActiveMenuItem);
  }
  
  if (document.getElementById('footer-placeholder')) {
    const footerPath = isGitHubPages ? `${repoPath}/footer.html` : 'footer.html';
    loadComponent("footer-placeholder", footerPath);
  }

  function loadComponent(elementId, url, callback) {
    const element = document.getElementById(elementId);
    if (element) {
      fetch(url)
        .then(response => {
          if (!response.ok) {
            console.error(`Failed to load ${url}: ${response.status}`);
            throw new Error(`Failed to load ${url}: ${response.status}`);
          }
          return response.text();
        })
        .then(data => {
          element.innerHTML = data;
          
          // Execute any scripts in the loaded content
          const scripts = element.getElementsByTagName('script');
          for (let i = 0; i < scripts.length; i++) {
            eval(scripts[i].innerText);
          }
          
          // Run callback if provided (e.g., for active menu item)
          if (callback) callback();
          
          // Dispatch event to signal component is loaded
          document.dispatchEvent(new CustomEvent(`${elementId}-loaded`));
        })
        .catch(error => {
          console.error("Error loading component:", error);
          // Try fallback with direct GitHub raw content if initial fetch fails
          if (isGitHubPages) {
            const rawUrl = `https://raw.githubusercontent.com/shanis-nheralat/Backsure-Global-Support/main/${url.replace(repoPath + '/', '')}`;
            console.log("Trying fallback URL:", rawUrl);
            
            fetch(rawUrl)
              .then(response => {
                if (!response.ok) throw new Error(`Fallback also failed: ${response.status}`);
                return response.text();
              })
              .then(data => {
                element.innerHTML = data;
                if (callback) callback();
              })
              .catch(fallbackError => {
                console.error("Fallback also failed:", fallbackError);
                element.innerHTML = `<p>Error loading component. Please refresh the page.</p>`;
              });
          } else {
            element.innerHTML = `<div class="component-error">Error loading ${url.split('/').pop()}. Please check your connection.</div>`;
          }
        });
    }
  }

  // ====== Set Active Menu Item Based on URL ======
  function setActiveMenuItem() {
    const currentPath = window.location.pathname;
    let filename = currentPath.substring(currentPath.lastIndexOf('/') + 1);
    
    // Handle case when URL ends with / (directory index)
    if (filename === '') filename = 'index.html';
    
    console.log("Current filename:", filename);
    
    const navLinks = document.querySelectorAll('.navbar a');
    navLinks.forEach(link => link.classList.remove('active'));

    if (filename === '' || filename === 'index.html') {
      const homeLink = document.getElementById('nav-home');
      if (homeLink) homeLink.classList.add('active');
    } else if (filename.includes('service-model') || filename === 'services.html') {
      const servicesLink = document.getElementById('nav-services');
      if (servicesLink) servicesLink.classList.add('active');
    } else if (filename.includes('solution') || filename === 'solutions.html') {
      const solutionsLink = document.getElementById('nav-solutions');
      if (solutionsLink) solutionsLink.classList.add('active');
    } else if (filename.includes('about') || filename.includes('team') || 
               filename.includes('careers') || filename.includes('testimonials')) {
      const aboutLink = document.getElementById('nav-about');
      if (aboutLink) aboutLink.classList.add('active');
    } else if (filename.includes('contact')) {
      const contactLink = document.getElementById('nav-contact');
      if (contactLink) contactLink.classList.add('active');
    }

    if (filename.includes('-')) {
      const specificLinks = document.querySelectorAll(`.navbar a[href="${filename}"]`);
      specificLinks.forEach(link => link.classList.add('active'));
    }
  }

  // ====== Mobile Menu Toggle ======
  // This is delegated to after header loads
  document.addEventListener('click', function(e) {
    if (e.target.matches('#nav-toggle') || e.target.closest('#nav-toggle')) {
      e.stopPropagation();
      const menu = document.querySelector(".navbar");
      if (menu) menu.classList.toggle("active");
    }
    
    const isClickInsideHeader = e.target.closest("header");
    const menu = document.querySelector(".navbar");
    if (!isClickInsideHeader && menu && menu.classList.contains("active")) {
      menu.classList.remove("active");
    }
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      const menu = document.querySelector(".navbar");
      if (menu && menu.classList.contains("active")) {
        menu.classList.remove("active");
      }
    }
  });

  // Add animations for the landing page
  initializeAnimations();
  
  function initializeAnimations() {
    // Handle scroll animations if needed
    if (document.querySelector('.value-card, .service-card, .solution-card')) {
      const elementsToAnimate = document.querySelectorAll('.value-card, .service-card, .solution-card, .stat-item');
      
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate-in');
          }
        });
      }, { threshold: 0.1 });
      
      elementsToAnimate.forEach(element => {
        element.classList.add('animate-on-scroll');
        observer.observe(element);
      });
    }
    
    // Handle testimonials auto-rotation if needed
    const testimonials = document.querySelectorAll('.testimonial-card');
    if (testimonials.length > 1) {
      let currentTestimonial = 0;
      
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
});
