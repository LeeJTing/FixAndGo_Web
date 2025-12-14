(function ($) {
  function recalcHeaderCart() {
    // Recalculate total only for items checked (is_check)
    // But count should show total number of items regardless of is_check
    let total = 0;
    let count = 0;
    $("#cartItems .cart-item").each(function () {
      const $it = $(this);
      const checked = $it.find(".is-check-checkbox").is(":checked");
      const qty = parseInt($it.find(".product-quantity-display").text()) || 0;
      const price = parseFloat($it.data("price")) || 0;
      
      // Count all items (for cart count badge)
      count += 1;
      
      // Total only includes checked items (for checkout total)
      if (checked) {
        total += qty * price;
      }
    });
    $("#cartTotalPrice").text("RM " + total.toFixed(2));
    $("#cartCount").text(count);
  }

  $(function () {
    // initialize count from data attribute if present
    const initial = parseInt($("#cartCount").attr("data-count")) || 0;
    $("#cartCount").text(initial);

    // Open/close handlers
    $("#cartIcon").on("click", function (e) {
      e.stopPropagation();
      $("#cartSidebar").addClass("open");
      $("#cartOverlay").show();
    });

    $(".close-cart, #cartOverlay").on("click", function () {
      $("#cartSidebar").removeClass("open");
      $("#cartOverlay").hide();
    });

    // Quantity buttons
    $(document).on("click", "#cartItems .qty-btn", function (e) {
      e.preventDefault();
      const $btn = $(this);
      
      // Prevent action if button is disabled
      if ($btn.hasClass("disabled")) {
        return false;
      }
      
      const $item = $btn.closest(".cart-item");
      const itemId = $item.data("item-id");
      let qty = parseInt($item.find(".product-quantity-display").text()) || 0;
      if ($btn.hasClass("plus")) qty++;
      if ($btn.hasClass("minus")) qty = Math.max(1, qty - 1);

      $.post(ROOT_DIR + "/AJAX/update_quantity.php", {
        update_quantity: true,
        item_id: itemId,
        quantity: qty,
      })
        .done(function (resp) {
          if (resp.success) {
            $item.find(".product-quantity-display").text(qty);
            const price = parseFloat($item.data("price")) || 0;
            $item.find(".product-total").text((price * qty).toFixed(2));
            
            // Update disabled state for minus button
            const $minusBtn = $item.find(".qty-btn.minus");
            if (qty <= 1) {
              $minusBtn.addClass("disabled");
            } else {
              $minusBtn.removeClass("disabled");
            }
            
            recalcHeaderCart();
          } else {
            alert("Error: " + (resp.error || "Failed to update quantity"));
          }
        })
        .fail(function () {
          alert("Network error updating quantity");
        });
    });

    // Remove
    $(document).on("click", "#cartItems .delete-btn", function (e) {
      e.preventDefault();
      const $btn = $(this);
      const itemId = $btn.data("item-id");
      if (!confirm("Remove this item from cart?")) return;
      $.post(ROOT_DIR + "/AJAX/remove_item.php", {
        remove_item: true,
        item_id: itemId,
      })
        .done(function (resp) {
          if (resp.success) {
            $btn.closest(".cart-item").slideUp(200, function () {
              $(this).remove();
              recalcHeaderCart();
            });
          } else {
            alert("Error: " + (resp.error || "Failed to remove item"));
          }
        })
        .fail(function () {
          alert("Network error removing item");
        });
    });

    // is_check checkbox -> update DB and sync is_take
    $(document).on("change", "#cartItems .is-check-checkbox", function () {
      const $cb = $(this);
      const itemId = $cb.data("item-id");
      const isCheck = $cb.is(":checked") ? 1 : 0;

      $.post(ROOT_DIR + "/AJAX/update_cart_check.php", {
        update_is_check: true,
        item_id: itemId,
        is_check: isCheck,
      })
        .done(function (resp) {
          if (!resp.success) {
            alert("Error updating selection: " + (resp.error || "Unknown"));
            $cb.prop("checked", !isCheck);
            return;
          }

          $.post(ROOT_DIR + "/AJAX/update_cart_take.php", {
            update_is_take: true,
            item_id: itemId,
            is_take: isCheck,
          })
            .done(function (resp2) {
              if (!resp2.success)
                console.warn("Failed to sync is_take:", resp2.error);
            })
            .fail(function () {
              console.warn("Network error syncing is_take");
            });

          const $mainChk = $(
            '.is-take-checkbox[data-item-id="' + itemId + '"]'
          );
          if ($mainChk.length) {
            $mainChk.prop("checked", !!isCheck);
            const $mainRow = $mainChk.closest(".cart-item");
            if ($mainRow.length) {
              if (isCheck) $mainRow.addClass("item-selected");
              else $mainRow.removeClass("item-selected");
            }
          }

          recalcHeaderCart();
          if (typeof updateCartTotals === "function") {
            try {
              updateCartTotals();
            } catch (e) {
              console.warn(e);
            }
          }
        })
        .fail(function () {
          alert("Network error updating selection");
          $cb.prop("checked", !$cb.is(":checked"));
        });
    });
  });
})(jQuery);
