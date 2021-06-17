<?php

namespace App\Entity;

abstract class Entity
{
    public int $id;
    private $get;

    public function __construct()
    {
        $this->get = fn() => $this;
    }

    public function _toProxy(int $id, callable $get)
    {
        $this->id = $id;
        $this->get = $get;

        return $this;
    }

    public function &__get(string $property)
    {
        return ($this->get)()->{$property};
    }

    public function __set(string $property, mixed $value)
    {
        ($this->get)()->{$property} = $value;
    }
}