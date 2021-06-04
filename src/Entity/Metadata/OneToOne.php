<?php

namespace App\Entity\Metadata;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class OneToOne
{
    public function __construct(public string $class)
    { }
}