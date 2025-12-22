let currentPage = 1;
let totalPages = 1;
const limit = 8; // products per page

$(document).ready(function () {
  let currentCategory = $("#category_filter").val() || "";
  let currentSort = "";
  let currentValue = $("#priceRange").val();

  if (currentCategory !== "") {
    $("#category_filter").val(currentCategory);
  }

  currentPage = 1;
  loadProduct(currentCategory, currentSort, currentValue, currentPage);

  $(document).on("click", ".page-btn[data-page]", function () {
    currentPage = $(this).data("page");
    loadProduct(currentCategory, currentSort, currentValue, currentPage);
  });

  $(document).on("click", ".page-btn.prev", function () {
    if (currentPage > 1) {
      currentPage--;
      loadProduct(currentCategory, currentSort, currentValue, currentPage);
    }
  });

  $(document).on("click", ".page-btn.next", function () {
    if (currentPage < totalPages) {
      currentPage++;
      loadProduct(currentCategory, currentSort, currentValue, currentPage);
    }
  });

  $("#searchInput").on("input", function () {
    getSearchData($(this).val());
  });

  $("#category_filter").on("change", function () {
    currentCategory = $(this).val();
    loadProduct(currentCategory, currentSort, currentValue);
  });

  $("#sortSelect").on("change", function () {
    currentSort = $(this).val();
    loadProduct(currentCategory, currentSort, currentValue);
  });

  let hasTouchedPrice = false;

  $("#priceRange").on("input", function () {
    let value = $(this).val();
    hasTouchedPrice = true;

    $("#priceValue").text("RM " + value);
    loadProduct(currentCategory, currentSort, value);
  });

  $(document).on("click", ".favorite-btn", function () {
    $(this).find("i").toggleClass("changeColor");
  });

  $(".clear-filters").on("click", function () {
    currentCategory = "";
    currentSort = "";
    currentValue = 500;
    currentPage = 1;

    $("#category_filter").prop("selectedIndex", 0);
    $("#sortSelect").prop("selectedIndex", 0);
    $("#priceRange").val(500);
    $("#priceValue").text("--");

    loadProduct("", "", currentValue, currentPage);
  });
});

function loadProduct(category, sortType, price, page = 1) {
  $.ajax({
    url: "../../controller/product-controller.php?function=allProduct",
    type: "GET",
    data: {
      category: category,
      sort: sortType,
      price: price,
      page: page,
      limit: limit,
    },
    dataType: "json",
    beforeSend: function () {
      $(".products-grid").html("<p>Loading...</p>");
    },
    success: function (res) {
      $(".products-grid").empty();
      $("#pagination").empty();

      if (res.data.length === 0) {
        $(".products-grid").html("<p>No products found.</p>");
        return;
      }

      // render products
      res.data.forEach((p) => {
        $(".products-grid").append(`
          <div class="product-card">
            <div class="product-image">
              <img src="../../${p.file_path}" alt="${
          p.alt_text
        }" loading="lazy">
            </div>
            <div class="product-info">
              <div class="product-category">${p.category_name}</div>
              <h3 class="product-title">${p.product_name}</h3>
              <div class="product-price">RM ${parseFloat(p.unit_price).toFixed(
                2
              )}</div>
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

      renderPagination(res.totalPages, page);
    },
  });
}

function renderPagination(pages, current) {
  totalPages = pages;

  $("#pagination").empty();

  for (let i = 1; i <= pages; i++) {
    $("#pagination").append(`
      <button class="page-btn ${i === current ? "active" : ""}" 
              data-page="${i}">
        ${i}
      </button>
    `);
  }

  // Enable / Disable prev & next
  $(".prev").prop("disabled", current === 1);
  $(".next").prop("disabled", current === pages);
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
