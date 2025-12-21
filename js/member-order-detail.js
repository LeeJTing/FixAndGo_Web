$("#cancelOrderForm").on("submit", function (e) {
  e.preventDefault();

  const orderId = $(this).find("input[name='order_id']").val(); // or .data('order-id')
  const user_id = $("input[name='user_id']").val();

  showConfirm("Are you sure you want to cancel this order?", function (result) {
    if (result) {
      const cancelBtn = $("#cancelOrderForm button[type='submit']");
      cancelBtn.prop("disabled", true).text("Cancelling...");

      $.post("../../controller/order-controller.php", {
        action: "cancelOrder",
        order_id: orderId,
        userId: user_id, // optional – better to rely on session
      })
        .done(function (response) {
          if (response.success) {
            $(".status")
              .removeClass("processing")
              .addClass("cancelled")
              .text("Cancelled");
            $(".btn-danger").remove();
            alert("Order cancelled successfully.");
          } else {
            alert("Error: " + (response.message || "Unable to cancel order."));
          }
        })
        .fail(function (xhr) {
          alert("Request failed. Check console for details.");
          console.error(xhr.responseText);
        })
        .always(function () {
          cancelBtn.prop("disabled", false).text("Cancel Order");
        });
    }
  });
});
