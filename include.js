document.addEventListener("DOMContentLoaded", function () {
  const toggle = document.getElementById("nav-toggle");
  const menu = document.querySelector(".navbar");

  // Toggle menu on mobile when hamburger icon is clicked
  if (toggle && menu) {
    toggle.addEventListener("click", function (e) {
      e.stopPropagation(); // Prevent bubbling
      menu.classList.toggle("active");
    });
  }

  // Close the menu when clicking outside of the header
  document.addEventListener("click", function (e) {
    const isClickInsideHeader = e.target.closest("header");
    if (!isClickInsideHeader && menu.classList.contains("active")) {
      menu.classList.remove("active");
    }
  });

  // Optional: Close on ESC key press
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && menu.classList.contains("active")) {
      menu.classList.remove("active");
    }
  });
});
