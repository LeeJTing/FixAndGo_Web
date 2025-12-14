$(document).ready(function () {
  $(document).on("click", "#profileIcon", function (e) {
    e.stopPropagation();

    const $dropdown = $("#profileDropdown");
    const $arrow = $(this).find(".dropdown-arrow");

    $dropdown.toggleClass("active");
    if ($arrow.length) {
      $arrow.toggleClass("rotated");
    }
  });
  // ================= CART SIDEBAR =================
  const $cartIcon = $("#cartIcon");
  if ($cartIcon.length) {
    $cartIcon.on("click", function (e) {
      e.preventDefault();
      e.stopPropagation(); // 🔥 IMPORTANT

      $("#cartSidebar").addClass("open");
      $("#cartOverlay").addClass("active");
      $("body").css("overflow", "hidden");
    });
  }

  // Close cart sidebar
  if ($(".close-cart, #cartOverlay").length) {
    $(".close-cart, #cartOverlay").on("click", function () {
      $("#cartSidebar").removeClass("open");
      $("#cartOverlay").removeClass("active");
      $("body").css("overflow", "auto");
    });
  }

  // Close cart with ESC
  $(document).on("keydown", function (e) {
    if (e.key === "Escape") {
      $("#cartSidebar").removeClass("open");
      $("#cartOverlay").removeClass("active");
      $("body").css("overflow", "auto");
    }
  });
});
