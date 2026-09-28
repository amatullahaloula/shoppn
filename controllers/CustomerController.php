<?php
/**
 * controllers/CustomerController.php
 * Sits between the action files and CustomerClass.
 * Actions call the controller. The controller calls the class.
 * The controller never talks to the database directly.
 */

require_once __DIR__ . '/../classes/CustomerClass.php';

class CustomerController
{
    private $customer;

    public function __construct()
    {
        $this->customer = new CustomerClass();
    }

    /**
     * Register a new customer.
     *
     * @param  array $data keys: name, email, password, country, city, contact
     * @return array ['success' => true, 'customer_id' => 12]
     *            or  ['success' => false, 'error' => 'message']
     */
    public function register($data)
    {
        // Basic server-side checks (JavaScript can be bypassed)
        $required = ['name', 'email', 'password', 'country', 'city', 'contact'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                return ['success' => false, 'error' => 'All fields are required'];
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email address'];
        }

        // 1. Is the email already taken?
        if ($this->customer->emailExists($data['email'])) {
            return ['success' => false, 'error' => 'Email already registered'];
        }

        // 2. Save the customer (CustomerClass hashes the password)
        $newId = $this->customer->addCustomer(
            $data['name'],
            $data['email'],
            $data['password'],
            $data['country'],
            $data['city'],
            $data['contact']
        );

        if ($newId === false) {
            return ['success' => false, 'error' => 'Registration failed. Please try again'];
        }

        return ['success' => true, 'customer_id' => $newId];
    }

    /**
     * Log a customer in.
     *
     * @param  string $email
     * @param  string $pass  the plain password typed by the user
     * @return array ['success' => true, 'customer' => [row from the database]]
     *            or  ['success' => false, 'error' => 'message']
     */
    public function login($email, $pass)
    {
        if (trim($email) === '' || $pass === '') {
            return ['success' => false, 'error' => 'Email and password are required'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email or password'];
        }

        $customer = $this->customer->login($email, $pass);

        // One message for both "unknown email" and "wrong password",
        // so nobody can use the form to find out which emails are registered
        if (!$customer) {
            return ['success' => false, 'error' => 'Invalid email or password'];
        }

        return ['success' => true, 'customer' => $customer];
    }
}