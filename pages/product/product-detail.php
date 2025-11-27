<?php include '../../_head.php'; ?>

<body>
    <main class="main-container">
        <h1 class="page-title">All Products</h1>

        <div class="products-grid">
            <div class="product-card">
                <div class="product-image">
                    <button class="favorite-btn" aria-label="Add to wishlist">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                    <img src="../../images/icon.png" alt="Drill">
                </div>
                <div class="product-info">
                    <div class="product-category">Power Tools</div>
                    <h3 class="product-title">20V Cordless Drill</h3>
                    <div class="product-price">$89.99</div>
                </div>
            </div>
        </div>
    </main>

    <?php include "../../_foot.php" ?>

    <script>
        $(document).ready(function() {
            $(document).on("click", ".favorite-btn", function() {
                $(this).find("i").toggleClass("changeColor");
            });
        });
    </script>
</body>

</html>