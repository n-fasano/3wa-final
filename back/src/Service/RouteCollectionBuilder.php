<?php

namespace App\Service;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteCollectionBuilder
{
    private RouteCollection $routeCollection;

    public function __construct(
        private ?string $prefix = null
    ) {
        $this->routeCollection = new RouteCollection();
    }

    public function add(string $name, Route $route, int $priority = 0)
    {
        $this->routeCollection->add($name, $route, $priority);
    }

    public function build(): RouteCollection
    {
        if (null !== $this->prefix) {
            $this->routeCollection->addPrefix($this->prefix);
        }

        return $this->routeCollection;
    }
}