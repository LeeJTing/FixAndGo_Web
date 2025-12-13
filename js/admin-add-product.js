$(document).ready(function () {
  let existingProductNames = [];
  $.getJSON(
    "../../controller/admin-controller.php?function=getProductName",
    function (data) {
      if (data.status === "success") {
        existingProductNames = data.products.map((name) => name.toLowerCase());
      } else {
        console.error("Failed to fetch product names:", data.message);
      }
    }
  );

  $(".add-product-form").on("submit", function (e) {
    e.preventDefault();
    const form = this;
    let productNameValue = $("input[name=product_name]").val().trim();
    let isValidProductName = true;

    if (existingProductNames.includes(productNameValue.toLowerCase())) {
      showError(
        $("input[name=product_name]"),
        "This product name already exists."
      );
      return; // Stop here
    }

    isValidProductName = validateField("product_name", {
      required: true,
      min: 10,
    });

    if (!isValidProductName) {
      return;
    }

    let status = $("select[name=status]").val();
    if (status === "active") {
      let isValid = true;

      isValid =
        validateField("short_desc", { required: true, min: 10 }) && isValid;
      isValid =
        validateField("price", {
          required: true,
          decimal: true,
          positive: true,
        }) && isValid;
      isValid = validateField("category_id", { required: true }) && isValid;
      isValid =
        validateField("stock", {
          required: true,
          number: true,
          positive: true,
        }) && isValid;
      isValid =
        validateField("point", {
          required: true,
          number: true,
          positive: true,
          minPrice: "price",
        }) && isValid;
      isValid =
        validateField("description", { required: true, min: 20 }) && isValid;
      isValid =
        validateField("lowstock", {
          required: true,
          positive: true,
          number: true,
          Mimstock: true,
        }) && isValid;
      isValid =
        validateFileInput("#productImages", {
          types: ["image/jpeg", "image/png", "image/gif"],
          maxSize: 2 * 1024 * 1024,
        }) && isValid;

      if (!isValid) {
        return; // Stop here if validation fails
      }
    }

    // Step 4: Check if product image exists
    let mainImgSrc = $("#mainImg").attr("src");

    if (
      mainImgSrc.includes("dark_image.jpg") ||
      mainImgSrc.includes("no-image.jpg")
    ) {
      // Only show warning if user tried to set status as active
      if (status === "active") {
        showConfirm(
          "No product image uploaded. The status of the product will be set to Inactive. Continue?",
          function (result) {
            if (result) {
              // User clicked Yes - force inactive and submit
              $("select[name='status']").val("inactive");
              form.submit();
            } else {
              // User clicked No - focus on file input
              document.querySelector('input[name="product_images"]').focus();
            }
          }
        );
      } else {
        window.location.href =
          "../controller/admin-controller.php?function=createTemporary";
        // Status is already inactive, just submit
        //form.submit();
      }
    } else {
      // Image exists, submit normally
      form.submit();
    }
  });
});

let arrayImage = []; // Store all selected files

// File input change
$("#productImages").on("change", function (e) {
  const files = Array.from(this.files);
  if (!files.length) return;

  const $mainImg = $("#mainImg");
  const $gallery = $("#thumbnailGallery");
  const $overlay = $(".main-image-preview .upload-overlay");

  // Add new files to arrayImage
  arrayImage = arrayImage.concat(files);

  // Limit to max 8 images
  arrayImage = arrayImage.slice(0, 8);

  // Clear gallery except "Add More"
  $gallery.html(
    '<div class="thumbnail-item add-more"><i class="fa-solid fa-plus"></i><span>Add More</span></div>'
  );

  arrayImage.forEach((file, i) => {
    const reader = new FileReader();
    reader.onload = function (ev) {
      if (i === 0) {
        // Show main image preview
        $mainImg.attr("src", ev.target.result);
        $overlay.hide();
      }

      // Create thumbnail
      const $thumb = $(`
        <div class="thumbnail-item">
          <img src="${ev.target.result}" alt="thumb">
          <button type="button" class="remove-thumb">×</button>
        </div>
      `);

      // Insert thumbnail before "Add More"
      $gallery.find(".add-more").before($thumb);

      // Remove thumbnail click
      $thumb.find(".remove-thumb").on("click", function () {
        arrayImage.splice(i, 1); // remove from array
        $thumb.remove();

        // Reset main image if first removed
        if (i === 0) {
          if (arrayImage.length > 0) {
            const newReader = new FileReader();
            newReader.onload = function (e2) {
              $mainImg.attr("src", e2.target.result);
            };
            newReader.readAsDataURL(arrayImage[0]);
          } else {
            $mainImg.attr("src", "../../images/no-image.jpg");
            $overlay.show();
          }
        }
      });
    };
    reader.readAsDataURL(file);
  });
});

// Click anywhere in upload area to trigger file input
$(".main-image-preview, #thumbnailGallery").on("click", function (e) {
  if ($(e.target).closest(".add-more, .main-image-preview").length) {
    $("#productImages").click();
  }
});
