
<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../core/payment_config.php';
require_once __DIR__ . '/../controllers/CartController.php';

// Redirect failed payments to the failure page
$failPayment = function ($message) {
    $_SESSION['error'] = $message;

    header('Location: ' . rtrim(BASE_URL, '/') . '/views/payment_failed.php');
    exit;
};

// Customer must be logged in
if (
    !isset($_SESSION['customer_id']) ||
    !isset($_SESSION['customer_email'])
) {
    header('Location: ' . rtrim(BASE_URL, '/') . '/views/login.php');
    exit;
}

// Receive Paystack reference
$reference = trim($_GET['reference'] ?? '');

if ($reference === '') {
    $failPayment('Payment reference is missing.');
}

// Check secret key configuration
if (
    !defined('PAYSTACK_SECRET_KEY') ||
    strpos(PAYSTACK_SECRET_KEY, 'sk_test_') !== 0
) {
    $failPayment('Paystack test secret key is not configured correctly.');
}

// Check cURL availability
if (!function_exists('curl_init')) {
    $failPayment('PHP cURL is not enabled. Please enable it in XAMPP.');
}

// Verify transaction directly with Paystack
$url = 'https://api.paystack.co/transaction/verify/' .
       rawurlencode($reference);

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
        'Content-Type: application/json'
    ],
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

curl_close($ch);

if ($response === false || $curlError !== '') {
    error_log('Paystack cURL error: ' . $curlError);
    $failPayment('Could not contact Paystack. Please try again.');
}

// Decode Paystack response
$result = json_decode($response, true);

// Confirm Paystack API response
if (
    $httpCode !== 200 ||
    !is_array($result) ||
    empty($result['status']) ||
    empty($result['data'])
) {
    $failPayment('Paystack could not verify this payment.');
}

$transaction = $result['data'];

// Confirm successful transaction
if (($transaction['status'] ?? '') !== 'success') {
    $failPayment('Your payment was not successful.');
}

// Confirm transaction reference
if (($transaction['reference'] ?? '') !== $reference) {
    $failPayment('Payment reference does not match.');
}

// Confirm currency
if (($transaction['currency'] ?? '') !== PAYSTACK_CURRENCY) {
    $failPayment('Payment currency does not match.');
}

// Confirm customer email
$paidEmail = strtolower(
    trim($transaction['customer']['email'] ?? '')
);

$loggedInEmail = strtolower(
    trim($_SESSION['customer_email'])
);

if ($paidEmail === '' || $paidEmail !== $loggedInEmail) {
    $failPayment('The payment email does not match your account.');
}

// Paystack amount is in pesewas.
// Convert it to GHS before recording the payment.
$verifiedAmountGhs = ((int)$transaction['amount']) / 100;
$verifiedAmountGhs = round($verifiedAmountGhs, 2);

// Record the order only after successful server-side verification
$cartController = new CartController();

$customerId = (int)$_SESSION['customer_id'];
$ip = get_ip();

$orderId = $cartController->processPayment(
    $customerId,
    $ip,
    $reference,
    $verifiedAmountGhs
);

if (!$orderId) {
    error_log(
        'Paystack payment verified but order recording failed. Reference: ' .
        $reference
    );

    $failPayment(
        'Your payment was verified, but we could not record your order. ' .
        'Please contact the administrator with your payment reference: ' .
        $reference
    );
}

// Successful payment and order recording
header(
    'Location: ' . rtrim(BASE_URL, '/') .
    '/views/payment_success.php?order_id=' . (int)$orderId
);

exit;

?>