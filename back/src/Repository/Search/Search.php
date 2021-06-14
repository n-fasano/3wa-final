<?php

namespace App\Repository\Search;

use App\Service\Collection;

class Search extends Collection
{
    public function __construct(array $criterias)
    {
        parent::__construct($criterias);
    }
}