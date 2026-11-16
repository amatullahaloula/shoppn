
<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

if (empty($_SESSION['customer_id'])) {
    redirect('views/login.php');
}

$order_id = (int)($_GET['order_id'] ?? 0);

if ($order_id <= 0) {
    redirect('views/cart.php');
}

$cartController = new CartController();
$orderData = $cartController->getOrderById($order_id);

if (
    !$orderData ||
    (int)$orderData['order']['customer_id'] !== (int)$_SESSION['customer_id']
) {
    $_SESSION['error'] = 'Order not found.';
    redirect('index.php');
}

$order = $orderData['order'];
$items = $orderData['items'];

$usdTotal = 0;

foreach ($items as $item) {
    $usdTotal += (int)$item['qty'] * (float)$item['product_price'];
}

$page_title = 'Payment Successful';

require_once __DIR__ . '/layout/header.php';

?>

<section class="payment-success">

    <h1>Payment Successful!</h1>

    <p>Thank you for your purchase.</p>

    <h2>Order Invoice</h2>

    <p>
        <strong>Invoice Number:</strong>
        <?= e($order['invoice_no']) ?>
    </p>

    <p>
        <strong>Order ID:</strong>
        <?= e($order['order_id']) ?>
    </p>

    <p>
        <strong>Order Date:</strong>
        <?= e($order['order_date']) ?>
    </p>

    <p>
        <strong>Customer:</strong>
        <?= e($order['customer_name']) ?>
    </p>

    <p>
        <strong>Email:</strong>
        <?= e($order['customer_email']) ?>
    </p>

    <h3>Purchased Products</h3>

    <div class="cart-table-wrapper">

        <table class="cart-table">

            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price (USD)</th>
                    <th>Total (USD)</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($items as $item): ?>

                    <?php
                    $qty = (int)$item['qty'];
                    $price = (float)$item['product_price'];
                    $lineTotal = $qty * $price;
                    ?>

                    <tr>
                        <td><?= e($item['product_title']) ?></td>
                        <td><?= $qty ?></td>
                        <td>$<?= number_format($price, 2) ?></td>
                        <td>$<?= number_format($lineTotal, 2) ?></td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

            <tfoot>
                <tr>
                    <th colspan="3">Order Total (USD)</th>
                    <th>$<?= number_format($usdTotal, 2) ?></th>
                </tr>
            </tfoot>

        </table>

    </div>

    <p>
        <strong>Payment Status:</strong>
        <?= e($order['order_status']) ?>
    </p>

    <button type="button" onclick="window.print()">
        Print Invoice
    </button>

    <p>
        <a href="<?= BASE_URL ?>/index.php">
            Continue Shopping
        </a>
    </p>

</section>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
