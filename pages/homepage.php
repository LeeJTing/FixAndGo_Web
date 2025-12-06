  <?php
    $controllerPath = __DIR__ . '/../controller/guest-controller.php';
    require $controllerPath;
    $category = getAllCategoryDAO();
    $product = getProductListDao();
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
                  <a href="products.php?cat=<?= urlencode($cat->category_code) ?>" class="circle-cat">
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
      <div class="container">
          <!-- Section Header -->
          <div class="section-header text-center">
              <h2 class="section-title">Featured Tools</h2>
              <p class="section-subtitle">Explore our selection of top-rated hardware</p>
          </div>

          <!-- Tools Grid -->
          <div class="product-grid">
              <?php
                $firstthree = array_slice($product, 0, 3);
                foreach ($firstthree as $item):
                ?>
                  <article class="product-card">
                      <div class="product-image">
                          <button class="favorite-btn" aria-label="Add to wishlist" title="Add to wishlist">
                              <i class="fa-regular fa-heart"></i>
                          </button>
                          <img src="<?= $path ?><?= $item->file_path ?>"
                              alt="<?= $item->alt_text ?>"
                              class="product-img">
                      </div>
                      <div class="product-info">
                          <span class="product-category"><?= $item->category_name ?></span>
                          <h3 class="product-title"><?= $item->product_name ?></h3>
                          <p class="product-description"><?= $item->description ?></p>
                          <div class="product-price">RM <?= $item->unit_price ?></div>
                          <div class="product-actions">
                              <a href="#details-<?= strtolower(str_replace(' ', '-', $item->product_id)) ?>" class="btn-view">
                                  View Details
                              </a>
                              <button class="btn-cart add-to-cart"
                                  data-name="<?= $item->product_name ?>"
                                  data-price="<?= $item->unit_price ?>">
                                  Add to Cart
                              </button>
                          </div>
                      </div>
                  </article>
              <?php endforeach; ?>
          </div>
  </section>


  <script>
      $(document).on("click", ".favorite-btn", function() {
          $(this).find("i").toggleClass("changeColor");
      });
  </script>