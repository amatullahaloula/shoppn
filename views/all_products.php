
<?php
require_once __DIR__ . '/../controllers/ProductController.php';

$controller = new ProductController();

$page_title = 'All Products';

// Support searches from both the shared navbar and this page.
$search = trim($_GET['q'] ?? $_GET['search'] ?? '');

$products = $controller->getAllProducts($search);

// Load the existing shared header and navbar.
require_once __DIR__ . '/layout/header.php';
?>

<style>
    .products-container {
        width: 90%;
        max-width: 1200px;
        margin: 30px auto;
    }

    .products-container h2 {
        margin-bottom: 10px;
    }

    .product-search-form {
        display: flex;
        gap: 10px;
        margin: 25px 0;
    }

    .product-search-form input {
        flex: 1;
        min-width: 0;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    .product-search-form button {
        background: #EB4511;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 6px;
        cursor: pointer;
    }

    .product-search-form button:hover {
        background: #c9360b;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 22px;
    }

    .product-card {
        background: white;
        border: 1px solid #eee;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 10px #00000012;
        padding-bottom: 18px;
    }

    .product-card img {
        width: 100%;
        height: 210px;
        object-fit: contain;
        background: #f7f7f7;
        padding: 10px;
    }

    .product-card h3,
    .product-card p {
        margin: 12px 16px;
    }

    .product-price {
        color: #EB4511;
        font-weight: bold;
        font-size: 20px;
    }

    .product-category {
        display: inline-block;
        background: #FFCA3A;
        padding: 5px 9px;
        border-radius: 5px;
        margin: 0 16px;
        font-size: 13px;
    }

    .add-cart-button {
        display: inline-block;
        background: #EB4511;
        color: white;
        padding: 11px 16px;
        margin: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
    }

    .add-cart-button:hover {
        background: #c9360b;
    }

    .no-products {
        background: #fff3cd;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
    }

    @media (max-width: 500px) {
        .product-search-form {
            flex-direction: column;
        }
    }
</style>

<div class="products-container">
    <h2>Our Products</h2>
    <p>Browse our available products below.</p>

    <form class="product-search-form" method="GET" action="all_products.php">
        <input
            type="text"
            name="search"
            placeholder="Search products..."
            value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
        >
        <button type="submit">Search</button>
    </form>

    <?php if (empty($products)): ?>

        <div class="no-products">
            No products found. Try another search or add a product from the admin page.
        </div>

    <?php else: ?>

        <div class="products-grid">

            <?php foreach ($products as $product): ?>

                <?php
                $imageName = basename($product['product_image'] ?? '');
                $imagePath = __DIR__ . '/../images/products/' . $imageName;
                $imageUrl = '../images/products/' . rawurlencode($imageName);
                $productTitle = $product['product_title'] ?? 'Product';
                ?>

                <article class="product-card">

                    <?php if ($imageName !== '' && is_file($imagePath)): ?>

                        <img
                            src="<?php echo htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>"
                            alt="<?php echo htmlspecialchars($productTitle, ENT_QUOTES, 'UTF-8'); ?>"
                        >

                    <?php else: ?>

                        <img
                            src="https://placehold.co/400x300?text=Product"
                            alt="Product image unavailable"
                        >

                    <?php endif; ?>

                    <h3>
                        <?php
                        echo htmlspecialchars(
                            $productTitle,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </h3>

                    <p class="product-price">
                        GH₵ <?php echo number_format((float)($product['product_price'] ?? 0), 2); ?>
                    </p>

                    <span class="product-category">
                        <?php
                        echo htmlspecialchars(
                            $product['cat_name'] ?? 'Uncategorized',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </span>

                    <p>
                        <?php
                        echo htmlspecialchars(
                            $product['product_desc'] ?? 'No description available.',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </p>

                    <a
                        class="add-cart-button"
                        href="../actions/add_to_cart_action.php?add_cart=<?php echo (int)$product['product_id']; ?>"
                    >
                        Add to Cart
                    </a>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>
</div>

</main>
</body>
</html>
