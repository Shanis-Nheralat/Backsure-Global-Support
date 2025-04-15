document.addEventListener("DOMContentLoaded", function () {

  // ====== Dynamic Loading of Header and Footer ======
  // Include the repository name in path for GitHub Pages
  const repoPath = '/Backsure-Global-Support';
  loadComponent("header-placeholder", `${repoPath}/header.html`, setActiveMenuItem);
  loadComponent("footer-placeholder", `${repoPath}/footer.html`);

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
          if (callback) callback();
        })
        .catch(error => {
          console.error("Error loading component:", error);
          // Try fallback with direct GitHub raw content if initial fetch fails
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

  // ====== Contact Page Functionality ======
  if (document.querySelector('.contact-forms')) {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        tabBtns.forEach(b => b.classList.remove('active'));
        tabContents.forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        
        const tabId = this.getAttribute('data-tab');
        const tabContent = document.getElementById(tabId);
        if (tabContent) tabContent.classList.add('active');
      });
    });

    // Calendar functionality
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
    const currentMonthElement = document.querySelector('.current-month');
    const calendarDaysElement = document.querySelector('.calendar-days');
    const prevMonthBtn = document.querySelector('.calendar-nav.prev');
    const nextMonthBtn = document.querySelector('.calendar-nav.next');

    if (currentMonthElement && calendarDaysElement) {
      function updateCalendarMonth() {
        currentMonthElement.textContent = `${monthNames[currentMonth]} ${currentYear}`;
      }

      function generateCalendarDays() {
        calendarDaysElement.innerHTML = '';
        const firstDay = new Date(currentYear, currentMonth, 1).getDay() - 1;
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const adjustedFirstDay = firstDay < 0 ? 6 : firstDay;

        for (let i = 0; i < adjustedFirstDay; i++) {
          const emptyDay = document.createElement('span');
          emptyDay.className = 'calendar-day empty';
          calendarDaysElement.appendChild(emptyDay);
        }
        
        for (let day = 1; day <= daysInMonth; day++) {
          const dayBtn = document.createElement('button');
          dayBtn.type = 'button';
          dayBtn.className = 'calendar-day';
          dayBtn.textContent = day;
          
          // Check if this is today
          if (day === currentDate.getDate() && 
              currentMonth === currentDate.getMonth() && 
              currentYear === currentDate.getFullYear()) {
            dayBtn.classList.add('today');
          }
          
          dayBtn.addEventListener('click', function() {
            document.querySelectorAll('.calendar-day').forEach(d => {
              d.classList.remove('selected');
            });
            this.classList.add('selected');
            
            // Update hidden input if needed
            const selectedDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            let selectedDateInput = document.getElementById('selected-date');
            if (!selectedDateInput) {
              selectedDateInput = document.createElement('input');
              selectedDateInput.type = 'hidden';
              selectedDateInput.id = 'selected-date';
              selectedDateInput.name = 'selected-date';
              const formSection = document.querySelector('.form-section');
              if (formSection) formSection.appendChild(selectedDateInput);
            }
            selectedDateInput.value = selectedDate;
          });
          
          calendarDaysElement.appendChild(dayBtn);
        }
      }

      updateCalendarMonth();
      generateCalendarDays();

      if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', function() {
          currentMonth--;
          if (currentMonth < 0) {
            currentMonth = 11; 
            currentYear--;
          }
          updateCalendarMonth(); 
          generateCalendarDays();
        });
      }

      if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', function() {
          currentMonth++;
          if (currentMonth > 11) {
            currentMonth = 0; 
            currentYear++;
          }
          updateCalendarMonth(); 
          generateCalendarDays();
        });
      }
    }

    // Time-slot selection and form validation
    const timeSlots = document.querySelectorAll('.time-slot input');
    timeSlots.forEach(slot => {
      slot.addEventListener('change', function() {
        if (this.checked) {
          let selectedTimeInput = document.getElementById('selected-time');
          if (!selectedTimeInput) {
            selectedTimeInput = document.createElement('input');
            selectedTimeInput.type = 'hidden';
            selectedTimeInput.id = 'selected-time';
            selectedTimeInput.name = 'selected-time';
            const timeSlotContainer = document.querySelector('.time-slots');
            if (timeSlotContainer) timeSlotContainer.appendChild(selectedTimeInput);
          }
          selectedTimeInput.value = this.value;
        }
      });
    });

    const forms = document.querySelectorAll('.contact-form');
    forms.forEach(form => {
      form.addEventListener('submit', function(event) {
        event.preventDefault();
        if (form.checkValidity()) {
          const formName = form.closest('.tab-content')?.id || 'form';
          form.style.display = 'none';
          const successMessage = document.createElement('div');
          successMessage.className = 'form-success-message';
          successMessage.innerHTML = `
            <div class="success-icon">
              <i class="fas fa-check-circle"></i>
            </div>
            <h3>Thank you!</h3>
            <p>Your ${formName.replace('-', ' ')} has been submitted successfully. We'll get back to you shortly.</p>
          `;
          form.insertAdjacentElement('afterend', successMessage);
          form.reset();
        }
      });
    });
  }

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
