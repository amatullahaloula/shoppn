
<?php

require_once __DIR__ . '/../core/db_class.php';

class CartClass extends Database
{
    // Check whether a product is already in the cart
    public function isInCart($product_id, $ip)
    {
        $sql = "SELECT 1 FROM cart WHERE p_id = ? AND ip_add = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        $stmt->bind_param("is", $product_id, $ip);

        if (!$stmt->execute()) {
            error_log($stmt->error);
            $stmt->close();
            return false;
        }

        $stmt->store_result();
        $exists = $stmt->num_rows > 0;

        $stmt->close();

        return $exists;
    }

    // Add a product to the cart
    public function addToCart($product_id, $qty, $ip, $customer_id = null)
    {
        if (
            !filter_var($product_id, FILTER_VALIDATE_INT) ||
            $product_id < 1 ||
            !filter_var($qty, FILTER_VALIDATE_INT) ||
            $qty < 1
        ) {
            return false;
        }

        $sql = "INSERT INTO cart (p_id, ip_add, c_id, qty)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        $product_id = (int) $product_id;
        $qty = (int) $qty;

        $stmt->bind_param("isii", $product_id, $ip, $customer_id, $qty);

        $result = $stmt->execute();

        if (!$result) {
            error_log($stmt->error);
        }

        $stmt->close();

        return $result;
    }

    // Get all cart items for an IP address
    public function getCartItems($ip)
    {
        $sql = "SELECT cart.*, products.product_title,
                       products.product_price, products.product_image
                FROM cart
                JOIN products ON cart.p_id = products.product_id
                WHERE cart.ip_add = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return [];
        }

        $stmt->bind_param("s", $ip);

        if (!$stmt->execute()) {
            error_log($stmt->error);
            $stmt->close();
            return [];
        }

        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $items;
    }

    // Count the number of cart rows
    public function getCartCount($ip)
    {
        $sql = "SELECT COUNT(*) AS total FROM cart WHERE ip_add = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return 0;
        }

        $stmt->bind_param("s", $ip);

        if (!$stmt->execute()) {
            error_log($stmt->error);
            $stmt->close();
            return 0;
        }

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();

        return (int) ($row['total'] ?? 0);
    }

    // Calculate the total price of the cart
    public function getCartTotal($ip)
    {
        $items = $this->getCartItems($ip);
        $total = 0.0;

        foreach ($items as $item) {
            $total += (int) $item['qty'] * (float) $item['product_price'];
        }

        return $total;
    }

    // Remove a product belonging to this IP address
    public function removeFromCart($product_id, $ip)
    {
        if (
            !filter_var($product_id, FILTER_VALIDATE_INT) ||
            $product_id < 1
        ) {
            return false;
        }

        $sql = "DELETE FROM cart WHERE p_id = ? AND ip_add = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        $stmt->bind_param("is", $product_id, $ip);
        $result = $stmt->execute();

        if (!$result) {
            error_log($stmt->error);
        }

        $stmt->close();

        return $result;
    }

    // Update a product quantity; quantity must be at least 1
    public function updateQty($product_id, $new_qty, $ip)
    {
        if (
            !filter_var($product_id, FILTER_VALIDATE_INT) ||
            $product_id < 1 ||
            !filter_var($new_qty, FILTER_VALIDATE_INT) ||
            $new_qty < 1
        ) {
            return false;
        }

        $sql = "UPDATE cart
                SET qty = ?
                WHERE p_id = ? AND ip_add = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        $stmt->bind_param("iis", $new_qty, $product_id, $ip);
        $result = $stmt->execute();

        if (!$result) {
            error_log($stmt->error);
        }

        $stmt->close();

        return $result;
    }
}
