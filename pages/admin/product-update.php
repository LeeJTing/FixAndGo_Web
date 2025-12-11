<?php
require '../../controller/product-controller.php';
include '../../_head.php';

$id = $_GET['id'] ?? 0;
$product = getProductById($id);
$categories = getAllCategory(); // create this function or hardcode
$images = getProductImages($id);

if (!$product) die('Product not found');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = updateProductById($id, $_POST);
    $product = getProductById($id);
    if ($result['success']) {
        echo "<div style='background:#d1fae5;color:green;padding:1rem;border-radius:12px;text-align:center;'>
                Product updated successfully!
              </div>";
    } else {
        echo "<div style='background:#fee2e2;color:red;padding:1.5rem;border-radius:12px;margin:1rem 0;'>
                <strong>UPDATE FAILED!</strong><br><br>
                Errors:<br>
                • " . implode("<br>• ", $result['errors']) . "
                <hr>
                <small>Check your <strong>C:\xampp\logs\php_error_log</strong> for full debug info</small>
              </div>";
    }
}

/*
        <?php if (isset($success)): ?>
            <div style="background:#d1fae5; color:#065f46; padding:1.5rem; text-align:center; font-weight:600; border-radius:16px; margin:2rem;">
                Success: <?= $success ?>
            </div>
        <?php endif; ?>
*/
?>
<style>
    :root {
        --orange: #f59e0b;
        --red: #ef4444;
        --green: #10b981;
        --gray: #64748b;
        --dark: #1e293b;
    }

    .admin-container {
        max-width: 1200px;
        margin: 1rem auto;
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .admin-header {
        background: linear-gradient(135deg, #f59e0b, #e67e22);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .admin-header h1 {
        margin: 0;
        font-size: 2.2rem;
        font-weight: 700;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    label {
        display: block;
        margin-bottom: 0.6rem;
        font-weight: 600;
        color: var(--dark);
        font-size: 1.05rem;
    }

    input,
    select,
    textarea {
        width: 100%;
        padding: 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: var(--orange);
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.15);
    }

    textarea {
        min-height: 120px;
        resize: vertical;
    }

    .image-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 1rem;
    }

    .img-box {
        position: relative;
        width: 150px;
        height: 150px;
        border-radius: 16px;
        overflow: hidden;
        border: 3px solid #eee;
    }

    .img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .remove-img {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #ef4444;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: none;
        cursor: pointer;
        font-size: 1.1rem;
    }

    .btn-group {
        grid-column: 1 / -1;
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
    }

    .btn {
        padding: 1rem 2.5rem;
        border: none;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-save {
        background: #10b981;
        color: white;
    }

    .btn-save:hover {
        background: #059669;
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
    }

    .btn-cancel {
        background: #94a3b8;
        color: white;
    }

    .btn-cancel:hover {
        background: #64748b;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .status-active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    @media (max-width: 992px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<body>

    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fa-solid fa-screwdriver-wrench"></i> Edit Product</h1>
            <p>Product ID: #<?= sprintf("%04d", $product->product_id) ?> • Last updated: <?= date('d M Y') ?></p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <input type="text" name="id" value="<?= $id ?>" hidden>
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
                                    [<?= htmlspecialchars($cat->category_code) ?>] <?= htmlspecialchars($cat->category_name) ?>
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
                        <label>Product Images</label>
                        <div class="image-preview">
                            <?php foreach ($images as $img): ?>
                                <div class="img-box">
                                    <img src="../../<?= htmlspecialchars($img->file_path) ?>" alt="">
                                    <button type="button" class="remove-img" onclick="this.parentElement.style.display='none'">×</button>
                                </div>
                            <?php endforeach; ?>
                            <div class="img-box" style="border:2px dashed #cbd5e1; display:grid; place-items:center; color:#94a3b8;">
                                <i class="fa-solid fa-plus" style="font-size:2rem;"></i>
                                <small>Add New</small>
                                <input type="file" name="new_images[]" multiple accept="image/*" style="position:absolute; opacity:0; width:100%; height:100%; cursor:pointer;">
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

<?php include '../../_foot.php'; ?>