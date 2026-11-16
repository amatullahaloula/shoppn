
<?php

require_once __DIR__ . '/../core/core.php';

if (empty($_SESSION['customer_id'])) {
    redirect('views/login.php');
}

$page_title = 'Payment Failed';

$error_message = $_SESSION['error'] ?? 
    'Your payment could not be completed. Please try again.';

unset($_SESSION['error']);

require_once __DIR__ . '/layout/header.php';

?>

<section class="payment-failed">

    <h1>Payment Unsuccessful</h1>

    <p><?= e($error_message) ?></p>

    <p>
        Don't worry. You can return to checkout and try again.
    </p>

    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:20px;">

        <a
            href="<?= BASE_URL ?>/views/checkout.php"
            style="display:inline-block; background:#EB4511; color:#FEFDFF; padding:12px 22px; border-radius:6px; text-decoration:none; font-weight:bold;"
        >
            Try Payment Again
        </a>

        <a
            href="<?= BASE_URL ?>/views/cart.php"
            style="display:inline-block; background:#FFCA3A; color:#333333; padding:12px 22px; border-radius:6px; text-decoration:none; font-weight:bold;"
        >
            Return to Cart
        </a>

    </div>

</section>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
