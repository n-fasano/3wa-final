<?php

namespace App\Mysql;

use App\Service\LetterCase;

class EntitySerializer
{
    public static function serialize(string $shortName): string
    {
        return LetterCase::pascalToSnake($shortName);
    }

    public static function unserialize(string $tableName): string
    {
        return '\\App\\Entity\\'.LetterCase::snakeToPascal($tableName);
    }
}