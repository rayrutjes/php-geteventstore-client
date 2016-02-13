<?php

namespace RayRutjes\GetEventStore\Client\Http;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use RayRutjes\GetEventStore\StreamId;

final class DeleteStreamRequestFactory implements RequestFactoryInterface
{
    /**
     * @var StreamId
     */
    private $streamId;

    /**
     * @param StreamId $streamId
     */
    public function __construct(StreamId $streamId)
    {
        $this->streamId = $streamId;
    }

    /**
     * @return RequestInterface
     */
    public function buildRequest(): RequestInterface
    {
        return new Request(
            'DELETE',
            sprintf('streams/%s', $this->streamId->toString()),
            [
                RequestHeader::HARD_DELETE => 'true',
            ]
        );
    }
}
