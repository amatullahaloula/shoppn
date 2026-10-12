
<?php

require_once __DIR__ . '/../core/db_class.php';

class ProductClass extends Database
{
    // =========================
    // BRANDS
    // =========================

    public function addBrand($name)
    {
        $sql = "INSERT INTO brands (brand_name) VALUES (?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $name);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function getAllBrands()
    {
        $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $brands = [];

        while ($row = $result->fetch_assoc()) {
            $brands[] = $row;
        }

        $stmt->close();

        return $brands;
    }

    public function getBrandById($id)
    {
        $sql = "SELECT * FROM brands WHERE brand_id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $brand = $result->fetch_assoc();

        $stmt->close();

        return $brand;
    }

    public function updateBrand($id, $name)
    {
        $sql = "UPDATE brands SET brand_name = ? WHERE brand_id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("si", $name, $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    // =========================
    // CATEGORIES
    // =========================

    public function addCategory($name)
    {
        $sql = "INSERT INTO categories (cat_name) VALUES (?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $name);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function getAllCategories()
    {
        $sql = "SELECT * FROM categories ORDER BY cat_name ASC";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $categories = [];

        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        $stmt->close();

        return $categories;
    }

    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM categories WHERE cat_id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $category = $result->fetch_assoc();

        $stmt->close();

        return $category;
    }

    public function updateCategory($id, $name)
    {
        $sql = "UPDATE categories SET cat_name = ? WHERE cat_id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("si", $name, $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    // =========================
    // PRODUCTS
    // =========================

    // Add a product
    public function addProduct(
        $cat,
        $brand,
        $title,
        $price,
        $desc,
        $image_filename,
        $keywords
    ) {
        $sql = "INSERT INTO products
                (product_cat, product_brand, product_title,
                 product_price, product_desc, product_image,
                 product_keywords)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "iisdsss",
            $cat,
            $brand,
            $title,
            $price,
            $desc,
            $image_filename,
            $keywords
        );

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    // Update a product
    public function updateProduct(
        $id,
        $cat,
        $brand,
        $title,
        $price,
        $desc,
        $image_filename,
        $keywords
    ) {
        $sql = "UPDATE products
                SET product_cat = ?,
                    product_brand = ?,
                    product_title = ?,
                    product_price = ?,
                    product_desc = ?,
                    product_image = ?,
                    product_keywords = ?
                WHERE product_id = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "iisdsssi",
            $cat,
            $brand,
            $title,
            $price,
            $desc,
            $image_filename,
            $keywords,
            $id
        );

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    // Get one product with its category and brand names
    public function getProductById($id)
    {
        $sql = "SELECT p.*,
                       c.cat_name,
                       b.brand_name
                FROM products p
                LEFT JOIN categories c
                    ON p.product_cat = c.cat_id
                LEFT JOIN brands b
                    ON p.product_brand = b.brand_id
                WHERE p.product_id = ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        $stmt->close();

        return $product;
    }

    // Get all products and search products
    public function getAllProducts($search = '')
    {
        $sql = "SELECT p.*,
                       c.cat_name,
                       b.brand_name
                FROM products p
                LEFT JOIN categories c
                    ON p.product_cat = c.cat_id
                LEFT JOIN brands b
                    ON p.product_brand = b.brand_id";

        if ($search !== '') {
            $sql .= " WHERE p.product_title LIKE ?
                      OR p.product_keywords LIKE ?
                      OR p.product_desc LIKE ?";
        }

        $sql .= " ORDER BY p.product_id DESC";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return [];
        }

        if ($search !== '') {
            $term = '%' . $search . '%';
            $stmt->bind_param("sss", $term, $term, $term);
        }

        if (!$stmt->execute()) {
            $stmt->close();
            return [];
        }

        $result = $stmt->get_result();
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        $stmt->close();

        return $products;
    }

    // Search products
    public function searchProducts($search)
    {
        return $this->getAllProducts($search);
    }
}

?>