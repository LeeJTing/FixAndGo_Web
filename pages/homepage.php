  <?php
    $controllerPath = __DIR__ . '/../controller/guest-controller.php';
    require $controllerPath;
    $category = getGuestAllCategory();
    $product = getProductForDisplay();
    if (basename($_SERVER['PHP_SELF']) != "memberHomepage.php") {
        $path = "../../";
    } else {
        $path = "../";
    }
    ?>
  <style>
      .product-grid {
          display: flex;
          justify-content: space-around;
          /* Equal space around each card */
          align-items: stretch;
          /* Cards same height */
          gap: 2rem;
          /* Extra breathing room */
          flex-wrap: wrap;
          /* Responsive fallback */
          padding: 2rem 1rem;
          margin: 0 auto;
          max-width: 1400px;
      }

      .favorite-btn {
          position: absolute;
          top: 12px;
          right: 12px;
          z-index: 10;
          background: white;
          width: 40px;
          height: 40px;
          border-radius: 50%;
          border: none;
          display: grid;
          place-items: center;
          font-size: 1.2rem;
          color: #94a3b8;
          /* Default gray */
          box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
          cursor: pointer;
          transition: all 0.35s ease;
      }

      /* Default gray */
      .favorite-btn i {
          color: #94a3b8;
          transition: color 0.3s ease;
      }

      /* RED when clicked */
      .changeColor {
          color: #ef4444 !important;
          /* Bright red */
      }

      /* Optional: Filled heart when active */
      .changeColor::before {
          content: "\f004";
          /* Solid heart */
          font-weight: 900;
      }

      .product-info {
          padding: 1.5rem;
      }

      .product-category {
          font-size: 0.85rem;
          color: #64748b;
          text-transform: uppercase;
          letter-spacing: 1px;
          margin-bottom: 0.5rem;
          font-weight: 600;
      }

      .product-title {
          font-size: 1.35rem;
          font-weight: 700;
          color: #1e293b;
          margin: 0.5rem 0;
          display: -webkit-box;
          -webkit-box-orient: vertical;
          overflow: hidden;
      }

      .product-price {
          font-size: 1.2rem;
          font-weight: 800;
          color: #f59e0b;
          margin: 1rem 0;
      }

      .old-price {
          font-size: 1.1rem;
          color: #94a3b8;
          text-decoration: line-through;
          margin-left: 0.5rem;
      }

      .product-actions {
          display: flex;
          gap: 0.8rem;
      }

      .btn-view {
          flex: 1;
          padding: 0.6rem;
          border: 2px solid #f59e0b;
          color: #f59e0b;
          background: transparent;
          border-radius: 12px;
          font-weight: 600;
          cursor: pointer;
          text-align: center;
          transition: all 0.3s;
      }

      .btn-view:hover {
          background: #f59e0b;
          color: white;
      }

      .btn-cart {
          width: 48px;
          height: 48px;
          background: #f59e0b;
          color: white;
          border: none;
          border-radius: 12px;
          display: grid;
          place-items: center;
          font-size: 1.3rem;
          cursor: pointer;
      }

      .product-card {
          background: white;
          border-radius: 20px;
          overflow: hidden;
          box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
          transition: all 0.4s ease;
          position: relative;

          /* OR use this for fully responsive (recommended for most cases) */
          max-width: 300px;
          width: 100%;
          height: 700px;
          aspect-ratio: 3 / 4.4;
          /* Keeps perfect proportions */
      }

      .product-card:hover {
          transform: translateY(-12px);
          box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
      }

      .product-image {
          height: 260px;
          background: #f8fafc;
          position: relative;
          overflow: hidden;
      }

      .product-image img {
          width: 100%;
          height: 100%;
          object-fit: contain;
          padding: 20px;
          transition: transform 0.5s ease;
      }

      .product-card:hover img {
          transform: scale(1.1);
      }
  </style>
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
                          <div class="product-price">$<?= $item->unit_price ?></div>
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