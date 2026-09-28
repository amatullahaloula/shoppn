<?php
/**
 * views/layout/header.php
 * Shared page header: <head>, navigation bar and search box.
 * Included at the top of every view.
 *
 * Optional variables a view can set BEFORE including this file:
 *   $page_title  - text for the <title> tag
 *   $cart_count  - number shown next to the Cart link (set by the view/controller)
 *   $extra_head  - extra HTML for <head> (for example one more CSS file)
 *
 * No SQL here. Only HTML and simple session checks.
 */

require_once __DIR__ . '/../../core/core.php';

$page_title = isset($page_title) ? $page_title : 'Home';
$cart_count = isset($cart_count) ? (int) $cart_count : 0;
$search_q   = isset($_GET['q']) ? $_GET['q'] : '';
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
        <form class="search-form" action="<?= BASE_URL ?>/views/search_results.php" method="GET">
            <input type="text" name="q" placeholder="Search products..."
                   value="<?= e($search_q) ?>" required>
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Navigation bar -->
    <nav class="main-nav">
        <ul>
            <li><a href="<?= BASE_URL ?>/index.php">Home</a></li>
            <li><a href="<?= BASE_URL ?>/views/all_products.php">All Products</a></li>
            <li>
                <a href="<?= BASE_URL ?>/views/cart.php">
                    Cart<?php if ($cart_count > 0) { ?> (<?= $cart_count ?>)<?php } ?>
                </a>
            </li>

            <?php if (is_admin()) { ?>
                <!-- Admin only -->
                <li><a href="<?= BASE_URL ?>/views/admin/brand.php">Brands</a></li>
                <li><a href="<?= BASE_URL ?>/views/admin/category.php">Categories</a></li>
                <li><a href="<?= BASE_URL ?>/views/admin/product.php">Products (Admin)</a></li>
            <?php } ?>

            <?php if (is_logged_in()) { ?>
                <!-- Logged in: Welcome [name] | My Account | Logout -->
                <li class="nav-welcome">Welcome <?= e(isset($_SESSION['customer_name']) ? $_SESSION['customer_name'] : '') ?></li>
                <li class="nav-sep" aria-hidden="true">|</li>
                <li><a href="<?= BASE_URL ?>/views/account/my_account.php">My Account</a></li>
                <li class="nav-sep" aria-hidden="true">|</li>
                <li><a href="<?= BASE_URL ?>/logout.php">Logout</a></li>
            <?php } else { ?>
                <!-- Not logged in: Register | Login -->
                <li><a href="<?= BASE_URL ?>/views/register.php">Register</a></li>
                <li class="nav-sep" aria-hidden="true">|</li>
                <li><a href="<?= BASE_URL ?>/views/login.php">Login</a></li>
            <?php } ?>
        </ul>
    </nav>

    <?php
    // Messages sent back by the action files, e.g. ?status=Registered or ?error=Invalid login
    if (!empty($_GET['status'])) { ?>
        <div class="alert alert-success"><?= e($_GET['status']) ?></div>
    <?php }
    if (!empty($_GET['error'])) { ?>
        <div class="alert alert-error"><?= e($_GET['error']) ?></div>
    <?php }

    // Error stored in the session by an action file. Show it once, then clear it.
    if (!empty($_SESSION['error'])) { ?>
        <div class="alert alert-error"><?= e($_SESSION['error']) ?></div>
    <?php
        unset($_SESSION['error']);
    } ?>
</header>

<main class="site-main">