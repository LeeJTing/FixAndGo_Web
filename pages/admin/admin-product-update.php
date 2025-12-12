<link rel="stylesheet" href="../../css/admin-product-update.css">
<link rel="stylesheet" href="../../css/msg.css">
<?php
require '../../controller/product-controller.php';
require_once '../../component/msg.php';
include 'adminHeader.php';

$id = $_GET['id'] ?? 0;
$product = getProductById($id);
$categories = getAllCategory();
$images = getProductImages($id);
?>

<body>
    <?php displayFlashMessage(); ?>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fa-solid fa-screwdriver-wrench"></i> Edit Product</h1>
            <p>Product ID: #<?= sprintf("%04d", $product->product_id) ?> • Last updated: <?= date('d M Y') ?></p>
        </div>

        <form id="updateProductForm" method="POST" enctype="multipart/form-data" action="../../controller/admin-controller.php">
            <input type="text" name="function" value="update" hidden>
            <input type="text" id="productId" name="id" value="<?= $id ?>" hidden>

            <div class="form-grid">
                <!-- Left Column -->
                <div>
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="product_name" value="<?= htmlspecialchars($product->product_name) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_code" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat->category_code) ?>"
                                    <?= $cat->category_code == $product->category_code ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat->category_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Unit Price (RM)</label>
                        <input type="number" step="0.01" name="unit_price" value="<?= $product->unit_price ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" name="stock_quantity" value="<?= $product->stock_quantity ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Product Points (Optional)</label>
                        <input type="number" name="product_point" value="<?= $product->product_point ?? 0 ?>">
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <div class="form-group">
                        <label>Short Description</label>
                        <textarea name="short_desc"><?= htmlspecialchars($product->short_desc ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Full Description</label>
                        <textarea name="description"><?= htmlspecialchars($product->description ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-input">
                            <option value="active" <?= $product->status === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $product->status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Product Images</label>
                        <div class="image-preview">
                            <!-- Existing images from DB -->
                            <?php foreach ($images as $img): ?>
                                <div class="img-box existing-img" data-filename="<?= htmlspecialchars($img->file_path) ?>">
                                    <img src="../../<?= htmlspecialchars($img->file_path) ?>" alt="<?= $img->alt ?>">
                                    <button type="button" class="remove-img">×</button>
                                </div>
                            <?php endforeach; ?>

                            <!-- Add new image box -->
                            <div class="img-box add-new" style="border:2px dashed #cbd5e1; display:grid; place-items:center; color:#94a3b8;">
                                <i class="fa-solid fa-plus" style="font-size:2rem;"></i>
                                <small>Add New</small>
                                <input type="file"
                                    id="newImagesInput"
                                    name="new_images[]"
                                    multiple
                                    accept="image/*"
                                    style="position:absolute; opacity:0; width:100%; height:100%; cursor:pointer;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="btn-group">
                    <button type="button" class="btn btn-cancel" onclick="history.back()">Cancel</button>
                    <button type="submit" class="btn btn-save">
                        <i class="fa-solid fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

</body>
<script src="../../js/confirmMsg.js"></script>
<script src="../../js/admin-product-update.js"></script>
<?php include 'adminFooter.php'; ?>