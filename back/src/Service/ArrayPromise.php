<?php

namespace App\Service;

use ArrayAccess;

class ArrayPromise extends Collection implements ArrayAccess
{
    public function __construct(
        protected callable $get
    ) { }

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
        if (!isset($this->items)) {
            $this->items = ($this->get)();
        }

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