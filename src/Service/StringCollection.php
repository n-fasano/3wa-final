<?php

namespace App\Service;

use Iterator;

class StringCollection implements Iterator
{
    private $position = 0;
    private array $items;

    public function __construct(array $items)
    {
        $this->position = 0;
        $this->items = $items;    
    }

    public function not(string $not): StringCollection
    {
        return new StringCollection(
            array_filter(
                $this->items,
                fn($item) => $not !== $item
            )
        );
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->items[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->items[$this->position]);
    }
}