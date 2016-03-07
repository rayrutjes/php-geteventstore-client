<?php

namespace RayRutjes\GetEventStore\Client\Http\Feed;

use RayRutjes\GetEventStore\EventRecord;

final class EventStreamViaPersistentSubscriptionIterator implements \Iterator
{
    /**
     * @var EventStreamViaPersistentSubscriptionFeedIterator
     */
    private $feedIterator;

    /**
     * @var \ArrayIterator
     */
    private $eventsIterator;

    /**
     * @var int
     */
    private $currentKey;

    /**
     * @param EventStreamViaPersistentSubscriptionFeedIterator $feedIterator
     */
    public function __construct(EventStreamViaPersistentSubscriptionFeedIterator $feedIterator)
    {
        $this->feedIterator = $feedIterator;
    }

    /**
     * @return EventRecord
     */
    public function current(): EventRecord
    {
        return $this->eventsIterator->current();
    }

    public function next()
    {
        $this->eventsIterator->next();
        $this->currentKey++;
        if ($this->eventsIterator->valid()) {
            return;
        }

        $this->feedIterator->next();
        if ($this->feedIterator->valid()) {
            $this->eventsIterator = $this->newEventsIterator();
        } else {
            $this->feedIterator = null;
        }
    }

    /**
     * @return \ArrayIterator
     */
    private function newEventsIterator()
    {
        $events = $this->feedIterator->current()->getEvents();

        $events = array_reverse($events);

        return new \ArrayIterator($events);
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->currentKey;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return $this->eventsIterator->valid();
    }

    public function rewind()
    {
        $this->currentKey = 0;
        $this->eventsIterator = $this->newEventsIterator();
    }
}
