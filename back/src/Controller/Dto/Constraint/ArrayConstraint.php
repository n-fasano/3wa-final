<?php

namespace App\Controller\Dto\Constraint;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ArrayConstraint implements Constraint
{
    private Constraint $constraint;
    private $errorItem;

    public function __construct(private string $constraintClass, ...$parameters)
    {
        $this->constraint = new $constraintClass(...$parameters);
    }

    public function validate($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $item) {
            if (false === $this->constraint->validate($item)) {
                $this->errorItem = $item;
                return false;
            }
        }

        return true;
    }

    public function error(string $property, $value): string
    {
        return $this->constraint->error($property, $this->errorItem);
    }
}