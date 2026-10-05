<?php

require_once __DIR__ . '/../core/db_class.php';

class ProductClass extends Database
{
    // =========================
    // BRANDS
    // =========================

    // Add a new brand
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

    // Get all brands
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

    // Get one brand
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

    // Update brand
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

    // Add a new category
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

    // Get all categories
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

    // Get one category
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

    // Update category
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
}

?>