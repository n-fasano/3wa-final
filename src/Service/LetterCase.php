<?php

namespace App\Service;

class LetterCase
{
    public static function camelToSnake(string $camel): string
    {
        $snake = $camel[0] ?? '';

        for ($i = 1; $i < strlen($camel); $i++) {
            $letter = $camel[$i];

            $code = ord($letter);
            if (64 < $code && $code < 91) {
                $letter = chr($code + 32);
                $snake .= '_';
            }

            $snake .= $letter;
        }

        return $snake;
    }

    public static function pascalToSnake(string $pascal): string
    {
        return lcfirst(self::camelToSnake($pascal));
    }

    public static function snakeToCamel(string $snake): string
    {
        $len = strlen($snake);
        $camel = '';

        for ($i = 0; $i < $len; $i++) {
            $letter = $snake[$i];

            if ('_' === $letter) {
                $i++;
                $letter = ucfirst($snake[$i]);
            }

            $camel .= $letter;
        }

        return $camel;
    }

    public static function snakeToPascal(string $snake): string
    {
        return ucfirst(self::snakeToCamel($snake));
    }
}