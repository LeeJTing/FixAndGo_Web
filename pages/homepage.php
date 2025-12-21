  <?php
    $controllerPath = __DIR__ . '/../controller/guest-controller.php';
    require $controllerPath;
    $category = getGuestAllCategory();
    $product = getTop5Product();
    if (basename($_SERVER['PHP_SELF']) != "memberHomepage.php") {
        $path = "../../";
    } else {
        $path = "../";
    }
    ?>
  <link rel="stylesheet" href="../../css/homepage.css">

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
              <?php foreach ($category as $cat): ?>
                  <a href="../../controller/product-controller.php?function=clickCategory&category=<?= urlencode($cat->category_code) ?>" class="circle-cat">
                      <div class="circle-img">
                          <img src="<?= $path ?><?= $cat->img_path ?>" alt="<?= htmlspecialchars($cat->description) ?>">
                      </div>
                      <span class="circle-label"><?= htmlspecialchars($cat->category_name) ?></span>
                  </a>
              <?php endforeach; ?>
          </div>
      </div>
  </section>

  <section class="featured-tools-section">

      <!-- Section Header -->
      <div class="section-header text-center">
          <h2 class="section-title">Featured Tools</h2>
          <p class="section-subtitle">Explore our selection of top-rated hardware</p>
      </div>

      <!-- Tools Grid -->

      <div class="product-grid top5-grid">
          <?php foreach ($product as $item): ?>
              <article class="product-card">
                  <div class="product-image">
                      <img src="<?= $path ?><?= htmlspecialchars($item->file_path) ?>"
                          alt="<?= htmlspecialchars($item->alt_text ?? $item->product_name) ?>"
                          class="product-img">
                  </div>

                  <div class="product-info">
                      <span class="product-category"><?= htmlspecialchars($item->category_name) ?></span>
                      <h3 class="product-title"><?= htmlspecialchars($item->product_name) ?></h3>
                      <p class="product-description">
                          <?= htmlspecialchars($item->short_desc ?? substr(strip_tags($item->description ?? ''), 0, 150) . '...') ?>
                      </p>

                      <div class="product-price">
                          RM <?= number_format($item->unit_price, 2) ?>
                          <?php if (!empty($item->old_price)): ?>
                              <span class="old-price">RM <?= number_format($item->old_price, 2) ?></span>
                          <?php endif; ?>
                      </div>

                      <div class="product-actions">
                          <a href="product-detail.php?id=<?= $item->product_id ?>" class="btn-view">
                              View Details
                          </a>
                          <button class="btn-cart add-to-cart"
                              data-id="<?= $item->product_id ?>"
                              data-name="<?= htmlspecialchars($item->product_name) ?>"
                              data-price="<?= $item->unit_price ?>">
                              <i class="fas fa-cart-plus"></i>
                          </button>
                      </div>
                  </div>
              </article>
          <?php endforeach; ?>
      </div>

  </section>