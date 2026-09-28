<?php
/**
 * classes/CustomerClass.php
 * Model layer for customers. Extends the Database base class (MySQLi).
 * Every query that uses user input is a prepared statement.
 * No user value is ever joined into an SQL string.
 *
 * Uses $this->conn, the mysqli connection created in core/db_class.php.
 */

require_once __DIR__ . '/../core/db_class.php';

class CustomerClass extends Database
{
    public function __construct()
    {
        parent::__construct(); // opens the connection and sets $this->conn
    }

    /**
     * Check if an email is already registered.
     * @param  string $email
     * @return bool   true if the email exists, false if not (or on error)
     */
    public function emailExists($email)
    {
        try {
            $sql  = "SELECT customer_email FROM customer WHERE customer_email = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                $this->logDbError('emailExists', $this->conn->error);
                return false;
            }

            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();

            $exists = $stmt->num_rows > 0;
            $stmt->close();

            return $exists;
        } catch (Exception $e) {
            $this->logDbError('emailExists', $e->getMessage());
            return false;
        }
    }

    /**
     * Add a new customer.
     * The password is hashed here, before it reaches the query,
     * so the plain password is never stored.
     *
     * @return int|false the new customer_id if inserted, false otherwise
     */
    public function addCustomer($name, $email, $pass, $country, $city, $contact)
    {
        try {
            $hashed = password_hash($pass, PASSWORD_BCRYPT);

            $sql = "INSERT INTO customer
                        (customer_name, customer_email, customer_pass,
                         customer_country, customer_city, customer_contact)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                $this->logDbError('addCustomer', $this->conn->error);
                return false;
            }

            $stmt->bind_param('ssssss', $name, $email, $hashed, $country, $city, $contact);
            $ok    = $stmt->execute();
            $newId = $this->conn->insert_id;
            $stmt->close();

            return $ok ? (int) $newId : false;
        } catch (Exception $e) {
            $this->logDbError('addCustomer', $e->getMessage());
            return false;
        }
    }

    /**
     * Get one customer by email.
     * @param  string $email
     * @return array|false the customer row (all columns), or false if not found
     */
    public function getCustomerByEmail($email)
    {
        try {
            $sql  = "SELECT * FROM customer WHERE customer_email = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                $this->logDbError('getCustomerByEmail', $this->conn->error);
                return false;
            }

            $stmt->bind_param('s', $email);
            $stmt->execute();

            $result = $stmt->get_result();
            $row    = $result->fetch_assoc(); // null when no row matches
            $stmt->close();

            return $row ? $row : false;
        } catch (Exception $e) {
            $this->logDbError('getCustomerByEmail', $e->getMessage());
            return false;
        }
    }

    /**
     * Check a customer's login details.
     * @param  string $email
     * @param  string $pass  the plain password typed by the user
     * @return array|false the customer row on success, false on failure
     */
    public function login($email, $pass)
    {
        $row = $this->getCustomerByEmail($email);

        // No such email
        if (!$row) {
            return false;
        }

        // Compare the typed password with the stored bcrypt hash
        if (!password_verify($pass, $row['customer_pass'])) {
            return false;
        }

        // The hash is not needed after this point, so do not pass it around
        unset($row['customer_pass']);

        return $row;
    }

    /**
     * Write a database error to error/error.log
     * (uses log_error() from core.php when it is loaded).
     */
    private function logDbError($method, $message)
    {
        $text = "CustomerClass::$method failed: " . $message;

        if (function_exists('log_error')) {
            log_error($text);
        } else {
            error_log($text);
        }
    }
}