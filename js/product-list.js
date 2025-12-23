let currentPage = 1;
let currentSearch = "";
let currentCategory = "";
let currentSort = "";
let currentValue = 500;
let totalPages = 1;

const limit = 8;

$(document).ready(function () {
  currentCategory = $("#category_filter").val() || "";
  currentValue = $("#priceRange").val();

  // Wishlist toggle
  $(document).on("click", ".favorite-btn", function (e) {
    e.preventDefault();
    e.stopPropagation();

    const $btn = $(this);
    const productId = parseInt($btn.data("product-id"), 10);
    if (!productId) return;

    $.post(
      "../../AJAX/toggle_wishlist.php",
      { toggle_wishlist: true, product_id: productId },
      function (res) {
        if (res && res.success) {
          $btn.find("i").toggleClass("changeColor", !!res.in_wishlist);
        } else if (res && res.error === "LOGIN_REQUIRED") {
          alert(res.message || "Please login to use Wishlist.");
          window.location.href = "../../pages/guest/login.php";
        } else {
          alert((res && res.error) || "Wishlist update failed.");
        }
      },
      "json"
    ).fail(function () {
      alert("Network error. Please try again.");
    });
  });

  loadProduct(currentCategory, currentSort, currentValue, 1);

  // 🔁 Pagination buttons
  $(document).on("click", ".page-btn[data-page]", function () {
    const page = $(this).data("page");
    currentPage = page;

    if (currentSearch) {
      loadSearchData(currentSearch, page);
    } else {
      loadProduct(currentCategory, currentSort, currentValue, page);
    }
  });

  $(".prev").on("click", function () {
    if (currentPage > 1) {
      currentPage--;
      triggerLoad();
    }
  });

  $(".next").on("click", function () {
    if (currentPage < totalPages) {
      currentPage++;
      triggerLoad();
    }
  });

  // 🎯 Filters
  $("#category_filter").on("change", function () {
    currentCategory = $(this).val();
    resetSearch();
    loadProduct(currentCategory, currentSort, currentValue, 1);
  });

  $("#sortSelect").on("change", function () {
    currentSort = $(this).val();
    resetSearch();
    loadProduct(currentCategory, currentSort, currentValue, 1);
  });

  $("#priceRange").on("input", function () {
    currentValue = $(this).val();
    $("#priceValue").text("RM " + currentValue);
    resetSearch();
    loadProduct(currentCategory, currentSort, currentValue, 1);
  });

  // 🔍 Search
  $("#searchInput").on("keyup", function () {
    const value = $(this).val().trim();
    currentPage = 1;

    if (value) {
      currentSearch = value;
      loadSearchData(value, 1);
    } else {
      resetSearch();
      loadProduct(currentCategory, currentSort, currentValue, 1);
    }
  });

  // 🧹 Clear filters
  $(".clear-filters").on("click", function () {
    resetSearch();
    currentCategory = "";
    currentSort = "";
    currentValue = 500;

    $("#category_filter").prop("selectedIndex", 0);
    $("#sortSelect").prop("selectedIndex", 0);
    $("#priceRange").val(500);
    $("#priceValue").text("--");

    loadProduct("", "", currentValue, 1);
  });
});
function triggerLoad() {
  if (currentSearch) {
    loadSearchData(currentSearch, currentPage);
  } else {
    loadProduct(currentCategory, currentSort, currentValue, currentPage);
  }
}

function resetSearch() {
  currentSearch = "";
  $("#searchInput").val("");
}
function renderPagination(pages, current) {
  totalPages = pages;
  $("#pagination").empty();

  for (let i = 1; i <= pages; i++) {
    $("#pagination").append(`
      <button class="page-btn ${
        i === current ? "active" : ""
      }" data-page="${i}">
        ${i}
      </button>
    `);
  }

  $(".prev").prop("disabled", current === 1);
  $(".next").prop("disabled", current === pages);
}
function loadSearchData(keyword, page = 1) {
  currentSearch = keyword;

  $.ajax({
    url: "../../controller/product-controller.php?function=Search",
    type: "GET",
    data: {
      search: keyword,
      page: page,
      limit: limit,
    },
    dataType: "json",
    beforeSend: function () {
      $(".products-grid").html("<p>Loading...</p>");
      $(".pagination-wrapper").hide();
    },
    success: function (res) {
      $(".products-grid").empty();
      $("#pagination").empty();

      // ❌ No result
      if (!res.data || res.data.length === 0) {
        $(".products-grid").html("<p>No products found.</p>");
        $(".pagination-wrapper").hide();
        return;
      }

      // ✅ Render products
      res.data.forEach((p) => {
        $(".products-grid").append(`
          <div class="product-card">
            <div class="product-image">
              <button class="favorite-btn" type="button" data-product-id="${
                p.product_id
              }" aria-label="Add to Wishlist">
                <i class="fa-regular fa-heart ${
                  p.is_wishlisted ? "changeColor" : ""
                }"></i>
              </button>
              <img src="../../${p.file_path}" 
                   alt="${p.alt || p.product_name}" 
                   loading="lazy">
            </div>
            <div class="product-info">
              <div class="product-category">${p.category_name}</div>
              <h3 class="product-title">${p.product_name}</h3>
              <div class="product-price">
                RM ${parseFloat(p.unit_price).toFixed(2)}
              </div>
              <div class="product-actions">
                <a href="product-detail.php?id=${
                  p.product_id
                }" class="btn-view">
                  View
                </a>
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

      // ✅ Pagination
      if (res.totalPages > 1) {
        renderPagination(res.totalPages, page);
        $(".pagination-wrapper").show();
      }
    },
    error: function (xhr, status, error) {
      console.error("Search load failed:", { status, error, xhr });
      $(".products-grid").html(
        "<p>Failed to load products. Please refresh the page.</p>"
      );
      $(".pagination-wrapper").hide();
    },
  });
}
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
      $(".pagination-wrapper").hide(); // hide first
    },
    success: function (res) {
      $(".products-grid").empty();
      $("#pagination").empty();

      // ✅ No products
      if (!res.data || res.data.length === 0) {
        $(".products-grid").html("<p>No products found.</p>");
        $(".pagination-wrapper").hide();
        return;
      }

      // render products
      res.data.forEach((p) => {
        $(".products-grid").append(`
          <div class="product-card">
            <div class="product-image">
              <button class="favorite-btn" type="button" data-product-id="${
                p.product_id
              }" aria-label="Add to Wishlist">
                <i class="fa-regular fa-heart ${
                  p.is_wishlisted ? "changeColor" : ""
                }"></i>
              </button>
              <img src="../../${p.file_path}" 
                   alt="${p.alt || p.product_name}" 
                   loading="lazy">
            </div>
            <div class="product-info">
              <div class="product-category">${p.category_name}</div>
              <h3 class="product-title">${p.product_name}</h3>
              <div class="product-price">
                RM ${parseFloat(p.unit_price).toFixed(2)}
              </div>
              <div class="product-actions">
                <a href="product-detail.php?id=${
                  p.product_id
                }" class="btn-view">
                  View
                </a>
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

      // ✅ Pagination only if more than 1 page
      if (res.totalPages && res.totalPages > 1) {
        renderPagination(res.totalPages, page);
        $(".pagination-wrapper").show();
      } else {
        $(".pagination-wrapper").hide();
      }
    },
    error: function (xhr, status, error) {
      console.error("Product list load failed:", { status, error, xhr });
      $(".products-grid").html(
        "<p>Failed to load products. Please refresh the page.</p>"
      );
      $(".pagination-wrapper").hide();
    },
  });
}
