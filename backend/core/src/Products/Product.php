<?php

namespace src\Products;

use src\Db\Connection;
use src\Products\ProductFactory;

abstract class Product

{
    public $id;
    public $sku;
    public $name;
    public $price;
    public $type;
    public $attribute;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public abstract function save();

    public abstract function setProperties($postData);


    public static function getProducts()
    {

        try {
            $conn = new Connection();

            $sql = 'SELECT products.id, products.name, products.sku, products.price, products.spec_attr_value, special_attributes.spec_attr_name, special_attributes.unit, special_attributes.description, product_type.type_string, product_type.type_name as type
        FROM products 
        JOIN product_type ON products.type_id = product_type.id
        JOIN special_attributes ON special_attributes.id = products.spec_attr_id ORDER BY products.id ASC';

            $stmt = $conn->query($sql);
            $result = $stmt->fetchAll();

            $products = [];
            foreach ($result as $key => $record) {
                $product = ProductFactory::buildFromDb($record);
                $products[] = $product;
            }

            return json_encode($products);
        } catch (\Throwable $th) {
            //throw $th;
            return json_encode(['error' => 'Oops, something went wrong. Please try again later.']);
        }
    }

    public static function checkIsSkuUnique($sku)
    {
        try {
            $conn = new Connection();
            $sql = 'SELECT sku FROM products WHERE sku = :sku';
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':sku', $sku);
            $stmt->execute();
            $result = ($stmt->fetchColumn() > 0) ? false : true;
            $ar = ["valid" => $result];
            $send = json_encode($ar);
            return $send;
        } catch (\Throwable $th) {
            //throw $th;
            return json_encode(['error' => 'Oops, something went wrong. Please try again later.']);
        }
    }


    public static function massDelete($array)
    {
        try {
            $conn = new Connection();

            $ids = implode(",", $array);
            $sql = "DELETE FROM products WHERE products.id IN ($ids)";
            $stmt = $conn->prepare($sql);
            $success = $stmt->execute();

            return $success;
        } catch (\Throwable $th) {
            //throw $th;
            return json_encode(['error' => 'Oops, something went wrong. Please try again later.']);
        }
    }
}
