
<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

// Only allow POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/views/cart.php');
    exit;
}

// Get and validate the product ID.
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

if (!$product_id || $product_id < 1) {
    $_SESSION['error'] = 'Please select a valid product to remove.';
    header('Location: ' . BASE_URL . '/views/cart.php');
    exit;
}

// Identify the visitor's cart.
$ip = get_ip();

if (empty($ip)) {
    $_SESSION['error'] = 'Unable to identify your cart. Please try again.';
    header('Location: ' . BASE_URL . '/views/cart.php');
    exit;
}

// Remove the product through the controller.
$cartController = new CartController();
$success = $cartController->removeFromCart($product_id, $ip);

if ($success) {
    $_SESSION['success'] = 'Product removed from your cart.';
} else {
    $_SESSION['error'] = 'Could not remove the product. Please try again.';
}

// Return to the cart page.
header('Location: ' . BASE_URL . '/views/cart.php');
exit;
