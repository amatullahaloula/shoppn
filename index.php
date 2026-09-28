<?php
/**
 * index.php
 * Entry point of the shoppn site.
 * Loads the core (session, database class, helpers), then shows the home view.
 *
 * TEMPORARY: until views/home.php is written, a simple test page is shown
 * so you can try the navigation. Once home.php exists, it is loaded instead.
 */

require_once __DIR__ . '/core/core.php';

// Real home page (used automatically once you create it)
if (file_exists(__DIR__ . '/views/home.php')) {
    require_once __DIR__ . '/views/home.php';
    exit;
}

// ---------------------------------------------------------------------
// Temporary test page
// ---------------------------------------------------------------------
$page_title = 'Home';
require_once __DIR__ . '/views/layout/header.php';
?>

<section class="test-page">
    <h2>Shoppn</h2>
    <p>The site is running. Use the navigation bar above to test what has been built so far.</p>

    <h3>Session status (for testing only, remove later)</h3>
    <ul>
        <li>Logged in: <strong><?= is_logged_in() ? 'Yes' : 'No' ?></strong></li>
        <li>Admin: <strong><?= is_admin() ? 'Yes' : 'No' ?></strong></li>
        <?php if (is_logged_in()) { ?>
            <li>Customer ID: <?= e($_SESSION['customer_id']) ?></li>
            <li>Name: <?= e($_SESSION['customer_name'] ?? '') ?></li>
            <li>Email: <?= e($_SESSION['customer_email'] ?? '') ?></li>
            <li>Role: <?= e($_SESSION['user_role'] ?? '') ?> (1 = admin, 2 = customer)</li>
        <?php } ?>
    </ul>

    <h3>What you can test now</h3>
    <ul>
        <li><a href="<?= BASE_URL ?>/views/register.php">Register</a></li>
        <li><a href="<?= BASE_URL ?>/views/login.php">Login</a></li>
        <li><a href="<?= BASE_URL ?>/views/account/my_account.php">My Account</a> (needs login)</li>
        <li><a href="<?= BASE_URL ?>/logout.php">Logout</a></li>
    </ul>
    <p>Other links in the navigation (All Products, Cart, admin pages) will give a
       "not found" error until those files are written.</p>
</section>

<?php require_once __DIR__ . '/views/layout/footer.php'; ?>