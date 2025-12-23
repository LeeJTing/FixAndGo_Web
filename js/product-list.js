let currentPage = 1;
let currentSearch = "";
let currentCategory = "";
let currentSort = "";
let currentValue = 500;
let totalPages = 1;
const limit = 8;

$(document).ready(function () {
  // Initialize filters
  currentCategory = $("#category_filter").val() || "";
  currentValue = $("#priceRange").val();

  // Load initial products
  loadProducts();

  // --------------------
  // Wishlist toggle
  // --------------------
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

  // --------------------
  // Pagination click
  // --------------------
  $(document).on("click", ".page-btn[data-page]", function () {
    currentPage = $(this).data("page");
    loadProducts();
  });
  $(".prev").on("click", function () {
    if (currentPage > 1) {
      currentPage--;
      loadProducts();
    }
  });
  $(".next").on("click", function () {
    if (currentPage < totalPages) {
      currentPage++;
      loadProducts();
    }
  });

  // --------------------
  // Filters
  // --------------------
  $("#category_filter").on("change", function () {
    currentCategory = $(this).val();
    currentPage = 1;
    loadProducts();
  });

  $("#sortSelect").on("change", function () {
    currentSort = $(this).val();
    currentPage = 1;
    loadProducts();
  });

  $("#priceRange").on("input", function () {
    currentValue = $(this).val();
    $("#priceValue").text("RM " + currentValue);
    currentPage = 1;
    loadProducts();
  });

  // --------------------
  // Search
  // --------------------
  $("#searchInput").on("keyup", function () {
    currentSearch = $(this).val().trim();
    currentPage = 1;
    loadProducts();
  });

  // --------------------
  // Clear filters
  // --------------------
  $(".clear-filters").on("click", function () {
    currentCategory = "";
    currentSort = "";
    currentValue = 500;
    currentSearch = "";
    currentPage = 1;

    $("#category_filter").prop("selectedIndex", 0);
    $("#sortSelect").prop("selectedIndex", 0);
    $("#priceRange").val(500);
    $("#priceValue").text("--");
    $("#searchInput").val("");

    loadProducts();
  });
});

// --------------------
// Unified load function
// --------------------
function loadProducts() {
  $.ajax({
    url: "../../controller/product-controller.php?function=allProduct",
    type: "GET",
    data: {
      category: currentCategory,
      sort: currentSort,
      price: currentValue,
      search: currentSearch,
      page: currentPage,
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

      // ❌ No results
      if (!res.data || res.data.length === 0) {
        $(".products-grid").html("<p>No products found.</p>");
        $(".pagination-wrapper").hide();
        return;
      }

      // ✅ Render products
      res.data.forEach((p) => {
        const altText = p.alt || p.product_name;
        const imagePath = "../../" + (p.file_path || "images/placeholder.jpg");
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
              <img src="${imagePath}" alt="${altText}" loading="lazy">
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
                <button class="btn-cart add-to-cart" data-id="${
                  p.product_id
                }" data-name="${p.product_name}" data-price="${
          p.unit_price
        }" data-image="${imagePath}">
                  <i class="fa-solid fa-cart-shopping"></i>
                </button>
              </div>
            </div>
          </div>
        `);
      });

      // ✅ Render pagination
      if (res.totalPages && res.totalPages > 1) {
        renderPagination(res.totalPages, currentPage);
        $(".pagination-wrapper").show();
      } else {
        $(".pagination-wrapper").hide();
      }
    },
    error: function (xhr, status, error) {
      console.error("Load failed:", { status, error, xhr });
      $(".products-grid").html(
        "<p>Failed to load products. Please refresh.</p>"
      );
      $(".pagination-wrapper").hide();
    },
  });
}

// --------------------
// Pagination renderer
// --------------------
function renderPagination(pages, current) {
  totalPages = pages;
  $("#pagination").empty();

  for (let i = 1; i <= pages; i++) {
    $("#pagination").append(`
      <button class="page-btn ${
        i === current ? "active" : ""
      }" data-page="${i}">${i}</button>
    `);
  }

  $(".prev").prop("disabled", current === 1);
  $(".next").prop("disabled", current === pages);
}
