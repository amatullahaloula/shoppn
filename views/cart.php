
<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

$page_title = 'Shopping Cart';

$ip = get_ip();
$cartController = new CartController();

$items = $cartController->getCartItems($ip);

$subtotal = 0;

foreach ($items as $item) {
    $subtotal += (int) $item['qty'] * (float) $item['product_price'];
}

require_once __DIR__ . '/layout/header.php';
?>

<section class="cart-page">

    <h1>Shopping Cart</h1>

    <?php if (empty($items)) { ?>

        <p>Your shopping cart is empty.</p>

        <a href="<?= BASE_URL ?>/index.php">
            Continue Shopping
        </a>

    <?php } else { ?>

        <!-- REMOVE FORM: handles product removal only -->
        <form
            method="POST"
            action="<?= BASE_URL ?>/actions/remove_from_cart_action.php"
        >

            <div class="cart-table-wrapper">

                <table class="cart-table">

                    <thead>
                        <tr>
                            <th>Remove</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Line Total</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($items as $item) {

                            $product_id = (int) $item['p_id'];
                            $qty = (int) $item['qty'];
                            $price = (float) $item['product_price'];
                            $line_total = $qty * $price;
                            $image = $item['product_image'] ?? '';

                        ?>

                            <tr>

                                <td>
                                    <input
                                        type="checkbox"
                                        name="remove[]"
                                        value="<?= $product_id ?>"
                                        aria-label="Remove <?= e($item['product_title']) ?>"
                                    >
                                </td>

                                <td class="cart-product">

                                    <?php if ($image !== '') { ?>

                                        <img
                                            src="<?= BASE_URL ?>/images/products/<?= rawurlencode(basename($image)) ?>"
                                            alt="<?= e($item['product_title']) ?>"
                                            width="80"
                                            height="80"
                                        >

                                    <?php } ?>

                                    <span>
                                        <?= e($item['product_title']) ?>
                                    </span>

                                </td>

                                <td>
                                    <?= $qty ?>
                                </td>

                                <td>
                                    $<?= number_format($price, 2) ?>
                                </td>

                                <td>
                                    $<?= number_format($line_total, 2) ?>
                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="4">Subtotal</th>
                            <th>$<?= number_format($subtotal, 2) ?></th>
                        </tr>
                    </tfoot>

                </table>

            </div>

            <button type="submit">
                Remove Selected Items
            </button>

        </form>

        <!-- UPDATE FORM: handles quantity updates only -->
        <form
            method="POST"
            action="<?= BASE_URL ?>/actions/update_qty_action.php"
        >

            <h2>Update Quantities</h2>

            <div class="cart-table-wrapper">

                <table class="cart-table">

                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($items as $item) {

                            $product_id = (int) $item['p_id'];
                            $qty = (int) $item['qty'];

                        ?>

                            <tr>

                                <td>
                                    <?= e($item['product_title']) ?>
                                </td>

                                <td>

                                    <input
                                        type="number"
                                        name="qty[<?= $product_id ?>]"
                                        value="<?= $qty ?>"
                                        min="1"
                                        step="1"
                                        required
                                        aria-label="Quantity for <?= e($item['product_title']) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="update[]"
                                        value="<?= $product_id ?>"
                                    >

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

            <button type="submit">
                Update Quantity
            </button>

        </form>

        <div class="cart-actions">

            <a href="<?= BASE_URL ?>/index.php">
                Continue Shopping
            </a>

            <a href="<?= BASE_URL ?>/views/checkout.php">
                Checkout
            </a>

        </div>

    <?php } ?>

</section>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
