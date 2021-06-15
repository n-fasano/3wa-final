<?php

namespace App\Service;

use ArrayAccess;
use Iterator;

class Collection implements Iterator, ArrayAccess
{
    protected $position = 0;

    public function __construct(protected ArrayAccess|array $items)
    { }

    public function toArray()
    {
        return $this->items;
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
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset] ?? null;
    }
}