```php
<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

// Accept POST requests only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../views/admin/product.php');
    exit;
}

// Get product ID and form values
$id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$cat = filter_input(INPUT_POST, 'product_cat', FILTER_VALIDATE_INT);
$brand = filter_input(INPUT_POST, 'product_brand', FILTER_VALIDATE_INT);

$title = trim($_POST['product_title'] ?? '');
$price = $_POST['product_price'] ?? '';
$desc = trim($_POST['product_desc'] ?? '');
$keywords = trim($_POST['product_keywords'] ?? '');
$imageFilename = basename(trim($_POST['current_image'] ?? ''));

// Validate the form
if (
    !$id || !$cat || !$brand ||
    $title === '' ||
    $desc === '' ||
    $keywords === '' ||
    !is_numeric($price) ||
    (float)$price < 0
) {
    $_SESSION['error'] = 'Please enter valid product details.';
    redirect('../views/admin/product.php');
    exit;
}

$controller = new ProductController();

// Confirm that the product exists
$existingProduct = $controller->getProductById($id);

if (!$existingProduct) {
    $_SESSION['error'] = 'Product not found.';
    redirect('../views/admin/product.php');
    exit;
}

// Keep the current image unless a new one is uploaded
if (
    isset($_FILES['product_image']) &&
    $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE
) {
    $image = $_FILES['product_image'];

    if ($image['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Image upload failed.';
        redirect('../views/admin/product.php?edit_id=' . $id);
        exit;
    }

    // Maximum image size: 5 MB
    if ($image['size'] > 5 * 1024 * 1024) {
        $_SESSION['error'] = 'Image must be 5 MB or smaller.';
        redirect('../views/admin/product.php?edit_id=' . $id);
        exit;
    }

    // Verify the uploaded file is an actual image
    $imageInfo = getimagesize($image['tmp_name']);

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($image['tmp_name']);

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp'
    ];

    if (
        $imageInfo === false ||
        !isset($allowedTypes[$mimeType])
    ) {
        $_SESSION['error'] = 'Only valid JPG, PNG, GIF, or WEBP images are allowed.';
        redirect('../views/admin/product.php?edit_id=' . $id);
        exit;
    }

    $uploadDirectory = __DIR__ . '/../images/products/';

    if (
        !is_dir($uploadDirectory) &&
        !mkdir($uploadDirectory, 0755, true)
    ) {
        $_SESSION['error'] = 'Could not create the product image folder.';
        redirect('../views/admin/product.php?edit_id=' . $id);
        exit;
    }

    $newFilename = uniqid('product_', true) . '.' . $allowedTypes[$mimeType];
    $destination = $uploadDirectory . $newFilename;

    if (!move_uploaded_file($image['tmp_name'], $destination)) {
        $_SESSION['error'] = 'Could not save the new image.';
        redirect('../views/admin/product.php?edit_id=' . $id);
        exit;
    }

    $imageFilename = $newFilename;
}

// Update the database
$result = $controller->updateProduct(
    $id,
    $cat,
    $brand,
    $title,
    (float)$price,
    $desc,
    $imageFilename,
    $keywords
);

if ($result) {
    $_SESSION['success'] = 'Product updated successfully.';
} else {
    $_SESSION['error'] = 'Could not update the product.';
}

redirect('../views/admin/product.php');
exit;

?>
```
