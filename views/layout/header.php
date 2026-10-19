<?php
/**
 * views/layout/header.php
 * Shared page header: head, navigation bar and search box.
 */

require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/CartController.php';

$page_title = isset($page_title) ? $page_title : 'Home';
$search_q = isset($_GET['q']) ? $_GET['q'] : '';

// Get the cart summary for this visitor.
$ip = get_ip();
$cart_count = 0;
$cart_total = 0.0;

if (!empty($ip)) {
    $cartController = new CartController();
    $cart_count = $cartController->getCartCount($ip);
    $cart_total = $cartController->getCartTotal($ip);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shoppn | <?= e($page_title) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <?php if (isset($extra_head)) { echo $extra_head; } ?>
</head>
<body>

<header class="site-header">
    <div class="header-top">
        <a href="<?= BASE_URL ?>/index.php" class="logo">
            <img src="<?= BASE_URL ?>/images/logo.gif" alt="Shoppn logo">
        </a>

        <!-- Search box -->
        <form class="search-form"
              action="<?= BASE_URL ?>/views/search_results.php"
              method="GET">
            <input
                type="text"
                name="q"
                placeholder="Search products..."
                value="<?= e($search_q) ?>"
                required
            >
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Navigation bar -->
    <nav class="main-nav">
        <ul>
            <li><a href="<?= BASE_URL ?>/index.php">Home</a></li>
            <li><a href="<?= BASE_URL ?>/views/all_products.php">All Products</a></li>

            <?php if (is_admin()) { ?>
                <li><a href="<?= BASE_URL ?>/views/admin/brand.php">Brands</a></li>
                <li><a href="<?= BASE_URL ?>/views/admin/category.php">Categories</a></li>
                <li><a href="<?= BASE_URL ?>/views/admin/product.php">Products (Admin)</a></li>
            <?php } ?>

            <?php if (is_logged_in()) { ?>
                <li class="nav-welcome">
                    Welcome <?= e($_SESSION['customer_name'] ?? '') ?>
                </li>
                <li><a href="<?= BASE_URL ?>/views/account/my_account.php">My Account</a></li>
                <li><a href="<?= BASE_URL ?>/logout.php">Logout</a></li>
            <?php } else { ?>
                <li><a href="<?= BASE_URL ?>/views/register.php">Register</a></li>
                <li><a href="<?= BASE_URL ?>/views/login.php">Login</a></li>
            <?php } ?>
        </ul>
    </nav>

    <!-- Shopping cart summary -->
    <div class="cart-summary">
        <strong>Shopping Cart</strong>
        <span>— Total Items: <?= (int) $cart_count ?></span>
        <span>| Total Price: $<?= number_format($cart_total, 2) ?></span>
        <span> | </span>
        <a href="<?= BASE_URL ?>/views/cart.php">Go to Cart</a>
    </div>

    <?php
    // Display messages sent through the URL.
    if (!empty($_GET['status'])) { ?>
        <div class="alert alert-success">
            <?= e($_GET['status']) ?>
        </div>
    <?php }

    if (!empty($_GET['error'])) { ?>
        <div class="alert alert-error">
            <?= e($_GET['error']) ?>
        </div>
    <?php }

    // Display and clear session messages.
    if (!empty($_SESSION['success'])) { ?>
        <div class="alert alert-success">
            <?= e($_SESSION['success']) ?>
        </div>
    <?php
        unset($_SESSION['success']);
    }

    if (!empty($_SESSION['error'])) { ?>
        <div class="alert alert-error">
            <?= e($_SESSION['error']) ?>
        </div>
    <?php
        unset($_SESSION['error']);
    }

    if (!empty($_SESSION['cart_message'])) { ?>
        <div class="alert alert-success">
            <?= e($_SESSION['cart_message']) ?>
        </div>
    <?php
        unset($_SESSION['cart_message']);
    }
    ?>
</header>

<main class="site-main">