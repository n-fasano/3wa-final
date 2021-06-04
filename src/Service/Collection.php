<?php

namespace App\Service;

use Iterator;
use ArrayAccess;

class Collection implements Iterator, ArrayAccess
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

    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }
}