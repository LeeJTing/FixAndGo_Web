<?php
require '../../controller/product-controller.php';
require '../../component/msg.php';
include 'adminHeader.php';
$category = getAllCategory();
$_title = "Fix & Go | Admin - Add Product";
displayFlashMessage();
?>
<link rel="stylesheet" href="../../css/msg.css">
<link rel="stylesheet" href="../../css/admin-add-product.css">

<main class="main-container admin-add-product">
    <div class="page-header">
        <h1 class="page-title">Add New Product</h1>
        <a href="products.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Back to Products
        </a>
    </div>

    <form class="add-product-form" action="../../controller/admin-controller.php" enctype="multipart/form-data" method="POST">
        <div class="form-grid">
            <input type="text" name="function" value="add" hidden>
            <!-- LEFT: IMAGE UPLOAD -->
            <div class="image-upload-section">
                <h3>Product Images <span class="required">*</span></h3>

                <!-- Main Preview -->
                <div class="main-image-preview" id="mainPreview">
                    <img src="../../images/dark_image.jpg" alt="No image" id="mainImg">
                    <div class="upload-overlay">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <p>Click to upload main image</p>
                        <small>Max 8 images • 800×800px recommended</small>
                    </div>
                </div>

                <!-- Hidden File Input -->
                <input type="file" name="product_images[]" id="productImages" accept="image/*" multiple hidden>

                <!-- Thumbnail Gallery -->
                <div class="thumbnail-gallery" id="thumbnailGallery">
                    <div class="thumbnail-item add-more">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add More</span>
                    </div>
                </div>
            </div>

            <div class="product-details-section">
                <h3 class="section-title">Product Information</h3>

                <!-- Product Name -->
                <div class="form-group">
                    <label class="form-label">
                        Product Name <span class="required">*</span>
                    </label>
                    <input type="text" name="product_name" class="form-input"
                        placeholder="e.g. 20V Cordless Drill Pro">
                </div>

                <!-- SKU -->
                <div class="form-group">
                    <label class="form-label">Short Desc</label>
                    <input type="text" name="short_desc" class="form-input"
                        placeholder="e.g. DRILL-20V-PRO">
                </div>

                <!-- Category + Price -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category <span class="required">*</span></label>
                        <select name="category_id" class="form-input">
                            <option value="">Select category</option>
                            <?php foreach ($category as $c): ?>
                                <option value="<?= $c->category_code ?>">
                                    <?= htmlspecialchars($c->category_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price (RM) <span class="required">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-input"
                            placeholder="89.99">
                    </div>
                </div>

                <!-- Stock + Status -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Stock Quantity <span class="required">*</span></label>
                        <input type="number" name="stock" class="form-input" min="0" value="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Product Point <span class="required">*</span></label>
                        <input type="number" name="point" class="form-input" value="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Low Stock Number<span class="required">*</span></label>
                        <input type="number" name="lowstock" class="form-input" value="100">
                    </div>
                </div>
                <!-- Description -->
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="7" class="form-input textarea"
                        placeholder="Describe features, specifications, benefits, and usage..."></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <i class="fa-solid fa-check"></i> Save Product
                    </button>
                    <button type="button" class="btn-cancel" onclick="history.back()">
                        Cancel
                    </button>
                </div>
            </div>
    </form>
</main>
<script src="../../js/file_validation.js"></script>
<script src="../../js/validation.js"></script>
<script src="../../js/confirmMsg.js"></script>
<script src="../../js/admin-add-product.js"></script>
<?php include 'adminFooter.php'; ?>