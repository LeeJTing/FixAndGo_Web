$(document).ready(function () {
  let currentCategory = "";
  let currentSort = "";
  let currentValue = $("#priceRange").val();

  loadProduct(currentCategory, currentSort, currentValue);

  $("#category_filter").on("change", function () {
    currentCategory = $(this).val();
    loadProduct(currentCategory, currentSort, currentValue);
  });

  $("#sortSelect").on("change", function () {
    currentSort = $(this).val();
    loadProduct(currentCategory, currentSort, currentValue);
  });

  $("#priceRange").on("input", function () {
    let value = $(this).val();
    currentValue = $(this).val();
    $("#priceValue").text("RM" + value);
    loadProduct(currentCategory, currentSort, currentValue);
  });

  $(document).on("click", ".favorite-btn", function () {
    $(this).find("i").toggleClass("changeColor");
  });

  $(document).on("click", ".clear-filters", function () {
    $("#category_filter").prop("selectedIndex", 0);
    $("#sortSelect").prop("selectedIndex", 0);
    $("#priceRange").val(50);
    $("#priceValue").text("RM" + 50);
    currentCategory = "";
    currentSort = "";
    currentValue = 50;
    loadProduct("", "", 50);
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
          $(".products-grid").append(`
            <div class="product-card">
                <div class="product-image">
                    <button class="favorite-btn" aria-label="Add to wishlist" title="Add to wishlist">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                    <img src="../../${
                      p.file_path
                    }" alt="${p.alt_text}" loading="lazy" width="200">
                </div>
                <div class="product-info">
                    <div class="product-category">${p.category_name}</div>
                    <h3 class="product-title">${p.product_name}</h3>
                    <div class="product-price">RM ${parseFloat(
                      p.unit_price
                    ).toFixed(2)}</div>
                    <div class="product-actions">
                        <a href="product-detail.php?id=${
                          p.product_id
                        }" class="btn-view">View</a>
                        <button class="btn-cart add-to-cart" 
                            data-id="${p.product_id}" 
                            data-name="${p.product_name}" 
                            data-price="${p.unit_price}" 
                            data-image="../../${p.file_path}">
                            <i class="fa-solid fa-cart-shopping"></i>
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
  });
}
