<?php
/**
 * logout.php
 * Ends the session and sends the user to the home page.
 */

require_once __DIR__ . '/core/core.php';

// Clear all session data
$_SESSION = [];

// Delete the session cookie in the browser
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy the session on the server
session_destroy();

redirect('index.php?status=You have been logged out');