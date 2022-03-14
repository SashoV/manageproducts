<?php

namespace src\Products;
use src\Products\Product;
use src\Products\Dvd;
use src\Products\Book;
use src\Products\Furniture;
use src\ValidationRules\Validation;

class ProductFactory
{
    public static function build($postData)
    {
        $product = ucwords($postData['type']);
        $p = "\src\Products\\".$product;
        if (class_exists($p)) {
            $object = new $p();
            $v = new Validation();
            $v->validate($postData, $object->rules);
            $errors = $v->error();
            if($errors) {
                return $errors;
            }
            $object->setProperties($postData);
            return $object;
        } else {
            return false;
        }
    }

    public static function buildFromDb($data)
    {
        $product = ucwords($data['type']);
        $p = "\src\Products\\".$product;
        if (class_exists($p)) {
            $object = new $p();
            $object->setPropertiesFromDb($data);
            return $object;
        } else {
            return false;
        }
    }
}
