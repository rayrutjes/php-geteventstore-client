<?php

namespace RayRutjes\GetEventStore\Client\Http\Feed;

use RayRutjes\GetEventStore\EventRecord;

final class EventStreamFeed
{
    /**
     * @var array
     */
    private $events;

    /**
     * @var array
     */
    private $links = [];

    /**
     * @var string
     */
    private $eTag;

    /**
     * @var bool
     */
    private $isHeadOfStream;

    /**
     * @param array  $events
     * @param array  $links
     * @param bool   $isHeadOfStream
     * @param string $eTag
     */
    public function __construct(array $events, array $links, bool $isHeadOfStream, string $eTag = null)
    {
        $this->validateEvents($events);
        $this->events = $events;

        foreach ($links as $link) {
            $this->validateLink($link);
            $this->links[$link->getRelation()] = $link;
        }
        $this->isHeadOfStream = $isHeadOfStream;
        $this->eTag = $eTag;
    }

    /**
     * @param array $events
     *
     * @return mixed
     */
    private function validateEvents(array $events)
    {
        foreach ($events as $event) {
            if (!$event instanceof EventRecord) {
                throw new \InvalidArgumentException(sprintf('Expected EventRecord, got %s', get_class($event)));
            }
        }
    }

    /**
     * @param $link
     */
    private function validateLink($link)
    {
        if (!$link instanceof EventStreamFeedLink) {
            throw new \InvalidArgumentException('Invalid link type %s.', get_class($link));
        }
        if (isset($this->links[$link->getRelation()])) {
            throw new \InvalidArgumentException(sprintf('Link relation %s already there.', $link->getRelation()));
        }
    }

    /**
     * @return bool
     */
    public function hasPreviousLink(): bool
    {
        return isset($this->links[EventStreamFeedLink::LINK_PREVIOUS]);
    }

    /**
     * @return EventStreamFeedLink
     */
    public function getPreviousLink(): EventStreamFeedLink
    {
        return $this->links[EventStreamFeedLink::LINK_PREVIOUS];
    }

    /**
     * @return bool
     */
    public function hasNextLink(): bool
    {
        return isset($this->links[EventStreamFeedLink::LINK_NEXT]);
    }

    /**
     * @return EventStreamFeedLink
     */
    public function getNextLink(): EventStreamFeedLink
    {
        return $this->links[EventStreamFeedLink::LINK_NEXT];
    }

    /**
     * @return bool
     */
    public function hasLastLink(): bool
    {
        return isset($this->links[EventStreamFeedLink::LINK_LAST]);
    }

    /**
     * @return EventStreamFeedLink
     */
    public function getLastLink(): EventStreamFeedLink
    {
        return $this->links[EventStreamFeedLink::LINK_LAST];
    }

    /**
     * @return string
     */
    public function getETag(): string
    {
        return $this->eTag;
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @return bool
     */
    public function isHeadOfStream(): bool
    {
        return $this->isHeadOfStream;
    }
}
