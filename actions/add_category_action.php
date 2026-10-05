<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/admin/category.php');
}

$name = trim($_POST['cat_name'] ?? '');

if ($name === '') {
    $_SESSION['error'] = 'Category name is required.';
    redirect('views/admin/category.php');
}

$controller = new ProductController();

$result = $controller->addCategory($name);

if ($result) {
    $_SESSION['success'] = 'Category added successfully.';
} else {
    $_SESSION['error'] = 'Could not add category.';
}

redirect('views/admin/category.php');

?>