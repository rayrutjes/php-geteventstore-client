<?php

namespace RayRutjes\GetEventStore\Util;

abstract class InternalIterator implements \Iterator
{
    use InternalIteratorTrait;

    /**
     * @param \Iterator $iterator
     */
    final protected function __construct(\Iterator $iterator)
    {
        $this->iterator = $iterator;
    }
}
