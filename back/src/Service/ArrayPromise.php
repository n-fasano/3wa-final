<?php

namespace App\Service;

use ArrayAccess;

class ArrayPromise implements ArrayAccess
{
    protected $get;

    public function __construct(callable $get)
    {
        $this->get = $get;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            ($this->get)()[] = $value;
        } else {
            ($this->get)()[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset(($this->get)()[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset(($this->get)()[$offset]);
    }

    public function offsetGet($offset)
    {
        return ($this->get)()[$offset] ?? null;
    }
}