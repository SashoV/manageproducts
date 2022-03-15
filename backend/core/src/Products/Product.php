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
    public $type_id;
    public $rules;
    public $attribute;
    public $attribute_id;
    public $attributeString;

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

    public function getTypeId()
    {
        return $this->type_id;
    }

    public function getAttribute()
    {
        return $this->attribute;
    }

    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }

    public function setAttributeString($attributeName, $attributeValue, $attributeUnit)
    {
        $this->attributeString = ucfirst($attributeName . ": " . $attributeValue . " " . $attributeUnit);
    }

    public function getAttributeString()
    {
        return $this->attributeString;
    }

    public function getAttributeId()
    {
        return $this->attribute_id;
    }

    public function setPropertiesFromDb($data)
    {
        $this->setId($data['id']);
        $this->setSku($data['sku']);
        $this->setName($data['name']);
        $this->setPrice($data['price']);
        $this->setType($data['type']);
        $this->setAttributeString($data['spec_attr_name'], $data['spec_attr_value'], $data['unit']);
    }

    public function setProperties($postData)
    {
        $this->setSku($postData['sku']);
        $this->setName($postData['name']);
        $this->setPrice($postData['price']);
        $this->setType($postData['type']);
        $this->setAttribute($postData);
    }

    public function save()
    {
        try {
            $conn = new Connection();
            $sql = 'INSERT INTO products (sku, name, price, type_id, spec_attr_id, spec_attr_value) VALUES (:sku, :name, :price, :type_id, :spec_attr_id, :spec_attr_value)';
            $stmt = $conn->prepare($sql);

            $sku = $this->getSku();
            $name = $this->getName();
            $price = $this->getPrice();
            $type_id = $this->getTypeId();
            $attr_id = $this->getAttributeId();
            $attr_value = $this->getAttribute();

            $stmt->bindParam(':sku', $sku);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':type_id', $type_id);
            $stmt->bindParam(':spec_attr_id', $attr_id);
            $stmt->bindParam(':spec_attr_value', $attr_value);

            $success = $stmt->execute();
            return $success;
        } catch (\Throwable $th) {
            //throw $th;
            return json_encode(['error' => 'Oops, something went wrong. Please try again later.']);
        }
    }

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
