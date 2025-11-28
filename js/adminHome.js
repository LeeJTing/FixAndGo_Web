$(document).ready(function () {
  // productsData comes from PHP above
  if (!productsData || productsData.length === 0) {
    $(".products-grid").html(
      "<p style='text-align:center; color:#64748b; padding:3rem;'>No products found.</p>"
    );
    return;
  }

  // Function to render products
  function renderProducts(filter = "") {
    $(".products-grid").empty();

    productsData.forEach((p) => {
      if (filter === "" || p.category_code === filter) {
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
                                <a href="product-update.php?id=${
                                  p.product_id
                                }" class="btn-update">
                                    Update
                                </a>

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
                            </div>
                        </div>
                    </div>
                `);
      }
    });
  }

  // Initial render
  renderProducts();

  // Filter on change
  $(".filter-select").on("change", function () {
    renderProducts($(this).val());
  });

  $(document).ready(function () {
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
});
