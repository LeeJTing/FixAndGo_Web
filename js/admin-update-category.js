$(document).ready(function () {
  const uploadContainer = document.getElementById("addNewBox");
  const fileInput = document.getElementById("categoryImageInput");
  const previewImg = document.getElementById("previewImg");
  const removePreview = document.getElementById("removePreview");
  const placeholderIcon = document.getElementById("placeholderIcon");

  // Click to open file dialog
  uploadContainer.addEventListener("click", () => fileInput.click());

  // Handle file preview
  function handleFile(file) {
    if (!file.type.startsWith("image/")) return;

    // Update preview
    const reader = new FileReader();
    reader.onload = function (e) {
      previewImg.src = e.target.result;
      previewImg.style.display = "block";
      removePreview.style.display = "block";
      placeholderIcon.style.display = "none";
    };
    reader.readAsDataURL(file);

    // Set the file in the input so it will submit
    const dataTransfer = new DataTransfer(); // modern browsers
    dataTransfer.items.add(file);
    fileInput.files = dataTransfer.files;
  }

  // Input change
  fileInput.addEventListener("change", () => handleFile(fileInput.files[0]));

  // Drag & Drop
  uploadContainer.addEventListener("dragover", (e) => {
    e.preventDefault();
    uploadContainer.classList.add("dragover");
  });

  uploadContainer.addEventListener("dragleave", () => {
    uploadContainer.classList.remove("dragover");
  });

  uploadContainer.addEventListener("drop", (e) => {
    e.preventDefault();
    uploadContainer.classList.remove("dragover");
    if (e.dataTransfer.files.length > 0) {
      handleFile(e.dataTransfer.files[0]);
    }
  });

  // Remove preview
  removePreview.addEventListener("click", function (e) {
    e.stopPropagation();
    fileInput.value = "";
    previewImg.src = "#";
    previewImg.style.display = "none";
    removePreview.style.display = "none";
    placeholderIcon.style.display = "block";
  });

  // Form submission validation
  $("#updateCategoryForm").on("submit", function (e) {
    e.preventDefault();

    let isValid = true;
    clearError();
    if (!validateField("category_name", { required: true, min: 3, max: 30 })) {
      isValid = false;
    }

    if (
      !validateField("description", { required: false, min: 20, max: 1500 })
    ) {
      isValid = false;
    }

    const status = $("select[name='status']").val();
    const hasCurrentImage = $("#currentImgBox").length > 0;
    const hasNewImage = $("#categoryImageInput")[0].files.length > 0;

    if (status === "active") {
      if (!hasNewImage) {
        clearError();
        showError("#categoryImageInput", "Please upload a new image.");
      }
    }
    if (status === "active" && !hasCurrentImage && !hasNewImage) {
      isValid = false;
    }

    if (!isValid) {
      return false;
    }

    this.submit();
  });

  $("#removeCurrent").on("click", function () {
    const categoryCode = $("input[name='category_code']").val();
    if (!categoryCode) {
      showMessage("Category code missing.", "error");
      return;
    }
    showConfirm(
      "Are you sure you want to delete the current image?",
      function (result) {
        if (result) {
          $.ajax({
            url: "../../controller/category-controller.php",
            type: "POST",
            dataType: "json",
            data: {
              function: "delete_image",
              category_code: categoryCode,
            },
            success: function (res) {
              if (res.success) {
                $("#currentImgBox").remove();
                $("#categoryImageInput").val("");
                $(".add-new").show();

                clearError();
                showError("#categoryImageInput", "Please upload a new image.");

                $("select[name='status']").val("inactive");
              } else {
                showMessage(".admin-layout", "Failed to delete image.");
              }
            },
            error: function () {
              showMessage(
                ".admin-layout",
                "Server error while deleting image."
              );
            },
          });
        } else {
          return;
        }
      }
    );
  });
});
