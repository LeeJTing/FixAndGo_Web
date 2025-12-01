<link rel="stylesheet" href="../../css/product-list.css">
<?php
require '../../_base.php';
$_title = "Fix & Go | Product";
include '../../_head.php';
require '../../DAO/product_dao.php';
$products = getProductForDisplay();
$items = getCountAllProduct();
$category = getAllCategory();
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
                        <option value="">All Categories</option>
                        <?php foreach ($category as $c): ?>
                            <option value="<?= $c->category_code ?>">
                                <?= htmlspecialchars($c->category_name) ?>
                            </option>
                        <?php endforeach; ?>
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
                    <span class="results-count"><?= $items ?> items</span>
                </div>

                <div class="products-grid">
                </div>
            </section>
        </div>
    </main>

</body>
<script>
    var productsData = <?= json_encode($products, JSON_PRETTY_PRINT); ?>;
</script>
<script src="../../js/product-list.js"></script>
<?php include "../../_foot.php" ?>