<?php

namespace RayRutjes\GetEventStore\Client\Http\Feed;

use RayRutjes\GetEventStore\Client\Http\HttpClient;
use RayRutjes\GetEventStore\Client\Http\ReadEventStreamViaPersistentSubscriptionRequestFactory;
use RayRutjes\GetEventStore\Client\Http\ReadEventStreamViaPersistentSubscriptionResponseInspector;
use RayRutjes\GetEventStore\StreamId;

final class EventStreamViaPersistentSubscriptionFeedIterator implements \Iterator
{
    /**
     * @var StreamId
     */
    private $streamId;

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var EventStreamViaPersistentSubscriptionFeed
     */
    private $currentFeed;

    /**
     * @var string
     */
    private $headUri;

    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @var int
     */
    private $currentKey = 0;

    /**
     * @var string
     */
    private $groupName;

    /**
     * @var int
     */
    private $batchSize;

    /**
     * @param StreamId   $streamId
     * @param string     $groupName
     * @param HttpClient $client
     * @param int        $batchSize
     */
    public function __construct(StreamId $streamId, string $groupName, HttpClient $client, int $batchSize = 1)
    {
        $this->streamId = $streamId;
        $this->groupName = $groupName;
        $this->client = $client;
        $this->headUri = sprintf('/subscriptions/%s/%s', $this->streamId->toString(), $this->groupName);
        $this->batchSize = $batchSize;
    }

    /**
     * @param string $uri
     *
     * @return EventStreamViaPersistentSubscriptionFeed
     */
    private function readEventStreamFeed(string $uri): EventStreamViaPersistentSubscriptionFeed
    {
        $factory = new ReadEventStreamViaPersistentSubscriptionRequestFactory($uri, $this->batchSize);
        $inspector = new ReadEventStreamViaPersistentSubscriptionResponseInspector();
        $this->client->send($factory->buildRequest(), $inspector);

        return $inspector->getFeed();
    }

    /**
     * @return EventStreamViaPersistentSubscriptionFeed
     */
    public function current(): EventStreamViaPersistentSubscriptionFeed
    {
        if (null === $this->currentFeed) {
            if (true === $this->initialized) {
                throw new \OutOfBoundsException('Stream overflow.');
            } else {
                $this->rewind();
            }
        }

        return $this->currentFeed;
    }

    public function next()
    {
        $this->currentKey++;
        $this->readForward();
    }

    private function readForward()
    {
        if ($this->currentFeed->hasPreviousLink() && !$this->currentFeed->isHeadOfStream()) {
            $this->currentFeed = $this->readEventStreamFeed($this->currentFeed->getPreviousLink()->getUri());

            return;
        }
        $this->currentFeed = null;
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->currentKey;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return null !== $this->currentFeed;
    }

    public function rewind()
    {
        $this->currentKey = 0;
        $this->currentFeed = $this->readEventStreamFeed($this->headUri);
        $this->initialized = true;
    }
}
