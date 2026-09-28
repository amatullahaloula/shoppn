<?php
/**
 * core/core.php
 * Included on every page and every action.
 *
 * What it does:
 *  1. Sets the timezone and error logging
 *  2. Starts the session
 *  3. Sends CORS headers
 *  4. Requires db_class.php (which requires db_cred.php)
 *  5. Defines shared helper functions
 */

// ---------------------------------------------------------------------
// 1. Timezone and constants
// ---------------------------------------------------------------------
date_default_timezone_set('Africa/Accra');

// Absolute folder path of the project, e.g. C:/xampp/htdocs/shoppn
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// URL of the project in the browser, worked out automatically so it is right
// wherever the folder lives (htdocs, a subfolder, a school server, and so on).
// To force a value, define('BASE_URL', '/your/path') BEFORE including core.php.
if (!defined('BASE_URL')) {
    $detected = null;

    if (!empty($_SERVER['SCRIPT_FILENAME']) && !empty($_SERVER['SCRIPT_NAME'])) {
        $scriptFile = realpath($_SERVER['SCRIPT_FILENAME']);

        if ($scriptFile !== false) {
            $root      = rtrim(str_replace('\\', '/', BASE_PATH), '/');
            $scriptDir = str_replace('\\', '/', dirname($scriptFile));

            // Only works if the running script is inside the project folder
            if (strpos($scriptDir, $root) === 0) {
                // How many folders deep is the running script? (index.php = 0, views/login.php = 1)
                $relative = trim(substr($scriptDir, strlen($root)), '/');
                $depth    = ($relative === '') ? 0 : count(explode('/', $relative));

                // Take the script's URL folder and remove that many folders from the end
                $urlParts = explode('/', trim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));
                $urlParts = array_filter($urlParts, 'strlen');
                if ($depth <= count($urlParts)) {
                    $urlParts = array_slice($urlParts, 0, count($urlParts) - $depth);
                    $detected = '/' . implode('/', $urlParts);
                    $detected = rtrim($detected, '/');
                }
            }
        }
    }

    define('BASE_URL', $detected !== null ? $detected : '/shoppn');
}

// Roles stored in $_SESSION['user_role']
if (!defined('ROLE_ADMIN')) {
    define('ROLE_ADMIN', 1);
}
if (!defined('ROLE_CUSTOMER')) {
    define('ROLE_CUSTOMER', 2);
}

// ---------------------------------------------------------------------
// 2. Error logging (writes to error/error.log, hides errors from users)
// ---------------------------------------------------------------------
$logDir = BASE_PATH . '/error';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', $logDir . '/error.log');
error_reporting(E_ALL);

/**
 * Write a custom message to error/error.log
 * Example: log_error('Login failed for ' . $email);
 */
function log_error($message)
{
    $file = BASE_PATH . '/error/error.log';
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    error_log($line, 3, $file);
}

// Catch any uncaught exception, log it, and show a safe message
set_exception_handler(function ($e) {
    log_error('Uncaught exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    echo 'Something went wrong. Please try again later.';
});

// ---------------------------------------------------------------------
// 3. Session
// ---------------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------------------
// 4. CORS (needed only if JS from another origin calls your actions)
// ---------------------------------------------------------------------
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Browsers send an OPTIONS "preflight" request first; answer it and stop
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ---------------------------------------------------------------------
// 5. Database base class
// ---------------------------------------------------------------------
require_once __DIR__ . '/db_class.php';

// ---------------------------------------------------------------------
// 6. Shared helper functions
// ---------------------------------------------------------------------

/**
 * Get the visitor's IP address.
 * The cart uses this to track guests who are not logged in.
 */
function get_ip()
{
    $keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];

    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            // X_FORWARDED_FOR can hold a list: take the first one
            $ip = trim(explode(',', $_SERVER[$key])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '0.0.0.0';
}

/**
 * Send the browser to another page and stop the script.
 * Example: redirect('views/login.php?error=Invalid login');
 * Full URLs (http...) and paths starting with / are used as they are.
 */
function redirect($url)
{
    if (!preg_match('#^(https?://|/)#i', $url)) {
        $url = BASE_URL . '/' . ltrim($url, '/');
    }
    header('Location: ' . $url);
    exit;
}

/**
 * Is a customer logged in?
 */
function is_logged_in()
{
    return isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id']);
}

/**
 * Is the logged-in user an admin?
 */
function is_admin()
{
    return is_logged_in()
        && isset($_SESSION['user_role'])
        && (int) $_SESSION['user_role'] === ROLE_ADMIN;
}

/**
 * Block a page unless the user is logged in.
 * Put at the top of pages like cart, checkout, my_account.
 */
function require_login()
{
    if (!is_logged_in()) {
        $_SESSION['error'] = 'Please log in first';
        redirect('views/login.php');
    }
}

/**
 * Block a page unless the user is an admin.
 * Put at the top of views/admin/* and admin actions.
 * Non-admins (logged in or not) are sent to the home page with an error.
 */
function require_admin()
{
    if (!is_admin()) {
        $_SESSION['error'] = 'You do not have permission to view that page';
        redirect('index.php');
    }
}

/**
 * Clean text before showing it in HTML (stops XSS).
 * Example: echo e($product['product_title']);
 */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Read a POST value safely (trimmed, empty string if missing).
 * Example: $email = post('email');
 */
function post($key, $default = '')
{
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}