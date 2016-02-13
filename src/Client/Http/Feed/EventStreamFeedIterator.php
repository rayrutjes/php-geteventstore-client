<?php

namespace RayRutjes\GetEventStore\Client\Http\Feed;

use RayRutjes\GetEventStore\Client\Http\HttpClient;
use RayRutjes\GetEventStore\Client\Http\ReadEventStreamFeedRequestFactory;
use RayRutjes\GetEventStore\Client\Http\ReadEventStreamFeedResponseInspector;
use RayRutjes\GetEventStore\ReadDirection;
use RayRutjes\GetEventStore\StreamId;

class EventStreamFeedIterator implements \Iterator
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
     * @var EventStreamFeed
     */
    private $currentFeed;

    /**
     * @var string
     */
    private $headUri;

    /**
     * @var string
     */
    private $startUri;

    /**
     * @var ReadDirection
     */
    private $readDirection;

    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @var int
     */
    private $currentKey = 0;

    /**
     * You can either iterate reading from the start of the stream, beginning at the first event registered and move forward,
     * or you can iterate from the end, meaning that the first iteration will correspond to the latest event registered,
     * and move backward to the beginning of the stream.
     *
     * @param StreamId   $streamId
     * @param HttpClient $client
     * @param bool       $readFromStart
     */
    public function __construct(StreamId $streamId, HttpClient $client, $readFromStart = true)
    {
        $this->streamId = $streamId;
        $this->client = $client;
        $this->headUri = sprintf('streams/%s', $streamId->toString());

        $direction = $readFromStart ? ReadDirection::FORWARD : ReadDirection::BACKWARD;
        $this->readDirection = new ReadDirection($direction);
    }

    /**
     * @param string $uri
     *
     * @return EventStreamFeed
     */
    private function readEventStreamFeed(string $uri): EventStreamFeed
    {
        $factory = new ReadEventStreamFeedRequestFactory($uri);
        $inspector = new ReadEventStreamFeedResponseInspector();
        $this->client->send($factory->buildRequest(), $inspector);

        return $inspector->getFeed();
    }

    /**
     * @return EventStreamFeed
     */
    public function current(): EventStreamFeed
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
        if ($this->readDirection->isForward()) {
            return $this->readForward();
        }
        $this->readBackward();
    }

    private function readForward()
    {
        if ($this->currentFeed->hasPreviousLink() && !$this->currentFeed->isHeadOfStream()) {
            $this->currentFeed = $this->readEventStreamFeed($this->currentFeed->getPreviousLink()->getUri());

            return;
        }
        $this->currentFeed = null;
    }

    private function readBackward()
    {
        if ($this->currentFeed->hasNextLink()) {
            $this->currentFeed = $this->readEventStreamFeed($this->currentFeed->getNextLink()->getUri());

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
        if ($this->readDirection->isForward()) {
            $this->startUri = $this->getStartUri();
            $this->currentFeed = $this->readEventStreamFeed($this->startUri);
        } else {
            $this->currentFeed = $this->readEventStreamFeed($this->headUri);
        }
        $this->initialized = true;
        // Todo: not sure how we should handle errors here.
        // Todo: for now, let it bubble.
    }

    /**
     * @return string
     */
    private function getStartUri(): string
    {
        if (null !== $this->startUri) {
            return $this->startUri;
        }

        // It is recommended to not guess the uris for backward compatibility.
        // So we have to make an intermediary request to get the start uri.
        $feed = $this->readEventStreamFeed($this->headUri);
        if ($feed->hasLastLink()) {
            return $feed->getLastLink()->getUri();
        }

        // If we came so far it means we are dealing with a feed of exactly one page.
        // So head = start.
        return $this->headUri;
    }

    /**
     * @return ReadDirection
     */
    public function getReadDirection()
    {
        return $this->readDirection;
    }
}
