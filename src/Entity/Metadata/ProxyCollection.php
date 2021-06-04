<?php

namespace App\Entity\Metadata;

use App\Service\Collection;

class ProxyCollection extends Collection
{
    public function __construct(
        private callable $get
    ) { }

    public function __get(string $property)
    {
        $this->get()->{$property};
    }

    public function __set(string $property, mixed $value)
    {
        $this->get()->{$property} = $value;
    }
}