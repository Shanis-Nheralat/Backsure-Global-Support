/**
 * BackSure Global Support - Contact Page Script
 * This script handles all functionality for the contact page including:
 * - Tab switching between form types
 * - Time zone conversion (UAE to India)
 * - Form validation and submission
 */

document.addEventListener('DOMContentLoaded', function() {
  // ====== Tab Switching Functionality ======
  const tabButtons = document.querySelectorAll('.tab-btn');
  const tabContents = document.querySelectorAll('.tab-content');
  
  tabButtons.forEach(button => {
    button.addEventListener('click', () => {
      // Remove active class from all buttons and contents
      tabButtons.forEach(btn => btn.classList.remove('active'));
      tabContents.forEach(content => content.classList.remove('active'));
      
      // Add active class to clicked button and corresponding content
      button.classList.add('active');
      const tabId = button.getAttribute('data-tab');
      document.getElementById(tabId).classList.add('active');
    });
  });
  
  // ====== UAE to Indian Time Conversion ======
  const timeSlotInputs = document.querySelectorAll('input[name="time"]');
  const indianTimeSpan = document.getElementById('indian-time');
  
  function updateIndianTime() {
    const selectedTimeInput = document.querySelector('input[name="time"]:checked');
    
    if (selectedTimeInput && indianTimeSpan) {
      // Get the UAE time value
      const uaeHours = parseInt(selectedTimeInput.value);
      
      // Convert to Indian time (UAE + 1:30 hours)
      let indianHours = uaeHours + 1;
      let indianMinutes = 30;
      
      // Format the time strings
      const formattedStartHours = indianHours.toString().padStart(2, '0');
      const formattedStartMinutes = indianMinutes.toString().padStart(2, '0');
      
      // For end time (1 hour later)
      let endIndianHours = indianHours + 1;
      let endIndianMinutes = indianMinutes;
      
      const formattedEndHours = endIndianHours.toString().padStart(2, '0');
      const formattedEndMinutes = endIndianMinutes.toString().padStart(2, '0');
      
      // Display the Indian time range
      indianTimeSpan.textContent = `${formattedStartHours}:${formattedStartMinutes} - ${formattedEndHours}:${formattedEndMinutes}`;
    } else if (indianTimeSpan) {
      indianTimeSpan.textContent = '';
    }
  }
  
  // Add event listeners to all time slot inputs
  if (timeSlotInputs.length > 0) {
    timeSlotInputs.forEach(input => {
      input.addEventListener('change', updateIndianTime);
    });
    
    // Check if any time slot is already selected on page load
    const selectedTimeInput = document.querySelector('input[name="time"]:checked');
    if (selectedTimeInput) {
      updateIndianTime();
    }
  }
  
  // ====== Form Submission Handling ======
  const generalForm = document.getElementById('general-inquiry-form');
  const meetingForm = document.getElementById('meeting-form');
  const serviceForm = document.getElementById('service-intake-form');
  
  // Add hidden Indian time input when meeting form is submitted
  if (meetingForm) {
    meetingForm.addEventListener('submit', function(e) {
      if (indianTimeSpan && indianTimeSpan.textContent) {
        const indianTimeInput = document.createElement('input');
        indianTimeInput.type = 'hidden';
        indianTimeInput.name = 'indian_time';
        indianTimeInput.value = indianTimeSpan.textContent;
        this.appendChild(indianTimeInput);
      }
    });
  }
  
  // Handle form submission for all forms
  function handleFormSubmit(form, formType) {
    form.addEventListener('submit', function(e) {
      // For standard HTML form submission, uncomment this line to prevent redirect
      // e.preventDefault();
      
      // For AJAX submission using fetch API
      if (form.getAttribute('data-ajax') === 'true') {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        // Convert FormData to object
        const data = {};
        formData.forEach((value, key) => {
          data[key] = value;
        });
        
        // Add submission timestamp
        data.submission_date = new Date().toISOString();
        
        // Send the form data to server
        fetch('submit-form.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(result => {
          showSuccessMessage(form, formType);
        })
        .catch(error => {
          console.error('Error:', error);
          alert('There was an error submitting your form. Please try again later.');
        });
      } else {
        // Display success message for regular form submission
        // Comment this out if you want the form to actually submit to server
        e.preventDefault();
        showSuccessMessage(form, formType);
      }
    });
  }
  
  // Helper function to display success message
  function showSuccessMessage(form, formType) {
    form.style.display = 'none';
    
    const successMessage = document.createElement('div');
    successMessage.className = 'form-success-message';
    successMessage.innerHTML = `
      <div class="success-icon">
        <i class="fas fa-check-circle"></i>
      </div>
      <h3>Thank you!</h3>
      <p>Your ${formType} has been submitted successfully. We will contact you shortly.</p>
    `;
    
    form.insertAdjacentElement('afterend', successMessage);
    form.reset();
    
    // Reset any selected time slots
    const timeInputs = form.querySelectorAll('input[type="radio"]');
    timeInputs.forEach(input => {
      input.checked = false;
    });
    
    if (indianTimeSpan) {
      indianTimeSpan.textContent = '';
    }
  }
  
  // Initialize form submission handlers
  if (generalForm) {
    handleFormSubmit(generalForm, 'general inquiry');
  }
  
  if (meetingForm) {
    handleFormSubmit(meetingForm, 'meeting request');
  }
  
  if (serviceForm) {
    handleFormSubmit(serviceForm, 'service request');
  }
  
  // ====== Form Field Validation ======
  const formInputs = document.querySelectorAll('.contact-form input, .contact-form textarea, .contact-form select');
  
  formInputs.forEach(input => {
    if (input.hasAttribute('required')) {
      input.addEventListener('invalid', function(e) {
        e.preventDefault();
        this.classList.add('invalid');
        
        const errorMsg = document.createElement('div');
        errorMsg.className = 'error-message';
        errorMsg.textContent = this.validationMessage;
        
        // Remove any existing error message
        const existingError = this.parentNode.querySelector('.error-message');
        if (existingError) {
          existingError.remove();
        }
        
        this.parentNode.appendChild(errorMsg);
      });
      
      input.addEventListener('input', function() {
        this.classList.remove('invalid');
        const errorMsg = this.parentNode.querySelector('.error-message');
        if (errorMsg) {
          errorMsg.remove();
        }
      });
    }
  });
  
  // ====== Date Input Validation ======
  const dateInput = document.getElementById('meeting-date');
  if (dateInput) {
    // Set min date to today
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    
    const todayFormatted = `${yyyy}-${mm}-${dd}`;
    dateInput.min = todayFormatted;
    
    // Set max date to 3 months from now
    const maxDate = new Date();
    maxDate.setMonth(maxDate.getMonth() + 3);
    
    const maxYyyy = maxDate.getFullYear();
    const maxMm = String(maxDate.getMonth() + 1).padStart(2, '0');
    const maxDd = String(maxDate.getDate()).padStart(2, '0');
    
    const maxDateFormatted = `${maxYyyy}-${maxMm}-${maxDd}`;
    dateInput.max = maxDateFormatted;
  }
});