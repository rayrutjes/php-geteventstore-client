<?php

namespace RayRutjes\GetEventStore\Client\Http;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use RayRutjes\GetEventStore\EventData;
use RayRutjes\GetEventStore\ExpectedVersion;
use RayRutjes\GetEventStore\StreamId;

final class AppendSingleToStreamRequestFactory implements RequestFactoryInterface
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
     * @var EventData
     */
    private $event;

    /**
     * @param StreamId        $streamId
     * @param ExpectedVersion $expectedVersion
     * @param EventData       $event
     */
    public function __construct(StreamId $streamId, ExpectedVersion $expectedVersion, EventData $event)
    {
        $this->streamId = $streamId;
        $this->expectedVersion = $expectedVersion;
        $this->event = $event;
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
                RequestHeader::CONTENT_TYPE     => ContentType::JSON,
                RequestHeader::EVENT_ID         => $this->event->getEventId()->toString(),
                RequestHeader::EVENT_TYPE       => $this->event->getType(),
                RequestHeader::EXPECTED_VERSION => $this->expectedVersion->toInt(),
            ],
            json_encode($this->event->getData())
        );
    }
}
