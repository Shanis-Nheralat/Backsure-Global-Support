// include.js
window.addEventListener("DOMContentLoaded", () => {
  const loadHTML = (id, file) => {
    fetch(file)
      .then(response => response.text())
      .then(data => {
        document.getElementById(id).innerHTML = data;
      });
  };

  loadHTML("header-placeholder", "header.html");
  loadHTML("footer-placeholder", "footer.html");
});
