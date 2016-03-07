<?php

namespace RayRutjes\GetEventStore\Client\Http\Feed;

use RayRutjes\GetEventStore\EventRecord;

final class EventStreamViaPersistentSubscriptionFeed
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
     * @var bool
     */
    private $isHeadOfStream;

    /**
     * @param array $events
     * @param array $links
     * @param bool  $isHeadOfStream
     */
    public function __construct(array $events, array $links, bool $isHeadOfStream)
    {
        $this->validateEvents($events);
        $this->events = $events;

        foreach ($links as $link) {
            $this->validateLink($link);
            $this->links[$link->getRelation()] = $link;
        }
        $this->isHeadOfStream = $isHeadOfStream;
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
        if (!$link instanceof EventStreamViaPersistentSubscriptionFeedLink) {
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
        return isset($this->links[EventStreamViaPersistentSubscriptionFeedLink::LINK_PREVIOUS]);
    }

    /**
     * @return EventStreamViaPersistentSubscriptionFeedLink
     */
    public function getPreviousLink(): EventStreamViaPersistentSubscriptionFeedLink
    {
        return $this->links[EventStreamViaPersistentSubscriptionFeedLink::LINK_PREVIOUS];
    }

    /**
     * @return bool
     */
    public function hasNextLink(): bool
    {
        return isset($this->links[EventStreamViaPersistentSubscriptionFeedLink::LINK_NEXT]);
    }

    /**
     * @return EventStreamViaPersistentSubscriptionFeedLink
     */
    public function getNextLink(): EventStreamViaPersistentSubscriptionFeedLink
    {
        return $this->links[EventStreamViaPersistentSubscriptionFeedLink::LINK_NEXT];
    }

    /**
     * @return bool
     */
    public function hasLastLink(): bool
    {
        return isset($this->links[EventStreamViaPersistentSubscriptionFeedLink::LINK_LAST]);
    }

    /**
     * @return EventStreamViaPersistentSubscriptionFeedLink
     */
    public function getLastLink(): EventStreamViaPersistentSubscriptionFeedLink
    {
        return $this->links[EventStreamViaPersistentSubscriptionFeedLink::LINK_LAST];
    }

    /**
     * @return EventStreamViaPersistentSubscriptionFeedLink
     */
    public function getAckAllLink(): EventStreamViaPersistentSubscriptionFeedLink
    {
        return $this->links[EventStreamViaPersistentSubscriptionFeedLink::LINK_ACK_ALL];
    }

    /**
     * @return EventStreamViaPersistentSubscriptionFeedLink
     */
    public function getNackAllLink(): EventStreamViaPersistentSubscriptionFeedLink
    {
        return $this->links[EventStreamViaPersistentSubscriptionFeedLink::LINK_NACK_ALL];
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
