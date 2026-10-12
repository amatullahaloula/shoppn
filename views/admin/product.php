<?php
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

require_admin();

$controller = new ProductController();
$categories = $controller->getAllCategories();
$brands = $controller->getAllBrands();

$editId = filter_input(INPUT_GET, 'edit_id', FILTER_VALIDATE_INT);
$isEditing = $editId !== null && $editId !== false && $editId > 0;
$product = null;

if ($isEditing) {
    $product = $controller->getProductById($editId);

    if (!$product) {
        $_SESSION['error'] = 'Product not found.';
        redirect('product.php');
        exit;
    }
}

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';

unset($_SESSION['success'], $_SESSION['error']);

function productValue($product, $key)
{
    return htmlspecialchars(
        (string)($product[$key] ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #292929;
            margin: 0;
            padding: 25px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            padding: 25px;
            background: #FEFDFF;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        h1 {
            color: #EB4511;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: 2px solid #FFCA3A;
            border-color: #EB4511;
        }

        button,
        .cancel {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 15px;
        }

        button {
            background: #EB4511;
            color: white;
        }

        button:hover {
            background: #c9380b;
        }

        .cancel {
            background: #FFCA3A;
            color: #292929;
        }

        .message {
            padding: 12px;
            margin-bottom: 12px;
            border-radius: 5px;
        }

        .success {
            background: #d9f5df;
            color: #155724;
        }

        .error {
            background: #ffe0dc;
            color: #842029;
        }

        .hint {
            display: block;
            margin-top: 6px;
            color: #666;
        }

        .preview img {
            display: block;
            max-width: 150px;
            max-height: 150px;
            object-fit: contain;
            margin-top: 10px;
        }
    </style>
</head>

<body>
<div class="container">

    <h1>
        <?= $isEditing ? 'Edit Product' : 'Add New Product' ?>
    </h1>

    <?php if ($success !== ''): ?>
        <div class="message success">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="message error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form
        action="<?= $isEditing
            ? '../../actions/update_product.php'
            : '../../actions/add_product.php' ?>"
        method="POST"
        enctype="multipart/form-data"
        id="productForm"
    >

        <?php if ($isEditing): ?>
            <input
                type="hidden"
                name="product_id"
                value="<?= (int)$product['product_id'] ?>"
            >

            <input
                type="hidden"
                name="current_image"
                value="<?= productValue($product, 'product_image') ?>"
            >
        <?php endif; ?>

        <label for="product_title">Product Title</label>
        <input
            type="text"
            id="product_title"
            name="product_title"
            value="<?= productValue($product, 'product_title') ?>"
            required
        >

        <label for="product_price">Price</label>
        <input
            type="number"
            id="product_price"
            name="product_price"
            min="0"
            step="0.01"
            value="<?= productValue($product, 'product_price') ?>"
            required
        >

        <label for="product_desc">Description</label>
        <textarea
            id="product_desc"
            name="product_desc"
            required
        ><?= productValue($product, 'product_desc') ?></textarea>

        <label for="product_keywords">Keywords</label>
        <input
            type="text"
            id="product_keywords"
            name="product_keywords"
            value="<?= productValue($product, 'product_keywords') ?>"
            required
        >

        <label for="product_cat">Category</label>
        <select id="product_cat" name="product_cat" required>
            <option value="">Choose a category</option>

            <?php foreach ($categories as $category): ?>
                <option
                    value="<?= (int)$category['cat_id'] ?>"
                    <?= (string)($product['product_cat'] ?? '') ===
                        (string)$category['cat_id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars(
                        $category['cat_name'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="product_brand">Brand</label>
        <select id="product_brand" name="product_brand" required>
            <option value="">Choose a brand</option>

            <?php foreach ($brands as $brand): ?>
                <option
                    value="<?= (int)$brand['brand_id'] ?>"
                    <?= (string)($product['product_brand'] ?? '') ===
                        (string)$brand['brand_id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars(
                        $brand['brand_name'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="product_image">
            Product Image <?= $isEditing ? '(Optional)' : '' ?>
        </label>

        <input
            type="file"
            id="product_image"
            name="product_image"
            accept="image/jpeg,image/png,image/gif,image/webp"
            <?= $isEditing ? '' : 'required' ?>
        >

        <small class="hint">
            Allowed formats: JPG, PNG, GIF, WEBP. Maximum size: 5 MB.
        </small>

        <?php if ($isEditing && !empty($product['product_image'])): ?>
            <div class="preview">
                <strong>Current image:</strong>

                <img
                    src="../../images/products/<?= rawurlencode(
                        basename($product['product_image'])
                    ) ?>"
                    alt="Current product image"
                >
            </div>
        <?php endif; ?>

        <button type="submit">
            <?= $isEditing ? 'Update Product' : 'Add Product' ?>
        </button>

        <?php if ($isEditing): ?>
            <a href="product.php" class="cancel">Cancel Edit</a>
        <?php endif; ?>

    </form>
</div>

<script>
document.getElementById('productForm').addEventListener(
    'submit',
    function (event) {
        const input = document.getElementById('product_image');
        const file = input.files[0];

        if (!file) {
            return;
        }

        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {
            event.preventDefault();
            alert('Choose a JPG, PNG, GIF, or WEBP image.');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            event.preventDefault();
            alert('The image must be 5 MB or smaller.');
        }
    }
);
</script>

</body>
</html>