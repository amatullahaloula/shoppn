<?php
/**
 * views/register.php
 * Registration form. Posts to actions/register_action.php.
 * The header (included below) shows $_SESSION['error'] once and then clears it.
 */

require_once __DIR__ . '/../core/core.php';

// A logged-in customer has no reason to see the register form
if (is_logged_in()) {
    redirect('views/account/my_account.php');
}

// Countries shown in the dropdown (each name must fit customer_country VARCHAR(30))
$countries = [
    'Algeria', 'Angola', 'Benin', 'Botswana', 'Burkina Faso', 'Cameroon',
    'Cape Verde', 'Chad', 'DR Congo', 'Egypt', 'Ethiopia', 'Gabon',
    'Gambia', 'Ghana', 'Guinea', 'Ivory Coast', 'Kenya', 'Liberia',
    'Libya', 'Mali', 'Mauritania', 'Morocco', 'Mozambique', 'Namibia',
    'Niger', 'Nigeria', 'Rwanda', 'Senegal', 'Sierra Leone', 'Somalia',
    'South Africa', 'Sudan', 'Tanzania', 'Togo', 'Tunisia', 'Uganda',
    'Zambia', 'Zimbabwe',
    'Canada', 'China', 'France', 'Germany', 'India', 'Italy', 'Netherlands',
    'Saudi Arabia', 'Spain', 'Turkey', 'United Arab Emirates',
    'United Kingdom', 'United States', 'Other',
];

$page_title = 'Register';
require_once __DIR__ . '/layout/header.php';
?>

<section class="form-container">
    <h2>Create an Account</h2>

    <!-- enctype is needed because of the optional image upload -->
    <form id="register-form" action="../actions/register_action.php" method="POST"
          enctype="multipart/form-data" novalidate>

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" maxlength="100"
                   placeholder="e.g. Kwame Mensah" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" maxlength="50"
                   placeholder="you@example.com" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   minlength="8" maxlength="72" required>
            <small>8 to 72 characters</small>
        </div>

        <div class="form-group">
            <label for="country">Country</label>
            <select id="country" name="country" required>
                <option value="">-- Select your country --</option>
                <?php foreach ($countries as $country) { ?>
                    <option value="<?= e($country) ?>"><?= e($country) ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="city">City</label>
            <input type="text" id="city" name="city" maxlength="30" required>
        </div>

        <div class="form-group">
            <label for="contact">Contact Number</label>
            <input type="tel" id="contact" name="contact" maxlength="15"
                   placeholder="e.g. 0241234567" required>
        </div>

        <div class="form-group">
            <label for="image">Profile Image (optional)</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <button type="submit" class="btn">Register</button>

        <p class="form-note">
            Already have an account?
            <a href="login.php">Log in here</a>
        </p>
    </form>
</section>

<script src="<?= BASE_URL ?>/js/validate.js"></script>

<?php require_once __DIR__ . '/layout/footer.php'; ?>