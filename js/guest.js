$(document).ready(function () {
  // Guest clicks cart icon → redirect
  $("#cartIcon").on("click", function () {
    alert("Please log in to access your cart.");
    window.location.href = "/pages/auth/login.php";
  });

  // Guest clicks checkout → redirect
  $(".checkout-btn").on("click", function () {
    alert("Please log in to checkout.");
    window.location.href = "/pages/auth/login.php";
  });
  $(document).on("click", "#profileIcon", function (e) {
    e.stopPropagation();

    const $dropdown = $("#profileDropdown");
    const $arrow = $(this).find(".dropdown-arrow");

    $dropdown.toggleClass("active");
    if ($arrow.length) {
      $arrow.toggleClass("rotated");
    }
  });
});
