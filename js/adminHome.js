$(document).ready(function () {
  loadProduct();

  $("#searchInput").on("input", function () {
    var valueSearch = $(this).val();
    getSearchData(valueSearch);
  });

  // Filter on change
  $(".filter-select").on("change", function () {
    loadProduct($(this).val(), "", "");
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
function loadProduct(category, sortType, currentValue) {
  $.ajax({
    url: "../../controller/product-controller.php?function=allProduct",
    type: "GET",
    data: { category: category, sort: sortType, price: currentValue },
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
          const imagePath =
            "../../" + (p.file_path || "images/placeholder.jpg");
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
                    <a href="../pages/admin/product-update.php" class="btn-update">
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
  window.location.href = "../../controller/delete-product.php?id=" + id;
}

function getSearchData(value) {
  $.ajax({
    url: "../../controller/product-controller.php?function=Search",
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
        const imagePath = "../../" + (p.file_path || "images/placeholder.jpg");
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
                          <a href="product-detail.php?id=123" class="btn-update">
                              Update Details
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
/*
<form method="POST" action="../../controller/admin-controller.php" 
                                      onsubmit="return confirm('Delete ${p.product_name.replace(
                                        /'/g,
                                        "\\'"
                                      )}? This cannot be undone!');"
                                      style="display:inline;">
                                    <input type="hidden" name="product_id" value="${
                                      p.product_id
                                    }">
                                    <button type="submit" name="delete_product" class="btn-delete">
                                        Delete
                                    </button>
                                </form>
                                */
