<?php

namespace RayRutjes\GetEventStore;

use RayRutjes\GetEventStore\Util\InternalIterator;

final class EventDataCollection extends InternalIterator implements \Iterator, \Countable
{
    /**
     * @param array $events
     *
     * @return EventDataCollection
     */
    public static function fromArray(array $events = []): EventDataCollection
    {
        $indexed = [];
        foreach ($events as $event) {
            if (!$event instanceof EventData) {
                throw new \InvalidArgumentException(sprintf('Invalid EventData, got: %s', get_class($event)));
            }
            $indexed[] = $event;
        }

        return new self(new \ArrayIterator($indexed));
    }

    /**
     * @return int
     */
    public function count()
    {
        if ($this->iterator instanceof \Countable) {
            return $this->iterator->count();
        }

        return 0;
    }
}
