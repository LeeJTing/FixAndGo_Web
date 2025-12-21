$(document).ready(function () {
  // Track new image selection
  $("#categoryImageInput").on("change", function (e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        $("#previewImg").attr("src", e.target.result);
        $("#previewContainer").show();
        $(".add-new").hide();
        $("#categoryImageInput").next(".error-msg").remove();
      };
      reader.readAsDataURL(file);
    }
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

    if (status === "1") {
      if (!hasNewImage) {
        $("select[name='status']").val(0);
        clearError();
        showError("#categoryImageInput", "Please upload a new image.");
      }
    }
    if (status === "1" && !hasCurrentImage && !hasNewImage) {
      $("select[name='status']").val(0);
      isValid = false;
    }

    if (!isValid) {
      return false;
    }

    this.submit();
  });

  $("#removePreview").on("click", function () {
    $("#categoryImageInput").val("");
    $("#previewContainer").hide();
    $(".add-new").show();
    showError("#categoryImageInput", "Please select an image.");
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

                $("select[name='status']").val(0);
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
