<?php

namespace src\Products;

use src\Products\Product;
use src\Db\Connection;

class Book extends Product
{
    public $attribute;
    public $attribute_id = 2;
    public $attributeString;
    public $type = "book";
    public $type_id = 2;

    public $rules = [
        'sku' => ['required', 'uniqueSku'],
        'name' => ['required'],
        'price' => ['required', 'decimal', 'positive'],
        'type' => ['required', 'alpha'],
        'weight' => ['required', 'integer', 'positive']
    ];

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

    public function getTypeId()
    {
        return $this->type_id;
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

    public function setProperties($postData)
    {
        $this->setSku($postData['sku']);
        $this->setName($postData['name']);
        $this->setPrice($postData['price']);
        $this->setType($postData['type']);
        $this->setAttribute($postData['weight']);
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
}
