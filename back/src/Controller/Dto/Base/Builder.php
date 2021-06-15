<?php

namespace App\Controller\Dto\Base;

use App\Controller\Dto\DataTransferObject;
use Exception;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;

final class Builder
{
    public static function build(string $class, Request $request): DataTransferObject
    {
        $dto = new $class;
        
        $reflection = new ReflectionClass($dto);
        foreach ($reflection->getProperties() as $prop) {
            $name = $prop->getName();
            $value = $request->request->get($name) ?? $request->get($name);
            if (null !== $value) {
                $prop->setAccessible(true);
                $prop->setValue($dto, $value);
            }
        }

        return $dto;
    }
}