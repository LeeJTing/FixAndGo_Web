$(document).ready(function () {
  // Edit button click
  $(".btn-text-edit").on("click", function () {
    const userId = $(this).data("user-id"); // Use data attribute
    const orderId = $(this).data("order-id"); // Use data attribute
    $("#form_order_id").val(orderId);

    // Show modal
    $("#address-form-overlay").fadeIn(200).css("display", "flex");
    $("body").css("overflow", "hidden");

    // AJAX call to fetch addresses
    $.ajax({
      url: "../../controller/order-controller.php",
      type: "GET",
      data: {
        action: "getAddress",
        user_id: userId,
        order_id: orderId,
      },
      dataType: "json",
      success: function (response) {
        const container = $("#address-list-container");
        container.empty(); // Clear previous content

        if (response.addresses && response.addresses.length > 0) {
          response.addresses.forEach((addr) => {
            const addrTwo = addr.address_two ? addr.address_two + "<br>" : "";
            const addrThree = addr.address_three
              ? addr.address_three + "<br>"
              : "";

            const html = `
                            <label class="address-option">
                                <input type="radio" name="address" value="${addr.address_id}">
                                <div class="address-details">
                                    <strong>${addr.address_name}</strong><br>
                                    ${addr.address_one}<br>
                                    ${addrTwo}
                                    ${addrThree}
                                    ${addr.post_code}, ${addr.state}<br>
                                    ${addr.country}
                                </div>
                            </label>
                        `;
            container.append(html);
          });
        }
      },
      error: function () {
        $("#address-list-container").html(
          '<p style="color:red; text-align:center;">Failed to load addresses</p>'
        );
      },
    });
  });

  // Cancel button
  $(".btn-cancel").on("click", function () {
    hideAddressForm();
  });

  // Clicking outside modal closes it
  $("#address-form-overlay").on("click", function (event) {
    if (event.target.id === "address-form-overlay") {
      hideAddressForm();
    }
  });

  function hideAddressForm() {
    $("#address-form-overlay").fadeOut(200);
    $("body").css("overflow", "auto");
  }
});
