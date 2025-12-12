$(document).ready(function () {
  var productId = $("#productId").val();
  let newFiles = []; // Array for newly selected files

  // Function to update input element with newFiles
  function updateInputFiles() {
    const dataTransfer = new DataTransfer();
    newFiles.forEach((file) => dataTransfer.items.add(file));
    $("#newImagesInput")[0].files = dataTransfer.files;
    console.log("Current new_images[] files:", $("#newImagesInput")[0].files);
  }

  // Handle new files selection
  $("#newImagesInput").on("change", function (e) {
    const files = Array.from(this.files);

    files.forEach((file) => {
      newFiles.push(file);

      const reader = new FileReader();
      reader.onload = function (ev) {
        const preview = $(`
          <div class="img-box new-img">
            <img src="${ev.target.result}" alt="preview">
            <button type="button" class="remove-img">×</button>
          </div>
        `);

        preview.insertBefore($(".img-box.add-new"));

        // Remove newly added image
        preview.find(".remove-img").click(() => {
          const index = newFiles.indexOf(file);
          if (index > -1) newFiles.splice(index, 1);
          preview.remove();
          updateInputFiles();
        });

        updateInputFiles();
      };
      reader.readAsDataURL(file);
    });

    $(this).val(""); // Allow selecting same file again
  });

  // Handle removing existing images from DB
  $(".image-preview").on("click", ".existing-img .remove-img", function () {
    const parentBox = $(this).parent();
    const fileName = parentBox.data("filename"); // make sure your div has data-filename attribute
    if (!fileName) return console.error("No filename!");
    console.log(fileName);
    showConfirm(
      "Are you sure you want to delete this image?",
      function (result) {
        if (result) {
          $.post(
            "../../controller/admin-controller.php",
            {
              filename: fileName,
              function: "file_delete",
              id: productId,
            },
            function (response) {
              if (response.status === "success") {
                parentBox.remove();
              } else {
                alert("Failed to delete image.");
                console.log(response.filePath);
              }
            },
            "json"
          );
        } else {
          console.log("Deletion canceled for:", fileName);
        }
      }
    );
  });
});
