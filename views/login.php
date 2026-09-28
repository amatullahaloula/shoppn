<?php
/**
 * views/login.php
 * Login form. Posts to actions/login_action.php.
 * The header (included below) shows $_SESSION['error'] once and then clears it.
 */

require_once __DIR__ . '/../core/core.php';

// A logged-in customer has no reason to see the login form
if (is_logged_in()) {
    redirect('index.php');
}

$page_title = 'Login';
require_once __DIR__ . '/layout/header.php';
?>

<section class="form-container">
    <h2>Log In</h2>

    <form id="login-form" action="../actions/login_action.php" method="POST">

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" maxlength="50"
                   placeholder="you@example.com" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" maxlength="72" required>
        </div>

        <button type="submit" class="btn">Log In</button>
    </form>

    <p class="form-note">
        Don't have an account?
        <a href="register.php">Register here</a>
    </p>
</section>

<?php require_once __DIR__ . '/layout/footer.php'; ?>