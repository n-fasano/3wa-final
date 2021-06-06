<?php

namespace App\Entity\Metadata;

use App\Service\ArrayPromise;
use App\Service\Collection;

class ProxyCollection extends Collection
{
    public function __construct(callable $get) { 
        $this->items = new ArrayPromise($get);
    }
}