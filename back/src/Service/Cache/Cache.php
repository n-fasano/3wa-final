<?php

namespace App\Service\Cache;

use RuntimeException;

class Cache
{
    public static function getCacheNamespace(): string
    {
        return "Cache\\";
    }

    public static function createClass(string $fullName, string $contents): bool
    {
        $directories = explode('\\', $fullName);
        $shortName = array_pop($directories);

        # Shifting out the "Cache" part
        array_shift($directories);

        $path = CACHE_DIR . '/' . implode('/', $directories);
        if (!is_dir($path) && mkdir($path, 0777, true) && !is_dir($path)) {
            throw new RuntimeException('Unable to write to cache.');
        }

        return (bool) file_put_contents("$path/$shortName.php", $contents);
    }
}