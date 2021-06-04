<?php

namespace App\Entity\Metadata;

use App\Service\Cache\Cache;

class ProxyBuilder
{
    public static function getProxy(string $entity)
    {
        $cacheNamespace = Cache::getCacheNamespace();
        $fullName = $cacheNamespace . $entity;

        if (!class_exists($fullName)) {
            $proxy = str_replace('\\', '_', $entity) . 'Proxy';
            $contents = str_replace(
                ['{CACHE_NAMESPACE}', '{ENTITY}', '{PROXY}'], 
                [$cacheNamespace, $entity, $proxy], 
                file_get_contents(__DIR__.'/Proxy.php.template')
            );

            Cache::createClass($fullName, $contents);
        }

        return $fullName;
    }
}