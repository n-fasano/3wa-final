<?php

namespace App\Entity\Metadata;

use App\Entity\Entity;

class Proxy extends Entity
{
    public function __construct(private callable $get)
    { }

    public function __get(string $property)
    {
        $this->get()->{$property};
    }

    public function __set(string $property, mixed $value)
    {
        $this->get()->{$property} = $value;
    }
}