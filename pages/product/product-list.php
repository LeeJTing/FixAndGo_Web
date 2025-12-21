<link rel="stylesheet" href="../../css/product-list.css">
<link rel="stylesheet" href="../../css/msg.css">
<?php
require '../../_base.php';
$_title = "Fix & Go | Product List";
include '../../_head.php';
require '../../controller/product-controller.php';
$products = getProductList();
$items = getCountAllProduct();
$category = getAllCategory();
$selected_category = $_SESSION['selected_category'] ?? '';
displayFlashMessage();
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
                    <select class="filter-select" id="category_filter">
                        <option value="">All Categories</option>
                        <?php
                        foreach ($category as $c):
                        ?>
                            <option value="<?= $c->category_code ?>"
                                <?= ($c->category_code == $selected_category) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c->category_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <!-- Price Range -->
                <div class="filter-group">
                    <label>Price Range</label>
                    <div class="price-range">
                        <input type="range" min="0" max="500" value="50" id="priceRange">
                        <div class="price-values">
                            <span>RM 0</span> - <span id="priceValue">RM 50</span>
                        </div>
                    </div>
                </div>

                <!-- Sort By -->
                <div class="filter-group">
                    <label>Sort By</label>
                    <select class="filter-select" id="sortSelect">
                        <option value="">Featured</option>
                        <option value="LowtoHigh">Price: Low to High</option>
                        <option value="HightoLow">Price: High to Low</option>
                        <option value="newest">Newest First</option>
                    </select>
                </div>

                <!-- Clear Filters -->
                <button class="clear-filters">Clear All Filters</button>
            </aside>

            <!-- RIGHT: PRODUCTS GRID -->
            <section class="products-content">

                <div class="results-header">
                    <!-- Left: Title -->
                    <div class="results-info">
                        <h2>All Products</h2>
                    </div>
                    <!-- Right: Search Bar with Results Count Inside -->
                    <div class="search-with-count">
                        <div class="search-wrapper">
                            <div>
                                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                                <input type="text" id="searchInput" placeholder="Search products, SKU, code..." autocomplete="off">
                            </div>
                            <span class="results-count"><?= $items ?> items</span>
                        </div>
                    </div>
                </div>

                <div class="products-grid">
                </div>
            </section>
        </div>
    </main>

</body>
<script src="../../js/product-list.js"></script>
<?php include "../../_foot.php" ?>