<?php include '../../_head.php'; ?>
<link rel="stylesheet" href="<?= $rootDir ?>/css/product-list.css">
<body>
    <main class="main-container">
        <h1 class="page-title">All Products</h1>
        <p class="page-subtitle">Explore our complete collection of professional tools</p>

        <!-- Filters & Search -->
        <div class="filters-bar">
            <input type="text" class="search-box" placeholder="Search products..." id="searchInput">
            <select class="sort-select">
                <option>Sort by: Featured</option>
                <option>Price: Low to High</option>
                <option>Price: High to Low</option>
                <option>Newest First</option>
            </select>
        </div>

        <!-- Products Grid -->
        <div class="products-grid">
            <div class="product-card">
                <div class="product-image">
                    <button class="favorite-btn" aria-label="Add to wishlist">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                    <img src="../../images/product/(MR.DIY)_2-Layers_Stainless_Steel_Storage_Rack_for_Bathroom-1.png" alt="Hammer">
                </div>
                <div class="product-info">
                    <div class="product-category">Hand Tools</div>
                    <h3 class="product-title">Professional Claw Hammer 16oz</h3>
                    <div class="product-price">$24.99</div>
                    <div class="product-actions">
                        <button class="btn-view" onclick="location.href='product-detail.php?id=2'">View</button>
                        <button class="btn-cart add-to-cart" data-name="Claw Hammer" data-price="24.99">cart</button>
                    </div>
                </div>
            </div>

            <!-- Add more cards here -->
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