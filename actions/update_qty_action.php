
<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

// Only allow POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/views/cart.php');
    exit;
}

// Get the visitor's IP address.
$ip = get_ip();

if (empty($ip)) {
    $_SESSION['error'] = 'Unable to identify your cart. Please try again.';
    header('Location: ' . BASE_URL . '/views/cart.php');
    exit;
}

// Read the submitted quantities and product IDs.
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

// Process each submitted product.
foreach ($product_ids as $product_id_value) {

    // Validate the product ID.
    $product_id = filter_var(
        $product_id_value,
        FILTER_VALIDATE_INT
    );

    if ($product_id === false || $product_id < 1) {
        $has_error = true;
        continue;
    }

    // Find the quantity submitted for this product.
    if (!array_key_exists($product_id, $quantities)) {
        $has_error = true;
        continue;
    }

    $quantity = filter_var(
        $quantities[$product_id],
        FILTER_VALIDATE_INT
    );

    // Quantity must be a positive integer.
    if ($quantity === false || $quantity < 1) {
        $has_error = true;
        continue;
    }

    // Update the quantity through the controller.
    if ($cartController->updateQty($product_id, $quantity, $ip)) {
        $updated++;
    } else {
        $has_error = true;
    }
}

// Show the appropriate message.
if ($has_error) {
    $_SESSION['error'] = $updated > 0
        ? 'Some quantities were updated, but others could not be updated.'
        : 'Some quantities could not be updated. Check your entries and try again.';
} else {
    $_SESSION['success'] = 'Cart quantities updated successfully.';
}

// Return to the cart page.
header('Location: ' . BASE_URL . '/views/cart.php');
exit;
