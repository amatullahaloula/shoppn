
<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

$redirect_url = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/index.php';

$product_id = filter_input(INPUT_GET, 'add_cart', FILTER_VALIDATE_INT);

if (!$product_id || $product_id < 1) {
    $_SESSION['cart_message'] = 'Invalid product selected.';
    header('Location: ' . $redirect_url);
    exit;
}

$ip = get_ip();
$customer_id = isset($_SESSION['customer_id'])
    ? (int) $_SESSION['customer_id']
    : null;

if (empty($ip)) {
    $_SESSION['cart_message'] = 'Unable to identify your session.';
    header('Location: ' . $redirect_url);
    exit;
}

$cartController = new CartController();

if ($cartController->isInCart($product_id, $ip, $customer_id)) {
    $_SESSION['cart_message'] = 'This item is already in your cart.';
} else {
    $success = $cartController->addToCart($product_id, 1, $ip, $customer_id);

    $_SESSION['cart_message'] = $success
        ? 'Item added to your cart successfully.'
        : 'Unable to add this item to your cart. Please try again.';
}

header('Location: ' . $redirect_url);
exit;
