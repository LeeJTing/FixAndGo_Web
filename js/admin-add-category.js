$(document).ready(function () {
  let imageSelected = false;

  // Image preview
  $("#categoryImageInput").on("change", function (e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        $("#previewImg").attr("src", e.target.result);
        $("#previewContainer").show();
        $(".add-new").hide();
        imageSelected = true;
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
    imageSelected = false;
    $("#image-error").text("Please select an image.");
  });

  // Form submission validation
  $("#addCategoryForm").on("submit", function (e) {
    let isValid = true;

    clearError();
    // Validate category_name
    if (!validateField("category_name", { required: true, min: 3, max: 30 })) {
      isValid = false;
    }

    if (!validateField("description", { required: true, min: 20, max: 1500 })) {
      isValid = false;
    }

    // Validate image upload
    if (!validateFileInput($("#categoryImageInput")[0])) {
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
    }
  });
});
