$(document).ready(function () {
  // Image preview
  // $("#categoryImageInput").on("change", function (e) {
  //   const file = e.target.files[0];
  //   if (file) {
  //     const reader = new FileReader();
  //     reader.onload = function (e) {
  //       $("#previewImg").attr("src", e.target.result);
  //       $("#previewContainer").show();
  //       $(".add-new").hide();
  //       $("#image-error").text("");
  //     };
  //     reader.readAsDataURL(file);
  //   }
  // });
  const uploadContainer = document.getElementById("uploadContainer");
  const fileInput = document.getElementById("categoryImageInput");
  const previewImg = document.getElementById("previewImg");
  const removePreview = document.getElementById("removePreview");
  const placeholderIcon = document.getElementById("placeholderIcon");

  // Click container to open file dialog
  uploadContainer.addEventListener("click", () => fileInput.click());

  // Handle file selection / drag
  function handleFile(file) {
    if (!file.type.startsWith("image/")) return;
    const reader = new FileReader();
    reader.onload = function (e) {
      previewImg.src = e.target.result;
      previewImg.style.display = "block";
      removePreview.style.display = "block";
      placeholderIcon.style.display = "none";
    };
    reader.readAsDataURL(file);
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
  removePreview.addEventListener("click", (e) => {
    e.stopPropagation(); // prevent triggering file dialog
    fileInput.value = "";
    previewImg.src = "#";
    previewImg.style.display = "none";
    removePreview.style.display = "none";
    placeholderIcon.style.display = "block";
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
