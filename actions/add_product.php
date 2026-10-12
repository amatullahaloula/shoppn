```php
<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/admin/product.php');
    exit;
}

// Get form values
$title = trim($_POST['product_title'] ?? '');
$price = $_POST['product_price'] ?? '';
$desc = trim($_POST['product_desc'] ?? '');
$cat = filter_input(INPUT_POST, 'product_cat', FILTER_VALIDATE_INT);
$brand = filter_input(INPUT_POST, 'product_brand', FILTER_VALIDATE_INT);
$keywords = trim($_POST['product_keywords'] ?? '');

// Validate required fields
if (
    $title === '' ||
    $desc === '' ||
    $keywords === '' ||
    $cat === false || $cat === null || $cat < 1 ||
    $brand === false || $brand === null || $brand < 1 ||
    !is_numeric($price) || (float)$price < 0
) {
    $_SESSION['error'] = 'Please enter valid product details.';
    redirect('views/admin/product.php');
    exit;
}

// Check image upload
if (
    !isset($_FILES['product_image']) ||
    $_FILES['product_image']['error'] !== UPLOAD_ERR_OK
) {
    $_SESSION['error'] = 'Please select a product image.';
    redirect('views/admin/product.php');
    exit;
}

$image = $_FILES['product_image'];

// Limit image size to 5 MB
$maxSize = 5 * 1024 * 1024;

if ($image['size'] > $maxSize) {
    $_SESSION['error'] = 'Image must be 5 MB or smaller.';
    redirect('views/admin/product.php');
    exit;
}

// Check actual image type
$imageInfo = getimagesize($image['tmp_name']);

if ($imageInfo === false) {
    $_SESSION['error'] = 'The uploaded file is not a valid image.';
    redirect('views/admin/product.php');
    exit;
}

$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp'
];

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($image['tmp_name']);

if (
    !isset($allowedTypes[$mimeType]) ||
    $imageInfo[2] === false
) {
    $_SESSION['error'] = 'Only JPG, PNG, GIF, and WEBP images are allowed.';
    redirect('views/admin/product.php');
    exit;
}

// Create the product image folder if necessary
$uploadDirectory = __DIR__ . '/../images/products/';

if (
    !is_dir($uploadDirectory) &&
    !mkdir($uploadDirectory, 0755, true)
) {
    $_SESSION['error'] = 'Could not create the product image folder.';
    redirect('../views/admin/product.php');
    exit;
}

// Generate a unique filename and move the image
$filename = uniqid('product_', true) . '.' . $allowedTypes[$mimeType];
$destination = $uploadDirectory . $filename;

if (!move_uploaded_file($image['tmp_name'], $destination)) {
    $_SESSION['error'] = 'Image upload failed. Please try again.';
    redirect('../views/admin/product.php');
    exit;
}

// Save product details in the database
$controller = new ProductController();

$result = $controller->addProduct(
    $cat,
    $brand,
    $title,
    (float)$price,
    $desc,
    $filename,
    $keywords
);

if ($result) {
    $_SESSION['success'] = 'Product added successfully.';
} else {
    // Remove the uploaded image if database insertion fails
    if (file_exists($destination)) {
        unlink($destination);
    }

    $_SESSION['error'] = 'Could not add the product. Please check your details.';
}

redirect('views/admin/product.php');
exit;

?>
```
