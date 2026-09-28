<?php
/**
 * actions/login_action.php
 * Processes the login form (POST only).
 * Flow: form -> this action -> CustomerController -> CustomerClass -> database
 */

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

// ---------------------------------------------------------------------
// 1. Only accept POST
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/login.php');
}

/**
 * Send the user back to the login page with an error message.
 */
function fail_login($message)
{
    $_SESSION['error'] = $message;
    redirect('views/login.php');
}

// ---------------------------------------------------------------------
// 2. Sanitise inputs
// ---------------------------------------------------------------------
$email = strip_tags(trim($_POST['email'] ?? ''));

// The password is NOT trimmed or stripped: it must stay exactly as typed
// or it will not match the stored hash.
$password = $_POST['password'] ?? '';

// ---------------------------------------------------------------------
// 3. Validate
// ---------------------------------------------------------------------
if ($email === '' || $password === '') {
    fail_login('Email and password are required');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 50) {
    fail_login('Invalid email or password');
}

// ---------------------------------------------------------------------
// 4. Log in through the controller
// ---------------------------------------------------------------------
$controller = new CustomerController();
$result     = $controller->login($email, $password);

// ---------------------------------------------------------------------
// 5. Failure: save the error and go back to the login form
// ---------------------------------------------------------------------
if (!$result['success']) {
    log_error('Failed login for ' . $email . ' from ' . get_ip());
    fail_login($result['error']);
}

// ---------------------------------------------------------------------
// 6. Success: store the customer in the session and go to the home page
// ---------------------------------------------------------------------
$customer = $result['customer'];

session_regenerate_id(true); // new session ID stops session fixation attacks

$_SESSION['customer_id']    = (int) $customer['customer_id'];
$_SESSION['customer_name']  = $customer['customer_name'];
$_SESSION['customer_email'] = $customer['customer_email'];
$_SESSION['user_role']      = (int) $customer['user_role'];

redirect('index.php');