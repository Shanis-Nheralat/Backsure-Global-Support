<script>
  document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById('nav-toggle');
    const menu = document.querySelector('.navbar');

    if (toggle && menu) {
      toggle.addEventListener('click', function () {
        menu.classList.toggle('active');
      });
    }

    // Optional: Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
      if (!toggle.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.remove('active');
      }
    });
  });
</script>
