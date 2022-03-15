<?php

namespace src\Products;

use src\Products\Product;

class Dvd extends Product
{
    public $attribute_id = 1;
    public $type_id = 1;
    public $rules = [
        'sku' => ['required', 'uniqueSku'],
        'name' => ['required'],
        'price' => ['required', 'decimal', 'positive'],
        'type' => ['required', 'alpha'],
        'size' => ['required', 'integer', 'positive']
    ];

    public function setAttribute($data)
    {
        $this->attribute = $data['size'];
    }

}
