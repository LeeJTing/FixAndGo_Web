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

  // Profile dropdown toggle
  $("#profileIcon").on("click", function () {
    $("#profileDropdown").toggleClass("open");
  });

  // Close dropdown when clicking away
  $(document).on("click", function (e) {
    if (!$(e.target).closest("#profileIcon, #profileDropdown").length) {
      $("#profileDropdown").removeClass("open");
    }
  });
});
