<?php

namespace App\Service;

class StringCollection extends Collection
{
    public function not(string $not): StringCollection
    {
        return new StringCollection(
            array_filter(
                $this->items,
                fn($item) => $not !== $item
            )
        );
    }
}