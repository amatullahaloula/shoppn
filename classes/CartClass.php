
<?php

require_once __DIR__ . '/../core/db_class.php';

class CartClass extends Database
{
    // Check whether this product is already in the correct customer's cart
    public function isInCart($product_id, $ip, $customer_id = null)
    {
        if ($customer_id !== null && (int)$customer_id > 0) {
            $sql = "SELECT 1 FROM cart WHERE p_id = ? AND c_id = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                error_log($this->conn->error);
                return false;
            }

            $customer_id = (int)$customer_id;
            $stmt->bind_param("ii", $product_id, $customer_id);
        } else {
            $sql = "SELECT 1 FROM cart
                    WHERE p_id = ? AND c_id IS NULL AND ip_add = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                error_log($this->conn->error);
                return false;
            }

            $stmt->bind_param("is", $product_id, $ip);
        }

        $stmt->execute();
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

        $product_id = (int)$product_id;
        $qty = (int)$qty;

        $sql = "INSERT INTO cart (p_id, ip_add, c_id, qty)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log($this->conn->error);
            return false;
        }

        if ($customer_id !== null && (int)$customer_id > 0) {
            $customer_id = (int)$customer_id;
        } else {
            $customer_id = null;
        }

        $stmt->bind_param("isii", $product_id, $ip, $customer_id, $qty);

        $result = $stmt->execute();

        if (!$result) {
            error_log($stmt->error);
        }

        $stmt->close();

        return $result;
    }

    // Get cart items for a logged-in customer or guest
    public function getCartItems($ip, $customer_id = null)
    {
        $sql = "SELECT cart.*, products.product_title,
                       products.product_price, products.product_image
                FROM cart
                JOIN products ON cart.p_id = products.product_id";

        if ($customer_id !== null && (int)$customer_id > 0) {
            $sql .= " WHERE cart.c_id = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                error_log($this->conn->error);
                return [];
            }

            $customer_id = (int)$customer_id;
            $stmt->bind_param("i", $customer_id);
        } else {
            $sql .= " WHERE cart.c_id IS NULL AND cart.ip_add = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                error_log($this->conn->error);
                return [];
            }

            $stmt->bind_param("s", $ip);
        }

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

    // Count cart items
    public function getCartCount($ip, $customer_id = null)
    {
        if ($customer_id !== null && (int)$customer_id > 0) {
            $sql = "SELECT COUNT(*) AS total FROM cart WHERE c_id = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                error_log($this->conn->error);
                return 0;
            }

            $customer_id = (int)$customer_id;
            $stmt->bind_param("i", $customer_id);
        } else {
            $sql = "SELECT COUNT(*) AS total
                    FROM cart
                    WHERE c_id IS NULL AND ip_add = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                error_log($this->conn->error);
                return 0;
            }

            $stmt->bind_param("s", $ip);
        }

        if (!$stmt->execute()) {
            error_log($stmt->error);
            $stmt->close();
            return 0;
        }

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();

        return (int)($row['total'] ?? 0);
    }

    // Calculate cart total in USD
    public function getCartTotal($ip, $customer_id = null)
    {
        $items = $this->getCartItems($ip, $customer_id);
        $total = 0.0;

        foreach ($items as $item) {
            $total += (int)$item['qty'] * (float)$item['product_price'];
        }

        return $total;
    }

    // Remove a product from the correct cart
    public function removeFromCart($product_id, $ip, $customer_id = null)
    {
        if (!filter_var($product_id, FILTER_VALIDATE_INT) || $product_id < 1) {
            return false;
        }

        if ($customer_id !== null && (int)$customer_id > 0) {
            $sql = "DELETE FROM cart WHERE p_id = ? AND c_id = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                error_log($this->conn->error);
                return false;
            }

            $customer_id = (int)$customer_id;
            $stmt->bind_param("ii", $product_id, $customer_id);
        } else {
            $sql = "DELETE FROM cart
                    WHERE p_id = ? AND c_id IS NULL AND ip_add = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                error_log($this->conn->error);
                return false;
            }

            $stmt->bind_param("is", $product_id, $ip);
        }

        $result = $stmt->execute();

        if (!$result) {
            error_log($stmt->error);
        }

        $stmt->close();

        return $result;
    }

    // Update quantity in the correct cart
    public function updateQty($product_id, $new_qty, $ip, $customer_id = null)
    {
        if (
            !filter_var($product_id, FILTER_VALIDATE_INT) ||
            $product_id < 1 ||
            !filter_var($new_qty, FILTER_VALIDATE_INT) ||
            $new_qty < 1
        ) {
            return false;
        }

        if ($customer_id !== null && (int)$customer_id > 0) {
            $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                error_log($this->conn->error);
                return false;
            }

            $customer_id = (int)$customer_id;
            $stmt->bind_param("iii", $new_qty, $product_id, $customer_id);
        } else {
            $sql = "UPDATE cart
                    SET qty = ?
                    WHERE p_id = ? AND c_id IS NULL AND ip_add = ?";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                error_log($this->conn->error);
                return false;
            }

            $stmt->bind_param("iis", $new_qty, $product_id, $ip);
        }

        $result = $stmt->execute();

        if (!$result) {
            error_log($stmt->error);
        }

        $stmt->close();

        return $result;
    }

    // Save verified Paystack payment and create the order
    public function processPayment($customer_id, $ip, $reference, $amount)
    {
        require_once __DIR__ . '/../core/payment_config.php';

        $customer_id = (int)$customer_id;
        $amount = (float)$amount;
        $reference = trim($reference);

        if (
            $customer_id <= 0 ||
            $reference === '' ||
            $amount <= 0 ||
            !defined('USD_TO_GHS_RATE') ||
            USD_TO_GHS_RATE <= 0
        ) {
            return false;
        }

        $transactionStarted = false;

        try {
            $this->conn->begin_transaction();
            $transactionStarted = true;

            // Prevent duplicate payment references
            $check = $this->conn->prepare(
                "SELECT pay_id FROM payment WHERE paystack_reference = ?"
            );

            if (!$check) {
                throw new Exception($this->conn->error);
            }

            $check->bind_param("s", $reference);

            if (!$check->execute()) {
                throw new Exception($check->error);
            }

            $duplicate = $check->get_result()->num_rows > 0;
            $check->close();

            if ($duplicate) {
                throw new Exception("This payment reference has already been used.");
            }

            // Get only this logged-in customer's cart items
            $cart = $this->conn->prepare(
                "SELECT c.p_id, c.qty, p.product_price
                 FROM cart c
                 JOIN products p ON c.p_id = p.product_id
                 WHERE c.c_id = ?
                 FOR UPDATE"
            );

            if (!$cart) {
                throw new Exception($this->conn->error);
            }

            $cart->bind_param("i", $customer_id);

            if (!$cart->execute()) {
                throw new Exception($cart->error);
            }

            $result = $cart->get_result();
            $items = [];
            $usdTotal = 0.0;

            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
                $usdTotal += (int)$row['qty'] * (float)$row['product_price'];
            }

            $cart->close();

            if (empty($items)) {
                throw new Exception("Your cart is empty.");
            }

            // Convert USD total to GHS
            $expectedGhs = round($usdTotal * USD_TO_GHS_RATE, 2);

            // Compare payment amounts in pesewas
            $expectedPesewas = (int)round($expectedGhs * 100);
            $receivedPesewas = (int)round($amount * 100);

            if ($expectedPesewas !== $receivedPesewas) {
                throw new Exception("Payment amount does not match the cart.");
            }

            // Generate invoice details
            $invoice_no = mt_rand(100000, 999999);
            $order_date = date('Y-m-d');
            $order_status = 'paid';

            // Create order
            $order = $this->conn->prepare(
                "INSERT INTO orders
                 (customer_id, invoice_no, order_date, order_status)
                 VALUES (?, ?, ?, ?)"
            );

            if (!$order) {
                throw new Exception($this->conn->error);
            }

            $order->bind_param(
                "iiss",
                $customer_id,
                $invoice_no,
                $order_date,
                $order_status
            );

            if (!$order->execute()) {
                throw new Exception($order->error);
            }

            $order_id = $this->conn->insert_id;
            $order->close();

            // Save the products in orderdetails
            $detail = $this->conn->prepare(
                "INSERT INTO orderdetails (order_id, product_id, qty)
                 VALUES (?, ?, ?)"
            );

            if (!$detail) {
                throw new Exception($this->conn->error);
            }

            foreach ($items as $item) {
                $product_id = (int)$item['p_id'];
                $qty = (int)$item['qty'];

                $detail->bind_param("iii", $order_id, $product_id, $qty);

                if (!$detail->execute()) {
                    throw new Exception($detail->error);
                }
            }

            $detail->close();

            // Record the verified payment in GHS
            $currency = 'GHS';
            $payment_date = date('Y-m-d');

            $payment = $this->conn->prepare(
                "INSERT INTO payment
                 (amt, customer_id, order_id, currency,
                  payment_date, paystack_reference)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );

            if (!$payment) {
                throw new Exception($this->conn->error);
            }

            $payment->bind_param(
                "diisss",
                $expectedGhs,
                $customer_id,
                $order_id,
                $currency,
                $payment_date,
                $reference
            );

            if (!$payment->execute()) {
                throw new Exception($payment->error);
            }

            $payment->close();

            // Delete only this customer's cart items
            $delete = $this->conn->prepare(
                "DELETE FROM cart WHERE c_id = ?"
            );

            if (!$delete) {
                throw new Exception($this->conn->error);
            }

            $delete->bind_param("i", $customer_id);

            if (!$delete->execute()) {
                throw new Exception($delete->error);
            }

            $delete->close();

            $this->conn->commit();

            return $order_id;

        } catch (Throwable $e) {
            if ($transactionStarted) {
                $this->conn->rollback();
            }

            error_log("Payment processing error: " . $e->getMessage());

            return false;
        }
    }

    // Get order and invoice details
    public function getOrderById($order_id)
    {
        $order_id = (int)$order_id;

        if ($order_id <= 0) {
            return false;
        }

        // Get order and customer information
        $order = $this->conn->prepare(
            "SELECT o.order_id, o.invoice_no, o.order_date,
                    o.order_status, o.customer_id,
                    c.customer_name, c.customer_email
             FROM orders o
             JOIN customer c ON o.customer_id = c.customer_id
             WHERE o.order_id = ?"
        );

        if (!$order) {
            error_log($this->conn->error);
            return false;
        }

        $order->bind_param("i", $order_id);

        if (!$order->execute()) {
            error_log($order->error);
            $order->close();
            return false;
        }

        $orderResult = $order->get_result();
        $orderData = $orderResult->fetch_assoc();
        $order->close();

        if (!$orderData) {
            return false;
        }

        // Get products belonging to this order
        $itemsQuery = $this->conn->prepare(
            "SELECT od.product_id, od.qty,
                    p.product_title, p.product_price, p.product_image
             FROM orderdetails od
             JOIN products p ON od.product_id = p.product_id
             WHERE od.order_id = ?"
        );

        if (!$itemsQuery) {
            error_log($this->conn->error);
            return false;
        }

        $itemsQuery->bind_param("i", $order_id);

        if (!$itemsQuery->execute()) {
            error_log($itemsQuery->error);
            $itemsQuery->close();
            return false;
        }

        $itemsResult = $itemsQuery->get_result();
        $items = [];

        while ($row = $itemsResult->fetch_assoc()) {
            $items[] = $row;
        }

        $itemsQuery->close();

        return [
            'order' => $orderData,
            'items' => $items
        ];
    }
}

?>
