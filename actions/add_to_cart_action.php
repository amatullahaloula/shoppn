<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Remember the page the visitor came from.
$redirect_url = $_SERVER['HTTP_REFERER'] ?? '../index.php';

// Read the product ID from ?add_cart=N.
$product_id = filter_input(INPUT_GET, 'add_cart', FILTER_VALIDATE_INT);

if (!$product_id || $product_id < 1) {
    $_SESSION['cart_message'] = 'Invalid product selected.';
    header('Location: ' . $redirect_url);
    exit;
}

// Get the visitor's IP address.
$ip = get_ip();

if (empty($ip)) {
    $_SESSION['cart_message'] = 'Unable to identify your session. Please try again.';
    header('Location: ' . $redirect_url);
    exit;
}

$cartController = new CartController();

// Check whether this product is already in the cart.
if ($cartController->isInCart($product_id, $ip)) {
    $_SESSION['cart_message'] =
        'This item is already in your cart. Go to cart to update quantity.';

    header('Location: ' . $redirect_url);
    exit;
}

// Add the product with a quantity of 1.
$success = $cartController->addToCart($product_id, 1, $ip);

if ($success) {
    $_SESSION['cart_message'] = 'Item added to your cart successfully.';
} else {
    $_SESSION['cart_message'] = 'Unable to add this item to your cart. Please try again.';
}

// Return to the referring page.
header('Location: ' . $redirect_url);
exit;