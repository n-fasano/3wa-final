<?php

namespace App\Controller\Dto\Constraint;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Length implements Constraint
{
    public int $lowerBoundary;
    public int $upperBoundary;

    public function __construct(int $lowerBoundary, int $upperBoundary)
    {
        $this->lowerBoundary = $lowerBoundary;
        $this->upperBoundary = $upperBoundary;
    }

    public function validate($value): bool
    {
        $len = strlen($value);
        return $this->lowerBoundary <= $len && $len <= $this->upperBoundary;
    }

    public function error(string $property): string
    {
        return sprintf('The %s must be between %u and %u characters.', 
            $property,
            $this->lowerBoundary,
            $this->upperBoundary
        );
    }
}