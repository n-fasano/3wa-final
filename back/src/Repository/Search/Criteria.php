<?php

namespace App\Repository\Search;

class Criteria
{
    const TYPE_EQUAL = '=';
    const TYPE_NOT_EQUAL = '!=';
    const TYPE_SIMILAR = '%';
    const TYPE_INFERIOR = '<';
    const TYPE_SUPERIOR = '>';

    public string $field;
    public string $type;
    public mixed $value;

    public function __construct(
        string $field, 
        mixed $value, 
        string $type = self::TYPE_EQUAL
    ) {
        $this->field = $field;
        $this->value = $value;
        $this->type = $type;
    }
}