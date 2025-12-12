function showConfirm(message, callback) {
  if ($("#customConfirmModal").length === 0) {
    $("body").append(`
      <div id="customConfirmModal" class="modal-overlay">
        <div class="modal-box">
          <button class="modal-close">&times;</button>
          <p id="customConfirmMessage"></p>
          <div class="modal-buttons">
            <button id="customConfirmYes" class="btn-yes">Yes</button>
            <button id="customConfirmNo" class="btn-no">No</button>
          </div>
        </div>
      </div>
    `);
  }

  const $modal = $("#customConfirmModal");
  const $box = $modal.find(".modal-box");
  const $close = $modal.find(".modal-close");

  $("#customConfirmMessage").text(message);

  // Show modal with animation
  $modal.addClass("show");
  $box.css({ transform: "translateY(-60px)", opacity: 0 });

  setTimeout(() => {
    $box.css({ transform: "translateY(0)", opacity: 1 });
  }, 20);

  // Clear old handlers
  $("#customConfirmYes").off("click");
  $("#customConfirmNo").off("click");
  $close.off("click");

  function closeModal() {
    $box.css({ transform: "translateY(-60px)", opacity: 0 });
    setTimeout(() => $modal.removeClass("show"), 250);
  }

  $("#customConfirmYes").on("click", function () {
    closeModal();
    callback(true);
  });

  $("#customConfirmNo").on("click", function () {
    closeModal();
    callback(false);
  });

  // X Button closes (same as NO)
  $close.on("click", function () {
    closeModal();
    callback(false);
  });
}
