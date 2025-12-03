// Mobile menu toggle
$("#menu-toggle").on("click", function () {
  $(".mobile-menu").toggleClass("open");
});

// Smooth scroll or general animations
$('a[href^="#"]').on("click", function (e) {
  e.preventDefault();
  $("html, body").animate(
    { scrollTop: $($(this).attr("href")).offset().top },
    500
  );
});

document
  .getElementById("mobileMenuToggle")
  .addEventListener("click", function () {
    const menu = document.getElementById("mobileDropdown");
    menu.classList.toggle("open");

    // Toggle icon: bars → x
    this.querySelector("i").classList.toggle("fa-bars");
    this.querySelector("i").classList.toggle("fa-xmark");
  });

$(document).on("click", "#profileIcon", function (e) {
  e.stopPropagation();
  const $dropdown = $("#profileDropdown");
  const $arrow = $(this).find(".dropdown-arrow");

  $dropdown.toggleClass("active");
  $arrow.toggleClass("rotated");
});

// Close when clicking outside
$(document).on("click", function (e) {
  if (!$(e.target).closest(".profile-wrapper").length) {
    $("#profileDropdown").removeClass("active");
    $(".dropdown-arrow").removeClass("rotated");
  }
});
