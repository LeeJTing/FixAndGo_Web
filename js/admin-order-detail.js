$(document).ready(function () {
  // Handle status update form submission
  $("#statusUpdateForm").on("submit", function (e) {
    e.preventDefault();

    const formData = {
      order_id: $('input[name="order_id"]').val(),
      order_status: $("#order_status").val(),
      payment_status: $("#payment_status").val(),
    };

    // Show loading state
    const $btn = $(".btn-update");
    const originalText = $btn.html();
    $btn
      .html('<i class="fas fa-spinner fa-spin"></i> Updating...')
      .prop("disabled", true);

    $.ajax({
      url: "../../controller/admin-controller.php?function=updateOrderStatus",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          showNotification("Order status updated successfully!", "success");

          // Update status badges on the page
          setTimeout(function () {
            location.reload();
          }, 1500);
        } else {
          showNotification(
            response.message || "Failed to update order status",
            "error"
          );
          $btn.html(originalText).prop("disabled", false);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
        showNotification(
          "Failed to update order status. Please try again.",
          "error"
        );
        $btn.html(originalText).prop("disabled", false);
      },
    });
  });

  // Show notification function
  function showNotification(message, type) {
    const notification = $("<div>")
      .addClass("notification")
      .addClass(
        type === "success" ? "notification-success" : "notification-error"
      )
      .html(
        `
        <i class="fas fa-${
          type === "success" ? "check-circle" : "exclamation-circle"
        }"></i>
        <span>${message}</span>
      `
      )
      .appendTo("body")
      .fadeIn(300);

    setTimeout(function () {
      notification.fadeOut(300, function () {
        $(this).remove();
      });
    }, 3000);
  }
});
