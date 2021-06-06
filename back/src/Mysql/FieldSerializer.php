<?php

namespace App\Mysql;

use App\Service\LetterCase;

class FieldSerializer
{
    public static function serialize(string $field): string
    {
        return LetterCase::camelToSnake($field);
    }

    public static function serializeId(string $entity): string
    {
        return 'id_' . EntitySerializer::serialize($entity);
    }

    public static function unserialize(string $field): string
    {
        return LetterCase::snakeToCamel($field);
    }
}