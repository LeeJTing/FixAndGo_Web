<?php
$_title = 'Fix & Go | Admin Add Category';
require_once '../../_base.php';
require '../../controller/category-controller.php';
require_once '../../component/msg.php';
include 'adminHeader.php';
$category = getAllCategoryDao() ?? [];
?>
<link rel="stylesheet" href="../../css/admin-add-category.css">
<link rel="stylesheet" href="../../css/msg.css">
<?php displayFlashMessage(); ?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fa-solid fa-folder-plus"></i> Add New Category</h1>
        <p>Create a new product category for your store</p>
    </div>

    <form id="addCategoryForm" method="POST" enctype="multipart/form-data" action="../../controller/category-controller.php">
        <input type="hidden" name="function" value="add_category">
        <div class="form-grid">
            <div>
                <div class="form-group">
                    <label for="category_name">Category Name <span class="required">*</span></label>
                    <input type="text"
                        name="category_name"
                        id="category_name"
                        required
                        placeholder="e.g., Power Tools, Fasteners, Storage">
                </div>

                <div class="form-group">
                    <label for="description">Description (Optional)</label>
                    <textarea name="description"
                        id="description"
                        rows="5"
                        placeholder="Brief description of this category"></textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-input" required>
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Right Column: Image Upload -->
            <div class="form-group">
                <label>Category Image <span class="required">*</span></label>
                <p class="help-text">Recommended size: 400x400px (JPG, PNG)</p>

                <div class="img-box add-new" id="uploadContainer">
                    <i class="fa-solid fa-image" id="placeholderIcon" style="font-size:2.5rem; color:#94a3b8;"></i>
                    <img id="previewImg" src="#" alt="Preview" style="display:none; border-radius:8px; max-width:100%; max-height:150px;">
                    <small>Drag & Drop or Click to Upload</small>
                    <input type="file"
                        name="category_image"
                        id="categoryImageInput"
                        accept="image/*"
                        hidden>
                    <button type="button" id="removePreview" class="remove-img" style="display:none;">×</button>
                </div>

            </div>

        </div>

        <!-- Action Buttons -->
        <div class="btn-group">
            <a type="button" class="btn btn-cancel" href="../admin/admin-category.php" style="text-decoration: none;">Cancel</a>
            <button type="submit" class="btn btn-save">
                <i class="fa-solid fa-plus-circle"></i> Add Category
            </button>
        </div>
    </form>
</div>
<script src="../../js/file_validation.js"></script>
<script src="../../js/admin-add-category.js"></script>
<script src="../../js/validation.js"></script>
<?php include 'adminFooter.php'; ?>