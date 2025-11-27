<link rel="stylesheet" href="../../css/product-list.css">
<?php
include '../../_head.php';
require '../../DAO/product_dao.php';
$products = getProductForDisplay();
$items = getCountAllProduct();
?>

<body>
    <main class="main-container products-page">
        <!-- TWO-COLUMN LAYOUT -->
        <div class="products-layout">

            <!-- LEFT: FILTERS SIDEBAR -->
            <aside class="filters-sidebar">
                <h3 class="filter-title">Filters</h3>
                <!-- Category Filter -->
                <div class="filter-group">
                    <label>Category</label>
                    <select class="filter-select">
                        <option>All Categories</option>
                        <option>Hand Tools</option>
                        <option>Power Tools</option>
                        <option>Measuring Tools</option>
                        <option>Safety Gear</option>
                        <option>Accessories</option>
                    </select>
                </div>

                <!-- Price Range -->
                <div class="filter-group">
                    <label>Price Range</label>
                    <div class="price-range">
                        <input type="range" min="0" max="500" value="500" id="priceRange">
                        <div class="price-values">
                            <span>$0</span> - <span id="priceValue">$500</span>
                        </div>
                    </div>
                </div>

                <!-- Sort By -->
                <div class="filter-group">
                    <label>Sort By</label>
                    <select class="filter-select" id="sortSelect">
                        <option>Featured</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                        <option>Newest First</option>
                        <option>Best Selling</option>
                    </select>
                </div>

                <!-- Clear Filters -->
                <button class="clear-filters">Clear All Filters</button>
            </aside>

            <!-- RIGHT: PRODUCTS GRID -->
            <section class="products-content">
                <div class="results-header">
                    <h2>All Products</h2>
                    <span class="results-count"><?= $items ?></span>
                </div>

                <div class="products-grid">
                    <?php foreach ($products as $p): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <button class="favorite-btn" aria-label="Add to wishlist" title="Add to wishlist">
                                    <i class="fa-regular fa-heart"></i>
                                </button>
                                <img src="../../<?= $p->file_path ?>"
                                    alt="<?= htmlspecialchars($p->alt ?? $p->product_name) ?>"
                                    loading="lazy"
                                    width="200">
                            </div>

                            <div class="product-info">
                                <div class="product-category">
                                    <?= htmlspecialchars($p->category_name) ?>
                                </div>

                                <h3 class="product-title">
                                    <?= htmlspecialchars($p->product_name) ?>
                                </h3>

                                <div class="product-price">
                                    RM <?= number_format($p->unit_price, 2) ?>
                                </div>

                                <div class="product-actions">
                                    <a href="product_detail.php?id=<?= $p->product_id ?>" class="btn-view">
                                        View
                                    </a>
                                    <button class="btn-cart add-to-cart"
                                        data-id="<?= $p->product_id ?>"
                                        data-name="<?= htmlspecialchars($p->product_name) ?>"
                                        data-price="<?= $p->unit_price ?>"
                                        data-image="<?= $imagePath ?>">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>
    <script>
        // Your existing jQuery cart code goes here (same as before)
        $(document).ready(function() {
            // Add to cart from listing page
            $(".add-to-cart").on("click", function() {
                const name = $(this).data("name");
                const price = parseFloat($(this).data("price"));
                // Reuse your existing addToCart function
                addToCart(name, price);
            });

            // Search filter
            $("#searchInput").on("keyup", function() {
                const value = $(this).val().toLowerCase();
                $(".product-card").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });

        $(document).on("click", ".favorite-btn", function() {
            $(this).find("i").toggleClass("changeColor");
        });
    </script>
    <?php include "../../_foot.php" ?>

    </html>