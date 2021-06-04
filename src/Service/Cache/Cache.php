<?php

namespace App\Service\Cache;

use Exception;

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
        $path = CACHE_DIR;

        foreach ($directories as $directory) {
            $path .= $directory;
            if (!is_dir($path) && mkdir($path) && !is_dir($path)) {
                throw new Exception('Unable to write to cache.');
            }
        }

        return (bool) file_put_contents("$shortName.php", $contents);
    }
}