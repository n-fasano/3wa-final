<?php

namespace App\Controller\Dto\Constraint;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class NotBlank implements Constraint
{
    public function validate($value): bool
    {
        return !empty($value);
    }

    public function error(string $property, $value): string
    {
        return sprintf('The %s must not be blank.', $property);
    }
}