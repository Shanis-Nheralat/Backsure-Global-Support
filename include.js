document.addEventListener("DOMContentLoaded", function () {

  // ====== Dynamic Loading of Header and Footer ======
  loadComponent("header-placeholder", "header.html", setActiveMenuItem);
  loadComponent("footer-placeholder", "footer.html");

  function loadComponent(elementId, url, callback) {
    const element = document.getElementById(elementId);
    if (element) {
      fetch(url)
        .then(response => {
          if (!response.ok) throw new Error(`Failed to load ${url}: ${response.status}`);
          return response.text();
        })
        .then(data => {
          element.innerHTML = data;
          if (callback) callback();
        })
        .catch(error => {
          console.error("Error loading component:", error);
          element.innerHTML = `<p>Error loading component. Please refresh the page.</p>`;
        });
    }
  }

  // ====== Set Active Menu Item Based on URL ======
  function setActiveMenuItem() {
    const currentPath = window.location.pathname;
    const filename = currentPath.substring(currentPath.lastIndexOf('/') + 1);
    const navLinks = document.querySelectorAll('.navbar a');
    
    navLinks.forEach(link => link.classList.remove('active'));

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

    if (filename.includes('-')) {
      const specificLinks = document.querySelectorAll(`.navbar a[href="${filename}"]`);
      specificLinks.forEach(link => link.classList.add('active'));
    }
  }

  // ====== Mobile Menu Toggle (retained from uploaded include.js) ======
  const toggle = document.getElementById("nav-toggle");
  const menu = document.querySelector(".navbar");

  if (toggle && menu) {
    toggle.addEventListener("click", function (e) {
      e.stopPropagation();
      menu.classList.toggle("active");
    });
  }

  document.addEventListener("click", function (e) {
    const isClickInsideHeader = e.target.closest("header");
    if (!isClickInsideHeader && menu?.classList.contains("active")) {
      menu.classList.remove("active");
    }
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && menu?.classList.contains("active")) {
      menu.classList.remove("active");
    }
  });

  // ====== Contact Page Functionality (retained fully from uploaded include.js) ======
  if (document.querySelector('.contact-forms')) {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        tabBtns.forEach(b => b.classList.remove('active'));
        tabContents.forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        document.getElementById(this.getAttribute('data-tab')).classList.add('active');
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

    function updateCalendarMonth() {
      currentMonthElement.textContent = `${monthNames[currentMonth]} ${currentYear}`;
    }

    function generateCalendarDays() {
      calendarDaysElement.innerHTML = '';
      const firstDay = new Date(currentYear, currentMonth, 1).getDay() - 1;
      const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
      const adjustedFirstDay = firstDay < 0 ? 6 : firstDay;

      for (let i = 0; i < adjustedFirstDay; i++) {
        calendarDaysElement.appendChild(document.createElement('span')).className = 'calendar-day empty';
      }
      for (let day = 1; day <= daysInMonth; day++) {
        const dayBtn = document.createElement('button');
        dayBtn.type = 'button';
        dayBtn.className = 'calendar-day';
        dayBtn.textContent = day;
        calendarDaysElement.appendChild(dayBtn);
      }
    }

    if (currentMonthElement && calendarDaysElement) {
      updateCalendarMonth();
      generateCalendarDays();

      prevMonthBtn?.addEventListener('click', function() {
        currentMonth--;
        if (currentMonth < 0) {
          currentMonth = 11; currentYear--;
        }
        updateCalendarMonth(); generateCalendarDays();
      });

      nextMonthBtn?.addEventListener('click', function() {
        currentMonth++;
        if (currentMonth > 11) {
          currentMonth = 0; currentYear++;
        }
        updateCalendarMonth(); generateCalendarDays();
      });
    }

    // Time-slot selection and form validation (retained fully from original uploaded JS)
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
            document.querySelector('.time-slots')?.appendChild(selectedTimeInput);
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
          form.style.display = 'none';
          const successMessage = document.createElement('div');
          successMessage.className = 'form-success-message';
          successMessage.innerHTML = `<h3>Thank you!</h3><p>Your submission was successful.</p>`;
          form.insertAdjacentElement('afterend', successMessage);
          form.reset();
        }
      });
    });
  }

});
