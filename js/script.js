$(document).ready(function () {
  // Smooth scroll
  $('a[href^="#"]').on("click", function (e) {
    e.preventDefault();
    $("html, body").animate(
      { scrollTop: $($(this).attr("href")).offset().top },
      500
    );
  });

  // Mobile menu (safe)
  const mobileBtn = document.getElementById("mobileMenuToggle");
  if (mobileBtn) {
    mobileBtn.addEventListener("click", function () {
      const menu = document.getElementById("mobileDropdown");
      menu.classList.toggle("open");

      this.querySelector("i").classList.toggle("fa-bars");
      this.querySelector("i").classList.toggle("fa-xmark");
    });
  }
});
