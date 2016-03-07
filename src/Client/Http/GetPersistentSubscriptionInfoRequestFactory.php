<?php

namespace RayRutjes\GetEventStore\Client\Http;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use RayRutjes\GetEventStore\StreamId;

final class GetPersistentSubscriptionInfoRequestFactory implements RequestFactoryInterface
{
    /**
     * @var StreamId
     */
    private $streamId;

    /**
     * @var string
     */
    private $groupName;

    /**
     * @param StreamId $streamId
     * @param string   $groupName
     */
    public function __construct(StreamId $streamId, string $groupName)
    {
        $this->streamId = $streamId;
        $this->groupName = $groupName;
    }

    /**
     * @return RequestInterface
     */
    public function buildRequest(): RequestInterface
    {
        return new Request(
            'GET',
            sprintf('subscriptions/%s/%s/info', $this->streamId->toString(), $this->groupName),
            [
                RequestHeader::CONTENT_TYPE => ContentType::JSON,
            ]
        );
    }
}
