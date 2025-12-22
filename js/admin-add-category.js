$(document).ready(function () {
  // Image preview
  $("#categoryImageInput").on("change", function (e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        $("#previewImg").attr("src", e.target.result);
        $("#previewContainer").show();
        $(".add-new").hide();
        $("#image-error").text("");
      };
      reader.readAsDataURL(file);
    }
  });

  // Remove preview
  $("#removePreview").on("click", function () {
    $("#categoryImageInput").val("");
    $("#previewContainer").hide();
    $(".add-new").show();
    $("#image-error").text("Please select an image.");
  });

  // Form submission validation
  $("#addCategoryForm").on("submit", function (e) {
    e.preventDefault(); // always prevent default first
    let isValid = true;

    clearError();

    // Validate category name
    if (!validateField("category_name", { required: true, min: 3, max: 30 })) {
      isValid = false;
    }

    // Validate description (optional but min length if filled)
    if (
      !validateField("description", { required: false, min: 20, max: 1500 })
    ) {
      isValid = false;
    }

    if ($("select[name='status']").val() === "active") {
      if (!validateFileInput("#categoryImageInput", { required: true })) {
        isValid = false;
      }
    } else {
      if (
        !validateFileInput("#categoryImageInput", {
          types: ["image/jpeg", "image/png", "image/gif", "image/webp"],
          maxSize: 2 * 1024 * 1024,
          required: false,
        })
      ) {
        isValid = false;
      }
    }

    if (!isValid) {
      $(".input-error:first").focus();
      return;
    }

    this.submit();
  });
});
