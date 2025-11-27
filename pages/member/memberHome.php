
<?php
$products = [
    [
        'name' => 'Cordless Drill 20V',
        'category' => 'Power Tools',
        'desc' => 'High-power cordless drill with 2 batteries included.',
        'price' => '89.99',
        'image' => '../../images/product/claw_hammer_23mm/claw_hammer_23mm-0.jpg'
    ],
    [
        'name' => 'Cordless Drill 20V',
        'category' => 'Power Tools',
        'desc' => 'High-power cordless drill with 2 batteries included.',
        'price' => '89.99',
        'image' => '../../images/product/cross_pein_pin_hammer_14mm/cross_pein_pin_hammer_14mm-0.jpg'
    ],
    [
        'name' => 'Cordless Drill 20V',
        'category' => 'Power Tools',
        'desc' => 'High-power cordless drill with 2 batteries included.',
        'price' => '89.99',
        'image' => '../../images/product/cross_pein_pin_hammer_14mm/cross_pein_pin_hammer_14mm-0.jpg'
    ],
    // ... more products
];
?>

<body>
    <?php 
    require "../../_base.php";
    $rootDir = "../../";

    include $rootDir."_head.php" 
    
    ?>
    <section class="hero">
        <div class="container">
            <h1>Build Your Dreams</h1>
            <p>Premium quality tools for professionals and DIY enthusiasts. Built to last, designed for precision.</p>
            <button class="btn btn-primary" data-link="products">Shop Now &rarr;</button>
        </div>
    </section>
 <!-- 5 CIRCULAR CATEGORIES – CLEAN WHITE BACKGROUND -->
<section class="circular-categories-clean">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Shop by Category</h2>
            <p class="section-subtitle">Find the right tool for every job</p>
        </div>

        <div class="categories-circle-grid">
            <!-- 1. Hand Tools -->
            <a href="products.php?cat=hand-tools" class="circle-cat">
                <div class="circle-img">
                    <img src="../../images/product/claw_hammer_23mm/claw_hammer_23mm-1.jpg" alt="Hand Tools">
                </div>
                <span class="circle-label">Hand Tools</span>
            </a>

            <!-- 2. Power Tools -->
            <a href="products.php?cat=power-tools" class="circle-cat">
                <div class="circle-img">
                    <img src="../../images/product/claw_hammer_23mm/claw_hammer_23mm-0.jpg" alt="Power Tools">
                </div>
                <span class="circle-label">Power Tools</span>
            </a>

            <!-- 3. Measuring Tools -->
            <a href="products.php?cat=measuring" class="circle-cat">
                <div class="circle-img">
                    <img src="../../images/product/cross_pein_pin_hammer_14mm/cross_pein_pin_hammer_14mm-0.jpg" alt="Measuring Tools">
                </div>
                <span class="circle-label">Measuring Tools</span>
            </a>

            <!-- 4. Safety Gear -->
            <a href="products.php?cat=safety" class="circle-cat">
                <div class="circle-img">
                    <img src="../../images/product/magnetic_claw_hammer/magnetic_claw_hammer-0.jpg" alt="Safety Gear">
                </div>
                <span class="circle-label">Safety Gear</span>
            </a>

            <!-- 5. Accessories -->
            <a href="products.php?cat=accessories" class="circle-cat">
                <div class="circle-img">
                    <img src="../../images/product/magnetic_claw_hammer/magnetic_claw_hammer-2.jpg" alt="Accessories">
                </div>
                <span class="circle-label">Accessories</span>
            </a>
        </div>
    </div>
</section>
    <section class="featured-tools-section">
        <div class="container">
            <!-- Section Header -->
            <div class="section-header text-center">
                <h2 class="section-title">Featured Tools</h2>
                <p class="section-subtitle">Explore our selection of top-rated hardware</p>
            </div>

        <!-- Tools Grid -->
        <div class="tools-grid">
            <?php foreach ($products as $item): ?>
                <article class="tool-card">
                    <div class="tool-image">
                        <div class="favorite">★</div>
                        <img src="<?= $item['image'] ?>" 
                            alt="<?= $item['name'] ?>" 
                            class="product-img">
                    </div>
                    <div class="tool-content">
                        <span class="category"><?= $item['category'] ?></span>
                        <h3 class="tool-title"><?= $item['name'] ?></h3>
                        <p class="tool-description"><?= $item['desc'] ?></p>
                        <div class="price">$<?= $item['price'] ?></div>
                        <div class="tool-actions">
                            <a href="#details-<?= strtolower(str_replace(' ', '-', $item['name'])) ?>" class="btn-details">
                                View Details
                            </a>
                            <button class="add-to-cart" 
                                    data-name="<?= $item['name'] ?>" 
                                    data-price="<?= $item['price'] ?>">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
     <?php include $rootDir . "_foot.php" ?>
    <script src="<?= $rootDir ?>js/script.js"></script>

</html>