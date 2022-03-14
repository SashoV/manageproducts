<?php

namespace src\ValidationRules;
class Validation
{
    private $errors = [];

    public function validate($src, $rules = [])
    {
        foreach ($src as $item => $item_value) {
            if (key_exists($item, $rules)) {
                foreach ($rules[$item] as $rule => $rule_value) {
                    if (is_int($rule))
                        $rule = $rule_value;
                    switch ($rule) {
                        case 'uniqueSku':
                            $bool = \src\Products\Product::checkIsSkuUnique(trim($item_value));
                            $result = json_decode($bool);
                            if (!$result->valid) {
                                $this->addError($item, ucwords($item) . ' not unique');
                            }
                            break;
                        case 'required':
                            if (empty($item_value) && $rule_value) {
                                $this->addError($item, ucwords($item) . ' required');
                            }
                            break;
                        case 'integer':
                            if (!ctype_digit($item_value) && $rule_value) {
                                $this->addError($item, ucwords($item) . ' should be numeric');
                            }
                            break;
                        case 'decimal':
                            if (!(is_numeric($item_value) || is_float($item_value)) && $rule_value) {
                                $this->addError($item, ucwords($item) . ' should be decimal');
                            }
                            break;
                        case 'alpha':
                            if (!ctype_alpha($item_value) && $rule_value) {
                                $this->addError($item, ucwords($item) . ' should be alphabetic characters');
                            }
                            break;
                        case 'positive':
                            if (!($item_value >= 0) && $rule_value) {
                                $this->addError($item, ucwords($item) . ' should be positive integer');
                            }
                    }
                }
            }
        }
    }

    private function addError($item, $error)
    {
        $this->errors[$item][] = $error;
    }

    public function error()
    {
        if (empty($this->errors)) return false;
        return $this->errors;
    }
}
