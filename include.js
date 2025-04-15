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

  // ===== MOBILE NAVIGATION MENU =====
  // This will be called after the header is loaded
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

  // Optional: Close on ESC key press
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      const menu = document.querySelector(".navbar");
      if (menu && menu.classList.contains("active")) {
        menu.classList.remove("active");
      }
    }
  });

  // ===== CONTACT PAGE FUNCTIONALITY =====
  // Only run this code if we're on the contact page
  if (document.querySelector('.contact-forms')) {
    // ----- TABS FUNCTIONALITY -----
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        // Remove active class from all buttons and content
        tabBtns.forEach(b => b.classList.remove('active'));
        tabContents.forEach(c => c.classList.remove('active'));
        
        // Add active class to clicked button
        this.classList.add('active');
        
        // Show corresponding content
        const tabId = this.getAttribute('data-tab');
        document.getElementById(tabId).classList.add('active');
      });
    });
    
    // ----- CALENDAR FUNCTIONALITY -----
    initializeCalendar();
    
    // ----- FORM VALIDATION -----
    initializeFormValidation();
  }
  
  function initializeCalendar() {
    // Current date tracking
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    
    // Days of the week
    const daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    
    // Month names
    const monthNames = [
      'January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'
    ];
    
    // Get DOM elements
    const currentMonthElement = document.querySelector('.current-month');
    const calendarDaysElement = document.querySelector('.calendar-days');
    const prevMonthBtn = document.querySelector('.calendar-nav.prev');
    const nextMonthBtn = document.querySelector('.calendar-nav.next');
    
    // Initialize calendar if elements exist
    if (currentMonthElement && calendarDaysElement) {
      // Set initial month display
      updateCalendarMonth();
      
      // Generate the calendar
      generateCalendarDays();
      
      // Add event listeners for month navigation
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
    
    // Update the month and year display
    function updateCalendarMonth() {
      if (currentMonthElement) {
        currentMonthElement.textContent = `${monthNames[currentMonth]} ${currentYear}`;
      }
    }
    
    // Generate calendar days
    function generateCalendarDays() {
      if (!calendarDaysElement) return;
      
      // Clear existing days
      calendarDaysElement.innerHTML = '';
      
      // Get first day of month
      const firstDay = new Date(currentYear, currentMonth, 1);
      
      // Get the day of the week (0-6, where 0 is Sunday)
      // Adjust to make Monday=0, Sunday=6
      let firstDayIndex = firstDay.getDay() - 1;
      if (firstDayIndex < 0) firstDayIndex = 6; // If Sunday, set to 6
      
      // Get number of days in month
      const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
      
      // Create empty placeholders for days before the 1st
      for (let i = 0; i < firstDayIndex; i++) {
        const emptyDay = document.createElement('span');
        emptyDay.className = 'calendar-day empty';
        calendarDaysElement.appendChild(emptyDay);
      }
      
      // Create the actual day buttons
      for (let day = 1; day <= daysInMonth; day++) {
        const dayBtn = document.createElement('button');
        dayBtn.type = 'button';
        dayBtn.className = 'calendar-day';
        dayBtn.textContent = day;
        
        // Check if the date is today
        const currentCalendarDate = new Date(currentYear, currentMonth, day);
        const today = new Date();
        
        if (currentCalendarDate.toDateString() === today.toDateString()) {
          dayBtn.classList.add('today');
        }
        
        // Set the current day as selected by default if it's the current month
        if (day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear()) {
          dayBtn.classList.add('selected');
          updateSelectedDateDisplay(day);
        }
        
        // Add click event
        dayBtn.addEventListener('click', function() {
          // Remove selected class from all days
          document.querySelectorAll('.calendar-day').forEach(d => {
            d.classList.remove('selected');
          });
          
          // Add selected class to clicked day
          this.classList.add('selected');
          
          // Update selected date display
          updateSelectedDateDisplay(day);
        });
        
        calendarDaysElement.appendChild(dayBtn);
      }
      
      // Update selected date if needed
      const selectedDay = document.querySelector('.calendar-day.selected');
      if (selectedDay) {
        updateSelectedDateDisplay(parseInt(selectedDay.textContent));
      }
    }
    
    // Update the display of the selected date
    function updateSelectedDateDisplay(day) {
      // Create a hidden input field to store the selected date if it doesn't exist
      let selectedDateInput = document.getElementById('selected-date');
      if (!selectedDateInput) {
        selectedDateInput = document.createElement('input');
        selectedDateInput.type = 'hidden';
        selectedDateInput.id = 'selected-date';
        selectedDateInput.name = 'selected-date';
        const formSection = document.querySelector('.form-section');
        if (formSection) {
          formSection.appendChild(selectedDateInput);
        }
      }
      
      // Format the date (YYYY-MM-DD)
      const formattedDate = `${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
      selectedDateInput.value = formattedDate;
      
      // You can also update a visible text display if needed
      const dateDisplayElement = document.querySelector('.selected-date-display');
      if (dateDisplayElement) {
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const readableDate = new Date(currentYear, currentMonth, day).toLocaleDateString('en-US', dateOptions);
        dateDisplayElement.textContent = readableDate;
      }
    }
  }
  
  function initializeFormValidation() {
    // ----- TIME SLOT SELECTION -----
    const timeSlots = document.querySelectorAll('.time-slot input');
    
    timeSlots.forEach(slot => {
      slot.addEventListener('change', function() {
        if (this.checked) {
          // Update a hidden input or display if needed
          const timeSlotValue = this.value;
          
          // Create or update a hidden input field
          let selectedTimeInput = document.getElementById('selected-time');
          if (!selectedTimeInput) {
            selectedTimeInput = document.createElement('input');
            selectedTimeInput.type = 'hidden';
            selectedTimeInput.id = 'selected-time';
            selectedTimeInput.name = 'selected-time';
            const timeSlotContainer = document.querySelector('.time-slots');
            if (timeSlotContainer) {
              timeSlotContainer.appendChild(selectedTimeInput);
            }
          }
          selectedTimeInput.value = timeSlotValue;
        }
      });
    });
    
    // ----- FORM VALIDATION -----
    const forms = document.querySelectorAll('.contact-form');
    
    forms.forEach(form => {
      form.addEventListener('submit', function(event) {
        // For demo purposes, prevent actual form submission
        // Remove this in production
        event.preventDefault();
        
        // Check form validity
        if (form.checkValidity()) {
          // Show success message
          const formName = form.closest('.tab-content').id;
          
          // Create success message
          const successMessage = document.createElement('div');
          successMessage.className = 'form-success-message';
          successMessage.innerHTML = `
            <div class="success-icon">
              <i class="fas fa-check-circle"></i>
            </div>
            <h3>Thank you!</h3>
            <p>Your ${formName.replace('-', ' ')} has been submitted successfully. We'll get back to you shortly.</p>
          `;
          
          // Replace form with success message
          form.style.display = 'none';
          form.insertAdjacentElement('afterend', successMessage);
          
          // Optional: Scroll to success message
          successMessage.scrollIntoView({ behavior: 'smooth' });
          
          // Optional: Reset form for future use (hidden)
          form.reset();
          
          // In production, you would submit the form data to your server here
          // fetch('/submit-form', {
          //   method: 'POST',
          //   body: new FormData(form)
          // })
          // .then(response => response.json())
          // .then(data => {
          //   console.log('Success:', data);
          // })
          // .catch(error => {
          //   console.error('Error:', error);
          // });
        } else {
          // The form has validation errors
          // The browser will handle displaying these errors
        }
      });
    });
  }
});
