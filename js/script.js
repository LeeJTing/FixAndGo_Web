$(document).ready(function () {
  // Smooth scroll
  $('a[href^="#"]').on("click", function (e) {
    e.preventDefault();
    $("html, body").animate(
      { scrollTop: $($(this).attr("href")).offset().top },
      500
    );
  });

  // Add to cart
  $(document).on("click", ".add-to-cart", function (e) {
    e.preventDefault();

    const $btn = $(this);
    const productId = parseInt($btn.data("id"), 10);
    if (!productId) {
      showCartToast("Invalid product.", "error");
      return;
    }

    // If product detail page has a quantity input, respect it
    const qtyEl = document.getElementById("quantity");
    const quantity = qtyEl ? Math.max(1, parseInt(qtyEl.value, 10) || 1) : 1;

    const originalHtml = $btn.html();
    $btn.prop("disabled", true);
    $btn.html('<i class="fa-solid fa-spinner fa-spin"></i>');

    $.ajax({
      url: ROOT_DIR + "/AJAX/add_to_cart.php",
      type: "POST",
      dataType: "json",
      data: {
        add_to_cart: true,
        product_id: productId,
        quantity: quantity,
      },
    })
      .done(function (resp) {
        if (resp && resp.success) {
          if (typeof resp.cartCount !== "undefined") {
            $("#cartCount").text(resp.cartCount);
          }
          $btn.html('<i class="fa-solid fa-check"></i>');
          showCartToast("Added to cart.", "success");

          //refresh cart content from DB
          if ($("#cartSidebar").hasClass("open")) {
            if (
              window.FixAndGo &&
              typeof window.FixAndGo.refreshSidebarCart === "function"
            ) {
              window.FixAndGo.refreshSidebarCart();
            }
          }

          setTimeout(function () {
            $btn.html(originalHtml);
            $btn.prop("disabled", false);
          }, 900);
        } else {
          $btn.html(originalHtml);
          $btn.prop("disabled", false);
          showCartToast(
            (resp && resp.error) || "Failed to add to cart.",
            "error"
          );
        }
      })
      .fail(function () {
        $btn.html(originalHtml);
        $btn.prop("disabled", false);
        showCartToast("Network error. Please try again.", "error");
      });
  });

  function showCartToast(message, type) {
    $(".cart-toast").remove();
    const $t = $("<div />", {
      class: "cart-toast " + (type || ""),
      text: message,
    });
    $("body").append($t);
    setTimeout(function () {
      $t.addClass("show");
    }, 20);
    setTimeout(function () {
      $t.removeClass("show");
      setTimeout(function () {
        $t.remove();
      }, 250);
    }, 2200);
  }
  // Mobile menu
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
