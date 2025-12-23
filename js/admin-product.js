$(document).ready(function () {
  $("#searchInput").on("input", function () {
    var valueSearch = $(this).val();
    getSearchData(valueSearch);
  });
  loadProduct();

  $(".filter-select").on("change", function () {
    filterCategory($(this).val(), "", "");
  });

  $("#mobileMenuToggle").click(function () {
    $("#mobileDropdown").toggleClass("active");
    $(this).find("i").toggleClass("fa-bars fa-xmark");
  });

  // Close menu when clicking outside
  $(document).click(function (e) {
    if (!$(e.target).closest(".container").length) {
      $("#mobileDropdown").removeClass("active");
      $("#mobileMenuToggle i").removeClass("fa-xmark").addClass("fa-bars");
    }
  });

  // Optional: Add scroll effect
  $(window).scroll(function () {
    if ($(this).scrollTop() > 50) {
      $("header").addClass("scrolled");
    } else {
      $("header").removeClass("scrolled");
    }
  });
});

function filterCategory(category, sortType, currentValue) {
  $.ajax({
    url: "../../controller/admin-controller.php",
    type: "GET",
    data: {
      function: "allProduct",
      category: category,
      sort: sortType,
      price: currentValue,
    },
    dataType: "json",
    cache: false,
    beforeSend: function () {
      $(".products-grid").html("<p>Loading...</p>");
    },
    success: function (data) {
      $(".products-grid").empty();

      if (Array.isArray(data) && data.length > 0) {
        data.forEach(function (p) {
          const altText = p.alt || p.product_name;
          const imagePath = p.file_path
            ? "../../" + p.file_path.replace(/^\/+/, "")
            : "../../images/no-image.jpg";

          $(".products-grid").append(`
            <div class="product-card">
              <div class="product-image">
                <img src="${imagePath}" alt="${altText}" loading="lazy">
              </div>

              <div class="product-info">
                <div class="product-category">
                  ${p.category_name} <small>(${p.category_code})</small>
                </div>

                <h3 class="product-title">${p.product_name}</h3>

                <div class="product-price">
                  RM ${parseFloat(p.unit_price).toFixed(2)}
                </div>

                <div class="product-actions">
                  <a href="../admin/admin-product-update.php?id=${
                    p.product_id
                  }" class="btn-update">
                    Update Details
                  </a>
                  <button class="btn-delete" onclick="deleteItem(${
                    p.product_id
                  })">
                    Delete
                  </button>
                </div>
              </div>
            </div>
          `);
        });
      } else {
        $(".products-grid").html("<p>No products found.</p>");
      }
    },
    error: function (xhr) {
      console.error(xhr.responseText);
      $(".products-grid").html("<p>Error loading products.</p>");
    },
  });
}

function loadProduct() {
  $.ajax({
    url: "../../controller/admin-controller.php?function=allProductAdmin",
    type: "GET",
    dataType: "json",
    cache: false,
    beforeSend: function () {
      $(".products-grid").html("<p>Loading...</p>");
    },
    success: function (data) {
      $(".products-grid").empty();
      if (data.length > 1) {
        data.forEach(function (p) {
          const altText = p.alt_text || p.product_name;
          const imagePath = "../../" + (p.file_path || "images/no-image.jpg");
          $(".products-grid").append(`
             <div class="product-card">
                        <div class="product-image">
                            <img src="${imagePath}" alt="${altText}" loading="lazy">
                        </div>

                        <div class="product-info">
                            <div class="product-category">
                                ${
                                  p.category_name
                                } <small>(${p.category_code})</small>
                            </div>

                            <h3 class="product-title">${p.product_name}</h3>

                            <div class="product-price">
                                RM ${parseFloat(p.unit_price).toFixed(2)}
                            </div>

                <div class="product-actions">
                    <a href="../admin/admin-product-update.php?id=${
                      p.product_id
                    }" class="btn-update">
                        Update Details
                    </a>
                    <button class="btn-delete" onclick="deleteItem(${
                      p.product_id
                    })">
                        Delete
                    </button>
                </div>
            </div>
        `);
        });
      } else {
        $(".products-grid").html("<p>No products found.</p>");
      }
    },
  });
}

function deleteItem(id) {
  showConfirm(
    "Are you sure you want to delete this item?",
    function (confirmed) {
      if (confirmed) {
        // Do delete action
        window.location.href =
          "../../controller/admin-controller.php?function=delete&id=" + id;
      } else {
        console.log("User cancelled delete.");
      }
    }
  );
}

function getSearchData(value) {
  $.ajax({
    url: "../../controller/admin-controller.php?function=SearchAdmin",
    type: "GET",
    data: { search: value },
    dataType: "json",
    cache: false,
    beforeSend: function () {
      $(".products-grid").html("<p>Loading...</p>");
    },
    success: function (data) {
      $(".products-grid").empty();

      data.forEach((p) => {
        const altText = p.alt_text || p.product_name;
        const imagePath = "../../" + (p.file_path || "images/no-image.jpg");
        $(".products-grid").append(`
             <div class="product-card">
                        <div class="product-image">
                            <img src="${imagePath}" alt="${altText}" loading="lazy">
                        </div>

                        <div class="product-info">
                            <div class="product-category">
                                ${p.category_name} <small>(${
          p.category_code
        })</small>
                            </div>

                            <h3 class="product-title">${p.product_name}</h3>

                            <div class="product-price">
                                RM ${parseFloat(p.unit_price).toFixed(2)}
                            </div>

                <div class="product-actions">
                    <a href="../admin/admin-product-update.php?id=${
                      p.product_id
                    }" class="btn-update">
                        Update Details
                    </a>
                    <button class="btn-delete" onclick="deleteItem(${
                      p.product_id
                    })">
                        Delete
                    </button>
                </div>
            </div>
        `);
      });
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", status, error);
      console.log("Server Response:", xhr.responseText);
      $(".products-grid").html(
        "<p style='color:red'>Error retrieving data. Check Console (F12).</p>"
      );
    },
  });
}
