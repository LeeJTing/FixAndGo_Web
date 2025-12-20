(function ($) {
  function escapeHtml(text) {
    return String(text)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/\"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function renderSidebarCart(payload) {
    const items = (payload && payload.items) || [];
    if (!items.length) {
      $("#cartItems").html('<p class="empty-cart">Your cart is empty</p>');
      $("#cartTotalPrice").text("RM 0.00");
      if (typeof payload.cartCount !== "undefined") {
        $("#cartCount").text(payload.cartCount);
      } else {
        $("#cartCount").text("0");
      }
      return;
    }

    const html = items
      .map(function (it) {
        const itemId = parseInt(it.item_id, 10) || 0;
        const name = escapeHtml(it.product_name || "");
        const price = parseFloat(it.unit_price) || 0;
        const qty = parseInt(it.qty, 10) || 1;
        const total = (price * qty).toFixed(2);
        const isCheck = parseInt(it.is_check, 10) ? true : false;
        const filePath = (it.file_path || "").replace(/^\/+/, "");
        const imgSrc = filePath ? ROOT_DIR + "/" + filePath : "";

        return (
          '<div class="cart-item flex align-center" data-item-id="' +
          itemId +
          '" data-price="' +
          price +
          '">' +
          '<div class="item-checkbox-container">' +
          '<input type="checkbox" class="item-checkbox is-check-checkbox" data-item-id="' +
          itemId +
          '" ' +
          (isCheck ? "checked" : "") +
          " />" +
          "</div>" +
          (imgSrc
            ? '<img src="' +
              imgSrc +
              '" alt="' +
              name +
              '" class="product-image" style="width:50px;height:50px;object-fit:cover;" />'
            : "") +
          '<div class="cart-item-info">' +
          '<div class="product-name-small">' +
          name +
          "</div>" +
          '<div class="product-price-small">RM ' +
          price.toFixed(2) +
          "</div>" +
          "</div>" +
          '<div class="quantity-control-small">' +
          '<a href="javascript:void(0)" class="qty-btn minus ' +
          (qty <= 1 ? "disabled" : "") +
          '">-</a>' +
          '<span class="product-quantity-display">' +
          qty +
          "</span>" +
          '<a href="javascript:void(0)" class="qty-btn plus">+</a>' +
          "</div>" +
          '<div class="product-total-small">RM <span class="product-total">' +
          total +
          "</span></div>" +
          '<a href="javascript:void(0)" class="delete-btn" data-item-id="' +
          itemId +
          '">Remove</a>' +
          "</div>"
        );
      })
      .join("");

    $("#cartItems").html(html);
    if (typeof payload.cartTotal !== "undefined") {
      const t = parseFloat(payload.cartTotal) || 0;
      $("#cartTotalPrice").text("RM " + t.toFixed(2));
    }
    if (typeof payload.cartCount !== "undefined") {
      $("#cartCount").text(payload.cartCount);
    }
  }

  function refreshSidebarCart() {
    return $.ajax({
      url: ROOT_DIR + "/AJAX/get_cart_sidebar.php",
      type: "POST",
      dataType: "json",
      data: { get_cart_sidebar: true },
    }).done(function (resp) {
      if (resp && resp.success) {
        renderSidebarCart(resp);
        recalcHeaderCart();
      }
    });
  }

  // Allow other scripts to refresh the sidebar when needed
  window.FixAndGo = window.FixAndGo || {};
  window.FixAndGo.refreshSidebarCart = refreshSidebarCart;

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
      count += qty;

      // Total only includes checked items (for checkout total)
      if (checked) {
        total += qty * price;
      }
    });
    $("#cartTotalPrice").text("RM " + total.toFixed(2));
    $("#cartCount").text(count);
  }

  $(function () {
    // initialize count from server-rendered value or data attribute
    const initialCount =
      parseInt($("#cartCount").attr("data-count")) ||
      parseInt($("#cartCount").text()) ||
      0;
    $("#cartCount").text(initialCount);
    recalcHeaderCart();

    // Open/close handlers
    $("#cartIcon").on("click", function (e) {
      e.stopPropagation();
      $("#cartSidebar").addClass("open");
      $("#cartOverlay").show();

      // Pull fresh cart data so newly-added items appear immediately
      refreshSidebarCart();
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

    // is_check checkbox -> update DB (is_check) and also update is_take so both views stay in sync
    $(document).on("change", "#cartItems .is-check-checkbox", function () {
      const $cb = $(this);
      const itemId = $cb.data("item-id");
      const isCheck = $cb.is(":checked") ? 1 : 0;

      // First update the is_check flag
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

          // Also update is_take on server so full-cart and slide-cart remain consistent
          $.post(ROOT_DIR + "/AJAX/update_cart_take.php", {
            update_is_take: true,
            item_id: itemId,
            is_take: isCheck,
          })
            .done(function (resp2) {
              if (!resp2.success) {
                console.warn("Failed to sync is_take:", resp2.error);
              }
            })
            .fail(function () {
              console.warn("Network error syncing is_take");
            });

          // Update any matching checkboxes on the main cart page (if present)
          const $mainChk = $(
            '.is-take-checkbox[data-item-id="' + itemId + '"]'
          );
          if ($mainChk.length) {
            $mainChk.prop("checked", !!isCheck);
            // update visual selection class on the cart item row
            const $mainRow = $mainChk.closest(".cart-item");
            if ($mainRow.length) {
              if (isCheck) $mainRow.addClass("item-selected");
              else $mainRow.removeClass("item-selected");
            }
          }

          // Recalculate header totals and page totals if available
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

//  (function($) {
//         function recalcHeaderCart() {
//             // Recalculate total only for items checked (is_check)
//             let total = 0;
//             let count = 0;
//             $('#cartItems .cart-item').each(function() {
//                 const $it = $(this);
//                 const checked = $it.find('.is-check-checkbox').is(':checked');
//                 const qty = parseInt($it.find('.product-quantity-display').text()) || 0;
//                 const price = parseFloat($it.data('price')) || 0;
//                 if (checked) {
//                     total += qty * price;
//                     count += 1;
//                 }
//             });
//             $('#cartTotalPrice').text('RM ' + total.toFixed(2));
//             $('#cartCount').text(count);
//         };

//         $(function() {
//             // initialize count from server-rendered value
//             $('#cartCount').text(<?= (int)$cart_count ?>);

//             // Open/close handlers
//             $('#cartIcon').on('click', function(e) {
//                 e.stopPropagation();
//                 $('#cartSidebar').addClass('open');
//                 $('#cartOverlay').show();
//             });

//             $('.close-cart, #cartOverlay').on('click', function() {
//                 $('#cartSidebar').removeClass('open');
//                 $('#cartOverlay').hide();
//             });

//             // Quantity buttons
//             $(document).on('click', '#cartItems .qty-btn', function(e) {
//                 e.preventDefault();
//                 const $btn = $(this);
//                 const $item = $btn.closest('.cart-item');
//                 const itemId = $item.data('item-id');
//                 let qty = parseInt($item.find('.product-quantity-display').text()) || 0;
//                 if ($btn.hasClass('plus')) qty++;
//                 if ($btn.hasClass('minus')) qty = Math.max(1, qty - 1);

//                 $.post(ROOT_DIR + '/AJAX/update_quantity.php', {
//                         update_quantity: true,
//                         item_id: itemId,
//                         quantity: qty
//                     })
//                     .done(function(resp) {
//                         if (resp.success) {
//                             $item.find('.product-quantity-display').text(qty);
//                             const price = parseFloat($item.data('price')) || 0;
//                             $item.find('.product-total').text((price * qty).toFixed(2));
//                             recalcHeaderCart();
//                         } else {
//                             alert('Error: ' + (resp.error || 'Failed to update quantity'));
//                         }
//                     }).fail(function() {
//                         alert('Network error updating quantity');
//                     });
//             });

//             // Remove
//             $(document).on('click', '#cartItems .delete-btn', function(e) {
//                 e.preventDefault();
//                 const $btn = $(this);
//                 const itemId = $btn.data('item-id');
//                 if (!confirm('Remove this item from cart?')) return;
//                 $.post(ROOT_DIR + '/AJAX/remove_item.php', {
//                         remove_item: true,
//                         item_id: itemId
//                     })
//                     .done(function(resp) {
//                         if (resp.success) {
//                             $btn.closest('.cart-item').slideUp(200, function() {
//                                 $(this).remove();
//                                 recalcHeaderCart();
//                             });
//                         } else {
//                             alert('Error: ' + (resp.error || 'Failed to remove item'));
//                         }
//                     }).fail(function() {
//                         alert('Network error removing item');
//                     });
//             });

//             // is_check checkbox -> update DB (is_check) and also update is_take so both views stay in sync
//             $(document).on('change', '#cartItems .is-check-checkbox', function() {
//                 const $cb = $(this);
//                 const itemId = $cb.data('item-id');
//                 const isCheck = $cb.is(':checked') ? 1 : 0;

//                 // First update the is_check flag
//                 $.post(ROOT_DIR + '/AJAX/update_cart_check.php', {
//                         update_is_check: true,
//                         item_id: itemId,
//                         is_check: isCheck
//                     })
//                     .done(function(resp) {
//                         if (!resp.success) {
//                             alert('Error updating selection: ' + (resp.error || 'Unknown'));
//                             $cb.prop('checked', !isCheck);
//                             return;
//                         }

//                         // Also update is_take on server so full-cart and slide-cart remain consistent
//                         $.post(ROOT_DIR + '/AJAX/update_cart_take.php', {
//                                 update_is_take: true,
//                                 item_id: itemId,
//                                 is_take: isCheck
//                             })
//                             .done(function(resp2) {
//                                 if (!resp2.success) {
//                                     console.warn('Failed to sync is_take:', resp2.error);
//                                 }
//                             }).fail(function() {
//                                 console.warn('Network error syncing is_take');
//                             });

//                         // Update any matching checkboxes on the main cart page (if present)
//                         const $mainChk = $('.is-take-checkbox[data-item-id="' + itemId + '"]');
//                         if ($mainChk.length) {
//                             $mainChk.prop('checked', !!isCheck);
//                             // update visual selection class on the cart item row
//                             const $mainRow = $mainChk.closest('.cart-item');
//                             if ($mainRow.length) {
//                                 if (isCheck) $mainRow.addClass('item-selected');
//                                 else $mainRow.removeClass('item-selected');
//                             }
//                         }

//                         // Recalculate header totals and page totals if available
//                         recalcHeaderCart();
//                         if (typeof updateCartTotals === 'function') {
//                             try {
//                                 updateCartTotals();
//                             } catch (e) {
//                                 console.warn(e);
//                             }
//                         }
//                     }).fail(function() {
//                         alert('Network error updating selection');
//                         $cb.prop('checked', !$cb.is(':checked'));
//                     });
//             });
//         });

//         $('.logout-btn').on('click', function(e) {
//             e.preventDefault();

//             logout = confirm("Click OK to confirm to exit!");
//             if (logout) {
//                 $.post(ROOT_DIR + "/_logout.php", {
//                     logout: true
//                 }).done(function() {
//                     window.location.href = ROOT_DIR + "/pages/guest/login.php";
//                     console.log("posted");
//                 }).fail(function() {
//                     console.log("failed");
//                 })
//             }
//         });
//     })(jQuery);
