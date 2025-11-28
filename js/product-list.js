$(document).ready(function () {
  let data = productsData; // Use data from PHP

  // Initial load
  renderProducts(data);

  $(".filter-select").on("change", function () {
    let selected = $(this).val();
    let filtered = data.filter(
      (p) => selected === "" || p.category_code == selected
    );
    renderProducts(filtered);
  });

  $(document).on("click", ".favorite-btn", function () {
    $(this).find("i").toggleClass("changeColor");
  });
});

function renderProducts(list) {
  $(".products-grid").empty();

  list.forEach(function (p) {
    let altText = p.alt_text || p.product_name;
    let imagePath = "../../" + p.file_path;

    $(".products-grid").append(`
            <div class="product-card">
                <div class="product-image">
                    <button class="favorite-btn" aria-label="Add to wishlist" title="Add to wishlist">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                    <img src="${imagePath}" alt="${altText}" loading="lazy" width="200">
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
                            data-image="${imagePath}">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </button>
                    </div>
                </div>
            </div>
        `);
  });
}
