<?php

namespace src\Products;

use src\Db\Connection;

class ProductType
{

    public $type_name;
    public $type_string;
    public $spec_attr_description;
    public $spec_attr_details = [];
    public $unit;

    public function setTypeName($type_name)
    {
        $this->type_name = $type_name;
    }

    public function getTypeName()
    {
        return $this->type_name;
    }

    public function setTypeString($type_string)
    {
        $this->type_string = $type_string;
    }

    public function getTypeString()
    {
        return $this->type_string;
    }

    public function setAttrDescription($description)
    {
        $this->spec_attr_description = $description;
    }

    public function getAttrDescription()
    {
        return $this->spec_attr_description;
    }

    public function setAttrDetails($details)
    {
        $this->spec_attr_details = $details;
    }

    public function getAttrDetails()
    {
        return $this->spec_attr_details;
    }

    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    public function getUnit()
    {
        return $this->unit;
    }


    public static function getAll()
    {

        try {

            $conn = new Connection();

            $sql = 'SELECT *
        FROM product_type 
        JOIN special_attributes ON product_type.spec_attr_id = special_attributes.id
        JOIN attributes ON attributes.spec_attr_id = special_attributes.id';

            $stmt = $conn->query($sql);
            $result = $stmt->fetchAll();

            $data = [];
            foreach ($result as $key => $record) {
                $prod_type_name = $record['type_name'];
                $prod_type_string = $record['type_string'];
                $prod_type_description = $record['description'];
                $unit = $record['unit'];
                $spec_attr_id = $record['spec_attr_id'];
                $spec_attr_name = $record['spec_attr_name'];
                $a_name = $record['attr_name'];
                $a_type = $record['attr_type'];

                if (!array_key_exists($spec_attr_id, $data)) {
                    $data[$spec_attr_id] = [
                        'type_name' => $prod_type_name,
                        'type_string' => $prod_type_string,
                        'spec_attr_name' => $spec_attr_name,
                        'type_description' => $prod_type_description,
                        'unit' => $unit,
                        'attribute_details' => [],
                    ];
                }

                if (!array_key_exists($a_name, $data[$spec_attr_id]['attribute_details'])) {
                    $data[$spec_attr_id]['attribute_details'][$a_name] = [
                        'a_name' => $a_name,
                        'a_type' => $a_type,
                    ];
                }
            }

            $productTypes = [];
            foreach ($data as $key => $record) {
                $productType = new \src\Products\ProductType;
                $productType->setTypeName($record['type_name']);
                $productType->setTypeString($record['type_string']);
                $productType->setAttrDescription($record['type_description']);
                $productType->setUnit($record['unit']);
                $productType->setAttrDetails($record['attribute_details']);
                $productTypes[] = $productType;
            }

            return json_encode($productTypes);
        } catch (\Throwable $th) {
            //throw $th;
            return json_encode(['error' => 'Oops, something went wrong. Please try again later.']);
        }
    }
}
