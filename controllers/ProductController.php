<?php

require_once __DIR__ . '/../classes/ProductClass.php';

class ProductController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new ProductClass();
    }


    // =========================
    // BRANDS
    // =========================

    public function addBrand($name)
    {
        return $this->productModel->addBrand($name);
    }

    public function getAllBrands()
    {
        return $this->productModel->getAllBrands();
    }

    public function getBrandById($id)
    {
        return $this->productModel->getBrandById($id);
    }

    public function updateBrand($id, $name)
    {
        return $this->productModel->updateBrand($id, $name);
    }


    // =========================
    // CATEGORIES
    // =========================

    public function addCategory($name)
    {
        return $this->productModel->addCategory($name);
    }

    public function getAllCategories()
    {
        return $this->productModel->getAllCategories();
    }

    public function getCategoryById($id)
    {
        return $this->productModel->getCategoryById($id);
    }

    public function updateCategory($id, $name)
    {
        return $this->productModel->updateCategory($id, $name);
    }
}

?>