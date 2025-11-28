<?php
include '_head.php';
require '../../controller/admin-controller.php';
$items = getCountAllProduct();
$products = getAdminProducts();
?>

<link rel="stylesheet" href="../../css/adminHome.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<body>
    <main class="main-container products-page">
        <div class="products-layout">
            <section class="products-content">

                <!-- SEARCH + FILTER BAR (Beautiful & Responsive) -->
                <div class="search-filter-bar">
                    <div class="search-wrapper">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" id="searchInput" placeholder="Search products, tools, spare parts..." autocomplete="off">
                    </div>

                    <select id="categoryFilter" class="filter-select">
                        <option value="">All Categories</option>
                        <?php foreach ($category as $c): ?>
                            <option value="<?= htmlspecialchars($c->category_code) ?>">
                                <?= htmlspecialchars($c->category_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Results Counter -->
                <div class="results-header">
                    <h2>All Products</h2>
                    <span class="results-count"><?= $items ?> items</span>
                </div>

                <!-- Products Grid -->
                <div class="products-grid"></div>
            </section>
        </div>
    </main>

    <!-- PASS PHP DATA TO JS SAFELY -->
    <script>
        // This is the ONLY correct way!
        const productsData = <?= json_encode($products, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

        // Optional: debug to see data
        console.log("Products loaded:", productsData);
    </script>
    <script src="../../js/adminHome.js"></script>
</body>
<?php include '_foot.php'; ?>