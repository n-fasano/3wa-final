<?php

namespace App\Service;

use Iterator;

class Collection implements Iterator
{
    protected $position = 0;
    protected array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->items[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }
}