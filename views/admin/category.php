<?php

require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

require_admin();

$controller = new ProductController();

$editCategory = null;

// Check if we are editing a category
if (isset($_GET['edit_id']) && ctype_digit($_GET['edit_id'])) {
    $editCategory = $controller->getCategoryById((int)$_GET['edit_id']);
}

$categories = $controller->getAllCategories();

?>

<!DOCTYPE html>
<html>
<head>

    <title>Manage Categories</title>

    <link rel="stylesheet" href="../../css/style.css">

</head>

<body>

<h1>Manage Categories</h1>


<?php if (isset($_SESSION['success'])): ?>

    <p>
        <?php echo e($_SESSION['success']); ?>
    </p>

    <?php unset($_SESSION['success']); ?>

<?php endif; ?>


<?php if (isset($_SESSION['error'])): ?>

    <p>
        <?php echo e($_SESSION['error']); ?>
    </p>

    <?php unset($_SESSION['error']); ?>

<?php endif; ?>


<h2>
    <?php echo $editCategory ? 'Edit Category' : 'Add Category'; ?>
</h2>


<?php if ($editCategory): ?>

    <form action="../../actions/update_category_action.php" method="POST">

        <input
            type="hidden"
            name="cat_id"
            value="<?php echo e($editCategory['cat_id']); ?>"
        >

        <label>Category Name</label>

        <input
            type="text"
            name="cat_name"
            value="<?php echo e($editCategory['cat_name']); ?>"
            required
        >

        <button type="submit">
            Update Category
        </button>

        <a href="category.php">
            Cancel
        </a>

    </form>

<?php else: ?>

    <form action="../../actions/add_category_action.php" method="POST">

        <label>Category Name</label>

        <input
            type="text"
            name="cat_name"
            required
        >

        <button type="submit">
            Add Category
        </button>

    </form>

<?php endif; ?>


<h2>All Categories</h2>


<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Category Name</th>
        <th>Action</th>
    </tr>


    <?php if (empty($categories)): ?>

        <tr>

            <td colspan="3">
                No categories found.
            </td>

        </tr>

    <?php else: ?>

        <?php foreach ($categories as $category): ?>

            <tr>

                <td>
                    <?php echo e($category['cat_id']); ?>
                </td>

                <td>
                    <?php echo e($category['cat_name']); ?>
                </td>

                <td>

                    <a href="category.php?edit_id=<?php echo $category['cat_id']; ?>">
                        Edit
                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

    <?php endif; ?>

</table>


<br>

<a href="../../index.php">
    Back to Home
</a>


</body>
</html>