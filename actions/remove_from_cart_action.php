
<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/views/cart.php');
    exit;
}

$ip = get_ip();
$customer_id = isset($_SESSION['customer_id'])
    ? (int) $_SESSION['customer_id']
    : null;

$products = $_POST['remove'] ?? [];

if (!is_array($products) || empty($products)) {
    $_SESSION['error'] = 'Please select at least one product to remove.';
    header('Location: ' . BASE_URL . '/views/cart.php');
    exit;
}

$cartController = new CartController();
$removed = 0;

foreach ($products as $value) {
    $product_id = filter_var($value, FILTER_VALIDATE_INT);

    if ($product_id && $product_id > 0 &&
        $cartController->removeFromCart($product_id, $ip, $customer_id)) {
        $removed++;
    }
}

if ($removed > 0) {
    $_SESSION['success'] = $removed . ' product(s) removed from your cart.';
} else {
    $_SESSION['error'] = 'Could not remove the selected products.';
}

header('Location: ' . BASE_URL . '/views/cart.php');
exit;
