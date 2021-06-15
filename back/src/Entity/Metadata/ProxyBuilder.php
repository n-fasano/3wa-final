<?php

namespace App\Entity\Metadata;

use App\Service\Cache\Cache;

class ProxyBuilder
{
    public static function getProxy(string $entity)
    {
        $cacheNamespace = Cache::getCacheNamespace();
        $proxyClass = $cacheNamespace . $entity;

        if (!class_exists($proxyClass)) {
            $proxy = str_replace('App\\Entity\\', '', $entity);
            $contents = str_replace(
                ['{CACHE_NAMESPACE}', '{ENTITY}', '{PROXY}'], 
                [$cacheNamespace, $entity, $proxy], 
                file_get_contents(__DIR__.'/Proxy.php.template')
            );

            Cache::createClass($proxyClass, $contents);
        }

        return "\\$proxyClass";
    }
}