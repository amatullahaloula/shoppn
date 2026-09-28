/**
 * js/validate.js
 * Client-side validation for the registration form (views/register.php).
 *
 * This is the "convenience" check: it gives instant feedback and saves a
 * round trip to the server. It is NOT security. actions/register_action.php
 * checks everything again, because JavaScript can be switched off or bypassed.
 */

document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('register-form');
    if (!form) {
        return;
    }

    // -----------------------------------------------------------------
    // Regular expressions
    // -----------------------------------------------------------------
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var phoneRegex = /^[0-9+\-\s]{7,15}$/;

    // The server accepts digits only (optional leading +) once spaces and
    // hyphens are removed, so the phone is also checked in that cleaned form.
    var cleanPhoneRegex = /^\+?[0-9]{7,14}$/;

    // Field limits. These match the columns in database/shoppn.sql
    var limits = {
        name: 100,
        email: 50,
        city: 30,
        contact: 15
    };
    var passwordMin = 8;
    var passwordMax = 72;
    var maxImageBytes = 2 * 1024 * 1024; // 2 MB

    // -----------------------------------------------------------------
    // Inline error helpers
    // -----------------------------------------------------------------
    function showError(field, message) {
        var errorId = 'error-' + field.name;
        var span = document.getElementById(errorId);

        if (!span) {
            span = document.createElement('span');
            span.id = errorId;
            span.className = 'error-msg';
            span.setAttribute('role', 'alert');
            field.insertAdjacentElement('afterend', span);
        }

        span.textContent = message;
        field.classList.add('invalid');
        field.setAttribute('aria-invalid', 'true');
    }

    function clearError(field) {
        var span = document.getElementById('error-' + field.name);
        if (span) {
            span.remove();
        }
        field.classList.remove('invalid');
        field.removeAttribute('aria-invalid');
    }

    // -----------------------------------------------------------------
    // One validator per field. Each returns an error message, or '' if OK.
    // The order here is the order the fields appear on the page.
    // -----------------------------------------------------------------
    var validators = {
        name: function (field) {
            var value = field.value.trim();
            if (value === '') return 'Full name is required';
            if (value.length < 2) return 'Name is too short';
            if (value.length > limits.name) return 'Name must be ' + limits.name + ' characters or fewer';
            return '';
        },

        email: function (field) {
            var value = field.value.trim();
            if (value === '') return 'Email is required';
            if (!emailRegex.test(value)) return 'Please enter a valid email address';
            if (value.length > limits.email) return 'Email must be ' + limits.email + ' characters or fewer';
            return '';
        },

        password: function (field) {
            // Passwords are not trimmed: spaces are part of what the user typed
            var value = field.value;
            if (value === '') return 'Password is required';
            if (value.length < passwordMin) return 'Password must be at least ' + passwordMin + ' characters';
            if (value.length > passwordMax) return 'Password must be ' + passwordMax + ' characters or fewer';
            return '';
        },

        country: function (field) {
            if (field.value === '') return 'Please select your country';
            return '';
        },

        city: function (field) {
            var value = field.value.trim();
            if (value === '') return 'City is required';
            if (value.length > limits.city) return 'City must be ' + limits.city + ' characters or fewer';
            return '';
        },

        contact: function (field) {
            var value = field.value.trim();
            if (value === '') return 'Contact number is required';
            if (!phoneRegex.test(value) || !cleanPhoneRegex.test(value.replace(/[\s\-]/g, ''))) {
                return 'Enter a valid number (7 to 14 digits, e.g. 0241234567)';
            }
            return '';
        },

        // Optional field: only checked if a file was chosen
        image: function (field) {
            if (field.files.length === 0) return '';
            var file = field.files[0];
            if (file.type.indexOf('image/') !== 0) return 'Please choose an image file';
            if (file.size > maxImageBytes) return 'Image must be 2 MB or smaller';
            return '';
        }
    };

    // -----------------------------------------------------------------
    // Remove a field's error as soon as the user edits it
    // -----------------------------------------------------------------
    Object.keys(validators).forEach(function (name) {
        var field = form.elements[name];
        if (!field) return;

        field.addEventListener('input', function () { clearError(field); });
        field.addEventListener('change', function () { clearError(field); });
    });

    // -----------------------------------------------------------------
    // Submit: validate everything, stop the form if anything fails
    // -----------------------------------------------------------------
    var submitButton = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function (event) {
        var firstInvalid = null;

        Object.keys(validators).forEach(function (name) {
            var field = form.elements[name];
            if (!field) return;

            var message = validators[name](field);
            if (message) {
                showError(field, message);
                if (!firstInvalid) firstInvalid = field;
            } else {
                clearError(field);
            }
        });

        if (firstInvalid) {
            event.preventDefault();
            firstInvalid.focus();
            return;
        }

        // Tidy the values before they are sent
        ['name', 'email', 'city'].forEach(function (name) {
            form.elements[name].value = form.elements[name].value.trim();
        });
        form.elements.contact.value = form.elements.contact.value.trim().replace(/[\s\-]/g, '');

        // Loading state: stops double clicks while the request is sent
        if (submitButton) {
            submitButton.dataset.originalText = submitButton.textContent;
            submitButton.textContent = 'Registering...';
            submitButton.disabled = true;
        }
    });

    // If the user comes back with the browser Back button, reset the button
    window.addEventListener('pageshow', function () {
        if (submitButton && submitButton.dataset.originalText) {
            submitButton.textContent = submitButton.dataset.originalText;
            submitButton.disabled = false;
        }
    });
});