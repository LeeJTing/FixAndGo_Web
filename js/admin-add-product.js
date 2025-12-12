$(document).ready(function () {
  $(".add-product-form").on("submit", function (e) {
    e.preventDefault();
    const form = this; // store reference to the form

    let isValidProductName = validateField("product_name", {
      required: true,
      min: 10,
    });
    let isValidShort_desc = validateField("short_desc", {
      required: true,
      min: 10,
    });
    let isValidPrice = validateField("price", {
      required: true,
      decimal: true,
    });
    let isValidCategory = validateField("category_id", { required: true });
    let isValidStock = validateField("stock", {
      required: true,
      number: true,
      positive: true,
    });
    let isValidStatus = validateField("status", { required: true });
    let isValidPoint = validateField("point", {
      required: true,
      number: true,
      positive: true,
    });
    let isValidDescription = validateField("description", {
      required: true,
      min: 20,
    });
    let isValidFile = validateFileInput("#productImages", {
      types: ["image/jpeg", "image/png", "image/gif"],
      maxSize: 2 * 1024 * 1024,
    });

    if (
      !isValidProductName ||
      !isValidShort_desc ||
      !isValidPrice ||
      !isValidStock ||
      !isValidStatus ||
      !isValidCategory ||
      !isValidPoint ||
      !isValidDescription ||
      !isValidFile
    ) {
      return; // Stop submit
    }

    let mainImgSrc = $("#mainImg").attr("src");

    if (mainImgSrc.includes("dark_image.jpg")) {
      showConfirm(
        "No product image uploaded. The status of the product will be set to Inactive.",
        function (result) {
          if (result) {
            // User clicked Yes - allow submission
            $("select[name='status']").val("0"); // force inactive
            form.submit(); // submit the original form
          } else {
            // User clicked No - stay on page
            document.querySelector('input[name="product_images"]').focus();
          }
        }
      );
    } else {
      // If image is OK, submit normally
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
            $mainImg.attr("src", "../../images/no-image-dark.jpg");
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
