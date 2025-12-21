function showMessage(text, type = "info", autoHide = true, duration = 5000) {
  // Valid types
  const validTypes = ["success", "error", "warning", "info"];
  if (!validTypes.includes(type)) type = "info";

  // Create message element
  const message = $(`
        <div class="message ${type}">
            <span>${text}</span>
            <button class="close-btn" aria-label="Close">&times;</button>
        </div>
    `);

  // Append to container
  $("#message-container").append(message);

  // Trigger show animation
  setTimeout(() => message.addClass("show"), 100);

  // Close button
  message.find(".close-btn").on("click", function () {
    message.removeClass("show");
    setTimeout(() => message.remove(), 400);
  });

  // Auto hide
  if (autoHide && duration > 0) {
    setTimeout(() => {
      message.removeClass("show");
      setTimeout(() => message.remove(), 400);
    }, duration);
  }

  // Optional: return the element if you want to manipulate it later
  return message;
}
