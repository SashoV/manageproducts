<?php

namespace src\Controllers;

use src\Products\Product;
use src\Products\ProductType;
use src\Products\ProductFactory;

class ProductController
{

    public static function index() {
        return Product::getProducts();
    }

    public static function productTypes() {
        return ProductType::getAll();
    }

    public static function store($data)
    {
        $product = ProductFactory::build($data);
        try {
            if (is_object($product)) {
                if ($product->save()) {
                    return json_encode(['message' => 'Product saved.']);
                }
            } else if (is_array($product)) {
                return json_encode(['errors' => $product]);
            }
        } catch (\Throwable $th) {
            return json_encode(['error' => 'Oops, something went wrong. Please try again later.']);
        }
    }

    public static function massDelete($data) {
        return Product::massDelete($data);
    }

    public static function checkIsSkuUnique($data) 
    {
        return Product::checkIsSkuUnique($data);
    }
}
