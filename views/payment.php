
<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../core/payment_config.php';
require_once __DIR__ . '/../controllers/CartController.php';

// Make sure the customer is logged in
if (!isset($_SESSION['customer_id']) || !isset($_SESSION['customer_email'])) {
    redirect(BASE_URL . '/views/login.php');
    exit;
}

// Get customer's cart
$cartController = new CartController();
$ip = get_ip();
$customer_id = (int) $_SESSION['customer_id'];

$items = $cartController->getCartItems($ip, $customer_id);

// Calculate total in USD
$usdTotal = 0;

foreach ($items as $item) {
    $usdTotal += (float)$item['product_price'] * (int)$item['qty'];
}

// Convert USD to GHS
$ghsTotal = round($usdTotal * USD_TO_GHS_RATE, 2);

// Paystack requires the amount in pesewas
$amountInPesewas = (int) round($ghsTotal * 100);

$paymentActionUrl = rtrim(BASE_URL, '/') .
    '/actions/process_payment_action.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #FEFDFF;
            color: #333;
            margin: 0;
            padding: 25px;
        }

        .payment-container {
            max-width: 850px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        h2 {
            color: #EB4511;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #FEFDFF;
        }

        .total {
            background: #FFCA3A;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .pay-button {
            background: #EB4511;
            color: white;
            border: none;
            padding: 14px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .pay-button:hover {
            opacity: 0.9;
        }

        .back-link {
            color: #EB4511;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="payment-container">

    <h2>Complete Your Payment</h2>

    <p>
        Customer:
        <strong><?= htmlspecialchars($_SESSION['customer_name'] ?? '') ?></strong>
    </p>

    <p>
        Email:
        <?= htmlspecialchars($_SESSION['customer_email']) ?>
    </p>

    <?php if (empty($items)): ?>

        <p>Your cart is empty.</p>
        <a class="back-link"
           href="<?= htmlspecialchars(BASE_URL) ?>/views/cart.php">
            Return to Cart
        </a>

    <?php else: ?>

        <h3>Order Summary</h3>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Unit Price (USD)</th>
                    <th>Quantity</th>
                    <th>Total (USD)</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($items as $item): ?>
                    <?php
                    $price = (float)$item['product_price'];
                    $qty = (int)$item['qty'];
                    $lineTotal = $price * $qty;
                    ?>

                    <tr>
                        <td>
                            <?= htmlspecialchars($item['product_title']) ?>
                        </td>

                        <td>
                            $<?= number_format($price, 2) ?>
                        </td>

                        <td>
                            <?= $qty ?>
                        </td>

                        <td>
                            $<?= number_format($lineTotal, 2) ?>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            <p>
                Subtotal:
                <strong>$<?= number_format($usdTotal, 2) ?></strong>
            </p>

            <p>
                Exchange rate:
                1 USD = <?= number_format(USD_TO_GHS_RATE, 2) ?> GHS
            </p>

            <h3>
                Amount to Pay:
                GHS <?= number_format($ghsTotal, 2) ?>
            </h3>
        </div>

        <?php if (!defined('PAYSTACK_PUBLIC_KEY') ||
                  strpos(PAYSTACK_PUBLIC_KEY, 'pk_test_') !== 0): ?>

            <p style="color:red;">
                Please configure your Paystack test public key.
            </p>

        <?php else: ?>

            <button type="button"
                    class="pay-button"
                    onclick="payWithPaystack()">
                Pay with Paystack
            </button>

        <?php endif; ?>

        <p>
            <a class="back-link"
               href="<?= htmlspecialchars(BASE_URL) ?>/views/cart.php">
                Return to Cart
            </a>
        </p>

    <?php endif; ?>

</div>

<?php if (!empty($items) && defined('PAYSTACK_PUBLIC_KEY')): ?>

<script src="https://js.paystack.co/v1/inline.js"></script>

<script>
function payWithPaystack() {

    var handler = PaystackPop.setup({

        key: <?= json_encode(PAYSTACK_PUBLIC_KEY) ?>,

        email: <?= json_encode($_SESSION['customer_email']) ?>,

        amount: <?= json_encode($amountInPesewas) ?>,

        currency: "GHS",

        ref: "ORDER_" + Date.now() + "_" +
             Math.random().toString(36).substring(2, 10),

        onSuccess: function(response) {

            window.location.href =
                <?= json_encode($paymentActionUrl) ?> +
                "?reference=" +
                encodeURIComponent(response.reference);

        },

        onCancel: function() {
            alert("Payment was cancelled. You have not completed your payment.");
        }

    });

    handler.openIframe();
}
</script>

<?php endif; ?>

</body>
</html>
