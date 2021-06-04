<?php

namespace App\Entity\Metadata;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class OneToMany
{
    public function __construct(public string $class)
    { }
}