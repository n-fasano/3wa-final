<?php

namespace App\Mysql;

use App\Service\LetterCase;

class EntitySerializer
{
    public static function serialize(string $name): string
    {
        $name = str_replace('App\\Entity\\', '', $name);
        return LetterCase::pascalToSnake($name);
    }

    public static function unserialize(string $tableName): string
    {
        return '\\App\\Entity\\'.LetterCase::snakeToPascal($tableName);
    }
}