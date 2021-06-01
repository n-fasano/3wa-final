<?php

namespace App\Mysql;

use App\Service\LetterCase;

class EntitySerializer
{
    public static function serialize(string $entity): string
    {
        $basename = basename($entity);
        return LetterCase::pascalToSnake($basename);
    }

    public static function unserialize(string $entity): string
    {
        return '\\App\\Entity\\'.LetterCase::snakeToPascal($entity);
    }
}