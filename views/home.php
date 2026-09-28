<?php
/**
 * views/home.php
 * The real home / landing page.
 * Loaded automatically by index.php once this file exists.
 *
 * Product listing will be wired in once ProductClass / ProductController
 * exist. For now this page is fully functional (header, nav, session,
 * footer) but shows a placeholder where featured products will go.
 */

require_once __DIR__ . '/../core/core.php';

$page_title = 'Home';
require_once __DIR__ . '/layout/header.php';
?>

<section class="hero">
    <img src="<?= BASE_URL ?>/images/ad_banner.gif" alt="Shoppn promotions" class="hero-banner">
    <h1>Welcome to Shoppn</h1>
    <p>Quality products, delivered to your door.</p>
    <a href="<?= BASE_URL ?>/views/all_products.php" class="btn">Shop All Products</a>
</section>

<section class="featured-products">
    <h2>Featured Products</h2>

    <?php
    // TODO: replace with ProductController::getFeatured() once ProductClass exists
    $products = [];
    ?>

    <?php if (empty($products)) { ?>
        <p>No products to show yet. Once products are added in Admin &rarr; Products, they will appear here.</p>
    <?php } else { ?>
        <div class="product-grid">
            <?php foreach ($products as $product) { ?>
                <div class="product-card">
                    <a href="<?= BASE_URL ?>/views/single_product.php?id=<?= (int) $product['product_id'] ?>">
                        <img src="<?= BASE_URL ?>/images/products/<?= e($product['product_image']) ?>"
                             alt="<?= e($product['product_title']) ?>">
                        <h3><?= e($product['product_title']) ?></h3>
                        <p class="price">GHS <?= number_format($product['product_price'], 2) ?></p>
                    </a>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</section>

<?php require_once __DIR__ . '/layout/footer.php'; ?>