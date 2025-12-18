<?php
require '../../_base.php';
require '../../controller/category-controller.php';
require_once '../../component/msg.php';
include 'adminHeader.php';
$category = getAllCategory();
displayFlashMessage();
?>
<link rel="stylesheet" href="../../css/admin-category.css">
<link rel="stylesheet" href="../../css/msg.css">
<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">Category List</h2>
        <a href="admin-add-category.php" class="btn-add-category">Add Category</a>
    </div>

    <table class="category-table">
        <colgroup>
            <col>
            <col class="col-category-name">
            <col>
            <col>
        </colgroup>
        <thead>
            <tr>
                <th>Image</th>
                <th>Category Name</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($category) && is_array($category)) { ?>
                <?php foreach ($category as $cat) { ?>
                    <tr>
                        <td>
                            <img
                                src="../../<?= htmlspecialchars($cat->img_path) ?>"
                                class="category-img"
                                alt="<?= htmlspecialchars($cat->category_name) ?>">
                        </td>
                        <td><?= htmlspecialchars($cat->category_name) ?></td>
                        <td><?= htmlspecialchars($cat->description) ?></td>
                        <td class="action-buttons">

                            <a href="admin-update-category.php?code=<?= htmlspecialchars($cat->category_code) ?>"
                                class="btn-modify"
                                title="Edit Category">
                                <i class="fa-solid fa-pen-to-square"></i> Modify
                            </a>


                            <form method="post" action="../../controller/category-controller.php" id="form-oder"
                                style="display:inline;">
                                <input type="hidden" name="function" value="delete">
                                <input type="hidden" name="category_code" value="<?= htmlspecialchars($cat->category_code) ?>">
                                <button type="submit"
                                    class="btn-delete"
                                    <?= $cat->is_show == 0 ? 'disabled' : '' ?>
                                    title="Delete Category">
                                    <i class="fa-solid fa-trash-can"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="4" style="text-align:center; padding:20px;">
                        No category found.
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script src="../../js/confirmMsg.js"></script>
<script>
    $("#form-order").on("submit", function(e) {
        e.preventDefault();

        showConfirm("Are you sure you want to delete this category?", function(result) {
            if (result) {
                $("#form-order")[0].submit();
            }
        });
    });
</script>
<?php include 'adminFooter.php'; ?>