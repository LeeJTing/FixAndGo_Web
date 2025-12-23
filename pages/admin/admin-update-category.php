<?php
$_title = 'Fix & Go | Admin Update Category';
require '../../_base.php';
require '../../controller/category-controller.php';
require_once '../../component/msg.php';
include 'adminHeader.php';

$category_code = $_GET['code'] ?? '';
$category = getCategoryByCode($category_code);
?>
<link rel="stylesheet" href="../../css/admin-add-category.css">
<link rel="stylesheet" href="../../css/msg.css">

<?php displayFlashMessage(); ?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fa-solid fa-folder-pen"></i> Edit Category</h1>
        <p>Update category details • Code: <?= htmlspecialchars($category->category_code) ?></p>
    </div>

    <form id="updateCategoryForm" method="POST" enctype="multipart/form-data" action="../../controller/category-controller.php">
        <input type="hidden" name="function" value="update_category">
        <input type="hidden" name="category_code" value="<?= htmlspecialchars($category->category_code) ?>">
        <input type="hidden" name="existed_image" value="<?= htmlspecialchars($category->img_path) ?>">

        <div class="form-grid">
            <!-- Left Column -->
            <div>
                <div class="form-group">
                    <label for="category_name">Category Name <span class="required">*</span></label>
                    <input type="text"
                        name="category_name"
                        id="category_name"
                        value="<?= htmlspecialchars($category->category_name) ?>"
                        placeholder="e.g., Power Tools, Fasteners, Storage"
                        required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description"
                        id="description"
                        rows="5"
                        placeholder="Brief description of this category (optional)"><?= htmlspecialchars($category->description ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-input">
                        <option value="active" <?= $category->is_show == 1 ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $category->is_show == 0 ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Right Column: Image Upload -->
            <div>
                <div class="form-group">
                    <label>Category Image <span class="required">*</span></label>
                    <p class="help-text">Recommended size: 400x400px (JPG, PNG). Leave unchanged to keep current image.</p>

                    <div class="image-preview" id="imagePreview">
                        <!-- Current Image (if exists) -->
                        <?php if (!empty($category->img_path)): ?>
                            <div class="img-box current-img" id="currentImgBox">
                                <img id="previewImage" src="../../<?= htmlspecialchars($category->img_path) ?>"
                                    alt="Current <?= htmlspecialchars($category->category_name) ?> image"
                                    style="width:100%; height:100%; object-fit:cover; border-radius:8px;">
                                <button type="button" id="removeCurrent" class="remove-img" title="Remove current image">×</button>
                            </div>
                        <?php endif; ?>

                        <!-- Upload / Drag & Drop New Image -->
                        <div class="img-box add-new" id="addNewBox" style="<?= !empty($category->img_path) ? 'display:none;' : '' ?>">
                            <i class="fa-solid fa-image" id="placeholderIcon" style="font-size:2.5rem; color:#94a3b8;"></i>
                            <img id="previewImg" src="#" alt="New Preview" style="display:none; max-width:100%; max-height:150px; border-radius:8px;">
                            <small>Drag & Drop or Click to Upload</small>
                            <input type="file" name="category_image" id="categoryImageInput" accept="image/*" hidden>
                            <button type="button" id="removePreview" class="remove-img" style="display:none;">×</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Action Buttons -->
        <div class="btn-group">
            <a class="btn btn-cancel" href="../admin/admin-category.php" style="text-decoration: none;">Cancel</a>
            <button type="submit" class="btn btn-save">
                <i class="fa-solid fa-save"></i> Save Changes
            </button>
        </div>
    </form>
</div>
<script src="../../js/displayMsg.js"></script>
<script src="../../js/confirmMsg.js"></script>
<script src="../../js/validation.js"></script>
<script src="../../js/admin-update-category.js"></script>
<?php include 'adminFooter.php'; ?>