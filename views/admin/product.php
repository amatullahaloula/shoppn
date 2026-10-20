<?php
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

require_admin();

$productController = new ProductController();

$categories = $productController->getAllCategories();
$brands = $productController->getAllBrands();

$isEditing = false;
$product = [];

if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $product = $productController->getProductById((int) $_GET['edit_id']);

    if ($product) {
        $isEditing = true;
    } else {
        $_SESSION['error'] = 'Product not found.';
        redirect(BASE_URL . '/views/admin/product.php');
        exit;
    }
}

$page_title = $isEditing ? 'Edit Product' : 'Add New Product';

function productValue($product, $key)
{
    return isset($product[$key]) ? htmlspecialchars((string) $product[$key], ENT_QUOTES, 'UTF-8') : '';
}

$successMessage = $_SESSION['success'] ?? '';
$errorMessage = $_SESSION['error'] ?? '';

unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> | Shoppn</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">

    <style>
        .product-admin-container {
            max-width: 850px;
            margin: 35px auto;
            padding: 25px;
            background: #FEFDFF;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.10);
        }

        .product-admin-container h1 {
            color: #EB4511;
            margin-bottom: 10px;
        }

        .product-admin-container .intro {
            color: #555;
            margin-bottom: 25px;
        }

        .back-button {
            display: inline-block;
            padding: 10px 16px;
            margin-bottom: 20px;
            background: #FFCA3A;
            color: #222;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .back-button:hover {
            background: #f0b91f;
        }

        .product-admin-container .form-group {
            margin-bottom: 18px;
        }

        .product-admin-container label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
            color: #333;
        }

        .product-admin-container input,
        .product-admin-container textarea,
        .product-admin-container select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 15px;
            background: #fff;
        }

        .product-admin-container textarea {
            min-height: 120px;
            resize: vertical;
        }

        .submit-button {
            background: #EB4511;
            color: #FEFDFF;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .submit-button:hover {
            background: #c9360a;
        }

        .message {
            padding: 12px;
            margin-bottom: 18px;
            border-radius: 6px;
        }

        .success-message {
            background: #e5f7e9;
            color: #17652a;
        }

        .error-message {
            background: #fde8e7;
            color: #a32119;
        }

        .current-image {
            margin-top: 10px;
            max-width: 150px;
            display: block;
            border-radius: 6px;
        }

        .form-note {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            .product-admin-container {
                margin: 15px;
                padding: 18px;
            }
        }
    </style>
</head>

<body>

<main class="product-admin-container">

    <a class="back-button" href="<?= BASE_URL ?>/views/all_products.php">
        &larr; Back to Products
    </a>

    <h1><?= e($page_title) ?></h1>
    <p class="intro">
        <?= $isEditing ? 'Update the details of this product.' : 'Fill in the form below to add a new product.' ?>
    </p>

    <?php if ($successMessage !== ''): ?>
        <div class="message success-message">
            <?= e($successMessage) ?>
        </div>
    <?php endif; ?>

    <?php if ($errorMessage !== ''): ?>
        <div class="message error-message">
            <?= e($errorMessage) ?>
        </div>
    <?php endif; ?>

    <form
        action="<?= BASE_URL ?>/actions/<?= $isEditing ? 'update_product.php' : 'add_product.php' ?>"
        method="POST"
        enctype="multipart/form-data"
    >

        <?php if ($isEditing): ?>
            <input type="hidden" name="product_id" value="<?= (int) ($product['product_id'] ?? 0) ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="product_title">Product Name</label>
            <input
                type="text"
                id="product_title"
                name="product_title"
                value="<?= productValue($product, 'product_title') ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="product_price">Product Price (GH₵)</label>
            <input
                type="number"
                id="product_price"
                name="product_price"
                min="0"
                step="0.01"
                value="<?= productValue($product, 'product_price') ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="product_desc">Product Description</label>
            <textarea
                id="product_desc"
                name="product_desc"
                required
            ><?= productValue($product, 'product_desc') ?></textarea>
        </div>

        <div class="form-group">
            <label for="product_keywords">Product Keywords</label>
            <input
                type="text"
                id="product_keywords"
                name="product_keywords"
                value="<?= productValue($product, 'product_keywords') ?>"
                placeholder="Example: shoes, fashion, sneakers"
                required
            >
        </div>

        <div class="form-group">
            <label for="product_cat">Category</label>
            <select id="product_cat" name="product_cat" required>
                <option value="">Choose a category</option>

                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?= (int) $category['cat_id'] ?>"
                        <?= isset($product['product_cat']) && (int) $product['product_cat'] === (int) $category['cat_id'] ? 'selected' : '' ?>
                    >
                        <?= e($category['cat_title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="product_brand">Brand</label>
            <select id="product_brand" name="product_brand" required>
                <option value="">Choose a brand</option>

                <?php foreach ($brands as $brand): ?>
                    <option
                        value="<?= (int) $brand['brand_id'] ?>"
                        <?= isset($product['product_brand']) && (int) $product['product_brand'] === (int) $brand['brand_id'] ? 'selected' : '' ?>
                    >
                        <?= e($brand['brand_title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="product_image">
                <?= $isEditing ? 'Change Product Image (optional)' : 'Product Image' ?>
            </label>

            <input
                type="file"
                id="product_image"
                name="product_image"
                accept=".jpg,.jpeg,.png,.gif,.webp"
                <?= $isEditing ? '' : 'required' ?>
            >

            <small class="form-note">Accepted formats: JPG, JPEG, PNG, GIF, WEBP. Maximum size: 5 MB.</small>

            <?php if ($isEditing && !empty($product['product_image'])): ?>
                <p>Current image:</p>
                <img
                    class="current-image"
                    src="<?= BASE_URL ?>/uploads/<?= rawurlencode(basename($product['product_image'])) ?>"
                    alt="Current product image"
                >
            <?php endif; ?>
        </div>

        <button type="submit" class="submit-button">
            <?= $isEditing ? 'Update Product' : 'Add Product' ?>
        </button>

    </form>

</main>

</body>
</html>