
<?php
require_once __DIR__ . '/../controllers/ProductController.php';

$controller = new ProductController();

$search = trim($_GET['search'] ?? '');
$products = $controller->getAllProducts($search);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products | ShopPN</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #FEFDFF;
            color: #222;
        }

        header {
            background: #EB4511;
            color: white;
            padding: 22px 6%;
            text-align: center;
        }

        header h1 {
            margin: 0 0 8px;
        }

        nav {
            background: #222;
            padding: 14px;
            text-align: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 12px;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin: 25px 0;
        }

        .search-form input {
            flex: 1;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .search-form button {
            background: #EB4511;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
        }

        .products {
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

        .price {
            color: #EB4511;
            font-weight: bold;
            font-size: 20px;
        }

        .category {
            display: inline-block;
            background: #FFCA3A;
            padding: 5px 9px;
            border-radius: 5px;
            margin: 0 16px;
            font-size: 13px;
        }

        .empty {
            background: #fff3cd;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        footer {
            margin-top: 40px;
            background: #222;
            color: white;
            text-align: center;
            padding: 20px;
        }

        @media (max-width: 500px) {
            .search-form {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
<header>
    <h1>ShopPN</h1>
    <p>Find something you love</p>
</header>

<nav>
    <a href="../index.php">Home</a>
    <a href="product.php">Products</a>
</nav>

<main class="container">
    <h2>Our Products</h2>
    <p>Browse our available products below.</p>

    <form class="search-form" method="GET" action="product.php">
        <input
            type="text"
            name="search"
            placeholder="Search products..."
            value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
        >
        <button type="submit">Search</button>
    </form>

    <?php if (empty($products)): ?>
        <div class="empty">
            No products found. Try another search or add a product from the admin page.
        </div>
    <?php else: ?>
        <div class="products">
            <?php foreach ($products as $product): ?>
                <article class="product-card">
                    <?php
                    $imageName = basename($product['product_image'] ?? '');
                    $imagePath = __DIR__ . '/../images/products/' . $imageName;
                    $imageUrl = '../images/products/' . rawurlencode($imageName);
                    ?>

                    <?php if ($imageName !== '' && is_file($imagePath)): ?>
                        <img
                            src="<?php echo htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>"
                            alt="<?php echo htmlspecialchars($product['product_title'] ?? 'Product', ENT_QUOTES, 'UTF-8'); ?>"
                        >
                    <?php else: ?>
                        <img
                            src="https://placehold.co/400x300?text=Product"
                            alt="Product image unavailable"
                        >
                    <?php endif; ?>

                    <h3>
                        <?php echo htmlspecialchars($product['product_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                    </h3>

                    <p class="price">
                        GH₵ <?php echo number_format((float)($product['product_price'] ?? 0), 2); ?>
                    </p>

                    <span class="category">
                        <?php echo htmlspecialchars($product['cat_name'] ?? 'Uncategorized', ENT_QUOTES, 'UTF-8'); ?>
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
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> ShopPN. All rights reserved.</p>
</footer>
</body>
</html>