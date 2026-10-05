<?php

require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

require_admin();

$controller = new ProductController();

$editBrand = null;

// Check if we are editing a brand
if (isset($_GET['edit_id']) && ctype_digit($_GET['edit_id'])) {
    $editBrand = $controller->getBrandById((int)$_GET['edit_id']);
}

$brands = $controller->getAllBrands();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Brands</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

<h1>Manage Brands</h1>

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
    <?php echo $editBrand ? 'Edit Brand' : 'Add Brand'; ?>
</h2>


<?php if ($editBrand): ?>

    <form action="../../actions/update_brand_action.php" method="POST">

        <input
            type="hidden"
            name="brand_id"
            value="<?php echo e($editBrand['brand_id']); ?>"
        >

        <label>Brand Name</label>

        <input
            type="text"
            name="brand_name"
            value="<?php echo e($editBrand['brand_name']); ?>"
            required
        >

        <button type="submit">
            Update Brand
        </button>

        <a href="brand.php">
            Cancel
        </a>

    </form>

<?php else: ?>

    <form action="../../actions/add_brand_action.php" method="POST">

        <label>Brand Name</label>

        <input
            type="text"
            name="brand_name"
            required
        >

        <button type="submit">
            Add Brand
        </button>

    </form>

<?php endif; ?>


<h2>All Brands</h2>

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Brand Name</th>
        <th>Action</th>
    </tr>

    <?php if (empty($brands)): ?>

        <tr>
            <td colspan="3">
                No brands found.
            </td>
        </tr>

    <?php else: ?>

        <?php foreach ($brands as $brand): ?>

            <tr>

                <td>
                    <?php echo e($brand['brand_id']); ?>
                </td>

                <td>
                    <?php echo e($brand['brand_name']); ?>
                </td>

                <td>

                    <a href="brand.php?edit_id=<?php echo $brand['brand_id']; ?>">
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