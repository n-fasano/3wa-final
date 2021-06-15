<?php

namespace App\Controller\Dto\Base;

use App\Controller\Dto\Constraint\Constraint;
use App\Controller\Dto\DataTransferObject;
use ReflectionClass;

final class Validator
{
    public static function validate(DataTransferObject $dto): array
    {
        $errors = [];

        $reflection = new ReflectionClass($dto);
        foreach ($reflection->getProperties() as $prop) {
            $name = $prop->getName();
            $value = $dto->{$name}();
            
            foreach ($prop->getAttributes() as $attribute) {
                /** @var Constraint $constraint */
                $constraint = $attribute->newInstance();
                if (!$constraint->validate($value)) {
                    $errors[] = $constraint->error($prop->getName(), $value);
                }
            }
        }

        return $errors;
    }
}