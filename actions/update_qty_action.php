
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

$quantities = $_POST['qty'] ?? [];
$product_ids = $_POST['update'] ?? [];

if (!is_array($quantities) || !is_array($product_ids)) {
    $_SESSION['error'] = 'Invalid quantity information.';
    header('Location: ' . BASE_URL . '/views/cart.php');
    exit;
}

$cartController = new CartController();
$updated = 0;
$has_error = false;

foreach ($product_ids as $value) {
    $product_id = filter_var($value, FILTER_VALIDATE_INT);

    if (!$product_id || $product_id < 1 ||
        !isset($quantities[$product_id])) {
        $has_error = true;
        continue;
    }

    $quantity = filter_var($quantities[$product_id], FILTER_VALIDATE_INT);

    if (!$quantity || $quantity < 1) {
        $has_error = true;
        continue;
    }

    if ($cartController->updateQty($product_id, $quantity, $ip, $customer_id)) {
        $updated++;
    } else {
        $has_error = true;
    }
}

if ($has_error) {
    $_SESSION['error'] = $updated > 0
        ? 'Some quantities were updated, but others could not be updated.'
        : 'Some quantities could not be updated.';
} else {
    $_SESSION['success'] = 'Cart quantities updated successfully.';
}

header('Location: ' . BASE_URL . '/views/cart.php');
exit;
