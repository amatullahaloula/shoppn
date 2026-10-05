<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/admin/brand.php');
}

$id = $_POST['brand_id'] ?? '';
$name = trim($_POST['brand_name'] ?? '');

if (!ctype_digit($id) || (int)$id <= 0) {
    $_SESSION['error'] = 'Invalid brand ID.';
    redirect('views/admin/brand.php');
}

if ($name === '') {
    $_SESSION['error'] = 'Brand name is required.';
    redirect('views/admin/brand.php');
}

$controller = new ProductController();

$result = $controller->updateBrand((int)$id, $name);

if ($result) {
    $_SESSION['success'] = 'Brand updated successfully.';
} else {
    $_SESSION['error'] = 'Could not update brand.';
}

redirect('views/admin/brand.php');

?>