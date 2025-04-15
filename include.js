<script>
  document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("nav-toggle");
    const menu = document.querySelector(".navbar");

    // Toggle menu on click
    if (toggle && menu) {
      toggle.addEventListener("click", function (e) {
        e.stopPropagation(); // Prevent click from bubbling up
        menu.classList.toggle("active");
      });
    }

    // Close menu when clicking outside of header/nav
    document.addEventListener("click", function (e) {
      if (!e.target.closest("header")) {
        menu.classList.remove("active");
      }
    });

    // Optional: Close menu on escape key
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        menu.classList.remove("active");
      }
    });
  });
</script>
