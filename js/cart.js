$(document).ready(function () {
  initCart();
});

function initCart() {
  $(document).on("change", ".is-take-checkbox", function () {
    const $checkbox = $(this);
    const itemId = $checkbox.data("item-id");
    const isTake = $checkbox.is(":checked") ? 1 : 0;
    const $item = $checkbox.closest(".cart-item");

    console.log("Checkbox changed:", { itemId, isTake });
    updateIsTake(itemId, isTake, $checkbox, $item);
  });

  $(document).on("click", ".qty-btn:not(.disabled)", function (e) {
    e.preventDefault();

    const $btn = $(this);
    const $item = $btn.closest(".cart-item");
    const itemId = $item.data("item-id");
    const currentQty = parseInt($item.find(".product-quantity-display").text());
    let newQty = currentQty;

    if ($btn.hasClass("plus")) {
      newQty = currentQty + 1;
    } else if ($btn.hasClass("minus")) {
      newQty = currentQty - 1;
    }

    if (newQty < 1) return;

    updateQuantity(itemId, newQty, $item);
  });

  $(document).on("click", ".delete-btn", function (e) {
    e.preventDefault();

    const $btn = $(this);
    const itemId = $btn.data("item-id");

    if (confirm("Remove this item from cart?")) {
      removeItem(itemId, $btn.closest(".cart-item"));
    }
  });
  initImagePopup();
  initSelectAll();
  initAddressManagement();
}

function updateIsTake(itemId, isTake, $checkbox, $item) {
  console.log("Updating is_take:", { itemId, isTake });

  $item.addClass("updating");
  $checkbox.prop("disabled", true);

  $.ajax({
    url: ROOT_DIR + "/AJAX/update_cart_take.php",
    type: "POST",
    data: {
      update_is_take: true,
      item_id: itemId,
      is_take: isTake,
    },
    success: function (response) {
      console.log("AJAX Success:", response);

      $item.removeClass("updating");
      $checkbox.prop("disabled", false);

      if (response.success) {
        if (isTake) {
          $item.addClass("item-selected");
        } else {
          $item.removeClass("item-selected");
        }

        updateCartTotals();

        // Also update is_check on server so slide-cart and full cart stay in sync
        $.post(ROOT_DIR + "/AJAX/update_cart_check.php", {
          update_is_check: true,
          item_id: itemId,
          is_check: isTake,
        })
          .done(function (r) {
            // update data attribute on checkbox for other scripts
            $checkbox.attr("data-is-check", isTake ? 1 : 0);
          })
          .fail(function () {
            console.warn("Failed to sync is_check after is_take update");
          });

        console.log("Successfully updated is_take to:", isTake);
      } else {
        $checkbox.prop("checked", !isTake);
        alert(
          "Error updating selection: " + (response.error || "Unknown error")
        );
        console.error("Update error:", response.error);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", { status, error, xhr });

      $item.removeClass("updating");
      $checkbox.prop("disabled", false);

      $checkbox.prop("checked", !isTake);

      if (xhr.status === 404) {
        alert(
          "Server error: update_cart_take.php not found. Please check the file path."
        );
      } else if (xhr.status === 500) {
        alert(
          "Server error: Internal server error. Please check the server logs."
        );
      } else {
        alert("Network error. Please check your connection and try again.");
      }
    },
  });
}

function updateQuantity(itemId, newQty, $item) {
  $item.addClass("updating");

  $.post(
    ROOT_DIR + "/AJAX/update_quantity.php",
    {
      update_quantity: true,
      item_id: itemId,
      quantity: newQty,
    },
    function (response) {
      $item.removeClass("updating");

      if (response.success) {
        $item.find(".product-quantity-display").text(newQty);

        const unitPrice = parseFloat($item.data("price"));
        const itemTotal = unitPrice * newQty;
        $item.find(".product-total").text("RM " + itemTotal.toFixed(2));

        updateCartTotals();

        updateQuantityButtons($item, newQty);
      } else {
        alert("Error updating quantity: " + response.error);
      }
    }
  ).fail(function (xhr, status, error) {
    $item.removeClass("updating");
    console.error("Quantity update error:", { status, error, xhr });
    alert("Network error. Please try again.");
  });
}

function removeItem(itemId, $item) {
  $item.addClass("updating");

  $.post(
    ROOT_DIR + "/AJAX/remove_item.php",
    {
      remove_item: true,
      item_id: itemId,
    },
    function (response) {
      if (response.success) {
        $item.slideUp(300, function () {
          $(this).remove();
          updateCartTotals();
          checkEmptyCart();
        });
      } else {
        $item.removeClass("updating");
        alert("Error removing item: " + response.error);
      }
    }
  ).fail(function (xhr, status, error) {
    $item.removeClass("updating");
    console.error("Remove error:", { status, error, xhr });
    alert("Network error. Please try again.");
  });
}

function updateCartTotals() {
  let cartTotal = 0;
  let selectedTotal = 0;

  $(".cart-item").each(function () {
    const $item = $(this);
    const quantity = parseInt($item.find(".product-quantity-display").text());
    const unitPrice = parseFloat($item.data("price"));
    const itemTotal = unitPrice * quantity;

    cartTotal += itemTotal;

    if ($item.find(".is-take-checkbox").is(":checked")) {
      selectedTotal += itemTotal;
    }
  });

  $(".total-label").text("Cart Total: RM " + cartTotal.toFixed(2));
  $("#selected-total").text(selectedTotal.toFixed(2));
}

function updateQuantityButtons($item, currentQty) {
  const $minusBtn = $item.find(".qty-btn.minus");

  if (currentQty <= 1) {
    $minusBtn.addClass("disabled");
  } else {
    $minusBtn.removeClass("disabled");
  }
}

function checkEmptyCart() {
  if ($(".cart-item").length === 0) {
    $(".cart-items-container").html(`
            <div class="empty-cart">
                <i class="fa-solid fa-cart-shopping" style="font-size: 4em; color: #ccc; margin-bottom: 20px;"></i>
                <p style="font-size: 1.3em; margin-bottom: 20px; color: #6c757d;">Your cart is empty</p>
                <a href="../product-list.php" class="btn" style="background: #FFA000; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-size: 1.1em; transition: all 0.3s ease;">
                    <i class="fa-solid fa-bag-shopping"></i> Continue Shopping
                </a>
            </div>
        `);
    $(".cart-footer").hide();
  }
}

function initImagePopup() {
  const popup = $("#imagePopup");
  const popupImage = $("#popupImage");
  const popupProductName = $("#popupProductName");
  const popupProductPrice = $("#popupProductPrice");
  const popupShortDescription = $("#popupShortDescription");
  const popupProductDescription = $("#popupProductDescription");
  const popupViewDetails = $("#popupViewDetails");

  $(document).on("click", ".clickable-image", function () {
    const productId = $(this).data("product-id");
    const productName = $(this).data("product-name");
    const productPrice = $(this).data("product-price");
    const productDescription = $(this).data("product-description");
    const productShortDesc = $(this).data("product-short-desc");

    popupImage.attr("src", $(this).attr("src"));
    popupImage.attr("alt", $(this).attr("alt"));
    popupProductName.text(productName);
    popupProductPrice.text(productPrice);
    popupShortDescription.html(
      productShortDesc
        ? `<strong>Key Features:</strong> ${productShortDesc}`
        : ""
    );
    popupProductDescription.text(
      productDescription || "No description available."
    );
    popupViewDetails.attr(
      "href",
      ROOT_DIR + "/product-detail.php?id=" + productId
    );

    popup.show();
    $("body").addClass("popup-open");
  });

  $(".popup-close").on("click", function () {
    popup.hide();
    $("body").removeClass("popup-open");
  });

  popup.on("click", function (e) {
    if (e.target === this) {
      popup.hide();
      $("body").removeClass("popup-open");
    }
  });
}

function initSelectAll() {
  $("#select-all-checkbox").on("change", function () {
    const isChecked = $(this).is(":checked");

    $(".is-take-checkbox").each(function () {
      const $checkbox = $(this);
      const itemId = $checkbox.data("item-id");
      const $item = $checkbox.closest(".cart-item");

      if ($checkbox.is(":checked") !== isChecked) {
        $checkbox.prop("checked", isChecked);
        updateIsTake(itemId, isChecked ? 1 : 0, $checkbox, $item);
      }
    });
  });
}

// Address Management Functions
function initAddressManagement() {
  const toggleFormBtn = $("#toggleAddressForm");
  const addressForm = $("#newAddressForm");
  const cancelFormBtn = $("#cancelAddressForm");

  // Check address limit on initialization
  updateAddressLimit();
  // Toggle address form
  toggleFormBtn.on("click", function () {
    addressForm.toggleClass("hidden");
    if (!addressForm.hasClass("hidden")) {
      addressForm.find("input:first").focus();
    }
  });

  // Cancel form
  cancelFormBtn.on("click", function () {
    addressForm.addClass("hidden");
    addressForm[0].reset();
  });

  // Submit form
  addressForm.on("submit", function (e) {
    e.preventDefault();
    saveNewAddress();
  });

  $(document).on("click", ".edit-address-btn", function (e) {
    e.preventDefault();
    e.stopPropagation();
    const $btn = $(this);
    // Use attr() to reliably read data-* attributes (consistent across jQuery versions)
    const id = $btn.attr("data-address-id") || $btn.data("addressId");
    $("#address_id").val(id);
    $("#address_name").val(
      $btn.attr("data-address-name") || $btn.data("addressName") || ""
    );
    $("#address_one").val(
      $btn.attr("data-address-one") || $btn.data("addressOne") || ""
    );
    $("#address_two").val(
      $btn.attr("data-address-two") || $btn.data("addressTwo") || ""
    );
    $("#address_three").val(
      $btn.attr("data-address-three") || $btn.data("addressThree") || ""
    );
    $("#state").val($btn.attr("data-state") || $btn.data("state") || "");
    $("#post_code").val(
      $btn.attr("data-post-code") || $btn.data("postCode") || ""
    );
    $("#newAddressForm").removeClass("hidden");
    $("#address_one").focus();
  });

  // Fallback: double-click the address row to open edit form (in case small edit button is hard to click)
  $(document).on("dblclick", ".address-option", function (e) {
    // ignore dblclicks on the radio/input elements themselves
    if ($(e.target).is("input, label, .address-actions, button, a")) return;
    const $btn = $(this).find(".edit-address-btn").first();
    if ($btn.length) {
      $btn.trigger("click");
    }
  });
}

function saveNewAddress() {
  const form = $("#newAddressForm");

  const formData = {
    address_id: $("#address_id").val() || "",
    address_name: $("#address_name").val() || "Home",
    address_one: $("#address_one").val(),
    address_two: $("#address_two").val(),
    address_three: $("#address_three").val(),
    state: $("#state").val(),
    post_code: $("#post_code").val(),
    country: "Malaysia",
  };

  // Validate postal code
  if (!/^\d{5}$/.test(formData.post_code)) {
    showAddressMessage("Postal code must be 5 digits", "error");
    return;
  }

  $.ajax({
    type: "POST",
    url: ROOT_DIR + "/AJAX/save_address.php",
    data: formData,
    dataType: "json",
    success: function (response) {
      if (response.success) {
        showAddressMessage(response.message || "Saved", "success");

        const addr = response.address_display;
        const existing = $("#address-" + addr.address_id).closest(
          ".address-option"
        );
        if (existing.length) {
          // update display lines
          const $opt = existing;
          $opt.find(".address-label .address-details").html(`
      <p class="address-line"><strong>${escapeHtml(
        addr.address_name || addr.address_one
      )}</strong></p>
      <p class="address-line">${escapeHtml(addr.address_one)}</p>
      ${
        addr.address_two
          ? `<p class="address-line">${escapeHtml(addr.address_two)}</p>`
          : ""
      }
      ${
        addr.address_three
          ? `<p class="address-line">${escapeHtml(addr.address_three)}</p>`
          : ""
      }
      <p class="address-line">${escapeHtml(addr.post_code)} ${escapeHtml(
            addr.state
          )}, ${escapeHtml(addr.country)}</p>
    `);
          // also update the edit button data-* values
          const $editBtn = $opt.find(".edit-address-btn");
          $editBtn.data("address-name", addr.address_name);
          $editBtn.data("address-one", addr.address_one);
          $editBtn.data("address-two", addr.address_two);
          $editBtn.data("address-three", addr.address_three);
          $editBtn.data("state", addr.state);
          $editBtn.data("post-code", addr.post_code);
          $editBtn.data("country", addr.country);
        } else {
          addAddressToList(addr);
        }

        // reset form
        $("#newAddressForm")[0].reset();
        $("#address_id").val("");
        $("#newAddressForm").addClass("hidden");

        // Check and update address limit after adding
        updateAddressLimit();
      } else {
        showAddressMessage(
          response.message || "Failed to save address",
          "error"
        );
      }
    },
    error: function (xhr, status, error) {
      showAddressMessage("Error: " + error, "error");
      console.error("AJAX Error:", error);
    },
  });
}

function addAddressToList(addressData) {
  const addressList = $(".address-options");
  const isFirstAddress = addressList.find(".address-option").length === 0;
  const html = `
    <div class="address-option" data-address-id="${addressData.address_id}">
      <input type="radio"
        class="address-radio"
        id="address-${addressData.address_id}"
        name="selected_address"
        value="${addressData.address_id}"
        data-address-id="${addressData.address_id}"
        ${isFirstAddress ? "checked" : ""} />

      <label for="address-${addressData.address_id}" class="address-label">
        <div class="address-details">
          <p class="address-line"><strong>${escapeHtml(
            addressData.address_name || addressData.address_one
          )}</strong></p>
          <p class="address-line">${escapeHtml(addressData.address_one)}</p>
          ${
            addressData.address_two
              ? `<p class="address-line">${escapeHtml(
                  addressData.address_two
                )}</p>`
              : ``
          }
          ${
            addressData.address_three
              ? `<p class="address-line">${escapeHtml(
                  addressData.address_three
                )}</p>`
              : ``
          }
          <p class="address-line">${escapeHtml(
            addressData.post_code
          )} ${escapeHtml(addressData.state)}, ${escapeHtml(
    addressData.country
  )}</p>
        </div>
      </label>

    <div class="address-actions">
        <button type="button" class="btn-edit edit-address-btn" style="margin-left:8px;"
          data-address-id="${addressData.address_id}"
          data-address-name="${escapeHtml(addressData.address_name)}"
          data-address-one="${escapeHtml(addressData.address_one)}"
          data-address-two="${escapeHtml(addressData.address_two)}"
          data-address-three="${escapeHtml(addressData.address_three)}"
          data-state="${escapeHtml(addressData.state)}"
          data-post-code="${escapeHtml(addressData.post_code)}"
          data-country="${escapeHtml(addressData.country)}">
          Edit
        </button>
    </div>
  </div>
  `;

  addressList.append(html);

  // Update address limit after adding
  updateAddressLimit();
}

function updateAddressLimit() {
  const existingCount = $(".address-option").length;
  if (existingCount >= 3) {
    $("#toggleAddressForm").hide();
    $("#newAddressForm").addClass("hidden");
    if (!$(".address-limit-msg").length) {
      $(".address-list").before(
        '<div class="address-limit-msg">You can add up to 3 addresses only.</div>'
      );
    }
  } else {
    $("#toggleAddressForm").show();
    $(".address-limit-msg").remove();
  }
}

function showAddressMessage(message, type) {
  let messageHtml = `<div class="address-message ${type}">${escapeHtml(
    message
  )}</div>`;

  // Remove existing messages
  $(".address-message").remove();

  // Add new message
  $("#newAddressForm").before(messageHtml);

  // Auto-hide success messages after 3 seconds
  if (type === "success") {
    setTimeout(function () {
      $(".address-message").fadeOut(function () {
        $(this).remove();
      });
    }, 3000);
  }
}

function escapeHtml(text) {
  if (text === null || text === undefined) return "";
  const s = String(text);
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };
  return s.replace(/[&<>"']/g, (m) => map[m]);
}

// Note: edit-address-btn handler is registered inside initAddressManagement()

function proceedToCheckout() {
  // 1) Must have at least 1 checked item
  let selectedCount = 0;
  $(".cart-item").each(function () {
    if ($(this).find(".is-take-checkbox").is(":checked")) selectedCount++;
  });

  if (selectedCount === 0) {
    alert("Please select at least 1 item to checkout.");
    return;
  }

  // 2) Must select delivery address
  const $selectedAddress = $("input[name='selected_address']:checked");
  if ($selectedAddress.length === 0) {
    alert("Please select a delivery address.");
    return;
  }

  // 3) Must select payment method
  const paymentMethod = $("input[name='payment_method']:checked").val();
  if (!paymentMethod) {
    alert("Please select a payment method.");
    return;
  }

  // Submit to create_order.php
  const form = $("<form>", {
    method: "POST",
    action: ROOT_DIR + "/pages/checkout/create_order.php",
    style: "display:none;",
  });

  form.append($("<input>", { type: "hidden", name: "payment_method", value: paymentMethod }));
  form.append($("<input>", { type: "hidden", name: "address_id", value: $selectedAddress.val() }));

  $("body").append(form);
  form.submit();
}
function updateCheckoutButtonState() {
  const hasCheckedItem = $(".is-take-checkbox:checked").length > 0;
  const hasAddress = $("input[name='selected_address']:checked").length > 0;
  const hasPayment = $("input[name='payment_method']:checked").length > 0;

  const canCheckout = hasCheckedItem && hasAddress && hasPayment;

  $("#btnCheckout").prop("disabled", !canCheckout);

  if (!canCheckout) {
    $("#btnCheckout").addClass("disabled");
  } else {
    $("#btnCheckout").removeClass("disabled");
  }
}

$(document).on("change", ".is-take-checkbox, input[name='selected_address'], input[name='payment_method']", function () {
  updateCheckoutButtonState();
});

$(document).ready(function () {
  updateCheckoutButtonState();
});
