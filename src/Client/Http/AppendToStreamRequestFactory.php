<?php

namespace RayRutjes\GetEventStore\Client\Http;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use RayRutjes\GetEventStore\EventData;
use RayRutjes\GetEventStore\EventDataCollection;
use RayRutjes\GetEventStore\ExpectedVersion;
use RayRutjes\GetEventStore\StreamId;

final class AppendToStreamRequestFactory implements RequestFactoryInterface
{
    /**
     * @var StreamId
     */
    private $streamId;

    /**
     * @var ExpectedVersion
     */
    private $expectedVersion;

    /**
     * @var EventDataCollection
     */
    private $events;

    /**
     * @param StreamId            $streamId
     * @param ExpectedVersion     $expectedVersion
     * @param EventDataCollection $events
     */
    public function __construct(StreamId $streamId, ExpectedVersion $expectedVersion, EventDataCollection $events)
    {
        if (empty($events)) {
            throw new \InvalidArgumentException('No events provided.');
        }
        $this->streamId = $streamId;
        $this->expectedVersion = $expectedVersion;
        $this->events = $events;
    }

    /**
     * @return RequestInterface
     */
    public function buildRequest(): RequestInterface
    {
        return new Request(
            'POST',
            sprintf('streams/%s', $this->streamId->toString()),
            [
                RequestHeader::CONTENT_TYPE     => ContentType::JSON_ES,
                RequestHeader::EXPECTED_VERSION => $this->expectedVersion->toInt(),
            ],
            $this->buildBody()
        );
    }

    /**
     * @return string
     */
    private function buildBody(): string
    {
        $data = [];
        foreach ($this->events as $event) {
            /* @var $event EventData */
            $data[] = [
                'eventId'   => $event->getEventId()->toString(),
                'eventType' => $event->getType(),
                'data'      => $event->getData(),
                'metadata'  => $event->getMetadata(),
            ];
        }

        return json_encode($data);
    }
}
