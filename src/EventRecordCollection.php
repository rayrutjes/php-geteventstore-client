<?php

namespace RayRutjes\GetEventStore;

use RayRutjes\GetEventStore\Util\InternalIterator;

final class EventRecordCollection extends InternalIterator implements \Iterator, \Countable
{
    /**
     * @param array $events
     *
     * @return EventRecordCollection
     */
    public static function fromArray(array $events = []): EventRecordCollection
    {
        $indexed = [];
        foreach ($events as $event) {
            if (!$event instanceof EventRecord) {
                throw new \InvalidArgumentException(sprintf('Invalid EventRecord, got: %s', get_class($event)));
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
