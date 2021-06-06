<?php

namespace App\Repository\Search;

use App\Service\Collection;

class Search extends Collection
{
    public string $class;

    public function __construct(string $class, array $criterias)
    {
        $this->class = $class;

        parent::__construct($criterias);
    }
}