<?php
$_title = 'Fix & Go | Admin Product List';
require_once '../../_base.php';
include 'adminHeader.php';
require '../../controller/admin-controller.php';
require_once '../../component/msg.php';
$products = getAdminProducts();
$category = getAllCategory();
displayFlashMessage();
?>

<link rel="stylesheet" href="../../css/admin-product.css">
<link rel="stylesheet" href="../../css/msg.css">

<body>
    <main class="main-container admin-products">
        <div class="admin-header">
            <h1 class="page-title">All Products</h1>
            <div class="header-actions">
                <button class="btn-add-product">
                    <a href="admin-add-product.php" class="add-link"><i class="fa-solid fa-plus"></i> Add New Product</a>
                </button>
            </div>
        </div>
        <div class="products-layout">
            <section class="products-content">
                <div class="search-filter-bar">
                    <div class="search-wrapper">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" id="searchInput" placeholder="Search products, SKU, code..." autocomplete="off">
                    </div>

                    <select id="categoryFilter" class="filter-select">
                        <option value="">All Categories</option>
                        <?php foreach ($category as $c): ?>
                            <option value="<?= $c->category_code ?>">
                                <?= htmlspecialchars($c->category_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Products Grid -->
                <div class="products-grid"></div>
            </section>
        </div>
    </main>

    <script src="../../js/admin-product.js"></script>
    <script src="../../js/confirmMsg.js"></script>
</body>
<?php
include 'adminFooter.php';
