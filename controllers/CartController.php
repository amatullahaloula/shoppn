
<?php
require_once __DIR__ . '/../classes/CartClass.php';

class CartController
{
    private $cartModel;

    public function __construct()
    {
        $this->cartModel = new CartClass();
    }

    public function addToCart($product_id, $qty, $ip, $customer_id = null)
    {
        return $this->cartModel->addToCart($product_id, $qty, $ip, $customer_id);
    }

    public function getCartItems($ip, $customer_id = null)
    {
        return $this->cartModel->getCartItems($ip, $customer_id);
    }

    public function getCartCount($ip, $customer_id = null)
    {
        return $this->cartModel->getCartCount($ip, $customer_id);
    }

    public function getCartTotal($ip, $customer_id = null)
    {
        return $this->cartModel->getCartTotal($ip, $customer_id);
    }

    public function isInCart($product_id, $ip, $customer_id = null)
    {
        return $this->cartModel->isInCart($product_id, $ip, $customer_id);
    }

    public function removeFromCart($product_id, $ip, $customer_id = null)
    {
        return $this->cartModel->removeFromCart($product_id, $ip, $customer_id);
    }

    public function updateQty($product_id, $new_qty, $ip, $customer_id = null)
    {
        return $this->cartModel->updateQty($product_id, $new_qty, $ip, $customer_id);
    }

    public function processPayment($customer_id, $ip, $reference, $amount)
    {
        return $this->cartModel->processPayment($customer_id, $ip, $reference, $amount);
    }

    public function getOrderById($order_id)
    {
        return $this->cartModel->getOrderById($order_id);
    }
}
?>
