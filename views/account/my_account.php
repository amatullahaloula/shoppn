<?php
/**
 * views/account/my_account.php
 * TEMPORARY placeholder so the register/login redirect has somewhere to land.
 * Replace with the real account page later.
 */

require_once __DIR__ . '/../../core/core.php';
require_login(); // before any HTML output

$page_title = 'My Account';
require_once __DIR__ . '/../layout/header.php';
?>

<section class="account-page">
    <h2>My Account</h2>
    <p>Name: <?= e($_SESSION['customer_name'] ?? '') ?></p>
    <p>Email: <?= e($_SESSION['customer_email'] ?? '') ?></p>
    <p>Role: <?= is_admin() ? 'Admin' : 'Customer' ?></p>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>