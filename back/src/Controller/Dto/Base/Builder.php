<?php

namespace App\Controller\Dto\Base;

use App\Controller\Dto\DataTransferObject;
use App\Entity\Entity;
use DateTime;
use DateTimeInterface;
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
                $type = $prop->getType()->getName();
                if ($type === DateTimeInterface::class) {
                    $format = constant("$class::DATE_FORMAT") ?? 'Y-m-d H:i:s';
                    $value = DateTime::createFromFormat($format, $value) ?: null;
                } else if (!is_a($type, Entity::class)) {
                    settype($value, $type);
                }

                $prop->setAccessible(true);
                $prop->setValue($dto, $value);
            }
        }

        return $dto;
    }
}