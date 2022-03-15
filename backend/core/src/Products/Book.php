<?php

namespace src\Products;

use src\Products\Product;

class Book extends Product
{
    public $attribute_id = 2;
    public $type_id = 2;
    public $rules = [
        'sku' => ['required', 'uniqueSku'],
        'name' => ['required'],
        'price' => ['required', 'decimal', 'positive'],
        'type' => ['required', 'alpha'],
        'weight' => ['required', 'integer', 'positive']
    ];

    public function setAttribute($data)
    {
        $this->attribute = $data['weight'];
    }

}
