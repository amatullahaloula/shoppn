<?php
/**
 * actions/register_action.php
 * Processes the registration form (POST only).
 * Flow: form -> this action -> CustomerController -> CustomerClass -> database
 */

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

// ---------------------------------------------------------------------
// 1. Only accept POST
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/register.php');
}

// ---------------------------------------------------------------------
// 2. Field limits. These must match the columns in database/shoppn.sql
// ---------------------------------------------------------------------
$limits = [
    'name'    => 100,   // customer_name    VARCHAR(100)
    'email'   => 50,    // customer_email   VARCHAR(50)
    'country' => 30,    // customer_country VARCHAR(30)
    'city'    => 30,    // customer_city    VARCHAR(30)
    'contact' => 15,    // customer_contact VARCHAR(15)
];
$password_min = 8;
$password_max = 72;     // bcrypt only uses the first 72 bytes

/**
 * Send the user back to the register page with an error message.
 */
function fail_register($message)
{
    $_SESSION['error'] = $message;
    redirect('views/register.php');
}

// ---------------------------------------------------------------------
// 3. Sanitise inputs: trim() and strip_tags()
// ---------------------------------------------------------------------
$name    = strip_tags(trim($_POST['name']    ?? ''));
$email   = strip_tags(trim($_POST['email']   ?? ''));
$country = strip_tags(trim($_POST['country'] ?? ''));
$city    = strip_tags(trim($_POST['city']    ?? ''));
$contact = strip_tags(trim($_POST['contact'] ?? ''));

// Allow "024 123 4567" or "024-123-4567": remove spaces and hyphens, keep digits and +
$contact = preg_replace('/[\s\-]/', '', $contact);

// The password is NOT trimmed or stripped. Changing it would change what
// the user typed, and they could not log in with it later.
$password = $_POST['password'] ?? '';

// ---------------------------------------------------------------------
// 4. Validate: required fields
// ---------------------------------------------------------------------
if ($name === '' || $email === '' || $password === '' ||
    $country === '' || $city === '' || $contact === '') {
    fail_register('All fields are required');
}

// ---------------------------------------------------------------------
// 5. Validate: email format
// ---------------------------------------------------------------------
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail_register('Please enter a valid email address');
}

// ---------------------------------------------------------------------
// 6. Validate: lengths (mb_strlen counts characters, not bytes)
// ---------------------------------------------------------------------
$values = [
    'name'    => $name,
    'email'   => $email,
    'country' => $country,
    'city'    => $city,
    'contact' => $contact,
];

foreach ($limits as $field => $max) {
    if (mb_strlen($values[$field]) > $max) {
        fail_register(ucfirst($field) . " must be $max characters or fewer");
    }
}

if (strlen($password) < $password_min) {
    fail_register("Password must be at least $password_min characters");
}
if (strlen($password) > $password_max) {
    fail_register("Password must be $password_max characters or fewer");
}

// ---------------------------------------------------------------------
// 7. Validate: contact number (digits, optionally starting with +)
// ---------------------------------------------------------------------
if (!preg_match('/^\+?[0-9]{7,14}$/', $contact)) {
    fail_register('Please enter a valid contact number');
}

// ---------------------------------------------------------------------
// 8. Register through the controller
// ---------------------------------------------------------------------
$controller = new CustomerController();

$result = $controller->register([
    'name'     => $name,
    'email'    => $email,
    'password' => $password,
    'country'  => $country,
    'city'     => $city,
    'contact'  => $contact,
]);

// ---------------------------------------------------------------------
// 9. Failure: save the error and go back to the form
// ---------------------------------------------------------------------
if (!$result['success']) {
    fail_register($result['error']);
}

// ---------------------------------------------------------------------
// 10. Success: log the new customer in and go to their account
// ---------------------------------------------------------------------
session_regenerate_id(true); // new session ID stops session fixation attacks

$_SESSION['customer_id']   = $result['customer_id'];
$_SESSION['user_role']     = ROLE_CUSTOMER;   // new sign-ups are always customers
$_SESSION['customer_name'] = $name;           // used by the header greeting

redirect('views/account/my_account.php');