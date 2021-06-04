<?php

namespace App\Controller\Dto\Constraint;

interface Constraint
{
    public function validate($value): bool;
    public function error(string $property): string;
}