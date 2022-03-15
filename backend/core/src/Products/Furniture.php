<?php

namespace src\Products;

use src\Products\Product;

class Furniture extends Product
{
    public $attribute_id = 3;
    public $type_id = 3;
    public $rules = [
        'sku' => ['required', 'uniqueSku'],
        'name' => ['required'],
        'price' => ['required', 'decimal', 'positive'],
        'type' => ['required', 'alpha'],
        'height' => ['required', 'integer', 'positive'],
        'width' => ['required', 'integer', 'positive'],
        'length' => ['required', 'integer', 'positive'],
    ];


    public function setAttribute($data)
    {
        $this->attribute = $data['height'] . 'x' . $data['width'] . 'x' . $data['length'];
    }

}
