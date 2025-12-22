$(document).ready(function () {
  // $("select[name='status']").on("change", function () {
  //   if ($(this).val() === "inactive") {
  //     $("select[name='category_code']").val(5);
  //   }
  // });

  $("#updateProductForm").on("submit", async function (e) {
    e.preventDefault();
    const form = this;
    let product_id = Number($("#productId").val()) | 0;
    let productNameValue = $("input[name=product_name]")
      .val()
      .trim()
      .toLowerCase();
    let isValidProductName = true;
    let status = $("select[name=status]").val();
    let price = parseFloat($("input[name=unit_price]").val()) || 0;
    let point = parseFloat($("input[name=product_point]").val()) || 0;
    let stock = parseInt($("input[name=stock_quantity]").val(), 10) || 0;
    let lowStock = parseInt($("input[name=low_stock]").val(), 10) || 0;

    let existingProductNames = await fetchExistingProductNames(product_id);
    let mainImgSrc = $("#mainImg").first().attr("src") || "";

    existingProductNames = existingProductNames.map((name) =>
      name.toLowerCase().trim()
    );
    let currentName = productNameValue.toLowerCase().trim();
    if (existingProductNames.includes(currentName)) {
      showError(
        $("input[name=product_name]"),
        "This product name already exists."
      );

      return;
    }
    isValidProductName = validateField("product_name", {
      required: true,
      min: 10,
    });

    if (!isValidProductName) {
      return;
    }
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
              $("select[name='category_code']").val(5);
              form.submit();
            } else {
              // User clicked No - focus on file input
              document.querySelector('input[name="new_images"]').focus();
            }
          }
        );
      } else {
        form.submit();
      }
    } else {
      let isValid = true;

      isValid =
        validateField("short_desc", { required: true, min: 10 }) && isValid;
      isValid =
        validateField("unit_price", {
          required: true,
          decimal: true,
          positive: true,
        }) && isValid;
      isValid = validateField("category_code", { required: true }) && isValid;
      isValid =
        validateField("stock_quantity", {
          required: true,
          number: true,
          positive: true,
        }) && isValid;
      isValid =
        validateField("sold_number", {
          required: true,
          number: true,
          positive: true,
        }) && isValid;
      isValid =
        validateField("product_point", {
          required: true,
          number: true,
          positive: true,
          minPrice: "price",
        }) && isValid;
      isValid =
        validateField("description", { required: true, min: 20 }) && isValid;
      isValid =
        validateField("low_stock", {
          required: true,
          positive: true,
          number: true,
          Mimstock: true,
        }) && isValid;

      if (!isValid) {
        return; // Stop here if validation fails
      }
      const statusSelect = $("select[name=status]").val();
      const categorySelect = $("select[name=category_code]");
      if (statusSelect != "inactive") {
        if (categorySelect.val() === "5") {
          showError(
            categorySelect,
            "Please select a valid category for the product."
          );
          return false;
        }
      }

      if (point > price) {
        showError(
          $("input[name=product_point]"),
          "The Product Point cannot be greater than Product Price."
        );
        return false; // stop further processing
      }
      if (lowStock > stock) {
        showError(
          $("input[name=low_stock]"),
          "The Low Stock Handling Value cannot greater than stock quantity."
        );
        return false; // stop further processing
      }

      if (statusSelect === "active") {
        if (!hasValidProductImage()) {
          $("select[name='status']").val("inactive");
          $("select[name='category_code']").val(5);

          showConfirm(
            "No product image detected. The product will be set to Inactive. Continue?",
            function (result) {
              if (result) {
                form.submit();
              } else {
                $("#newImagesInput").focus();
              }
            }
          );
          return;
        }
      }

      form.submit();
    }
  });
});

let newFiles = [];
$(document).ready(function () {
  var productId = $("#productId").val();

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
// Function to update input element with newFiles
function updateInputFiles() {
  const dataTransfer = new DataTransfer();
  newFiles.forEach((file) => dataTransfer.items.add(file));
  $("#newImagesInput")[0].files = dataTransfer.files;
  console.log("Current new_images[] files:", $("#newImagesInput")[0].files);
}

function fetchExistingProductNames(id) {
  return $.getJSON(
    "../../controller/admin-controller.php?function=getProductName&id=" + id
  )
    .then(function (response) {
      if (response.status === "success") {
        return response.data; // ✅ array of names
      } else {
        console.error("Failed:", response.message);
        return [];
      }
    })
    .catch(function (error) {
      console.error("AJAX Error:", error);
      return [];
    });
}

function hasValidProductImage() {
  // 1️⃣ Existing preview image
  let mainImg = $("#mainImg");
  let hasExistingImage =
    mainImg.length > 0 &&
    !mainImg.attr("src").includes("dark_image.jpg") &&
    !mainImg.attr("src").includes("no-image.jpg");

  // 2️⃣ New uploaded images
  let hasNewImages = $("#newImagesInput")[0].files.length > 0;

  // ✅ Valid if either exists
  return hasExistingImage || hasNewImages;
}
