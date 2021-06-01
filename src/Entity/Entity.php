<?php

namespace App\Entity;

use ReflectionClass;

abstract class Entity
{
    protected int $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}