<?php
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
        <input type="hidden" name="existing_image" id="existingImage" value="<?= htmlspecialchars($category->img_path) ?>">

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
                        <option value="active" <?= $category->is_show ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= !$category->is_show ? 'selected' : '' ?>>Inactive</option>
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
                                <img src="../../<?= htmlspecialchars($category->img_path) ?>"
                                    alt="Current <?= htmlspecialchars($category->category_name) ?> image"
                                    style="width:100%; height:100%; object-fit:cover; border-radius:8px;">
                                <button type="button" id="removeCurrent" class="remove-img" title="Remove current image">×</button>
                            </div>
                        <?php endif; ?>

                        <!-- Upload New Image Box -->
                        <div class="img-box add-new" id="addNewBox" style="<?= !empty($category->img_path) ? 'display:none;' : '' ?>">
                            <i class="fa-solid fa-image" style="font-size:2.5rem; color:#94a3b8;"></i>
                            <small>Upload New Image</small>
                            <input type="file"
                                name="category_image"
                                id="categoryImageInput"
                                accept="image/*">
                        </div>
                    </div>

                    <!-- Preview for new image -->
                    <div id="previewContainer" style="display:none; margin-top:12px;">
                        <div class="img-box preview-img">
                            <img id="previewImg" src="#" alt="New Preview">
                            <button type="button" id="removePreview" class="remove-img">×</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="btn-group">
            <a class="btn btn-cancel" href="../admin/admin-category.php">Cancel</a>
            <button type="submit" class="btn btn-save">
                <i class="fa-solid fa-save"></i> Save Changes
            </button>
        </div>
    </form>
</div>
<script>
    // Image preview for new upload
    $('#categoryImageInput').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#previewContainer').show();
                $('#addNewBox').hide();
                $('#currentImgBox').hide(); // Hide old image when new is selected
            }
            reader.readAsDataURL(file);
        }
    });

    // Remove new preview
    $('#removePreview').on('click', function() {
        $('#categoryImageInput').val('');
        $('#previewContainer').hide();
        $('#addNewBox').show();
        if ($('#existingImage').val()) {
            $('#currentImgBox').show();
        }
    });

    // Remove current image (will trigger upload new)
    $('#removeCurrent').on('click', function() {
        $('#existingImage').val(''); // Clear hidden field
        $('#currentImgBox').hide();
        $('#addNewBox').show();
    });
</script>
<?php include 'adminFooter.php'; ?>