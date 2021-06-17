<?php

namespace App\Controller\Dto\Constraint;

use App\Repository\Repository;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Exists implements Constraint
{
    private Repository $repository;
    
    public function __construct(string $repositoryClass)
    {
        $this->repository = new $repositoryClass;
    }

    public function validate($value): bool
    {
        return $value && $this->repository->exists((int) $value);
    }

    public function error(string $property, $value): string
    {
        return sprintf('%s #%d does not exist.', $property, $value);
    }
}